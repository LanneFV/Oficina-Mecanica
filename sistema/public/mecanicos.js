function carregarMecanicos() {
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
                        <td style="display:flex;gap:6px;">
                            <button class="btn-edit" onclick="editar(${m.ID_mecanico}, '${m.nome}', '${m.especialidade}', ${m.disponibilidade})">Editar</button>
                            <button class="btn-delete" onclick="excluir(${m.ID_mecanico})">Excluir</button>
                        </td>
                    </tr>`;
            });
        });
}
function salvar() {
    const id = document.getElementById('modal-id').value;
    const acao = id ? 'editar' : 'salvar';
    fetch('../app/controllers/MecanicoController.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            acao: acao, id: id,
            nome: document.getElementById('m-nome').value,
            especialidade: document.getElementById('m-especialidade').value,
            disponibilidade: document.getElementById('m-disponibilidade').value
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.sucesso) {
            fecharModal();
            carregarMecanicos();
        } else {
            document.getElementById('msg-modal').textContent = data.erro;
            document.getElementById('msg-modal').className = 'msg err';
            document.getElementById('msg-modal').style.display = 'block';
        }
    });
}
function editar(id, nome, especialidade, disponibilidade) {
    document.getElementById('modal-titulo').textContent = 'Editar Mecânico';
    document.getElementById('modal-id').value = id;
    document.getElementById('m-nome').value = nome;
    document.getElementById('m-especialidade').value = especialidade;
    document.getElementById('m-disponibilidade').value = disponibilidade;
    document.getElementById('msg-modal').style.display = 'none';
    document.getElementById('modal-mecanico').classList.add('open');
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