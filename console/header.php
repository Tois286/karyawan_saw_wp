<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>METODE WP & SAW</title>
    <!-- CSS Bootstrap dan FontAwesome -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2qqe1Yl1/5lik9S6YxM7R9jwF3aP4b4xlpF1TzYbbgN5SKLog=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <link rel="stylesheet" href="../asset/css/header.css">
    <link rel="stylesheet" href="../asset/css/table.css">
</head>
<style>
    img {
        width: 150px;
        background-color: white;
        border-radius: 20px;
        padding: 5px;
        padding-right: 10px;
        padding-left: 10px;
    }

    .sidebar {
        height: 100%;
        /* Full-height */
        width: 220px;
        /* Lebar sidebar */
        position: fixed;
        /* Tetap di tempat */
        right: 0;
        /* Posisikan di sisi kanan */
        top: 0;
        /* Posisikan di atas */
        background-color: #e51b24;
        /* Warna latar belakang */
        box-shadow: -2px 0 5px rgba(0, 0, 0, 0.1);
        /* Bayangan */
        padding: 15px;
        /* Padding */
        display: none;
        /* Sembunyikan secara default */
        z-index: 9999;
    }

    .sidebar-header {
        display: flex;
        /* Menggunakan flexbox */
        align-items: center;
        /* Vertikal center */
        justify-content: space-between;
        /* Ruang antara elemen */
    }

    .sidebar .nav-link {
        display: block;
        /* Buat link menjadi blok */
        padding: 10px;
        /* Padding dalam link */
        color: #333;
        /* Warna teks */
        text-decoration: none;
        /* Hapus garis bawah */
    }

    .sidebar-header h4 {
        margin: 0;
        /* Menghapus margin default */
    }

    .close-btn {
        background: none;
        border: none;
        font-size: 24px;
        cursor: pointer;
        color: #fff;
        /* Warna tombol */
        margin-right: 10px;
        /* Jarak kanan */
    }

    .close-btn:hover {
        color: #ffc107;
    }
</style>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="#" onclick="showContent('home')"><img src="../img/BANK DKI.png" alt="dki"></a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <form method="POST" action="../view/dashboard.php">
                    <div class="input-group ml-4">
                        <input type="text" class="form-control" placeholder="Search..." name="search">
                        <div class="input-group-append">
                            <button class="btn btn-warning" type="submit"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                </form>
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#" onclick="showContent('home')"><i class="fas fa-home"></i> Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="showContent('nilai')"><i class="fas fa-calculator"></i> Penilaian</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="showContent('rangking')"><i class="fas fa-trophy"></i> Rangking</a>
                    </li>
                    <a class="nav-link" href="#" onclick="toggleSidebar()"><i class="fas fa-cogs"></i> Settings</a>
                    <div class="sidebar" id="mySidebar">
                        <div class="sidebar-header">
                            <button class="close-btn" onclick="toggleSidebar()">
                                <i class="fas fa-bars"></i> <!-- Menggunakan ikon garis tiga -->
                            </button>
                            <h4><i class="fas fa-user"></i> <?php echo $_SESSION['username'] ?></h4>
                        </div>
                        <a class="nav-link" href="#" onclick="showContent('profile')"><i class="fas fa-user"></i> Profile</a>
                        <a class="nav-link" href="#" onclick="showContent('profileEdit')"><i class="fas fa-edit"></i> Edit Profile</a>
                        <?php if ($_SESSION['role'] === 'superAdmin'): ?>
                            <a class="nav-link" href="#" onclick="showContent('usersAdd')"><i class="fas fa-user-plus"></i> Add Users</a>
                            <a class="nav-link" href="#" onclick="showContent('rankSet')"><i class="fas fa-plus-square"></i> Add Rank</a>
                        <?php elseif ($_SESSION['role'] === 'superAdmin' || $_SESSION['role'] === 'admin'): ?>
                            <a class="nav-link" href="#" onclick="showContent('usersAdd')"><i class="fas fa-user-plus"></i> Add Users</a>
                            <!-- <a class="nav-link" href="#" onclick="showContent('cabang')"><i class="fas fa-building"></i> Add Cabang</a> -->
                        <?php endif; ?>
                        <a class="nav-link" href="#" onclick="confirmLogout(event)"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </div>
                </ul>

            </div>
        </div>
    </nav>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>



    <script>
        function confirmLogout(event) {
            event.preventDefault(); // Mencegah aksi default link
            if (confirm("Apakah Anda yakin ingin logout?")) {
                window.location.href = "../config/logout.php"; // Arahkan ke halaman logout jika pengguna mengkonfirmasi
            }
        }

        function toggleSidebar() {
            var sidebar = document.getElementById("mySidebar");
            if (sidebar.style.display === "block") {
                sidebar.style.display = "none"; // Menyembunyikan sidebar
            } else {
                sidebar.style.display = "block"; // Menampilkan sidebar
            }
        }

        function toggleSidebar() {
            var sidebar = document.getElementById("mySidebar");
            if (sidebar.style.display === "block") {
                sidebar.style.display = "none"; // Menyembunyikan sidebar
            } else {
                sidebar.style.display = "block"; // Menampilkan sidebar
            }
        }


        function showContent(content) {
            // Sembunyikan semua konten
            document.getElementById('home').style.display = 'none';
            document.getElementById('profile').style.display = 'none';
            document.getElementById('profileEdit').style.display = 'none';
            document.getElementById('nilai').style.display = 'none';
            document.getElementById('rangking').style.display = 'none';
            document.getElementById('rankSet').style.display = 'none';
            document.getElementById('usersAdd').style.display = 'none';
            document.getElementById('cabang').style.display = 'none';

            // Hapus kelas 'active' dari semua nav-link
            var navLinks = document.querySelectorAll('.nav-link');
            navLinks.forEach(function(link) {
                link.classList.remove('active');
            });

            // Tampilkan konten yang sesuai
            if (content === 'home') {
                document.getElementById('home').style.display = 'block';
                document.querySelector('a[href="#"][onclick="showContent(\'home\')"]').classList.add('active');
            } else if (content === 'profile') {
                document.getElementById('profile').style.display = 'block';
                document.querySelector('a[href="#"][onclick="showContent(\'profile\')"]').classList.add('active');
            } else if (content === 'profileEdit') {
                document.getElementById('profileEdit').style.display = 'block';
                document.querySelector('a[href="#"][onclick="showContent(\'profileEdit\')"]').classList.add('active');
            } else if (content === 'nilai') {
                document.getElementById('nilai').style.display = 'block';
                document.querySelector('a[href="#"][onclick="showContent(\'nilai\')"]').classList.add('active');
            } else if (content === 'rangking') {
                document.getElementById('rangking').style.display = 'block';
                document.querySelector('a[href="#"][onclick="showContent(\'rangking\')"]').classList.add('active');
            } else if (content === 'usersAdd') {
                document.getElementById('usersAdd').style.display = 'block';
                document.querySelector('a[href="#"][onclick="showContent(\'usersAdd\')"]').classList.add('active');
            } else if (content === 'rankSet') {
                document.getElementById('rankSet').style.display = 'block';
                document.querySelector('a[href="#"][onclick="showContent(\'rankSet\')"]').classList.add('active');
            } else if (content === 'cabang') {
                document.getElementById('cabang').style.display = 'block';
                document.querySelector('a[href="#"][onclick="showContent(\'cabang\')"]').classList.add('active');
            }
        }

        // Tampilkan home secara default saat halaman dimuat
        showContent('home');
    </script>