<?php

require_once "Person.php";

class Customer extends Person
{
    public function getRole()
    {
        return "Customer";
    }
}
?>