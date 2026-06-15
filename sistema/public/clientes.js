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
                        <td>
                            <button class="btn-editar" onclick="editar(${c.ID_cliente}, '${c.nome}', '${c.documento}')">Editar</button>
                            <button class="btn-excluir" onclick="excluir(${c.ID_cliente})">Excluir</button>
                        </td>
                    </tr>`;
            });
        });
}

function salvar() {
    const id = document.getElementById('id_cliente').value;
    const acao = id ? 'editar' : 'salvar';

    fetch('../app/controllers/ClienteController.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            acao: acao,
            id: id,
            nome: document.getElementById('nome').value,
            documento: document.getElementById('documento').value
        })
    })
    .then(res => res.json())
    .then(data => {
        document.getElementById('mensagem').textContent = data.sucesso ? 'Salvo com sucesso!' : data.erro;
        limpar();
        carregarClientes();
    });
}

function editar(id, nome, documento) {
    document.getElementById('id_cliente').value = id;
    document.getElementById('nome').value = nome;
    document.getElementById('documento').value = documento;
    document.getElementById('titulo-form').textContent = 'Editar Cliente';
}

function excluir(id) {
    if (!confirm('Tem certeza que deseja excluir?')) return;

    fetch('../app/controllers/ClienteController.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ acao: 'excluir', id: id })
    })
    .then(res => res.json())
    .then(data => {
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
}

carregarClientes();