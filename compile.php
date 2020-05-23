<?php

require "config.php";

if (isset($_FILES["fileToUpload"]["name"])) {
    if(isset($_POST["submit"])) {
        $file_location = $_FILES["fileToUpload"]["tmp_name"];
        if(!getimagesize($file_location)) {
            echo 'Not an image, please provide an image.';die;
        }

        $base64_image = base64_encode(file_get_contents($file_location));
    }
}

if (!isset($base64_image)) {
    $body = json_decode(file_get_contents("php://input"), true);

    if (!isset($body['image']) || !is_base64($body['image'])) {
        echo 'Please provide the image in base64.'; die;
    }
}


$data = [
    "requests" => [
        "image" => [
            "content" => isset($base64_image) ? $base64_image : $body['image']
        ],
        "features" => [
            "type" => "DOCUMENT_TEXT_DETECTION"
        ],
        "imageContext" => [
            "languageHints" => ["fi-t-i0-handwrit"]
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

$fixedRES = $result_array['responses'][0]['fullTextAnnotation']['text'];
$fixedRES = preg_replace("/\ \n/", "\n", $fixedRES);
$fixedRES = preg_replace("/j\n/", ";\n", $fixedRES);
$fixedRES = preg_replace("/i\n/", ";\n", $fixedRES);
$fixedRES = preg_replace("/returno\n/", "return 0;\n", $fixedRES);
$fixedRES = preg_replace("/return O;\n/", "return 0;\n", $fixedRES);
$fixedRES = preg_replace("/return o;\n/", "return 0;\n", $fixedRES);
$fixedRES = preg_replace("/return i;\n/", "return 1;\n", $fixedRES);
$fixedRES = preg_replace("/\(\)\n/", "(){\n", $fixedRES);
$fixedRES = preg_replace("/\n[^;]{1,3}\n/", "\n\n", $fixedRES);
$lchr = substr($fixedRES, -1);
if($lchr!="}") $fixedRES.="}";


$dir = __DIR__;
file_put_contents("$dir/data/$filename.c", $fixedRES);

$compilation = shell_exec("gcc $dir/data/$filename.c -o $dir/public/bin/$filename 2>&1");
echo "<div>";
//TODO: simplify this code
if(empty($compilation)){
    echo "<pre>here is program:</pre><br/>";
    echo "<pre><a href='https://recyclr.pro/bin/$filename'>DOWNLOAD BIN</a></pre>";
}else{
    
    echo '
    <script>
    function myFunction() {
        var x = document.getElementById("why");
        var y = document.getElementById("noMore");
        if (x.style.display === "none") {
          x.style.display = "";
          y.style.display = "none";
        } else {
          x.style.display = "none";
          y.style.display = "";
        }
      } 
    </script>
    ';
    echo '<button onclick="myFunction()">Make the error prettier</button>';
    echo "<div id='why'>";
    echo "Some errors occured";
    echo "<pre>".htmlspecialchars($compilation)."</pre><br/>";
    echo "<pre>".htmlspecialchars($result_array['responses'][0]['fullTextAnnotation']['text'])."</pre><br/>";
    echo "<pre>".htmlspecialchars($fixedRES)."</pre>";
    echo "</div>";
    echo "<div id='noMore' style='display:none;'>";
    $printable_extras = ["<"=>"lt",">"=>"gt","å"=>"å","("=>"prl",")"=>"prr","#"=>"ht",'"'=>"qt","'"=>"sq","%"=>"pr",";"=>"hpp",":"=>"pp","+"=>"pl","."=>"pt","^"=>"yv","-"=>"ds",","=>"plk","ä"=>"ä","ö"=>"ö"];
    $array = str_split($compilation);
    foreach($array as $letter){
        if(!empty($printable_extras[strtolower($letter)])){
            echo  '<img src="./ltr/'.$printable_extras[strtolower($letter)].'_0.jpg" alt="" height="20" width="20"> ';
        }elseif(preg_match("/^[a-zA-Z0-9]+$/", $letter)){
            echo  '<img src="./ltr/'.strtolower($letter).'_0.jpg" alt="" height="20" width="20"> ';
        
        }elseif( $letter=="\n"){
            echo "<br/>";
        }else {
            echo htmlspecialchars($letter);
        }
        

    }
    echo "</pre></br>";
    $array = str_split($fixedRES);
    echo "<pre>";
    foreach($array as $letter){
        if(!empty($printable_extras[strtolower($letter)])){
            echo  '<img src="./ltr/'.$printable_extras[strtolower($letter)].'_0.jpg" alt="" height="20" width="20"> ';
        }elseif(preg_match("/^[a-zA-Z0-9]+$/", $letter)){
            echo  '<img src="./ltr/'.strtolower($letter).'_0.jpg" alt="" height="20" width="20"> ';
        
        }elseif($letter=="\n"){
            echo "<br/>";
        }else {
            echo htmlspecialchars($letter);
        }
        

    }
    echo "</pre></br>";
    
    echo "</div>";
}
echo "<pre><a href='https://recyclr.pro/upload.php'>RETURN</a></pre>";
echo "</div>";
//Is this function necessary?
function is_base64($s)
{
    return (bool) preg_match("/^[a-zA-Z0-9\/\r\n+]*={0,2}$/", $s);
}
