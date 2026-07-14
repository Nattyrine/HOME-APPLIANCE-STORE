<?php

class User
{
    private ?\PDO $conn;
    private $table = "users";

    public function __construct(\PDO $db)
    {
        $this->conn = $db;
    }

    // Register new user
    public function register(string $name, string $email, string $passwordHash)
    {
        $query = "INSERT INTO " . $this->table . "
                  (name, email, password_hash)
                  VALUES
                  (:name, :email, :password_hash)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":password_hash", $passwordHash);

        return $stmt->execute();
    }

    // Find user by email
    public function findByEmail(string $email)
    {
        $query = "SELECT * FROM " . $this->table . "
                  WHERE email = :email
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Get user by ID
    public function findById(int $id)
    {
        $query = "SELECT * FROM " . $this->table . "
                  WHERE id = :id
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>