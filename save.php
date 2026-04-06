<?php
header('Content-Type: application/json');

// Upload folder
$uploadDir = "uploads/";
if(!is_dir($uploadDir)){
    mkdir($uploadDir, 0777, true);
}

// JSON file
$dataFile = "data.json";
if(!file_exists($dataFile)){
    file_put_contents($dataFile, json_encode([]));
}
$data = json_decode(file_get_contents($dataFile), true);
if(!$data) $data = [];

// POST data
$name = $_POST['name'] ?? '';
$passport = $_POST['passport'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';

// Application ID
$appID = "APP-" . time();

// File uploads
$photoURL = "";
$passportURL = "";
$cvURL = "";

if(isset($_FILES['photo']) && $_FILES['photo']['error'] === 0){
    $photoName = $appID . "_photo_" . basename($_FILES['photo']['name']);
    move_uploaded_file($_FILES['photo']['tmp_name'], $uploadDir.$photoName);
    $photoURL = $uploadDir.$photoName;
}

if(isset($_FILES['passportFile']) && $_FILES['passportFile']['error'] === 0){
    $passportName = $appID . "_passport_" . basename($_FILES['passportFile']['name']);
    move_uploaded_file($_FILES['passportFile']['tmp_name'], $uploadDir.$passportName);
    $passportURL = $uploadDir.$passportName;
}

if(isset($_FILES['cv']) && $_FILES['cv']['error'] === 0){
    $cvName = $appID . "_cv_" . basename($_FILES['cv']['name']);
    move_uploaded_file($_FILES['cv']['tmp_name'], $uploadDir.$cvName);
    $cvURL = $uploadDir.$cvName;
}

// New record
$new = [
    "appID" => $appID,
    "name" => $name,
    "passport" => $passport,
    "email" => $email,
    "phone" => $phone,
    "photo" => $photoURL,
    "passportFile" => $passportURL,
    "cv" => $cvURL,
    "status" => "pending",
    "date" => date("Y-m-d"),
    "time" => date("H:i:s")
];

array_unshift($data, $new);
file_put_contents($dataFile, json_encode($data, JSON_PRETTY_PRINT));

// Response
echo json_encode([
    "status" => "success",
    "appID" => $appID,
    "photo" => $photoURL,
    "passportFile" => $passportURL,
    "cv" => $cvURL
]);
?>