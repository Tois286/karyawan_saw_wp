<?php
require_once '../view/criteria.php';

// Mendapatkan nama kriteria
$criteriaNames = getCriteriaNames();
?>
<!DOCTYPE html>
<html lang="en">

<?php include 'head.php'; ?>

<body>
    <div class="container">
        <h1>Tambah Karyawan</h1>
        <form method="post" action="process_alternative.php">
            <div class="form-group">
                <label for="name">Nama Karyawan:</label>
                <input type="text" class="form-control" name="name" id="name" placeholder="Masukkan nama Karyawan" required>
            </div>

            <?php
            // Membuat form input dinamis berdasarkan nama kriteria
            foreach ($criteriaNames as $index => $criteriaName) :
            ?>
                <div class="form-group">
                    <label for="value<?php echo $index; ?>">
                        <?php echo ucfirst($criteriaName); ?>:
                    </label>
                    <input type="number" class="form-control" name="value[]" id="value<?php echo $index; ?>"
                        step="0.01" placeholder="Masukkan nilai untuk <?php echo $criteriaName; ?>" required>
                </div>
            <?php endforeach; ?>

            <button type="submit" class="btn btn-primary btn-block">Tambah</button>
        </form>
        <a href="../view/dashboard.php" class="back-link"><i class="fas fa-arrow-left"></i> Kembali</a>
    </div>
</body>

</html>