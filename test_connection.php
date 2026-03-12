<?php
require("config/database.php");

$stmt = $pdo->query("SELECT DATABASE() as db");
$row = $stmt->fetch();
echo "Connexion réussie à la base : " . $row['db'];
?>

