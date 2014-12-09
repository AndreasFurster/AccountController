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
            //return array(User,...)
        }

        public function GetUserById($id){
            //return User | null
        }

        public function GetUserByUsername($username){
            //return User | null
        }

        public function GetUserByEmail($email){
            //return User | null
        }

        public function UserIdExists($id){
            //return bool
            return true;
        }

        public function UsernameExists($username){
            //return bool
        }

        public function EmailExists($email){
            //return bool
        }

        public function GetAuthenticatedUserById($id, $password){
            //return User | null | false
        }

        public function GetAuthenticatedUserByUsername($username, $password){
            //return User | null | false
        }

        public function GetAuthenticatedUserByEmail($email, $password){
            //return User | null | false
        }

        public function SaveUser($user){
            if($user->Id !== null){
                if($this->UserIdExists($user->Id)) {
                    return $this->DbContext->UpdateUser($user);
                }

                return $this->SaveUser($user->Id = null);
            }

            return $this->DbContext->InsertUser($user);
        }
    }
       
        
?>