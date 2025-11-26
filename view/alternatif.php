<?php

// include files (sesuaikan path jika perlu)
require_once 'alternatives.php';
require_once 'criteria.php';

// Ambil data alternatif dengan aman
$alternatives = [];
if (function_exists('getAlternatives')) {
    $alternatives = getAlternatives() ?? [];
}

// Ambil data nama kriteria jika tersedia
$criteriaNamesRaw = [];
if (function_exists('getCriteriaNames')) {
    $criteriaNamesRaw = getCriteriaNames() ?? [];
}


// 2) Jika belum ada, coba deteksi id dari keys alternatif (value{id})
if (empty($criteriaList) && !empty($alternatives)) {
    // gabungkan semua keys dari semua alternatives untuk lebih aman
    $idsFound = [];
    foreach ($alternatives as $alt) {
        foreach ($alt as $k => $v) {
            if (preg_match('/^value(\d+)$/i', $k, $m)) {
                $idsFound[(int)$m[1]] = true;
            }
        }
    }
    if (!empty($idsFound)) {
        $ids = array_keys($idsFound);
        sort($ids, SORT_NUMERIC);
        // Coba ambil nama via getCriteria($id) jika fungsi tersedia
        foreach ($ids as $cid) {
            $label = "Kriteria {$cid}";
            if (function_exists('getCriteria')) {
                $crit = getCriteria($cid);
                if (!empty($crit) && isset($crit['name'])) {
                    $label = $crit['name'];
                }
            } else {
                // jika $criteriaNamesRaw berisi list string, kita coba mapping by position (best effort)
                if (!empty($criteriaNamesRaw) && array_values($criteriaNamesRaw) === $criteriaNamesRaw && isset($criteriaNamesRaw[$cid - 1])) {
                    // jika array numerik sederhana (string list), coba ambil berdasarkan indeks (cid-1)
                    $label = $criteriaNamesRaw[$cid - 1];
                }
            }
            $criteriaList[] = ['id' => $cid, 'name' => $label];
        }
    }
}

// 3) Jika masih kosong (tidak ada alternatives, dan tidak ada getCriteriaList), fallback:
// gunakan $criteriaNamesRaw (bisa berupa array assoc atau array string)
if (empty($criteriaList)) {
    if (!empty($criteriaNamesRaw)) {
        // jika setiap elemen adalah array ['id'=>..,'name'=>..]
        $allAssoc = true;
        foreach ($criteriaNamesRaw as $c) {
            if (!is_array($c) || !isset($c['id'])) {
                $allAssoc = false;
                break;
            }
        }
        if ($allAssoc) {
            foreach ($criteriaNamesRaw as $c) {
                $criteriaList[] = ['id' => (int)$c['id'], 'name' => $c['name'] ?? ('Kriteria ' . $c['id'])];
            }
        } else {
            // kemungkinan array string (nama saja) -> generate sequential ids 1..n
            $i = 1;
            foreach ($criteriaNamesRaw as $name) {
                $criteriaList[] = ['id' => $i, 'name' => $name];
                $i++;
            }
        }
    }
}

// Jika tetap kosong: ambil dari fungsi DB langsung (attempt)
if (empty($criteriaList) && function_exists('getCriteriaNames')) {
    // last resort: coba panggil getCriteriaNames lagi dan buat id sequential
    $raw = getCriteriaNames();
    if (!empty($raw) && is_array($raw)) {
        $i = 1;
        foreach ($raw as $r) {
            if (is_array($r) && isset($r['id'])) {
                $criteriaList[] = ['id' => (int)$r['id'], 'name' => $r['name'] ?? "Kriteria {$r['id']}"];
            } else {
                $criteriaList[] = ['id' => $i, 'name' => is_string($r) ? $r : "Kriteria {$i}"];
            }
            $i++;
        }
    }
}

// Pastikan criteriaList terurut berdasarkan id numerik
usort($criteriaList, function ($a, $b) {
    return $a['id'] <=> $b['id'];
});

// --- proses pencarian (jika ada)
$searchTerm = '';
if (!empty($_POST['search'])) {
    $searchTerm = trim($_POST['search']);
    $alternatives = array_filter($alternatives, function ($alternative) use ($searchTerm) {
        return stripos($alternative['name'] ?? '', $searchTerm) !== false;
    });
}

?>
<!-- Tampilan HTML -->
<div class="panel panel-container" style="padding: 20px; margin: 20px; box-shadow: 2px 2px 5px #888888;">
    <h4>Tabel Nilai Karyawan</h4>
    <div class="table-responsive ">
        <div class="table-container">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th style="min-width:60px;">ID</th>
                        <th>Nama Karyawan</th>

                        <?php foreach ($criteriaList as $crit): ?>
                            <th><?= htmlspecialchars(ucfirst($crit['name'])); ?></th>
                        <?php endforeach; ?>

                        <?php if (!empty($_SESSION['role']) && ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'superAdmin')): ?>
                            <th>Aksi</th>
                        <?php endif; ?>
                    </tr>
                </thead>

                <tbody>
                    <?php if (empty($alternatives)): ?>
                        <tr>
                            <td colspan="<?= count($criteriaList) + 3; ?>" class="text-center">Tidak ada hasil ditemukan.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($alternatives as $alternative): ?>
                            <tr>
                                <td class="text-center"><?= htmlspecialchars($alternative['id'] ?? ''); ?></td>
                                <td class="text-center"><?= htmlspecialchars($alternative['name'] ?? ''); ?></td>

                                <?php foreach ($criteriaList as $crit):
                                    $cid = (int)$crit['id'];
                                    $col = "value{$cid}";
                                    $val = $alternative[$col] ?? '';
                                ?>
                                    <td class="text-center"><?= htmlspecialchars((string)$val); ?></td>
                                <?php endforeach; ?>

                                <?php if (!empty($_SESSION['role']) && ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'superAdmin')): ?>
                                    <td class="text-center">
                                        <a href="../config/edit_alternative.php?id=<?= urlencode($alternative['id'] ?? ''); ?>&aksi=ubah" class="btn btn-warning" style="color:white; font-size:12px;"><span class="fa fa-pencil"></span> Edit</a>
                                        <a href="../config/delete_alternative.php?id=<?= urlencode($alternative['id'] ?? ''); ?>&proses=proses-hapus" class="btn btn-danger " style="color:white; font-size:12px;"><span class="fa fa-trash"></span> Hapus</a>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>

            </table>
        </div>
    </div>
</div>