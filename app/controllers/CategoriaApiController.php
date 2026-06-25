<?php

require_once 'app/models/CategoriaModel.php';

class CategoriaApiController {
    private $model;

    public function __construct() {
        $this->model = new CategoriaModel();
    }

    public function listar() {
        ApiHelper::json([
            'data' => $this->model->obtenerTodas()
        ]);
    }

    public function ver($id) {
        $id = ApiHelper::pedirEntero($id, 'id');
        $categoria = $this->model->obtenerPorId($id);

        if (!$categoria) {
            ApiHelper::json(['error' => 'Categoria no encontrada'], 404);
        }

        ApiHelper::json($categoria);
    }
}
