<?php

namespace App;

use App\Config;
use PDO;
use PDOException;

class Database {
    private $db_port;
    private $db_host;
    private $db_name;
    private $db_user;
    private $db_password;

    public function __construct() {
        $this->db_port = Config::Get('DB_PORT', 3306);
        $this->db_host = Config::Get('DB_HOST');
        $this->db_name = Config::Get('DB_NAME');
        $this->db_user = Config::Get('DB_USER');
        $this->db_password = Config::Get('DB_PASSWORD');
    }

    public function connect($dieOnFailure = true) {
        $connection = "mysql:host={$this->db_host};port={$this->db_port};dbname={$this->db_name};charset=utf8";

        try {
            $db = new PDO($connection, $this->db_user, $this->db_password);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $db;
        } catch(PDOException $e) {
            if ($dieOnFailure) {
                die('Connection failed: ' . $e->getMessage());
            } else {
                throw $e;
            }
        }
    }
}
?>
