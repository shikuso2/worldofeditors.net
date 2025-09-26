<?php

function in_post($param_names)
{
    foreach ($param_names as $param_name) {
        if (!isset($_POST[$param_name])) {
            return false;
        }
    }

    return true;
}

function any_in_post($param_names)
{
    foreach ($param_names as $param_name) {
        if (isset($_POST[$param_name])) {
            return true;
        }
    }

    return false;
}

function post_value($param_name, $default_value = null)
{
    if (isset($_POST[$param_name]) && $_POST[$param_name]) {
        return $_POST[$param_name];
    }

    return $default_value;
}
