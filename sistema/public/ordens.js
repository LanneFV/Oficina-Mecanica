const CONTROLLER = "../app/controllers/ordemcontroller.php";
const perfil = localStorage.getItem("perfil") || "";
const nome   = localStorage.getItem("nome")   || "";

if (!localStorage.getItem("id")) window.location.href = "login.html";

document.getElementById("info-usuario").innerHTML =
    `<strong>${nome}</strong> &nbsp;|&nbsp; <strong>${perfil}</strong>`;

if (perfil === "administrador") {
    document.getElementById("col-acoes").style.display = "table-cell";
}

if (perfil === "administrador" || perfil === "gerencia") {
    document.getElementById("btn-nova-os").style.display = "inline-block";
}

function mostrarMsg(id, texto, tipo) {
    const el = document.getElementById(id);
    el.textContent = texto;
    el.className = "msg " + tipo;
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
    fetch(CONTROLLER, { credentials: 'include' })
        .then(r => r.json())
        .then(data => {
            const tbody = document.getElementById("corpo-tabela");
            if (!Array.isArray(data) || data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="9" style="text-align:center;color:#888;padding:20px">Nenhuma OS cadastrada.</td></tr>';
                return;
            }
            tbody.innerHTML = data.map(os => {
                const acoesHtml = perfil === "administrador" ? `
                    <td style="display:flex;gap:6px;">
                        <button class="btn-edit" onclick="abrirModalEditar(${os.ID_os},'${os.status}',${os.id_veiculo},${os.id_mecanico},'${os.data_entrega_prevista ?? ""}',${os.garantia_meses ?? 0})">Editar</button>
                        <button class="btn-delete" onclick="excluirOS(${os.ID_os})">Excluir</button>
                    </td>` : "";
                return `
                <tr>
                    <td>${os.ID_os}</td>
                    <td>${badgeStatus(os.status)}</td>
                    <td>${os.placa}</td>
                    <td>${os.cliente}</td>
                    <td>${os.mecanico}</td>
                    <td>${os.data_abertura ? os.data_abertura.substring(0,10) : "-"}</td>
                    <td>${os.data_entrega_prevista ?? "-"}</td>
                    <td>${os.garantia_meses ? os.garantia_meses + " mes(es)" : "-"}</td>
                    ${acoesHtml}
                </tr>`;
            }).join("");
        })
        .catch(() => mostrarMsg("msg-lista", "Erro ao carregar ordens.", "err"));
}

function carregarSelects() {
    fetch(CONTROLLER + '?selects=1', { credentials: 'include' })
        .then(r => r.json())
        .then(data => {
            const sv = document.getElementById("m-veiculo");
            const sm = document.getElementById("m-mecanico");
            sv.innerHTML = '<option value="">Selecione...</option>';
            sm.innerHTML = '<option value="">Selecione...</option>';
            (data.veiculos ?? []).forEach(v => {
                sv.innerHTML += `<option value="${v.ID_veiculo}">${v.placa} — ${v.cliente} (${v.modelo})</option>`;
            });
            (data.mecanicos ?? []).forEach(m => {
                sm.innerHTML += `<option value="${m.ID_mecanico}">${m.nome} — ${m.especialidade}</option>`;
            });
        });
}

function abrirModalNovo() {
    document.getElementById("modal-titulo").textContent  = "Nova Ordem de Serviço";
    document.getElementById("modal-id").value            = "";
    document.getElementById("m-status").value            = "";
    document.getElementById("m-veiculo").value           = "";
    document.getElementById("m-mecanico").value          = "";
    document.getElementById("m-entrega").value           = "";
    document.getElementById("m-garantia").value          = "";
    document.getElementById("msg-modal").style.display   = "none";
    document.getElementById("modal-os").classList.add("open");
}

function abrirModalEditar(id, status, id_veiculo, id_mecanico, entrega, garantia) {
    document.getElementById("modal-titulo").textContent  = "Editar Ordem de Serviço";
    document.getElementById("modal-id").value            = id;
    document.getElementById("m-status").value            = status;
    document.getElementById("m-veiculo").value           = id_veiculo;
    document.getElementById("m-mecanico").value          = id_mecanico;
    document.getElementById("m-entrega").value           = entrega;
    document.getElementById("m-garantia").value          = garantia;
    document.getElementById("msg-modal").style.display   = "none";
    document.getElementById("modal-os").classList.add("open");
}

function fecharModal() {
    document.getElementById("modal-os").classList.remove("open");
}

document.getElementById("modal-os").addEventListener("click", function(e) {
    if (e.target === this) fecharModal();
});

function salvarOS() {
    const id       = document.getElementById("modal-id").value;
    const status   = document.getElementById("m-status").value;
    const veiculo  = document.getElementById("m-veiculo").value;
    const mecanico = document.getElementById("m-mecanico").value;
    const entrega  = document.getElementById("m-entrega").value;
    const garantia = document.getElementById("m-garantia").value;
    const edicao   = id !== "";

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
        credentials: 'include',
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
    if (!confirm("Excluir esta OS?")) return;
    fetch(CONTROLLER, {
        method: 'DELETE',
        credentials: 'include',
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

function fazerLogout() {
    if (!confirm("Deseja sair do sistema?")) return;
    fetch("../app/controllers/logoutcontroller.php", { method: "POST" })
        .then(r => r.json())
        .then(() => { localStorage.clear(); window.location.href = "login.html"; })
        .catch(() => { localStorage.clear(); window.location.href = "login.html"; });
}

carregarSelects();
carregarOS();