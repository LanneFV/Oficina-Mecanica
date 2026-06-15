<?php
class MecanicoModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function listar() {
        $stmt = $this->conn->prepare("SELECT ID_mecanico, nome, especialidade, disponibilidade FROM mecanicos ORDER BY nome");
        $stmt->execute();
        $result = $stmt->get_result();
        $mecanicos = [];
        while ($row = $result->fetch_assoc()) { $mecanicos[] = $row; }
        $stmt->close();
        return $mecanicos;
    }

    public function salvar($nome, $especialidade, $disponibilidade) {
        $stmt = $this->conn->prepare("INSERT INTO mecanicos (nome, especialidade, disponibilidade) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $nome, $especialidade, $disponibilidade);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function editar($id, $nome, $especialidade, $disponibilidade) {
        $stmt = $this->conn->prepare("UPDATE mecanicos SET nome = ?, especialidade = ?, disponibilidade = ? WHERE ID_mecanico = ?");
        $stmt->bind_param("ssii", $nome, $especialidade, $disponibilidade, $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function excluir($id, $perfil_usuario_logado) {
        if ($perfil_usuario_logado !== 'administrador') {
            return ["erro" => "Apenas administradores podem excluir registros."];
        }

        $stmt = $this->conn->prepare("SELECT COUNT(*) AS total FROM ordens_servicos WHERE id_mecanico = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($row['total'] > 0) {
            return ["erro" => "Mecânico vinculado a ordens de serviço e não pode ser excluído."];
        }

        $stmt = $this->conn->prepare("DELETE FROM mecanicos WHERE ID_mecanico = ?");
        $stmt->bind_param("i", $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok ? ["sucesso" => true] : ["erro" => "Erro ao excluir mecânico."];
    }
}
?>