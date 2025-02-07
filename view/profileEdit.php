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
        <div class="text-center">
            <div class="panel panel-container" style="padding: 20px; box-shadow: 2px 2px 5px #888888;">
                <img src="../img/admin.jpg" alt="Anjas Kosasih" width="140" class="rounded-circle">
                <table class="table mt-4" style="width:100%; margin:0 auto;">
                    <form action="" method="post">
                        <tbody>
                            <tr>
                                <td align="right" width="50%"><strong>Nama</strong></td>
                                <td align="left" width="50%">
                                    <input type="text" class="form-control" name="nama" value="<?php echo isset($data['username']) ? htmlspecialchars($data['nama']) : 'N/A'; ?>">
                                </td>
                            </tr>
                            <tr>
                                <td align="right" width="50%"><strong>Username</strong></td>
                                <td align="left" width="50%">
                                    <input type="text" class="form-control" name="username" value="<?php echo isset($data['username']) ? htmlspecialchars($data['username']) : 'N/A'; ?>">
                                </td>
                            </tr>
                            <tr>
                                <td align="right" width="50%"><strong>Role</strong></td>
                                <td align="left" width="50%">
                                    <input type="text" class="form-control" name="role" value=" <?php echo isset($data['role']) ? htmlspecialchars($data['role']) : 'N/A'; ?>">
                                </td>
                            </tr>
                            <tr>
                                <td align="right" width="50%"><strong>Area</strong></td>
                                <td align="left" width="50%">
                                    <input type="text" class="form-control" name="area" value="<?php echo isset($data['role']) ? htmlspecialchars($data['area']) : 'N/A'; ?>">
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <button class="btn btn-warning">Kirim Perubahan</button>
                                </td>
                            </tr>
                        </tbody>
                    </form>
                </table>
            </div>
        </div>
    </div>
</div>