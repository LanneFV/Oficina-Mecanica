<?php
if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json');

require_once '../../config/conexao.php';
require_once '../models/PecaModel.php';

if (!isset($_SESSION['id'])) {
    http_response_code(401);
    echo json_encode(['erro' => 'Não autenticado. Faça login primeiro.']);
    exit;
}

$model  = new PecaModel($conn);
$perfil = $_SESSION['perfil'];
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    if ($perfil !== 'administrador' && $perfil !== 'gerencia') {
        http_response_code(403);
        echo json_encode(['erro' => 'Acesso negado.']);
        exit;
    }
    echo json_encode($model->listar());
    exit;
}

if ($perfil !== 'administrador') {
    http_response_code(403);
    echo json_encode(['erro' => 'Apenas administradores podem realizar esta ação.']);
    exit;
}

$dados = json_decode(file_get_contents('php://input'), true) ?? [];

if ($method === 'POST') {
    foreach (['nome', 'descricao', 'preco_unitario', 'nivel_estoque'] as $campo) {
        if (!isset($dados[$campo]) || $dados[$campo] === '') {
            http_response_code(400);
            echo json_encode(['erro' => "Campo obrigatório ausente: $campo"]);
            exit;
        }
    }

    $preco   = floatval($dados['preco_unitario']);
    $estoque = intval($dados['nivel_estoque']);

    if ($preco < 0) { echo json_encode(['erro' => 'O preço não pode ser negativo.']); exit; }
    if ($estoque < 0) { echo json_encode(['erro' => 'O estoque não pode ser negativo.']); exit; }

    if ($model->nomeExiste(trim($dados['nome']))) {
        http_response_code(409);
        echo json_encode(['erro' => 'Já existe uma peça com esse nome.']);
        exit;
    }

    $ok = $model->salvar(trim($dados['nome']), trim($dados['descricao']), $preco, $estoque);
    echo json_encode($ok
        ? ['sucesso' => true, 'mensagem' => 'Peça cadastrada com sucesso.']
        : ['erro' => 'Erro interno ao cadastrar peça.']
    );
    exit;
}

if ($method === 'PUT') {
    $id = intval($dados['id'] ?? 0);
    if (!$id || empty($dados['nome']) || empty($dados['descricao']) || !isset($dados['preco_unitario']) || !isset($dados['nivel_estoque'])) {
        http_response_code(400);
        echo json_encode(['erro' => 'Dados incompletos para edição.']);
        exit;
    }

    $preco   = floatval($dados['preco_unitario']);
    $estoque = intval($dados['nivel_estoque']);

    if ($preco < 0) { echo json_encode(['erro' => 'O preço não pode ser negativo.']); exit; }
    if ($estoque < 0) { echo json_encode(['erro' => 'O estoque não pode ser negativo.']); exit; }

    if ($model->nomeExiste(trim($dados['nome']), $id)) {
        http_response_code(409);
        echo json_encode(['erro' => 'Já existe outra peça com esse nome.']);
        exit;
    }

    $ok = $model->editar($id, trim($dados['nome']), trim($dados['descricao']), $preco, $estoque);
    echo json_encode($ok
        ? ['sucesso' => true, 'mensagem' => 'Peça atualizada com sucesso.']
        : ['erro' => 'Erro ao atualizar peça.']
    );
    exit;
}

if ($method === 'DELETE') {
    $id = intval($dados['id'] ?? 0);
    if (!$id) { http_response_code(400); echo json_encode(['erro' => 'ID inválido.']); exit; }
    echo json_encode($model->excluir($id));
    exit;
}

http_response_code(405);
echo json_encode(['erro' => 'Método não permitido.']);