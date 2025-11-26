<?php
session_start();

include '../console/header.php';

?>


<?php
$show = $_GET['show'] ?? 'home';
?>

<script>
    document.addEventListener("DOMContentLoaded", function() {

        // Sembunyikan semua section
        const sections = ["home", "profile", "nilai", "rangking", "usersAdd", "rankSet", "profileEdit", "cabang"];
        sections.forEach(id => document.getElementById(id).style.display = "none");

        // Tampilkan section sesuai parameter GET
        const show = "<?php echo $show; ?>";
        const section = document.getElementById(show);
        if (section) {
            section.style.display = "block";
            section.scrollIntoView({
                behavior: "smooth"
            });
        }
    });
</script>
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
    #rangking,
    #usersAdd,
    #rankSet,
    #profileEdit,
    #cabang {
        display: none;
    }
</style>

<div id="home">
    <div class="container text-center mt-4">
        <div class="welcome-card">
            <img src="../img/DKI.png" width="200px" alt="UNPAM">
            <h3 class="mt-2">Halo, Selamat Datang!</h3>
            <p class="lead">Sistem Penunjang Keputusan Penilaian Kinerja Karyawan Berbasis Website</p>
            <p><strong>Dengan Visualisasi Data Menggunakan Metode</strong></p>
            <h4 class="font-weight-bold">SAW</h4>
        </div>
    </div>
    <?php include '../console/footer.php' ?>
</div>
<!-- Menampilkan Hasil Pencarian -->
<div id="profile">
    <?php include '../view/profile.php'; ?>
    <?php include '../console/footer.php' ?>
</div>
<div id="nilai">
    <?php include '../view/alternatif.php'; ?>
    <br>
    <?php if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'superAdmin'): ?>
        <?php include '../view/kriteria.php'; ?>
    <?php endif; ?>
    <?php include '../console/footer.php' ?>
</div>
<div id="rangking">
    <?php require_once '../view/hasil.php'; ?>
    <?php include '../console/footer.php' ?>
</div>

<div id="profileEdit">
    <?php include '../view/profileEdit.php'; ?>
    <?php include '../console/footer.php' ?>
</div>

<div id="usersAdd">
    <?php include '../view/usersAdd.php'; ?>
    <?php include '../console/footer.php' ?>
</div>

<div id="rankSet">
    <?php include '../view/rankSet.php'; ?>
    <?php include '../console/footer.php' ?>
</div>

<div id="cabang">
    <?php include '../view/cabang.php'; ?>
</div>