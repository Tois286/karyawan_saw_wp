<?php
require_once 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] === 'POST') {
  // Mengambil data dari form dan menggunakan prepared statements
  $username = $_POST['username'];
  $password = $_POST['password'];
  $nama = $_POST['nama'];
  $role = $_POST['role'];
  $area = $_POST['area'];

  $hashed_password = password_hash($password, PASSWORD_DEFAULT);

  // Menggunakan prepared statement untuk menghindari SQL Injection
  $stmt = $conn->prepare("INSERT INTO users (username, password, nama, role, area) VALUES (?, ?, ?, ?, ?)");
  $stmt->bind_param("sssss", $username, $hashed_password, $nama, $role, $area);

  if ($stmt->execute()) {
    echo "<script>
                alert('User berhasil ditambahkan.');
                window.location.href = '../view/dashboard.php';
              </script>";
  } else {
    echo "<script>
                alert('Error: " . addslashes($stmt->error) . "');
              </script>";
  }

  $stmt->close();
}
