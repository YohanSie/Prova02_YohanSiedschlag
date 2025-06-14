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

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        .fade-in {
            animation: fadeIn 0.6s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .custom-card {
            border-radius: 1rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            border: none;
        }

        .search-box {
            transition: all 0.3s ease;
        }

        .search-box:focus {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(13, 110, 253, 0.15);
        }

        .table-hover tbody tr:hover {
            background-color: rgba(13, 110, 253, 0.05) !important;
            transition: all 0.3s ease;
        }

        .badge-perfil {
            font-size: 0.85em;
            padding: 0.5em 0.8em;
        }

        .btn-action {
            transition: all 0.3s ease;
        }

        .btn-action:hover {
            transform: translateY(-2px);
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
            <i class="bi bi-search text-primary me-2"></i>
            Buscar Fornecedor
        </h2>
        <button class="btn btn-outline-secondary" onclick="window.location.href='principal.php'">
            <i class="bi bi-arrow-left me-2"></i>Voltar
        </button>
    </div>

    <!-- Card de busca -->
    <div class="card custom-card mb-4">
        <div class="card-body">
            <form action="./buscar_fornecedor.php" method="post" class="d-flex gap-3">
                <div class="flex-grow-1">
                    <div class="input-group">
                            <span class="input-group-text bg-white">
                                <i class="bi bi-search text-primary"></i>
                            </span>
                        <input type="text" class="form-control search-box" name="busca" id="busca" placeholder="Digite o ID ou nome do fornecedor">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-search me-2"></i>Pesquisar
                </button>
            </form>
        </div>
    </div>

    <!-- Tabela de resultados -->
    <?php if (!empty($fornecedores)): ?>
        <div class="card custom-card">
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
                                <i class="bi bi-envelope text-secondary me-2"></i>
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
                                       onclick="return confirm('Tem certeza que deseja excluir este usuário?')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php else: ?>
        <div class="text-center py-5 text-muted">
            <i class="bi bi-search" style="font-size: 3rem;"></i>
            <p class="h5 mt-3">Nenhum fornecedor encontrado</p>
        </div>
    <?php endif; ?>
</div>


<!-- Copyright Logo -->
<div class="copyright-logo">
    <span class="copyright-text">
        <i class="bi bi-c-circle"></i> 2024 Yohan Siedschlag
    </span>
</div>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>