<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="PHPMercearia-master/Style/pagina_registo.css">
    <title>Caso prático PHP</title>
</head>
<body>
    <h1>Página de registo!</h1>
    <div id="login-container">
        <div class="content">
            <div class="image-container">
                <img src="PHPMercearia-master/uploads/cidade.jpg" alt="Imagem de Fundo" class="background-image">
            </div>
            <div id="loginForm" class="form-container">
                <form action="" method="post" enctype="multipart/form-data">
                    Nome:
                    <input type="text" name="nome" required><br>
                    Apelido:
                    <input type="text" name="apelido" required><br>
                    Username:
                    <input type="text" name="username" required><br>
                    Password:
                    <input type="password" name="password" required><br>
                    E-mail:
                    <input type="email" name="email" required><br>
                    Imagem:
                    <input type="file" name="file"><br>
                    Tipo de utilizador:
                    <select name="tipoUtilizador" value="Escolha uma opção!" required>
                        <option value="admin">Admin</option>
                        <option value="utilizador">Utilizador</option>
                    </select>

                    <input type="submit" name="submit" value="Registrar" id="submit">
                    <a href="index.php">Fazer Login!</a>
                </form>
            </div>
        </div>
    </div>
    <div class="wave"></div>
    <div class="wave"></div>
    <div class="wave"></div>
        
    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        include 'db_connection.php'; // Certifique-se de que este arquivo configura a conexão com o PostgreSQL

        // Sanitiza os dados do formulário
        $nome = htmlspecialchars($_POST['nome']);
        $apelido = htmlspecialchars($_POST['apelido']);
        $utilizador = htmlspecialchars($_POST['username']);
        $senha = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $email = htmlspecialchars($_POST['email']);
        $tipoUtilizador = htmlspecialchars($_POST['tipoUtilizador']);

        // Verifica se o tipo de utilizador é admin e se já existe um admin
        if ($tipoUtilizador === 'admin') {
            $query = "SELECT COUNT(*) AS admin_count FROM utilizadores WHERE user_type = 'admin'";
            $result = pg_query($conn, $query);

            if ($result && pg_num_rows($result) > 0) {
                $adminData = pg_fetch_assoc($result);
                if ($adminData['admin_count'] >= 1) {
                    echo '<h3>Não pode haver mais que um admin!</h3>';
                    exit();
                }
            }
        }

        // Processa o upload da imagem
        $imagem = '';
        if (isset($_FILES['file']) && $_FILES['file']['error'] == UPLOAD_ERR_OK) {
            $imagem = 'PHPMercearia-master/uploads/' . basename($_FILES['file']['name']);
            move_uploaded_file($_FILES['file']['tmp_name'], $imagem);
        }

        // Insere os dados do utilizador no PostgreSQL
        $query = "INSERT INTO utilizadores (nome, apelido, user_name, email, imagem, senha, user_type) VALUES ($1, $2, $3, $4, $5, $6, $7)";
        $result = pg_query_params($conn, $query, array($nome, $apelido, $utilizador, $email, $imagem, $senha, $tipoUtilizador));

        if ($result) {
            echo "Dados inseridos com sucesso!";
            header('Location: index.php');
            exit();
        } else {
            echo "Dados não foram inseridos: " . pg_last_error($conn);
        }

        pg_close($conn); // Fecha a conexão com o PostgreSQL
    }
    ?>
</body>
</html>