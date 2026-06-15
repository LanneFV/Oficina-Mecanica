function carregar() {
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
                        <td>
                            <button class="btn-editar" onclick="editar(${s.ID_servico_ref}, '${s.descricao}', ${s.preco_base})">Editar</button>
                            <button class="btn-excluir" onclick="excluir(${s.ID_servico_ref})">Excluir</button>
                        </td>
                    </tr>`;
            });
        });
}

function salvar() {
    const id = document.getElementById('id_servico').value;
    const acao = id ? 'editar' : 'salvar';

    fetch('../app/controllers/ServicoController.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            acao: acao,
            id: id,
            descricao: document.getElementById('descricao').value,
            preco_base: document.getElementById('preco_base').value
        })
    })
    .then(res => res.json())
    .then(data => {
        document.getElementById('mensagem').textContent = data.sucesso ? data.mensagem : data.erro;
        limpar();
        carregar();
    });
}

function editar(id, descricao, preco_base) {
    document.getElementById('id_servico').value = id;
    document.getElementById('descricao').value = descricao;
    document.getElementById('preco_base').value = preco_base;
    document.getElementById('titulo-form').textContent = 'Editar Serviço';
}

function excluir(id) {
    if (!confirm('Tem certeza que deseja excluir?')) return;

    fetch('../app/controllers/ServicoController.php', {
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
    document.getElementById('id_servico').value = '';
    document.getElementById('descricao').value = '';
    document.getElementById('preco_base').value = '';
    document.getElementById('titulo-form').textContent = 'Cadastrar Serviço';
    document.getElementById('mensagem').textContent = '';
}

carregar();