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
                        <td>
                            <button class="btn-editar" onclick="editar(${v.ID_veiculo}, '${v.placa}', ${v.ano}, ${v.id_cliente}, ${v.id_modelo})">Editar</button>
                            <button class="btn-excluir" onclick="excluir(${v.ID_veiculo})">Excluir</button>
                        </td>
                    </tr>`;
            });
        });
}

function carregarClientes() {
    fetch('../app/controllers/ClienteController.php?acao=listar')
        .then(res => res.json())
        .then(data => {
            let select = document.getElementById('id_cliente');
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
            let select = document.getElementById('id_modelo');
            select.innerHTML = '<option value="">Selecione o Modelo</option>';
            data.forEach(m => {
                select.innerHTML += `<option value="${m.ID_modelo}">${m.nome}</option>`;
            });
        });
}

function salvar() {
    const id = document.getElementById('id_veiculo').value;
    const acao = id ? 'editar' : 'salvar';

    fetch('../app/controllers/VeiculoController.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            acao: acao,
            id: id,
            placa: document.getElementById('placa').value,
            ano: document.getElementById('ano').value,
            id_cliente: document.getElementById('id_cliente').value,
            id_modelo: document.getElementById('id_modelo').value
        })
    })
    .then(res => res.json())
    .then(data => {
        document.getElementById('mensagem').textContent = data.sucesso ? 'Salvo com sucesso!' : data.erro;
        limpar();
        carregarVeiculos();
    });
}

function editar(id, placa, ano, id_cliente, id_modelo) {
    document.getElementById('id_veiculo').value = id;
    document.getElementById('placa').value = placa;
    document.getElementById('ano').value = ano;
    document.getElementById('id_cliente').value = id_cliente;
    document.getElementById('id_modelo').value = id_modelo;
    document.getElementById('titulo-form').textContent = 'Editar Veículo';
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
        document.getElementById('mensagem').textContent = data.sucesso ? 'Excluído com sucesso!' : data.erro;
        carregarVeiculos();
    });
}

function limpar() {
    document.getElementById('id_veiculo').value = '';
    document.getElementById('placa').value = '';
    document.getElementById('ano').value = '';
    document.getElementById('titulo-form').textContent = 'Cadastrar Veículo';
    document.getElementById('mensagem').textContent = '';
}

carregarVeiculos();
carregarClientes();
carregarModelos();