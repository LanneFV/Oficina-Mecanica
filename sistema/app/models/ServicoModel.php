<?php
class ServicoModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function listar() {
        $stmt = $this->conn->prepare("SELECT ID_servico_ref, descricao, preco_base FROM servicos_catalagos ORDER BY descricao");
        $stmt->execute();
        $result = $stmt->get_result();
        $servicos = [];
        while ($row = $result->fetch_assoc()) { $servicos[] = $row; }
        $stmt->close();
        return $servicos;
    }

    public function salvar($descricao, $preco_base) {
        $stmt = $this->conn->prepare("INSERT INTO servicos_catalagos (descricao, preco_base) VALUES (?, ?)");
        $stmt->bind_param("sd", $descricao, $preco_base);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function editar($id, $descricao, $preco_base) {
        $stmt = $this->conn->prepare("UPDATE servicos_catalagos SET descricao = ?, preco_base = ? WHERE ID_servico_ref = ?");
        $stmt->bind_param("sdi", $descricao, $preco_base, $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function excluir($id, $perfil_usuario_logado) {
        if ($perfil_usuario_logado !== 'administrador') {
            return ["erro" => "Apenas administradores podem excluir registros."];
        }

        $stmt = $this->conn->prepare("SELECT COUNT(*) AS total FROM itens_os_servicos WHERE ID_servico_ref = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($row['total'] > 0) {
            return ["erro" => "Serviço vinculado a ordens de serviço e não pode ser excluído."];
        }

        $stmt = $this->conn->prepare("DELETE FROM servicos_catalagos WHERE ID_servico_ref = ?");
        $stmt->bind_param("i", $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok ? ["sucesso" => true] : ["erro" => "Erro ao excluir serviço."];
    }
}
?>