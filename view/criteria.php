<?php
// File: criteria.php
// Koneksi ke database
require_once 'alternatives.php';
$connection = new mysqli("localhost", "root", "", "sawdanwp");

// Fungsi untuk mendapatkan data kriteria berdasarkan id
function getCriteria($id)
{
    global $connection;

    $query = "SELECT * FROM criteria WHERE id = $id";
    $result = $connection->query($query);

    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    }

    return null;
}


// Fungsi Create
function createCriteria($name, $weight, $type, $status)
{
    global $connection;

    // Ambil semua ID yang sudah ada
    $query = "SELECT id FROM criteria ORDER BY id ASC";
    $result = $connection->query($query);

    $existingIds = [];
    while ($row = $result->fetch_assoc()) {
        $existingIds[] = (int)$row['id'];
    }

    // Cari ID yang kosong dalam rentang 1–10
    $missingId = null;
    for ($i = 1; $i <= 10; $i++) {
        if (!in_array($i, $existingIds)) {
            $missingId = $i;
            break;
        }
    }

    // Tentukan ID yang dipakai
    if ($missingId !== null) {
        // Jika ada ID kosong, pakai itu
        $idToUse = $missingId;
    } else {
        // Jika tidak ada ID kosong, ID baru = ID terbesar + 1
        $idToUse = max($existingIds) + 1;
    }

    // Query insert dengan ID yang sudah ditentukan
    $stmt = $connection->prepare("
        INSERT INTO criteria (id, name, weight, type, status) 
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("isdss", $idToUse, $name, $weight, $type, $status);

    if ($stmt->execute()) {

        // Buat kolom baru di tabel alternatives
        $newColumnName = "value" . $idToUse;
        addColumnToAlternatives($newColumnName, 1);

        echo "<script>
                alert('Data berhasil ditambahkan.');
                window.location.href = '../view/dashboard.php?show=nilai';
              </script>";
    } else {
        echo "<script>
                alert('Terjadi kesalahan: " . $stmt->error . "');
                window.location.href = '../view/dashboard.php?show=nilai';
              </script>";
    }

    return true;
}




// Fungsi Read
function getCriterias()
{
    global $connection;
    $query = "SELECT * FROM criteria";
    $result = $connection->query($query);
    $criterias = [];
    while ($row = $result->fetch_assoc()) {
        $criterias[] = $row;
    }
    return $criterias;
}

// Fungsi Update
function updateCriteria($id, $name, $weight, $type, $status)
{
    global $connection;
    $query = "UPDATE criteria SET name='$name', weight=$weight, type='$type', status ='$status' WHERE id=$id";
    $result = $connection->query($query);
    return $result;
}

// Fungsi Delete
function deleteCriteria($id)
{
    global $connection;
    $query = "DELETE FROM criteria WHERE id=$id";
    $result = $connection->query($query);
    return $result;
}

function getCriteriaNames()
{
    global $connection;

    $query = "SELECT name FROM criteria";
    $result = $connection->query($query);

    if (!$result) {
        die("Gagal mendapatkan data kriteria: " . $connection->error);
    }

    $criteriaNames = [];
    while ($row = $result->fetch_assoc()) {
        $criteriaNames[] = $row['name'];
    }

    return $criteriaNames;
}
