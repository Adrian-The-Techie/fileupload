<?php
    include("config.php");
    //get mixes.
    $mixes=array();
    $sql="SELECT * FROM mix";
    $stmt=$pdo->prepare($sql);
    $stmt->execute();

    //loop through to insert data to mixes array
    $i=0;
    while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
        $mixes[$i]["id"]=$row["id"];
        $mixes[$i]["thumbnailFileName"]=$row["thumbnailName"];
        $mixes[$i]["mixFileName"]=$row["mixName"];
        $mixes[$i]["mixTitleName"]=$row["mixTitleName"];
        $i++;
    }
    echo json_encode(["mixes"=>$mixes]);
?>