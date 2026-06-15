<?php
class OrdemModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function listar() {
        $stmt = $this->conn->prepare("
            SELECT os.ID_os, os.status, os.data_abertura, os.data_entrega_prevista, os.garantia_meses,
                   os.id_veiculo, os.id_mecanico,
                   v.placa, v.ano, mo.nome AS modelo, ma.nome_marca AS marca,
                   c.nome AS cliente, me.nome AS mecanico, me.especialidade
            FROM ordens_servicos os
            JOIN veiculos v  ON v.ID_veiculo   = os.id_veiculo
            JOIN modelos  mo ON mo.ID_modelo   = v.id_modelo
            JOIN marcas   ma ON ma.ID_marca    = mo.id_marca
            JOIN clientes c  ON c.ID_cliente   = v.id_cliente
            JOIN mecanicos me ON me.ID_mecanico = os.id_mecanico
            ORDER BY os.data_abertura DESC
        ");
        $stmt->execute();
        $result = $stmt->get_result();
        $ordens = [];
        while ($row = $result->fetch_assoc()) {
            $ordens[] = $row;
        }
        $stmt->close();
        return $ordens;
    }

    public function salvar($status, $data_entrega_prevista, $garantia_meses, $id_veiculo, $id_mecanico) {
        $stmt = $this->conn->prepare("INSERT INTO ordens_servicos (status, data_abertura, data_entrega_prevista, garantia_meses, id_veiculo, id_mecanico) VALUES (?, NOW(), ?, ?, ?, ?)");
        $stmt->bind_param("ssiii", $status, $data_entrega_prevista, $garantia_meses, $id_veiculo, $id_mecanico);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function editar($id, $status, $data_entrega_prevista, $garantia_meses, $id_veiculo, $id_mecanico) {
        $stmt = $this->conn->prepare("UPDATE ordens_servicos SET status = ?, data_entrega_prevista = ?, garantia_meses = ?, id_veiculo = ?, id_mecanico = ? WHERE ID_os = ?");
        $stmt->bind_param("ssiiii", $status, $data_entrega_prevista, $garantia_meses, $id_veiculo, $id_mecanico, $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function excluir($id) {
        $stmt = $this->conn->prepare("DELETE FROM itens_os_pecas WHERE ID_os = ?");
        $stmt->bind_param("i", $id); $stmt->execute(); $stmt->close();

        $stmt = $this->conn->prepare("DELETE FROM itens_os_servicos WHERE ID_os = ?");
        $stmt->bind_param("i", $id); $stmt->execute(); $stmt->close();

        $stmt = $this->conn->prepare("DELETE FROM ordens_servicos WHERE ID_os = ?");
        $stmt->bind_param("i", $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function listarPorCliente($id_cliente) {
        $stmt = $this->conn->prepare("
            SELECT os.ID_os, os.status, os.data_abertura, os.data_entrega_prevista, os.garantia_meses,
                   v.placa, mo.nome AS modelo, ma.nome_marca AS marca,
                   me.nome AS mecanico,
                   GROUP_CONCAT(DISTINCT sc.descricao ORDER BY sc.descricao SEPARATOR ', ') AS servicos
            FROM ordens_servicos os
            JOIN veiculos v   ON v.ID_veiculo    = os.id_veiculo
            JOIN modelos  mo  ON mo.ID_modelo    = v.id_modelo
            JOIN marcas   ma  ON ma.ID_marca     = mo.id_marca
            JOIN mecanicos me ON me.ID_mecanico  = os.id_mecanico
            LEFT JOIN itens_os_servicos ios ON ios.ID_os          = os.ID_os
            LEFT JOIN servicos_catalagos sc ON sc.ID_servico_ref  = ios.ID_servico_ref
            WHERE v.id_cliente = ?
            GROUP BY os.ID_os
            ORDER BY os.data_abertura DESC
        ");
        $stmt->bind_param("i", $id_cliente);
        $stmt->execute();
        $result = $stmt->get_result();
        $ordens = [];
        while ($row = $result->fetch_assoc()) {
            $ordens[] = $row;
        }
        $stmt->close();
        return $ordens;
    }

    public function listarVeiculos() {
        $stmt = $this->conn->prepare("SELECT v.ID_veiculo, v.placa, v.ano, mo.nome AS modelo, c.nome AS cliente FROM veiculos v JOIN modelos mo ON mo.ID_modelo = v.id_modelo JOIN clientes c ON c.ID_cliente = v.id_cliente ORDER BY c.nome");
        $stmt->execute();
        $result = $stmt->get_result();
        $lista = [];
        while ($row = $result->fetch_assoc()) { $lista[] = $row; }
        $stmt->close();
        return $lista;
    }

    public function listarMecanicos() {
        $stmt = $this->conn->prepare("SELECT ID_mecanico, nome, especialidade, disponibilidade FROM mecanicos ORDER BY nome");
        $stmt->execute();
        $result = $stmt->get_result();
        $lista = [];
        while ($row = $result->fetch_assoc()) { $lista[] = $row; }
        $stmt->close();
        return $lista;
    }
}
?>