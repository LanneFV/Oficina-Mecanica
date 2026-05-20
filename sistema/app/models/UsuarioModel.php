<?php

class UsuarioModel {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function listar() {
        $stmt = $this->conn->prepare("SELECT ID_cliente, nome, documento, perfil FROM clientes");
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function buscar($id) {
        $stmt = $this->conn->prepare("SELECT ID_cliente, nome, documento, perfil FROM clientes WHERE ID_cliente = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function salvar($nome, $documento, $perfil, $senha) {
    $hash = password_hash($senha, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("INSERT INTO clientes (nome, documento, perfil, senha) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nome, $documento, $perfil, $hash);
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }

   
    public function editar($id, $nome, $documento, $perfil) {
        $stmt = $this->conn->prepare("UPDATE clientes SET nome = ?, documento = ?, perfil = ? WHERE ID_cliente = ?");
        $stmt->bind_param("sssi", $nome, $documento, $perfil, $id);
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }

    public function excluir($id) {
        $stmt = $this->conn->prepare("DELETE FROM clientes WHERE ID_cliente = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }
}

