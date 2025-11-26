<?php
require_once '../view/criteria.php';
require_once '../view/alternatives.php';

// ------------------------------------------
// 1. Ambil data alternatif berdasarkan ID
// ------------------------------------------
$id = $_GET['id'] ?? null;
$alternative = ($id) ? getAlternative($id) : null;

// Jika alternatif kosong
if (!$alternative) {
    die("Data alternatif tidak ditemukan");
}

// ------------------------------------------
// 2. Ambil daftar kriteria
//    tetapi kita harus SEIMBANGKAN dengan kolom valueX yang benar-benar ada
// ------------------------------------------
$criteriaNames = getCriteriaNames() ?? [];

// Deteksi jumlah kolom valueX yang tersedia di $alternative
$availableValues = [];
foreach ($alternative as $key => $val) {
    if (preg_match('/^value(\d+)$/', $key, $m)) {
        $availableValues[(int)$m[1]] = $val;
    }
}

// Urutkan berdasarkan index (1,2,3,...)
ksort($availableValues);

// Sinkronisasi jumlah kriteria
// Bila kriteria kurang → generate nama default
$criteriaCount = count($availableValues);

if (count($criteriaNames) < $criteriaCount) {
    for ($i = count($criteriaNames); $i < $criteriaCount; $i++) {
        $criteriaNames[] = "Kriteria " . ($i + 1);
    }
}
?>
<!DOCTYPE html>
<html>
<?php include 'head.php'; ?>

<body>

    <div class="container">
        <h1>Edit Karyawan</h1>

        <form method="post" action="process_alternative.php?mode=update&id=<?= $id ?>">

            <div class="form-group">
                <label>Nama Karyawan:</label>
                <input type="text" name="name" class="form-control"
                    value="<?= htmlspecialchars($alternative['name']) ?>" required>
            </div>

            <?php
            $i = 1;
            foreach ($availableValues as $index => $value):
            ?>
                <div class="form-group">
                    <label><?= ucfirst($criteriaNames[$index - 1] ?? "Kriteria $index") ?>:</label>
                    <input type="number" class="form-control"
                        name="value[]" value="<?= htmlspecialchars($value) ?>" step="0.01" required>
                </div>
            <?php
                $i++;
            endforeach;
            ?>

            <button type="submit" class="btn btn-primary">Simpan</button>

        </form>
        <br>
        <center>
            <a href="../view/dashboard.php?show=nilai"><i class="fas fa-arrow-left text"></i> Kembali</a>
        </center>
    </div>

</body>

</html>