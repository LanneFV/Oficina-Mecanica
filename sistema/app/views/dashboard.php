<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
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
    <title>Painel — Oficina Mecânica</title>
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

        main { max-width: 900px; margin: 30px auto; padding: 0 16px; }

        .card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,.1);
            padding: 24px;
            margin-bottom: 24px;
        }

        table { width: 100%; border-collapse: collapse; font-size: 0.9rem; }
        th, td { padding: 10px 12px; text-align: left; border-bottom: 1px solid #e5e5e5; }
        th { background: #f0f0f0; font-weight: 600; }
        tr:last-child td { border-bottom: none; }

        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.78rem;
            font-weight: 600;
        }
        .badge-admin    { background: #fde68a; color: #92400e; }
        .badge-gerencia { background: #bfdbfe; color: #1e40af; }
        .badge-usuario  { background: #d1fae5; color: #065f46; }

        button {
            cursor: pointer;
            border: none;
            border-radius: 5px;
            padding: 7px 14px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        button:hover { opacity: 0.85; }
        .btn-primary { background: #1a1a2e; color: #fff; }
        .btn-edit    { background: #3b82f6; color: #fff; }
        .btn-delete  { background: #ef4444; color: #fff; }
        .btn-logout  { background: #6b7280; color: #fff; }
        .btn-cancel  { background: #e5e7eb; color: #333; }

        /* Modal */
        .modal-overlay {
            display: none;
            position: fixed; inset: 0;
            background: rgba(0,0,0,.5);
            z-index: 100;
            justify-content: center;
            align-items: center;
        }
        .modal-overlay.open { display: flex; }
        .modal {
            background: #fff;
            border-radius: 10px;
            padding: 28px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 8px 24px rgba(0,0,0,.2);
        }
        .modal h3 { margin-bottom: 18px; font-size: 1rem; color: #1a1a2e; }

        label { display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 4px; }
        input, select {
            width: 100%;
            padding: 8px 10px;
            border: 1px solid #d1d5db;
            border-radius: 5px;
            font-size: 0.9rem;
            margin-bottom: 14px;
        }
        input:focus, select:focus { outline: 2px solid #3b82f6; border-color: transparent; }

        .modal-footer { display: flex; gap: 10px; justify-content: flex-end; margin-top: 6px; }

        .msg {
            padding: 10px 14px;
            border-radius: 6px;
            font-size: 0.88rem;
            margin-bottom: 16px;
            display: none;
        }
        .msg.ok  { background: #d1fae5; color: #065f46; }
        .msg.err { background: #fee2e2; color: #991b1b; }

        .toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }
        .toolbar h2 { font-size: 1.1rem; color: #1a1a2e; }
    </style>
</head>
<body>

<header>
    <h1>Oficina Mecânica — Painel</h1>
    <div style="display:flex;align-items:center;gap:16px;">
        <span style="font-size:.9rem;opacity:.85;">
            Olá, <strong><?= $nomeSessao ?></strong>
            &nbsp;|&nbsp; Perfil: <strong><?= htmlspecialchars($perfilSessao) ?></strong>
        </span>
        <button class="btn-logout" onclick="fazerLogout()">Sair</button>
    </div>
</header>

<main>

    <?php if ($perfilSessao === 'administrador' || $perfilSessao === 'gerencia'): ?>
    <div class="card">
        <div class="toolbar">
            <h2>Usuários Cadastrados</h2>
            <?php if ($perfilSessao === 'administrador'): ?>
            <button class="btn-primary" onclick="abrirModalNovo()">+ Novo Usuário</button>
            <?php endif; ?>
        </div>

        <div id="msg-lista" class="msg"></div>

        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nome</th>
                    <th>Documento</th>
                    <th>Perfil</th>
                    <?php if ($perfilSessao === 'administrador'): ?>
                    <th>Ações</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody id="corpo-tabela">
                <tr><td colspan="5" style="text-align:center;color:#888;padding:20px">Carregando...</td></tr>
            </tbody>
        </table>
    </div>

    <?php else: ?>
    <div class="card">
        <p>Bem-vindo ao sistema. Seu perfil é <strong>Usuário Comum</strong>.</p>
    </div>
    <?php endif; ?>

</main>

<!-- Modal: Novo / Editar Usuário -->
<div id="modal-usuario" class="modal-overlay">
    <div class="modal">
        <h3 id="modal-titulo">Novo Usuário</h3>
        <input type="hidden" id="modal-id">

        <div id="msg-modal" class="msg"></div>

        <label for="m-nome">Nome completo</label>
        <input type="text" id="m-nome" placeholder="Nome do usuário">

        <label for="m-doc">Documento (CPF / CNPJ)</label>
        <input type="text" id="m-doc" placeholder="000.000.000-00">

        <label for="m-perfil">Perfil de acesso</label>
        <select id="m-perfil">
            <option value="">Selecione...</option>
            <option value="administrador">Administrador</option>
            <option value="gerencia">Gerência</option>
            <option value="usuario">Usuário Comum</option>
        </select>

        <div id="campo-senha">
            <label for="m-senha">Senha <small style="font-weight:normal;color:#666">(mín. 6 caracteres)</small></label>
            <input type="password" id="m-senha" placeholder="Senha">
        </div>

        <div class="modal-footer">
            <button class="btn-cancel" onclick="fecharModal()">Cancelar</button>
            <button class="btn-primary" id="btn-salvar" onclick="salvarUsuario()">Salvar</button>
        </div>
    </div>
</div>

<script>
const perfilSessao = "<?= $perfilSessao ?>";
const CONTROLLER   = "../controllers/usuariocontroller.php";

function mostrarMsg(id, texto, tipo) {
    const el = document.getElementById(id);
    el.textContent = texto;
    el.className = "msg " + tipo;
    el.style.display = "block";
    setTimeout(() => { el.style.display = "none"; }, 4000);
}

function badgePerfil(perfil) {
    const classes = { administrador: "badge-admin", gerencia: "badge-gerencia", usuario: "badge-usuario" };
    const labels  = { administrador: "Administrador", gerencia: "Gerência", usuario: "Usuário Comum" };
    return `<span class="badge ${classes[perfil] || ''}">${labels[perfil] || perfil}</span>`;
}

// ── Listar ──────────────────────────────────────────────────────

function carregarUsuarios() {
    fetch(CONTROLLER)
        .then(r => r.json())
        .then(data => {
            const tbody = document.getElementById("corpo-tabela");
            if (!Array.isArray(data) || data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;color:#888;padding:20px">Nenhum usuário cadastrado.</td></tr>';
                return;
            }
            tbody.innerHTML = data.map(u => `
                <tr>
                    <td>${u.ID_cliente}</td>
                    <td>${u.nome}</td>
                    <td>${u.documento}</td>
                    <td>${badgePerfil(u.perfil)}</td>
                    ${perfilSessao === 'administrador' ? `
                    <td style="display:flex;gap:6px;">
                        <button class="btn-edit"
                            onclick="abrirModalEditar(${u.ID_cliente}, '${u.nome.replace(/'/g,"\\'")}', '${u.documento}', '${u.perfil}')">
                            Editar
                        </button>
                        <button class="btn-delete"
                            onclick="excluirUsuario(${u.ID_cliente}, '${u.nome.replace(/'/g,"\\'")}')">
                            Excluir
                        </button>
                    </td>` : ''}
                </tr>
            `).join('');
        })
        .catch(() => mostrarMsg("msg-lista", "Erro ao carregar usuários.", "err"));
}

// ── Modal ───────────────────────────────────────────────────────

function abrirModalNovo() {
    document.getElementById("modal-titulo").textContent = "Novo Usuário";
    document.getElementById("modal-id").value  = "";
    document.getElementById("m-nome").value    = "";
    document.getElementById("m-doc").value     = "";
    document.getElementById("m-perfil").value  = "";
    document.getElementById("m-senha").value   = "";
    document.getElementById("campo-senha").style.display = "block";
    document.getElementById("msg-modal").style.display   = "none";
    document.getElementById("modal-usuario").classList.add("open");
}

function abrirModalEditar(id, nome, doc, perfil) {
    document.getElementById("modal-titulo").textContent = "Editar Usuário";
    document.getElementById("modal-id").value  = id;
    document.getElementById("m-nome").value    = nome;
    document.getElementById("m-doc").value     = doc;
    document.getElementById("m-perfil").value  = perfil;
    document.getElementById("campo-senha").style.display = "none";
    document.getElementById("msg-modal").style.display   = "none";
    document.getElementById("modal-usuario").classList.add("open");
}

function fecharModal() {
    document.getElementById("modal-usuario").classList.remove("open");
}

document.getElementById("modal-usuario").addEventListener("click", function(e) {
    if (e.target === this) fecharModal();
});

// ── Salvar ──────────────────────────────────────────────────────

function salvarUsuario() {
    const id     = document.getElementById("modal-id").value;
    const nome   = document.getElementById("m-nome").value.trim();
    const doc    = document.getElementById("m-doc").value.trim();
    const perfil = document.getElementById("m-perfil").value;
    const senha  = document.getElementById("m-senha").value.trim();
    const edicao = id !== "";

    if (!nome || !doc || !perfil) {
        mostrarMsg("msg-modal", "Preencha todos os campos obrigatórios.", "err");
        return;
    }
    if (!edicao && senha.length < 6) {
        mostrarMsg("msg-modal", "A senha deve ter no mínimo 6 caracteres.", "err");
        return;
    }

    const corpo  = edicao
        ? { id: parseInt(id), nome, documento: doc, perfil }
        : { nome, documento: doc, perfil, senha };
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
            carregarUsuarios();
        } else {
            mostrarMsg("msg-modal", data.erro || "Erro ao salvar.", "err");
        }
    })
    .catch(() => mostrarMsg("msg-modal", "Erro de conexão.", "err"))
    .finally(() => { btn.disabled = false; });
}

// ── Excluir ─────────────────────────────────────────────────────

function excluirUsuario(id, nome) {
    if (!confirm(`Excluir o usuário "${nome}"?\nEsta ação não pode ser desfeita.`)) return;

    fetch(CONTROLLER, {
        method: "DELETE",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ id })
    })
    .then(r => r.json())
    .then(data => {
        if (data.sucesso) {
            mostrarMsg("msg-lista", "Usuário excluído com sucesso.", "ok");
            carregarUsuarios();
        } else {
            mostrarMsg("msg-lista", data.erro || "Erro ao excluir.", "err");
        }
    })
    .catch(() => mostrarMsg("msg-lista", "Erro de conexão.", "err"));
}

// ── Logout ──────────────────────────────────────────────────────

function fazerLogout() {
    if (!confirm("Deseja sair do sistema?")) return;
    fetch("../controllers/logoutcontroller.php", { method: "POST" })
        .then(r => r.json())
        .then(data => { if (data.sucesso) window.location.href = "login.php"; })
        .catch(() => { window.location.href = "login.php"; });
}

// ── Init ─────────────────────────────────────────────────────────

if (perfilSessao === "administrador" || perfilSessao === "gerencia") {
    carregarUsuarios();
}
</script>
</body>
</html>
