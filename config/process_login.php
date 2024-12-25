<?php
session_start();

// Ganti dengan data pengguna Anda
$valid_username = "admin@admin.com";
$valid_password = "qwer"; // Ganti dengan password yang lebih aman

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($username === $valid_username && $password === $valid_password) {
        // Login berhasil
        $_SESSION['username'] = $username;
        header("Location: ../view/dashboard.php"); // Redirect ke halaman home
        exit();
    } else {
        // Login gagal
        echo "<script>
            window.history.back();
            document.getElementById('error-message').innerHTML = 'Username atau password salah!';
            document.getElementById('error-message').style.display = 'block';
        </script>";
    }
}
