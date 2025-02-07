<?php
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $sm = $_POST['sm'];
  $m = $_POST['m'];
  $b = $_POST['b'];
  $cb = $_POST['cb'];
  $ck = $_POST['ck'];
  $id_cabang = $_POST['id_cabang'];

  // Mencari area sebelum insert dilakukan
  $cari = mysqli_query($conn, "SELECT * FROM cabang WHERE id_cabang='$id_cabang'");

  if ($cari && mysqli_num_rows($cari) > 0) {
    $data = $cari->fetch_assoc();
    $area = $data['area'];

    // Insert into rangking
    $stmt = $conn->prepare("INSERT INTO rangking (area, sm, m, b, cb, ck, id_cabang) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("siiiiis", $area, $sm, $m, $b, $cb, $ck, $id_cabang);

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
}
