<?php

namespace App\Model;

class Database
{
    // Database Credentials.
    //---------------------------------------------//.
    private $db_name = 'weather_api';
    private $db_user = 'root';
    private $db_pass = 'root';
    private $db_host = 'localhost';

    public \mysqli $connection;

    public function __construct()
    {
        $this->connect();
    }

    protected function connect(): void
    {
        try {
            $this->connection = new \mysqli($this->db_host, $this->db_user, $this->db_pass, $this->db_name);
            if ($this->connection->connect_errno) {
                throw new \Exception($this->connection->connect_error);
            }
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }
}
