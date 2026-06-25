<?php

class ApiHelper {

    public static function json($data, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public static function body() {
        $raw = file_get_contents('php://input');

        if ($raw === '') {
            return [];
        }

        $data = json_decode($raw, true);

        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            self::json(['error' => 'El JSON enviado no es valido'], 400);
        }

        return $data;
    }

    public static function validarToken() {
        $headers = function_exists('getallheaders') ? getallheaders() : [];
        $token = $headers['X-API-Token'] ?? $headers['x-api-token'] ?? null;
        $auth = $headers['Authorization'] ?? $headers['authorization'] ?? '';

        if ($token === null && stripos($auth, 'Bearer ') === 0) {
            $token = trim(substr($auth, 7));
        }

        if ($token !== API_TOKEN) {
            self::json(['error' => 'Token invalido o ausente'], 401);
        }
    }

    public static function pedirEntero($valor, $nombre) {
        if ($valor === null || $valor === '' || !ctype_digit((string)$valor)) {
            self::json(['error' => "El parametro $nombre debe ser numerico"], 400);
        }

        return (int)$valor;
    }
}
