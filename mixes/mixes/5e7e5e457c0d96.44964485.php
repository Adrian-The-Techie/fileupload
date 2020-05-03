<?php
    require_once 'dbhandler.php';

    class Admin{
        public function __construct(){
            $this->dbhandler=new DbHandler();
        }

        function login($data){
            return $this->dbhandler->login($data);
        }
        function addContestant($data){
            return $this->dbhandler->addContestant($data);
        }
        function getAllContestants(){
            return $this->dbhandler->getAllContestants();
        }
        function updateContestantDetails($data){
            return $this->dbhandler->updateContestantDetails($data);
        }
        function deleteContestant($data){
            return $this->dbhandler->deleteContestant($data);
        }
        function getContestantsResults(){
            $homePanelData=array_merge($this->dbhandler->getMaleContestantsResults(), $this->dbhandler->getFemaleContestantsResults(), $this->dbhandler->totalVotesCast()) ;
            return json_encode($homePanelData);
        }
    }
    $admin= new Admin();
?>