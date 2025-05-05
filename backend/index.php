<?php
require_once __DIR__ . '/db/Database.php';
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Allow all origins for CORS
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Manejo de preflight CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = str_replace('/pruebaTecinca-escueldaDidactica/backend', '', $uri);

$db = Database::getInstance()->getConnection();

switch (true) {

    // GET /users
    case $method === 'GET' && $uri === '/users':
        $stmt = $db->query('SELECT * FROM users');
        echo json_encode($stmt->fetchAll());
        break;

    // POST /users
    case $method === 'POST' && $uri === '/users':
        $data = json_decode(file_get_contents('php://input'), true);
        if (!isset($data['name']) || !isset($data['email'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing name or email']);
            break;
        }
        $stmt = $db->prepare('INSERT INTO users (name, email) VALUES (?, ?)');
        $stmt->execute([$data['name'], $data['email']]);
        echo json_encode(['id' => $db->lastInsertId()]);
        break;

    // GET /tasks
    case $method === 'GET' && $uri === '/tasks':
        $stmt = $db->query('SELECT * FROM tasks');
        echo json_encode($stmt->fetchAll());
        break;

    // POST /tasks
    case $method === 'POST' && $uri === '/tasks':
        $data = json_decode(file_get_contents('php://input'), true);
        if (!isset($data['title']) || !isset($data['user_id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing title or user_id']);
            break;
        }
        $stmt = $db->prepare('INSERT INTO tasks (title, user_id, completed) VALUES (?, ?, 0)');
        $stmt->execute([$data['title'], $data['user_id']]);
        echo json_encode(['id' => $db->lastInsertId()]);
        break;

    // PUT /tasks/{id}
    case $method === 'PUT' && preg_match('#^/tasks/(\d+)$#', $uri, $matches):
        $taskId = $matches[1];
        // Verificar si la tarea existe
        $stmt = $db->prepare('SELECT id FROM tasks WHERE id = ?');
        $stmt->execute([$taskId]);
        if (!$stmt->fetch()) {
            http_response_code(404);
            echo json_encode(['error' => 'Tarea no encontrada']);
            break;
        }
        $stmt = $db->prepare('UPDATE tasks SET completed = 1 WHERE id = ?');
        $stmt->execute([$taskId]);
        echo json_encode(['updated' => $stmt->rowCount()]);
        break;

    // DELETE /tasks/{id}
    case $method === 'DELETE' && preg_match('#^/tasks/(\d+)$#', $uri, $matches):
        $taskId = $matches[1];
        // Verificar si la tarea existe
        $stmt = $db->prepare('SELECT id FROM tasks WHERE id = ?');
        $stmt->execute([$taskId]);
        if (!$stmt->fetch()) {
            http_response_code(404);
            echo json_encode(['error' => 'Tarea no encontrada']);
            break;
        }
        $stmt = $db->prepare('DELETE FROM tasks WHERE id = ?');
        $stmt->execute([$taskId]);
        echo json_encode(['deleted' => $stmt->rowCount()]);
        break;

    default:
        http_response_code(404);
        echo json_encode(['error' => 'Not found']);
        break;
}