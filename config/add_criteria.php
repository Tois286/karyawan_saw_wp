<!DOCTYPE html>
<html lang="en">

<?php include 'head.php'; ?>

<body>
    <div class="container">
        <h1>Tambah Kriteria</h1>
        <form method="post" action="process_criteria.php">
            <div class="form-group">
                <label for="name">Nama Kriteria:</label>
                <input type="text" class="form-control" name="name" id="name" placeholder="Masukkan nama kriteria" required>
            </div>
            <div class="form-group">
                <label for="weight">Bobot:</label>
                <input type="text" class="form-control" name="weight" id="weight" placeholder="Masukkan bobot kriteria" required>
            </div>
            <div class="form-group">
                <label for="type">Tipe:</label>
                <select class="form-control" name="type" id="type" required>
                    <option value="benefit">Benefit</option>
                    <option value="cost">Cost</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Tambah</button>
        </form>
        <a href="../view/dashboard.php" class="back-link"><i class="fas fa-arrow-left"></i> Kembali</a>
    </div>
</body>

</html>