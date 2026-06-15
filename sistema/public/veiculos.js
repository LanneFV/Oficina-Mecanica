function carregarVeiculos() {
    fetch('../app/controllers/VeiculoController.php?acao=listar')
        .then(res => res.json())
        .then(data => {
            let tabela = document.getElementById('tabela');
            tabela.innerHTML = '';
            data.forEach(v => {
                tabela.innerHTML += `
                    <tr>
                        <td>${v.ID_veiculo}</td>
                        <td>${v.placa}</td>
                        <td>${v.ano}</td>
                        <td>${v.cliente}</td>
                        <td>${v.modelo}</td>
                        <td style="display:flex;gap:6px;">
                            <button class="btn-edit" onclick="editar(${v.ID_veiculo}, '${v.placa}', ${v.ano}, ${v.id_cliente}, ${v.id_modelo})">Editar</button>
                            <button class="btn-delete" onclick="excluir(${v.ID_veiculo})">Excluir</button>
                        </td>
                    </tr>`;
            });
        });
}

function carregarClientes() {
    fetch('../app/controllers/ClienteController.php?acao=listar')
        .then(res => res.json())
        .then(data => {
            let select = document.getElementById('m-cliente');
            select.innerHTML = '<option value="">Selecione o Cliente</option>';
            data.forEach(c => {
                select.innerHTML += `<option value="${c.ID_cliente}">${c.nome}</option>`;
            });
        });
}

function carregarModelos() {
    fetch('../app/controllers/ModeloController.php?acao=listar')
        .then(res => res.json())
        .then(data => {
            let select = document.getElementById('m-modelo');
            select.innerHTML = '<option value="">Selecione o Modelo</option>';
            data.forEach(m => {
                select.innerHTML += `<option value="${m.ID_modelo}">${m.nome}</option>`;
            });
        });
}

function salvar() {
    const id = document.getElementById('modal-id').value;
    const acao = id ? 'editar' : 'salvar';

    fetch('../app/controllers/VeiculoController.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            acao: acao,
            id: id,
            placa: document.getElementById('m-placa').value,
            ano: document.getElementById('m-ano').value,
            id_cliente: document.getElementById('m-cliente').value,
            id_modelo: document.getElementById('m-modelo').value
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.sucesso) {
            fecharModal();
            carregarVeiculos();
        } else {
            document.getElementById('msg-modal').textContent = data.erro;
            document.getElementById('msg-modal').className = 'msg err';
            document.getElementById('msg-modal').style.display = 'block';
        }
    });
}

function editar(id, placa, ano, id_cliente, id_modelo) {
    document.getElementById('modal-titulo').textContent = 'Editar Veículo';
    document.getElementById('modal-id').value = id;
    document.getElementById('m-placa').value = placa;
    document.getElementById('m-ano').value = ano;
    document.getElementById('m-cliente').value = id_cliente;
    document.getElementById('m-modelo').value = id_modelo;
    document.getElementById('msg-modal').style.display = 'none';
    document.getElementById('modal-veiculo').classList.add('open');
}

function excluir(id) {
    if (!confirm('Tem certeza que deseja excluir?')) return;
    fetch('../app/controllers/VeiculoController.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ acao: 'excluir', id: id })
    })
    .then(res => res.json())
    .then(data => {
        if (data.sucesso) {
            carregarVeiculos();
        } else {
            document.getElementById('msg-lista').textContent = data.erro;
            document.getElementById('msg-lista').className = 'msg err';
            document.getElementById('msg-lista').style.display = 'block';
        }
    });
}

carregarVeiculos();
carregarClientes();
carregarModelos();