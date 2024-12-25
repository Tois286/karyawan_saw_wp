<?php
// Koneksi ke database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sawdanwp";

$conn = new mysqli($servername, $username, $password, $dbname);

require_once '../view/criteria.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_GET['id'])) {
        // Proses update kriteria
        $id = $_GET['id'];
        $name = $_POST['name'];
        $weight = $_POST['weight'];
        $type = $_POST['type'];

        $result = updateCriteria($id, $name, $weight, $type);
        if ($result) {
            header('Location: ../view/dashboard.php');
            exit; // Pastikan skrip berhenti setelah header
        } else {
            echo "Gagal mengupdate kriteria.";
        }
    } else {
        // Proses tambah kriteria
        $name = $_POST['name'];
        $weight = $_POST['weight'];
        $type = $_POST['type'];

        $result = createCriteria($name, $weight, $type);
        if ($result) {
            header('Location: ../view/dashboard.php');
            exit; // Pastikan skrip berhenti setelah header
        } else {
            echo "Gagal menambahkan kriteria.";
        }
    }
}
