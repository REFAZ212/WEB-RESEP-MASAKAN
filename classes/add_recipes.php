<?php
require_once 'classes/db.php'; // Pastikan sudah ada kelas Database

session_start();

// Pastikan pengguna sudah login
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Koneksi ke database
$db = new Database();
$conn = $db->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Data resep yang ingin ditambahkan
    $recipes = [
        ['Bakso', 'assets/background.jpg', 'Bakso adalah bola daging yang dimasak dalam kuah kaldu, biasanya disajikan dengan mie atau nasi.'],
        ['Capcay', 'https://example.com/images/capcay.jpg', 'Capcay adalah sayuran tumis yang disajikan dengan berbagai jenis protein seperti ayam, udang, atau daging sapi.'],
        ['Ayam Goreng Kremes', 'https://example.com/images/ayam-goreng-kremes.jpg', 'Ayam goreng yang dilapisi dengan kremesan gurih dan disajikan dengan sambal pedas.'],
        ['Pempek', 'https://example.com/images/pempek.jpg', 'Pempek adalah makanan khas Palembang yang terbuat dari ikan, tepung, dan dimasak dengan cara digoreng atau direbus.'],
        ['Gulai Ikan', 'https://example.com/images/gulai-ikan.jpg', 'Gulai ikan adalah ikan yang dimasak dengan kuah kental berbumbu rempah khas Indonesia.'],
        ['Tahu Tempe Goreng', 'https://example.com/images/tahu-tempe-goreng.jpg', 'Tahu dan tempe goreng dengan bumbu khas yang renyah di luar dan lembut di dalam.'],
        ['Martabak Manis', 'https://example.com/images/martabak-manis.jpg', 'Martabak manis adalah kue berisi cokelat, kacang, keju, dan susu kental manis, dengan tekstur lembut di dalam.']
    ];

    // Menambahkan setiap resep ke dalam tabel recipes
    foreach ($recipes as $recipe) {
        // Debug: Menampilkan data yang akan dimasukkan
        echo "Menambahkan resep: " . $recipe[0] . "<br>";

        $query = "INSERT INTO recipes (name, image, description) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);

        // Debug: Periksa jika query berhasil dipersiapkan
        if ($stmt === false) {
            echo "Error pada prepare statement: " . $conn->error . "<br>";
        }

        $stmt->bind_param("sss", $recipe[0], $recipe[1], $recipe[2]);

        // Eksekusi query
        if ($stmt->execute()) {
            echo "Resep berhasil ditambahkan: " . $recipe[0] . "<br>";
        } else {
            // Debug: Tampilkan pesan error jika gagal eksekusi
            echo "Gagal menambahkan resep: " . $stmt->error . "<br>";
        }

        // Pastikan statement ditutup setelah eksekusi
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Resep Baru</title>
</head>
<body>
    <h1>Tambah Resep Baru</h1>
    <form method="POST">
        <button type="submit">Tambah 7 Resep</button>
    </form>
</body>
</html>
