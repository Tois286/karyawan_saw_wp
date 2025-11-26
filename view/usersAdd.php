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
    <details id="formSection" class="btn btn-light btn-block">
        <summary id="formTitle">Tambah Data</summary><br>

        <form id="userForm" action="../config/add_users.php" method="post">
            <input type="hidden" name="id" id="id_user">

            <div class="form-container">
                <div class="kiri">

                    <div class="form-group">
                        <label>NIK</label>
                        <input type="text" class="form-control" name="nik" id="nik" required>
                    </div>

                    <div class="form-group">
                        <label>Nama Lengkap</label>
                        <input type="text" class="form-control" name="nama" id="nama" required>
                    </div>

                    <div class="form-group">
                        <label>Jabatan</label>
                        <select name="role" class="form-control" id="role" required>
                            <option value="karyawan">FrontLiner</option>
                            <option value="admin">Admin Web</option>
                        </select>
                    </div>

                </div>

                <div class="kanan">

                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" class="form-control" name="username" id="username" required>
                    </div>

                    <div class="form-group">
                        <label>Password (kosongkan jika edit tanpa ganti password)</label>
                        <input type="password" class="form-control" name="password" id="password">
                    </div>

                    <button type="submit" id="submitBtn" class="btn btn-outline-danger btn-block">
                        Save
                    </button>

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
                    <th>Action</th>
                </tr>
            </thead>
            <?php
            include '../config/koneksi.php';

            $sql = mysqli_query($conn, "SELECT * FROM users");
            ?>
            <tbody>
                <?php
                while ($row = mysqli_fetch_array($sql)) {
                    echo "
    <tr>
        <td>" . htmlspecialchars($row['nik']) . "</td>
        <td>" . htmlspecialchars($row['nama']) . "</td>
        <td>" . htmlspecialchars($row['role']) . "</td>

        <td>
            <button class='btn btn-warning btn-sm' 
                onclick='editUser(
                    " . $row['id_users'] . ",
                    \"" . $row['nik'] . "\",
                    \"" . $row['nama'] . "\",
                    \"" . $row['username'] . "\",
                    \"" . $row['role'] . "\"
                )'>
                Edit
            </button>

            <a href='../config/delete_users.php?id=" . $row['id_users'] . "' 
               onclick=\"return confirm('Yakin ingin menghapus user ini?')\"
               class='btn btn-danger btn-sm'>
               Delete
            </a>
        </td>
    </tr>";
                }
                ?>
            </tbody>

        </table>
    </div>
</div>

<script>
    function editUser(id, nik, nama, username, role) {
        // Buka form otomatis
        document.getElementById("formSection").open = true;

        // Ubah judul
        document.getElementById("formTitle").innerText = "Edit Data User";

        // Isi form dengan data user
        document.getElementById("id_user").value = id;
        document.getElementById("nik").value = nik;
        document.getElementById("nama").value = nama;
        document.getElementById("username").value = username;
        document.getElementById("role").value = role;

        // Kosongkan password (user isi jika ingin ganti)
        document.getElementById("password").value = "";

        // Ubah aksi form dari tambah → edit
        document.getElementById("userForm").action = "../config/edit_users.php";

        // Ubah tombol Save menjadi Update
        document.getElementById("submitBtn").innerText = "Update";
        document.getElementById("submitBtn").classList.remove("btn-outline-danger");
        document.getElementById("submitBtn").classList.add("btn-success");
    }
</script>