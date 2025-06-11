<?php
session_start();
require 'conexao.php';

// Verifica se o usuário tem permissão de ADM
if ($_SESSION['perfil'] != 1) {
    echo "<script>alert('Acesso negado!'); window.location.href='principal.php';</script>";
    exit();
}

// Inicializa variáveis
$usuario = null;

// Se o formulário for enviado, busca o usuário pelo ID ou nome
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['busca_usuario'])) {
        $busca = trim($_POST['busca_usuario']);

        // Verifica se a busca é um número (ID) ou um nome
        if (is_numeric($busca)) {
            $sql = "SELECT * FROM usuario WHERE id_usuario = :busca";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':busca', $busca, PDO::PARAM_INT);
        } else {
            $sql = "SELECT * FROM usuario WHERE nome LIKE :busca_nome";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':busca_nome', "%$busca%", PDO::PARAM_STR);
        }

        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        // Se o usuário não for encontrado, exibe um alerta
        if (!$usuario) {
            echo "<script>alert('Usuário não encontrado!');</script>";
        }
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_usuario'])) {
        $id_usuario = $_POST['id_usuario'];
        $nome = trim($_POST['nome']);
        $email = trim($_POST['email']);
        $id_perfil = $_POST['id_perfil'];

        // Atualiza senha somente se informada
        if (!empty($_POST['nova_senha'])) {
            $nova_senha = password_hash($_POST['nova_senha'], PASSWORD_DEFAULT);
            $sql = "UPDATE usuario SET nome = :nome, email = :email, id_perfil = :id_perfil, senha = :senha WHERE id_usuario = :id_usuario";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':senha', $nova_senha);
        } else {
            $sql = "UPDATE usuario SET nome = :nome, email = :email, id_perfil = :id_perfil WHERE id_usuario = :id_usuario";
            $stmt = $pdo->prepare($sql);
        }

        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':id_perfil', $id_perfil);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo "<script>alert('Usuário alterado com sucesso!'); window.location.href='alterar_usuario.php';</script>";
            exit();
        } else {
            echo "<script>alert('Erro ao alterar usuário!');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Alterar Usuário</title>
    <link rel="stylesheet" href="./css/styles.css">
</head>

<body>
    <h2>Alterar Usuário</h2>

    <form action="alterar_usuario.php" method="POST">
        <label for="busca_usuario">Digite o ID ou Nome do usuário:</label>
        <input type="text" id="busca_usuario" name="busca_usuario" required>
        <button type="submit">Buscar</button>
    </form>

    <?php if ($usuario): ?>
        <form action="alterar_usuario.php" method="POST">
            <input type="hidden" name="id_usuario" value="<?= htmlspecialchars($usuario['id_usuario']) ?>">

            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($usuario['nome']) ?>" required>

            <label for="email">E-mail:</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($usuario['email']) ?>" required>

            <label for="id_perfil">Perfil:</label>
            <select id="id_perfil" name="id_perfil">
                <option value="1" <?= $usuario['id_perfil'] == 1 ? 'selected' : '' ?>>Administrador</option>
                <option value="2" <?= $usuario['id_perfil'] == 2 ? 'selected' : '' ?>>Secretaria</option>
                <option value="3" <?= $usuario['id_perfil'] == 3 ? 'selected' : '' ?>>Almoxarife</option>
                <option value="4" <?= $usuario['id_perfil'] == 4 ? 'selected' : '' ?>>Cliente</option>
            </select>

            <?php if ($_SESSION['perfil'] == 1): ?>
                <label for="nova_senha">Nova Senha:</label>
                <input type="password" id="nova_senha" name="nova_senha">
            <?php endif; ?>

            <button type="submit">Alterar</button>
            <button type="reset">Cancelar</button>
        </form>
    <?php endif; ?>

    <button class="btn-voltar" onclick="window.location.href='principal.php'">Voltar</button>
</body>

</html>