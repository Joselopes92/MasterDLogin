<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="PHPMercearia-master/Style/alterar_info.css">
    <title>Atualizar Dados do Utilizador</title>
</head>
<body>
    <div id="utilizador">
        <h1>Atualização dos dados</h1>
        <div id="dados">
            <div class="content">
                <div class="form-container">
                    <?php
                    include 'db_connection.php'; 

                    if (isset($_GET['id'])) {
                        $userId = $_GET['id'];

                        // Busca os dados do utilizador no PostgreSQL
                        $query = "SELECT * FROM utilizadores WHERE id = $1";
                        $result = pg_query_params($conn, $query, array($userId));

                        if ($result && pg_num_rows($result) > 0) {
                            $row = pg_fetch_assoc($result);
                    ?>
                            <form action="" method="post">
                                <label for="nome">Nome:</label>
                                <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($row['nome']); ?>" required>

                                <label for="apelido">Apelido:</label>
                                <input type="text" id="apelido" name="apelido" value="<?php echo htmlspecialchars($row['apelido']); ?>" required>

                                <label for="user_name">User Name:</label>
                                <input type="text" id="user_name" name="user_name" value="<?php echo htmlspecialchars($row['user_name']); ?>" required>

                                <label for="email">Email:</label>
                                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($row['email']); ?>" required>

                                <label for="senha">Senha:</label>
                                <input type="password" id="senha" name="senha" required>

                                <input type="submit" name="submit" value="Atualizar">
                            </form>
                    <?php
                        } else {
                            echo "Utilizador não encontrado!";
                        }
                    } else {
                        echo "Sessão não válida!";
                    }

                    // Processa o envio do formulário
                    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                        $nome = $_POST['nome'];
                        $apelido = $_POST['apelido'];
                        $userName = $_POST['user_name'];
                        $email = $_POST['email'];
                        $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);

                        // Atualiza os dados do utilizador no PostgreSQL
                        $query = "UPDATE utilizadores SET nome = $1, apelido = $2, user_name = $3, email = $4, senha = $5 WHERE id = $6";
                        $result = pg_query_params($conn, $query, array($nome, $apelido, $userName, $email, $senha, $userId));

                        if ($result) {
                            echo "Dados atualizados com sucesso!";
                            $_SESSION['username'] = $userName; // Atualiza o nome de utilizador na sessão
                        } else {
                            echo "Erro ao atualizar os dados: " . pg_last_error($conn);
                        }
                    }

                    pg_close($conn); // Fecha a conexão com o PostgreSQL
                    ?>
                    <a href="pag_admin.php" class="editarDados">Voltar</a>
                    <a href="index.php" class="editarDados">Logout</a>
                </div>
            </div>
        </div>
    </div>
    <div class="wave"></div>
    <div class="wave"></div>
    <div class="wave"></div>
</body>
</html>