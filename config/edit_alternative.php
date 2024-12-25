<?php
require_once '../view/criteria.php';

// Mendapatkan nama kriteria
$criteriaNames = getCriteriaNames();
?>

<!DOCTYPE html>
<html lang="en">
<?php include 'head.php' ?>

<body>
    <div class="container">
        <h1>Edit Karyawan</h1>

        <?php
        require_once '../view/alternatives.php';

        $id = $_GET['id'];
        $alternative = getAlternative($id);
        if ($alternative) {
        ?>
            <form method="post" action="process_alternative.php?mode=update&id=<?php echo $id; ?>">
                <div class="form-group">
                    <label for="name">Nama Karyawan:</label>
                    <input type="text" class="form-control" name="name" id="name" value="<?php echo $alternative['name']; ?>" required>
                </div>

                <?php
                // Menampilkan input nilai berdasarkan kriteria yang ada
                foreach ($criteriaNames as $index => $criteriaName) :
                    $valueKey = 'value' . ($index + 1); // Dinamis nilai berdasarkan index
                    $value = isset($alternative[$valueKey]) ? $alternative[$valueKey] : '';
                ?>
                    <div class="form-group">
                        <label for="value<?php echo $index + 1; ?>">
                            <?php echo ucfirst($criteriaName); ?>:
                        </label>
                        <input type="number" class="form-control" name="value<?php echo $index + 1; ?>"
                            id="value<?php echo $index + 1; ?>"
                            step="0.01" value="<?php echo $value; ?>" required>
                    </div>
                <?php endforeach; ?>

                <button type="submit" class="btn btn-primary">Simpan</button>
            </form>
        <?php
        } else {
            echo "<p>Alternatif tidak ditemukan.</p>";
        }
        ?>

        <a href="../view/dashboard.php" class="back-link"><i class="fas fa-arrow-left"></i> Kembali</a>
    </div>
</body>

</html>