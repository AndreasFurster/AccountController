<?php
    require_once(AccountControllerPath . "AccountsDatabase.class.php");
    require_once(AccountControllerPath . "User.class.php");


    class Accounts{
        private $DbContext;
       
        public function __construct($hostname = null, $username = null, $password = null, $database = null, $tablePrefix = null, $port = null, $socket = null){
            $this->DbContext = new AccountsDatabase($hostname, $username, $password, $database, $tablePrefix, $port, $socket);
        }
       
        public function GetAllUsers(){
            return $this->DbContext->SelectAllUsers();
        }

        public function GetAllUsersInRole($roleName){
            
        }

        public function GetUserById($id){
            
        }

        public function GetUserByUsername($id){
            
        }

        public function GetUserByEmail($id){
            
        }

        public function GetAuthenticatedUser(){
            
        }

        public function GetAuthenticatedUserByUsername(){
            
        }

        public function GetAuthenticatedUserByEmail(){
            
        }

        public function AddUser($user){
            return $this->DbContext->InsertUser($user);

        }

        public function SaveUser($user){
            return $this->DbContext->UpdateUser($user);
        }
    }
       
        
?>