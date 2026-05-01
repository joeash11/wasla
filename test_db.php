<?php
require_once __DIR__ . '/db/connection.php';
$result = $conn->query("SELECT * FROM smtp_settings ORDER BY id ASC LIMIT 1");
$row = $result->fetch_assoc();
var_dump($row);
var_dump(empty($row['smtp_username']));
var_dump(empty($row['smtp_password']));
?>
