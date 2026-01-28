<?php
ini_set("display_errors", 1);

function split_url()
{
    $url = isset($_GET['url']) ? $_GET['url'] : "home";
    $url = explode("/", filter_var(trim($url, "/"), FILTER_SANITIZE_URL));

    return $url;
}

// Create ROOT variable
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$script_name = $_SERVER['SCRIPT_NAME'];
$root = $scheme . "://" . $host . dirname($script_name);

define("ROOT", rtrim($root, "/") . "/");

$URL = split_url();
?>
