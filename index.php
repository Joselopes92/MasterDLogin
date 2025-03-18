<?php
session_start();

// Include database connection
include 'db_connection.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $utilizador = htmlspecialchars($_POST['username']); // Sanitize input
    $senha = $_POST['password']; // Password doesn't need htmlspecialchars

    if (!empty($utilizador) && !empty($senha)) {
        // Check if there is more than one admin
        $adminCheck = pg_query($conn, "SELECT COUNT(*) AS admin_count FROM utilizadores WHERE user_type = 'admin'");
        if (!$adminCheck) {
            die("Erro ao verificar administradores: " . pg_last_error($conn));
        }

        $adminData = pg_fetch_assoc($adminCheck);
        if ($adminData['admin_count'] > 1) {
            echo '<h3>Não pode haver mais que um admin!</h3>';
        } else {
            // Fetch user data using prepared statements
            $query = "SELECT * FROM utilizadores WHERE user_name = $1";
            $result = pg_query_params($conn, $query, array($utilizador));

            if ($result && pg_num_rows($result) > 0) {
                $row = pg_fetch_assoc($result);
                if (password_verify($senha, $row['senha'])) {
                    // Regenerate session ID for security
                    session_regenerate_id(true);

                    // Set session variables
                    $_SESSION['username'] = $row['user_name'];
                    $_SESSION['id'] = $row['id'];

                    // Redirect based on user type
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
        echo '<h3>Por favor, preencha todos os campos!</h3>';
    }

    pg_close($conn); // Close the database connection
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