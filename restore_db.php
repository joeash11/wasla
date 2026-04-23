<?php
$conn = new mysqli('localhost', 'root', '');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 1. Drop the DB if it exists and recreate it completely
$conn->query("DROP DATABASE IF EXISTS wasla_db");
$conn->query("CREATE DATABASE wasla_db");
$conn->select_db('wasla_db');

// 2. Read the SQL file
$sql = file_get_contents(__DIR__ . '/db/wasla_db.sql');
if ($conn->multi_query($sql)) {
    do {
        // flush results
        if ($res = $conn->store_result()) {
            $res->free();
        }
    } while ($conn->more_results() && $conn->next_result());
    echo "Database restored successfully!\n";
} else {
    echo "Error executing SQL: " . $conn->error;
}
$conn->close();
?>
