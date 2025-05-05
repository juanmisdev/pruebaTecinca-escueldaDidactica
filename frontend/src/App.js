import React, { useEffect, useState } from "react";
import './App.css';

const API_URL = "http://localhost/pruebaTecinca-escueldaDidactica/backend";

function App() {
  const [users, setUsers] = useState([]);
  const [selectedUser, setSelectedUser] = useState("");
  const [tasks, setTasks] = useState([]);
  const [newTask, setNewTask] = useState("");
  const [filter, setFilter] = useState("all");
  const [error, setError] = useState("");

  // Fetch users
  useEffect(() => {
    fetch(`${API_URL}/users`)
      .then(res => res.json())
      .then(setUsers)
      .catch(() => setError("Error al cargar usuarios"));
  }, []);

  // Fetch tasks for selected user
  useEffect(() => {
    if (selectedUser) {
      fetch(`${API_URL}/tasks`)
        .then(res => res.json())
        .then(data => setTasks(data.filter(task => task.user_id === Number(selectedUser))))
        .catch(() => setError("Error al cargar tareas"));
    } else {
      setTasks([]);
    }
  }, [selectedUser]);

  // Add new task
  const handleAddTask = e => {
    e.preventDefault();
    if (!newTask.trim() || !selectedUser) return;
    fetch(`${API_URL}/tasks`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ title: newTask, user_id: Number(selectedUser) }),
    })
      .then(res => res.json())
      .then(() => {
        setNewTask("");
        // Refresh tasks
        fetch(`${API_URL}/tasks`)
          .then(res => res.json())
          .then(data => setTasks(data.filter(task => task.user_id === Number(selectedUser))));
      })
      .catch(() => setError("Error al agregar tarea"));
  };

  // Mark task as completed
  const handleComplete = id => {
    fetch(`${API_URL}/tasks/${id}`, { method: "PUT" })
      .then(() => {
        setTasks(tasks =>
          tasks.map(t => (t.id === id ? { ...t, completed: 1 } : t))
        );
      })
      .catch(() => setError("Error al marcar tarea como completada"));
  };

  // Delete task
  const handleDelete = id => {
    fetch(`${API_URL}/tasks/${id}`, { method: "DELETE" })
      .then(() => setTasks(tasks => tasks.filter(t => t.id !== id)))
      .catch(() => setError("Error al eliminar tarea"));
  };

  // Filter tasks
  const filteredTasks = tasks.filter(t => {
    if (filter === "all") return true;
    if (filter === "completed") return t.completed === 1;
    if (filter === "pending") return t.completed === 0;
    return true;
  });

  return (
    <div className="app-container">
      <h2>Gestión de Tareas By Juan Miguel <span role="img" aria-label="wink">&#128521;</span></h2>
      {error && <div className="error-message">{error}</div>}

      <div>
        <label>Usuario: </label>
        <select
          value={selectedUser}
          onChange={e => setSelectedUser(e.target.value)}
        >
          <option value="">Seleccione un usuario</option>
          {users.map(u => (
            <option key={u.id} value={u.id}>
              {u.name}
            </option>
          ))}
        </select>
      </div>

      <div className="filter-buttons">
        <button
          className={filter === "all" ? "active" : ""}
          onClick={() => setFilter("all")}
        >
          Todas
        </button>
        <button
          className={filter === "completed" ? "active" : ""}
          onClick={() => setFilter("completed")}
        >
          Completadas
        </button>
        <button
          className={filter === "pending" ? "active" : ""}
          onClick={() => setFilter("pending")}
        >
          Pendientes
        </button>
      </div>

      <ul>
        {filteredTasks.map(task => (
          <li key={task.id}>
            <input
              type="checkbox"
              checked={!!task.completed}
              onChange={() => handleComplete(task.id)}
              disabled={!!task.completed}
            />
            <span
              style={{
                textDecoration: task.completed ? "line-through" : "none"
              }}
            >
              {task.title}
            </span>
            <button onClick={() => handleDelete(task.id)}>
              Eliminar
            </button>
          </li>
        ))}
      </ul>

      <form onSubmit={handleAddTask}>
        <input
          type="text"
          value={newTask}
          onChange={e => setNewTask(e.target.value)}
          placeholder="Nueva tarea"
        />
        <button type="submit">
          Agregar
        </button>
      </form>
    </div>
  );
}

export default App;
