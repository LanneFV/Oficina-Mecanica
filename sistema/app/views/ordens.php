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
    <title>Ordens de Serviço — Oficina Mecânica</title>
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

        main { max-width: 1200px; margin: 30px auto; padding: 0 16px; }

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

        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.78rem;
            font-weight: 600;
        }
        .badge-aberta       { background: #fde68a; color: #92400e; }
        .badge-em-andamento { background: #bfdbfe; color: #1e40af; }
        .badge-concluida    { background: #d1fae5; color: #065f46; }
        .badge-cancelada    { background: #fee2e2; color: #991b1b; }

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
            width: 100%; max-width: 480px;
            box-shadow: 0 8px 24px rgba(0,0,0,.2);
            max-height: 90vh; overflow-y: auto;
        }
        .modal h3 { margin-bottom: 18px; font-size: 1rem; color: #1a1a2e; }

        label { display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 4px; }
        input, select, textarea {
            width: 100%; padding: 8px 10px;
            border: 1px solid #d1d5db; border-radius: 5px;
            font-size: 0.9rem; margin-bottom: 14px;
            font-family: inherit;
        }
        input:focus, select:focus, textarea:focus {
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
    <h1>Oficina Mecânica — Ordens de Serviço</h1>
    <div style="display:flex;align-items:center;gap:16px;">
        <nav>
            <a href="dashboard.php">Usuários</a>
            <a href="ordens.php" class="ativa">Ordens</a>
            <?php if ($perfilSessao === 'administrador'): ?>
            <a href="pecas.php">Peças</a>
            <?php endif; ?>
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
            <h2>Ordens de Serviço</h2>
            <?php if ($perfilSessao === 'administrador' || $perfilSessao === 'gerencia'): ?>
            <button class="btn-primary" onclick="abrirModalNovo()">+ Nova OS</button>
            <?php endif; ?>
        </div>

        <div id="msg-lista" class="msg"></div>

        <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Status</th>
                    <th>Placa</th>
                    <th>Cliente</th>
                    <th>Mecânico</th>
                    <th>Serviços</th>
                    <th>Peças</th>
                    <th>Abertura</th>
                    <th>Entrega Prevista</th>
                    <th>Garantia</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody id="corpo-tabela">
                <tr><td colspan="11" style="text-align:center;color:#888;padding:20px">Carregando...</td></tr>
            </tbody>
        </table>
        </div>
    </div>
</main>

<!-- Modal OS -->
<div id="modal-os" class="modal-overlay">
    <div class="modal">
        <h3 id="modal-titulo">Nova Ordem de Serviço</h3>
        <input type="hidden" id="modal-id">
        <div id="msg-modal" class="msg"></div>

        <label for="m-status">Status</label>
        <select id="m-status">
            <option value="">Selecione...</option>
            <option value="aberta">Aberta</option>
            <option value="em andamento">Em Andamento</option>
            <option value="concluida">Concluída</option>
            <option value="cancelada">Cancelada</option>
        </select>

        <label for="m-veiculo">Veículo</label>
        <select id="m-veiculo">
            <option value="">Selecione...</option>
        </select>

        <label for="m-mecanico">Mecânico</label>
        <select id="m-mecanico">
            <option value="">Selecione...</option>
        </select>

        <label for="m-entrega">Data de Entrega Prevista</label>
        <input type="date" id="m-entrega">

        <label for="m-garantia">Garantia (meses)</label>
        <input type="number" id="m-garantia" min="0" placeholder="Ex: 6">

        <div class="modal-footer">
            <button class="btn-cancel" onclick="fecharModal()">Cancelar</button>
            <button class="btn-primary" id="btn-salvar" onclick="salvarOS()">Salvar</button>
        </div>
    </div>
</div>

<!-- Modal Itens da OS -->
<div id="modal-itens" class="modal-overlay">
    <div class="modal" style="max-width:640px;">
        <h3>Itens da OS <span id="itens-os-id" style="color:#6b7280;font-weight:normal;font-size:0.9rem;"></span></h3>
        <div id="msg-itens" class="msg"></div>

        <!-- Peças -->
        <div style="margin-bottom:20px;">
            <h4 style="font-size:0.95rem;color:#1a1a2e;margin-bottom:10px;">Peças</h4>
            <table style="width:100%;border-collapse:collapse;font-size:0.85rem;margin-bottom:10px;">
                <thead>
                    <tr>
                        <th style="text-align:left;padding:6px 8px;background:#f0f0f0;">Peça</th>
                        <th style="text-align:left;padding:6px 8px;background:#f0f0f0;">Qtd</th>
                        <th style="text-align:left;padding:6px 8px;background:#f0f0f0;">Preço Venda</th>
                        <th style="padding:6px 8px;background:#f0f0f0;"></th>
                    </tr>
                </thead>
                <tbody id="itens-pecas-lista"></tbody>
            </table>
            <div style="display:flex;gap:8px;align-items:flex-end;flex-wrap:wrap;">
                <div>
                    <label style="font-size:0.8rem;font-weight:600;display:block;margin-bottom:3px;">Peça</label>
                    <select id="add-peca-id" style="width:180px;padding:6px;border:1px solid #d1d5db;border-radius:5px;font-size:0.85rem;margin-bottom:0;">
                        <option value="">Selecione...</option>
                    </select>
                </div>
                <div>
                    <label style="font-size:0.8rem;font-weight:600;display:block;margin-bottom:3px;">Qtd</label>
                    <input type="number" id="add-peca-qtd" min="1" value="1" style="width:70px;padding:6px;border:1px solid #d1d5db;border-radius:5px;font-size:0.85rem;margin-bottom:0;">
                </div>
                <div>
                    <label style="font-size:0.8rem;font-weight:600;display:block;margin-bottom:3px;">Preço (R$)</label>
                    <input type="number" id="add-peca-preco" min="0" step="0.01" placeholder="0.00" style="width:90px;padding:6px;border:1px solid #d1d5db;border-radius:5px;font-size:0.85rem;margin-bottom:0;">
                </div>
                <button onclick="adicionarPeca()" style="background:#1a1a2e;color:#fff;border:none;border-radius:5px;padding:7px 14px;font-size:0.85rem;font-weight:600;cursor:pointer;">+ Adicionar</button>
            </div>
        </div>

        <hr style="border:none;border-top:1px solid #e5e5e5;margin-bottom:20px;">

        <!-- Serviços -->
        <div style="margin-bottom:10px;">
            <h4 style="font-size:0.95rem;color:#1a1a2e;margin-bottom:10px;">Serviços</h4>
            <table style="width:100%;border-collapse:collapse;font-size:0.85rem;margin-bottom:10px;">
                <thead>
                    <tr>
                        <th style="text-align:left;padding:6px 8px;background:#f0f0f0;">Serviço</th>
                        <th style="text-align:left;padding:6px 8px;background:#f0f0f0;">Valor</th>
                        <th style="text-align:left;padding:6px 8px;background:#f0f0f0;">Diagnóstico</th>
                        <th style="padding:6px 8px;background:#f0f0f0;"></th>
                    </tr>
                </thead>
                <tbody id="itens-servicos-lista"></tbody>
            </table>
            <div style="display:flex;gap:8px;align-items:flex-end;flex-wrap:wrap;">
                <div>
                    <label style="font-size:0.8rem;font-weight:600;display:block;margin-bottom:3px;">Serviço</label>
                    <select id="add-serv-id" style="width:180px;padding:6px;border:1px solid #d1d5db;border-radius:5px;font-size:0.85rem;margin-bottom:0;">
                        <option value="">Selecione...</option>
                    </select>
                </div>
                <div>
                    <label style="font-size:0.8rem;font-weight:600;display:block;margin-bottom:3px;">Valor (R$)</label>
                    <input type="number" id="add-serv-valor" min="0" step="0.01" placeholder="0.00" style="width:90px;padding:6px;border:1px solid #d1d5db;border-radius:5px;font-size:0.85rem;margin-bottom:0;">
                </div>
                <div>
                    <label style="font-size:0.8rem;font-weight:600;display:block;margin-bottom:3px;">Diagnóstico</label>
                    <input type="text" id="add-serv-diag" placeholder="Opcional" style="width:180px;padding:6px;border:1px solid #d1d5db;border-radius:5px;font-size:0.85rem;margin-bottom:0;">
                </div>
                <button onclick="adicionarServico()" style="background:#1a1a2e;color:#fff;border:none;border-radius:5px;padding:7px 14px;font-size:0.85rem;font-weight:600;cursor:pointer;">+ Adicionar</button>
            </div>
        </div>

        <div class="modal-footer" style="margin-top:20px;">
            <button class="btn-cancel" onclick="fecharModalItens()">Fechar</button>
        </div>
    </div>
</div>

<script>
const perfil     = "<?= $perfilSessao ?>";
const CONTROLLER = "../controllers/ordemcontroller.php";

function mostrarMsg(id, texto, tipo) {
    const el = document.getElementById(id);
    el.textContent = texto;
    el.className   = "msg " + tipo;
    el.style.display = "block";
    setTimeout(() => { el.style.display = "none"; }, 4000);
}

function badgeStatus(status) {
    const map = {
        "aberta":       ["badge-aberta",       "Aberta"],
        "em andamento": ["badge-em-andamento",  "Em Andamento"],
        "concluida":    ["badge-concluida",     "Concluída"],
        "cancelada":    ["badge-cancelada",     "Cancelada"],
    };
    const [cls, label] = map[status] ?? ["", status];
    return `<span class="badge ${cls}">${label}</span>`;
}

function carregarOS() {
    fetch(CONTROLLER)
        .then(r => r.json())
        .then(data => {
            const tbody = document.getElementById("corpo-tabela");
            if (!Array.isArray(data) || data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="11" style="text-align:center;color:#888;padding:20px">Nenhuma OS cadastrada.</td></tr>';
                return;
            }
            tbody.innerHTML = data.map(os => `
                <tr>
                    <td>${os.ID_os}</td>
                    <td>${badgeStatus(os.status)}</td>
                    <td>${os.placa}</td>
                    <td>${os.cliente}</td>
                    <td>${os.mecanico}</td>
                    <td style="max-width:160px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis" title="${os.servicos ?? ''}">${os.servicos ?? '—'}</td>
                    <td style="max-width:160px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis" title="${os.pecas ?? ''}">${os.pecas ?? '—'}</td>
                    <td>${os.data_abertura ? os.data_abertura.substring(0,10) : '-'}</td>
                    <td>${os.data_entrega_prevista ?? '-'}</td>
                    <td>${os.garantia_meses ? os.garantia_meses + ' mes(es)' : '-'}</td>
                    ${perfil === 'administrador' ? `
                    <td style="display:flex;gap:6px;">
                        <button class="btn-edit"
                            onclick="abrirModalEditar(${os.ID_os},'${os.status}',${os.id_veiculo},${os.id_mecanico},'${os.data_entrega_prevista ?? ''}',${os.garantia_meses ?? 0})">
                            Editar
                        </button>
                        <button style="background:#8b5cf6;color:#fff;cursor:pointer;border:none;border-radius:5px;padding:7px 14px;font-size:0.85rem;font-weight:600;"
                            onclick="abrirModalItens(${os.ID_os})">Itens</button>
                        <button class="btn-delete" onclick="excluirOS(${os.ID_os})">Excluir</button>
                    </td>` : `
                    <td>
                        <button style="background:#8b5cf6;color:#fff;cursor:pointer;border:none;border-radius:5px;padding:7px 14px;font-size:0.85rem;font-weight:600;"
                            onclick="abrirModalItens(${os.ID_os})">Itens</button>
                    </td>`}
                </tr>
            `).join('');
        })
        .catch(() => mostrarMsg("msg-lista", "Erro ao carregar ordens.", "err"));
}

function carregarSelects() {
    fetch(CONTROLLER + "?selects=1")
        .then(r => r.json())
        .then(data => {
            const sv = document.getElementById("m-veiculo");
            const sm = document.getElementById("m-mecanico");

            (data.veiculos ?? []).forEach(v => {
                sv.innerHTML += `<option value="${v.ID_veiculo}">${v.placa} — ${v.cliente} (${v.modelo})</option>`;
            });
            (data.mecanicos ?? []).forEach(m => {
                sm.innerHTML += `<option value="${m.ID_mecanico}">${m.nome} — ${m.especialidade}</option>`;
            });
        });
}

function abrirModalNovo() {
    document.getElementById("modal-titulo").textContent = "Nova Ordem de Serviço";
    document.getElementById("modal-id").value   = "";
    document.getElementById("m-status").value   = "";
    document.getElementById("m-veiculo").value  = "";
    document.getElementById("m-mecanico").value = "";
    document.getElementById("m-entrega").value  = "";
    document.getElementById("m-garantia").value = "";
    document.getElementById("msg-modal").style.display = "none";
    document.getElementById("modal-os").classList.add("open");
}

function abrirModalEditar(id, status, id_veiculo, id_mecanico, entrega, garantia) {
    document.getElementById("modal-titulo").textContent = "Editar Ordem de Serviço";
    document.getElementById("modal-id").value   = id;
    document.getElementById("m-status").value   = status;
    document.getElementById("m-veiculo").value  = id_veiculo;
    document.getElementById("m-mecanico").value = id_mecanico;
    document.getElementById("m-entrega").value  = entrega;
    document.getElementById("m-garantia").value = garantia;
    document.getElementById("msg-modal").style.display = "none";
    document.getElementById("modal-os").classList.add("open");
}

function fecharModal() {
    document.getElementById("modal-os").classList.remove("open");
}

document.getElementById("modal-os").addEventListener("click", function(e) {
    if (e.target === this) fecharModal();
});

function salvarOS() {
    const id        = document.getElementById("modal-id").value;
    const status    = document.getElementById("m-status").value;
    const veiculo   = document.getElementById("m-veiculo").value;
    const mecanico  = document.getElementById("m-mecanico").value;
    const entrega   = document.getElementById("m-entrega").value;
    const garantia  = document.getElementById("m-garantia").value;
    const edicao    = id !== "";

    if (!status || !veiculo || !mecanico) {
        mostrarMsg("msg-modal", "Preencha status, veículo e mecânico.", "err");
        return;
    }

    const corpo  = edicao
        ? { id: parseInt(id), status, id_veiculo: parseInt(veiculo), id_mecanico: parseInt(mecanico), data_entrega_prevista: entrega, garantia_meses: parseInt(garantia) || 0 }
        : { status, id_veiculo: parseInt(veiculo), id_mecanico: parseInt(mecanico), data_entrega_prevista: entrega, garantia_meses: parseInt(garantia) || 0 };
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
            carregarOS();
        } else {
            mostrarMsg("msg-modal", data.erro || "Erro ao salvar.", "err");
        }
    })
    .catch(() => mostrarMsg("msg-modal", "Erro de conexão.", "err"))
    .finally(() => { btn.disabled = false; });
}

function excluirOS(id) {
    if (!confirm("Excluir esta OS?\nTodos os itens vinculados (peças e serviços) também serão removidos.")) return;

    fetch(CONTROLLER, {
        method: "DELETE",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ id })
    })
    .then(r => r.json())
    .then(data => {
        if (data.sucesso) {
            mostrarMsg("msg-lista", "OS excluída com sucesso.", "ok");
            carregarOS();
        } else {
            mostrarMsg("msg-lista", data.erro || "Erro ao excluir.", "err");
        }
    })
    .catch(() => mostrarMsg("msg-lista", "Erro de conexão.", "err"));
}

// ── Itens da OS ──────────────────────────────────────────────────────────────
let osAtiva = null;
let catalogoPecas    = [];
let catalogoServicos = [];

function abrirModalItens(id_os) {
    osAtiva = id_os;
    document.getElementById("itens-os-id").textContent = "#" + id_os;
    document.getElementById("msg-itens").style.display = "none";
    document.getElementById("modal-itens").classList.add("open");
    carregarItensPecasServicos();
    carregarCatalogos();
}

function fecharModalItens() {
    document.getElementById("modal-itens").classList.remove("open");
    osAtiva = null;
    carregarOS();
}

document.getElementById("modal-itens").addEventListener("click", function(e) {
    if (e.target === this) fecharModalItens();
});

function msgItens(texto, tipo) {
    const el = document.getElementById("msg-itens");
    el.textContent = texto;
    el.className = "msg " + tipo;
    el.style.display = "block";
    setTimeout(() => { el.style.display = "none"; }, 3000);
}

function formatarR(v) {
    return parseFloat(v).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
}

function carregarItensPecasServicos() {
    fetch(CONTROLLER + "?itens=1&id_os=" + osAtiva)
        .then(r => r.json())
        .then(data => {
            // Peças
            const tbP = document.getElementById("itens-pecas-lista");
            if (!data.pecas || data.pecas.length === 0) {
                tbP.innerHTML = '<tr><td colspan="4" style="text-align:center;color:#888;padding:8px">Nenhuma peça.</td></tr>';
            } else {
                tbP.innerHTML = data.pecas.map(p => `
                    <tr>
                        <td style="padding:6px 8px;border-bottom:1px solid #eee">${p.nome}</td>
                        <td style="padding:6px 8px;border-bottom:1px solid #eee">${p.quantidade}</td>
                        <td style="padding:6px 8px;border-bottom:1px solid #eee">${formatarR(p.preco_venda)}</td>
                        <td style="padding:6px 8px;border-bottom:1px solid #eee">
                            ${perfil === 'administrador' || perfil === 'gerencia' ? `<button class="btn-delete" style="padding:3px 10px;font-size:0.8rem;" onclick="removerPeca(${p.ID_peca})">✕</button>` : ''}
                        </td>
                    </tr>`).join('');
            }
            // Serviços
            const tbS = document.getElementById("itens-servicos-lista");
            if (!data.servicos || data.servicos.length === 0) {
                tbS.innerHTML = '<tr><td colspan="4" style="text-align:center;color:#888;padding:8px">Nenhum serviço.</td></tr>';
            } else {
                tbS.innerHTML = data.servicos.map(s => `
                    <tr>
                        <td style="padding:6px 8px;border-bottom:1px solid #eee">${s.descricao}</td>
                        <td style="padding:6px 8px;border-bottom:1px solid #eee">${formatarR(s.valor_cobrado)}</td>
                        <td style="padding:6px 8px;border-bottom:1px solid #eee;max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="${s.diagnostico_tecnico}">${s.diagnostico_tecnico || '—'}</td>
                        <td style="padding:6px 8px;border-bottom:1px solid #eee">
                            ${perfil === 'administrador' || perfil === 'gerencia' ? `<button class="btn-delete" style="padding:3px 10px;font-size:0.8rem;" onclick="removerServico(${s.ID_servico_ref})">✕</button>` : ''}
                        </td>
                    </tr>`).join('');
            }
        })
        .catch(() => msgItens("Erro ao carregar itens.", "err"));
}

function carregarCatalogos() {
    if (catalogoPecas.length === 0) {
        fetch("../controllers/pecacontroller.php")
            .then(r => r.json())
            .then(data => {
                catalogoPecas = data;
                const sel = document.getElementById("add-peca-id");
                sel.innerHTML = '<option value="">Selecione...</option>';
                data.forEach(p => sel.innerHTML += `<option value="${p.ID_peca}">${p.nome}</option>`);
            });
    }
    if (catalogoServicos.length === 0) {
        fetch("../controllers/ServicoController.php")
            .then(r => r.json())
            .then(data => {
                catalogoServicos = data;
                const sel = document.getElementById("add-serv-id");
                sel.innerHTML = '<option value="">Selecione...</option>';
                data.forEach(s => sel.innerHTML += `<option value="${s.ID_servico_ref}">${s.descricao}</option>`);
            });
    }
}

function adicionarPeca() {
    const id_peca    = parseInt(document.getElementById("add-peca-id").value);
    const quantidade = parseInt(document.getElementById("add-peca-qtd").value);
    const preco      = parseFloat(document.getElementById("add-peca-preco").value);
    if (!id_peca || quantidade <= 0 || isNaN(preco)) {
        msgItens("Preencha peça, quantidade e preço.", "err"); return;
    }
    fetch(CONTROLLER, {
        method: "PATCH",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ id_os: osAtiva, tipo: "peca", acao: "adicionar", id_peca, quantidade, preco_venda: preco })
    })
    .then(r => r.json())
    .then(data => {
        if (data.sucesso) { msgItens("Peça adicionada.", "ok"); carregarItensPecasServicos(); }
        else msgItens(data.erro || "Erro.", "err");
    })
    .catch(() => msgItens("Erro de conexão.", "err"));
}

function removerPeca(id_peca) {
    if (!confirm("Remover esta peça da OS?")) return;
    fetch(CONTROLLER, {
        method: "PATCH",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ id_os: osAtiva, tipo: "peca", acao: "remover", id_peca })
    })
    .then(r => r.json())
    .then(data => {
        if (data.sucesso) carregarItensPecasServicos();
        else msgItens(data.erro || "Erro.", "err");
    });
}

function adicionarServico() {
    const id_servico         = parseInt(document.getElementById("add-serv-id").value);
    const valor_cobrado      = parseFloat(document.getElementById("add-serv-valor").value);
    const diagnostico_tecnico = document.getElementById("add-serv-diag").value.trim();
    if (!id_servico || isNaN(valor_cobrado)) {
        msgItens("Preencha serviço e valor.", "err"); return;
    }
    fetch(CONTROLLER, {
        method: "PATCH",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ id_os: osAtiva, tipo: "servico", acao: "adicionar", id_servico, valor_cobrado, diagnostico_tecnico })
    })
    .then(r => r.json())
    .then(data => {
        if (data.sucesso) { msgItens("Serviço adicionado.", "ok"); carregarItensPecasServicos(); }
        else msgItens(data.erro || "Erro.", "err");
    })
    .catch(() => msgItens("Erro de conexão.", "err"));
}

function removerServico(id_servico) {
    if (!confirm("Remover este serviço da OS?")) return;
    fetch(CONTROLLER, {
        method: "PATCH",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ id_os: osAtiva, tipo: "servico", acao: "remover", id_servico })
    })
    .then(r => r.json())
    .then(data => {
        if (data.sucesso) carregarItensPecasServicos();
        else msgItens(data.erro || "Erro.", "err");
    });
}

function fazerLogout() {
    if (!confirm("Deseja sair do sistema?")) return;
    fetch("../controllers/logoutcontroller.php", { method: "POST" })
        .then(r => r.json())
        .then(data => { if (data.sucesso) window.location.href = "login.php"; })
        .catch(() => { window.location.href = "login.php"; });
}

carregarSelects();
carregarOS();
</script>
</body>
</html>
