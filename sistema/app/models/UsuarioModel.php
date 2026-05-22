<?php

class UsuarioModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function salvar($nome, $documento, $perfil, $senha) {
        $hash = password_hash($senha, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare(
            "INSERT INTO clientes (nome, documento, perfil, senha) VALUES (?, ?, ?, ?)"
        );
        $stmt->bind_param("ssss", $nome, $documento, $perfil, $hash);
        return $stmt->execute();
    }

    public function listar() {
        $stmt = $this->conn->prepare(
            "SELECT ID_cliente, nome, documento, perfil FROM clientes ORDER BY nome"
        );
        $stmt->execute();
        $result = $stmt->get_result();
        $usuarios = [];
        while ($row = $result->fetch_assoc()) {
            $usuarios[] = $row;
        }
        return $usuarios;
    }

    public function buscarPorId($id) {
        $stmt = $this->conn->prepare(
            "SELECT ID_cliente, nome, documento, perfil FROM clientes WHERE ID_cliente = ?"
        );
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function editar($id, $nome, $documento, $perfil) {
        $stmt = $this->conn->prepare(
            "UPDATE clientes SET nome = ?, documento = ?, perfil = ? WHERE ID_cliente = ?"
        );
        $stmt->bind_param("sssi", $nome, $documento, $perfil, $id);
        return $stmt->execute();
    }

    public function excluir($id, $perfil_de_quem_exclui) {
        $stmt = $this->conn->prepare(
            "SELECT perfil FROM clientes WHERE ID_cliente = ?"
        );
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $alvo = $stmt->get_result()->fetch_assoc();

        if (!$alvo) {
            return ["erro" => "Usuário não encontrado."];
        }
        if ($alvo['perfil'] === 'administrador' && $perfil_de_quem_exclui !== 'administrador') {
            return ["erro" => "Gerência não pode excluir um administrador."];
        }

        $stmt = $this->conn->prepare("DELETE FROM clientes WHERE ID_cliente = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return ["sucesso" => true];
    }

    public function documentoExiste($documento, $excluir_id = null) {
        if ($excluir_id) {
            $stmt = $this->conn->prepare(
                "SELECT ID_cliente FROM clientes WHERE documento = ? AND ID_cliente != ?"
            );
            $stmt->bind_param("si", $documento, $excluir_id);
        } else {
            $stmt = $this->conn->prepare(
                "SELECT ID_cliente FROM clientes WHERE documento = ?"
            );
            $stmt->bind_param("s", $documento);
        }
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }
}
