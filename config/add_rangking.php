<?php
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $sm = $_POST['sm'];
  $m = $_POST['m'];
  $b = $_POST['b'];
  $cb = $_POST['cb'];
  $ck = $_POST['ck'];

  // Insert into rangking
  $stmt = $conn->prepare("INSERT INTO rangking ( sm, m, b, cb, ck) VALUES ( ?, ?, ?, ?, ?)");
  $stmt->bind_param("iiiii", $sm, $m, $b, $cb, $ck);

  if ($stmt->execute()) {
    echo "<script>
                    alert('Ranking berhasil ditambahkan.');
                    window.location.href = '../view/dashboard.php';
                  </script>";
  } else {
    echo "<script>
                    alert('Error: " . addslashes($stmt->error) . "');
                  </script>";
  }

  $stmt->close();
} else {
  echo "<script>
                alert('Cabang tidak ditemukan.');
              </script>";
}
