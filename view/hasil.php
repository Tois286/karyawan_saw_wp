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
        background-color: #e51b24;
        text-decoration: none;
        transition: all 0.3s ease;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .button a:hover {
        background-color: #e51b24;
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

    .table-container {
        width: 100%;
        height: 250px;
        /* Anda bisa mengatur tinggi sesuai kebutuhan */
        overflow-y: auto;
    }

    /* Table Styling */
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;

    }

    table thead {
        background-color: #e51b24;
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
        margin: 10px auto;
        overflow: hidden;
    }

    .card-header {
        background-color: #e51b24;
        /* Warna header tabel */
        color: white;
        padding: 15px;
        text-align: center;
        font-size: 20px;
        /* Ukuran font lebih besar */
        font-weight: bold;
    }

    .card-body {
        display: flex;
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
        width: 100%;
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
        color: #e51b24;
        /* Warna untuk label nilai */
    }

    .card-pie {
        width: 60%;
        background-color: white;
        border-radius: 10px;
        margin: 10px;
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

    h3 {
        color: black;
        padding-top: 10px;
    }
</style>

<div class="panel">
    <div class="button">
        <a href="#" onclick="showHasil('pros')">Proses Perhitungan</a>
        <a href="#" onclick="showHasil('wp')">Metode WP</a>
        <a href="#" onclick="showHasil('saw')">Metode SAW</a>
        <a href="../console/print.php" style="background-color:#ffd700;">Cetak & Simpan</a>
    </div>
    <div id="pros">
        <details class="dropdown-normalisasi">
            <summary>Tabel Ketentuan Bonus</summary>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Nama Area</th>
                            <th>sm</th>
                            <th>m</th>
                            <th>b</th>
                            <th>cb</th>
                            <th>ck</th>
                        </tr>
                    </thead>
                    <?php
                    include '../config/koneksi.php';

                    $sql = mysqli_query($conn, "SELECT * FROM rangking");
                    ?>
                    <tbody>
                        <?php
                        while ($row = mysqli_fetch_array($sql)) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['area']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['sm']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['m']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['b']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['cb']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['ck']) . "</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </details>
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

    <div id="wp">

        <h3>Hasil Penilaian WP</h3>
        <div class="table-responsive">
            <div class="card-rank">
                <div class="card-header">
                    <h5>Karyawan Terbaik</h5>
                </div>

                <div class="card-body">

                    <!-- Elemen untuk Grafik Pie -->
                    <div class="card-pie">
                        <center>
                            <p><strong>Nama:</strong> <?= $topWP['name']; ?></p>

                            <canvas id="myPieChart1" width="200" height="200"></canvas>
                        </center>
                    </div>
                    <ul>
                        <?php foreach ($criterias as $criteria): ?>
                            <li>
                                <strong><?= $criteria['name']; ?> (Value <?= $criteria['id']; ?>):</strong>
                                <?= $topWP['value' . $criteria['id']]; ?>
                            </li>
                        <?php endforeach; ?>
                        <div class="table-container">
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
                                        if ($searchTerm && stripos($alternative['name'], $searchTerm) === false) {
                                            continue;  // Skip alternatif yang tidak cocok dengan pencarian
                                        }
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
                        </div>
                    </ul>
                </div>
            </div>

        </div>
    </div>

    <div id="saw">
        <h3>Hasil Penilaian SAW</h3>
        <div class="table-responsive">
            <div class="card-rank">
                <div class="card-header">
                    <h5>Karyawan Terbaik</h5>
                </div>

                <div class="card-body">
                    <!-- Elemen untuk Grafik Pie -->
                    <div class="card-pie">
                        <center>
                            <p><strong>Nama:</strong> <?= $topWP['name']; ?></p>
                            <canvas id="myPieChart2" width="200" height="200"></canvas>
                        </center>
                    </div>
                    <ul>
                        <?php foreach ($criterias as $criteria): ?>
                            <li>
                                <strong><?= $criteria['name']; ?> (Value <?= $criteria['id']; ?>):</strong>
                                <?= $topWP['value' . $criteria['id']]; ?>
                            </li>
                        <?php endforeach; ?>
                        <div class="table-container">
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
                                        if ($searchTerm && stripos($alternative['name'], $searchTerm) === false) {
                                            continue;
                                        }
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
                        </div>
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
        document.getElementById('pros').style.display = 'none';

        // Show the selected section
        document.getElementById(method).style.display = 'block';
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