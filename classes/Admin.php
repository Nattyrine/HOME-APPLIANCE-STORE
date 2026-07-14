<?php

require_once "Person.php";

class Admin extends Person
{
    public function getRole()
    {
        return "Administrator";
    }

    public function manageProducts()
    {
        return "Admin can manage products.";
    }
}
?>