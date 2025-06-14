<?php
session_start();
require 'conexao.php';

// Verifica se o usuário tem permissão de ADM
if ($_SESSION['perfil'] != 1) {
    echo "<script>alert('Acesso negado!'); window.location.href='principal.php';</script>";
    exit();
}

// Inicializa variáveis
$fornecedor = null;

// Busca fornecedor pelo ID passado via GET
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_fornecedor = $_GET['id'];
    $sql = "SELECT * FROM fornecedor WHERE id_fornecedor = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id_fornecedor, PDO::PARAM_INT);
    $stmt->execute();
    $fornecedor = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$fornecedor) {
        echo "<script>alert('Fornecedor não encontrado!');window.location.href='alterar_fornecedor.php';</script>";
        exit();
    }
}

// Se o formulário for enviado, busca o usuário pelo ID ou nome
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['busca_fornecedor'])) {
        $busca = trim($_POST['busca_fornecedor']);

        // Verifica se a busca é um número (ID) ou um nome
        if (is_numeric($busca)) {
            $sql = "SELECT * FROM fornecedor WHERE id_fornecedor = :busca";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':busca', $busca, PDO::PARAM_INT);
        } else {
            $sql = "SELECT * FROM fornecedor WHERE nome_fornecedor LIKE :busca_nome";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':busca_nome', "%$busca%", PDO::PARAM_STR);
        }

        $stmt->execute();
        $fornecedor = $stmt->fetch(PDO::FETCH_ASSOC);

        // Se o usuário não for encontrado, exibe um alerta
        if (!$fornecedor) {
            echo "<script>alert('Fornecedor não encontrado!');</script>";
        }
    }

    if (isset($_POST['id_fornecedor'])) {
        $nome_fornecedor = trim($_POST['nome']);
        $endereco = trim($_POST['endereco']);
        $telefone = $_POST['telefone'];
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $contato = $_POST['contato'];
        $id_fornecedor = $_POST['id_fornecedor'];

        $sql = "UPDATE fornecedor SET nome_fornecedor = :nome_fornecedor, endereco = :endereco, telefone = :telefone, email = :email, contato = :contato WHERE id_fornecedor = :id_fornecedor";
        $stmt = $pdo->prepare($sql);

        if ($stmt->execute([
            ':nome_fornecedor' => $nome_fornecedor,
            ':endereco' => $endereco,
            ':telefone' => $telefone,
            ':email' => $email,
            ':contato' => $contato,
            'id_fornecedor' => $id_fornecedor
        ])) {
            echo "<script>alert('Fornecedor alterado com sucesso!'); window.location.href='alterar_fornecedor.php';</script>";
            exit();
        } else {
            echo "<script>alert('Erro ao alterar Fornecedor!');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alterar Usuário</title>

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

        .form-control:focus, .form-select:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }

        .btn-action {
            transition: all 0.3s ease;
        }

        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(13, 110, 253, 0.15);
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
            <i class="bi bi-pencil-square text-primary me-2"></i>
            Alterar Fornecedor
        </h2>
        <button class="btn btn-outline-secondary" onclick="window.location.href='principal.php'">
            <i class="bi bi-arrow-left me-2"></i>Voltar
        </button>
    </div>

    <!-- Card de busca -->
    <div class="card custom-card mb-4">
        <div class="card-body">
            <form action="alterar_fornecedor.php" method="POST" class="d-flex gap-3">
                <div class="flex-grow-1">
                    <div class="input-group">
                            <span class="input-group-text bg-white">
                                <i class="bi bi-search text-primary"></i>
                            </span>
                        <input type="text" class="form-control" name="busca_fornecedor" id="busca_fornecedor"
                               placeholder="Digite o ID ou nome do fornecedor" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-search me-2"></i>Buscar
                </button>
            </form>
        </div>
    </div>

    <!-- Formulário de alteração -->
    <?php if ($fornecedor): ?>
        <div class="card custom-card">
            <div class="card-body p-4">
                <form action="alterar_fornecedor.php" method="POST" class="needs-validation" >
                    <input type="hidden" name="id_fornecedor" value="<?= htmlspecialchars($fornecedor['id_fornecedor']) ?>">

                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="nome" name="nome"
                                       value="<?= htmlspecialchars($fornecedor['nome_fornecedor']) ?>" required>
                                <label for="nome">
                                    <i class="bi bi-person me-2"></i>Nome Fornecedor
                                </label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="endereco" name="endereco"
                                       value="<?= htmlspecialchars($fornecedor['endereco']) ?>" required>
                                <label for="endereco">
                                    <i class="bi bi-key me-2"></i>Endereço
                                </label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="tel"
                                       class="form-control"
                                       id="telefone"
                                       name="telefone"
                                       pattern="\([0-9]{2}\) [0-9]{4,5}-[0-9]{4}"
                                       maxlength="15"
                                       placeholder="(99) 99999-9999"
                                       value="<?= htmlspecialchars($fornecedor['telefone']) ?>"
                                       title="Digite um telefone válido com DDD no formato (99) 99999-9999"
                                       required>
                                <label for="telefone">
                                    <i class="bi bi-telephone me-2"></i>Telefone
                                </label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="email" class="form-control" id="email" name="email"
                                       value="<?= htmlspecialchars($fornecedor['email']) ?>" required>
                                <label for="email">
                                    <i class="bi bi-envelope me-2"></i>Email
                                </label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="contato" name="contato"
                                       value="<?= htmlspecialchars($fornecedor['contato']) ?>" required>
                                <label for="email">
                                    <i class="bi bi-person me-2"></i>Contato
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="reset" class="btn btn-outline-secondary px-4">
                            <i class="bi bi-x-circle me-2"></i>Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary px-4 btn-action">
                            <i class="bi bi-check-circle me-2"></i>Alterar
                        </button>
                    </div>
                </form>
            </div>
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

<script>
    document.getElementById('telefone').addEventListener('input', function(e) {
        let value = e.target.value;
        value = value.replace(/\D/g, '');

        if (value.length <= 10) {
            value = value.replace(/^(\d{2})(\d{4})(\d{4}).*/, '($1) $2-$3');
        } else {
            value = value.replace(/^(\d{2})(\d{5})(\d{4}).*/, '($1) $2-$3');
        }

        e.target.value = value;
    });

    // Adiciona validação antes do envio
    document.querySelector('form').addEventListener('submit', function(e) {
        const telefone = document.getElementById('telefone');
        const telefoneValue = telefone.value.replace(/\D/g, '');

        if (telefoneValue.length < 10 || telefoneValue.length > 11) {
            e.preventDefault();
            telefone.setCustomValidity('Digite um telefone válido com DDD');
        } else {
            telefone.setCustomValidity('');
        }
    });

    document.getElementById('telefone').addEventListener('input', function(e) {
        e.target.setCustomValidity('');
    });
</script>
</body>
</html>