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
        background-color: #d81d26ff;
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
        margin: 8px;
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
        margin: 10px 0;
    }

    h3 {
        color: black;
        padding-top: 10px;
    }
</style>

<div class="panel  panel-container" style="padding: 20px;margin:20px; box-shadow: 2px 2px 5px #888888;">
    <div class="button">
        <a href="#" type="button" class="btn btn-sm" onclick="showHasil('pros')">Proses Perhitungan</a>
        <!-- <a href="#" type="button" class="btn btn-sm" onclick="showHasil('wp')">Metode WP</a> -->
        <a href="#" type="button" class="btn btn-sm" onclick="showHasil('saw')">Metode SAW</a>
        <?php if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'superAdmin'): ?>
            <a href="../console/print.php" type="button" class="btn btn-sm" style="background-color:#ffd700;">Cetak & Simpan</a>
        <?php endif; ?>
    </div>
    <div id="pros">
        <div class="card-rank" style="padding:10px;background-color:white;">
            <h3>Peringkat Tertinggi Dari Semua Karyawan</h3>

            <p>
                <strong>Metode SAW:</strong>
                <?= isset($topSAW['name']) ? $topSAW['name'] : 'Tidak ada data'; ?>
                dengan nilai <?= isset($sawResults[$topSAWId]) ? number_format($sawResults[$topSAWId], 4) : '0.0000'; ?>
            </p>
        </div>
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

        <details class="dropdown-normalisasi">
            <summary>Ketentuan Status</summary>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>SM</th>
                            <th>M</th>
                            <th>B</th>
                            <th>CB</th>
                            <th>CK</th>
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
                            echo "<td>" . htmlspecialchars($row['sm'] ?? '') . " Org" . "</td>";
                            echo "<td>" . htmlspecialchars($row['m'] ?? '') . " Org" . "</td>";
                            echo "<td>" . htmlspecialchars($row['b'] ?? '') . " Org" . "</td>";
                            echo "<td>" . htmlspecialchars($row['cb'] ?? '') . " Org" . "</td>";
                            echo "<td>" . htmlspecialchars($row['ck'] ?? '') . " Org" . "</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </details>

        <h4>Hasil Perhitungan</h4>
        <div class="table-container">

            <table>
                <thead>
                    <tr>
                        <th>NIK</th>
                        <th>Nama Karyawan</th>
                        <th>Ranking</th>
                        <th>Nilai Akhir SAW</th>
                        <!-- <th>Status</th> -->
                    </tr>
                </thead>
                <tbody>
                    <?php
                    include '../config/koneksi.php';
                    $rank = 1;
                    // Create a combined array to hold both WP and SAW results
                    $combinedResults = [];

                    // Loop untuk WP results
                    foreach ($vVector as $alternativeId => $value) {
                        $alternative = getAlternative($alternativeId);
                        if ($searchTerm && stripos($alternative['name'], $searchTerm) === false) {
                            continue;
                        }
                        $combinedResults[$alternativeId] = [
                            'id' => htmlspecialchars($alternative['id']),
                            'name' => htmlspecialchars($alternative['name']),
                            'wpRank' => $rank,
                            'finalValue' => $value
                        ];
                        $rank++;
                    }

                    // Add SAW results to the combined array
                    $rank = 1;
                    foreach ($sawResults as $alternativeId => $value) {
                        if (isset($combinedResults[$alternativeId])) {
                            $combinedResults[$alternativeId]['sawRank'] = $rank;
                            $combinedResults[$alternativeId]['finalValue'] = $value;
                        } else {
                            $alternative = getAlternative($alternativeId);
                            if ($searchTerm && stripos($alternative['name'], $searchTerm) === false) {
                                continue;
                            }
                            $combinedResults[$alternativeId] = [
                                'id' => htmlspecialchars($alternative['id']),
                                'name' => htmlspecialchars($alternative['name']),
                                'wpRank' => null, // No WP rank
                                'finalValue' => $value
                            ];
                        }
                        $rank++;
                    }

                    //jika nilai sm = 3 maka wpRank 1 sampai dengan 3 mendpatkan status SM
                    $query = "SELECT * FROM rangking";
                    $result = mysqli_query($connection, $query);

                    $data = mysqli_fetch_assoc($result);
                    $sm = $data['sm'] ?? '';
                    $m = $data['m'] ?? '';
                    $b = $data['b'] ?? '';
                    $cb = $data['cb'] ?? '';
                    $ck = $data['ck'] ?? '';


                    // Display the combined results in the table
                    foreach ($combinedResults as $result) {
                        $nama = $result['name'];

                        // Ambil NIK berdasarkan nama
                        $queryNik = "SELECT nik FROM users WHERE nama='$nama'";
                        $resultNik = mysqli_query($connection, $queryNik);
                        $nikData = mysqli_fetch_assoc($resultNik);
                        $nik = $nikData['nik'] ?? '-';

                        echo "<tr>";
                        echo "<td>$nik</td>";
                        echo "<td>{$result['name']}</td>";
                        echo "<td>" . ($result['wpRank'] ?? '-') . "</td>";
                        echo "<td>" . ($result['finalValue'] ?? '-') . "</td>";
                        echo "</tr>";

                        // // Determine status based on wpRank
                        // if (isset($result['wpRank'])) {
                        //     if ($result['wpRank'] >= 1 && $result['wpRank'] <= $sm) {
                        //         $status = 'SM';
                        //     } elseif ($result['wpRank'] > $sm && $result['wpRank'] <= ($sm + $m)) {
                        //         $status = 'M';
                        //     } elseif ($result['wpRank'] > ($sm + $m) && $result['wpRank'] <= ($sm + $m + $b)) {
                        //         $status = 'B';
                        //     } elseif ($result['wpRank'] > ($sm + $m + $b) && $result['wpRank'] <= ($sm + $m + $b + $cb)) {
                        //         $status = 'CB';
                        //     } elseif ($result['wpRank'] > ($sm + $m + $b + $cb) && $result['wpRank'] <= ($sm + $m + $b + $cb + $ck)) {
                        //         $status = 'CK';
                        //     } else {
                        //         $status = $data['status'] ?? 'none'; // Default status
                        //     }
                        // } else {
                        //     $status = $data['status'] ?? 'none'; // Default status if wpRank is not set
                        // }

                        // echo "<td>{$status}</td>"; // Display status
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
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
                            <p><strong>Nama:</strong> <?= $topWP['name'] ?? 'none'; ?></p>
                            <canvas id="myPieChart2" width="200" height="200"></canvas>
                        </center>
                    </div>
                    <ul>
                        <div class="table-container">
                            <?php foreach ($criterias as $criteria): ?>
                                <li>
                                    <strong><?= $criteria['name'] ?? ''; ?> (Value <?= $criteria['id'] ?? ''; ?>):</strong>
                                    <?= $topWP['value' . $criteria['id'] ?? '']; ?>
                                </li>
                            <?php endforeach; ?>
                        </div>
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
                                        if ($searchTerm && stripos($alternative['name'] ?? '', $searchTerm) === false) {
                                            continue;
                                        }
                                        echo "<tr>";
                                        echo "<td>" . $alternative['id'] ?? '' . "</td>";
                                        echo "<td>" . $rank . "</td>";
                                        echo "<td>" . $alternative['name'] ?? '' . "</td>";
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
        document.getElementById('saw').style.display = 'none';
        document.getElementById('pros').style.display = 'none';

        // Show the selected section
        document.getElementById(method).style.display = 'block';
    }
</script>

<script>
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