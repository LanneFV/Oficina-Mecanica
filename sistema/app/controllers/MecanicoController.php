<?php
if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json');

require_once '../../config/conexao.php';
require_once '../models/MecanicoModel.php';

if (!isset($_SESSION['id'])) {
    http_response_code(401);
    echo json_encode(['erro' => 'Não autenticado. Faça login primeiro.']);
    exit;
}

$model  = new MecanicoModel($conn);
$perfil = $_SESSION['perfil'];
$method = $_SERVER['REQUEST_METHOD'];
$dados  = [];

if (in_array($method, ['POST', 'PUT', 'DELETE'])) {
    $dados = json_decode(file_get_contents('php://input'), true) ?? [];
}

$acao = $_GET['acao'] ?? $dados['acao'] ?? '';

if ($method === 'GET' || $acao === 'listar') {
    echo json_encode($model->listar());
    exit;
}

if ($perfil !== 'administrador' && $perfil !== 'gerencia') {
    http_response_code(403);
    echo json_encode(['erro' => 'Acesso negado.']);
    exit;
}

if ($method === 'POST' || $acao === 'salvar') {
    $nome            = trim($dados['nome'] ?? '');
    $especialidade   = trim($dados['especialidade'] ?? '');
    $disponibilidade = intval($dados['disponibilidade'] ?? 1);

    if (empty($nome) || empty($especialidade)) {
        http_response_code(400);
        echo json_encode(['erro' => 'Nome e especialidade são obrigatórios.']);
        exit;
    }

    $ok = $model->salvar($nome, $especialidade, $disponibilidade);
    echo json_encode($ok
        ? ['sucesso' => true, 'mensagem' => 'Mecânico cadastrado com sucesso.']
        : ['erro' => 'Erro ao cadastrar mecânico.']
    );
    exit;
}

if ($method === 'PUT' || $acao === 'editar') {
    $id              = intval($dados['id'] ?? 0);
    $nome            = trim($dados['nome'] ?? '');
    $especialidade   = trim($dados['especialidade'] ?? '');
    $disponibilidade = intval($dados['disponibilidade'] ?? 1);

    if (!$id || empty($nome) || empty($especialidade)) {
        http_response_code(400);
        echo json_encode(['erro' => 'Dados incompletos para edição.']);
        exit;
    }

    $ok = $model->editar($id, $nome, $especialidade, $disponibilidade);
    echo json_encode($ok
        ? ['sucesso' => true, 'mensagem' => 'Mecânico atualizado com sucesso.']
        : ['erro' => 'Erro ao atualizar mecânico.']
    );
    exit;
}

if ($method === 'DELETE' || $acao === 'excluir') {
    $id = intval($dados['id'] ?? 0);
    if (!$id) { http_response_code(400); echo json_encode(['erro' => 'ID inválido.']); exit; }
    echo json_encode($model->excluir($id, $perfil));
    exit;
}

http_response_code(405);
echo json_encode(['erro' => 'Método não permitido.']);
?>