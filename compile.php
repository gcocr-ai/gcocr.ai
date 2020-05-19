<?php

require "config.php";

$data = json_decode(file_get_contents("php://input"), true);

if (!is_base64($data["image"])) {
    echo "Pls give base64 pls"; die;
}

$data = [
    "requests" => [
        "image" => [
            "content" => $data["image"]
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
    "Content-Type: application/json",
    "Content-Length: " . strlen($payload))
];
 
$result = curl_exec($ch);
curl_close($ch);

function is_base64($s)
{
      return (bool) preg_match("/^[a-zA-Z0-9\/\r\n+]*={0,2}$/", $s);
}