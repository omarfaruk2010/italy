<?php

$file = "data.json";
if (!file_exists($file)) {
    echo "no data";
    exit;
}

$data = json_decode(file_get_contents($file), true);
if (!is_array($data)) {
    echo "invalid";
    exit;
}

$status = $_POST["status"] ?? "";
$id = isset($_POST["id"]) ? preg_replace("/\s+/", "", trim($_POST["id"])) : "";
$passport = isset($_POST["passport"]) ? trim($_POST["passport"]) : "";

if ($status === "") {
    echo "missing status";
    exit;
}

$updated = false;
foreach ($data as &$item) {
    if ($id !== "") {
        $rowId = isset($item["id"]) ? (string) $item["id"] : "";
        if ($rowId === "" && isset($item["appID"])) {
            $rowId = (string) $item["appID"];
        }
        $rowNorm = preg_replace("/\s+/", "", $rowId);
        if ($rowNorm !== "" && strcasecmp($rowNorm, $id) === 0) {
            $item["status"] = $status;
            $updated = true;
            break;
        }
    } elseif ($passport !== "" && ($item["passport"] ?? "") === $passport) {
        $item["status"] = $status;
        $updated = true;
        break;
    }
}
unset($item);

if ($updated) {
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

echo $updated ? "updated" : "not found";
