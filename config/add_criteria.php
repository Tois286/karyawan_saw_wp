<!DOCTYPE html>
<html lang="en">

<?php include 'head.php'; ?>
<style>
    .card-bobot {
        width: 100%;
        border: 1px solid #ced4da;
        padding: 10px;
        border-radius: 20px;
    }

    .star {
        margin: 20px;
        font-size: 50px;
        color: lightgray;
        cursor: pointer;
    }

    .star.selected {
        color: gold;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }
</style>

<body>
    <div class="container">
        <h1>Tambah Kriteria</h1>
        <form method="post" action="process_criteria.php">
            <div class="card-bobot">
                <center>
                    <strong>Keterangan Type</strong><br><br>
                    <p>Benefit, Semakin Banyak Bintang Semakin Tinggi Nilai Bobotnya Sedangkan Cost, Semakin Sedikit Bintang Semakin Tinggi Nilai Bobotnya.</p>
                    <p>Kriteria Hanya dapat di isi hingga 12 kolom saja</p>
                </center>
            </div><br>
            <div class="form-group">
                <label for="type">Tipe:</label>
                <select class="form-control" name="type" id="type" required>
                    <option value="benefit">Benefit</option>
                    <option value="cost">Cost</option>
                </select>
            </div>

            <div class="form-group">
                <label for="name">Nama Kriteria:</label>
                <input type="text" class="form-control" name="name" id="name" placeholder="Masukkan nama kriteria" required>
            </div>
            <div class="form-group">
                <label for="weight">Bobot:</label>
                <div class="card-bobot">
                    <center>
                        <div id="star-rating">
                            <span class="star" data-value="1">&#9733;</span>
                            <span class="star" data-value="2">&#9733;</span>
                            <span class="star" data-value="3">&#9733;</span>
                            <span class="star" data-value="4">&#9733;</span>
                            <span class="star" data-value="5">&#9733;</span>
                        </div>
                    </center>
                    <input type="hidden" name="weight" id="weight" value="0">
                </div>
            </div>

            <div class="form-group">
                <label for="status">Status Kriteria</label>
                <select name="status" id="" class="form-control">
                    <option value="kantor">Kantor</option>
                    <option value="individu">Individu</option>
                    <option value="normal">Normal</option>
                </select>
            </div>

            <script>
                const stars = document.querySelectorAll('.star');
                const weightInput = document.getElementById('weight');

                stars.forEach(star => {
                    star.addEventListener('click', () => {
                        const value = star.getAttribute('data-value');
                        weightInput.value = value;

                        stars.forEach(s => {
                            s.classList.remove('selected');
                        });

                        for (let i = 0; i < value; i++) {
                            stars[i].classList.add('selected');
                        }
                    });
                });
            </script>

            <button type="submit" class="btn btn-primary btn-block">Tambah</button>
        </form>
        <a href="../view/dashboard.php?show=nilai" class="back-link"><i class="fas fa-arrow-left"></i> Kembali</a>
    </div>
</body>

</html>