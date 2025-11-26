<?php
include '../config/koneksi.php';

$sql = mysqli_query($conn, "SELECT * FROM users WHERE username = '" . $_SESSION['username'] . "'");

if ($sql) {
    $data = mysqli_fetch_assoc($sql);

    if ($data) {
        // Data ditemukan, tampilkan
    } else {
        echo "Data tidak ditemukan";
    }
} else {
    echo "Query gagal: " . mysqli_error($conn);
}
?>
<div class="row">
    <div class="col-md-12">
        <center>
            <img src="../img/admin.jpg" alt="Admin" width="140" class="rounded-circle">
        </center>

        <form action="../config/edit_users.php" method="post">
            <table class="table mt-4" style="width:100%; margin:0 auto;">
                <tbody>

                    <!-- NIK -->
                    <tr>
                        <td align="right"><strong>NIK</strong></td>
                        <td align="left">
                            <input type="text" class="form-control" name="nik"
                                value="<?php echo htmlspecialchars($data['nik'] ?? ''); ?>">
                        </td>
                    </tr>

                    <!-- Nama -->
                    <tr>
                        <td align="right"><strong>Nama</strong></td>
                        <td align="left">
                            <input type="text" class="form-control" name="nama"
                                value="<?php echo htmlspecialchars($data['nama'] ?? ''); ?>">
                        </td>
                    </tr>

                    <!-- Username -->
                    <tr>
                        <td align="right"><strong>Username</strong></td>
                        <td align="left">
                            <input type="text" class="form-control" name="username"
                                value="<?php echo htmlspecialchars($data['username'] ?? ''); ?>">
                        </td>
                    </tr>

                    <!-- Password Baru -->
                    <tr>
                        <td align="right"><strong>Password Baru</strong></td>
                        <td align="left">
                            <input type="password" class="form-control" name="password">
                            <small class="text-muted">Kosongkan jika tidak ingin mengubah password</small>
                        </td>
                    </tr>

                    <!-- Konfirmasi Password -->
                    <tr>
                        <td align="right"><strong>Konfirmasi Password</strong></td>
                        <td align="left">
                            <input type="password" class="form-control" name="confirm_password">
                        </td>
                    </tr>

                    <!-- Role -->
                    <?php if ($_SESSION['role'] === 'superAdmin' || $_SESSION['role'] === 'admin'): ?>
                        <tr>
                            <td align="right"><strong>Role</strong></td>
                            <td align="left">
                                <input type="text" class="form-control" name="role"
                                    value="<?php echo htmlspecialchars($data['role'] ?? ''); ?>">
                            </td>
                        </tr>
                    <?php else: ?>
                        <input type="hidden" name="role"
                            value="<?php echo htmlspecialchars($data['role'] ?? ''); ?>">
                    <?php endif; ?>

                    <tr>
                        <td colspan="2">
                            <button class="btn btn-warning">Kirim Perubahan</button>
                        </td>
                    </tr>

                </tbody>
            </table>
        </form>
    </div>
</div>