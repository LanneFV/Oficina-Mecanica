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
                   c.nome AS cliente, me.nome AS mecanico, me.especialidade,
                   GROUP_CONCAT(DISTINCT sc.descricao ORDER BY sc.descricao SEPARATOR ', ') AS servicos,
                   GROUP_CONCAT(DISTINCT p.nome ORDER BY p.nome SEPARATOR ', ')             AS pecas
            FROM ordens_servicos os
            JOIN veiculos v   ON v.ID_veiculo    = os.id_veiculo
            JOIN modelos  mo  ON mo.ID_modelo    = v.id_modelo
            JOIN marcas   ma  ON ma.ID_marca     = mo.id_marca
            JOIN clientes c   ON c.ID_cliente    = v.id_cliente
            JOIN mecanicos me ON me.ID_mecanico  = os.id_mecanico
            LEFT JOIN itens_os_servicos ios ON ios.ID_os         = os.ID_os
            LEFT JOIN servicos_catalagos sc ON sc.ID_servico_ref = ios.ID_servico_ref
            LEFT JOIN itens_os_pecas    iop ON iop.ID_os         = os.ID_os
            LEFT JOIN pecas              p  ON p.ID_peca          = iop.ID_peca
            GROUP BY os.ID_os
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

    public function listarItensPecas($id_os) {
        $stmt = $this->conn->prepare("
            SELECT iop.ID_peca, p.nome, iop.quantidade, iop.preco_venda
            FROM itens_os_pecas iop
            JOIN pecas p ON p.ID_peca = iop.ID_peca
            WHERE iop.ID_os = ?
            ORDER BY p.nome
        ");
        $stmt->bind_param("i", $id_os);
        $stmt->execute();
        $result = $stmt->get_result();
        $lista = [];
        while ($row = $result->fetch_assoc()) { $lista[] = $row; }
        $stmt->close();
        return $lista;
    }

    public function listarItensServicos($id_os) {
        $stmt = $this->conn->prepare("
            SELECT ios.ID_servico_ref, sc.descricao, ios.valor_cobrado, ios.diagnostico_tecnico
            FROM itens_os_servicos ios
            JOIN servicos_catalagos sc ON sc.ID_servico_ref = ios.ID_servico_ref
            WHERE ios.ID_os = ?
            ORDER BY sc.descricao
        ");
        $stmt->bind_param("i", $id_os);
        $stmt->execute();
        $result = $stmt->get_result();
        $lista = [];
        while ($row = $result->fetch_assoc()) { $lista[] = $row; }
        $stmt->close();
        return $lista;
    }

    public function adicionarPeca($id_os, $id_peca, $quantidade, $preco_venda) {
        $stmt = $this->conn->prepare("
            INSERT INTO itens_os_pecas (ID_os, ID_peca, quantidade, preco_venda)
            VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE quantidade = ?, preco_venda = ?
        ");
        $stmt->bind_param("iiidid", $id_os, $id_peca, $quantidade, $preco_venda, $quantidade, $preco_venda);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function removerPeca($id_os, $id_peca) {
        $stmt = $this->conn->prepare("DELETE FROM itens_os_pecas WHERE ID_os = ? AND ID_peca = ?");
        $stmt->bind_param("ii", $id_os, $id_peca);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function adicionarServico($id_os, $id_servico, $valor_cobrado, $diagnostico) {
        $stmt = $this->conn->prepare("
            INSERT INTO itens_os_servicos (ID_os, ID_servico_ref, valor_cobrado, diagnostico_tecnico)
            VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE valor_cobrado = ?, diagnostico_tecnico = ?
        ");
        $stmt->bind_param("iidsds", $id_os, $id_servico, $valor_cobrado, $diagnostico, $valor_cobrado, $diagnostico);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function removerServico($id_os, $id_servico) {
        $stmt = $this->conn->prepare("DELETE FROM itens_os_servicos WHERE ID_os = ? AND ID_servico_ref = ?");
        $stmt->bind_param("ii", $id_os, $id_servico);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
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