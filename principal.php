<?php
session_start();
require_once 'conexao.php';

// Garante que o usuário esteja logado
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}
// Obtendo o nome do perfil do usuário logado
$id_perfil = $_SESSION["perfil"];

$sqlPerfil = "SELECT nome_perfil FROM perfil WHERE id_perfil = :id_perfil";
$stmtPerfil = $pdo->prepare($sqlPerfil);
$stmtPerfil->bindParam(':id_perfil', $id_perfil);
$stmtPerfil->execute();
$perfil = $stmtPerfil->fetch(PDO::FETCH_ASSOC);

$nome_perfil = $perfil['nome_perfil'];

// Definição das permissões por perfil

$permissoes = [
    1 => [
        "Cadastrar" => [
            "cadastro_usuario.php",
            "cadastro_perfil.php",
            "cadastro_cliente.php",
            "cadastro_fornecedor.php",
            "cadastro_produto.php",
            "cadastro_funcionário.php",
        ],
        "Buscar" => [
            "buscar_usuario.php",
            "buscar_perfil.php",
            "buscar_cliente.php",
            "buscar_fornecedor.php",
            "buscar_produto.php",
            "buscar_funcionário.php",
        ],
        "Alterar" => [
            "alterar_usuario.php",
            "alterar_perfil.php",
            "alterar_cliente.php",
            "alterar_fornecedor.php",
            "alterar_produto.php",
            "alterar_funcionário.php",
        ],
        "Excluir" => [
            "excluir_usuario.php",
            "excluir_perfil.php",
            "excluir_cliente.php",
            "excluir_fornecedor.php",
            "excluir_produto.php",
            "excluir_funcionário.php",
        ]
    ],
    2 => [
        "Cadastrar" => [
            "cadastro_cliente.php"
        ],
        "Buscar" => [
            "buscar_cliente.php",
            "buscar_fornecedor.php",
            "buscar_produto.php"
        ],
        "Alterar" => [
            "alterar_cliente.php",
            "alterar_fornecedor.php",
        ]
    ],
    3 => [
        "Cadastrar" => [
            "cadastro_fornecedor.php",
            "cadastro_produto.php",
        ],
        "Buscar" => [
            "buscar_cliente.php",
            "buscar_fornecedor.php",
            "buscar_produto.php",
        ],
        "Alterar" => [
            "alterar_fornecedor.php",
            "alterar_produto.php",
        ],
        "Excluir" => [
            "excluir_produto.php",
        ]
    ],
    4 => [
        "Cadastrar" => [
            "cadastro_cliente.php",
        ],
        "Buscar" => [
            "buscar_produto.php",
        ],
        "Alterar" => [
            "alterar_cliente.php",
        ]
    ]
];

// Obtendo as opções disponiveis para o perfil logado
$opcoes_menu = $permissoes[$id_perfil];

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Principal</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">

    <link rel="stylesheet" href="./css/styles.css">

    <style>
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

    <script src="./scripts/scripts.js"></script>
</head>

<body>
    <header>

        <div class="saudacao">
            <h2>Bem-Vindo, <?= $_SESSION['usuario']; ?>! Perfil: <?= $nome_perfil; ?></h2>
        </div>

        <div class="logout">
            <form action="./logout.php" method="post">
                <button type="submit">Logout</button>
            </form>
        </div>

    </header>

    <nav>
        <ul class="menu">
            <?php foreach ($opcoes_menu as $categoria => $arquivos): ?>
                <li class="dropdown">
                    <a href="#"><?= $categoria ?></a>
                    <ul class="dropdown-menu">
                        <?php foreach ($arquivos as $arquivo): ?>
                            <li>
                                <a href="<?= $arquivo ?>"><?= ucfirst(str_replace("_", " ", basename($arquivo, ".php"))); ?></a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>

    <!-- Copyright Logo -->
    <div class="copyright-logo">
    <span class="copyright-text">
        <i class="bi bi-c-circle"></i> 2024 Yohan Siedschlag
    </span>
    </div>
</body>

</html>