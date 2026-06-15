<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

// Somente administrador e gerência acessam peças
if (!in_array($_SESSION['perfil'], ['administrador', 'gerencia'])) {
    header("Location: dashboard.php");
    exit;
}

$perfilSessao = $_SESSION['perfil'];
$nomeSessao   = htmlspecialchars($_SESSION['nome']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peças — Oficina Mecânica</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; background: #f4f4f4; color: #333; }

        header {
            background: #1a1a2e;
            color: #fff;
            padding: 14px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        header h1 { font-size: 1.2rem; }
        nav a {
            color: #fff;
            text-decoration: none;
            margin-left: 18px;
            font-size: .9rem;
            opacity: .75;
        }
        nav a:hover { opacity: 1; }
        nav a.ativa { opacity: 1; font-weight: 700; border-bottom: 2px solid #facc15; }

        main { max-width: 1000px; margin: 30px auto; padding: 0 16px; }

        .card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,.1);
            padding: 24px;
            margin-bottom: 24px;
        }

        table { width: 100%; border-collapse: collapse; font-size: 0.88rem; }
        th, td { padding: 10px 12px; text-align: left; border-bottom: 1px solid #e5e5e5; }
        th { background: #f0f0f0; font-weight: 600; }
        tr:last-child td { border-bottom: none; }

        .estoque-baixo { color: #dc2626; font-weight: 700; }
        .estoque-ok    { color: #059669; font-weight: 600; }

        button {
            cursor: pointer; border: none; border-radius: 5px;
            padding: 7px 14px; font-size: 0.85rem; font-weight: 600;
        }
        button:hover { opacity: 0.85; }
        .btn-primary { background: #1a1a2e; color: #fff; }
        .btn-edit    { background: #3b82f6; color: #fff; }
        .btn-delete  { background: #ef4444; color: #fff; }
        .btn-logout  { background: #6b7280; color: #fff; }
        .btn-cancel  { background: #e5e7eb; color: #333; }

        .modal-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,.5); z-index: 100;
            justify-content: center; align-items: center;
        }
        .modal-overlay.open { display: flex; }
        .modal {
            background: #fff; border-radius: 10px; padding: 28px;
            width: 100%; max-width: 440px;
            box-shadow: 0 8px 24px rgba(0,0,0,.2);
        }
        .modal h3 { margin-bottom: 18px; font-size: 1rem; color: #1a1a2e; }

        label { display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 4px; }
        input, textarea {
            width: 100%; padding: 8px 10px;
            border: 1px solid #d1d5db; border-radius: 5px;
            font-size: 0.9rem; margin-bottom: 14px;
            font-family: inherit;
        }
        textarea { resize: vertical; min-height: 80px; }
        input:focus, textarea:focus {
            outline: 2px solid #3b82f6; border-color: transparent;
        }

        .modal-footer { display: flex; gap: 10px; justify-content: flex-end; margin-top: 6px; }

        .msg {
            padding: 10px 14px; border-radius: 6px;
            font-size: 0.88rem; margin-bottom: 16px; display: none;
        }
        .msg.ok  { background: #d1fae5; color: #065f46; }
        .msg.err { background: #fee2e2; color: #991b1b; }

        .toolbar {
            display: flex; justify-content: space-between;
            align-items: center; margin-bottom: 16px;
        }
        .toolbar h2 { font-size: 1.1rem; color: #1a1a2e; }
    </style>
</head>
<body>

<header>
    <h1>Oficina Mecânica — Peças</h1>
    <div style="display:flex;align-items:center;gap:16px;">
        <nav>
            <a href="dashboard.php">Usuários</a>
            <a href="ordens.php">Ordens</a>
            <a href="pecas.php" class="ativa">Peças</a>
        </nav>
        <span style="font-size:.9rem;opacity:.85;">
            <strong><?= $nomeSessao ?></strong>
            &nbsp;|&nbsp; <strong><?= htmlspecialchars($perfilSessao) ?></strong>
        </span>
        <button class="btn-logout" onclick="fazerLogout()">Sair</button>
    </div>
</header>

<main>
    <div class="card">
        <div class="toolbar">
            <h2>Estoque de Peças</h2>
            <?php if ($perfilSessao === 'administrador'): ?>
            <button class="btn-primary" onclick="abrirModalNovo()">+ Nova Peça</button>
            <?php endif; ?>
        </div>

        <div id="msg-lista" class="msg"></div>

        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nome</th>
                    <th>Descrição</th>
                    <th>Preço Unit.</th>
                    <th>Estoque</th>
                    <?php if ($perfilSessao === 'administrador'): ?>
                    <th>Ações</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody id="corpo-tabela">
                <tr><td colspan="6" style="text-align:center;color:#888;padding:20px">Carregando...</td></tr>
            </tbody>
        </table>
    </div>
</main>

<!-- Modal Peça -->
<div id="modal-peca" class="modal-overlay">
    <div class="modal">
        <h3 id="modal-titulo">Nova Peça</h3>
        <input type="hidden" id="modal-id">
        <div id="msg-modal" class="msg"></div>

        <label for="m-nome">Nome da Peça</label>
        <input type="text" id="m-nome" placeholder="Ex: Filtro de óleo">

        <label for="m-descricao">Descrição</label>
        <textarea id="m-descricao" placeholder="Descrição detalhada da peça..."></textarea>

        <label for="m-preco">Preço Unitário (R$)</label>
        <input type="number" id="m-preco" min="0" step="0.01" placeholder="0.00">

        <label for="m-estoque">Nível de Estoque</label>
        <input type="number" id="m-estoque" min="0" placeholder="Quantidade disponível">

        <div class="modal-footer">
            <button class="btn-cancel" onclick="fecharModal()">Cancelar</button>
            <button class="btn-primary" id="btn-salvar" onclick="salvarPeca()">Salvar</button>
        </div>
    </div>
</div>

<script>
const perfil     = "<?= $perfilSessao ?>";
const CONTROLLER = "../controllers/pecacontroller.php";

function mostrarMsg(id, texto, tipo) {
    const el = document.getElementById(id);
    el.textContent = texto;
    el.className   = "msg " + tipo;
    el.style.display = "block";
    setTimeout(() => { el.style.display = "none"; }, 4000);
}

function formatarPreco(valor) {
    return parseFloat(valor).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
}

function carregarPecas() {
    fetch(CONTROLLER)
        .then(r => r.json())
        .then(data => {
            const tbody = document.getElementById("corpo-tabela");
            if (!Array.isArray(data) || data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;color:#888;padding:20px">Nenhuma peça cadastrada.</td></tr>';
                return;
            }
            tbody.innerHTML = data.map(p => {
                const estoqueCls = p.nivel_estoque <= 5 ? 'estoque-baixo' : 'estoque-ok';
                const estoqueLabel = p.nivel_estoque <= 5 ? p.nivel_estoque + ' ⚠' : p.nivel_estoque;
                return `
                <tr>
                    <td>${p.ID_peca}</td>
                    <td>${p.nome}</td>
                    <td style="max-width:240px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis" title="${p.descricao}">${p.descricao}</td>
                    <td>${formatarPreco(p.preco_unitario)}</td>
                    <td class="${estoqueCls}">${estoqueLabel}</td>
                    ${perfil === 'administrador' ? `
                    <td style="display:flex;gap:6px;">
                        <button class="btn-edit"
                            onclick="abrirModalEditar(${p.ID_peca},'${p.nome.replace(/'/g,"\\'")}','${p.descricao.replace(/'/g,"\\'").replace(/\n/g,' ')}',${p.preco_unitario},${p.nivel_estoque})">
                            Editar
                        </button>
                        <button class="btn-delete" onclick="excluirPeca(${p.ID_peca},'${p.nome.replace(/'/g,"\\'")}')">Excluir</button>
                    </td>` : ''}
                </tr>`;
            }).join('');
        })
        .catch(() => mostrarMsg("msg-lista", "Erro ao carregar peças.", "err"));
}

function abrirModalNovo() {
    document.getElementById("modal-titulo").textContent = "Nova Peça";
    document.getElementById("modal-id").value      = "";
    document.getElementById("m-nome").value        = "";
    document.getElementById("m-descricao").value   = "";
    document.getElementById("m-preco").value       = "";
    document.getElementById("m-estoque").value     = "";
    document.getElementById("msg-modal").style.display = "none";
    document.getElementById("modal-peca").classList.add("open");
}

function abrirModalEditar(id, nome, descricao, preco, estoque) {
    document.getElementById("modal-titulo").textContent = "Editar Peça";
    document.getElementById("modal-id").value      = id;
    document.getElementById("m-nome").value        = nome;
    document.getElementById("m-descricao").value   = descricao;
    document.getElementById("m-preco").value       = preco;
    document.getElementById("m-estoque").value     = estoque;
    document.getElementById("msg-modal").style.display = "none";
    document.getElementById("modal-peca").classList.add("open");
}

function fecharModal() {
    document.getElementById("modal-peca").classList.remove("open");
}

document.getElementById("modal-peca").addEventListener("click", function(e) {
    if (e.target === this) fecharModal();
});

function salvarPeca() {
    const id       = document.getElementById("modal-id").value;
    const nome     = document.getElementById("m-nome").value.trim();
    const descricao= document.getElementById("m-descricao").value.trim();
    const preco    = parseFloat(document.getElementById("m-preco").value);
    const estoque  = parseInt(document.getElementById("m-estoque").value);
    const edicao   = id !== "";

    if (!nome || !descricao || isNaN(preco) || isNaN(estoque)) {
        mostrarMsg("msg-modal", "Preencha todos os campos corretamente.", "err");
        return;
    }
    if (preco < 0 || estoque < 0) {
        mostrarMsg("msg-modal", "Preço e estoque não podem ser negativos.", "err");
        return;
    }

    const corpo  = edicao
        ? { id: parseInt(id), nome, descricao, preco_unitario: preco, nivel_estoque: estoque }
        : { nome, descricao, preco_unitario: preco, nivel_estoque: estoque };
    const method = edicao ? "PUT" : "POST";

    const btn = document.getElementById("btn-salvar");
    btn.disabled = true;

    fetch(CONTROLLER, {
        method,
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(corpo)
    })
    .then(r => r.json())
    .then(data => {
        if (data.sucesso) {
            fecharModal();
            mostrarMsg("msg-lista", data.mensagem, "ok");
            carregarPecas();
        } else {
            mostrarMsg("msg-modal", data.erro || "Erro ao salvar.", "err");
        }
    })
    .catch(() => mostrarMsg("msg-modal", "Erro de conexão.", "err"))
    .finally(() => { btn.disabled = false; });
}

function excluirPeca(id, nome) {
    if (!confirm(`Excluir a peça "${nome}"?\nEsta ação não pode ser desfeita.`)) return;

    fetch(CONTROLLER, {
        method: "DELETE",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ id })
    })
    .then(r => r.json())
    .then(data => {
        if (data.sucesso) {
            mostrarMsg("msg-lista", "Peça excluída com sucesso.", "ok");
            carregarPecas();
        } else {
            mostrarMsg("msg-lista", data.erro || "Erro ao excluir.", "err");
        }
    })
    .catch(() => mostrarMsg("msg-lista", "Erro de conexão.", "err"));
}

function fazerLogout() {
    if (!confirm("Deseja sair do sistema?")) return;
    fetch("../controllers/logoutcontroller.php", { method: "POST" })
        .then(r => r.json())
        .then(data => { if (data.sucesso) window.location.href = "login.php"; })
        .catch(() => { window.location.href = "login.php"; });
}

carregarPecas();
</script>
</body>
</html>
