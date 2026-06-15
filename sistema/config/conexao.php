<?php
$conn = new mysqli("127.0.0.1", "root", "Giulia123!", "oficina");
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro de conexão: ' . $conn->connect_error]);
    exit;
}
$conn->set_charset("utf8");
