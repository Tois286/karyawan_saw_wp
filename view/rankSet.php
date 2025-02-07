<div class="panel panel-container" style="padding: 20px; box-shadow: 2px 2px 5px #888888;">
    <form action="../config/add_rangking.php" method="post">
        <div class="form-group">
            <?php
            // Koneksi ke database
            include '../config/koneksi.php'; // Pastikan file ini ada dan berisi koneksi ke database

            $query = "SELECT * FROM cabang";
            $result = $conn->query($query);

            if (!$result) {
                die("Query gagal: " . $conn->error);
            }
            ?>
            <div class="form-group">
                <label for="id_cabang">Area</label>
                <select name="id_cabang" class="form-control">
                    <option value="">-- Pilih Area --</option>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <option value="<?= htmlspecialchars($row['id_cabang']); ?>">
                            <?= htmlspecialchars($row['area']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="sm">Berapa banyak SM</label>
            <input type="number" class="form-control" name="sm" require>
        </div>
        <div class="form-group">
            <label for="m">Berapa banyak M</label>
            <input type="number" class="form-control" name="m" require>
        </div>
        <div class="form-group">
            <label for="b">Berapa banyak B</label>
            <input type="number" class="form-control" name="b" require>
        </div>
        <div class="form-group">
            <label for="cb">Berapa banyak CB</label>
            <input type="number" class="form-control" name="cb" require>
        </div>
        <div class="form-group">
            <label for="ck">Berapa banyak CK</label>
            <input type="number" class="form-control" name="ck" require>
        </div>
        <div class="form-group">
            <button class="btn btn-warning btn-block">SAVE</button>
        </div>
    </form>
</div>