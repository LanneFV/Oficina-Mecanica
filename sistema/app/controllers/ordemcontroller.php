<?php
if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json');

require_once '../../config/conexao.php';
require_once '../models/OrdemModel.php';

if (!isset($_SESSION['id'])) {
    http_response_code(401);
    echo json_encode(['erro' => 'Não autenticado. Faça login primeiro.']);
    exit;
}

$model  = new OrdemModel($conn);
$perfil = $_SESSION['perfil'];
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    if (isset($_GET['selects'])) {
        echo json_encode([
            'veiculos'  => $model->listarVeiculos(),
            'mecanicos' => $model->listarMecanicos(),
        ]);
        exit;
    }
    if ($perfil === 'usuario_comum') {
        echo json_encode($model->listarPorCliente(intval($_SESSION['id'])));
        exit;
    }
    echo json_encode($model->listar());
    exit;
}

if ($method === 'POST') {
    if ($perfil !== 'administrador' && $perfil !== 'gerencia') {
        http_response_code(403);
        echo json_encode(['erro' => 'Acesso negado.']);
        exit;
    }

    $dados = json_decode(file_get_contents('php://input'), true) ?? [];

    foreach (['status', 'id_veiculo', 'id_mecanico'] as $campo) {
        if (empty($dados[$campo])) {
            http_response_code(400);
            echo json_encode(['erro' => "Campo obrigatório ausente: $campo"]);
            exit;
        }
    }

    $status_validos = ['aberta', 'em andamento', 'concluida', 'cancelada'];
    if (!in_array($dados['status'], $status_validos)) {
        http_response_code(400);
        echo json_encode(['erro' => 'Status inválido.']);
        exit;
    }

    $ok = $model->salvar(
        trim($dados['status']),
        $dados['data_entrega_prevista'] ?? null,
        intval($dados['garantia_meses'] ?? 0),
        intval($dados['id_veiculo']),
        intval($dados['id_mecanico'])
    );

    echo json_encode($ok
        ? ['sucesso' => true, 'mensagem' => 'Ordem de serviço criada com sucesso.']
        : ['erro' => 'Erro interno ao criar ordem de serviço.']
    );
    exit;
}

if ($method === 'PUT') {
    if ($perfil !== 'administrador') {
        http_response_code(403);
        echo json_encode(['erro' => 'Apenas administradores podem editar ordens de serviço.']);
        exit;
    }

    $dados = json_decode(file_get_contents('php://input'), true) ?? [];
    $id    = intval($dados['id'] ?? 0);

    if (!$id || empty($dados['status']) || empty($dados['id_veiculo']) || empty($dados['id_mecanico'])) {
        http_response_code(400);
        echo json_encode(['erro' => 'Dados incompletos para edição.']);
        exit;
    }

    $ok = $model->editar(
        $id,
        trim($dados['status']),
        $dados['data_entrega_prevista'] ?? null,
        intval($dados['garantia_meses'] ?? 0),
        intval($dados['id_veiculo']),
        intval($dados['id_mecanico'])
    );

    echo json_encode($ok
        ? ['sucesso' => true, 'mensagem' => 'Ordem de serviço atualizada com sucesso.']
        : ['erro' => 'Erro ao atualizar ordem de serviço.']
    );
    exit;
}

if ($method === 'DELETE') {
    if ($perfil !== 'administrador') {
        http_response_code(403);
        echo json_encode(['erro' => 'Apenas administradores podem excluir ordens de serviço.']);
        exit;
    }

    $dados = json_decode(file_get_contents('php://input'), true) ?? [];
    $id    = intval($dados['id'] ?? 0);

    if (!$id) {
        http_response_code(400);
        echo json_encode(['erro' => 'ID inválido.']);
        exit;
    }

    $ok = $model->excluir($id);
    echo json_encode($ok
        ? ['sucesso' => true]
        : ['erro' => 'Erro ao excluir ordem de serviço.']
    );
    exit;
}

http_response_code(405);
echo json_encode(['erro' => 'Método não permitido.']);
