<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #36d1dc, #5b86e5);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background-color: #fff;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 100%;
            text-align: center;
        }
        .container h1 {
            font-size: 28px;
            color: #333;
            margin-bottom: 20px;
        }
        .profile-img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background-color: #ddd;
            margin: 0 auto 20px;
        }
        .profile-img img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
        }
        .user-info {
            margin-bottom: 20px;
            font-size: 16px;
            color: #555;
        }
        .user-info span {
            font-weight: bold;
        }
        .btn {
            background-color: #36d1dc;
            color: #fff;
            padding: 12px 25px;
            border: none;
            border-radius: 30px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .btn:hover {
            background-color: #2ca7b9;
        }
        .footer {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }
        .footer a {
            color: #36d1dc;
            text-decoration: none;
            font-size: 20px;
        }
        .footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="profile-img">
            <img src="https://via.placeholder.com/100" alt="User Profile Picture">
        </div>
        <h1>Selamat datang, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
        <div class="user-info">
            <p><span>Username:</span> <?php echo htmlspecialchars($_SESSION['username']); ?></p>
        <button class="btn">Edit Profil</button>
        <div class="footer">
            <a href="home.php"><i class="fas fa-home"></i> Home</a>
            <a href="favorite.php"><i class="fas fa-heart"></i> Favorit</a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>
</body>
</html>
