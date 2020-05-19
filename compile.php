<?php

require "config.php";

if (!isset($_GET['image'])) {
    echo 'Pls give some image in base64 pls'; die;
}

if (!is_base64($_GET['image'])) {
    echo "Pls give base64 pls"; die;
}

$data = [
    "requests" => [
        "image" => [
            "content" => $_GET['image']
        ],
        "features" => [
            "type" => "DOCUMENT_TEXT_DETECTION"
        ]
    ]
];

$payload = json_encode($data);
 
$ch = curl_init(getenv("GOOGLE_API_URL"));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLINFO_HEADER_OUT, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
 
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json; charset=utf-8",
    "Authorization: Bearer " . getenv("GOOGLE_API_TOKEN")
]);
 
$result = curl_exec($ch);
curl_close($ch);

$result_array = json_decode($result, true);

$filename = bin2hex(random_bytes(16));
file_put_contents('data/' . $filename, $result_array['responses']['fullTextAnnotation']['text']);

$compilation = shell_exec("gcc data/$filename -o bin/$filename");

function is_base64($s)
{
    return (bool) preg_match("/^[a-zA-Z0-9\/\r\n+]*={0,2}$/", $s);
}