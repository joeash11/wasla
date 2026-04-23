<?php
$conn = new mysqli('localhost', 'root', '', 'wasla_db');
$res = $conn->query("SELECT cv_path FROM users LIMIT 1");
if ($res) {
    echo "exists\n";
} else {
    echo "does not exist: " . $conn->error . "\n";
    $conn->query("ALTER TABLE users ADD COLUMN cv_path VARCHAR(255) DEFAULT NULL;");
}
?>
