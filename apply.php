<?php
header("Content-Type: application/json; charset=utf-8");

$dataFile = "data.json";
if (!file_exists($dataFile)) {
    file_put_contents($dataFile, "[]");
}

$list = json_decode(file_get_contents($dataFile), true);
if (!is_array($list)) {
    $list = [];
}

function generateApplicationId(array $list) {
    $existing = [];
    foreach ($list as $row) {
        if (!empty($row["id"])) {
            $existing[$row["id"]] = true;
        }
        if (!empty($row["appID"])) {
            $existing[$row["appID"]] = true;
        }
    }
    for ($i = 0; $i < 50; $i++) {
        $id = "ITA" . str_pad((string) random_int(0, 999999), 6, "0", STR_PAD_LEFT);
        if (!isset($existing[$id])) {
            return $id;
        }
    }
    return "ITA" . strtoupper(bin2hex(random_bytes(4)));
}

$id = generateApplicationId($list);

$record = $_POST;
if (!is_array($record)) {
    $record = [];
}

$record["id"] = $id;
$record["status"] = "pending";
$record["date"] = date("Y-m-d");
$record["time"] = date("H:i:s");

$uploadDir = "uploads/";
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

if (!empty($_FILES["applicant_photo"]["tmp_name"]) && is_uploaded_file($_FILES["applicant_photo"]["tmp_name"])) {
    $ext = pathinfo($_FILES["applicant_photo"]["name"], PATHINFO_EXTENSION);
    $ext = preg_replace("/[^a-zA-Z0-9]/", "", $ext);
    if ($ext === "") {
        $ext = "jpg";
    }
    $fname = $id . "_photo." . strtolower($ext);
    $dest = $uploadDir . $fname;
    if (move_uploaded_file($_FILES["applicant_photo"]["tmp_name"], $dest)) {
        // Web path with forward slashes (works on Windows + subfolders like /italy/)
        $record["applicant_photo"] = "uploads/" . $fname;
    }
}

array_unshift($list, $record);
file_put_contents($dataFile, json_encode($list, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo json_encode([
    "success" => true,
    "id" => $id,
    "status" => "pending",
], JSON_UNESCAPED_UNICODE);
