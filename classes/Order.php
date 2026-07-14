<?php

class Order
{
    private \PDO $conn;
    private $table = "orders";

    public function __construct(\PDO $db)
    {
        $this->conn = $db;
    }


    // Create order
    public function create(int $user_id, float $total)
    {
        $query = "INSERT INTO " . $this->table . "
                  (user_id, total)
                  VALUES
                  (:user_id, :total)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":total", $total);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }

        return false;
    }


    // Get all orders
    public function read()
    {
        $query = "SELECT 
                    o.id,
                    o.total,
                    o.status,
                    o.created_at,
                    u.name AS customer_name,
                    u.email
                  FROM orders o
                  JOIN users u
                  ON o.user_id = u.id
                  ORDER BY o.id DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }


    // Get orders by user
    public function getByUser(int $user_id)
    {
        $query = "SELECT *
                  FROM " . $this->table . "
                  WHERE user_id = :user_id
                  ORDER BY id DESC";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":user_id", $user_id);

        $stmt->execute();

        return $stmt;
    }


    // Update order status
    public function updateStatus(int $id, string $status)
    {
        $query = "UPDATE " . $this->table . "
                  SET status = :status
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":status", $status);

        return $stmt->execute();
    }


    // Delete order
    public function delete(int $id)
    {
        $query = "DELETE FROM " . $this->table . "
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":id", $id);

        return $stmt->execute();
    }
}

?>