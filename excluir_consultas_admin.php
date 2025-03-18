<?php
include 'db_connection.php'; // Certifique-se de que este arquivo configura a conexão com o PostgreSQL
session_start();

if (isset($_SESSION['username']) && isset($_SESSION['id'])) {
    $userId = $_SESSION['id'];

    if (isset($_GET['id_consulta'])) {
        $consultaId = $_GET['id_consulta'];

        // Executa a exclusão da consulta no PostgreSQL
        $query = "DELETE FROM consultas WHERE id_consulta = $1 AND id = $2";
        $result = pg_query_params($conn, $query, array($consultaId, $userId));

        if ($result) {
            echo "Consulta apagada com sucesso!";
            header("Location: pag_admin.php");
            exit();
        } else {
            echo "Falha ao apagar a consulta: " . pg_last_error($conn);
        }
    } else {
        echo "ID da consulta não fornecido!";
    }
} else {
    echo "Sessão não válida!";
}

pg_close($conn); // Fecha a conexão com o PostgreSQL
?>