<?php
// include 'header.php';
require_once 'view/criteria.php';
require_once 'view/alternatives.php';

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
    $columnValues = array_column($alternatives, 'value' . ($criteria['id'] ?? ''));

    if (!empty($columnValues)) {
        $maxValue = max($columnValues);
        $minValue = min($columnValues);
    } else {
        $maxValue = 1; // Atur default agar tidak error (bisa diganti sesuai kebutuhan)
        $minValue = 1;
    }

    foreach ($alternatives as $alternative) {
        $value = $alternative['value' . ($criteria['id'] ?? '')] ?? 0;

        if ($criteria['type'] === 'benefit') {
            $normalizedMatrix[$alternative['id']][$criteria['id']] = ($maxValue != 0) ? ($value / $maxValue) : 0;
        } else {
            $normalizedMatrix[$alternative['id']][$criteria['id']] = ($value != 0) ? ($minValue / $value) : 0;
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

// Handle search query
$searchTerm = isset($_POST['search']) ? $_POST['search'] : '';

$filteredAlternatives = array_filter($alternatives, function ($alternative) use ($searchTerm) {
    return isset($alternative['name']) && stripos($alternative['name'], $searchTerm) !== false;
});
?>
<link rel="stylesheet" href="asset/css/datarank.css">

<head>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<div class="panel">
    <div class="button">
        <a href="#" onclick="showHasil('pros')">Proses Perhitungan</a>
        <!-- <a href="#" onclick="showHasil('wp')">Metode WP</a> -->
        <a href="#" onclick="showHasil('saw')">Metode SAW</a>
    </div>
    <form method="POST">
        <div class="input-group ml-1">
            <input type="text" name="search" class="form-control" placeholder="Search..." value="<?= htmlspecialchars($searchTerm) ?? ''; ?>">
            <div class="input-group-append">
                <button class="btn btn-warning" type="submit">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
    </form>
    <div id="pros">
        <details class="dropdown-normalisasi">
            <summary>Proses Normalisasi</summary>
            <div>
                <?php
                // Menampilkan proses normalisasi dengan tabel
                foreach ($criterias as $criteria) {
                    echo "<h3 style='color:black;'>Kriteria: " . htmlspecialchars($criteria['name'] ?? '') . " (" . htmlspecialchars($criteria['type'] ?? '') . ")</h3>";

                    $columnValues = array_column($alternatives, 'value' . ($criteria['id'] ?? ''));

                    // Cek apakah array berisi nilai sebelum menggunakan max() dan min()
                    if (!empty($columnValues)) {
                        $maxValue = max($columnValues);
                        $minValue = min($columnValues);
                    } else {
                        $maxValue = 1; // Atur nilai default agar tidak error
                        $minValue = 1;
                    }

                    echo "<table>";
                    echo "<thead>
    <tr>
        <th>Alternatif</th>
        <th>Nilai Awal</th>
        <th>Proses</th>
        <th>Hasil Normalisasi</th>
    </tr>
  </thead>";
                    echo "<tbody>";

                    foreach ($alternatives as $alternative) {
                        $value = $alternative['value' . ($criteria['id'] ?? '')] ?? 0;

                        if ($criteria['type'] === 'benefit') {
                            $normalizedValue = ($maxValue != 0) ? ($value / $maxValue) : 0;
                            $process = "$value ÷ $maxValue";
                        } else {
                            $normalizedValue = ($value != 0) ? ($minValue / $value) : 0;
                            $process = "$minValue ÷ $value";
                        }

                        echo "<tr>";
                        echo "<td>R<sub>" . ($alternative['id'] ?? '') . ($criteria['id'] ?? '') . "</sub></td>";
                        echo "<td>" . htmlspecialchars($value) . "</td>";
                        echo "<td>" . htmlspecialchars($process) . "</td>";
                        echo "<td>" . number_format($normalizedValue, 4) . "</td>";
                        echo "</tr>";
                    }

                    echo "</tbody>";
                    echo "</table>";
                }
                ?>

            </div>
        </details>

        <details class="dropdown-normalisasi">
            <summary>Normalisasi Matriks</summary>
            <h3>Normalisasi Matriks</h3>
            <table>
                <thead>
                    <tr>
                        <th>Alternatif</th>
                        <?php foreach ($criterias as $criteria): ?>
                            <th><?= $criteria['name'] ?? ''; ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($alternatives as $alternative): ?>
                        <tr>
                            <td><?= $alternative['name'] ?? ''; ?></td>
                            <?php foreach ($criterias as $criteria): ?>
                                <td><?= number_format($normalizedMatrix[$alternative['id']][$criteria['id']], 4); ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- <h3>Perhitungan Metode WP</h3>
            <table>
                <thead>
                    <tr>
                        <th>Alternatif</th>
                        <th>Nilai S</th>
                        <th>Nilai V</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($vVector as $id => $v): ?>
                        <tr>
                            <td><?= getAlternative($id)['name'] ?? ''; ?></td>
                            <td><?= number_format($sVector[$id], 4); ?></td>
                            <td><?= number_format($v, 4); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table> -->

            <h3>Perhitungan Metode SAW</h3>
            <table>
                <thead>
                    <tr>
                        <th>Alternatif</th>
                        <th>Nilai Akhir</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sawResults as $id => $total): ?>
                        <tr>
                            <td><?= getAlternative($id)['name'] ?? ''; ?></td>
                            <td><?= number_format($total, 4); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </details>
        <div class="card-rank" style="padding:10px;background-color:white;">
            <h3>Peringkat Tertinggi</h3>

            <p><strong>Metode SAW:</strong>
                <?= isset($topSAW['name']) ? htmlspecialchars($topSAW['name']) : 'Tidak ada data'; ?>
                dengan nilai
                <?= isset($sawResults[$topSAWId]) ? number_format($sawResults[$topSAWId], 4) : '0.0000'; ?>
            </p>
        </div>

    </div>
    <br>
    <div id="wp">
        <h4>Hasil Penilaian WP</h4>
        <div class="table-responsive">
            <div class="card-rank">
                <div class="card-body">
                    <div class="card-pie">
                        <center>
                            <canvas id="myPieChart1" width="200" height="200"></canvas>
                        </center>
                    </div>
                    <ul>
                        <p><strong>Nama:</strong> <?= $topWP['name'] ?? 'none'; ?></p>
                        <?php foreach ($criterias as $criteria): ?>
                            <li>
                                <strong><?= $criteria['name'] ?? 'Tidak ada nama'; ?> (value <?= $criteria['id'] ?? 'Tidak ada ID'; ?>):</strong>
                                <?= isset($topWP['value' . ($criteria['id'] ?? '')]) ? $topWP['value' . ($criteria['id'] ?? '')] : 'Tidak ada data'; ?>
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
            <div class="card-rank">
                <div class="card-body">
                    <!-- Elemen untuk Grafik Pie -->
                    <div class="card-pie">
                        <center>
                            <canvas id="myPieChart2" width="200" height="200"></canvas>
                        </center>
                    </div>
                    <ul>
                        <p><strong>Nama:</strong> <?= $topWP['name'] ?? 'none'; ?></p>
                        <div class="table-container">
                            <?php foreach ($criterias as $criteria): ?>
                                <li>
                                    <strong><?= $criteria['name'] ?? ''; ?> (Value <?= $criteria['id'] ?? ''; ?>):</strong>
                                    <?= $topWP['value' . $criteria['id'] ?? '']; ?>
                                </li>
                            <?php endforeach; ?>
                        </div>
                    </ul>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
    function showHasil(method) {
        if (method === 'saw') {
            // document.getElementById('wp').style.display = 'none';
            document.getElementById('pros').style.display = 'none';
            document.getElementById('saw').style.display = 'block';
        } else if (method === 'pros') {
            document.getElementById('pros').style.display = 'block';
            // document.getElementById('wp').style.display = 'none';
            document.getElementById('saw').style.display = 'none';
        }
    }
</script>

<script>
    const ctx1 = document.getElementById('myPieChart1').getContext('2d');
    const data1 = {
        labels: [<?php foreach ($criterias as $criteria) echo '"' . $criteria['name'] . '", '; ?>],
        datasets: [{
            label: 'Values',
            data: [<?php foreach ($criterias as $criteria) echo $topWP['value' . $criteria['id']] . ', '; ?>],
            backgroundColor: [
                '#ff4d4d', // Merah cerah
                '#4da6ff', // Biru cerah
                '#ffcc00', // Kuning cerah
                '#4dd2b0', // Hijau toska cerah
                '#b366ff', // Ungu cerah
                '#ffcc66' // Oranye cerah
            ],
            borderColor: [
                '#e51b24', // Warna dasar merah
                '#0073e6', // Warna dasar biru
                '#ffcc00', // Warna dasar kuning
                '#00994d', // Warna dasar hijau toska
                '#9933cc', // Warna dasar ungu
                '#ff9900' // Warna dasar oranye
            ],
            borderWidth: 2
        }]
    };

    const myPieChart1 = new Chart(ctx1, {
        type: 'pie',
        data: data1,
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                },
                title: {
                    display: true,
                    text: 'Grafik Winner',
                },
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            const label = tooltipItem.label || '';
                            const value = tooltipItem.raw || 0;
                            const total = tooltipItem.dataset.data.reduce((acc, val) => acc + val, 0);
                            const percentage = ((value / total) * 100).toFixed(2); // Hitung persentase
                            return `${label}: ${value} (${percentage}%)`; // Tampilkan nilai dan persentase
                        }
                    }
                }
            }
        }
    });

    const ctx2 = document.getElementById('myPieChart2').getContext('2d');
    const data2 = {
        labels: [<?php foreach ($criterias as $criteria) echo '"' . $criteria['name'] . '", '; ?>],
        datasets: [{
            label: 'Values',
            data: [<?php foreach ($criterias as $criteria) echo $topWP['value' . $criteria['id']] . ', '; ?>],
            backgroundColor: [
                '#ff4d4d', // Merah cerah
                '#4da6ff', // Biru cerah
                '#ffcc00', // Kuning cerah
                '#4dd2b0', // Hijau toska cerah
                '#b366ff', // Ungu cerah
                '#ffcc66' // Oranye cerah
            ],
            borderColor: [
                '#e51b24', // Warna dasar merah
                '#0073e6', // Warna dasar biru
                '#ffcc00', // Warna dasar kuning
                '#00994d', // Warna dasar hijau toska
                '#9933cc', // Warna dasar ungu
                '#ff9900' // Warna dasar oranye
            ],
            borderWidth: 2
        }]
    };

    const myPieChart2 = new Chart(ctx2, {
        type: 'pie',
        data: data2,
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                },
                title: {
                    display: true,
                    text: 'Grafik Winner',
                },
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            const label = tooltipItem.label || '';
                            const value = tooltipItem.raw || 0;
                            const total = tooltipItem.dataset.data.reduce((acc, val) => acc + val, 0);
                            const percentage = ((value / total) * 100).toFixed(2); // Hitung persentase
                            return `${label}: ${value} (${percentage}%)`; // Tampilkan nilai dan persentase
                        }
                    }
                }
            }
        }
    });
</script>