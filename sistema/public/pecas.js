const CONTROLLER = "../app/controllers/pecacontroller.php";
const perfil = localStorage.getItem("perfil") || "";
const nome   = localStorage.getItem("nome")   || "";

if (!localStorage.getItem("id")) window.location.href = "login.html";
if (perfil !== "administrador" && perfil !== "gerencia") window.location.href = "index.html";

document.getElementById("info-usuario").innerHTML =
    `<strong>${nome}</strong> &nbsp;|&nbsp; <strong>${perfil}</strong>`;

if (perfil === "administrador") {
    document.getElementById("btn-nova-peca").style.display = "inline-block";
    document.getElementById("col-acoes").style.display = "table-cell";
}

function mostrarMsg(id, texto, tipo) {
    const el = document.getElementById(id);
    el.textContent = texto;
    el.className = "msg " + tipo;
    el.style.display = "block";
    setTimeout(() => { el.style.display = "none"; }, 4000);
}

function formatarPreco(valor) {
    return parseFloat(valor).toLocaleString("pt-BR", { style: "currency", currency: "BRL" });
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
                const estoqueCls   = p.nivel_estoque <= 5 ? "estoque-baixo" : "estoque-ok";
                const estoqueLabel = p.nivel_estoque <= 5 ? p.nivel_estoque + " ⚠" : p.nivel_estoque;
                const acoesHtml    = perfil === "administrador" ? `
                    <td style="display:flex;gap:6px;">
                        <button class="btn-edit" onclick="abrirModalEditar(${p.ID_peca},'${p.nome.replace(/'/g,"\\'")}','${p.descricao.replace(/'/g,"\\'").replace(/\n/g," ")}',${p.preco_unitario},${p.nivel_estoque})">Editar</button>
                        <button class="btn-delete" onclick="excluirPeca(${p.ID_peca},'${p.nome.replace(/'/g,"\\'")}')">Excluir</button>
                    </td>` : "";
                return `
                <tr>
                    <td>${p.ID_peca}</td>
                    <td>${p.nome}</td>
                    <td style="max-width:240px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis" title="${p.descricao}">${p.descricao}</td>
                    <td>${formatarPreco(p.preco_unitario)}</td>
                    <td class="${estoqueCls}">${estoqueLabel}</td>
                    ${acoesHtml}
                </tr>`;
            }).join("");
        })
        .catch(() => mostrarMsg("msg-lista", "Erro ao carregar peças.", "err"));
}

function abrirModalNovo() {
    document.getElementById("modal-titulo").textContent    = "Nova Peça";
    document.getElementById("modal-id").value             = "";
    document.getElementById("m-nome").value               = "";
    document.getElementById("m-descricao").value          = "";
    document.getElementById("m-preco").value              = "";
    document.getElementById("m-estoque").value            = "";
    document.getElementById("msg-modal").style.display    = "none";
    document.getElementById("modal-peca").classList.add("open");
}

function abrirModalEditar(id, nome, descricao, preco, estoque) {
    document.getElementById("modal-titulo").textContent    = "Editar Peça";
    document.getElementById("modal-id").value             = id;
    document.getElementById("m-nome").value               = nome;
    document.getElementById("m-descricao").value          = descricao;
    document.getElementById("m-preco").value              = preco;
    document.getElementById("m-estoque").value            = estoque;
    document.getElementById("msg-modal").style.display    = "none";
    document.getElementById("modal-peca").classList.add("open");
}

function fecharModal() {
    document.getElementById("modal-peca").classList.remove("open");
}

document.getElementById("modal-peca").addEventListener("click", function(e) {
    if (e.target === this) fecharModal();
});

function salvarPeca() {
    const id        = document.getElementById("modal-id").value;
    const nome      = document.getElementById("m-nome").value.trim();
    const descricao = document.getElementById("m-descricao").value.trim();
    const preco     = parseFloat(document.getElementById("m-preco").value);
    const estoque   = parseInt(document.getElementById("m-estoque").value);
    const edicao    = id !== "";

    if (!nome || !descricao || isNaN(preco) || isNaN(estoque)) {
        mostrarMsg("msg-modal", "Preencha todos os campos corretamente.", "err");
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
    if (!confirm(`Excluir a peça "${nome}"?`)) return;
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
    fetch("../app/controllers/logoutcontroller.php", { method: "POST" })
        .then(r => r.json())
        .then(() => { localStorage.clear(); window.location.href = "login.html"; })
        .catch(() => { localStorage.clear(); window.location.href = "login.html"; });
}

carregarPecas();