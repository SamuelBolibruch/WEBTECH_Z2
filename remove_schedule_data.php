<?php

require_once 'config.php';

$sql = "DELETE FROM schedule";
$stmt = $pdo->prepare($sql);
$stmt->execute();

header("Location: index.php");