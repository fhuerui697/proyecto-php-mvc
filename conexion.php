<?php
require __DIR__ . '/config.php';

$pdo = new PDO("mysql:host=$host;dbname=$bd;charset=utf8mb4", $user, $pass);
