<?php
session_start();
include 'koneksi.php';

$username = $_SESSION['username'];

// Ambil data user lama
$getData = mysqli_query($conn, "SELECT * FROM users WHERE username='$username'");
$data = mysqli_fetch_assoc($getData);

// Jika POST (update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nik = mysqli_real_escape_string($conn, $_POST['nik']);
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $user = mysqli_real_escape_string($conn, $_POST['username']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    // Mulai query update
    $query = "UPDATE users SET 
                nik='$nik',
                nama='$nama',
                username='$user',
                role='$role'";

    // Jika password diisi, validasi dan update
    if (!empty($password) || !empty($confirm)) {

        if ($password !== $confirm) {
            echo "<script>alert('Password dan konfirmasi tidak cocok!'); history.back();</script>";
            exit;
        }

        // Encrypt password
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $query .= ", password='$hash'";
    }

    $query .= " WHERE username='$username'";

    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Data berhasil diperbarui'); window.location='../view/dashboard.php?show=profile';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui data');</script>";
    }
}
