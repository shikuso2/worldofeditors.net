<?php

// List maps.

include "../include/db.php";
include "../include/noindex.php";

header("Cache-Control: public, max-age=5, stale-while-revalidate=60");

$term = $_GET["nombre"];

$maps = find(
    "
    select
        maps.*
    from
        maps
    where
        maps.name != '' and
        (
            maps.name          like :term collate nocase or
            maps.description   like :term collate nocase or
            maps.author        like :term collate nocase or
            maps.map_file_name like :term collate nocase
        )
    group by
        maps.map_file_name
    order by
        maps.created_at desc
    limit 150;
    ",
    ["term" => "%" . $term . "%"]
);

echo json_encode($maps);
