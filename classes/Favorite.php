<?php
require_once 'classes/db.php';

class Favorite extends Database {
    public function addFavorite($userId, $recipeId) {
        $conn = $this->getConnection();
        $query = "INSERT INTO favorites (user_id, recipe_id) VALUES (?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $userId, $recipeId);
        return $stmt->execute();
    }

    public function getFavoritesByUser($userId) {
        $conn = $this->getConnection();
        $query = "SELECT recipes.* FROM favorites 
                  JOIN recipes ON favorites.recipe_id = recipes.id 
                  WHERE favorites.user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function removeFavorite($userId, $recipeId) {
        $conn = $this->getConnection();
        $query = "DELETE FROM favorites WHERE user_id = ? AND recipe_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $userId, $recipeId);
        return $stmt->execute();
    }
}
?>
