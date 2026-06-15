function carregarServicos() {
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
                        <td style="display:flex;gap:6px;">
                            <button class="btn-edit" onclick="editar(${s.ID_servico_ref}, '${s.descricao}', ${s.preco_base})">Editar</button>
                            <button class="btn-delete" onclick="excluir(${s.ID_servico_ref})">Excluir</button>
                        </td>
                    </tr>`;
            });
        });
}

function salvar() {
    const id = document.getElementById('modal-id').value;
    const acao = id ? 'editar' : 'salvar';

    fetch('../app/controllers/ServicoController.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            acao: acao,
            id: id,
            descricao: document.getElementById('m-descricao').value,
            preco_base: document.getElementById('m-preco').value
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.sucesso) {
            fecharModal();
            carregarServicos();
        } else {
            document.getElementById('msg-modal').textContent = data.erro;
            document.getElementById('msg-modal').className = 'msg err';
            document.getElementById('msg-modal').style.display = 'block';
        }
    });
}

function editar(id, descricao, preco_base) {
    document.getElementById('modal-titulo').textContent = 'Editar Serviço';
    document.getElementById('modal-id').value = id;
    document.getElementById('m-descricao').value = descricao;
    document.getElementById('m-preco').value = preco_base;
    document.getElementById('msg-modal').style.display = 'none';
    document.getElementById('modal-servico').classList.add('open');
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