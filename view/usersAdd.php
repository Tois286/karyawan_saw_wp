<style>
    .form-container {
        text-align: left;
        display: flex;
        /* Menggunakan flexbox untuk tata letak */
        justify-content: space-between;
        /* Ruang antara div kiri dan kanan */
        gap: 20px;
        /* Jarak antar div */
    }

    .kiri,
    .kanan {
        flex: 1;
        /* Membuat kedua div memiliki lebar yang sama */
        min-width: 200px;
        /* Lebar minimum untuk responsif */
    }
</style>
<div class="panel panel-container" style="padding: 20px; margin: 20px; box-shadow: 2px 2px 5px #888888;">
    <center>
        <h4>Add Users</h4>
    </center>
    <details class="btn btn-light btn-block">
        <summary>Tambah Data</summary><br>
        <form action="../config/add_users.php" method="post">
            <div class="form-container">
                <div class="kiri">
                    <div class="form-group">
                        <label for="nik">NIK</label>
                        <input type="text" class="form-control" name="nik" required>
                    </div>
                    <div class="form-group">
                        <label for="nama">Nama Lengkap</label>
                        <input type="text" class="form-control" name="nama" required>
                    </div>

                    <div class="form-group">
                        <label for="role">Jabatan</label>
                        <select name="role" class="form-control" required>
                            <option value="karyawan">FrontLiner</option>
                            <option value="admin">Admin Web</option>
                        </select>
                    </div>

                </div>

                <div class="kanan">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" class="form-control" name="username" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>

                    <div class="form-group">
                        <label for="save">Simpan Data baru dan Lihat di bagian tabel!</label>
                        <button type="submit" class="btn btn-outline-danger btn-block">Save</button>
                    </div>
                </div>
            </div>
        </form>
    </details>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>NIK</th>
                    <th>Name</th>
                    <th>Role</th>
                </tr>
            </thead>
            <?php
            include '../config/koneksi.php';

            $sql = mysqli_query($conn, "SELECT * FROM users");
            ?>
            <tbody>
                <?php
                while ($row = mysqli_fetch_array($sql)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['nik']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['nama']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['role']) . "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>