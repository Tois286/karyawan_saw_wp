<?php
include 'koneksi.php';

if (!isset($_GET['id'])) {
    echo "<script>alert('ID tidak ditemukan!'); window.location='../view/users.php';</script>";
    exit;
}

$id = intval($_GET['id']); // aman dari injection

// Cek apakah user ada
$cek = mysqli_query($conn, "SELECT * FROM users WHERE id_users = '$id'");
if (mysqli_num_rows($cek) == 0) {
    echo "<script>alert('User tidak ditemukan!'); window.location='../view/users.php';</script>";
    exit;
}

// Hapus user
$delete = mysqli_query($conn, "DELETE FROM users WHERE id_users = '$id'");

if ($delete) {
    echo "<script>alert('User berhasil dihapus!'); window.location='../view/dashboard.php?show=usersAdd';</script>";
} else {
    echo "<script>alert('Gagal menghapus data!'); window.location='../view/dashboard.php?show=usersAdd';</script>";
}
