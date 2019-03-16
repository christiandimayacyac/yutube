<?php
    class  User {
        private $db;

        public function __construct() {
            $this->db = new Database;
        }

        public function register($data) {
            $this->db->query("INSERT INTO users (username, email, firstName, lastName, password) VALUES (:username, :email, :firstName, :lastName, :password)");
            $this->db->bind(":username", $data['inputUserName']);
            $this->db->bind(":email", $data['inputEmail']);
            $this->db->bind(":firstName", $data['inputFirstName']);
            $this->db->bind(":lastName", $data['inputLastName']);
            //Encrypt the password before saving to the database
            $hashed_password = password_hash($data['username'], PASSWORD_DEFAULT);
            $this->db->bind(":password", $hashed_password);
            $this->db->execute();
            
            //Check if inserting record successful
            if ( $this->db->getRowCount() == 1 ) {
                return true;
            }
            return false;
        }

        public function isUsernameExists($username) {
            $this->db->query("SELECT * FROM users WHERE username = :username");
            $this->db->bind(":username", $username);

            return ( $this->db->getResultRow() ) ? true : false;
        }

        public function getUserByEmail($email) {
            $this->db->query("SELECT * FROM users WHERE email = :email");
            $this->db->bind(":email", $email);
            
            if ( $userData = $this->db->getResultRow() ) {
                return $userData;
            }
            else {
                return false;
            }
        }

    }

?>