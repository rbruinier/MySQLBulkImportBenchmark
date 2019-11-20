<?php

class Database {
    private $connection;

    public function init() {
        $dbHost = "mysql";
        $dbUser = "root";
        $dbPassword = 'password';
        $dbName = "people";

        $this->connection = new mysqli($dbHost, $dbUser, $dbPassword, $dbName);

        if ($this->connection->connect_error) {
            die("Connection failed: " . $this->connection->connect_error);
        }

        mysqli_options($this->connection, MYSQLI_OPT_LOCAL_INFILE, true);
    }

    public function query(string $query) {
        if ($this->connection->query($query) !== TRUE) {
            die("Drop of person table failed: " . $this->connection->error);
        }    
    }
}
