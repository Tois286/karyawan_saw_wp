<?php
session_start();
include '../console/header.php';
?>
<style>
    body {
        background-color: #fff;
        /* Warna latar belakang lembut */
    }

    .welcome-card {
        background-color: white;
        border-radius: 15px;
        padding: 30px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        /* Bayangan lembut */
        transition: transform 0.3s;
        /* Animasi */
    }

    .welcome-card:hover {
        transform: scale(1.05);
        /* Efek zoom saat hover */
    }

    #profile,
    #nilai,
    #rangking {
        display: none;
    }
</style>
<div class="container mt-4">
    <div id="home">

        <body>
            <div class="container text-center mt-5">
                <div class="welcome-card">
                    <img src="../img/unpam.png" width="200px" alt="UNPAM">
                    <h3 class="mt-2">Halo, Selamat Datang!</h3>
                    <p class="lead">Sistem Penunjang Keputusan Penilaian Kinerja Karyawan Berbasis Website</p>
                    <p><strong>Dengan Visualisasi Data Menggunakan Metode</strong></p>
                    <h4 class="font-weight-bold">WP & SAW</h4>
                </div>
            </div>

            <!-- Tambahkan JS Bootstrap dan jQuery -->
            <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
            <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        </body>

    </div>
    <div id="profile">
        <?php include '../view/profile.php'; ?>
    </div>
    <div id="nilai">
        <?php include '../view/alternatif.php'; ?>
        <br>
        <?php include '../view/kriteria.php'; ?>
    </div>
    <div id="rangking">
        <?php require_once '../view/hasil.php'; ?>
    </div>
</div>
<br>
<?php
include '../console/footer.php';
?>