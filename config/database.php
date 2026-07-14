<?php

class Database {

    private $host = "localhost";
    private $db_name = "home_appliance_store";
    private $username = "root";
    private $password = "";

    public function connect(){

        try {

            $conn = new PDO(
                "mysql:host=".$this->host.
                ";dbname=".$this->db_name.
                ";charset=utf8",
                $this->username,
                $this->password
            );

            $conn->setAttribute(
                PDO::ATTR_ERRMODE,
                PDO::ERRMODE_EXCEPTION
            );

            return $conn;

        } catch(PDOException $e){

            die("Connection failed: ".$e->getMessage());

        }
    }
}
$db = new Database();
$conn = $db->connect();
?>