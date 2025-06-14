<?php
session_start();
require 'conexao.php';

// Verifica se o usuário tem permissão de ADM
if ($_SESSION['perfil'] != 1) {
    echo "<script>alert('Acesso negado!'); window.location.href='principal.php';</script>";
    exit();
}

// Inicializa variável para armazenar usuários
$usuarios = [];

// Busca todos os usuários cadastrados em ordem alfabética
$sql = "SELECT * FROM usuario ORDER BY nome ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Se um ID for passado via GET, exclui o usuário
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_usuario = $_GET['id'];

    // Exclui o usuário do banco de dados
    $sql = "DELETE FROM usuario WHERE id_usuario = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id_usuario, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo "<script>alert('Usuário excluído com sucesso!'); window.location.href='excluir_usuario.php';</script>";
    } else {
        echo "<script>alert('Erro ao excluir usuário!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excluir Usuário</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">

    <!-- CSS Personalizado -->
    <style>
        /* Animação de fade-in para a página */
        .fade-in {
            animation: fadeIn 0.6s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Estilização da tabela */
        .table-hover tbody tr:hover {
            background-color: rgba(0,123,255,0.05);
            transition: all 0.3s ease;
        }

        /* Botão de exclusão personalizado */
        .btn-delete {
            color: #dc3545;
            border: 1px solid #dc3545;
            padding: 0.375rem 0.75rem;
            border-radius: 0.25rem;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .btn-delete:hover {
            background-color: #dc3545;
            color: white;
        }

        /* Card personalizado */
        .custom-card {
            border-radius: 1rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        /* Badge de perfil */
        .badge-perfil {
            font-size: 0.85em;
            padding: 0.5em 0.8em;
        }
    </style>
</head>
<body class="bg-light">
<div class="container py-5 fade-in">
    <!-- Cabeçalho -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="display-6">
            <i class="bi bi-person-x-fill text-danger me-2"></i>
            Excluir Usuário
        </h2>
        <button class="btn btn-outline-secondary" onclick="window.location.href='principal.php'">
            <i class="bi bi-arrow-left me-2"></i>Voltar
        </button>
    </div>

    <!-- Card principal -->
    <div class="card custom-card">
        <div class="card-body p-0">
            <?php if (!empty($usuarios)): ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-dark">
                        <tr>
                            <th scope="col" class="px-4">ID</th>
                            <th scope="col">Nome</th>
                            <th scope="col">Email</th>
                            <th scope="col">Perfil</th>
                            <th scope="col" class="text-center">Ações</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($usuarios as $usuario): ?>
                            <tr>
                                <td class="px-4"><?= htmlspecialchars($usuario['id_usuario']) ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-person-circle text-secondary me-2"></i>
                                        <?= htmlspecialchars($usuario['nome']) ?>
                                    </div>
                                </td>
                                <td>
                                    <i class="bi bi-envelope text-secondary me-2"></i>
                                    <?= htmlspecialchars($usuario['email']) ?>
                                </td>
                                <td>
                                    <?php
                                    $perfil_class = match((int)$usuario['id_perfil']) {
                                        1 => 'bg-danger',
                                        2 => 'bg-primary',
                                        3 => 'bg-success',
                                        4 => 'bg-info',
                                        default => 'bg-secondary'
                                    };
                                    $perfil_nome = match((int)$usuario['id_perfil']) {
                                        1 => 'Administrador',
                                        2 => 'Secretaria',
                                        3 => 'Almoxarife',
                                        4 => 'Cliente',
                                        default => 'Desconhecido'
                                    };
                                    ?>
                                    <span class="badge <?= $perfil_class ?> badge-perfil">
                                            <?= $perfil_nome ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="excluir_usuario.php?id=<?= htmlspecialchars($usuario['id_usuario']) ?>"
                                       class="btn-delete"
                                       onclick="return confirm('Tem certeza que deseja excluir este usuário?')">
                                        <i class="bi bi-trash me-1"></i>
                                        Excluir
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-exclamation-circle text-secondary" style="font-size: 3rem;"></i>
                    <p class="h5 mt-3 text-secondary">Nenhum usuário encontrado.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Bootstrap 5 JS Bundle com Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>