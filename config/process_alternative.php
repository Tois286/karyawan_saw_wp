<?php
require '../view/alternatives.php';

// Proses tambah alternatif
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['mode']) && $_GET['mode'] === 'add') {
    $name = $_POST['name'];
    $values = array(
        $_POST['value1'],
        $_POST['value2'],
        $_POST['value3'],
        $_POST['value4'],
        $_POST['value5']
    );

    // Menyimpan data alternatif
    $result = createAlternative($name, $values);
    if ($result) {
        // echo "Alternatif berhasil ditambahkan.";
        header('Location: ../view/dashboard.php');
    } else {
        echo "Gagal menambahkan alternatif.";
    }
}

// Proses update alternatif
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['mode']) && $_GET['mode'] === 'update') {
    $id = $_GET['id'];
    $name = $_POST['name'];
    $values = array(
        $_POST['value1'],
        $_POST['value2'],
        $_POST['value3'],
        $_POST['value4'],
        $_POST['value5']
    );

    // Mengupdate data alternatif
    $result = updateAlternative($id, $name, $values);
    if ($result) {
        header('Location: ../view/dashboard.php');
        exit; // Pastikan skrip berhenti setelah header
    } else {
        echo "Gagal mengupdate alternatif.";
    }
}

// Proses tambah alternatif (tanpa mode)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_GET['mode'])) {
    $name = $_POST['name'];
    $values = array(
        $_POST['value1'],
        $_POST['value2'],
        $_POST['value3'],
        $_POST['value4'],
        $_POST['value5']
    );

    // Menyimpan data alternatif
    $result = createAlternative($name, $values);
    if ($result) {
        // echo "Alternatif berhasil ditambahkan.";
        header('Location: ../view/dashboard.php');
        exit; // Pastikan skrip berhenti setelah header
    } else {
        echo "Gagal menambahkan alternatif.";
    }
}
