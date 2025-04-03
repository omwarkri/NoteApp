<?php
$host = "127.0.0.1";  // Change from "localhost" to "127.0.0.1"
$user = "root";      
$pass = "Radhakrushn@1234"; // Replace with your actual password
$dbname = "notes_app"; 

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
   
}
?>
