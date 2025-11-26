<?php
session_start();
require_once 'koneksi.php'; // Pastikan koneksi database sudah dimasukkan

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validasi input
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if (empty($username) || empty($password)) {
        $_SESSION['error'] = "Username dan password harus diisi!";
        header("Location: ../index.php");
        exit();
    }

    // Ambil data user dari database termasuk role
    $stmt = $conn->prepare("SELECT username, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Debug untuk melihat data pengguna
        // var_dump($user); // Uncomment untuk debugging

        // Verifikasi password
        if (password_verify($password, $user['password'])) {
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $user['role']; // Simpan role dalam session

            // Redirect berdasarkan role
            if ($user['role'] === 'admin' || $user['role'] === 'superAdmin') {
                header("Location: ../view/dashboard.php");
            } else if ($user['role'] === 'karyawan') {
                header("Location: ../view/dashboard.php");
            } else {
                $_SESSION['error'] = "Role tidak dikenal!";
                header("Location: ../index.php");
            }

            exit(); // Pastikan untuk keluar setelah redirect
        }
    }

    // Jika login gagal
    $_SESSION['error'] = "Username atau password salah!";
    header("Location: ../index.php");
    exit();
}
