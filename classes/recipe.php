<?php
require_once 'classes/db.php';

class Recipe extends Database {

    // Mengambil semua resep dengan pagination (limit dan offset)
    public function getAllRecipes($limit = 10, $offset = 0) {
        $conn = $this->getConnection();
        $query = "SELECT * FROM recipes LIMIT ? OFFSET ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Mengambil detail resep berdasarkan ID
    public function getRecipeById($id) {
        $conn = $this->getConnection();
        $query = "SELECT * FROM recipes WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Mencari resep berdasarkan nama
    public function searchRecipes($query, $userId) {
        $conn = $this->getConnection();
        $search = "%$query%";
        $stmt = $conn->prepare("SELECT * FROM recipes WHERE name LIKE ?");
        $stmt->bind_param("s", $search);
        $stmt->execute();
        $result = $stmt->get_result();

        // Log pencarian jika ada hasil
        if ($result->num_rows > 0) {
            $this->logSearch($userId, $query);
        }

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Log pencarian pengguna
    private function logSearch($userId, $query) {
        $conn = $this->getConnection();
        $stmt = $conn->prepare("INSERT INTO recent_searches (user_id, query) VALUES (?, ?)");
        $stmt->bind_param("is", $userId, $query);
        $stmt->execute();
    }

    // Mengambil pencarian terakhir pengguna
    public function getRecentSearches($userId) {
        $conn = $this->getConnection();
        $stmt = $conn->prepare("
            SELECT recipes.name, recipes.image 
            FROM recent_searches
            JOIN recipes ON recent_searches.query = recipes.name
            WHERE recent_searches.user_id = ?
            ORDER BY recent_searches.id DESC LIMIT 5
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Menambahkan resep baru
    public function addRecipe($name, $description, $image) {
        $conn = $this->getConnection();
        $query = "INSERT INTO recipes (name, description, image) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sss", $name, $description, $image);

        if ($stmt->execute()) {
            return $conn->insert_id; // Kembalikan ID resep yang baru saja ditambahkan
        } else {
            return false;
        }
    }

    // Menambahkan bahan ke dalam database
    public function addIngredient($recipeId, $name, $image = null) {
        $conn = $this->getConnection();
        $query = "INSERT INTO ingredients (recipe_id, name, image) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iss", $recipeId, $name, $image);
        return $stmt->execute();
    }

    // Menambahkan langkah ke dalam database
    public function addStep($recipeId, $description, $image = null) {
        $conn = $this->getConnection();
        $query = "INSERT INTO steps (recipe_id, description, image) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iss", $recipeId, $description, $image);
        return $stmt->execute();
    }

    // Mengambil semua bahan untuk suatu resep
    public function getIngredientsByRecipeId($recipeId) {
        $conn = $this->getConnection();
        $query = "SELECT * FROM ingredients WHERE recipe_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $recipeId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Mengambil semua langkah untuk suatu resep
    public function getStepsByRecipeId($recipeId) {
        $conn = $this->getConnection();
        $query = "SELECT * FROM steps WHERE recipe_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $recipeId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
?>
