<?php
session_start();
require_once 'classes/recipe.php';

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

$recipeModel = new Recipe();
$recipes = $recipeModel->getAllRecipes(); // Mengambil semua resep
$recentSearches = $recipeModel->getRecentSearches($_SESSION['user_id']); // Mendapatkan pencarian terakhir

// Menangani pencarian
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['query'])) {
    $query = $_GET['query'];
    $recipes = $recipeModel->searchRecipes($query, $_SESSION['user_id']);
}


// Fungsi untuk menambahkan bahan dan langkah-langkah
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_recipe'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $uploadDir = 'uploads/';
    $mainImage = '';
    $ingredients = $_POST['ingredients'] ?? [];
    $steps = $_POST['steps'] ?? [];

    // Buat folder uploads jika belum ada
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Upload gambar utama
    if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] === UPLOAD_ERR_OK) {
        $mainImage = $uploadDir . time() . '_' . basename($_FILES['main_image']['name']);
        move_uploaded_file($_FILES['main_image']['tmp_name'], $mainImage);
    }

    // Simpan resep utama
    $recipeId = $recipeModel->addRecipe($name, $description, $mainImage);

    // Simpan bahan
    if (!empty($ingredients)) {
        foreach ($ingredients as $key => $ingredientName) {
            $ingredientImage = '';
            if (isset($_FILES['ingredient_images']['name'][$key]) && $_FILES['ingredient_images']['error'][$key] === UPLOAD_ERR_OK) {
                $ingredientImage = $uploadDir . time() . '_ingredient_' . basename($_FILES['ingredient_images']['name'][$key]);
                move_uploaded_file($_FILES['ingredient_images']['tmp_name'][$key], $ingredientImage);
            }
            $recipeModel->addIngredient($recipeId, $ingredientName, $ingredientImage);
        }
    }

    // Simpan langkah-langkah
    if (!empty($steps)) {
        foreach ($steps as $key => $stepDescription) {
            $stepImage = '';
            if (isset($_FILES['step_images']['name'][$key]) && $_FILES['step_images']['error'][$key] === UPLOAD_ERR_OK) {
                $stepImage = $uploadDir . time() . '_step_' . basename($_FILES['step_images']['name'][$key]);
                move_uploaded_file($_FILES['step_images']['tmp_name'][$key], $stepImage);
            }
            $recipeModel->addStep($recipeId, $stepDescription, $stepImage);
        }
    }

    header("Location: home.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background: #f4f4f9;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        .header img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
        }
        .header .user-name {
            margin-left: 15px;
            font-size: 18px;
            font-weight: bold;
            color: #007BFF;
        }
        .search-bar {
            margin-bottom: 20px;
        }
        .search-bar input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 20px;
        }
        .form-container {
            margin: 20px 0;
            display: none;
        }
        .form-container.active {
            display: block;
        }
        .form-container form {
            display: flex;
            flex-direction: column;
        }
        .form-container form input,
        .form-container form textarea {
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .form-container form button {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
        }
        .recipe-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }
        .recipe-grid div {
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .recipe-grid img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-bottom: 2px solid #f4f4f4;
        }
        .footer {
            display: flex;
            justify-content: space-around;
            margin-top: 20px;
        }
        .footer a {
            color: #ff6f61;
            font-size: 24px;
            text-decoration: none;
        }
        .footer a:hover {
            color: #e65a50;
        }
    </style>
    <script>
        function toggleForm() {
            document.querySelector('.form-container').classList.toggle('active');
        }
        function addIngredient() {
            const container = document.getElementById('ingredients-container');
            const div = document.createElement('div');
            div.innerHTML = `
                <input type="text" name="ingredients[]" placeholder="Bahan" required>
                <input type="file" name="ingredient_images[]" accept="image/*">
            `;
            container.appendChild(div);
        }
        function addStep() {
            const container = document.getElementById('steps-container');
            const div = document.createElement('div');
            div.innerHTML = `
                <textarea name="steps[]" placeholder="Langkah" required></textarea>
                <input type="file" name="step_images[]" accept="image/*">
            `;
            container.appendChild(div);
        }
    </script>
</head>
<body>
<div class="container">
    <div class="header">
        <img src="https://via.placeholder.com/50" alt="User">
        <div class="user-name">Halo, <?php echo htmlspecialchars($_SESSION['username']); ?>!</div>
    </div>

    <!-- Search Bar -->
    <div class="search-bar">
        <form method="GET" action="home.php">
            <input type="text" name="query" placeholder="Cari resep..." value="<?php echo isset($query) ? htmlspecialchars($query) : ''; ?>">
        </form>
    </div>

    <!-- Tombol Tambah Resep -->
    <button onclick="toggleForm()">Tambah Resep</button>

    <!-- Form Tambah Resep -->
    <div class="form-container">
        <form method="POST" enctype="multipart/form-data">
            <input type="text" name="name" placeholder="Nama Masakan" required>
            <textarea name="description" placeholder="Deskripsi Masakan" required></textarea>
            <input type="file" name="main_image" accept="image/*" required>

            <h3>Bahan-Bahan</h3>
            <div id="ingredients-container"></div>
            <button type="button" onclick="addIngredient()">Tambah Bahan</button>

            <h3>Langkah-Langkah</h3>
            <div id="steps-container"></div>
            <button type="button" onclick="addStep()">Tambah Langkah</button>

            <button type="submit" name="add_recipe">Simpan Resep</button>
        </form>
    </div>

    <!-- Rekomendasi Resep -->
    <h2>Rekomendasi Resep</h2>
    <div class="recipe-grid">
    <?php if (!empty($recipes)): ?>
        <?php foreach ($recipes as $recipe): ?>
            <div>
                <a href="detail.php?id=<?php echo $recipe['id']; ?>">
                    <img src="<?php echo htmlspecialchars($recipe['image']); ?>" alt="<?php echo htmlspecialchars($recipe['name']); ?>">
                    <p><?php echo htmlspecialchars($recipe['name']); ?></p>
                </a>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Tidak ada resep ditemukan.</p>
    <?php endif; ?>
    </div>

    <!-- Footer -->
    <div class="footer">
        <a href="home.php"><i class="fas fa-home"></i></a>
        <a href="favorite.php"><i class="fas fa-heart"></i></a>
        <a href="profile.php"><i class="fas fa-user"></i></a>
    </div>
</div>
</body>
</html>
