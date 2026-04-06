<?php
header("Content-Type: application/json; charset=utf-8");

$file = "data.json";
if (!file_exists($file)) {
    echo "[]";
    exit;
}

$raw = file_get_contents($file);
if ($raw === false || trim($raw) === "") {
    echo "[]";
    exit;
}

echo $raw;
