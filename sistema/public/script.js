function trocarAba(aba) {
  document.querySelectorAll('.panel-content').forEach(p => p.classList.remove('active'));
  document.getElementById('painel-' + aba).classList.add('active');
}

function msg(id, texto) {
  const el = document.getElementById(id);
  el.textContent = texto;
  el.style.display = 'block';
  setTimeout(() => { el.style.display = 'none'; }, 4000);
}

function fazerLogin() {
  const documento = document.getElementById('login-doc').value.trim();
  const senha     = document.getElementById('login-senha').value.trim();
  const btn       = document.getElementById('btn-login');

  if (!documento || !senha) {
    msg('msg-login', 'Preencha documento e senha.');
    return;
  }

  btn.disabled = true;
  btn.textContent = 'Verificando...';

  fetch('../app/controllers/logincontroller.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ documento, senha })
  })
  .then(res => res.json())
  .then(data => {
    if (data.sucesso) {
      msg('msg-login', 'Login realizado! Redirecionando...');
      setTimeout(() => window.location.href = '../app/views/dashboard.php', 1200);
    } else {
      msg('msg-login', data.erro || 'Credenciais inválidas.');
    }
  })
  .catch(() => msg('msg-login', 'Erro de conexão com o servidor.'))
  .finally(() => {
    btn.disabled = false;
    btn.textContent = 'Entrar no Sistema';
  });
}

function fazerCadastro() {
  const admDoc   = document.getElementById('adm-doc').value.trim();
  const admSenha = document.getElementById('adm-senha').value.trim();
  const nome     = document.getElementById('cad-nome').value.trim();
  const doc      = document.getElementById('cad-doc').value.trim();
  const perfil   = document.getElementById('cad-perfil').value;
  const senha    = document.getElementById('cad-senha').value.trim();
  const btn      = document.getElementById('btn-cadastrar');

  if (!admDoc || !admSenha) {
    msg('msg-cadastro', 'Informe as credenciais do administrador.');
    return;
  }
  if (!nome || !doc || !perfil || !senha) {
    msg('msg-cadastro', 'Preencha todos os dados do novo usuário.');
    return;
  }
  if (senha.length < 6) {
    msg('msg-cadastro', 'A senha deve ter pelo menos 6 caracteres.');
    return;
  }

  btn.disabled = true;
  btn.textContent = 'Cadastrando...';

  fetch('../app/controllers/logincontroller.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ documento: admDoc, senha: admSenha })
  })
  .then(res => res.json())
  .then(authData => {
    if (!authData.sucesso) {
      msg('msg-cadastro', 'Autenticação do administrador falhou: ' + (authData.erro || 'credenciais inválidas.'));
      btn.disabled = false;
      btn.textContent = 'Cadastrar Usuário';
      return;
    }

    return fetch('../app/views/cadastrar.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ nome, documento: doc, perfil, senha })
    });
  })
  .then(res => res ? res.json() : null)
  .then(data => {
    if (!data) return;

    if (data.sucesso) {
      msg('msg-cadastro', data.mensagem);
      ['cad-nome', 'cad-doc', 'cad-senha'].forEach(id => document.getElementById(id).value = '');
      document.getElementById('cad-perfil').value = '';
    } else {
      msg('msg-cadastro', data.erro || 'Erro ao cadastrar.');
    }
  })
  .catch(() => msg('msg-cadastro', 'Erro de conexão com o servidor.'))
  .finally(() => {
    btn.disabled = false;
    btn.textContent = 'Cadastrar Usuário';
  });
}

document.addEventListener('keydown', e => {
  if (e.key !== 'Enter') return;
  if (document.getElementById('painel-login').classList.contains('active')) {
    fazerLogin();
  } else {
    fazerCadastro();
  }
});
