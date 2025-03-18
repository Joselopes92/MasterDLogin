<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include 'db_connection.php'; // Certifique-se de que este arquivo configura a conexão com o PostgreSQL
    $utilizador = $_POST['username'];
    $senha = $_POST['password'];

    if (!empty($utilizador) && !empty($senha)) {
        // Verifica se já existe mais de um admin
        $adminCheck = pg_query($conn, "SELECT COUNT(*) AS admin_count FROM utilizadores WHERE user_type = 'admin'");
        $adminData = pg_fetch_assoc($adminCheck);

        if ($adminData['admin_count'] > 1) {
            echo '<h3>Não pode haver mais que um admin!</h3>';
        } else {
            // Busca os dados do utilizador no PostgreSQL
            $query = "SELECT * FROM utilizadores WHERE user_name = $1";
            $result = pg_query_params($conn, $query, array($utilizador));

            if ($result && pg_num_rows($result) > 0) {
                $row = pg_fetch_assoc($result);
                if (password_verify($senha, $row['senha'])) {
                    $_SESSION['username'] = $row['user_name'];
                    $_SESSION['id'] = $row['id'];

                    // Redireciona com base no tipo de utilizador
                    if ($row['user_type'] == 'admin') {
                        header('Location: pag_admin.php');
                    } else {
                        header('Location: pag_utilizador.php');
                    }
                    exit();
                } else {
                    echo '<h3>Nome ou senha incorretos!</h3>';
                }
            } else {
                echo '<h3>Nome ou senha incorretos!</h3>';
            }
        }
    } else {
        echo 'Por favor, preencha todos os campos!';
    }

    pg_close($conn); // Fecha a conexão com o PostgreSQL
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="PHPMercearia-master/Style/index.css">
    <title>Caso prático PHP</title>
</head>
<body>
    <h1>Página login!</h1>
    <div id="loginContainer">
        <div class="content">
            <div class="image-container">
                <img src="PHPMercearia-master/uploads/carros.webp" alt="Imagem de Fundo" class="background-image">
            </div>
            <div id="loginForm" class="form-container">
                <form action="" method="post">
                    Nome:
                    <input type="text" name="username" id="username" required><br>
                    Password:
                    <input type="password" name="password" id="password" required><br>
                    <input type="submit" name="submit" value="Login" id="submit">
                    <a href="pagina-registo.php">Criar registo?</a>
                </form>
            </div>
        </div>
    </div>
    <div class="wave"></div>
    <div class="wave"></div>
    <div class="wave"></div>
</body>
</html>