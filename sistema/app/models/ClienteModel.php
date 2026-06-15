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

    public function salvar($nome, $documento, $senha) {
        $hash = password_hash($senha, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("INSERT INTO clientes (nome, documento, senha) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $nome, $documento, $hash);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function editar($id, $nome, $documento) {
        $stmt = $this->conn->prepare("UPDATE clientes SET nome = ?, documento = ? WHERE ID_cliente = ?");
        $stmt->bind_param("ssi", $nome, $documento, $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function excluir($id, $perfil_usuario_logado) {
        if ($perfil_usuario_logado !== 'administrador') {
            return ["erro" => "Apenas administradores podem excluir registros."];
        }
        $stmt = $this->conn->prepare("DELETE FROM clientes WHERE ID_cliente = ?");
        $stmt->bind_param("i", $id);
        $ok = $stmt->execute();
        $errno = $this->conn->errno;
        $stmt->close();
        if (!$ok) {
            if ($errno === 1451) {
                return ["erro" => "Não é possível excluir um cliente que possui veículos ou contatos vinculados."];
            }
            return ["erro" => "Erro ao excluir cliente."];
        }
        return ["sucesso" => true];
    }
}
?>