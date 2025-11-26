<?php
require_once '../view/alternatives.php';

$id = $_GET['id'];

$result = deleteAlternative($id);
if ($result) {
    // echo "Alternatif berhasil dihapus.";
    header('Location: ../view/dashboard.php?show=nilai');
    exit; // Pastikan skrip berhenti setelah header
} else {
    echo "Gagal menghapus alternatif.";
}
?>
<br>