<?php
$host = 'localhost';
$db   = 'apex_system';
$user = 'root';
$pass = 'root'; // MAMP má heslo 'root'
$port = '3306'; // MAMP MySQL port

$charset = 'utf8mb4';

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    try {
        $dsn_backup = "mysql:host=$host;dbname=$db;charset=$charset";
        $pdo = new PDO($dsn_backup, $user, $pass, $options);
    } catch (\PDOException $e2) {
        die("Chyba připojení k databázi: " . $e2->getMessage());
    }
}
?>