<?php
// File: criteria.php

// Koneksi ke database
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
function createCriteria($name, $weight, $type)
{
    global $connection;

    // Mengecek jumlah entri yang ada di tabel criteria
    $countQuery = "SELECT COUNT(*) AS total FROM criteria";
    $countResult = $connection->query($countQuery);
    $row = $countResult->fetch_assoc();

    // Jika sudah ada 5 entri, jangan izinkan input lebih lanjut
    if ($row['total'] >= 5) {
        echo "<script>
                alert('Anda hanya dapat menginput 5 kriteria saja.');
                window.location.href = '../view/dashboard.php';
              </script>";
        return false;
    }

    // Mencari ID yang kosong dalam rentang 1 sampai 5
    $missingId = null;
    $query = "SELECT id FROM criteria ORDER BY id ASC";
    $result = $connection->query($query);

    $existingIds = [];
    while ($row = $result->fetch_assoc()) {
        $existingIds[] = $row['id'];
    }

    // Cari ID yang kosong
    for ($i = 1; $i <= 5; $i++) {
        if (!in_array($i, $existingIds)) {
            $missingId = $i;
            break;
        }
    }

    // Menentukan ID yang akan digunakan
    if ($missingId !== null) {
        // Jika ada ID kosong, gunakan ID tersebut
        $query = "INSERT INTO criteria (id, name, weight, type) VALUES ($missingId, '$name', $weight, '$type')";
    } else {
        // Jika tidak ada ID kosong, masukkan data seperti biasa
        $query = "INSERT INTO criteria (name, weight, type) VALUES ('$name', $weight, '$type')";
    }

    // Menjalankan query insert
    $result = $connection->query($query);

    if ($result) {
        echo "<script>
                alert('Data berhasil ditambahkan.');
                window.location.href = '../view/dashboard.php';
              </script>";
    } else {
        echo "<script>
                alert('Terjadi kesalahan: " . $connection->error . "');
                window.location.href = '../view/dashboard.php';
              </script>";
    }

    return $result;
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
function updateCriteria($id, $name, $weight, $type)
{
    global $connection;
    $query = "UPDATE criteria SET name='$name', weight=$weight, type='$type' WHERE id=$id";
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
