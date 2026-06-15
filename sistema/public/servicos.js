<<<<<<< HEAD
function carregarServicos() {
=======
function carregar() {
>>>>>>> 312ae0909b19e373a5aeda9cee24fac3c143bd6f
    fetch('../app/controllers/ServicoController.php?acao=listar')
        .then(res => res.json())
        .then(data => {
            let tabela = document.getElementById('tabela');
            tabela.innerHTML = '';
            data.forEach(s => {
                tabela.innerHTML += `
                    <tr>
                        <td>${s.ID_servico_ref}</td>
                        <td>${s.descricao}</td>
                        <td>R$ ${parseFloat(s.preco_base).toFixed(2)}</td>
<<<<<<< HEAD
                        <td style="display:flex;gap:6px;">
                            <button class="btn-edit" onclick="editar(${s.ID_servico_ref}, '${s.descricao}', ${s.preco_base})">Editar</button>
                            <button class="btn-delete" onclick="excluir(${s.ID_servico_ref})">Excluir</button>
=======
                        <td>
                            <button class="btn-editar" onclick="editar(${s.ID_servico_ref}, '${s.descricao}', ${s.preco_base})">Editar</button>
                            <button class="btn-excluir" onclick="excluir(${s.ID_servico_ref})">Excluir</button>
>>>>>>> 312ae0909b19e373a5aeda9cee24fac3c143bd6f
                        </td>
                    </tr>`;
            });
        });
}

function salvar() {
<<<<<<< HEAD
    const id = document.getElementById('modal-id').value;
=======
    const id = document.getElementById('id_servico').value;
>>>>>>> 312ae0909b19e373a5aeda9cee24fac3c143bd6f
    const acao = id ? 'editar' : 'salvar';

    fetch('../app/controllers/ServicoController.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            acao: acao,
            id: id,
<<<<<<< HEAD
            descricao: document.getElementById('m-descricao').value,
            preco_base: document.getElementById('m-preco').value
=======
            descricao: document.getElementById('descricao').value,
            preco_base: document.getElementById('preco_base').value
>>>>>>> 312ae0909b19e373a5aeda9cee24fac3c143bd6f
        })
    })
    .then(res => res.json())
    .then(data => {
<<<<<<< HEAD
        if (data.sucesso) {
            fecharModal();
            carregarServicos();
        } else {
            document.getElementById('msg-modal').textContent = data.erro;
            document.getElementById('msg-modal').className = 'msg err';
            document.getElementById('msg-modal').style.display = 'block';
        }
=======
        document.getElementById('mensagem').textContent = data.sucesso ? data.mensagem : data.erro;
        limpar();
        carregar();
>>>>>>> 312ae0909b19e373a5aeda9cee24fac3c143bd6f
    });
}

function editar(id, descricao, preco_base) {
<<<<<<< HEAD
    document.getElementById('modal-titulo').textContent = 'Editar Serviço';
    document.getElementById('modal-id').value = id;
    document.getElementById('m-descricao').value = descricao;
    document.getElementById('m-preco').value = preco_base;
    document.getElementById('msg-modal').style.display = 'none';
    document.getElementById('modal-servico').classList.add('open');
=======
    document.getElementById('id_servico').value = id;
    document.getElementById('descricao').value = descricao;
    document.getElementById('preco_base').value = preco_base;
    document.getElementById('titulo-form').textContent = 'Editar Serviço';
>>>>>>> 312ae0909b19e373a5aeda9cee24fac3c143bd6f
}

function excluir(id) {
    if (!confirm('Tem certeza que deseja excluir?')) return;
<<<<<<< HEAD
=======

>>>>>>> 312ae0909b19e373a5aeda9cee24fac3c143bd6f
    fetch('../app/controllers/ServicoController.php', {
        method: 'DELETE',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: id })
    })
    .then(res => res.json())
    .then(data => {
<<<<<<< HEAD
        if (data.sucesso) {
            carregarServicos();
        } else {
            document.getElementById('msg-lista').textContent = data.erro;
            document.getElementById('msg-lista').className = 'msg err';
            document.getElementById('msg-lista').style.display = 'block';
        }
    });
}

carregarServicos();
=======
        document.getElementById('mensagem').textContent = data.sucesso ? 'Excluído com sucesso!' : data.erro;
        carregar();
    });
}

function limpar() {
    document.getElementById('id_servico').value = '';
    document.getElementById('descricao').value = '';
    document.getElementById('preco_base').value = '';
    document.getElementById('titulo-form').textContent = 'Cadastrar Serviço';
    document.getElementById('mensagem').textContent = '';
}

carregar();
>>>>>>> 312ae0909b19e373a5aeda9cee24fac3c143bd6f
