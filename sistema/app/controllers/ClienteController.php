<?php
if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json');

require_once '../../config/conexao.php';
require_once '../models/ClienteModel.php';

if (!isset($_SESSION['id'])) {
    http_response_code(401);
    echo json_encode(['erro' => 'Não autenticado. Faça login primeiro.']);
    exit;
}

$model = new ClienteModel($conn);
$method = $_SERVER['REQUEST_METHOD'];
$acao = $_GET['acao'] ?? '';
$dados = [];

if ($method === 'POST') {
    $dados = json_decode(file_get_contents('php://input'), true) ?? [];
    $acao = $dados['acao'] ?? $acao;
}

$perfilSessao = $_SESSION['perfil'] ?? '';

if ($acao === 'listar') {
    echo json_encode($model->listar());
    exit;
}

if ($acao === 'salvar') {
    $nome = trim($dados['nome'] ?? '');
    $documento = trim($dados['documento'] ?? '');
    $id_endereco = 1;
    $perfil = 'usuario_comum';
    $senha = '123456';

    if (empty($nome) || empty($documento)) {
        echo json_encode(['erro' => 'Nome e documento são obrigatórios.']);
        exit;
    }

    if ($model->salvar($nome, $documento, $id_endereco, $perfil, $senha)) {
        echo json_encode(['sucesso' => true]);
    } else {
        echo json_encode(['erro' => 'Erro ao salvar cliente.']);
    }
    exit;
}

if ($acao === 'editar') {
    $id = intval($dados['id'] ?? 0);
    $nome = trim($dados['nome'] ?? '');
    $documento = trim($dados['documento'] ?? '');
    $id_endereco = 1;
    $perfil = 'usuario_comum';

    if (!$id || empty($nome) || empty($documento)) {
        echo json_encode(['erro' => 'Dados incompletos para edição.']);
        exit;
    }

    if ($model->editar($id, $nome, $documento, $id_endereco, $perfil)) {
        echo json_encode(['sucesso' => true]);
    } else {
        echo json_encode(['erro' => 'Erro ao atualizar cliente.']);
    }
    exit;
}

if ($acao === 'excluir') {
    $id = intval($dados['id'] ?? 0);
    if (!$id) {
        echo json_encode(['erro' => 'ID inválido.']);
        exit;
    }
    echo json_encode($model->excluir($id, $perfilSessao));
    exit;
}

http_response_code(400);
echo json_encode(['erro' => 'Ação ou método inválido.']);
?>