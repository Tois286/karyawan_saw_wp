<div class="panel panel-container" style="padding: 20px; box-shadow: 2px 2px 5px #888888;">
    <center>
        <h3>Add Cabang</h3>

    </center>
    <form action="../config/add_cabang.php" method="post">
        <div class="form-group">
            <label for="area">Nama cabang</label>
            <input type="text" class="form-control" name="area" required>
        </div>
        <div class="form-group">
            <label for="kota">Kota</label>
            <input type="text" class="form-control" name="kota" id="" required>
        </div>
        <div class="form-group">
            <label for="alamat">Alamat</label>
            <input type="text" class="form-control" name="alamat" id="" required>
        </div>
        <div class="form-group">
            <button class="btn btn-warning btn-block">Save</button>
        </div>
    </form>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nama Area</th>
                    <th>Kota</th>
                    <th>Alamat</th>
                </tr>
            </thead>
            <?php
            include '../config/koneksi.php';

            $sql = mysqli_query($conn, "SELECT * FROM cabang");
            ?>
            <tbody>
                <?php
                while ($row = mysqli_fetch_array($sql)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['area']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['kota']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['alamat']) . "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>