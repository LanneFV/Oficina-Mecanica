<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Sistema - Painel</title>
</head>
<body>

    <h2>Painel Principal</h2>
    <p>Bem-vindo, <strong><?php echo htmlspecialchars($_SESSION['nome']); ?></strong>!</p>
    <p>Seu perfil de acesso é: <strong><?php echo htmlspecialchars($_SESSION['perfil']); ?></strong></p>

    <hr>

    <?php if ($_SESSION['perfil'] === 'administrador' || $_SESSION['perfil'] === 'gerencia'): ?>
        <h2>Lista de Usuários Cadastrados</h2>
        <ul id="lista"></ul>
        <hr>
    <?php endif; ?>

    <button type="button" onclick="fazerLogout()">Sair do Sistema</button>

<script>
    const perfilUsuario = "<?php echo $_SESSION['perfil']; ?>";

    function carregar() {
        fetch('../controllers/usuariocontroller.php')
            .then(res => res.json())
            .then(data => {
                let lista = document.getElementById('lista');
                if (!lista) return;
                
                lista.innerHTML = '';

                data.forEach(user => {
                    let li = document.createElement('li');
                    li.textContent = user.nome + ' - Doc: ' + user.documento + ' [' + user.perfil + ']';
                    lista.appendChild(li);
                });
            })
            .catch(err => console.error("Erro ao carregar usuários:", err));
    }

    function fazerLogout() {
        if (confirm("Tem certeza que deseja sair?")) {
            fetch('../controllers/logoutcontroller.php', { method: 'POST' })
            .then(res => res.json())
            .then(data => {
                if (data.sucesso) {
                    window.location.href = 'login.php';
                }
            });
        }
    }

    if (perfilUsuario === 'administrador' || perfilUsuario === 'gerencia') {
        carregar();
    }
</script>

</body>
</html>