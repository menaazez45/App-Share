<?php
$conn = new mysqli("localhost", "root", "", "app_store");

if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
} 
?>
