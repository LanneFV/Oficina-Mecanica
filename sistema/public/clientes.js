function carregarClientes() {
    fetch('../app/controllers/ClienteController.php?acao=listar')
        .then(res => res.json())
        .then(data => {
            let tabela = document.getElementById('tabela');
            tabela.innerHTML = '';
            data.forEach(c => {
                tabela.innerHTML += `
                    <tr>
                        <td>${c.ID_cliente}</td>
                        <td>${c.nome}</td>
                        <td>${c.documento}</td>
                        <td>${c.perfil}</td>
                        <td style="display:flex;gap:6px;">
                            <button class="btn-edit" onclick="editar(${c.ID_cliente}, '${c.nome}', '${c.documento}')">Editar</button>
                            <button class="btn-delete" onclick="excluir(${c.ID_cliente})">Excluir</button>
                        </td>
                    </tr>`;
            });
        });
}

function salvar() {
    const id = document.getElementById('modal-id').value;
    const acao = id ? 'editar' : 'salvar';

    fetch('../app/controllers/ClienteController.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            acao: acao,
            id: id,
            nome: document.getElementById('m-nome').value,
            documento: document.getElementById('m-documento').value
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.sucesso) {
            fecharModal();
            carregarClientes();
        } else {
            document.getElementById('msg-modal').textContent = data.erro;
            document.getElementById('msg-modal').className = 'msg err';
            document.getElementById('msg-modal').style.display = 'block';
        }
    });
}

function editar(id, nome, documento) {
    document.getElementById('modal-titulo').textContent = 'Editar Cliente';
    document.getElementById('modal-id').value = id;
    document.getElementById('m-nome').value = nome;
    document.getElementById('m-documento').value = documento;
    document.getElementById('msg-modal').style.display = 'none';
    document.getElementById('modal-cliente').classList.add('open');
}

function excluir(id) {
    if (!confirm('Tem certeza que deseja excluir?')) return;
<<<<<<< HEAD
=======

>>>>>>> 312ae0909b19e373a5aeda9cee24fac3c143bd6f
    fetch('../app/controllers/ClienteController.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ acao: 'excluir', id: id })
    })
    .then(res => res.json())
    .then(data => {
<<<<<<< HEAD
        if (data.sucesso) {
            carregarClientes();
        } else {
            document.getElementById('msg-lista').textContent = data.erro;
            document.getElementById('msg-lista').className = 'msg err';
            document.getElementById('msg-lista').style.display = 'block';
        }
    });
=======
        document.getElementById('mensagem').textContent = data.sucesso ? 'Excluído com sucesso!' : data.erro;
        carregarClientes();
    });
}

function limpar() {
    document.getElementById('id_cliente').value = '';
    document.getElementById('nome').value = '';
    document.getElementById('documento').value = '';
    document.getElementById('titulo-form').textContent = 'Cadastrar Cliente';
    document.getElementById('mensagem').textContent = '';
>>>>>>> 312ae0909b19e373a5aeda9cee24fac3c143bd6f
}

carregarClientes();