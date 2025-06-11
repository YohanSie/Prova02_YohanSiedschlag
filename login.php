<?php

session_start();

require_once 'conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST['email'];
    $senha = $_POST['senha'];

    $sql = "SELECT * FROM usuario WHERE email = :email";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && password_verify($senha, $usuario['senha'])) {

        // Login bem sucedido define variaveis de sessão
        $_SESSION['usuario'] = $usuario['nome'];
        $_SESSION['perfil'] = $usuario['id_perfil'];
        $_SESSION['id_usuario'] = $usuario['id_usuario'];

        // Verifica se a senha é temporária
        if ($usuario['senha_temporaria']) {
            // Redireciona para a troca de senha
            header("Location: alterar_senha.php");
            exit();
        } else {
            // Redireciona para a página principal
            header("Location: principal.php");
            exit();
        }
    } else {
        // Login inválido
        echo "<script>alert('E-mail ou senha incorretos);window.location.href='login.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="./css/styles.css">
</head>

<body>
    <h2>Login</h2>

    <form action="login.php" method="post">
        <!-- EMAIL -->
        <label for="email">E-mail:</label>
        <input type="email" name="email" id="email" required>
        <!-- SENHA -->
        <label for="senha">Senha:</label>
        <input type="password" name="senha" id="senha" required>

        <button type="submit">Entrar</button>
    </form>
    
    echo "";

    <p><a href="recuperar_senha.php">Esqueci a Senha</a></p>

</body>

</html>