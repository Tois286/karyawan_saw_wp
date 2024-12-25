<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>METODE WP & SAW</title>
    <!-- CSS Bootstrap dan FontAwesome -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="../asset/css/header.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="home.php">METODE WP & SAW</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
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
                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="showContent('profile')"><i class="fas fa-user"></i> My Profile</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../config/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </li>
                </ul>
                <div class="input-group ml-3">
                    <input type="text" class="form-control" placeholder="Search...">
                    <div class="input-group-append">
                        <button class="btn btn-warning" type="button"><i class="fas fa-search"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>


<script>
    function showContent(content) {
        // Sembunyikan semua konten
        document.getElementById('home').style.display = 'none';
        document.getElementById('profile').style.display = 'none';
        document.getElementById('nilai').style.display = 'none';
        document.getElementById('rangking').style.display = 'none';

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
        } else if (content === 'nilai') {
            document.getElementById('nilai').style.display = 'block';
            document.querySelector('a[href="#"][onclick="showContent(\'nilai\')"]').classList.add('active');
        } else if (content === 'rangking') {
            document.getElementById('rangking').style.display = 'block';
            document.querySelector('a[href="#"][onclick="showContent(\'rangking\')"]').classList.add('active');
        }
    }

    // Tampilkan home secara default saat halaman dimuat
    showContent('home');
</script>