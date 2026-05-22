<!DOCTYPE html>
<html>
<head>
    <title>Sistema - Login</title>
</head>
<body>

<h2>Acesso ao Sistema</h2>
    <form id="formLogin">
        <input type="text" id="documento" placeholder="Documento (CPF)">
        <input type="password" id="senha" placeholder="Senha">
        <button type="button" onclick="fazerLogin()">Entrar</button>
    </form>

<script>
    function fazerLogin() {
        const documentoInput = document.getElementById('documento').value;
        const senhaInput = document.getElementById('senha').value;

        const formData = new FormData();
        formData.append('documento', documentoInput);
        formData.append('senha', senhaInput);

        fetch('../controllers/logincontroller.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.sucesso) {
                window.location.href = 'dashboard.php';
            } else {
                alert(data.erro || "Erro de login.");
            }
        })
        .catch(err => {
            console.error("Erro na requisição:", err);
            alert("Não foi possível conectar ao servidor.");
        });
    }
</script>

</body>
</html>