<?php
require_once 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] === 'POST') {
  // Mengambil data dari form dan menggunakan prepared statements
  $username = $_POST['username'];
  $password = $_POST['password'];
  $nama = $_POST['nama'];
  $nik = $_POST['nik'];
  $role = $_POST['role'];

  $hashed_password = password_hash($password, PASSWORD_DEFAULT);

  // Menggunakan prepared statement untuk menghindari SQL Injection
  $stmt = $conn->prepare("INSERT INTO users (username, password, nama, role,nik) VALUES (?, ?, ?, ?, ?)");
  $stmt->bind_param("sssss", $username, $hashed_password, $nama, $role, $nik);

  if ($stmt->execute()) {
    $stmt_al = $conn->prepare("INSERT INTO alternatives (name) VALUES (?) ");
    $stmt_al->bind_param("s", $nama);
    if ($stmt_al->execute()) {
      echo "<script>
                alert('User berhasil ditambahkan.');
                window.location.href = '../view/dashboard.php';
              </script>";
    } else {
      echo "<script>
                alert('Error: " . addslashes($stmt->error) . "');
              </script>";
    }
  } else {
    echo "<script>
                alert('Error: " . addslashes($stmt->error) . "');
              </script>";
  }

  $stmt->close();
}
