<?php
include 'db_connection.php'; // Certifique-se de que este arquivo configura a conexão com o PostgreSQL
session_start();

if (isset($_SESSION['username']) && isset($_SESSION['id'])) {
    if (isset($_GET['id_projeto'])) {
        $id_projeto = $_GET['id_projeto'];

        // Executa a exclusão do projeto no PostgreSQL
        $query = "DELETE FROM projetos WHERE id_projeto = $1";
        $result = pg_query_params($conn, $query, array($id_projeto));

        if ($result) {
            echo "Projeto apagado com sucesso!";
            header("Location: pag_utilizador.php");
            exit();
        } else {
            echo "Falha ao apagar o projeto: " . pg_last_error($conn);
        }
    } else {
        echo "ID do projeto não fornecido!";
    }
} else {
    echo "Sessão não válida!";
}

pg_close($conn); // Fecha a conexão com o PostgreSQL
?>