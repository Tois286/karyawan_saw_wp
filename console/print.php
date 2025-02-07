<?php
// Include dependencies
require_once '../view/criteria.php';
require_once '../view/alternatives.php';

// Get criteria and alternatives
$criterias = getCriterias();
$alternatives = getAlternatives();

// Step 1: Calculate total weight
$totalWeight = array_sum(array_column($criterias, 'weight'));

// Step 2: Calculate criteria weights
$criteriaWeights = [];
foreach ($criterias as $criteria) {
    $criteriaWeights[$criteria['id']] = $criteria['weight'] / $totalWeight;
}

// Step 3: Calculate S vector
$sVector = [];
foreach ($alternatives as $alternative) {
    $s = 1;
    foreach ($criterias as $criteria) {
        $value = $alternative['value' . $criteria['id']];
        $weight = $criteriaWeights[$criteria['id']];
        $s *= ($criteria['type'] === 'benefit') ? pow($value, $weight) : pow($value, -$weight);
    }
    $sVector[$alternative['id']] = $s;
}

// Step 4: Calculate total S
$totalS = array_sum($sVector);

// Step 5: Calculate V vector
$vVector = [];
foreach ($alternatives as $alternative) {
    $vVector[$alternative['id']] = $sVector[$alternative['id']] / $totalS;
}

// Step 6: Rank alternatives based on V vector
arsort($vVector);

// Calculate SAW results
$normalizedMatrix = [];
foreach ($criterias as $criteria) {
    $maxValue = max(array_column($alternatives, 'value' . $criteria['id']));
    $minValue = min(array_column($alternatives, 'value' . $criteria['id']));

    foreach ($alternatives as $alternative) {
        $value = $alternative['value' . $criteria['id']];
        $normalizedMatrix[$alternative['id']][$criteria['id']] = ($criteria['type'] === 'benefit')
            ? $value / $maxValue
            : $minValue / $value;
    }
}

// Calculate final SAW scores
$sawResults = [];
foreach ($alternatives as $alternative) {
    $total = 0;
    foreach ($criterias as $criteria) {
        $total += $criteriaWeights[$criteria['id']] * $normalizedMatrix[$alternative['id']][$criteria['id']];
    }
    $sawResults[$alternative['id']] = $total;
}

// Rank SAW results
arsort($sawResults);

// Get top alternatives for WP and SAW
$topWPId = key($vVector);
$topWP = getAlternative($topWPId);
$topSAWId = key($sawResults);
$topSAW = getAlternative($topSAWId);

// Search functionality
$searchTerm = isset($_POST['search']) ? $_POST['search'] : '';
if ($searchTerm) {
    $alternatives = array_filter($alternatives, function ($alternative) use ($searchTerm) {
        return stripos($alternative['name'], $searchTerm) !== false;
    });
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <title>Save And Print</title>
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
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .button-save {
            color: #fff;
            background-color: #1B9BE5FF;
            /* Warna tombol */
            display: inline-block;
            padding: 12px 20px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            text-decoration: none;
            text-align: center;
            border: none;
            /* Menghilangkan border default */
            cursor: pointer;
            /* Menambahkan pointer saat hover */
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: auto;
            /* Memastikan tombol menyesuaikan dengan kontennya */
        }

        .button-save:hover {
            background-color: #157db0;
            /* Warna latar belakang saat hover */
            transform: scale(1.05);
            /* Efek memperbesar tombol saat hover */
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
            /* Efek bayangan saat hover */
        }

        .button-save:focus {
            outline: none;
            /* Menghapus outline default */
            box-shadow: 0 0 0 4px rgba(27, 155, 229, 0.5);
            /* Efek focus border */
        }

        .button-save:active {
            background-color: #136F99;
            /* Warna latar belakang saat klik */
            transform: scale(1);
            /* Mengembalikan ukuran tombol ke normal */
        }


        .button-print {
            color: #fff;
            background-color: #ffd700;

        }

        .button-back {
            color: #fff;
            background-color: #e51b24;
        }

        .button a:hover {
            background-color: #d41a1f;
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
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            margin: 20px auto;
            overflow: hidden;
        }

        .card-header {
            background-color: #e51b24;
            color: white;
            padding: 15px;
            text-align: center;
            font-size: 20px;
            font-weight: bold;
        }

        .card-body {
            display: flex;
            padding: 20px;
            font-size: 16px;
            color: #333;
        }

        .card-body p {
            margin-bottom: 15px;
            font-size: 18px;
            font-weight: bold;
        }

        .card-body ul {
            list-style-type: none;
            width: 100%;
            padding: 0;
            margin: 0;
        }

        .card-body ul li {
            margin: 8px 0;
            padding: 10px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            color: #555;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .card-body ul li strong {
            color: #e51b24;
        }

        .card-pie {
            width: 60%;
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

        h3 {
            color: black;
            padding-top: 10px;
        }
    </style>
</head>

<body>
    <div class="button">
        <a href="../view/dashboard.php" class="button-back">Back</a>
        <form id="saveForm" method="POST" action="../config/save.php">
            <input type="hidden" name="data" id="tableData">
            <button type="submit" id="saveButton" class="button-save">Save</button>
        </form>
        <a href="#print" class="button-print">print</a>
    </div>
    <div>
        <h3>Hasil Penilaian WP dan SAW</h3>
        <div class="table-responsive">
            <div class="card-rank">
                <div class="card-header">
                    <h5>Karyawan Terbaik</h5>
                </div>
                <div class="card-body">
                    <div class="card-pie">
                        <center>
                            <canvas id="myPieChart1" width="200" height="200"></canvas>
                        </center>
                    </div>
                    <ul>
                        <p><strong>Nama:</strong> <?= htmlspecialchars($topWP['name']); ?></p>
                        <?php foreach ($criterias as $criteria): ?>
                            <li>
                                <strong><?= htmlspecialchars($criteria['name']); ?> (Value <?= $criteria['id']; ?>):</strong>
                                <?= $topWP['value' . $criteria['id']]; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Ranking</th>
                        <th>Nama Karyawan</th>
                        <th>Nilai S</th>
                        <th>Nilai V</th>
                        <th>Nilai Akhir</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $rank = 1;
                    // Create a combined array to hold both WP and SAW results
                    $combinedResults = [];
                    foreach ($vVector as $alternativeId => $value) {
                        $alternative = getAlternative($alternativeId);
                        if ($searchTerm && stripos($alternative['name'], $searchTerm) === false) {
                            continue;
                        }
                        $combinedResults[$alternativeId] = [
                            'id' => htmlspecialchars($alternative['id']),
                            'name' => htmlspecialchars($alternative['name']),
                            'sValue' => $sVector[$alternativeId],
                            'vValue' => $value,
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
                            // If the alternative is not in WP results, create an entry
                            $alternative = getAlternative($alternativeId);
                            if ($searchTerm && stripos($alternative['name'], $searchTerm) === false) {
                                continue;
                            }
                            $combinedResults[$alternativeId] = [
                                'id' => htmlspecialchars($alternative['id']),
                                'name' => htmlspecialchars($alternative['name']),
                                'sValue' => null, // No WP value
                                'vValue' => null, // No WP vector value
                                'wpRank' => null, // No WP rank
                                'finalValue' => $value
                            ];
                        }
                        $rank++;
                    }

                    // Display the combined results in the table
                    foreach ($combinedResults as $result) {
                        echo "<tr>";
                        echo "<td>{$result['id']}</td>";
                        echo "<td>" . ($result['wpRank'] ?? '-') . "</td>";
                        echo "<td>{$result['name']}</td>";
                        echo "<td>" . ($result['sValue'] ?? '-') . "</td>";
                        echo "<td>" . ($result['vValue'] ?? '-') . "</td>";
                        echo "<td>" . ($result['finalValue'] ?? '-') . "</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const createPieChart = (ctx, labels, data) => {
            return new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Values',
                        data: data,
                        backgroundColor: [
                            '#ff4d4d', '#4da6ff', '#ffcc00', '#4dd2b0', '#b366ff', '#ffcc66'
                        ],
                        borderColor: [
                            '#e51b24', '#0073e6', '#ffcc00', '#00994d', '#9933cc', '#ff9900'
                        ],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        title: {
                            display: true,
                            text: 'Grafik Winner'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(tooltipItem) {
                                    const label = tooltipItem.label || '';
                                    const value = tooltipItem.raw || 0;
                                    const total = tooltipItem.dataset.data.reduce((acc, val) => acc + val, 0);
                                    const percentage = ((value / total) * 100).toFixed(2);
                                    return `${label}: ${value} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        };

        const ctx1 = document.getElementById('myPieChart1').getContext('2d');
        createPieChart(ctx1, [<?= implode(',', array_map('json_encode', array_column($criterias, 'name'))) ?>], [<?= implode(',', array_map('json_encode', array_map(fn($c) => $topWP['value' . $c['id']], $criterias))) ?>]);
    </script>
    <script>
        document.querySelector('.button-print').addEventListener('click', function() {
            window.print(); // Mencetak seluruh halaman
        });
        document.getElementById("saveButton").addEventListener("click", function(e) {
            // Ambil data dari tabel
            const tableRows = document.querySelectorAll("table tbody tr");
            const data = [];

            tableRows.forEach(row => {
                const cells = row.querySelectorAll("td");
                const rowData = {
                    id: cells[0]?.innerText.trim() || null,
                    wpRank: cells[1]?.innerText.trim() === '-' ? null : parseInt(cells[1].innerText.trim()),
                    name: cells[2]?.innerText.trim() || null,
                    sValue: cells[3]?.innerText.trim() === '-' ? null : parseFloat(cells[3].innerText.trim()),
                    vValue: cells[4]?.innerText.trim() === '-' ? null : parseFloat(cells[4].innerText.trim()),
                    finalValue: cells[5]?.innerText.trim() === '-' ? null : parseFloat(cells[5].innerText.trim()),
                };
                data.push(rowData);
            });

            // Masukkan data ke input hidden
            const tableDataInput = document.getElementById("tableData");
            tableDataInput.value = JSON.stringify(data);
        });
    </script>
</body>

</html>