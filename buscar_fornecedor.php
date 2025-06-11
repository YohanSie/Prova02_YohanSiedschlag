<?php

session_start();
require_once 'conexao.php';

//  Verifica se o usuário tem permissão
// suponde que o perfil 1 seja o administrador
if ($_SESSION['perfil'] != 1) {
    echo 'acesso negado';
}

if ($_SESSION['perfil'] != 1 && $_SESSION['perfil'] != 2) {
    echo "<script>alert('Acesso Negado');window.location.href='./principal.php'</script>";
}

$fornecedores = []; // Inicializa a variavel para evitar erros

// Se o form for enviado, busca o usuario pelo id ou nome
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['busca'])) {
    $busca = trim($_POST['busca']);

    // Verifica se a busca é um número (id) ou um nome
    if (is_numeric($busca)) {
        $sql = "SELECT * FROM fornecedor WHERE id_fornecedor = :busca ORDER BY nome_fornecedor ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':busca', $busca, PDO::PARAM_INT);
    } else {
        $sql = "SELECT * FROM fornecedor WHERE nome_fornecedor LIKE :busca_nome ORDER BY nome_fornecedor ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':busca_nome', "%$busca%", PDO::PARAM_STR);
    }
} else {
    $sql = "SELECT * FROM fornecedor ORDER BY nome_fornecedor ASC";
    $stmt = $pdo->prepare($sql);
}

$stmt->execute();
$fornecedores = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar Fornecedor</title>

    <link rel="stylesheet" href="./css/styles.css">

</head>

<body>
    <h2>Lista de Fornecedores</h2>

    <!-- Formulário para buscar usuários -->
    <form action="./buscar_fornecedor.php" method="post">
        <label for="busca">Digite o ID ou Nome (opcional):</label>
        <input type="text" name="busca" id="busca">

        <button type="submit">Pesquisar</button>
    </form>

    <?php if (!empty($fornecedores)): ?>
        <table border=1>
            <thead>
                <th>ID</th>
                <th>Nome Fornecedor</th>
                <th>Endereço</th>
                <th>Telefone</th>
                <th>Email</th>
                <th>Contato</th>
                <th>Ações</th>
            </thead>
            <?php foreach ($fornecedores as $fornecedor): ?>
                <tr>
                    <tbody>
                        <td><?= htmlspecialchars($fornecedor['id_fornecedor']) ?></td>
                        <td><?= htmlspecialchars($fornecedor['nome_fornecedor']) ?></td>
                        <td><?= htmlspecialchars($fornecedor['endereco']) ?></td>
                        <td><?= htmlspecialchars($fornecedor['telefone']) ?></td>
                        <td><?= htmlspecialchars($fornecedor['email']) ?></td>
                        <td><?= htmlspecialchars($fornecedor['contato']) ?></td>

                        <td>
                            <a href="./alterar_fornecedor.php?id=<?= htmlspecialchars($fornecedor['id_fornecedor']) ?>">Alterar</a>
                            <a href="./excluir_fornecedor.php?id=<?= htmlspecialchars($fornecedor['id_fornecedor']) ?>" onclick="return confirm('Tem certeza que deseja excluir este usuário?')">Excluir</a>
                        </td>
                    </tbody>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>Nenhum usuário encontrado!</p>
    <?php endif; ?>

    <button class="btn-voltar" onclick="window.location.href='principal.php'">Voltar</button>
</body>

</html>