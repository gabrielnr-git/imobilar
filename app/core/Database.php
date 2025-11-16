<?php

namespace Model;

if (!defined("ROOTPATH")) die("Access Denied");

/**
 * Database Trait
 */
Trait Database
{
    // Connect to the database and return the pdo if success and die if failure
    protected function connect() : object{
        try {
            $pdo = new \PDO("mysql:host=".DBHOST.";dbname=".DBNAME, DBUSER, DBPASS);
            $pdo->setAttribute(\PDO::ATTR_ERRMODE,\PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (\PDOException) {
            die("Connection Failed");
        }
    }

    // Query something in the database
    public function query(string $query, array $data = []) : object{
        $pdo = $this->connect();
        $stmt = $pdo->prepare($query);

        if ($stmt->execute($data)) {
            return $stmt;
        } else {
            return false;
        }
    }
}
