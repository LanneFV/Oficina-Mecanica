<?php
class ClienteModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function listar() {
        $stmt = $this->conn->prepare("SELECT c.ID_cliente, c.nome, c.documento, c.perfil, e.rua, e.numero, e.cidade, e.estado FROM clientes c LEFT JOIN enderecos e ON c.id_endereco = e.ID_endereco ORDER BY c.nome");
        $stmt->execute();
        $result = $stmt->get_result();
        $clientes = [];
        while ($row = $result->fetch_assoc()) {
            $clientes[] = $row;
        }
        $stmt->close();
        return $clientes;
    }

    public function salvar($nome, $documento, $id_endereco, $perfil, $senha) {
        $hash = password_hash($senha, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("INSERT INTO clientes (nome, documento, id_endereco, perfil, senha) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssiss", $nome, $documento, $id_endereco, $perfil, $hash);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function editar($id, $nome, $documento, $id_endereco, $perfil) {
        $stmt = $this->conn->prepare("UPDATE clientes SET nome = ?, documento = ?, id_endereco = ?, perfil = ? WHERE ID_cliente = ?");
        $stmt->bind_param("ssisi", $nome, $documento, $id_endereco, $perfil, $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function excluir($id, $perfil_usuario_logado) {
        if ($perfil_usuario_logado !== 'administrador') {
            return ["erro" => "Apenas administradores podem excluir registros."];
        }
        try {
            $stmt = $this->conn->prepare("DELETE FROM clientes WHERE ID_cliente = ?");
            $stmt->bind_param("i", $id);
            $ok = $stmt->execute();
            $stmt->close();
            return $ok ? ["sucesso" => true] : ["erro" => "Erro ao excluir cliente."];
        } catch (Exception $e) {
            return ["erro" => "Não é possível excluir um cliente que possui veículos ou contatos vinculados."];
        }
    }
}
?>