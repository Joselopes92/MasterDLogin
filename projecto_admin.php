<?php
session_start();
include 'db_connection.php'; // Certifique-se de que este arquivo configura a conexão com o PostgreSQL

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitiza os dados do formulário
    $user_id = htmlspecialchars($_SESSION['user_id']);
    $data_inicio = htmlspecialchars($_POST['data_inicio']);
    $data_termino = htmlspecialchars($_POST['data_termino']);
    $tecnologia = htmlspecialchars($_POST['tecnologia']);
    $status = htmlspecialchars($_POST['status']);

    // Processa o upload da imagem
    $imagem = $_FILES['imagem'];
    $imagem_nome = basename($imagem['name']);
    $imagem_caminho = 'PHPMercearia-master/uploads/' . $imagem_nome;

    // Cria o diretório de uploads, se não existir
    if (!file_exists('uploads')) {
        mkdir('uploads', 0777, true);
    }

    // Move a imagem para o diretório de uploads
    if (move_uploaded_file($imagem['tmp_name'], $imagem_caminho)) {
        // Insere os dados do projeto no PostgreSQL
        $query = "INSERT INTO projetos (user_id, data_inicio, data_termino, tecnologia, status, imagem) VALUES ($1, $2, $3, $4, $5, $6)";
        $result = pg_query_params($conn, $query, array($user_id, $data_inicio, $data_termino, $tecnologia, $status, $imagem_caminho));

        if ($result) {
            header("Location: pag_admin.php");
            exit();
        } else {
            echo "Erro ao adicionar o projeto: " . pg_last_error($conn);
        }
    } else {
        echo "Erro ao fazer upload da imagem!";
    }
}

pg_close($conn); // Fecha a conexão com o PostgreSQL
?>
