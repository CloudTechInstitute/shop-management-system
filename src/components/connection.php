<?php
$conn = mysqli_connect('localhost', 'root', '', 'inventory');
if (!$conn) {
    die("Could not connect to database" . $conn->connect_error);
}