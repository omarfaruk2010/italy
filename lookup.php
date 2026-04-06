<?php
header("Content-Type: application/json; charset=utf-8");
header("Cache-Control: no-store, no-cache, must-revalidate");

$id = isset($_GET["id"]) ? trim($_GET["id"]) : "";
$id = preg_replace("/\s+/", "", $id);

if ($id === "") {
    echo json_encode(["found" => false, "error" => "missing_id"]);
    exit;
}

$file = "data.json";
if (!file_exists($file)) {
    echo json_encode(["found" => false]);
    exit;
}

$data = json_decode(file_get_contents($file), true);
if (!is_array($data)) {
    echo json_encode(["found" => false]);
    exit;
}

foreach ($data as $row) {
    $rid = isset($row["id"]) ? (string) $row["id"] : "";
    if ($rid === "" && isset($row["appID"])) {
        $rid = (string) $row["appID"];
    }
    $ridNorm = preg_replace("/\s+/", "", $rid);
    if ($ridNorm !== "" && strcasecmp($ridNorm, $id) === 0) {
        $name = trim(($row["lname"] ?? "") . " " . ($row["fname"] ?? ""));
        if ($name === "") {
            $name = $row["name"] ?? "";
        }
        $st = $row["status"] ?? "";
        if ($st === null || $st === "") {
            $st = "pending";
        } else {
            $st = is_string($st) ? trim($st) : (string) $st;
            if ($st === "") {
                $st = "pending";
            }
        }
        echo json_encode([
            "found" => true,
            "id" => $rid,
            "status" => $st,
            "date" => $row["date"] ?? "",
            "time" => $row["time"] ?? "",
            "name" => $name,
            "passport" => $row["passport"] ?? "",
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

echo json_encode(["found" => false]);
