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
<<<<<<< HEAD
                        <td style="display:flex;gap:6px;">
                            <button class="btn-edit" onclick="editar(${v.ID_veiculo}, '${v.placa}', ${v.ano}, ${v.id_cliente}, ${v.id_modelo})">Editar</button>
                            <button class="btn-delete" onclick="excluir(${v.ID_veiculo})">Excluir</button>
=======
                        <td>
                            <button class="btn-editar" onclick="editar(${v.ID_veiculo}, '${v.placa}', ${v.ano}, ${v.id_cliente}, ${v.id_modelo})">Editar</button>
                            <button class="btn-excluir" onclick="excluir(${v.ID_veiculo})">Excluir</button>
>>>>>>> 312ae0909b19e373a5aeda9cee24fac3c143bd6f
                        </td>
                    </tr>`;
            });
        });
}

function carregarClientes() {
    fetch('../app/controllers/ClienteController.php?acao=listar')
        .then(res => res.json())
        .then(data => {
<<<<<<< HEAD
            let select = document.getElementById('m-cliente');
=======
            let select = document.getElementById('id_cliente');
>>>>>>> 312ae0909b19e373a5aeda9cee24fac3c143bd6f
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
<<<<<<< HEAD
            let select = document.getElementById('m-modelo');
=======
            let select = document.getElementById('id_modelo');
>>>>>>> 312ae0909b19e373a5aeda9cee24fac3c143bd6f
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
<<<<<<< HEAD
=======

>>>>>>> 312ae0909b19e373a5aeda9cee24fac3c143bd6f
    fetch('../app/controllers/VeiculoController.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ acao: 'excluir', id: id })
    })
    .then(res => res.json())
    .then(data => {
<<<<<<< HEAD
        if (data.sucesso) {
            carregarVeiculos();
        } else {
            document.getElementById('msg-lista').textContent = data.erro;
            document.getElementById('msg-lista').className = 'msg err';
            document.getElementById('msg-lista').style.display = 'block';
        }
    });
=======
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
>>>>>>> 312ae0909b19e373a5aeda9cee24fac3c143bd6f
}

carregarVeiculos();
carregarClientes();
carregarModelos();