<?php
    require_once(AccountControllerPath . "AccountsDatabase.class.php");
    require_once(AccountControllerPath . "User.class.php");


    class Accounts{
        private $DbContext;
       
        public function __construct($server, $dbUser, $dbPass, $dbName, $tablePrefix = null){
            $this->DbContext = new AccountsDatabase($server, $dbUser, $dbPass, $dbName, $tablePrefix);
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