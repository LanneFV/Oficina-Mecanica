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
    if (isset($_GET['itens'])) {
        $id_os = intval($_GET['id_os'] ?? 0);
        if (!$id_os) { http_response_code(400); echo json_encode(['erro' => 'ID da OS inválido.']); exit; }
        echo json_encode([
            'pecas'    => $model->listarItensPecas($id_os),
            'servicos' => $model->listarItensServicos($id_os),
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

if ($method === 'PATCH') {
    if ($perfil !== 'administrador' && $perfil !== 'gerencia') {
        http_response_code(403); echo json_encode(['erro' => 'Acesso negado.']); exit;
    }
    $dados  = json_decode(file_get_contents('php://input'), true) ?? [];
    $id_os  = intval($dados['id_os'] ?? 0);
    $tipo   = $dados['tipo'] ?? '';
    $acao   = $dados['acao'] ?? '';

    if (!$id_os || !in_array($tipo, ['peca','servico']) || !in_array($acao, ['adicionar','remover'])) {
        http_response_code(400); echo json_encode(['erro' => 'Dados inválidos.']); exit;
    }

    if ($tipo === 'peca') {
        if ($acao === 'adicionar') {
            $id_peca    = intval($dados['id_peca'] ?? 0);
            $quantidade = intval($dados['quantidade'] ?? 0);
            $preco      = floatval($dados['preco_venda'] ?? 0);
            if (!$id_peca || $quantidade <= 0 || $preco < 0) {
                http_response_code(400); echo json_encode(['erro' => 'Dados da peça inválidos.']); exit;
            }
            $ok = $model->adicionarPeca($id_os, $id_peca, $quantidade, $preco);
        } else {
            $ok = $model->removerPeca($id_os, intval($dados['id_peca'] ?? 0));
        }
    } else {
        if ($acao === 'adicionar') {
            $id_servico  = intval($dados['id_servico'] ?? 0);
            $valor       = floatval($dados['valor_cobrado'] ?? 0);
            $diagnostico = trim($dados['diagnostico_tecnico'] ?? '');
            if (!$id_servico || $valor < 0) {
                http_response_code(400); echo json_encode(['erro' => 'Dados do serviço inválidos.']); exit;
            }
            $ok = $model->adicionarServico($id_os, $id_servico, $valor, $diagnostico);
        } else {
            $ok = $model->removerServico($id_os, intval($dados['id_servico'] ?? 0));
        }
    }

    echo json_encode($ok ? ['sucesso' => true] : ['erro' => 'Erro ao atualizar itens.']);
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
