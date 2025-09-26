<?php

include "../include/db.php";
include "../include/noindex.php";

run_query(
    "
    CREATE TABLE IF NOT EXISTS maps (
        id SERIAL PRIMARY KEY,
        name TEXT NOT NULL,
        author TEXT NOT NULL,
        description TEXT NOT NULL,
        is_melee BOOLEAN NOT NULL,
        thumbnail_path TEXT NOT NULL,
        map_path TEXT NOT NULL,
        map_file_name TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );
    "
);

run_query(
    "
    CREATE TABLE IF NOT EXISTS games (
        user TEXT NOT NULL,
        map TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );
    "
);

run_query(
    "
    CREATE TABLE IF NOT EXISTS vips (
        user TEXT NOT NULL
    );
    "
);

run_query(
    "
    CREATE TABLE IF NOT EXISTS bans (
        user TEXT NOT NULL
    );
    "
);
