<?php

function get_pdo_connection()
{
    $dsn = "sqlite:" . __DIR__ . "/database.sqlite";

    return new PDO($dsn, null, null, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
}

function find(string $sql, array $params = [], ?PDO $pdo = null)
{
    if (!$pdo)
        $pdo = get_pdo_connection();

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll();
}

function find_one(string $sql, array $params = [], ?PDO $pdo = null)
{
    if (!$pdo)
        $pdo = get_pdo_connection();

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetch();
}

function insert(string $table, array $data, ?PDO $pdo = null)
{
    if (!$pdo)
        $pdo = get_pdo_connection();

    $columns = array_keys($data);
    $placeholders = array_map(fn($column) => ":$column", $columns);

    $sql = "INSERT INTO $table (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $placeholders) . ")";
    $stmt = $pdo->prepare($sql);

    $params = [];
    foreach ($data as $column => $value) {
        $params[":$column"] = $value;
    }

    $stmt->execute($params);

    return $pdo->lastInsertId();
}

function run_query(string $sql, array $params = [], ?PDO $pdo = null)
{
    if (!$pdo)
        $pdo = get_pdo_connection();

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    return $stmt;
}
