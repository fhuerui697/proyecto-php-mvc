<?php
require __DIR__ . '/conexion.php';
$total = $pdo->query('SELECT COUNT(*) FROM productos')->fetchColumn();
?>
<h1>¡Despliegue automático funcionando: Feder SIIIIIIIIIIII</h1>
<p>Conexión con MySQL correcta.</p>
<p>Productos registrados: <?= $total ?></p>
