<?php
require_once 'alternatives.php';
require_once 'criteria.php';

// Mendapatkan daftar alternatif
$alternatives = getAlternatives();
$columns = getTableColumns("alternatives");

// Mendapatkan nama kriteria
$criteriaNames = getCriteriaNames();
?>

<div class="panel panel-container" style="padding: 20px; box-shadow: 2px 2px 5px #888888;">
    <h4>Tabel Penilaian</h4>
    <a href="../config/add_alternative.php" class="btn btn-primary"><span class="fa fa-plus"></span>&emsp; Tambah Data</a>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <!-- Header untuk kolom tetap -->
                    <th>ID</th>
                    <th>Nama Alternatif</th>

                    <!-- Header untuk kolom nilai -->
                    <?php foreach ($criteriaNames as $criteriaName) : ?>
                        <th><?php echo ucfirst($criteriaName); ?></th>
                    <?php endforeach; ?>

                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($alternatives as $alternative) : ?>
                    <tr>
                        <!-- Data untuk kolom tetap -->
                        <td class="text-center"><?php echo $alternative['id']; ?></td>
                        <td class="text-center"><?php echo $alternative['name']; ?></td>

                        <!-- Data untuk kolom nilai -->
                        <?php for ($i = 1; $i <= count($criteriaNames); $i++) : ?>
                            <td class="text-center"><?php echo $alternative["value$i"]; ?></td>
                        <?php endfor; ?>

                        <!-- Aksi -->
                        <td class="text-center">
                            <a href="../config/edit_alternative.php?id=<?php echo $alternative['id']; ?> &aksi=ubah" class="btn btn-info"><span class="fa fa-pencil"></span>Edit</a>
                            <a href="../config/delete_alternative.php?id=<?php echo $alternative['id']; ?> &proses=proses-hapus" class="btn btn-danger"><span class="fa fa-trash"></span>Hapus</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>