<?php
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $cabang = $_POST['area'] ?? '';
    $kota = $_POST['kota'] ?? '';
    $alamat = $_POST['alamat'] ?? '';

    $stmt = $conn->prepare("INSERT INTO cabang (area,kota,alamat) VALUES (?,?,?)");
    $stmt->bind_param("sss", $cabang, $kota, $alamat);


    if ($stmt->execute()) {
        echo "<script>
                alert('Cabang berhasil ditambahkan.');
                window.location.href = '../view/dashboard.php';
              </script>";
    } else {
        echo "<script>
                alert('Error: " . addslashes($stmt->error) . "');
              </script>";
    }

    $stmt->close();
}
