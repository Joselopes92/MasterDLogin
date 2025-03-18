<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id_projeto'])) {
        $id_projeto = $_POST['id_projeto'];
    }
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
        echo "A data de início não pode ser no passado.";
        exit();
    }

    // Cria o diretório de uploads, se não existir
    if (!is_dir('uploads')) {
        mkdir('uploads', 0777, true);
    }

    // Processa o upload da imagem
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == UPLOAD_ERR_OK) {
        $imagem = 'uploads/' . basename($_FILES['imagem']['name']);
        if (!move_uploaded_file($_FILES['imagem']['tmp_name'], $imagem)) {
            echo "Erro ao fazer upload da imagem.";
            exit();
        }
    }

    // Atualiza os dados do projeto no PostgreSQL
    $query = "UPDATE projetos SET nome_projeto = $1, descricao = $2, data_inicio = $3, data_termino = $4, tecnologia = $5, status = $6, imagem = $7 WHERE id_projeto = $8";
    $result = pg_query_params($conn, $query, array($nome, $descricao, $data_inicio, $data_termino, $tecnologia, $status, $imagem, $id_projeto));

    if ($result) {
        header('Location: pag_admin.php');
        exit();
    } else {
        echo "Erro: " . pg_last_error($conn);
    }

    pg_close($conn); // Fecha a conexão com o PostgreSQL
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
    <h1>Atualizar Projeto</h1>
    <div class="logout-container">
        <a href="logout.php" class="logout-link">Logout</a>
    </div>
    <div id="dados">
        <div class="content">
            <div class="projetos-container">
                <h2>Atualizar Projeto</h2>
                <form action="" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="id_projeto" value="<?php echo htmlspecialchars($_GET['id_projeto']); ?>">

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

                    <input type="submit" value="Atualizar Projeto">
                    <a href="pag_admin.php" class="voltar">Voltar</a>
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