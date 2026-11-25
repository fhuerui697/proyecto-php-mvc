<?php
require __DIR__ . '/conexion.php';
$pdo->exec(file_get_contents(__DIR__ . '/database.sql'));
echo "Base de datos preparada.\n";
