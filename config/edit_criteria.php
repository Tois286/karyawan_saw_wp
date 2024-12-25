<!DOCTYPE html>
<html lang="en">
<?php include 'head.php' ?>

<body>
    <div class="container">
        <h1>Edit Kriteria</h1>

        <?php
        require_once '../view/criteria.php';

        $id = $_GET['id'];
        $criteria = getCriteria($id);
        if ($criteria) {
        ?>
            <form method="post" action="process_criteria.php?id=<?php echo $id; ?>">
                <div class="form-group">
                    <label for="name">Nama Kriteria:</label>
                    <input type="text" class="form-control" name="name" id="name" value="<?php echo $criteria['name']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="weight">Bobot:</label>
                    <input type="text" class="form-control" name="weight" id="weight" value="<?php echo $criteria['weight']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="type">Tipe:</label>
                    <select name="type" id="type" class="form-control" required>
                        <option value="benefit" <?php if ($criteria['type'] === 'benefit') echo 'selected'; ?>>Benefit</option>
                        <option value="cost" <?php if ($criteria['type'] === 'cost') echo 'selected'; ?>>Cost</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </form>
        <?php
        } else {
            echo "<p>Kriteria tidak ditemukan.</p>";
        }
        ?>

        <a href="../view/dashboard.php" class="back-link"><i class="fas fa-arrow-left"></i> Kembali</a>

    </div>
</body>