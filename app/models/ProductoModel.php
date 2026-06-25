<?php

require_once 'app/models/Model.php';

class ProductoModel extends Model {

    private $camposOrden = [
        'producto_id' => 'p.producto_id',
        'nombre' => 'p.nombre',
        'precio' => 'p.precio',
        'categoria_id' => 'p.categoria_id',
        'categoria_nombre' => 'c.nombre'
    ];

    public function camposParaOrdenar() {
        return array_keys($this->camposOrden);
    }

    public function obtenerTodos($opciones) {
        $where = [];
        $params = [];

        if (!empty($opciones['categoria_id'])) {
            $where[] = 'p.categoria_id = :categoria_id';
            $params['categoria_id'] = $opciones['categoria_id'];
        }

        if (!empty($opciones['buscar'])) {
            $where[] = 'p.nombre LIKE :buscar';
            $params['buscar'] = '%' . $opciones['buscar'] . '%';
        }

        $whereSql = '';
        if (!empty($where)) {
            $whereSql = ' WHERE ' . implode(' AND ', $where);
        }

        $sql = "SELECT p.*, c.nombre AS categoria_nombre
                FROM productos p
                JOIN categorias c ON p.categoria_id = c.categoria_id"
                . $whereSql;

        $orden = $this->camposOrden[$opciones['sort']];
        $direccion = $opciones['order'];
        $sql .= " ORDER BY $orden $direccion";

        if ($opciones['paginado']) {
            $sql .= ' LIMIT :limit OFFSET :offset';
        }

        $query = $this->db->prepare($sql);

        foreach ($params as $key => $value) {
            $query->bindValue(':' . $key, $value);
        }

        if ($opciones['paginado']) {
            $query->bindValue(':limit', $opciones['limit'], PDO::PARAM_INT);
            $query->bindValue(':offset', $opciones['offset'], PDO::PARAM_INT);
        }

        $query->execute();

        return $query->fetchAll(PDO::FETCH_OBJ);
    }

    public function contar($opciones) {
        $where = [];
        $params = [];

        if (!empty($opciones['categoria_id'])) {
            $where[] = 'categoria_id = :categoria_id';
            $params['categoria_id'] = $opciones['categoria_id'];
        }

        if (!empty($opciones['buscar'])) {
            $where[] = 'nombre LIKE :buscar';
            $params['buscar'] = '%' . $opciones['buscar'] . '%';
        }

        $sql = 'SELECT COUNT(*) FROM productos';

        if (!empty($where)) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

        $query = $this->db->prepare($sql);
        $query->execute($params);

        return (int)$query->fetchColumn();
    }

    public function obtenerPorId($id) {
        $query = $this->db->prepare(
            'SELECT p.*, c.nombre AS categoria_nombre
             FROM productos p
             JOIN categorias c ON p.categoria_id = c.categoria_id
             WHERE p.producto_id = ?'
        );
        $query->execute([$id]);

        return $query->fetch(PDO::FETCH_OBJ);
    }

    public function crear($nombre, $precio, $categoriaId) {
        $query = $this->db->prepare(
            'INSERT INTO productos (nombre, precio, categoria_id) VALUES (?, ?, ?)'
        );
        $query->execute([$nombre, $precio, $categoriaId]);

        return (int)$this->db->lastInsertId();
    }

    public function editar($id, $nombre, $precio, $categoriaId) {
        $query = $this->db->prepare(
            'UPDATE productos
             SET nombre = ?, precio = ?, categoria_id = ?
             WHERE producto_id = ?'
        );

        $query->execute([$nombre, $precio, $categoriaId, $id]);
    }
}
