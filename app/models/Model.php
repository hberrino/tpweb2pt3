<?php

class Model {
    protected $db;

    public function __construct() {
        try {
            $this->db = new PDO(
                'mysql:host=' . MYSQL_HOST . ';dbname=' . MYSQL_DB . ';charset=utf8',
                MYSQL_USER,
                MYSQL_PASS
            );
        } catch (PDOException $e) {
            ApiHelper::json(['error' => 'No se pudo conectar a la base de datos'], 500);
        }
    }
}
