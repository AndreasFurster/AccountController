<?php
    class User{
        public $Id;
        public $Username;
        public $Email;
        public $EmailConfirmed;
        public $Password;
        public $PasswordHash;
        public $RegisterDate;
        public $Deleted;

        public $Roles = array();
        public $Claims = array();

        /* Set property functions for chaining method programming */
        public function SetId($value){
            $this->Id = $value;
            return $this;
        }
        public function SetUsername($value){
            $this->Username = $value;
            return $this;
        }
        public function SetEmail($value){
            $this->Email = $value;
            return $this;
        }
        public function SetEmailConfirmed($value){
            $this->EmailConfirmed = $value;
            return $this;
        }
        public function SetPassword($value){
            $this->Password = $value;
            return $this;
        }
        public function SetPasswordHash($value){
            $this->PasswordHash = $value;
            return $this;
        }
        public function SetRegisterDate($value){
            $this->RegisterDate = $value;
            return $this;
        }
        public function SetDeleted($value){
            $this->Deleted = $value;
            return $this;
        }

        /**
         * @return bool
         */
        public function IsInRole(){
            $args = func_get_args();
            foreach ($args as $role) {
                if (!in_array($role, $this->Roles)) {
                    return false;
                }
            }
            return true;
        }

        /**
         * @return $this
         */
        public function AddToRole(){
            foreach (func_get_args() as $role) {
                $this->Roles[] = $role;
            }
            return $this;
        }

        /**
         * @return $this
         */
        public function RemoveFromRole(){
            $this->Roles = array_diff($this->Roles, func_get_args()); 
            return $this;   
        }

        /**
         * @param $claimname
         * @param $claimValue
         * @return $this
         */
        public function AddClaim($claimname, $claimValue){
            $this->Claims[$claimname] = $claimValue;
            return $this;
        }

        /**
         * @param $claimname
         * @param $claimValue
         * @return $this
         */
        public function UpdateClaim($claimname, $claimValue){
            $this->Claims[$claimname] = $claimValue;
            return $this;
        }

        /**
         * @param $claimname
         * @return mixed
         */
        public function GetClaim($claimname){
            return $this->Claims[$claimname];
        }
    }
?>