<?php
    try {
        $conn = new PDO("sqlite:../count.db");
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {
        die("ERROR");
    }
    $query = $conn->prepare('SELECT * FROM huutis_data where data="viewcount"');
    $query->execute([]);
    $row = $query->fetch();
    $query = $conn->prepare('UPDATE huutis_data SET value=? where data="viewcount"');
    $query->execute([(int)$row["value"]+1]);
    echo "<div style='background:black; display:inline-block;'>";
    foreach(str_split($row["value"]) as $char){
        echo '<img style="margin:3px;" src="./num/'.$char.'.jpg" alt="best viewed" height="16" width="16">';
    }
    echo '<img style="margin:3px;" src="./num/vis.jpg" alt="best viewed" height="16" width="auto">';
    echo "</div>";
?>