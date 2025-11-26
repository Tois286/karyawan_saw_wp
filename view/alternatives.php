<?php
// File: alternatives.php

// Koneksi ke database
$connection = new mysqli("localhost", "root", "", "sawdanwp");

// Memeriksa koneksi ke database
if ($connection->connect_error) {
    die("Koneksi ke database gagal: " . $connection->connect_error);
}

// Fungsi Create
function createAlternative($name, $values)
{
    global $connection;
    $name = $connection->real_escape_string($name);
    $value1 = $connection->real_escape_string($values[0]);
    $value2 = $connection->real_escape_string($values[1]);
    $value3 = $connection->real_escape_string($values[2]);
    $value4 = $connection->real_escape_string($values[3]);
    $value5 = $connection->real_escape_string($values[4]);


    $query = "INSERT INTO alternatives (name, value1, value2, value3, value4, value5) VALUES ('$name', '$value1', '$value2', '$value3', '$value4', '$value5')";
    $result = $connection->query($query);
    return $result;
}

// Fungsi Read All
function getAlternatives()
{
    global $connection;

    // Mendapatkan nama kolom tabel
    $columns = getTableColumns("alternatives");

    // Membuat query untuk mendapatkan data
    $query = "SELECT * FROM alternatives";
    $result = $connection->query($query);

    if (!$result) {
        die("Gagal mendapatkan data: " . $connection->error);
    }

    $alternatives = [];
    while ($row = $result->fetch_assoc()) {
        $data = [];
        foreach ($columns as $column) {
            $data[$column] = $row[$column];
        }
        $alternatives[] = $data;
    }

    return $alternatives;
}


// Fungsi Update

// Fungsi Update
function updateAlternative($id, $name, $values)
{
    global $connection;
    $name = $connection->real_escape_string($name);

    $escapedValues = array_map(function ($value) use ($connection) {
        return $connection->real_escape_string($value);
    }, $values);

    // Generate SET part of the query
    $setClauses = [];
    foreach ($escapedValues as $index => $value) {
        $setClauses[] = "value" . ($index + 1) . "='$value'";
    }

    $setQuery = implode(', ', $setClauses);

    // Pastikan setQuery tidak kosong
    if (empty($setQuery)) {
        die("Set query is empty. Check values input.");
    }

    $query = "UPDATE alternatives SET name='$name', $setQuery WHERE id=?";
    // Debug output untuk melihat query
    echo "Query: $query"; // Tambahkan ini sebelum prepare
    $stmt = $connection->prepare($query);
    if ($stmt === false) {
        die("Prepare failed: " . $connection->error);
    }

    // Binding parameter
    $stmt->bind_param("i", $id);

    // Execute the statement and check for errors
    if (!$stmt->execute()) {
        die("Execute failed: " . $stmt->error);
    }

    $stmt->close(); // Close statement
    return true; // Return true if successful
}

// Fungsi Delete
function deleteAlternative($id)
{
    global $connection;
    $query = "DELETE FROM alternatives WHERE id=$id";
    $result = $connection->query($query);
    return $result;
}

// Fungsi Read by ID
function getAlternative($id)
{
    global $connection;

    // Menghindari SQL Injection dengan prepared statements
    $stmt = $connection->prepare("SELECT * FROM alternatives WHERE id = ?");
    $stmt->bind_param("i", $id); // 'i' menunjukkan bahwa id adalah integer
    $stmt->execute();
    $result = $stmt->get_result();

    // Mengambil data
    if (
        $result->num_rows > 0
    ) {
        $alternative = $result->fetch_assoc();
    } else {
        $alternative = null; // Atau bisa mengembalikan array kosong
    }

    $stmt->close(); // Tutup statement
    return $alternative;
}

// Fungsi mendapatkan nama alternatif berdasarkan ID
function getAlternativeName($id)
{
    $alternative = getAlternative($id);
    if ($alternative) {
        return $alternative['name'];
    } else {
        return 'Alternatif tidak ditemukan';
    }
}

function getTableColumns($tableName)
{
    global $connection;

    // Mengambil informasi kolom dari tabel
    $query = "SHOW COLUMNS FROM $tableName";
    $result = $connection->query($query);

    if (!$result) {
        die("Gagal mendapatkan kolom tabel: " . $connection->error);
    }

    $columns = [];
    while ($row = $result->fetch_assoc()) {
        $columns[] = $row['Field']; // Field menyimpan nama kolom
    }

    return $columns;
}


function addColumnToAlternatives($columnName, $defaultValue = 1)
{
    global $connection;

    // Cek apakah kolom sudah ada
    if (!columnExists($columnName)) {
        $query = "ALTER TABLE alternatives ADD `$columnName` FLOAT DEFAULT $defaultValue"; // Sesuaikan tipe data jika perlu
        if ($connection->query($query) === TRUE) {
            // Set nilai default untuk semua baris yang ada
            $updateQuery = "UPDATE alternatives SET `$columnName` = $defaultValue WHERE `$columnName` IS NULL";
            $connection->query($updateQuery); // Menetapkan nilai default pada kolom baru
            return true;
        } else {
            die("Gagal menambah kolom: " . $connection->error);
        }
    }
    return false; // Kolom sudah ada
}

function columnExists($columnName)
{
    global $connection;
    $query = "SHOW COLUMNS FROM alternatives LIKE '$columnName'";
    $result = $connection->query($query);
    return $result->num_rows > 0;
}
