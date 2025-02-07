<?php
require_once 'alternatives.php';
require_once 'criteria.php';

// Mendapatkan daftar alternatif
$alternatives = getAlternatives();
$columns = getTableColumns("alternatives");

// Mendapatkan nama kriteria
$criteriaNames = getCriteriaNames();

// Memeriksa apakah ada pencarian
$searchTerm = '';
if (isset($_POST['search'])) {
    $searchTerm = $_POST['search'];  // Ambil kata kunci dari form pencarian
    // Filter alternatif berdasarkan pencarian
    $alternatives = array_filter($alternatives, function ($alternative) use ($searchTerm) {
        return stripos($alternative['name'], $searchTerm) !== false; // Filter berdasarkan nama alternatif
    });
}
?>

<div class="panel panel-container" style="padding: 20px; box-shadow: 2px 2px 5px #888888;">
    <h4>Tabel Penilaian</h4>
    <a href="../config/add_alternative.php" class="btn btn-warning" style="color:white;"><span class="fa fa-plus"></span>&emsp; Tambah Data</a>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Alternatif</th>
                    <?php foreach ($criteriaNames as $criteriaName) : ?>
                        <th><?php echo ucfirst($criteriaName); ?></th>
                    <?php endforeach; ?>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($alternatives)): ?>
                    <tr>
                        <td colspan="<?= count($criteriaNames) + 3; ?>" class="text-center">Tidak ada hasil ditemukan.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($alternatives as $alternative) : ?>
                        <tr>
                            <td class="text-center"><?php echo $alternative['id']; ?></td>
                            <td class="text-center"><?php echo $alternative['name']; ?></td>
                            <?php for ($i = 1; $i <= count($criteriaNames); $i++) : ?>
                                <td class="text-center"><?php echo $alternative["value$i"]; ?></td>
                            <?php endfor; ?>
                            <td class="text-center">
                                <a href="../config/edit_alternative.php?id=<?php echo $alternative['id']; ?>&aksi=ubah" class="btn btn-warning" style="color:white;"><span class="fas fa-pencil"></span>Edit</a>
                                <a href="../config/delete_alternative.php?id=<?php echo $alternative['id']; ?>&proses=proses-hapus" class="btn btn-danger"><span class="fa fa-trash"></span>Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>