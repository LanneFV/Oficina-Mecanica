function carregar() {
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
                        <td>
                            <button class="btn-editar" onclick="editar(${m.ID_mecanico}, '${m.nome}', '${m.especialidade}', ${m.disponibilidade})">Editar</button>
                            <button class="btn-excluir" onclick="excluir(${m.ID_mecanico})">Excluir</button>
                        </td>
                    </tr>`;
            });
        });
}

function salvar() {
    const id = document.getElementById('id_mecanico').value;
    const acao = id ? 'editar' : 'salvar';

    fetch('../app/controllers/MecanicoController.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            acao: acao,
            id: id,
            nome: document.getElementById('nome').value,
            especialidade: document.getElementById('especialidade').value,
            disponibilidade: document.getElementById('disponibilidade').value
        })
    })
    .then(res => res.json())
    .then(data => {
        document.getElementById('mensagem').textContent = data.sucesso ? data.mensagem : data.erro;
        limpar();
        carregar();
    });
}

function editar(id, nome, especialidade, disponibilidade) {
    document.getElementById('id_mecanico').value = id;
    document.getElementById('nome').value = nome;
    document.getElementById('especialidade').value = especialidade;
    document.getElementById('disponibilidade').value = disponibilidade;
    document.getElementById('titulo-form').textContent = 'Editar Mecânico';
}

function excluir(id) {
    if (!confirm('Tem certeza que deseja excluir?')) return;

    fetch('../app/controllers/MecanicoController.php', {
        method: 'DELETE',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: id })
    })
    .then(res => res.json())
    .then(data => {
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