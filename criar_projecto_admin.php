<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include 'db_connection.php'; // Certifique-se de que este arquivo configura a conexão com o PostgreSQL

    // Sanitiza os dados do formulário
    $nome = htmlspecialchars($_POST['nome_projeto']);
    $descricao = htmlspecialchars($_POST['descricao']);
    $data_inicio = htmlspecialchars($_POST['data_inicio']);
    $data_termino = htmlspecialchars($_POST['data_termino']);
    $tecnologia = htmlspecialchars($_POST['tecnologia']);
    $status = htmlspecialchars($_POST['status']);
    $imagem = '';

    // Verifica se a data de início é no passado
    $data_inicio_projecto = new DateTime($data_inicio);
    $agora = new DateTime();

    if ($data_inicio_projecto < $agora) {
        echo "Não é possível marcar uma consulta para uma data e hora no passado!";
        exit();
    }

    // Cria o diretório de uploads, se não existir
    if (!is_dir('uploads')) {
        mkdir('uploads', 0777, true);
    }

    // Processa o upload da imagem
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == UPLOAD_ERR_OK) {
        $imagem = 'PHPMercearia-master/uploads/' . basename($_FILES['imagem']['name']);
        if (!move_uploaded_file($_FILES['imagem']['tmp_name'], $imagem)) {
            echo "Erro ao fazer upload da imagem.";
            exit();
        }
    }

    // Insere os dados no PostgreSQL
    $query = "INSERT INTO projetos (id, nome_projeto, descricao, data_inicio, data_termino, tecnologia, imagem, status) VALUES ($1, $2, $3, $4, $5, $6, $7, $8)";
    $result = pg_query_params($conn, $query, array(
        $_POST['id'],
        $nome,
        $descricao,
        $data_inicio,
        $data_termino,
        $tecnologia,
        $imagem,
        $status
    ));

    if ($result) {
        header('Location: pag_admin.php');
        exit();
    } else {
        echo "Erro: " . pg_last_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="PHPMercearia-master/Style/pag_admin.css">
    <title>Painel do Administrador</title>
</head>
<body>

<div id="utilizador">
    <h1>Criar Projecto</h1>
    <div class="logout-container">
        <a href="logout.php" class="logout-link">Logout</a>
    </div>
    <div id="dados">
        <div class="content">
            <div class="projetos-container">
                <h2>Adicionar Novo Projeto</h2>
                <form action="" method="post" enctype="multipart/form-data">
                    <label for="id">Id dos Utilizadores</label>
                    <select name="id" id="id">
                        <?php
                        include 'db_connection.php'; // Certifique-se de que este arquivo configura a conexão com o PostgreSQL
                        $query = "SELECT id FROM utilizadores";
                        $result = pg_query($conn, $query);

                        if ($result && pg_num_rows($result) > 0) {
                            while ($row = pg_fetch_assoc($result)) {
                                echo '<option value="' . $row['id'] . '">' . $row['id'] . '</option>';
                            }
                        } else {
                            echo '<option disabled>Não tem qualquer informação no seu banco de dados</option>';
                        }
                        ?>
                    </select>
                    <label for="nome_projeto">Nome do Projeto:</label>
                    <input type="text" id="nome_projeto" name="nome_projeto" required><br>

                    <label for="descricao">Descrição:</label>
                    <textarea id="descricao" name="descricao" required></textarea><br>

                    <label for="data_inicio">Data de Início:</label>
                    <input type="date" id="data_inicio" name="data_inicio" required><br>

                    <label for="data_termino">Data de Término:</label>
                    <input type="date" id="data_termino" name="data_termino" required><br>

                    <label for="tecnologia">Tecnologia:</label>
                    <input type="text" id="tecnologia" name="tecnologia" required><br>

                    <label for="status">Status:</label>
                    <select id="status" name="status" required>
                        <option value="planejado">Planejado</option>
                        <option value="em_andamento">Em Andamento</option>
                        <option value="concluido">Concluído</option>
                    </select><br>

                    <label for="imagem">Imagem:</label>
                    <input type="file" id="imagem" name="imagem" accept="image/*" required><br>

                    <input type="submit" value="Adicionar Projeto">
                </form>
            </div>
        </div>
    </div>
</div>
<div class="wave"></div>
<div class="wave"></div>
<div class="wave"></div>
</body>
</html>

<?php
if (isset($conn)) {
    pg_close($conn); // Fecha a conexão com o PostgreSQL
}
?>