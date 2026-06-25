<?php

require_once 'app/models/Model.php';

class CategoriaModel extends Model {

    public function obtenerTodas() {
        $query = $this->db->prepare('SELECT * FROM categorias ORDER BY nombre ASC');
        $query->execute();

        return $query->fetchAll(PDO::FETCH_OBJ);
    }

    public function obtenerPorId($id) {
        $query = $this->db->prepare('SELECT * FROM categorias WHERE categoria_id = ?');
        $query->execute([$id]);

        return $query->fetch(PDO::FETCH_OBJ);
    }
}
