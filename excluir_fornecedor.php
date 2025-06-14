<?php
session_start();
require 'conexao.php';

// Verifica se o fornecedor tem permissão de ADM
if ($_SESSION['perfil'] != 1) {
    echo "<script>alert('Acesso negado!'); window.location.href='principal.php';</script>";
    exit();
}

// Inicializa variável para armazenar fornecedors
$fornecedores = [];

// Busca todos os fornecedors cadastrados em ordem alfabética
$sql = "SELECT * FROM fornecedor ORDER BY nome_fornecedor";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$fornecedores = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Se um ID for passado via GET, exclui o fornecedor
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_fornecedor = $_GET['id'];

    // Exclui o fornecedor do banco de dados
    $sql = "DELETE FROM fornecedor WHERE id_fornecedor = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id_fornecedor, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo "<script>alert('fornecedor excluído com sucesso!'); window.location.href='excluir_fornecedor.php';</script>";
    } else {
        echo "<script>alert('Erro ao excluir fornecedor!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excluir Fornecedor</title>

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

        .copyright-logo {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            z-index: 1000;
            backdrop-filter: blur(5px);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            transition: opacity 0.3s ease;
        }

        .copyright-logo:hover {
            opacity: 0.9;
        }

        .copyright-text {
            display: flex;
            align-items: center;
            gap: 5px;
        }
    </style>
</head>
<body class="bg-light">
<div class="container py-5 fade-in">
    <!-- Cabeçalho -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="display-6">
            <i class="bi bi-person-x-fill text-danger me-2"></i>
            Excluir Fornecedor
        </h2>
        <button class="btn btn-outline-secondary" onclick="window.location.href='principal.php'">
            <i class="bi bi-arrow-left me-2"></i>Voltar
        </button>
    </div>

    <!-- Card principal -->
    <div class="card custom-card">
        <div class="card-body p-0">
            <?php if (!empty($fornecedores)): ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-dark">
                        <tr>
                            <th scope="col" class="px-4">ID</th>
                            <th scope="col">Nome Fornecedor</th>
                            <th scope="col">Endereço</th>
                            <th scope="col">Telefone</th>
                            <th scope="col">Email</th>
                            <th scope="col">Contato</th>
                            <th scope="col" class="text-center">Ações</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($fornecedores as $fornecedor): ?>
                            <tr>
                                <td class="px-4"><?= htmlspecialchars($fornecedor['id_fornecedor']) ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-person-circle text-secondary me-2"></i>
                                        <?= htmlspecialchars($fornecedor['nome_fornecedor']) ?>
                                    </div>
                                </td>
                                <td>
                                    <i class="bi bi-key text-secondary me-2"></i>
                                    <?= htmlspecialchars($fornecedor['endereco']) ?>
                                </td>
                                <td>
                                    <i class="bi bi-telephone text-secondary me-2"></i>
                                    <?= htmlspecialchars($fornecedor['telefone']) ?>
                                </td>
                                <td>
                                    <i class="bi bi-envelope text-secondary me-2"></i>
                                    <?= htmlspecialchars($fornecedor['email']) ?>
                                </td>
                                <td>
                                    <i class="bi bi-person-circle text-secondary me-2"></i>
                                    <?= htmlspecialchars($fornecedor['contato']) ?>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="./alterar_fornecedor.php?id=<?= htmlspecialchars($fornecedor['id_fornecedor']) ?>"
                                           class="btn btn-sm btn-outline-primary btn-action">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <a href="./excluir_fornecedor.php?id=<?= htmlspecialchars($fornecedor['id_fornecedor']) ?>"
                                           class="btn btn-sm btn-outline-danger btn-action"
                                           onclick="return confirm('Tem certeza que deseja excluir este fornecedor?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-exclamation-circle text-secondary" style="font-size: 3rem;"></i>
                    <p class="h5 mt-3 text-secondary">Nenhum fornecedor encontrado.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Copyright Logo -->
<div class="copyright-logo">
    <span class="copyright-text">
        <i class="bi bi-c-circle"></i> 2024 Yohan Siedschlag
    </span>
</div>

<!-- Bootstrap 5 JS Bundle com Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>