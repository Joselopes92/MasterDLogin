<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

include 'db_connection.php'; // Certifique-se de que este arquivo configura a conexão com o PostgreSQL

// Função para verificar se a consulta pode ser editada (72 horas antes)
function can_edit($data_consulta, $hora_consulta) {
    $datahora_consulta = new DateTime($data_consulta . ' ' . $hora_consulta);
    $agora = new DateTime();
    $intervalo = $agora->diff($datahora_consulta);

    return ($intervalo->days > 3 || ($intervalo->days == 3 && $intervalo->h > 0));
}

if (isset($_GET['id_consulta'])) {
    // Sanitiza o ID da consulta
    $id_consulta = filter_var($_GET['id_consulta'], FILTER_SANITIZE_NUMBER_INT);

    if (filter_var($id_consulta, FILTER_VALIDATE_INT) === false) {
        echo "Parâmetros inválidos.";
        exit();
    }

    // Busca os dados da consulta no PostgreSQL
    $query = "SELECT data_consulta, hora_consulta FROM consultas WHERE id_consulta = $1";
    $result = pg_query_params($conn, $query, array($id_consulta));

    if ($result && pg_num_rows($result) > 0) {
        $consulta = pg_fetch_assoc($result);
        $data_consulta = htmlspecialchars($consulta['data_consulta']);
        $hora_consulta = htmlspecialchars($consulta['hora_consulta']);

        // Verifica se a consulta pode ser editada
        if (!can_edit($data_consulta, $hora_consulta)) {
            echo "A consulta não pode ser editada porque está a menos de 72 horas.";
            exit();
        }
    } else {
        echo "Consulta não encontrada.";
        exit();
    }
} else {
    echo "Parâmetros inválidos.";
    exit();
}

// Processa o formulário de edição
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar_consultas'])) {
    $data_consulta = htmlspecialchars($_POST['data_consulta']);
    $hora_consulta = htmlspecialchars($_POST['hora_consulta']);

    // Verifica se a data e hora da consulta são no futuro
    $datahora_consulta = new DateTime($data_consulta . ' ' . $hora_consulta);
    $agora = new DateTime();

    if ($datahora_consulta < $agora) {
        echo "Não é possível marcar uma consulta para uma data e hora no passado!";
        exit();
    }

    // Verifica se a consulta pode ser editada
    if (!can_edit($data_consulta, $hora_consulta)) {
        echo "A consulta não pode ser editada porque está a menos de 72 horas.";
        exit();
    }

    // Atualiza os dados da consulta no PostgreSQL
    $query = "UPDATE consultas SET data_consulta = $1, hora_consulta = $2 WHERE id_consulta = $3";
    $result = pg_query_params($conn, $query, array($data_consulta, $hora_consulta, $id_consulta));

    if ($result) {
        echo "Consulta atualizada com sucesso!";
        header("Location: pag_utilizador.php");
        exit();
    } else {
        echo "Erro ao atualizar a consulta: " . pg_last_error($conn);
    }
}

pg_close($conn); // Fecha a conexão com o PostgreSQL
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="PHPMercearia-master/Style/editar_consultas.css">
    <title>Editar Consultas - Caso Prático PHP</title>
</head>
<body>
    <div class="container">
        <form action="" method="post" class="edit-form">
            <input type="hidden" name="id_consulta" value="<?php echo $id_consulta; ?>">
            <label for="data_consulta">Data da Consulta:</label>
            <input type="date" id="data_consulta" name="data_consulta" value="<?php echo $data_consulta; ?>" required>
            <label for="hora_consulta">Hora da Consulta:</label>
            <input type="time" id="hora_consulta" name="hora_consulta" value="<?php echo $hora_consulta; ?>" required>
            <input type="submit" name="editar_consultas" value="Atualizar Consulta">
        </form>
        <a href="pag_utilizador.php" class="voltar">Voltar</a>
    </div>
    <div class="wave"></div>
    <div class="wave"></div>
    <div class="wave"></div>
</body>
</html>