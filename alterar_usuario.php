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
    </style>
</head>
<body class="bg-light">
<div class="container py-5 fade-in">
    <!-- Cabeçalho -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="display-6">
            <i class="bi bi-pencil-square text-primary me-2"></i>
            Alterar Usuário
        </h2>
        <button class="btn btn-outline-secondary" onclick="window.location.href='principal.php'">
            <i class="bi bi-arrow-left me-2"></i>Voltar
        </button>
    </div>

    <!-- Card de busca -->
    <div class="card custom-card mb-4">
        <div class="card-body">
            <form action="alterar_usuario.php" method="POST" class="d-flex gap-3">
                <div class="flex-grow-1">
                    <div class="input-group">
                            <span class="input-group-text bg-white">
                                <i class="bi bi-search text-primary"></i>
                            </span>
                        <input type="text" class="form-control" name="busca_usuario" id="busca_usuario"
                               placeholder="Digite o ID ou nome do usuário" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-search me-2"></i>Buscar
                </button>
            </form>
        </div>
    </div>

    <!-- Formulário de alteração -->
    <?php if ($usuario): ?>
        <div class="card custom-card">
            <div class="card-body p-4">
                <form action="alterar_usuario.php" method="POST" class="needs-validation" novalidate>
                    <input type="hidden" name="id_usuario" value="<?= htmlspecialchars($usuario['id_usuario']) ?>">

                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="nome" name="nome"
                                       value="<?= htmlspecialchars($usuario['nome']) ?>" required>
                                <label for="nome">
                                    <i class="bi bi-person me-2"></i>Nome
                                </label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="email" class="form-control" id="email" name="email"
                                       value="<?= htmlspecialchars($usuario['email']) ?>" required>
                                <label for="email">
                                    <i class="bi bi-envelope me-2"></i>Email
                                </label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <select class="form-select" id="id_perfil" name="id_perfil" required>
                                    <option value="1" <?= $usuario['id_perfil'] == 1 ? 'selected' : '' ?>>Administrador</option>
                                    <option value="2" <?= $usuario['id_perfil'] == 2 ? 'selected' : '' ?>>Secretaria</option>
                                    <option value="3" <?= $usuario['id_perfil'] == 3 ? 'selected' : '' ?>>Almoxarife</option>
                                    <option value="4" <?= $usuario['id_perfil'] == 4 ? 'selected' : '' ?>>Cliente</option>
                                </select>
                                <label for="id_perfil">
                                    <i class="bi bi-shield-check me-2"></i>Perfil
                                </label>
                            </div>
                        </div>

                        <?php if ($_SESSION['perfil'] == 1): ?>
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="password" class="form-control" id="nova_senha" name="nova_senha">
                                    <label for="nova_senha">
                                        <i class="bi bi-key me-2"></i>Nova Senha
                                    </label>
                                </div>
                            </div>
                        <?php endif; ?>
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

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>