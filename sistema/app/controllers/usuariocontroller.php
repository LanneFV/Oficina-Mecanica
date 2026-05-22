<?php
if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json');

require_once '../../config/conexao.php';
require_once '../models/UsuarioModel.php';

if (!isset($_SESSION['id'])) {
    http_response_code(401);
    echo json_encode(['erro' => 'Não autenticado. Faça login primeiro.']);
    exit;
}

$model  = new UsuarioModel($conn);
$perfil = $_SESSION['perfil'];
$method = $_SERVER['REQUEST_METHOD'];

// ───── GET — listar usuários (admin e gerência) ─────
if ($method === 'GET') {
    if ($perfil !== 'administrador' && $perfil !== 'gerencia') {
        http_response_code(403);
        echo json_encode(['erro' => 'Acesso negado.']);
        exit;
    }
    echo json_encode($model->listar());
    exit;
}

// ───── Operações abaixo: somente administrador ─────
if ($perfil !== 'administrador') {
    http_response_code(403);
    echo json_encode(['erro' => 'Apenas administradores podem realizar esta ação.']);
    exit;
}

$dados = json_decode(file_get_contents('php://input'), true) ?? [];

// ───── POST — cadastrar novo usuário ─────
if ($method === 'POST') {
    foreach (['nome', 'documento', 'perfil', 'senha'] as $campo) {
        if (empty($dados[$campo])) {
            http_response_code(400);
            echo json_encode(['erro' => "Campo obrigatório ausente: $campo"]);
            exit;
        }
    }

    $perfis_validos = ['administrador', 'gerencia', 'usuario'];
    if (!in_array($dados['perfil'], $perfis_validos)) {
        http_response_code(400);
        echo json_encode(['erro' => 'Perfil inválido. Use: administrador, gerencia ou usuario.']);
        exit;
    }

    if (strlen($dados['senha']) < 6) {
        http_response_code(400);
        echo json_encode(['erro' => 'A senha deve ter no mínimo 6 caracteres.']);
        exit;
    }

    if ($model->documentoExiste($dados['documento'])) {
        http_response_code(409);
        echo json_encode(['erro' => 'Documento já cadastrado no sistema.']);
        exit;
    }

    $ok = $model->salvar(
        trim($dados['nome']),
        trim($dados['documento']),
        trim($dados['perfil']),
        trim($dados['senha'])
    );

    echo json_encode($ok
        ? ['sucesso' => true, 'mensagem' => 'Usuário cadastrado com sucesso.']
        : ['erro' => 'Erro interno ao cadastrar usuário.']
    );
    exit;
}

// ───── PUT — editar usuário ─────
if ($method === 'PUT') {
    $id = intval($dados['id'] ?? 0);
    if (!$id || empty($dados['nome']) || empty($dados['documento']) || empty($dados['perfil'])) {
        http_response_code(400);
        echo json_encode(['erro' => 'Dados incompletos para edição.']);
        exit;
    }

    if ($model->documentoExiste($dados['documento'], $id)) {
        http_response_code(409);
        echo json_encode(['erro' => 'Documento já cadastrado para outro usuário.']);
        exit;
    }

    $ok = $model->editar($id, trim($dados['nome']), trim($dados['documento']), trim($dados['perfil']));
    echo json_encode($ok
        ? ['sucesso' => true, 'mensagem' => 'Usuário atualizado com sucesso.']
        : ['erro' => 'Erro ao atualizar usuário.']
    );
    exit;
}

// ───── DELETE — excluir usuário ─────
if ($method === 'DELETE') {
    $id = intval($dados['id'] ?? 0);
    if (!$id) {
        http_response_code(400);
        echo json_encode(['erro' => 'ID inválido.']);
        exit;
    }

    if ($id === intval($_SESSION['id'])) {
        http_response_code(400);
        echo json_encode(['erro' => 'Você não pode excluir sua própria conta.']);
        exit;
    }

    echo json_encode($model->excluir($id, $perfil));
    exit;
}

http_response_code(405);
echo json_encode(['erro' => 'Método não permitido.']);
