<?php

require_once 'app/models/ProductoModel.php';
require_once 'app/models/CategoriaModel.php';

class ProductoApiController {
    private $productoModel;
    private $categoriaModel;

    public function __construct() {
        $this->productoModel = new ProductoModel();
        $this->categoriaModel = new CategoriaModel();
    }

    public function listar() {
        $opciones = $this->leerOpcionesListado();
        $productos = $this->productoModel->obtenerTodos($opciones);
        $total = $this->productoModel->contar($opciones);

        $respuesta = [
            'data' => $productos,
            'total' => $total
        ];

        if ($opciones['paginado']) {
            $respuesta['pagina'] = $opciones['page'];
            $respuesta['limite'] = $opciones['limit'];
        }

        ApiHelper::json($respuesta);
    }

    public function ver($id) {
        $id = ApiHelper::pedirEntero($id, 'id');
        $producto = $this->productoModel->obtenerPorId($id);

        if (!$producto) {
            ApiHelper::json(['error' => 'Producto no encontrado'], 404);
        }

        ApiHelper::json($producto);
    }

    public function crear() {
        ApiHelper::validarToken();
        $data = ApiHelper::body();
        $this->validarProducto($data);

        if (!$this->categoriaModel->obtenerPorId($data['categoria_id'])) {
            ApiHelper::json(['error' => 'La categoria indicada no existe'], 400);
        }

        $id = $this->productoModel->crear(
            trim($data['nombre']),
            $data['precio'],
            $data['categoria_id']
        );

        $producto = $this->productoModel->obtenerPorId($id);
        ApiHelper::json($producto, 201);
    }

    public function editar($id) {
        ApiHelper::validarToken();
        $id = ApiHelper::pedirEntero($id, 'id');

        if (!$this->productoModel->obtenerPorId($id)) {
            ApiHelper::json(['error' => 'Producto no encontrado'], 404);
        }

        $data = ApiHelper::body();
        $this->validarProducto($data);

        if (!$this->categoriaModel->obtenerPorId($data['categoria_id'])) {
            ApiHelper::json(['error' => 'La categoria indicada no existe'], 400);
        }

        $this->productoModel->editar(
            $id,
            trim($data['nombre']),
            $data['precio'],
            $data['categoria_id']
        );

        ApiHelper::json($this->productoModel->obtenerPorId($id));
    }

    private function leerOpcionesListado() {
        $sort = $_GET['sort'] ?? 'producto_id';
        $order = strtoupper($_GET['order'] ?? 'ASC');
        $campos = $this->productoModel->camposParaOrdenar();

        if (!in_array($sort, $campos)) {
            ApiHelper::json([
                'error' => 'Campo de orden invalido',
                'campos_permitidos' => $campos
            ], 400);
        }

        if (!in_array($order, ['ASC', 'DESC'])) {
            ApiHelper::json(['error' => 'El parametro order debe ser ASC o DESC'], 400);
        }

        $opciones = [
            'sort' => $sort,
            'order' => $order,
            'categoria_id' => null,
            'buscar' => trim($_GET['buscar'] ?? ''),
            'paginado' => false,
            'page' => 1,
            'limit' => 0,
            'offset' => 0
        ];

        if (isset($_GET['categoria_id']) && $_GET['categoria_id'] !== '') {
            $opciones['categoria_id'] = ApiHelper::pedirEntero($_GET['categoria_id'], 'categoria_id');
        }

        if (isset($_GET['page']) || isset($_GET['limit'])) {
            $page = ApiHelper::pedirEntero($_GET['page'] ?? 1, 'page');
            $limit = ApiHelper::pedirEntero($_GET['limit'] ?? 10, 'limit');

            if ($page < 1 || $limit < 1) {
                ApiHelper::json(['error' => 'page y limit deben ser mayores a cero'], 400);
            }

            $opciones['paginado'] = true;
            $opciones['page'] = $page;
            $opciones['limit'] = $limit;
            $opciones['offset'] = ($page - 1) * $limit;
        }

        return $opciones;
    }

    private function validarProducto($data) {
        if (empty($data['nombre']) || !isset($data['precio']) || !isset($data['categoria_id'])) {
            ApiHelper::json([
                'error' => 'Faltan datos',
                'campos_requeridos' => ['nombre', 'precio', 'categoria_id']
            ], 400);
        }

        if (!is_numeric($data['precio']) || $data['precio'] < 0) {
            ApiHelper::json(['error' => 'El precio debe ser un numero mayor o igual a cero'], 400);
        }

        if (!ctype_digit((string)$data['categoria_id'])) {
            ApiHelper::json(['error' => 'categoria_id debe ser numerico'], 400);
        }

        $data['categoria_id'] = (int)$data['categoria_id'];
    }
}
