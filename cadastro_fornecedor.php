<?php

session_start();
require_once 'conexao.php';

//  Verifica se o usuário tem permissão
// suponde que o perfil 1 seja o administrador
if ($_SESSION['perfil'] != 1) {
    echo 'acesso negado';
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = trim($_POST['nome']);
    $endereco = $_POST['endereco'];
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $telefone = $_POST['telefone'];
    $nome_contato = trim($_POST['contato']);

    $sql = "INSERT INTO fornecedor(nome_fornecedor, endereco, email, telefone, contato)
    VALUES (:nome_fornecedor, :endereco, :email, :telefone, :contato)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nome_fornecedor', $nome);
    $stmt->bindParam(':endereco', $endereco);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':telefone', $telefone);
    $stmt->bindParam(':contato', $nome_contato);

    if ($stmt->execute()) {
        echo "<script>alert('Fornecedor cadastrado com sucesso!');</script>";
    } else {
        echo "<script>alert('Erro ao cadastrar Usuário!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Fornecedor</title>

    <link rel="stylesheet" href="./css/styles.css">
</head>

<body>
    <h2>Cadastrar Fornecedor</h2>

    <form action="./cadastro_fornecedor.php" method="post">
        <label for="nome">Nome:</label>
        <input type="text" id="nome" name="nome" required>

        <label for="endereco">Endereço:</label>
        <input type="text" id="endereco" name="endereco" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <label for="telefone">Telefone:</label>
        <input type="tel" id="telefone" name="telefone" required>

        <label for="contato">Nome Contato:</label>
        <input type="text" id="contato" name="contato" required>

        <button type="submit">Cadastrar</button>
        <button type="reset">Cancelar</button>
    </form>

    
    <button class="btn-voltar" onclick="window.location.href='principal.php'">Voltar</button>
</body>

</html>