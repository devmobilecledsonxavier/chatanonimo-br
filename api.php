<?php
header('Access-Control-Allow-Origin: *'); // Permitir CORS
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

$dataFile = 'data.json';
if (!file_exists($dataFile)) {
    file_put_contents($dataFile, json_encode([]));
}

$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action === 'send') {
    $input = json_decode(file_get_contents('php://input'), true);
    $email = $input['email'] ?? '';
    $name = $input['name'] ?? 'Anônimo';
    $text = $input['text'] ?? '';
    $createdAt = time();

    if ($email && $name && $text) {
        $messages = json_decode(file_get_contents($dataFile), true);
        $id = 'msg_' . $createdAt;
        $messages[] = [
            'id' => $id,
            'email' => $email,
            'name' => $name,
            'text' => $text,
            'createdAt' => $createdAt
        ];
        file_put_contents($dataFile, json_encode($messages));
        echo json_encode(['status' => 'success', 'id' => $id]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Dados incompletos']);
    }
} elseif ($action === 'list') {
    $messages = json_decode(file_get_contents($dataFile), true);
    usort($messages, function($a, $b) {
        return $a['createdAt'] <=> $b['createdAt'];
    });
    echo json_encode($messages);
} elseif ($action === 'clear') {
    file_put_contents($dataFile, json_encode([]));
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Ação inválida']);
}