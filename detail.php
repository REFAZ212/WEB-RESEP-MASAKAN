<?php
session_start();
require_once 'classes/Favorite.php';
require_once 'classes/Recipe.php';

// Redirect jika pengguna belum login
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$recipeModel = new Recipe();
$favoriteModel = new Favorite();

$recipeId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Ambil detail resep berdasarkan ID
$recipe = $recipeModel->getRecipeById($recipeId);
$ingredients = $recipeModel->getIngredientsByRecipeId($recipeId); // Ambil bahan-bahan
$steps = $recipeModel->getStepsByRecipeId($recipeId); // Ambil langkah-langkah

if (!$recipe) {
    die("Resep tidak ditemukan.");
}

// Tambahkan ke favorit jika form dikirim
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_favorite'])) {
    $userId = $_SESSION['user_id'];
    $result = $favoriteModel->addFavorite($userId, $recipeId);

    if ($result) {
        echo "<script>alert('Resep berhasil ditambahkan ke favorit!');</script>";
    } else {
        echo "<script>alert('Gagal menambahkan ke favorit!');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Resep</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #ff9a8b, #ff6f61);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
            max-width: 800px;
            width: 100%;
            padding: 20px;
            overflow: hidden;
        }
        .container img.main-image {
            width: 100%;
            border-radius: 15px;
            margin-bottom: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .container h1 {
            font-size: 28px;
            color: #333;
            margin-bottom: 15px;
            text-align: center;
        }
        .container p.description {
            font-size: 18px;
            color: #555;
            margin-bottom: 20px;
            text-align: justify;
        }
        .section-title {
            font-size: 22px;
            color: #ff6f61;
            margin: 20px 0 10px;
            border-bottom: 2px solid #ff6f61;
            padding-bottom: 5px;
        }
        .ingredients, .steps {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            justify-content: flex-start;
        }
        .ingredient, .step {
            background: #f9f9f9;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            text-align: center;
            flex: 0 0 calc(33.333% - 15px);
            max-width: calc(33.333% - 15px);
        }
        .ingredient img, .step img {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }
        .ingredient span, .step span {
            display: block;
            padding: 10px;
            font-size: 16px;
            color: #333;
        }
        .container form button {
            background: #ff6f61;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 30px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s ease;
            margin-top: 20px;
        }
        .container form button:hover {
            background: #e65a50;
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
        @media (max-width: 768px) {
            .ingredient, .step {
                flex: 0 0 calc(50% - 15px);
                max-width: calc(50% - 15px);
            }
        }
        @media (max-width: 480px) {
            .ingredient, .step {
                flex: 0 0 100%;
                max-width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Gambar utama resep -->
        <img class="main-image" src="<?php echo htmlspecialchars($recipe['image']); ?>" alt="<?php echo htmlspecialchars($recipe['name']); ?>">
        
        <!-- Nama resep -->
        <h1><?php echo htmlspecialchars($recipe['name']); ?></h1>
        
        <!-- Deskripsi resep -->
        <p class="description"><?php echo htmlspecialchars($recipe['description']); ?></p>

        <!-- Bahan-bahan -->
        <h2 class="section-title">Bahan-Bahan</h2>
        <div class="ingredients">
            <?php if (!empty($ingredients)): ?>
                <?php foreach ($ingredients as $ingredient): ?>
                    <div class="ingredient">
                        <?php if (!empty($ingredient['image'])): ?>
                            <img src="<?php echo htmlspecialchars($ingredient['image']); ?>" alt="Gambar Bahan">
                        <?php endif; ?>
                        <span><?php echo htmlspecialchars($ingredient['name']); ?></span>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Tidak ada bahan yang ditambahkan.</p>
            <?php endif; ?>
        </div>

        <!-- Langkah-langkah -->
        <h2 class="section-title">Langkah-Langkah</h2>
        <div class="steps">
            <?php if (!empty($steps)): ?>
                <?php foreach ($steps as $step): ?>
                    <div class="step">
                        <?php if (!empty($step['image'])): ?>
                            <img src="<?php echo htmlspecialchars($step['image']); ?>" alt="Gambar Langkah">
                        <?php endif; ?>
                        <span><?php echo htmlspecialchars($step['description']); ?></span>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Tidak ada langkah yang ditambahkan.</p>
            <?php endif; ?>
        </div>

        <!-- Tombol tambah ke favorit -->
        <form method="POST">
            <input type="hidden" name="add_to_favorite" value="1">
            <button type="submit">Tambah ke Favorit</button>
        </form>

        <!-- Navigasi footer -->
        <div class="footer">
            <a href="home.php"><i class="fas fa-home"></i></a>
            <a href="favorite.php"><i class="fas fa-heart"></i></a>
            <a href="profile.php"><i class="fas fa-user"></i></a>
        </div>
    </div>
</body>
</html>
