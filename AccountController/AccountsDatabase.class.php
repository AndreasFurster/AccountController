<?php
    class AccountsDatabase{
        private $Connection;
        private $DbName;
        private $TablePrefix;
        private $TableNames;

        public function __construct($hostname = null, $username = null, $password = null, $database = null, $tablePrefix = null, $port = null, $socket = null)
        {
            $hostname       = $hostname     !== null ? $hostname    : ini_get("mysqli.default_host");
            $username       = $username     !== null ? $username    : ini_get("mysqli.default_user");
            $password       = $password     !== null ? $password    : ini_get("mysqli.default_pw");
            $database       = $database     !== null ? $database    : "";
            $tablePrefix    = $tablePrefix  !== null ? $tablePrefix : "";
            $port           = $port         !== null ? $port        : ini_get("mysqli.default_port");
            $socket         = $socket       !== null ? $socket      : ini_get("mysqli.default_socket");

            $this->Connection   = new mysqli($hostname, $username, $password, $database, $port, $socket);
            $this->DbName       = $database;
            $this->TablePrefix  = $tablePrefix !== null ? $tablePrefix : "";

            $tp = $this->TablePrefix;

            $this->TableNames['users']      = $tp . "users";
            $this->TableNames['roles']      = $tp . "roles";
            $this->TableNames['userRoles']  = $tp . "user_roles";
            $this->TableNames['claims']     = $tp . "claims";
            $this->TableNames['userClaims'] = $tp . "user_claims";

            if ($this->Connection->connect_error) {
                throw new Exception("Error connecting to the server!");
            }
        }

        public function SelectAllUsers()
        {
            $stmt = $this->Connection->prepare("SELECT * FROM " . $this->TableNames['users']);
            $stmt->execute();

            while($row = $stmt->fetch()){
                //var_dump($row);
            }
        }

        public function SelectSingleUser($by, $value)
        {

        }

        public function InsertUser($user)
        {
            /** Map properties from User object to local scope variables */
            $username = $this->CheckPropertyIsNull($user->Username, "Username");
            $email = $this->CheckPropertyIsNull($user->Email, "Email");
            $password = $this->EncryptPassword($this->CheckPropertyIsNull($user->Username, "Username"));

            /** Insert user to database */
            $stmt = $this->Connection->prepare("INSERT INTO " . $this->TableNames['users'] . " (Email, Username, PasswordHash) VALUES (?, ?, ?);");
            $stmt->bind_param("sss", $email, $username, $password);

            if(!$stmt->execute()){
                throw new Exception("Failed to insert user in database. Maybe there is already a user with the same username or email");
            }

            /** Get just inserted full user from database */
            $insertedUser = $this->Connection->query("SELECT * FROM " . $this->TableNames['users'] . " WHERE Id = " . $stmt->insert_id . ";");
            $insertedUser =  $this->MapDbRowToUser($insertedUser->fetch_object());

            $insertedUser->Roles = $user->Roles;
            $insertedUser->Claims = $user->Claims;

            return $this->UpdateRolesAndClaims($insertedUser);
        }

        public function UpdateUser($user){
            $id = $user->Id;

            if($id !== null) {
                $username = $user->Username;
                $email = $user->Email;
                $emailConfirmed = $user->EmailConfirmed;
                $passwordHash = $this->EncryptPassword($user->Password);
                $deleted = $user->Deleted;

                $query = "UPDATE " . $this->TableNames['users'] . " SET
                    Username = IFNULL(?, Username),
                    Email = IFNULL(?, Email),
                    EmailConfirmed = IFNULL(?, EmailConfirmed),
                    PasswordHash = IFNULL(?, PasswordHash),
                    Deleted = IFNULL(?, Deleted) WHERE Id = ?";

                $stmt = $this->Connection->prepare($query);
                $stmt->bind_param("ssisii", $username, $email, $emailConfirmed, $passwordHash, $deleted, $id);
            }
            else{
                throw new Exception("No id given. Can't update a user without id");
            }

            if(!$stmt->execute()){
                throw new Exception("Error updating user: " . $stmt->error);
            }

            return $this->UpdateRolesAndClaims($user);
        }

        private function EncryptPassword($password){
            $key = 5141589982687;
            for($i = 0; $i <= 65536; $i++){
                $password = hash_hmac("sha512", $password, $key);
                $key += $i;
            }
            return $password;
        }

        private function CheckPropertyIsNull($property, $propertyName){
            if($property === null)
                throw new Exception("Missing required property $propertyName");

            return $property;
        }

        private function MapDbRowToUser($dbRow){
            $user = new User();
            $user->Id = $dbRow->Id;
            $user->Username = $dbRow->Username;
            $user->Email = $dbRow->Email;
            $user->EmailConfirmed = $dbRow->EmailConfirmed;
            $user->PasswordHash = $dbRow->PasswordHash;
            $user->RegisterDate = $dbRow->RegisterDate;
            $user->Deleted = $dbRow->Deleted;

            return $user;
        }

        private function UpdateRolesAndClaims($user){
            $roles = $user->Roles;
            $claims = $user->Claims;

            if($user->Id === null){
                throw new Exception("Can't update roles and claims without an id.");
            }

            $this->RemoveAllRolesAndClaims($user->Id);

            if(is_array($roles)) {
                foreach ($roles as $role) {
                    /** Add roles if not exists */
                    $roleId = $this->AddRoleIfNotExists($role);

                    /** Add or remove user to/from roles  */
                    $this->LinkUserToRole($user->Id, $roleId);
                }
            }

            if(is_array($claims)) {
                foreach ($claims as $claim => $claimValue) {
                    /** Add claims if not exists */
                    $claimId = $this->AddClaimIfNotExist($claim);

                    /** Set value for claims */
                    $this->SetClaimForUser($user->Id, $claimId, $claimValue);
                }
            }

            return $user;
        }

        private function RemoveAllRolesAndClaims($userId){
            $query = "DELETE FROM user_claims WHERE User_Id = ?;";
        }

        private function AddRoleIfNotExists($roleName)
        {
            $stmt = $this->Connection->prepare("INSERT INTO " . $this->TableNames['roles'] . " SET Role = ?;");
            $stmt->bind_param("s", $roleName);
            if($stmt->execute()){
                return $stmt->insert_id;
            }
            else{
                $stmt = $this->Connection->prepare("SELECT Id FROM " . $this->TableNames['roles'] . " WHERE Role = ?;");
                $stmt->bind_param("s", $roleName);
                if(!$stmt->execute()){
                    throw new Exception("Error by getting the id of an existing role");
                }
                $stmt->bind_result($existingRoleId);
                $stmt->fetch();
                return $existingRoleId;
            }
        }

        private function LinkUserToRole($userId, $roleId)
        {
            $stmt = $this->Connection->prepare("REPLACE INTO " . $this->TableNames['userRoles'] . " (User_Id, Role_Id) VALUES (?, ?)");
            $stmt->bind_param("ii", $userId, $roleId);
            if(!$stmt->execute()){
                throw new Exception("Error by insert of junction table user_roles");
            }
        }

        private function AddClaimIfNotExist($claimName){
            $stmt = $this->Connection->prepare("INSERT INTO " . $this->TableNames['claims'] . " SET Claim = ?;");
            $stmt->bind_param("s", $claimName);
            if($stmt->execute()){
                return $stmt->insert_id;
            }
            else {
                $stmt = $this->Connection->prepare("SELECT Id FROM " . $this->TableNames['claims'] . " WHERE Claim = ?;");
                $stmt->bind_param("s", $claimName);
                if (!$stmt->execute()) {
                    throw new Exception("Error by getting the id of an existing claim");
                }
                $stmt->bind_result($existingClaimId);
                $stmt->fetch();
                return $existingClaimId;
            }
        }

        private function SetClaimForUser($userId, $claimId, $claimValue)
        {
            $stmt = $this->Connection->prepare("INSERT INTO " . $this->TableNames['userClaims'] . " (User_Id, Claim_Id, Claim_Value) VALUES (?, ? , ?) ON DUPLICATE KEY UPDATE Claim_Value = ?");
            $stmt->bind_param("iiss", $userId, $claimId, $claimValue, $claimValue);
            if(!$stmt->execute()){
                throw new Exception("Error by insert of junction table user_claims");
            }
        }
    }
