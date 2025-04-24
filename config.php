
<?php
$host = "45.45.238.32";
$port = "3306";
$user = "u204_Jm0zuJipBX";
$password = "g=N6jW^V@p!p7Xzw00J1a@Ln";
$database = "s204_s1xs";

$conn = new mysqli($host, $user, $password, $database, $port);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
