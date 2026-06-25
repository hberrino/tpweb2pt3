<?php

require_once 'config.php';
require_once 'app/helpers/ApiHelper.php';
require_once 'app/controllers/ProductoApiController.php';
require_once 'app/controllers/CategoriaApiController.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-API-Token');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$resource = trim($_GET['resource'] ?? '', '/');

if ($resource === '') {
    ApiHelper::json([
        'mensaje' => 'API Goth Store',
        'endpoints' => [
            'GET /api/productos',
            'GET /api/productos/{id}',
            'POST /api/productos',
            'PUT /api/productos/{id}',
            'GET /api/categorias',
            'GET /api/categorias/{id}'
        ]
    ]);
}

$partes = explode('/', $resource);
$recurso = $partes[0] ?? '';
$id = $partes[1] ?? null;
$method = $_SERVER['REQUEST_METHOD'];

switch ($recurso) {
    case 'productos':
        $controller = new ProductoApiController();

        if ($method === 'GET' && $id === null) {
            $controller->listar();
        } elseif ($method === 'GET') {
            $controller->ver($id);
        } elseif ($method === 'POST' && $id === null) {
            $controller->crear();
        } elseif ($method === 'PUT' && $id !== null) {
            $controller->editar($id);
        } else {
            ApiHelper::json(['error' => 'Metodo o ruta no disponible'], 404);
        }
        break;

    case 'categorias':
        $controller = new CategoriaApiController();

        if ($method === 'GET' && $id === null) {
            $controller->listar();
        } elseif ($method === 'GET') {
            $controller->ver($id);
        } else {
            ApiHelper::json(['error' => 'Metodo o ruta no disponible'], 404);
        }
        break;

    default:
        ApiHelper::json(['error' => 'Recurso no encontrado'], 404);
}
