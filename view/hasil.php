<?php
// include 'header.php';
require_once 'criteria.php';
require_once 'alternatives.php';

// Mendapatkan daftar kriteria
$criterias = getCriterias();

// Mendapatkan daftar alternatif
$alternatives = getAlternatives();

// Langkah pertama: Menjumlahkan nilai bobot
$totalWeight = 0;
foreach ($criterias as $criteria) {
    $totalWeight += $criteria['weight'];
}

// Langkah kedua: Menghitung bobot kepentingan
$criteriaWeights = array();
foreach ($criterias as $criteria) {
    $criteriaWeights[$criteria['id']] = $criteria['weight'] / $totalWeight;
}

// Langkah ketiga: Mengalikan bobot kepentingan dengan nilai alternatif
$sVector = array();
foreach ($alternatives as $alternative) {
    $s = 1;
    foreach ($criterias as $criteria) {
        $value = $alternative['value' . $criteria['id']];
        $weight = $criteriaWeights[$criteria['id']];
        if ($criteria['type'] === 'benefit') {
            $s *= pow($value, $weight);
        } else {
            $s *= pow($value, -$weight);
        }
    }
    $sVector[$alternative['id']] = $s;
}

// Langkah keempat: Menjumlahkan nilai vektor s
$totalS = array_sum($sVector);

// Langkah kelima: Menghitung nilai vektor v
$vVector = array();
foreach ($alternatives as $alternative) {
    $vVector[$alternative['id']] = $sVector[$alternative['id']] / $totalS;
}

// Langkah keenam: Meranking alternatif berdasarkan nilai vektor v
arsort($vVector);

// Perhitungan SAW
$normalizedMatrix = array();
foreach ($criterias as $criteria) {
    $maxValue = max(array_column($alternatives, 'value' . $criteria['id']));
    $minValue = min(array_column($alternatives, 'value' . $criteria['id']));

    foreach ($alternatives as $alternative) {
        $value = $alternative['value' . $criteria['id']];
        if ($criteria['type'] === 'benefit') {
            $normalizedMatrix[$alternative['id']][$criteria['id']] = $value / $maxValue;
        } else {
            $normalizedMatrix[$alternative['id']][$criteria['id']] = $minValue / $value;
        }
    }
}

// Menghitung nilai akhir SAW
$sawResults = array();
foreach ($alternatives as $alternative) {
    $total = 0;
    foreach ($criterias as $criteria) {
        $weight = $criteriaWeights[$criteria['id']];
        $normalizedValue = $normalizedMatrix[$alternative['id']][$criteria['id']];
        $total += $weight * $normalizedValue;
    }
    $sawResults[$alternative['id']] = $total;
}

// Meranking hasil SAW
arsort($sawResults);

// Mendapatkan alternatif dengan peringkat pertama untuk metode WP
$topWPId = key($vVector);
$topWP = getAlternative($topWPId);

// Mendapatkan alternatif dengan peringkat pertama untuk metode SAW
$topSAWId = key($sawResults);
$topSAW = getAlternative($topSAWId);

?>

<style>
    /* General Button Styling */
    .button {
        display: flex;
        justify-content: center;
        gap: 15px;
        margin-bottom: 20px;
    }

    .button a {
        display: inline-block;
        padding: 12px 20px;
        border-radius: 8px;
        font-size: 16px;
        font-weight: bold;
        color: #fff;
        background-color: #007bff;
        text-decoration: none;
        transition: all 0.3s ease;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .button a:hover {
        background-color: #0056b3;
        transform: translateY(-2px);
        box-shadow: 0 6px 10px rgba(0, 0, 0, 0.15);
    }

    /* Panel Styling */
    .panel {
        padding: 20px;
        background: #f9f9f9;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    /* Table Styling */
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    table thead {
        background-color: #007bff;
        color: #fff;
        text-align: left;
    }

    table th,
    table td {
        padding: 12px;
        border: 1px solid #ddd;
        text-align: center;
    }

    table tbody tr:nth-child(odd) {
        background-color: #f2f2f2;
    }

    table tbody tr:hover {
        background-color: #e6f7ff;
    }

    /* Hidden Sections */
    #saw,
    #wp {
        display: none;
    }

    .card-rank {
        background: #f5f5f5;
        /* Warna latar belakang yang netral */
        border: 1px solid #ddd;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        width: 100%;
        margin: 20px auto;
        overflow: hidden;
    }

    .card-header {
        background-color: #007bff;
        /* Warna header tabel */
        color: white;
        padding: 15px;
        text-align: center;
        font-size: 20px;
        /* Ukuran font lebih besar */
        font-weight: bold;
    }

    .card-body {
        padding: 20px;
        font-size: 16px;
        /* Ukuran font isi */
        color: #333;
    }

    .card-body p {
        margin-bottom: 15px;
        font-size: 18px;
        /* Ukuran font untuk nama */
        font-weight: bold;
    }

    .card-body ul {
        list-style-type: none;
        /* Hilangkan bullet */
        padding: 0;
        margin: 0;
    }

    .card-body ul li {
        margin: 8px 0;
        padding: 10px;
        background-color: #f9f9f9;
        /* Warna latar belakang item */
        border: 1px solid #ddd;
        /* Border untuk setiap item */
        border-radius: 4px;
        font-size: 16px;
        /* Ukuran font untuk nilai */
        color: #555;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        /* Efek bayangan */
    }

    .card-body ul li strong {
        color: #007bff;
        /* Warna untuk label nilai */
    }
</style>

<div class="panel">
    <div class="button">
        <a href="#" onclick="showHasil('wp')">Metode WP</a>
        <a href="#" onclick="showHasil('saw')">Metode SAW</a>
    </div>

    <div id="wp">
        <h4>Hasil Penilaian WP</h4>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Ranking</th>
                        <th>Nama Karyawan</th>
                        <th>Nilai S</th>
                        <th>Nilai Vektor V</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $rank = 1;
                    foreach ($vVector as $alternativeId => $value) {
                        $alternative = getAlternative($alternativeId);
                        echo "<tr>";
                        echo "<td>" . $alternative['id'] . "</td>";
                        echo "<td>" . $rank . "</td>";
                        echo "<td>" . $alternative['name'] . "</td>";
                        echo "<td>" . $sVector[$alternative['id']] . "</td>";
                        echo "<td>" . $value . "</td>";
                        echo "</tr>";
                        $rank++;
                    }
                    ?>
                </tbody>
            </table>
            <div class="card-rank">
                <div class="card-header">
                    <h5>Karyawan Terbaik</h5>
                </div>
                <div class="card-body">
                    <p><strong>Nama:</strong> <?= $topWP['name']; ?></p>
                    <ul>
                        <?php foreach ($criterias as $criteria): ?>
                            <li>
                                <strong><?= $criteria['name']; ?> (value <?= $criteria['id']; ?>):</strong>
                                <?= $topWP['value' . $criteria['id']]; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

            </div>
        </div>
    </div>


    <div id="saw">
        <h4>Hasil Penilaian SAW</h4>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Ranking</th>
                        <th>Nama Karyawan</th>
                        <th>Nilai Akhir</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $rank = 1;
                    foreach ($sawResults as $alternativeId => $value) {
                        $alternative = getAlternative($alternativeId);
                        echo "<tr>";
                        echo "<td>" . $alternative['id'] . "</td>";
                        echo "<td>" . $rank . "</td>";
                        echo "<td>" . $alternative['name'] . "</td>";
                        echo "<td>" . $value . "</td>";
                        echo "</tr>";
                        $rank++;
                    }
                    ?>
                </tbody>
            </table>
            <div class="card-rank">
                <div class="card-header">
                    <h5>Karyawan Terbaik</h5>
                </div>
                <div class="card-body">
                    <p><strong>Nama:</strong> <?= $topWP['name']; ?></p>
                    <ul>
                        <?php foreach ($criterias as $criteria): ?>
                            <li>
                                <strong><?= $criteria['name']; ?> (Nilai <?= $criteria['id']; ?>):</strong>
                                <?= $topWP['value' . $criteria['id']]; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function showHasil(method) {
        // Hide all sections
        document.getElementById('wp').style.display = 'none';
        document.getElementById('saw').style.display = 'none';

        // Show the selected section
        document.getElementById(method).style.display = 'block';
    }
</script>