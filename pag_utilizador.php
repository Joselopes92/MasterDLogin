<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="PHPMercearia-master/Style/pag_utilizador.css">
    <title>Utilizador - Caso prático PHP</title>
</head>
<body>

<div id="utilizador">
    <?php 
    if (isset($_SESSION['username']) && isset($_SESSION['id'])) {
        echo '<h1> Bem-vindo/a ' . htmlspecialchars($_SESSION['username']) . '!</h1>'; 
    } else {
        echo '<h1>Utilizador não logado</h1>';
        exit();
    }
    ?>
    <div class="logout-container">
        <a href="logout.php" class="logout-link">Logout</a>
    </div>
    <div id="dados">
        <div class="content">
            <div class="image-container">
                <?php 
                include 'db_connection.php'; // Certifique-se de que este arquivo configura a conexão com o PostgreSQL
                $user_id = $_SESSION['id'];

                // Busca a imagem do utilizador no PostgreSQL
                $query = "SELECT imagem FROM utilizadores WHERE id = $1";
                $result = pg_query_params($conn, $query, array($user_id));

                if ($result && pg_num_rows($result) > 0) {
                    $user = pg_fetch_assoc($result);
                    echo '<img src="' . htmlspecialchars($user['imagem']) . '" alt="Imagem de Perfil" class="background-image">';
                } else {
                    echo "Imagem não encontrada!";
                }
                ?>
            </div>
            <div class="table-container">
                <h2>Dados do utilizador:</h2>
                <?php
                // Busca os dados do utilizador no PostgreSQL
                $query = "SELECT * FROM utilizadores WHERE id = $1";
                $result = pg_query_params($conn, $query, array($user_id));

                if ($result && pg_num_rows($result) > 0) {
                    echo '<table class="userTable">
                            <tr>
                                <th>Nome</th>
                                <th>Apelido</th>
                                <th>User_name</th>
                                <th>Email</th>
                            </tr>';

                    while ($row = pg_fetch_assoc($result)) {
                        echo '<tr>
                                <td>' . htmlspecialchars($row["nome"]) . '</td>
                                <td>' . htmlspecialchars($row["apelido"]) . '</td>
                                <td>' . htmlspecialchars($row["user_name"]) . '</td>
                                <td>' . htmlspecialchars($row["email"]) . '</td>
                              </tr>';
                    }
                    echo '</table>';
                } else {
                    echo "Nenhum dado encontrado!";
                }
                ?>
                <a href="alterar_Info.php" class="editarDados">Editar dados do utilizador</a> 
                
                <div class="consulta-container">
                    <h2>Marcar Consulta</h2>
                    <form action="" method="post">
                        <label for="data_consulta">Data da Consulta:</label>
                        <input type="date" id="data_consulta" name="data_consulta" required>

                        <label for="hora_consulta">Hora da Consulta:</label>
                        <input type="time" id="hora_consulta" name="hora_consulta" required>

                        <input type="submit" name="marcar_consulta" value="Marcar Consulta">
                    </form>
                </div>
                 
                <?php
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['marcar_consulta'])) {
                    $user_id = $_SESSION['id'];
                    $data = htmlspecialchars($_POST['data_consulta']);
                    $hora = htmlspecialchars($_POST['hora_consulta']);

                    // Formata a hora para o padrão HH:MM:SS
                    if (preg_match('/^\d{2}:\d{2}$/', $hora)) {
                        $hora .= ':00';
                    } elseif (!preg_match('/^\d{2}:\d{2}:\d{2}$/', $hora)) {
                        echo "Hora inválida!";
                        exit;
                    }

                    // Verifica se a data e hora da consulta são no futuro
                    $datahora_consulta = new DateTime($data . ' ' . $hora);
                    $agora = new DateTime();

                    if ($datahora_consulta < $agora) {
                        echo "Não é possível marcar uma consulta para uma data e hora no passado!";
                        exit();
                    }

                    // Insere a consulta no PostgreSQL
                    $query = "INSERT INTO consultas (id, data_consulta, hora_consulta) VALUES ($1, $2, $3)";
                    $result = pg_query_params($conn, $query, array($user_id, $data, $hora));

                    if ($result) {
                        header("Location: pag_utilizador.php");
                        exit();
                    } else {
                        echo "Erro ao marcar a consulta: " . pg_last_error($conn);
                    }
                }
                ?>
                
                <div class="consulta-lista">
                    <h2>Consultas Marcadas</h2>
                    <table class="consultaTable">
                        <tr>
                            <th>Data</th>
                            <th>Hora</th>
                            <th>Opções</th>
                        </tr>
                        <?php
                        // Busca as consultas do utilizador no PostgreSQL
                        $query = "SELECT id_consulta, data_consulta, hora_consulta FROM consultas WHERE id = $1 ORDER BY data_consulta ASC, hora_consulta ASC";
                        $result = pg_query_params($conn, $query, array($user_id));

                        if ($result && pg_num_rows($result) > 0) {
                            while ($row = pg_fetch_assoc($result)) {
                                $id_consulta = htmlspecialchars($row["id_consulta"]);
                                $data_consulta = htmlspecialchars($row["data_consulta"]);
                                $hora_consulta = htmlspecialchars($row["hora_consulta"]);
                                $hora_consulta = date('H:i:s', strtotime($hora_consulta));

                                echo '<tr>
                                        <td>' . $data_consulta . '</td>
                                        <td>' . $hora_consulta . '</td>
                                        <td><a href="editar_consultas_utilizador.php?id_consulta=' . $id_consulta . '">Editar</a> / <a href="excluir_consultas_utilizador.php?id_consulta=' . $id_consulta . '">Excluir</a></td>
                                      </tr>';
                            }
                        } else {
                            echo '<tr><td colspan="3">Nenhuma consulta marcada.</td></tr>';
                        }
                        ?>
                    </table>
                </div>
                <div class="projetos-container">
                    <h2>Projetos:</h2>
                    <?php
                    // Busca os projetos no PostgreSQL
                    $query = "SELECT * FROM projetos";
                    $result = pg_query($conn, $query);

                    if ($result && pg_num_rows($result) > 0) {
                        echo '<table class="projetoTable">
                                <tr>
                                    <th>Id Projeto</th>
                                    <th>Nome do Projeto</th>
                                    <th>Descrição</th>
                                    <th>Data de Início</th>
                                    <th>Data de Término</th>
                                    <th>Imagem</th>
                                    <th>Opções</th>
                                </tr>';

                        while ($row = pg_fetch_assoc($result)) {
                            echo '<tr>
                                    <td>' . htmlspecialchars($row['id_projeto']) . '</td>
                                    <td>' . htmlspecialchars($row['nome_projeto']) . '</td>
                                    <td style="overflow: auto;">' . htmlspecialchars($row['descricao']) . '</td>
                                    <td>' . htmlspecialchars($row['data_inicio']) . '</td>
                                    <td>' . htmlspecialchars($row['data_termino']) . '</td>
                                    <td><img src="' . htmlspecialchars($row['imagem']) . '" alt="Imagem do Projeto" width="50" height="50"></td>
                                    <td><a href="editar_projeto_utilizador.php?id_projeto=' . htmlspecialchars($row['id_projeto']) . '">Editar</a> / <a href="excluir_projetos_utilizador.php?id_projeto=' . htmlspecialchars($row['id_projeto']) . '">Excluir</a></td>
                                  </tr>';
                        }
                        echo '</table>';
                    } else {
                        echo 'Nenhum dado encontrado!';
                    }
                    ?>
                    <a href="criar_projeto_utilizador.php" class="voltar">Criar Projecto</a>
                </div>
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
pg_close($conn); // Fecha a conexão com o PostgreSQL
?>