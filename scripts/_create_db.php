<?php

$host = getenv('DB_HOST') ?: '127.0.0.1';
$port = getenv('DB_PORT') ?: '3306';
$user = getenv('DB_USERNAME') ?: 'root';
$pass = getenv('DB_PASSWORD') ?: '';
$name = getenv('DB_DATABASE') ?: 'db_warkop_kayu';

require dirname(__DIR__) . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->safeLoad();

$host = $_ENV['DB_HOST'] ?? $host;
$port = $_ENV['DB_PORT'] ?? $port;
$user = $_ENV['DB_USERNAME'] ?? $user;
$pass = $_ENV['DB_PASSWORD'] ?? $pass;
$name = $_ENV['DB_DATABASE'] ?? $name;

$pdo = new PDO("mysql:host={$host};port={$port}", $user, $pass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

$pdo->exec("CREATE DATABASE IF NOT EXISTS `{$name}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
echo "Database ready: {$name}\n";
