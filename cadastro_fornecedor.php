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
    $nome_fornecedor = trim($_POST['nome']);
    $endereco = trim($_POST['endereco']);
    $telefone = $_POST['telefone'];
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $contato = $_POST['contato'];


    // Verifica se já existe usuário com mesmo email ou nome
    $sql = "SELECT * FROM fornecedor WHERE email = :email OR nome_fornecedor = :nome_fornecedor";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':email' => $email,
        ':nome_fornecedor' => $nome_fornecedor
    ]);

    $fornecedor_existente = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($fornecedor_existente) {
        echo "<script>alert('Fornecedor já cadastrado com este nome ou email!'); window.location.href='cadastro_fornecedor.php';</script>";
        exit;
    }

    $sql = "INSERT INTO fornecedor(nome_fornecedor, endereco, telefone, email, contato)
    VALUES (:nome_fornecedor, :endereco, :telefone, :email, :contato)";
    $stmt = $pdo->prepare($sql);

    if ($stmt->execute([
        ':nome_fornecedor' => $nome_fornecedor,
        ':endereco' => $endereco,
        ':telefone' => $telefone,
        ':email' => $email,
        ':contato' => $contato
    ])) {
        echo "<script>alert('Fornecedor cadastrado com sucesso!');</script>";
    } else {
        echo "<script>alert('Erro ao cadastrar Fornecedor!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Fornecedor</title>

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
            <i class="bi bi-person-plus-fill text-primary me-2"></i>
            Cadastrar Fornecedor
        </h2>
        <button class="btn btn-outline-secondary" onclick="window.location.href='principal.php'">
            <i class="bi bi-arrow-left me-2"></i>Voltar
        </button>
    </div>

    <!-- Card do formulário -->
    <div class="card custom-card">
        <div class="card-body p-4">
            <form action="./cadastro_fornecedor.php" method="post" class="needs-validation" novalidate>
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="nome" name="nome" required>
                            <label for="nome">
                                <i class="bi bi-person me-2"></i>Nome Fornecedor
                            </label>
                            <div class="invalid-feedback">Por favor, informe o nome do fornecedor.</div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="endereco" name="endereco" required>
                            <label for="endereco">
                                <i class="bi bi-key me-2"></i>Endereço
                            </label>
                            <div class="invalid-feedback">Por favor, informe o endereço.</div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating mb-3">
                            <input type="tel" class="form-control" id="telefone" name="telefone" placeholder="(00)00000-0000" required>
                            <label for="telefone">
                                <i class="bi bi-telephone me-2"></i>Telefone
                            </label>
                            <div class="invalid-feedback">Por favor, informe um telefone válido.</div>
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
                            <input type="text" class="form-control" id="contato" name="contato" required>
                            <label for="contato">
                                <i class="bi bi-person me-2"></i>Contato
                            </label>
                            <div class="invalid-feedback">Por favor, informe um contato.</div>
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

<!-- Copyright Logo -->
<div class="copyright-logo">
    <span class="copyright-text">
        <i class="bi bi-c-circle"></i> 2024 Yohan Siedschlag
    </span>
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

    // Máscara e validação de telefone em tempo real
    document.getElementById('telefone').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, ''); // Remove tudo que não é dígito

        // Aplica a máscara
        if (value.length <= 10) {
            // Formato para telefone fixo: (00) 0000-0000
            value = value.replace(/^(\d{2})(\d{4})(\d{0,4}).*/, '($1)$2-$3');
        } else {
            // Formato para celular: (00) 00000-0000
            value = value.replace(/^(\d{2})(\d{5})(\d{0,4}).*/, '($1)$2-$3');
        }

        e.target.value = value;

        // Validação em tempo real
        const phoneRegex = /^\(\d{2}\)\d{4,5}-\d{4}$/;
        const isValid = phoneRegex.test(value) && (value.length === 14 || value.length === 15);

        if (value.length > 0) {
            if (isValid) {
                e.target.classList.remove('is-invalid');
                e.target.classList.add('is-valid');
            } else {
                e.target.classList.remove('is-valid');
                e.target.classList.add('is-invalid');
            }
        } else {
            e.target.classList.remove('is-valid', 'is-invalid');
        }
    });

    // Remove formatação visual ao focar no campo (opcional)
    document.getElementById('telefone').addEventListener('focus', function(e) {
        if (e.target.value === '') {
            e.target.placeholder = '(00)00000-0000';
        }
    });
</script>
</body>
</html>