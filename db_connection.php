<?php
// PostgreSQL connection details
$host = 'aws-0-eu-west-2.pooler.supabase.com'; // Replace with your Supabase host
$dbname = 'postgres'; // Default database name for Supabase
$user = 'postgres.cbbgrleafzjoianpiaff'; // Replace with your Supabase user
$password = 'SHq!ccn#PyYd6_4'; // Replace with your Supabase password
$port = '6543'; // Default PostgreSQL port

// Connect to PostgreSQL
$conn = pg_connect("host=$host dbname=$dbname user=$user password=$password port=$port");

if (!$conn) {
    die('Conexão à base de dados falhou! ' . pg_last_error());
}
?>