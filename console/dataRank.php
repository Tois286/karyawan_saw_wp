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

// Handle search query
$searchTerm = isset($_POST['search']) ? $_POST['search'] : '';

$filteredAlternatives = array_filter($alternatives, function ($alternative) use ($searchTerm) {
    return isset($alternative['name']) && stripos($alternative['name'], $searchTerm) !== false;
});
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
        padding: 10px 18px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: bold;
        color: #fff;
        background-color: #e51b24;
        text-decoration: none;
        transition: all 0.3s ease;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .button a:hover {
        background-color: #D81D26FF;
        transform: translateY(-2px);
        box-shadow: 0 6px 10px rgba(0, 0, 0, 0.15);
    }

    /* Panel Styling */
    .panel {
        padding: 15px;
        background: #f9f9f9;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        max-width: 1200px;
        /* Max width for the panel */
        margin: 20px auto;
        /* Center the panel */
    }

    /* Table Styling */
    table {
        width: 100%;
        max-width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        overflow-x: auto;
        /* Prevent table from overflowing */
    }

    table thead {
        background-color: #e51b24;
        color: #fff;
        text-align: left;
    }

    table th,
    table td {
        padding: 10px;
        border: 1px solid #ddd;
        text-align: center;
        font-size: 14px;
        /* Smaller font size */
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
        border: 1px solid #ddd;
        border-radius: 8px;
        width: 100%;
        margin: 20px auto;
        overflow: hidden;
    }

    .card-header {
        background-color: #e51b24;
        color: white;
        padding: 12px;
        text-align: center;
        font-size: 18px;
        /* Smaller font size for header */
        font-weight: bold;
    }

    .card-body {
        display: flex;
        padding: 15px;
        font-size: 14px;
        color: #333;
    }

    .card-body p {
        margin-bottom: 12px;
        font-size: 16px;
        /* Adjusted font size */
        font-weight: bold;
    }

    .card-body ul {
        width: 100%;
        list-style-type: none;
        padding: 0;
        margin: 0;
    }

    .card-body ul li {
        margin: 6px 0;
        padding: 8px;
        background-color: #f9f9f9;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 14px;
        /* Adjusted font size */
        color: #555;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .card-body ul li strong {
        color: #e51b24;
    }

    /* Make tables responsive */
    @media (max-width: 768px) {
        table {
            font-size: 12px;
            /* Smaller font size for small screens */
        }

        .button a {
            font-size: 12px;
            /* Smaller buttons */
        }

        .panel {
            padding: 10px;
        }

        .card-header {
            font-size: 16px;
        }

        .card-body p {
            font-size: 14px;
        }
    }

    .card-pie {
        width: 50%;
        background-color: white;
        border-radius: 10px;
        margin: 20px;
        padding: 5px;
        border: 1px solid #e51b24;
    }

    .dropdown-normalisasi {
        color: #e51b24;
        padding: 10px;
        border-radius: 10px;
        border: 1px solid #e51b24;
        margin: 10px;
    }
</style>

<head>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<div class="panel">
    <div class="button">
        <a href="#" onclick="showHasil('pros')">Proses Perhitungan</a>
        <a href="#" onclick="showHasil('wp')">Metode WP</a>
        <a href="#" onclick="showHasil('saw')">Metode SAW</a>
    </div>
    <form method="POST">
        <div class="input-group ml-1">
            <input type="text" name="search" class="form-control" placeholder="Search..." value="<?= htmlspecialchars($searchTerm); ?>">
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
                    echo "<h3 style='color:black;'>Kriteria: " . htmlspecialchars($criteria['name']) . " (" . htmlspecialchars($criteria['type']) . ")</h3>";
                    $maxValue = max(array_column($alternatives, 'value' . $criteria['id']));
                    $minValue = min(array_column($alternatives, 'value' . $criteria['id']));

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
                        $value = $alternative['value' . $criteria['id']];
                        if ($criteria['type'] === 'benefit') {
                            $normalizedValue = $value / $maxValue;
                            $process = "$value ÷ $maxValue";
                        } else {
                            $normalizedValue = $minValue / $value;
                            $process = "$minValue ÷ $value";
                        }

                        echo "<tr>";
                        echo "<td>R<sub>" . $alternative['id'] . $criteria['id'] . "</sub></td>";
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
                            <th><?= $criteria['name']; ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($alternatives as $alternative): ?>
                        <tr>
                            <td><?= $alternative['name']; ?></td>
                            <?php foreach ($criterias as $criteria): ?>
                                <td><?= number_format($normalizedMatrix[$alternative['id']][$criteria['id']], 4); ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <h3>Perhitungan Metode WP</h3>
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
                            <td><?= getAlternative($id)['name']; ?></td>
                            <td><?= number_format($sVector[$id], 4); ?></td>
                            <td><?= number_format($v, 4); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

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
                            <td><?= getAlternative($id)['name']; ?></td>
                            <td><?= number_format($total, 4); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </details>
        <div class="card-rank" style="padding:10px;background-color:white;">
            <h3>Peringkat Tertinggi</h3>
            <p><strong>Metode WP:</strong> <?= $topWP['name']; ?> dengan nilai <?= number_format($vVector[$topWPId], 4); ?></p>
            <p><strong>Metode SAW:</strong> <?= $topSAW['name']; ?> dengan nilai <?= number_format($sawResults[$topSAWId], 4); ?></p>
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
                            <canvas id="myPieChart1"></canvas>
                        </center>
                    </div>
                    <ul>
                        <p><strong>Nama:</strong> <?= $topWP['name']; ?></p>

                        <?php foreach ($criterias as $criteria): ?>
                            <li>
                                <strong><?= $criteria['name']; ?> (value <?= $criteria['id']; ?>):</strong>
                                <?= $topWP['value' . $criteria['id']]; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                </div>
            </div>

            <!-- Urutkan alternatif berdasarkan nilai vektor V -->
            <?php
            usort($filteredAlternatives, function ($a, $b) use ($vVector) {
                return $vVector[$b['id']] <=> $vVector[$a['id']]; // Urutkan berdasarkan nilai Vektor V
            });
            ?>

            <table>
                <thead>
                    <tr>
                        <th>Ranking</th>
                        <th>Nama Karyawan</th>
                        <th>Nilai S</th>
                        <th>Nilai Vektor V</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $rank = 1;
                    foreach ($filteredAlternatives as $alternativeId => $alternative) {
                        echo "<tr>";
                        echo "<td>" . $rank . "</td>";
                        echo "<td>" . $alternative['name'] . "</td>";
                        echo "<td>" . (isset($sVector[$alternative['id']]) ? $sVector[$alternative['id']] : 'N/A') . "</td>";
                        echo "<td>" . (isset($vVector[$alternative['id']]) ? $vVector[$alternative['id']] : 'N/A') . "</td>";
                        echo "</tr>";
                        $rank++;
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <div id="saw">
        <h4>Hasil Penilaian SAW</h4>
        <div class="table-responsive">
            <div class="card-rank">
                <div class="card-body">
                    <div class="card-pie">
                        <center>
                            <canvas id="myPieChart2"></canvas>
                        </center>
                    </div>
                    <ul>
                        <p><strong>Nama:</strong> <?= $topWP['name']; ?></p>

                        <?php foreach ($criterias as $criteria): ?>
                            <li>
                                <strong><?= $criteria['name']; ?> (value <?= $criteria['id']; ?>):</strong>
                                <?= $topWP['value' . $criteria['id']]; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                </div>
            </div>

            <!-- Urutkan alternatif berdasarkan nilai SAW -->
            <?php
            usort($filteredAlternatives, function ($a, $b) use ($sawResults) {
                return $sawResults[$b['id']] <=> $sawResults[$a['id']]; // Urutkan berdasarkan hasil SAW
            });
            ?>

            <table>
                <thead>
                    <tr>
                        <th>Ranking</th>
                        <th>Nama Karyawan</th>
                        <th>Nilai</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $rank = 1;
                    foreach ($filteredAlternatives as $alternativeId => $alternative) {
                        echo "<tr>";
                        echo "<td>" . $rank . "</td>";
                        echo "<td>" . $alternative['name'] . "</td>";
                        echo "<td>" . (isset($sawResults[$alternative['id']]) ? $sawResults[$alternative['id']] : 'N/A') . "</td>";
                        echo "</tr>";
                        $rank++;
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
    function showHasil(method) {
        if (method === 'wp') {
            document.getElementById('wp').style.display = 'block';
            document.getElementById('saw').style.display = 'none';
            document.getElementById('pros').style.display = 'none';

        } else if (method === 'saw') {
            document.getElementById('wp').style.display = 'none';
            document.getElementById('pros').style.display = 'none';
            document.getElementById('saw').style.display = 'block';
        } else if (method === 'pros') {
            document.getElementById('pros').style.display = 'block';
            document.getElementById('wp').style.display = 'none';
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
                    text: 'Grafik Pie WP',
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
                    text: 'Grafik Pie SAW',
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