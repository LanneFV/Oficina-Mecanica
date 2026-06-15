<<<<<<< HEAD
function carregarMecanicos() {
=======
function carregar() {
>>>>>>> 312ae0909b19e373a5aeda9cee24fac3c143bd6f
    fetch('../app/controllers/MecanicoController.php?acao=listar')
        .then(res => res.json())
        .then(data => {
            let tabela = document.getElementById('tabela');
            tabela.innerHTML = '';
            data.forEach(m => {
                tabela.innerHTML += `
                    <tr>
                        <td>${m.ID_mecanico}</td>
                        <td>${m.nome}</td>
                        <td>${m.especialidade}</td>
                        <td>${m.disponibilidade ? 'Disponível' : 'Indisponível'}</td>
<<<<<<< HEAD
                        <td style="display:flex;gap:6px;">
                            <button class="btn-edit" onclick="editar(${m.ID_mecanico}, '${m.nome}', '${m.especialidade}', ${m.disponibilidade})">Editar</button>
                            <button class="btn-delete" onclick="excluir(${m.ID_mecanico})">Excluir</button>
=======
                        <td>
                            <button class="btn-editar" onclick="editar(${m.ID_mecanico}, '${m.nome}', '${m.especialidade}', ${m.disponibilidade})">Editar</button>
                            <button class="btn-excluir" onclick="excluir(${m.ID_mecanico})">Excluir</button>
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
    const id = document.getElementById('id_mecanico').value;
>>>>>>> 312ae0909b19e373a5aeda9cee24fac3c143bd6f
    const acao = id ? 'editar' : 'salvar';

    fetch('../app/controllers/MecanicoController.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            acao: acao,
            id: id,
<<<<<<< HEAD
            nome: document.getElementById('m-nome').value,
            especialidade: document.getElementById('m-especialidade').value,
            disponibilidade: document.getElementById('m-disponibilidade').value
=======
            nome: document.getElementById('nome').value,
            especialidade: document.getElementById('especialidade').value,
            disponibilidade: document.getElementById('disponibilidade').value
>>>>>>> 312ae0909b19e373a5aeda9cee24fac3c143bd6f
        })
    })
    .then(res => res.json())
    .then(data => {
<<<<<<< HEAD
        if (data.sucesso) {
            fecharModal();
            carregarMecanicos();
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

function editar(id, nome, especialidade, disponibilidade) {
<<<<<<< HEAD
    document.getElementById('modal-titulo').textContent = 'Editar Mecânico';
    document.getElementById('modal-id').value = id;
    document.getElementById('m-nome').value = nome;
    document.getElementById('m-especialidade').value = especialidade;
    document.getElementById('m-disponibilidade').value = disponibilidade;
    document.getElementById('msg-modal').style.display = 'none';
    document.getElementById('modal-mecanico').classList.add('open');
=======
    document.getElementById('id_mecanico').value = id;
    document.getElementById('nome').value = nome;
    document.getElementById('especialidade').value = especialidade;
    document.getElementById('disponibilidade').value = disponibilidade;
    document.getElementById('titulo-form').textContent = 'Editar Mecânico';
>>>>>>> 312ae0909b19e373a5aeda9cee24fac3c143bd6f
}

function excluir(id) {
    if (!confirm('Tem certeza que deseja excluir?')) return;
<<<<<<< HEAD
=======

>>>>>>> 312ae0909b19e373a5aeda9cee24fac3c143bd6f
    fetch('../app/controllers/MecanicoController.php', {
        method: 'DELETE',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: id })
    })
    .then(res => res.json())
    .then(data => {
<<<<<<< HEAD
        if (data.sucesso) {
            carregarMecanicos();
        } else {
            document.getElementById('msg-lista').textContent = data.erro;
            document.getElementById('msg-lista').className = 'msg err';
            document.getElementById('msg-lista').style.display = 'block';
        }
    });
}

carregarMecanicos();
=======
        document.getElementById('mensagem').textContent = data.sucesso ? 'Excluído com sucesso!' : data.erro;
        carregar();
    });
}

function limpar() {
    document.getElementById('id_mecanico').value = '';
    document.getElementById('nome').value = '';
    document.getElementById('especialidade').value = '';
    document.getElementById('disponibilidade').value = '1';
    document.getElementById('titulo-form').textContent = 'Cadastrar Mecânico';
    document.getElementById('mensagem').textContent = '';
}

carregar();
>>>>>>> 312ae0909b19e373a5aeda9cee24fac3c143bd6f
