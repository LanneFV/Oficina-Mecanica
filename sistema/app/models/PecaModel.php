<?php
class PecaModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function listar() {
        $stmt = $this->conn->prepare("SELECT ID_peca, nome, descricao, preco_unitario, nivel_estoque FROM pecas ORDER BY nome");
        $stmt->execute();
        $result = $stmt->get_result();
        $pecas = [];
        while ($row = $result->fetch_assoc()) { $pecas[] = $row; }
        $stmt->close();
        return $pecas;
    }

    public function salvar($nome, $descricao, $preco_unitario, $nivel_estoque) {
        $stmt = $this->conn->prepare("INSERT INTO pecas (nome, descricao, preco_unitario, nivel_estoque) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssdi", $nome, $descricao, $preco_unitario, $nivel_estoque);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function editar($id, $nome, $descricao, $preco_unitario, $nivel_estoque) {
        $stmt = $this->conn->prepare("UPDATE pecas SET nome = ?, descricao = ?, preco_unitario = ?, nivel_estoque = ? WHERE ID_peca = ?");
        $stmt->bind_param("ssdii", $nome, $descricao, $preco_unitario, $nivel_estoque, $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function excluir($id) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) AS total FROM itens_os_pecas WHERE ID_peca = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($row['total'] > 0) {
            return ["erro" => "Peça vinculada a uma ou mais ordens de serviço."];
        }

        $stmt = $this->conn->prepare("DELETE FROM pecas WHERE ID_peca = ?");
        $stmt->bind_param("i", $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok ? ["sucesso" => true] : ["erro" => "Erro ao excluir peça."];
    }

    public function nomeExiste($nome, $excluir_id = null) {
        if ($excluir_id) {
            $stmt = $this->conn->prepare("SELECT ID_peca FROM pecas WHERE nome = ? AND ID_peca != ?");
            $stmt->bind_param("si", $nome, $excluir_id);
        } else {
            $stmt = $this->conn->prepare("SELECT ID_peca FROM pecas WHERE nome = ?");
            $stmt->bind_param("s", $nome);
        }
        $stmt->execute();
        $stmt->store_result();
        $existe = $stmt->num_rows > 0;
        $stmt->close();
        return $existe;
    }
}
?>