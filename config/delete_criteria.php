<?php
require_once '../view/criteria.php';

$id = $_GET['id'];

$result = deleteCriteria($id);
if ($result) {
    header('Location: ../view/dashboard.php');
    exit; // Pastikan skrip berhenti setelah header
} else {
    echo "Gagal menghapus kriteria.";
}
