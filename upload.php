<?php
include ("config.php");
//upload mix
    $mixName=$_FILES['mix']['name'];
    $mixTmpName=$_FILES["mix"]['tmp_name'];
    $message=array();
    if($_FILES['mix']['error'] === 0){
        $getMixFileExt=explode(".", $mixName);
        $mixFileExt = end($getMixFileExt);
        $newMixFileName = uniqid("", true).".".$mixFileExt;
        $mixFileDestination="mixes/mixes/".$newMixFileName;
        $mixUploadResult=move_uploaded_file($mixTmpName, $mixFileDestination);
    }
    else{
        $message=["response"=>"Error uploading mix file"];
    }

    //upload Thumbnail
    $thumbnailName=$_FILES['thumbnail']['name'];
    $thumbnailTmpName=$_FILES["thumbnail"]['tmp_name'];
    if($_FILES['thumbnail']['error'] === 0){
        $getThumbnailFileExt=explode(".", $thumbnailName);
        $thumbnailFileExt = end($getThumbnailFileExt);
        $newThumbnailFileName = uniqid("", true).".".$thumbnailFileExt;
        $thumbnailFileDestination="mixes/thumbnails/".$newThumbnailFileName;
        $thumbnailUploadResult=move_uploaded_file($thumbnailTmpName, $thumbnailFileDestination);
    }
    else{
        $message=["response"=>"Error uploading thumbnail"];
    }
    // store in database
    $mixName=$_POST['name'];
    $sql="INSERT INTO mix(thumbnailName, mixName, mixTitleName) VALUES(:thumbnail, :mix, :name)";
    $stmt=$pdo->prepare($sql);
    $stmt->bindParam(":thumbnail", $thumbnailFileDestination);
    $stmt->bindParam(":mix", $mixFileDestination);
    $stmt->bindParam(":name", $mixName);


    $databaseStoreResult= $stmt->execute();


    if($mixUploadResult == 1 && $thumbnailUploadResult == 1 && $databaseStoreResult == 1){
        $message=["response"=>"Mix uploaded Successfully"];
    } else{
        $message=["response"=>"Mix did not upload Successfully. Please Try Again"];
    }
        
    echo json_encode($message);

?>