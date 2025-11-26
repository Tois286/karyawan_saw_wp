<?php
require '../view/alternatives.php';

// Proses tambah alternatif
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['mode']) && $_GET['mode'] === 'add') {
    $name = $_POST['name'];
    $values = $_POST['value'] ?? []; // Ambil semua nilai dalam bentuk array

    // Menyimpan data alternatif
    $result = createAlternative($name, $values);
    if ($result) {
        header('Location: ../view/dashboard.php?show=nilai');
        exit;
    } else {
        echo "Gagal menambahkan alternatif.";
    }
}

// Proses update alternatif
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['mode']) && $_GET['mode'] === 'update') {
    $id = $_GET['id'];
    $name = $_POST['name'];
    $values = $_POST['value'] ?? [];

    // Memperbarui nama alternatif
    $stmt = $connection->prepare("UPDATE alternatives SET name=? WHERE id=?");
    $stmt->bind_param("si", $name, $id); // 's' untuk string, 'i' untuk integer
    $stmt->execute();

    // Mendefinisikan kolom secara otomatis dari value2 hingga value8
    $columns = array_map(function ($num) {
        return 'value' . $num;
    }, range(1, 20));

    // Memperbarui nilai kolom value2 hingga value8
    foreach ($columns as $index => $column) {
        if (isset($values[$index])) { // Pastikan ada nilai untuk kolom tersebut
            $stmt = $connection->prepare("UPDATE alternatives SET $column=? WHERE id=?");
            $stmt->bind_param("di", $values[$index], $id); // 'd' untuk double (float), 'i' untuk integer
            $stmt->execute();
        }
    }

    header('Location: ../view/dashboard.php?show=nilai');
    exit;
}

// Proses tambah alternatif (tanpa mode)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_GET['mode'])) {
    $name = $_POST['name'];
    $values = $_POST['value'] ?? [];

    // Menyimpan data alternatif
    $result = createAlternative($name, $values);
    if ($result) {
        header('Location: ../view/dashboard.php?show=nilai');
        exit;
    } else {
        echo "Gagal menambahkan alternatif.";
    }
}
