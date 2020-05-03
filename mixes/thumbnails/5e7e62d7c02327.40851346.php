<?php
    require_once "dbhandler.php";
    
    class Voter{
        public function __construct(){
            $this->dbHandler=new DbHandler;
        }
        function getMaleContestants(){
            return $this->dbHandler->getMaleContestants();
                     
        }
        function getfemaleContestants(){
            return $this->dbHandler->getFemaleContestants();
        }
        function vote($data){
            $maleContestantChoice=$data->maleContestant->id;
            $femaleContestantChoice=$data->femaleContestant->id;            

            return $this->dbHandler->vote($maleContestantChoice, $femaleContestantChoice);
        }
    }
    $voter= new Voter();
?>