<?php
session_start();
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
    <?php 
    if (isset($_SESSION['username'])) {
        echo '<h1>Bem-vindo/a ' . htmlspecialchars($_SESSION['username']) . '!</h1>'; 
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
            <div class="table-container">
                <h2>Dados dos Utilizadores:</h2>
                <?php
                include 'db_connection.php'; // Certifique-se de que este arquivo configura a conexão com o PostgreSQL

                // Busca os dados dos utilizadores no PostgreSQL
                $query = "SELECT * FROM utilizadores";
                $result = pg_query($conn, $query);

                if ($result && pg_num_rows($result) > 0) {
                    echo '<table class="userTable">
                            <tr>
                                <th>Id</th>
                                <th>Nome</th>
                                <th>Apelido</th>
                                <th>User_name</th>
                                <th>Email</th>
                                <th>Imagem</th>
                                <th>Opções</th>
                            </tr>';

                    while ($row = pg_fetch_assoc($result)) {
                        echo '<tr>
                                <td>' . htmlspecialchars($row["id"]) . '</td>
                                <td>' . htmlspecialchars($row["nome"]) . '</td>
                                <td>' . htmlspecialchars($row["apelido"]) . '</td>
                                <td>' . htmlspecialchars($row["user_name"]) . '</td>
                                <td>' . htmlspecialchars($row["email"]) . '</td>
                                <td><img src="' . htmlspecialchars($row["imagem"]) . '" alt="Imagem de Perfil" width="50" height="50"></td>
                                <td><a href="excluir_dados_admin.php?id=' . htmlspecialchars($row['id']) . '">Excluir</a> / <a href="editar_dados_admin.php?id=' . htmlspecialchars($row['id']) . '">Editar</a></td>
                              </tr>';
                    }
                    echo '</table>';
                } else {
                    echo "Nenhum dado encontrado!";
                }
                ?>

                <div class="consulta-container">
                    <h2>Consultas Marcadas:</h2>
                    <?php
                    // Busca os dados das consultas no PostgreSQL
                    $queryConsultas = "SELECT * FROM consultas";
                    $resultConsultas = pg_query($conn, $queryConsultas);

                    if ($resultConsultas && pg_num_rows($resultConsultas) > 0) {
                        echo '<table class="consultaTable">
                                <tr>
                                    <th>Id Consulta</th>
                                    <th>Id do Usuário</th>
                                    <th>Data Consulta</th>
                                    <th>Hora Consulta</th>
                                    <th>Opções</th>
                                </tr>';

                        while ($row = pg_fetch_assoc($resultConsultas)) {
                            echo '<tr>
                                    <td>' . htmlspecialchars($row['id_consulta']) . '</td>
                                    <td>' . htmlspecialchars($row['id']) . '</td>
                                    <td>' . htmlspecialchars($row['data_consulta']) . '</td>
                                    <td>' . htmlspecialchars($row['hora_consulta']) . '</td>
                                    <td><a href="editar_consultas_admin.php?id_consulta=' . htmlspecialchars($row['id_consulta']) . '">Editar</a> / <a href="excluir_consultas_admin.php?id_consulta=' . htmlspecialchars($row['id_consulta']) . '">Excluir</a></td>
                                  </tr>';
                        }
                        echo '</table>';
                    } else {
                        echo 'Nenhum dado encontrado!';
                    }
                    ?>
                </div>
                
                <div class="projetos-container">
                    <h2>Projetos:</h2>
                    <?php
                    // Busca os dados dos projetos no PostgreSQL
                    $queryProjetos = "SELECT * FROM projetos";
                    $resultProjetos = pg_query($conn, $queryProjetos);

                    if ($resultProjetos && pg_num_rows($resultProjetos) > 0) {
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

                        while ($row = pg_fetch_assoc($resultProjetos)) {
                            echo '<tr>
                                    <td>' . htmlspecialchars($row['id_projeto']) . '</td>
                                    <td>' . htmlspecialchars($row['nome_projeto']) . '</td>
                                    <td style="overflow: auto;">' . htmlspecialchars($row['descricao']) . '</td>
                                    <td>' . htmlspecialchars($row['data_inicio']) . '</td>
                                    <td>' . htmlspecialchars($row['data_termino']) . '</td>
                                    <td><img src="' . htmlspecialchars($row['imagem']) . '" alt="Imagem do Projeto" width="50" height="50"></td>
                                    <td><a href="editar_projetos_admin.php?id_projeto=' . htmlspecialchars($row['id_projeto']) . '">Editar</a> / <a href="excluir_projetos_admin.php?id_projeto=' . htmlspecialchars($row['id_projeto']) . '">Excluir</a></td>
                                  </tr>';
                        }
                        echo '</table>';
                    } else {
                        echo 'Nenhum dado encontrado!';
                    }
                    ?>
                    <a href="criar_projecto_admin.php" class="voltar">Criar Projecto</a>
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