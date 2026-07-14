<?php

class Product {

    private \PDO $conn;
    private $table = "products";


    public function __construct(\PDO $db){

        $this->conn = $db;

    }


    // GET ALL PRODUCTS
    public function read(){

        $query = "SELECT * FROM ".$this->table." ORDER BY id DESC";

        $stmt = $this->conn->prepare($query);

        $stmt->execute();

        return $stmt;

    }



    // GET SINGLE PRODUCT
    public function readOne(int $id){

        $query = "SELECT * FROM ".$this->table."
                  WHERE id = :id
                  LIMIT 1";


        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":id",$id);

        $stmt->execute();


        return $stmt->fetch(PDO::FETCH_ASSOC);

    }



    // CREATE PRODUCT
    public function create(int $category_id, string $name, string $description, float $price, int $stock, string $image){


        $query = "INSERT INTO ".$this->table."
        (category_id,name,description,price,stock,image)

        VALUES

        (:category_id,:name,:description,:price,:stock,:image)";


        $stmt = $this->conn->prepare($query);


        $stmt->bindParam(":category_id",$category_id);
        $stmt->bindParam(":name",$name);
        $stmt->bindParam(":description",$description);
        $stmt->bindParam(":price",$price);
        $stmt->bindParam(":stock",$stock);
        $stmt->bindParam(":image",$image);


        return $stmt->execute();

    }




    // UPDATE PRODUCT
    public function update(int $id, int $category_id, string $name, string $description, float $price, int $stock, string $image){


        $query = "UPDATE ".$this->table."
        SET

        category_id=:category_id,
        name=:name,
        description=:description,
        price=:price,
        stock=:stock,
        image=:image

        WHERE id=:id";


        $stmt = $this->conn->prepare($query);


        $stmt->bindParam(":id",$id);
        $stmt->bindParam(":category_id",$category_id);
        $stmt->bindParam(":name",$name);
        $stmt->bindParam(":description",$description);
        $stmt->bindParam(":price",$price);
        $stmt->bindParam(":stock",$stock);
        $stmt->bindParam(":image",$image);


        return $stmt->execute();

    }




    // DELETE PRODUCT
    public function delete(int $id){


        $query = "DELETE FROM ".$this->table."
        WHERE id=:id";


        $stmt = $this->conn->prepare($query);


        $stmt->bindParam(":id",$id);


        return $stmt->execute();

    }

}

?>