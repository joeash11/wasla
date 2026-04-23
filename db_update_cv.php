<?php
$conn = new mysqli('localhost', 'root', '', 'wasla_db');
$conn->query("ALTER TABLE users ADD COLUMN cv_path VARCHAR(255) DEFAULT NULL;");
?>
