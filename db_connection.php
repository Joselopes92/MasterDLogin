<?php
$host = getenv('DB_HOST');
$dbname = getenv('DB_NAME');
$user = getenv('DB_USER');
$password = getenv('DB_PASSWORD');
$port = getenv('DB_PORT');

$conn = pg_connect("host=$host dbname=$dbname user=$user password=$password port=$port");

if (!$conn) {
    die('Conexão à base de dados falhou! ' . pg_last_error());
}
?>