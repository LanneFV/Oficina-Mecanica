<?php
if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json');
require_once '../../config/conexao.php';

if (!isset($_SESSION['id'])) {
    echo json_encode([]);
    exit;
}

$acao = $_GET['acao'] ?? '';

if ($acao === 'listar') {
    $stmt = $conn->prepare("SELECT m.ID_modelo, CONCAT(ma.nome_marca, ' — ', m.nome) AS nome FROM modelos m JOIN marcas ma ON ma.ID_marca = m.id_marca ORDER BY ma.nome_marca, m.nome");
    $stmt->execute();
    $result = $stmt->get_result();

    $modelos = [];
    while ($row = $result->fetch_assoc()) {
        $modelos[] = $row;
    }
    $stmt->close();

    echo json_encode($modelos);
    exit;
}

http_response_code(400);
echo json_encode(['erro' => 'Ação inválida.']);
?>