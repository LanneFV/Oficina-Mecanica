<?php
if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json');

require_once '../../config/conexao.php';
require_once '../models/ServicoModel.php';

if (!isset($_SESSION['id'])) {
    http_response_code(401);
    echo json_encode(['erro' => 'Não autenticado. Faça login primeiro.']);
    exit;
}

$model  = new ServicoModel($conn);
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

if ($perfil !== 'administrador') {
    http_response_code(403);
    echo json_encode(['erro' => 'Apenas administradores podem realizar esta ação.']);
    exit;
}

if ($method === 'POST' || $acao === 'salvar') {
    $descricao  = trim($dados['descricao'] ?? '');
    $preco_base = floatval($dados['preco_base'] ?? 0);

    if (empty($descricao) || $preco_base <= 0) {
        http_response_code(400);
        echo json_encode(['erro' => 'Descrição e preço base são obrigatórios.']);
        exit;
    }

    $ok = $model->salvar($descricao, $preco_base);
    echo json_encode($ok
        ? ['sucesso' => true, 'mensagem' => 'Serviço cadastrado com sucesso.']
        : ['erro' => 'Erro ao cadastrar serviço.']
    );
    exit;
}

if ($method === 'PUT' || $acao === 'editar') {
    $id         = intval($dados['id'] ?? 0);
    $descricao  = trim($dados['descricao'] ?? '');
    $preco_base = floatval($dados['preco_base'] ?? 0);

    if (!$id || empty($descricao) || $preco_base <= 0) {
        http_response_code(400);
        echo json_encode(['erro' => 'Dados incompletos para edição.']);
        exit;
    }

    $ok = $model->editar($id, $descricao, $preco_base);
    echo json_encode($ok
        ? ['sucesso' => true, 'mensagem' => 'Serviço atualizado com sucesso.']
        : ['erro' => 'Erro ao atualizar serviço.']
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