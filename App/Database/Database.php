<?php
/**
 * Created by PhpStorm.
 * User: oleksandrnazarenko
 * Date: 4/23/19
 * Time: 12:37 PM
 */

namespace App\Database;

use App\Config\DbConfig;
use PDO;
use PDOException;

/**
 * Class Database
 * @package App\Database
 */
class Database
{
    use DbConfig;

    /**
     * @return PDO
     */
    public function getConnection(): PDO
    {
        try {
            return new PDO('mysql:host=' . $this->host . ';dbname=' . $this->db_name, $this->username, $this->password);
        } catch (PDOException $exception) {
            echo 'Database error: ' . $exception->getMessage();
        }
    }
}