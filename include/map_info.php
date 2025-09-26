<?php

include "parse_color_tags.php";

function read_byte(&$string, &$num_byte) 
{
    if (!is_string($string) || $num_byte >= strlen($string))
        return false;

    $tmp = unpack("C",substr($string,$num_byte,1));
    $num_byte++;

    return $tmp[1];
}

function read_uint32($string, &$num_byte) 
{
    if (strlen($string) - $num_byte - 4 < 0)
        return false;

    $tmp = unpack("V",substr($string,$num_byte,4));
    $num_byte += 4;

    return $tmp[1];
}

function get_map_info($map_file_name)
{
    // $map_file_name = "(2)EchoIsles.w3x";
    $map_id           = md5($map_file_name);
    $map_path         = __DIR__ . "/../public/maps/" . $map_file_name;
    $map_path_escaped = escapeshellarg($map_path);
    $map_info_path    = $map_id;

    $map_info_result_path = __DIR__ . "/../public/storage/" . $map_info_path;
    $map_info_result = $map_info_result_path . "/result.json";

    $command =
        "
        mkdir $map_info_result_path;

        MPQExtractor -o $map_info_result_path -e war3mapPreview.tga $map_path_escaped;
        MPQExtractor -o $map_info_result_path -e war3mapMap.tga     $map_path_escaped;
        MPQExtractor -o $map_info_result_path -e war3mapMap.blp     $map_path_escaped;
        MPQExtractor -o $map_info_result_path -e war3map.wts        $map_path_escaped;
        MPQExtractor -o $map_info_result_path -e war3map.w3i        $map_path_escaped;

        BLPConverter -o $map_info_result_path $map_info_result_path/war3mapMap.blp;
        mv $map_info_result_path/war3mapMap.png $map_info_result_path/thumbnail.png;

        convert $map_info_result_path/war3mapMap.tga -flip $map_info_result_path/thumbnail.png;

        convert $map_info_result_path/war3mapPreview.tga -flip $map_info_result_path/thumbnail.png;
        ";

    $output     = [];
    $return_var = 0;

    exec($command, $output, $return_var);

    $map_name               = "";
    $author                 = "";
    $description            = "";
    $players_recommended    = "";

    $wts = file_get_contents($map_info_result_path . "/war3map.wts");
    $w3i = file_get_contents($map_info_result_path . "/war3map.w3i");

    $cursor = 3 * 4;

    while (($s = read_byte($w3i, $cursor))) {
        $map_name .= chr($s);
    }

    while (($s = read_byte($w3i, $cursor))) {
        $author .= chr($s);
    }

    while (($s = read_byte($w3i, $cursor))) {
        $description .= chr($s);
    }

    while (($s = read_byte($w3i, $cursor))) {
        $players_recommended .= chr($s);
    }

    $cursor += 4 * 8; // camera bounds;
    $cursor += 4 * 4; // camera bounds complemenets
    $cursor += 4;     // map playable area w
    $cursor += 4;     // map playable area h

    $map_flags = read_uint32($w3i, $cursor); // map flags

    $cursor += 1; // main ground type
    $cursor += 4; // campaign background number

    // custom loading screen model
    while (($s = read_byte($w3i, $cursor)));

    // loading screen text
    while (($s = read_byte($w3i, $cursor)));

    // loading screen title
    while (($s = read_byte($w3i, $cursor)));

    // loading screen subtitle
    while (($s = read_byte($w3i, $cursor)));

    $cursor += 4; // loading screen number

    // prologue screen path
    while (($s = read_byte($w3i, $cursor)));

    // prologue text
    while (($s = read_byte($w3i, $cursor)));

    // prologue title
    while (($s = read_byte($w3i, $cursor)));

    // prologue subtitle
    while (($s = read_byte($w3i, $cursor)));

    // uses terrain fog, fog start z h, fog end z h, fog density, fog r g b a, global weather
    $cursor += 6 * 4;

    // custom sound environment
    while (($s = read_byte($w3i, $cursor)));

    $cursor += 5; // tileset id, water tinting r g b a

    $max_players = read_uint32($w3i, $cursor);

    $map_name_string            = "STRING " . intval(str_replace("TRIGSTR_", "", $map_name));
    $author_string              = "STRING " . intval(str_replace("TRIGSTR_", "", $author));
    $description_string         = "STRING " . intval(str_replace("TRIGSTR_", "", $description));
    $players_recommended_string = "STRING " . intval(str_replace("TRIGSTR_", "", $players_recommended));

    $map_name_value             = strstr($wts, $map_name_string);
    $author_value               = strstr($wts, $author_string);
    $description_value          = strstr($wts, $description_string);
    $players_recommended_value  = strstr($wts, $players_recommended_string);

    preg_match('/STRING \d+\R{\R(.*?)\R}/s', $map_name_value, $matches);
    $map_name_from_wts = $matches[1];

    preg_match('/STRING \d+\R{\R(.*?)\R}/s', $author_value, $matches);
    $author_from_wts = $matches[1];

    preg_match('/STRING \d+\R{\R(.*?)\R}/s', $description_value, $matches);
    $description_from_wts = $matches[1];

    preg_match('/STRING \d+\R{\R(.*?)\R}/s', $players_recommended_value, $matches);
    $players_recommended_from_wts = $matches[1];

    $result = array(
        "name" => $map_name_from_wts ? $map_name_from_wts : $map_name,
        "author" => $author_from_wts ? $author_from_wts : $author,
        "description" => $description_from_wts ? $description_from_wts : $description,
        "players_recommended" => $players_recommended_from_wts ? $players_recommended_from_wts : $players_recommended,
        "max_players" => $max_players,
        "is_melee" => ($map_flags & 0x0004) == 0x0004,
        "thumbnail" => $map_id . "/thumbnail.png",
    );

    $result["name"] = parse_color_tags($result["name"]);
    $result["author"] = parse_color_tags($result["author"]);
    $result["description"] = parse_color_tags($result["description"]);
    $result["players_recommended"] = parse_color_tags($result["players_recommended"]);

    return $result;
}
