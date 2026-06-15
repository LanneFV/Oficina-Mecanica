<?php
class VeiculoModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function listar() {
        $stmt = $this->conn->prepare("SELECT v.ID_veiculo, v.placa, v.ano, v.id_cliente, v.id_modelo, c.nome AS cliente, m.nome AS modelo, ma.nome_marca AS marca FROM veiculos v JOIN clientes c ON v.id_cliente = c.ID_cliente JOIN modelos m ON v.id_modelo = m.ID_modelo JOIN marcas ma ON m.id_marca = ma.ID_marca ORDER BY v.placa");
        $stmt->execute();
        $result = $stmt->get_result();
        $veiculos = [];
        while ($row = $result->fetch_assoc()) { $veiculos[] = $row; }
        $stmt->close();
        return $veiculos;
    }

    public function salvar($placa, $ano, $id_cliente, $id_modelo) {
        $stmt = $this->conn->prepare("INSERT INTO veiculos (placa, ano, id_cliente, id_modelo) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("siii", $placa, $ano, $id_cliente, $id_modelo);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function editar($id, $placa, $ano, $id_cliente, $id_modelo) {
        $stmt = $this->conn->prepare("UPDATE veiculos SET placa = ?, ano = ?, id_cliente = ?, id_modelo = ? WHERE ID_veiculo = ?");
        $stmt->bind_param("siiii", $placa, $ano, $id_cliente, $id_modelo, $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function excluir($id, $perfil_usuario_logado) {
        if ($perfil_usuario_logado !== 'administrador') {
            return ["erro" => "Apenas administradores podem excluir registros."];
        }
        try {
            $stmt = $this->conn->prepare("DELETE FROM veiculos WHERE ID_veiculo = ?");
            $stmt->bind_param("i", $id);
            $ok = $stmt->execute();
            $stmt->close();
            return $ok ? ["sucesso" => true] : ["erro" => "Erro ao excluir veículo."];
        } catch (Exception $e) {
            return ["erro" => "Não é possível excluir um veículo vinculado a uma ordem de serviço."];
        }
    }
}
?>