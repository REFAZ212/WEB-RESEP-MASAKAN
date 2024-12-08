<?php
session_start();
require_once 'classes/Favorite.php';

// Periksa login
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Inisialisasi model Favorite
$favoriteModel = new Favorite();
$userId = $_SESSION['user_id'];

// Tambahkan penghapusan favorit jika ada permintaan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['recipe_id']) && isset($_POST['action'])) {
    $recipeId = intval($_POST['recipe_id']);
    if ($_POST['action'] === 'remove') {
        $favoriteModel->removeFavorite($userId, $recipeId);
    }
}

// Ambil daftar favorit pengguna
$favorites = $favoriteModel->getFavoritesByUser($userId);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Favorit Saya</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        .container {
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        .header img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #2c3e50;
        }
        .header .user-name {
            margin-left: 10px;
            font-size: 18px;
        }
        .recipe-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-bottom: 20px;
        }
        .recipe-grid img {
            width: 100%;
            border-radius: 10px;
        }
        .recipe-grid .recipe-name {
            text-align: center;
            font-size: 14px;
            margin-top: 5px;
        }
        .footer {
            display: flex;
            justify-content: space-around;
            padding: 10px 0;
            border-top: 1px solid #ccc;
        }
        .footer i {
            font-size: 24px;
        }
        .remove-button {
            background-color: red;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 5px 10px;
            margin-top: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>
<div class="container">
    <!-- Header -->
    <div class="header">
        <img alt="User profile picture" src="https://via.placeholder.com/40" width="40" height="40"/>
        <div class="user-name">Halo, <?php echo htmlspecialchars($_SESSION['username']); ?>!</div>
    </div>

    <!-- Daftar Resep Favorit -->
    <h1>Resep Favorit</h1>
    <?php if (empty($favorites)): ?>
        <p>Belum ada resep favorit.</p>
    <?php else: ?>
        <div class="recipe-grid">
            <?php foreach ($favorites as $favorite): ?>
                <div>
                    <a href="detail.php?id=<?php echo $favorite['id']; ?>">
                        <img src="<?php echo htmlspecialchars($favorite['image']); ?>" alt="<?php echo htmlspecialchars($favorite['name']); ?>">
                        <div class="recipe-name"><?php echo htmlspecialchars($favorite['name']); ?></div>
                    </a>
                    <form method="POST" style="text-align: center;">
                        <input type="hidden" name="recipe_id" value="<?php echo $favorite['id']; ?>">
                        <input type="hidden" name="action" value="remove">
                        <button type="submit" class="remove-button">Hapus</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Footer Navigation -->
    <div class="footer">
        <a href="home.php"><i class="fas fa-home"></i></a>
        <a href="favorite.php"><i class="fas fa-heart"></i></a>
        <a href="profile.php"><i class="fas fa-user"></i></a>
    </div>
</div>
</body>
</html>
