<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = getenv('MYSQLHOST') ?: 'mysql.railway.internal';
$port = getenv('MYSQLPORT') ?: '3306';
$user = getenv('MYSQLUSER') ?: 'root';
$pass = getenv('MYSQLPASSWORD') ?: 'gRmcnMLLwdzFsLCBaAbIBhmsyMbzCa';
$db   = getenv('MYSQLDATABASE') ?: 'railway';

$conn = mysqli_connect($host, $user, $pass, $db, $port);
// Jika Railway menyediakan URL koneksi gabungan
$database_url = getenv('DATABASE_URL') ?: getenv('MYSQL_URL');
if ($database_url) {
    $parsed_url = parse_url($database_url);
    $host = $parsed_url['host'] ?? $host;
    $port = $parsed_url['port'] ?? $port;
    $user = $parsed_url['user'] ?? $user;
    $pass = $parsed_url['pass'] ?? $pass;
    $db   = ltrim($parsed_url['path'] ?? '', '/');
}

// Eksekusi koneksi MySQLi
$conn = mysqli_connect($host, $user, $pass, $db, $port);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
?>