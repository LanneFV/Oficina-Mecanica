<?php
require_once '../../config/conexao.php';

function login($documento, $senha) {
    global $conn;

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $stmt = $conn->prepare("SELECT * FROM clientes WHERE documento = ?");
    $stmt->bind_param("s", $documento);
    $stmt->execute();
    $result  = $stmt->get_result();
    $usuario = $result->fetch_assoc();

    if ($usuario && password_verify($senha, $usuario['senha'])) {
        session_regenerate_id(true);
        $_SESSION['id']     = $usuario['ID_cliente'];
        $_SESSION['nome']   = $usuario['nome'];
        $_SESSION['perfil'] = $usuario['perfil'];
<<<<<<< HEAD
        echo json_encode([
            "sucesso" => true,
            "perfil"  => $usuario['perfil'],
            "nome"    => $usuario['nome'],
            "id"      => $usuario['ID_cliente']
        ]);
=======
        echo json_encode(["sucesso" => true, "perfil" => $usuario['perfil']]);
>>>>>>> 312ae0909b19e373a5aeda9cee24fac3c143bd6f
    } else {
        http_response_code(401);
        echo json_encode(["erro" => "Documento ou senha incorretos."]);
    }

    $stmt->close();
}

function logout() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    session_unset();
    session_destroy();
    echo json_encode(["sucesso" => true]);
}
?>