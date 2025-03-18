<?php
session_start();
include 'db_connection.php'; // Certifique-se de que este arquivo configura a conexão com o PostgreSQL

if (isset($_SESSION['username']) && isset($_SESSION['id'])) {
    $userId = $_SESSION['id'];

    if (isset($_GET['id'])) {
        $consultarId = $_GET['id'];

        // Verifica se o usuário está tentando excluir a própria conta
        if ($consultarId == $userId) {
            echo "Você não pode excluir sua própria conta!";
        } else {
            // Executa a exclusão do usuário no PostgreSQL
            $query = "DELETE FROM utilizadores WHERE id = $1";
            $result = pg_query_params($conn, $query, array($consultarId));

            if ($result) {
                header("Location: pag_admin.php");
                exit();
            } else {
                echo "Falha ao apagar o usuário: " . pg_last_error($conn);
            }
        }
    } else {
        echo "ID do usuário não fornecido!";
    }
} else {
    echo "Sessão não válida!";
}

pg_close($conn); // Fecha a conexão com o PostgreSQL
?>