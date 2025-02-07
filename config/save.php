<?php
// Koneksi ke database
require_once 'koneksi.php';

// Pastikan data diterima
if (isset($_POST['data'])) {
    $data = json_decode($_POST['data'], true);

    if (!$data) {
        echo "Invalid data received.";
        exit;
    }

    // Persiapkan query untuk menyimpan data
    // Menggunakan backtick pada kolom 'rank' untuk menghindari konflik dengan kata kunci SQL
    $query = "INSERT INTO hasil (id, `rank`, name, s_value, v_value, final_value) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        die("Database error: " . $conn->error);
    }

    // Loop data dan masukkan ke database
    foreach ($data as $row) {
        // Pastikan data tidak null dan valid sebelum melakukan bind
        $id = $row['id'] ?? null;
        $wpRank = isset($row['wpRank']) ? (int) $row['wpRank'] : null;
        $name = $row['name'] ?? null;
        $sValue = isset($row['sValue']) ? (float) $row['sValue'] : null;
        $vValue = isset($row['vValue']) ? (float) $row['vValue'] : null;
        $finalValue = isset($row['finalValue']) ? (float) $row['finalValue'] : null;

        // Bind parameters dengan tipe yang sesuai
        $stmt->bind_param(
            'iisddd', // 'i' untuk integer, 's' untuk string, 'd' untuk double
            $id,
            $wpRank,
            $name,
            $sValue,
            $vValue,
            $finalValue
        );

        // Eksekusi query
        if (!$stmt->execute()) {
            die("Failed to save data: " . $stmt->error);
        }
    }

    // Tutup statement dan koneksi
    $stmt->close();
    $conn->close();
    header("location: ../console/print.php");
} else {
    echo "No data received.";
}
