<?php
if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json');

require_once '../../config/conexao.php';
require_once '../models/UsuarioModel.php';

if (!isset($_SESSION['id']) || $_SESSION['perfil'] !== 'administrador') {
    http_response_code(403);
    echo json_encode(['erro' => 'Acesso negado. Apenas administradores podem cadastrar usuários.']);
    exit;
}

$dados = json_decode(file_get_contents('php://input'), true);

foreach (['nome', 'documento', 'perfil', 'senha'] as $campo) {
    if (empty($dados[$campo])) {
        http_response_code(400);
        echo json_encode(['erro' => "Campo obrigatório ausente: $campo"]);
        exit;
    }
}

$nome      = trim($dados['nome']);
$documento = trim($dados['documento']);
$perfil    = trim($dados['perfil']);
$senha     = trim($dados['senha']);

$perfis_validos = ['administrador', 'gerencia', 'usuario'];
if (!in_array($perfil, $perfis_validos)) {
    http_response_code(400);
    echo json_encode(['erro' => 'Perfil inválido. Use: administrador, gerencia ou usuario.']);
    exit;
}

if (strlen($senha) < 6) {
    http_response_code(400);
    echo json_encode(['erro' => 'A senha deve ter no mínimo 6 caracteres.']);
    exit;
}

$model = new UsuarioModel($conn);

if ($model->documentoExiste($documento)) {
    http_response_code(409);
    echo json_encode(['erro' => 'Documento já cadastrado no sistema.']);
    exit;
}

$ok = $model->salvar($nome, $documento, $perfil, $senha);
echo json_encode($ok
    ? ['sucesso' => true, 'mensagem' => 'Usuário cadastrado com sucesso.']
    : ['erro' => 'Erro interno ao cadastrar usuário.']
);
