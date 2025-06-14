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
    $email = $_POST['email'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
    $id_perfil = $_POST['id_perfil'];

    // Verifica se já existe usuário com mesmo email ou nome
    $sql = "SELECT * FROM usuario WHERE email = :email OR nome = :nome";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':email' => $email,
        ':nome' => $nome
    ]);

    $usuario_existente = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario_existente) {
        echo "<script>alert('Usuário já cadastrado com este nome ou email!'); window.location.href='cadastro_usuario.php';</script>";
        exit;
    }

    $sql = "INSERT INTO usuario(nome, email, senha, id_perfil)
    VALUES (:nome, :email, :senha, :id_perfil)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':senha', $senha);
    $stmt->bindParam(':id_perfil', $id_perfil);

    if ($stmt->execute()) {
        echo "<script>alert('Usuário cadastrado com sucesso!');</script>";
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
    <title>Cadastrar Usuário</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        /* Animações e estilos personalizados */
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

        .btn-primary {
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
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
            <i class="bi bi-person-plus-fill text-primary me-2"></i>
            Cadastrar Usuário
        </h2>
        <button class="btn btn-outline-secondary" onclick="window.location.href='principal.php'">
            <i class="bi bi-arrow-left me-2"></i>Voltar
        </button>
    </div>

    <!-- Card do formulário -->
    <div class="card custom-card">
        <div class="card-body p-4">
            <form action="./cadastro_usuario.php" method="post" class="needs-validation" novalidate>
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="nome" name="nome" required>
                            <label for="nome">
                                <i class="bi bi-person me-2"></i>Nome
                            </label>
                            <div class="invalid-feedback">Por favor, informe o nome.</div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating mb-3">
                            <input type="email" class="form-control" id="email" name="email" required>
                            <label for="email">
                                <i class="bi bi-envelope me-2"></i>Email
                            </label>
                            <div class="invalid-feedback">Por favor, informe um email válido.</div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating mb-3">
                            <input type="password" class="form-control" id="senha" name="senha" required>
                            <label for="senha">
                                <i class="bi bi-key me-2"></i>Senha
                            </label>
                            <div class="invalid-feedback">Por favor, defina uma senha.</div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating mb-3">
                            <select class="form-select" id="id_perfil" name="id_perfil" required>
                                <option value="1">Administrador</option>
                                <option value="2">Secretaria</option>
                                <option value="3">Almoxarife</option>
                                <option value="4">Cliente</option>
                            </select>
                            <label for="id_perfil">
                                <i class="bi bi-shield-check me-2"></i>Perfil
                            </label>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <button type="reset" class="btn btn-outline-secondary px-4">
                        <i class="bi bi-x-circle me-2"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-check-circle me-2"></i>Cadastrar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Validação do formulário -->
<script>
    (() => {
        'use strict';
        const forms = document.querySelectorAll('.needs-validation');
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    })();
</script>
</body>
</html>