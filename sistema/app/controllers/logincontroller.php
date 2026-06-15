<?php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once '../../config/conexao.php';
require_once '../models/AuthModel.php';

header('Content-Type: application/json');

$json      = json_decode(file_get_contents('php://input'), true);
$documento = trim($json['documento'] ?? $_POST['documento'] ?? '');
$senha     = trim($json['senha']     ?? $_POST['senha']     ?? '');

if (empty($documento) || empty($senha)) {
    http_response_code(400);
    echo json_encode(['erro' => 'Documento e senha são obrigatórios.']);
    exit;
}

login($documento, $senha);