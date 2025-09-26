<?php

include "../include/db.php";
include "../include/map_info.php";
include "../include/noindex.php";

$valid_request = 
    isset($_FILES["file_chunk"]) && 
    isset($_POST["file_name"]) && 
    isset($_POST["chunk"]) && 
    isset($_POST["total_chunks"]);

$invalid_request = !$valid_request;

if ($invalid_request) {
    http_response_code(400);
    exit();
}

// Directory where chunks will be saved
$target_dir     = __DIR__ . "/maps/";
$file_name      = $_POST["file_name"];
$chunk_index    = $_POST["chunk"];
$total_chunks   = $_POST["total_chunks"];

$chunk_tmp_name = $_FILES["file_chunk"]["tmp_name"];
$file_path      = $target_dir . $file_name;

// Check if only 1 chunk required, if so, just move the file.
if ($total_chunks == 1) {
    move_uploaded_file($chunk_tmp_name, $file_path);

    $info = get_map_info($file_name);

    insert("maps", [
        "name" => $info["name"],
        "author" => $info["author"],
        "description" => $info["description"],
        "is_melee" => $info["is_melee"] ? "true" : "false",
        "thumbnail_path" => $info["thumbnail"],
        "map_path" => $file_path,
        "map_file_name" => $file_name,
    ]);

    exit();
}

// Save the chunk to a temporary location
file_put_contents($file_path . ".part" . $chunk_index, file_get_contents($chunk_tmp_name));

// Check if all chunks are uploaded
if ($chunk_index + 1 == $total_chunks) {
    $final_file = fopen($file_path, "wb");

    // Append each chunk to the final file
    for ($i = 0; $i < $total_chunks; $i++) {
        $chunk_file_path = $file_path . ".part" . $i;

        // Open the chunk file in read mode
        $chunk_file = fopen($chunk_file_path, "rb");

        if ($chunk_file) {
            stream_copy_to_stream($chunk_file, $final_file);

            fclose($chunk_file);

            unlink($chunk_file_path);
        }
    }

    fclose($final_file);

    $info = get_map_info($file_name);

    insert("maps", [
        "name" => $info["name"],
        "author" => $info["author"],
        "description" => $info["description"],
        "is_melee" => $info["is_melee"] ? "true" : "false",
        "thumbnail_path" => $info["thumbnail"],
        "map_path" => $file_path,
        "map_file_name" => $file_name,
    ]);
}
