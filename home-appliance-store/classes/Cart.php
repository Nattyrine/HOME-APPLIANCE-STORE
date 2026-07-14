<?php

class Cart
{
    private \PDO $conn;
    private $table = "order_items";


    public function __construct(\PDO $db)
    {
        $this->conn = $db;
    }


    // Add item to cart/order items
    public function add(int $order_id, int $product_id, int $quantity, float $price): bool
    {
        $query = "INSERT INTO " . $this->table . "
                  (order_id, product_id, quantity, price, subtotal)
                  VALUES
                  (:order_id, :product_id, :quantity, :price, :subtotal)";


        $subtotal = $quantity * $price;

        $stmt = $this->conn->prepare($query);


        $stmt->bindParam(":order_id", $order_id);
        $stmt->bindParam(":product_id", $product_id);
        $stmt->bindParam(":quantity", $quantity);
        $stmt->bindParam(":price", $price);
        $stmt->bindParam(":subtotal", $subtotal);


        return $stmt->execute();
    }



    // Get items of an order
    public function getItems(int $order_id): \PDOStatement
    {
        $query = "SELECT 
                    oi.id,
                    oi.quantity,
                    oi.price,
                    oi.subtotal,
                    p.name,
                    p.image
                  FROM " . $this->table . " oi
                  JOIN products p
                  ON oi.product_id = p.id
                  WHERE oi.order_id = :order_id";


        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":order_id", $order_id);

        $stmt->execute();


        return $stmt;
    }



    // Remove item
    public function remove(int $id): bool
    {
        $query = "DELETE FROM " . $this->table . "
                  WHERE id = :id";


        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":id", $id, \PDO::PARAM_INT);


        return $stmt->execute();
    }



    // Clear order items
    public function clear(int $order_id): bool
    {
        $query = "DELETE FROM " . $this->table . "
                  WHERE order_id = :order_id";


        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":order_id", $order_id);


        return $stmt->execute();
    }

}

?>