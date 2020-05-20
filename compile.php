<?php

require "config.php";

$body = json_decode(file_get_contents("php://input"), true);

$data = [
    "requests" => [
        "image" => [
            "content" => $body['image']
        ],
        "features" => [
            "type" => "DOCUMENT_TEXT_DETECTION"
        ]
    ]
];

$payload = json_encode($data);
 
$ch = curl_init(getenv("GOOGLE_API_URL") . "?key=" . getenv('GOOGLE_API_TOKEN'));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLINFO_HEADER_OUT, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
 
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json; charset=utf-8",
]);
 
$result = curl_exec($ch);
curl_close($ch);

$result_array = json_decode($result, true);

$filename = bin2hex(random_bytes(16));
file_put_contents('data/' . $filename . '.c', $result_array['responses'][0]['fullTextAnnotation']['text']);

$compilation = shell_exec("gcc data/$filename.c -o bin/$filename");
echo $compilation;

function is_base64($s)
{
    return (bool) preg_match("/^[a-zA-Z0-9\/\r\n+]*={0,2}$/", $s);
}
