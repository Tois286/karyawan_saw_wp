<?php
// Koneksi ke database
require_once 'koneksi.php';

// Pastikan data diterima
if (isset($_POST['data'])) {
    $data = json_decode($_POST['data'], true);

    if (!$data) {
        echo "<script>alert('Invalid data received.'); window.location.href = '../view/dashboard.php';</script>";
        exit;
    }

    // Variabel untuk pesan
    $message = []; // Ubah menjadi array untuk menyimpan beberapa pesan

    // Loop data dan proses setiap baris
    foreach ($data as $row) {
        // Ambil data dari JSON
        $id = $row['id'] ?? null;
        $wpRank = isset($row['wpRank']) ? (int) $row['wpRank'] : null;
        $name = $row['name'] ?? null;
        $sValue = isset($row['sValue']) ? (float) $row['sValue'] : null;
        $vValue = isset($row['vValue']) ? (float) $row['vValue'] : null;
        $finalValue = isset($row['finalValue']) ? (float) $row['finalValue'] : null;

        // Cek apakah ID ada
        if ($id === null) {
            $message[] = "ID tidak valid.";
            continue;
        }

        // Cek apakah data dengan ID sudah ada di database
        $checkQuery = "SELECT id FROM hasil WHERE id = ?";
        $checkStmt = $conn->prepare($checkQuery);
        $checkStmt->bind_param('i', $id);
        $checkStmt->execute();
        $checkStmt->store_result();

        if ($checkStmt->num_rows > 0) {
            // Data sudah ada, lakukan UPDATE
            $updateQuery = "UPDATE hasil SET `rank` = ?, name = ?, s_value = ?, v_value = ?, final_value = ? WHERE id = ?";
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->bind_param('issddi', $wpRank, $name, $sValue, $vValue, $finalValue, $id);

            if (!$updateStmt->execute()) {
                $message[] = "Failed to update data for ID $id: " . $updateStmt->error;
            } else {
                $message[] = "Data berhasil diperbarui untuk ID $id!";
            }
            $updateStmt->close();
        } else {
            // Data belum ada, lakukan INSERT
            $insertQuery = "INSERT INTO hasil (id, `rank`, name, s_value, v_value, final_value) VALUES (?, ?, ?, ?, ?, ?)";
            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->bind_param('iisddd', $id, $wpRank, $name, $sValue, $vValue, $finalValue);

            if (!$insertStmt->execute()) {
                $message[] = "Failed to insert data for ID $id: " . $insertStmt->error;
            } else {
                $message[] = "Data berhasil ditambahkan untuk ID $id!";
            }
            $insertStmt->close();
        }
        $checkStmt->close();
    }

    // Tutup koneksi database
    $conn->close();

    // Gabungkan pesan menjadi satu string
    $messageString = implode('<br>', $message);

    // Tampilkan pesan hasil operasi dan alihkan ke ../view/dashboard.php
    echo "<script>alert('$messageString'); window.location.href = '../view/dashboard.php';</script>";
} else {
    echo "<script>alert('No data received.'); window.location.href = '../view/dashboard.php';</script>";
}
