<?php
require_once '../view/criteria.php';

$id = $_GET['id'];

$result = deleteCriteria($id);
if ($result) {
    header('Location: ../view/dashboard.php?show=nilai');
    exit; // Pastikan skrip berhenti setelah header
} else {
    echo "Gagal menghapus kriteria.";
}
