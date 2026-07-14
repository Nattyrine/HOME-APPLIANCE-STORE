<?php

abstract class Person
{
    protected string $name;
    protected string $email;

    public function __construct(string $name, string $email)
    {
        $this->name = $name;
        $this->email = $email;
    }

    abstract public function getRole();
}
?>