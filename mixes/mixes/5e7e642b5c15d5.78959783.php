<?php
session_start();
require "config.php";

class DbHandler{
    
    public function __construct(){
        $this->connection= new Connection();
    }

    function getMaleContestants(){
        $maleContestants=[];
        $sql="SELECT
                id, thumbnail, name, gender, course, yearOfStudy
                FROM 
                malecontestants";
        $stmt=$this->connection->connectToDb()->prepare($sql);
        $stmt->execute();
        
        $i=0;
        while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
            $maleContestants[$i]["id"]=$row["id"];
            $maleContestants[$i]["thumbnail"]=$row["thumbnail"];
            $maleContestants[$i]["name"]=$row["name"];
            $maleContestants[$i]["gender"]=$row["gender"];
            $maleContestants[$i]["course"]=$row["course"];
            $maleContestants[$i]["yearOfStudy"]=$row["yearOfStudy"];

            $i++;
        }
        return json_encode($maleContestants);
    }

    function getFemaleContestants(){
        $femaleContestants=[];
        $sql="SELECT
                id, thumbnail, name, gender, course, yearOfStudy
                FROM 
                femalecontestants";
        $stmt=$this->connection->connectToDb()->prepare($sql);
        $stmt->execute();
        
        $i=0;
        while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
            $femaleContestants[$i]["id"]=$row["id"];
            $femaleContestants[$i]["thumbnail"]=$row["thumbnail"];
            $femaleContestants[$i]["name"]=$row["name"];
            $femaleContestants[$i]["gender"]=$row["gender"];
            $femaleContestants[$i]["course"]=$row["course"];
            $femaleContestants[$i]["yearOfStudy"]=$row["yearOfStudy"];

            $i++;
        }
        return json_encode($femaleContestants);
    }

    function vote($maleContestant, $femaleContestant){
        if(!isset($_SESSION["vote"])){
            $sessionId=session_id();
            $sql="INSERT INTO votinghistory VALUES (:voter_id, :maleContestant, :femaleContestant)";
            $stmt=$this->connection->connectToDb()->prepare($sql);
            $stmt->bindParam(":voter_id", $sessionId);
            $stmt->bindParam(":maleContestant", $maleContestant);
            $stmt->bindParam(":femaleContestant", $femaleContestant);
            $stmt->execute();

            $_SESSION["vote"]=true;
            return json_encode($sessionId);
        }
    }

    // ADMIN SQL QUERIES
    
    function login($data){
        $details=[];
        $sql="SELECT password FROM admin WHERE username= :username";
        $stmt=$this->connection->connectToDb()->prepare($sql);
        $stmt->bindParam(":username", $data->username);
        $stmt->execute();
        
        $counter=0;
        while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
            $details[$counter]["password"]=$row["password"];
        }
        if(count($details)==1){
            return json_encode(session_id());
        }
    }

    function addContestant($data){
        $response=[];
        if($data->gender=="male"){
           $sql="INSERT INTO malecontestants (name, gender, course, yearOfStudy)
                VALUES (:name, :gender, :course, :yearOfStudy, :briefDescription)";
            $stmt=$this->connection->connectToDb()->prepare($sql);
            $stmt->bindParam(":name", $data->name);
            $stmt->bindParam(":gender", $data->gender);
            $stmt->bindParam(":course", $data->course);
            $stmt->bindParam(":yearOfStudy", $data->yearOfStudy);

            $result=$stmt->execute();

            if($result==true){
                $response=["response"=>"{$data->name} added successfuly"];
            } else{
                $response=["response"=>"{$data->name} not added successfully. Please try again"];
            }
        }
        else {
            $sql="INSERT INTO femalecontestants (name, gender, course, yearOfStudy, briefDescription)
                 VALUES (:name, :gender, :course, :yearOfStudy, :briefDescription)";
             $stmt=$this->connection->connectToDb()->prepare($sql);
             $stmt->bindParam(":name", $data->name);
             $stmt->bindParam(":gender", $data->gender);
             $stmt->bindParam(":course", $data->course);
             $stmt->bindParam(":yearOfStudy", $data->yearOfStudy);
             $result=$stmt->execute();
 
             if($result==true){
                 $response=["response"=>"{$data->name} added successfuly"];
             } else{
                 $response=["response"=>"{$data->name} not added successfully. Please try again"];
             }
         }
        return json_encode($response);
    }


    function getAllContestants(){
        $maleContestants=[];
        $femaleContestants=[];
        $maleContestantsSql="SELECT * FROM malecontestants";
        $femaleContestantsSql="SELECT * FROM femalecontestants";

        $maleStmt=$this->connection->connectToDb()->prepare($maleContestantsSql);
        $femaleStmt=$this->connection->connectToDb()->prepare($femaleContestantsSql);

        $maleStmt->execute();
        $femaleStmt->execute();

        //get male contestants
        $counter=0;
        while($row=$maleStmt->fetch(PDO::FETCH_ASSOC)){
            $maleContestants[$counter]["id"]=$row["id"];
            $maleContestants[$counter]["thumbnail"]=$row["thumbnail"];
            $maleContestants[$counter]["name"]=$row["name"];
            $maleContestants[$counter]["gender"]=$row["gender"];
            $maleContestants[$counter]["course"]=$row["course"];
            $maleContestants[$counter]["yearOfStudy"]=$row["yearOfStudy"];

            $counter++;
        }

        //get female contestants
        $counter=0;
        while($row=$femaleStmt->fetch(PDO::FETCH_ASSOC)){
            $femaleContestants[$counter]["id"]=$row["id"];
            $femaleContestants[$counter]["thumbnail"]=$row["thumbnail"];
            $femaleContestants[$counter]["name"]=$row["name"];
            $femaleContestants[$counter]["gender"]=$row["gender"];
            $femaleContestants[$counter]["course"]=$row["course"];
            $femaleContestants[$counter]["yearOfStudy"]=$row["yearOfStudy"];

            $counter++;
        }
        $allContestants=array_merge($maleContestants, $femaleContestants);

        return json_encode($allContestants);
    }
    function updateContestantDetails($data){
        $response=[];
        if($data->gender==="male"){
            $sql="UPDATE malecontestants
                    SET 
                    thumbnail=:thumbnail,
                    name=:name,
                    gender=:gender,
                    course=:course,
                    yearOfStudy=:yearOfStudy,
                    briefDescription=:briefDescription
                    WHERE id=:id";
            $stmt=$this->connection->connectToDb()->prepare($sql);
            $stmt->bindParam(":thumbnail", $data->thumbnail);
            $stmt->bindParam(":name", $data->name);
            $stmt->bindParam(":gender", $data->gender);
            $stmt->bindParam(":course", $data->course);
            $stmt->bindParam(":yearOfStudy", $data->yearOfStudy);
            $stmt->bindParam(":id", $data->id);
            $result=$stmt->execute();

            if($result==true){
                $response=["response"=>"{$data->name}'s details updated successfully"];
            }
            else{
                $response=["response"=>"Problem updating {$data->name}'s details. Please try again"];
            }
        }
            //end of update male contestant

            //update female contestant
            else{
                $sql="UPDATE femalecontestants
                        SET 
                        thumbnail=:thumbnail,
                        name=:name,
                        gender=:gender,
                        course=:course,
                        yearOfStudy=:yearOfStudy,
                        briefDescription=:briefDescription
                        WHERE id=:id";
                $stmt=$this->connection->connectToDb()->prepare($sql);
                $stmt->bindParam(":thumbnail", $data->thumbnail);
                $stmt->bindParam(":name", $data->name);
                $stmt->bindParam(":gender", $data->gender);
                $stmt->bindParam(":course", $data->course);
                $stmt->bindParam(":yearOfStudy", $data->yearOfStudy);
                $stmt->bindParam(":briefDescription", $data->briefDescription);
                $stmt->bindParam(":id", $data->id);
                $result=$stmt->execute();

                if($result==true){
                    $response=["response"=>"{$data->name}'s details updated successfully"];
                }
                else{
                    $response=["response"=>"Problem updating {$data->name}'s details. Please try again"];
                }        
            }
        //end of update female contestant
       return json_encode($response); 
    }


    function deleteContestant($data){
        $response=[];

        if($data->gender=="MALE"){
        $sql="DELETE FROM malecontestants WHERE id=:id";
        $stmt=$this->connection->connectToDb()->prepare($sql);
        $stmt->bindParam(":id", $data->id);
        $result=$stmt->execute();

            if($result==true){
                $response=["response"=>"Contestant deleted successfully"];
            }
            else{
                $response=["response"=>"Problem deleting contestant. Please try again"];
            }
        }
        else {
            $sql="DELETE FROM femalecontestants WHERE id=:id";
            $stmt=$this->connection->connectToDb()->prepare($sql);
            $stmt->bindParam(":id", $data->id);
            $result=$stmt->execute();

            if($result==true){
                $response=["response"=>"Contestant deleted successfully"];
            }
            else{
                $response=["response"=>"Problem deleting contestant. Please try again"];
            }
        }
        return json_encode($response);
    }

    function getMaleContestantsResults(){
        $maleContestantId=[];
        $maleContestantResults=[];
        $maleContestantIdSql="SELECT id FROM malecontestants";
        $IdStmt=$this->connection->connectToDb()->prepare($maleContestantIdSql);
        
        $IdStmt->execute();
        $i=0;
        while($row=$IdStmt->fetch(PDO::FETCH_ASSOC)){
            $maleContestantId[$i]["id"]=$row["id"];
            $i++;
        }

        $counter=0;
        $j=0;
        foreach($maleContestantId as $row){
            $id=$maleContestantId[$counter]["id"];
            $sql="SELECT m.name AS name,
                         m.gender AS gender,
                      COUNT(v.maleContestantId) AS numberOfVotes, 
                      (COUNT(v.maleContestantId)*100 / (SELECT COUNT(*) FROM votinghistory))AS percentage
                      FROM malecontestants m
                      LEFT JOIN votinghistory v
                      ON m.id=v.maleContestantId
                      WHERE v.maleContestantId=:id";
                $stmt=$this->connection->connectToDb()->prepare($sql);
            
                $stmt->bindParam(":id", $id); 
                $stmt->execute();  
                
                    while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
                        $maleContestantResults[$j]["name"]=$row["name"];
                        $maleContestantResults[$j]["gender"]=$row["gender"];
                        $maleContestantResults[$j]["numberOfVotes"]=$row["numberOfVotes"];
                        $maleContestantResults[$j]["percentage"]=$row["percentage"];
                        $j++;
                    }                   
            $counter++;
        }
        return $maleContestantResults;
    }

    function getFemaleContestantsResults(){
        $femaleContestantId=[];
        $femaleContestantResults=[];
        $femaleContestantIdSql="SELECT id FROM femalecontestants";
        $IdStmt=$this->connection->connectToDb()->prepare($femaleContestantIdSql);
        
        $IdStmt->execute();
        $i=0;
        while($row=$IdStmt->fetch(PDO::FETCH_ASSOC)){
            $femaleContestantId[$i]["id"]=$row["id"];
            $i++;
        }

        $counter=0;
        $j=0;
        foreach($femaleContestantId as $row){
            $id=$femaleContestantId[$counter]["id"];
            $sql="SELECT f.name AS name,
                         f.gender AS gender,
                      COUNT(v.femaleContestantId) AS numberOfVotes, 
                      (COUNT(v.femaleContestantId)*100 / (SELECT COUNT(*) FROM votinghistory))AS percentage
                      FROM femalecontestants f
                      LEFT JOIN votinghistory v
                      ON f.id=v.femaleContestantId
                      WHERE v.femaleContestantId=:id";
                $stmt=$this->connection->connectToDb()->prepare($sql);
            
                $stmt->bindParam(":id", $id); 
                $stmt->execute();  
                
                    while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
                        $femaleContestantResults[$j]["name"]=$row["name"];
                        $femaleContestantResults[$j]["gender"]=$row["gender"];
                        $femaleContestantResults[$j]["numberOfVotes"]=$row["numberOfVotes"];
                        $femaleContestantResults[$j]["percentage"]=$row["percentage"];
                        $j++;
                    }                   
            $counter++;
        }
        return $femaleContestantResults;
    }

    function totalVotesCast(){
        $totalVotesCast=[];
        $sql="SELECT COUNT(*) AS totalVotesCast FROM votinghistory";
        $stmt=$this->connection->connectToDb()->prepare($sql);
        $stmt->execute();

        $counter=0;
        while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
            $totalVotesCast[$counter]["totalVotesCast"]=$row["totalVotesCast"];
        }
        return $totalVotesCast;
    }
}
?>