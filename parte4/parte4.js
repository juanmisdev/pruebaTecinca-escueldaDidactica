// Parte 4 prueba, funcion que aplica un filtro a un array de tareas:
  function filterTasks(tasks, filter){
    if (filter === "all") return tasks;
    if (filter === "completed") return tasks.filter(task => task.completed === true);
    if (filter === "pending") return tasks.filter(task => task.completed === false);
    return tasks;
  };

const tasks = [
    { id: 1, title: "Task 1", completed: true },
    { id: 2, title: "Task 2", completed: false },
    { id: 3, title: "Task 3", completed: true },
    { id: 4, title: "Task 4", completed: false }
  ];
  
  console.log(filterTasks(tasks, "all"));       // todas las tareas
  console.log(filterTasks(tasks, "completed")); // Solo las completadas
  console.log(filterTasks(tasks, "pending"));   // Solo las pendientes