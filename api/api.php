<?php

require 'database.php';

// Rota para buscar todas as tarefas
if ($_SERVER['REQUEST_METHOD'] === 'GET' && empty($_GET)) {
    try {
        $stmt = $conn->query('SELECT * FROM tasks');
        $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
        header('Content-Type: application/json');
        echo json_encode($tasks);
    } catch(PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
}

// Rota para adicionar uma nova tarefa
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (empty($data['title'])) {
        echo json_encode(['error' => 'O título da tarefa é obrigatório']);
        exit;
    }

    $title = $data['title'];

    try {
        $stmt = $conn->prepare('INSERT INTO tasks (title) VALUES (:title)');
        $stmt->bindParam(':title', $title);
        $stmt->execute();
        $taskId = $conn->lastInsertId();
        echo json_encode(['id' => $taskId, 'title' => $title, 'completed' => false]);
    } catch(PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
}

// Rota para marcar uma tarefa como concluída
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (empty($data['id'])) {
        echo json_encode(['error' => 'O ID da tarefa é obrigatório']);
        exit;
    }

    $taskId = $data['id'];

    try {
        $stmt = $conn->prepare('UPDATE tasks SET completed = 1 WHERE id = :id');
        $stmt->bindParam(':id', $taskId);
        $stmt->execute();
        echo json_encode(['success' => true]);
    } catch(PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
}

// Rota para deletar uma tarefa
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (empty($data['id'])) {
        echo json_encode(['error' => 'O ID da tarefa é obrigatório']);
        exit;
    }

    $taskId = $data['id'];

    try {
        $stmt = $conn->prepare('DELETE FROM tasks WHERE id = :id');
        $stmt->bindParam(':id', $taskId);
        $stmt->execute();
        echo json_encode(['success' => true]);
    } catch(PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
}