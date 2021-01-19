<?php
include_once "user.php";
include_once "task.php";

class Agent extends mysqli
{
    public $error_msg = "";
    public $http_code = 200;

    public function __construct() {
        parent::__construct("localhost","circl-agent","2JQ&JqP3&&fgV&#gDesL","circl");
        if ($this -> connect_error) {
            http_response_code(500);
            echo json_encode(array("message" => "The Agent could not connect to the database, please try again later"));
        };
    }

    protected function generateID($_length, $_db="USERS", $_name="ID") {
        $stmt = $this -> prepare(
            "SELECT *
            FROM $_db 
            WHERE $_name = ?"
        );
        echo $this->error;
        while (True) {
            $a = array();

            for ($i = 0; $i < $_length; $i++) {
                array_push($a, rand(0,9));
            }

            $stmt->bind_param("s", $param_id);
                $param_id = join($a);
            if($stmt->execute()){
                $stmt->store_result();
                if($stmt->num_rows == 0) {
                    return join($a);
                }
            }
        }
    }
    
    protected function checkID($_id, $_db="USERS", $_name="ID"){
        $stmt = $this -> prepare(
            "SELECT *
            FROM $_db 
            WHERE $_name = ?"
        );
        $stmt->bind_param("s", $param_id);
            $param_id = $_id;
        if($stmt->execute()){
            $stmt->store_result();
            if($stmt->num_rows == 0) {
                return false;
            } else {
                return true;
            }
        }
    }

    protected function raiseError($returnValue, $error, $errorCode){
        
    }


    /**
     * Function used to login to the Circl Network and assign a session variable
     * 
     * @param string $_username
     * @param string $_password unencrypted password
     * @return bool True if login is succesful, False if not
     */

    public function login($_username, $_password) {
        $stmt = $this -> prepare(
            "SELECT id, name, password 
            FROM USERS 
            WHERE email = ?"
        );
        $stmt->bind_param("s", $param_email);
            $param_email = strtolower(htmlspecialchars($_username));
        
        $stmt->execute();
        $stmt->bind_result($id, $name, $password);
        
        if(password_verify($_password, $password)){
            if (session_status() == "PHP_SESSION_NONE") {
                session_start();
            }
            $_SESSION['__$Circl-loggedIN=_3791248'] = true;
            $_SESSION['__$Circl-ID'] = $id;
            $_SESSION['__$Circl-Name'] = $name;
            return true;
        } else {
            return false;
        }
    }

    /**
     * Function used to get userdata from the database
     * 
     * @param int $_id 
     * @return User|false User object containing userdata if query was succesful, false if it went wrong.
     */

    public function getUser($_id) {
        $stmt = $this -> prepare(
            "SELECT id, name, email, created_on 
            FROM USERS 
            WHERE ID = ?"
        );

        $stmt->bind_param("s", $param_id);
            $param_id = htmlspecialchars($_id);

        if($stmt->execute()){
            $stmt->store_result();
            if($stmt->num_rows > 0) {
                $stmt->bind_result($id, $name, $email, $created_at);
                $stmt->fetch();
                $stmt->close();
                return new User($id, $name, $email, $created_at);
            } else {
                $this->error_msg="No user with that ID found";
                $this->http_code=404;
                return false;
            }
        } else {
            $this->error_msg="The query could not be executed, please try another time";
            $this->http_code=500;
            return false;
        }
    }

    public function createUser($_name, $_email, $_password) {
        $_id = $this -> generateID(16);
        $stmt = $this -> prepare(
            "INSERT INTO USERS
            (ID, NAME, EMAIL, PASSWORD, CREATED_ON, LAST_EDITED)
            VALUES 
            (?, ?, ?, ?, ?, ?)"
        );

        $stmt->bind_param("isssss", $id, $name, $email, $password, $created_at, $last_edited);
            $id = $_id;
            $name = htmlspecialchars($_name);
            $email = $_email;
            $password = password_hash($_password, PASSWORD_DEFAULT);
            $created_at = date("Y-m-d H:i:s");
            $last_edited = date("Y-m-d H:i:s");

        if($stmt->execute()){
            $stmt->close();
            return new User($id, $name, $email, $created_at);
        } else {
            $this->error_msg="The query could not be executed, please try another time";
            $this->http_code=500;
            return false;
        }
    }
    /**
     * Function used to get a task from the database using the user identifier.
     * 
     * @param int $_user_id The identifier of the user 
     * @return Array An array containing the tasks of a user
     */

    public function getTasksByUser($_user_id) {
        $stmt = $this -> prepare(
            "SELECT u.name, u.email, u.created_on, t.TASK_ID, t.USER_ID, t.TASK_NAME, t.TASK_INFO, t.TASK_SUBJECT, t.TASK_TIME, t.TASK_IMPORTANCE, t.TASK_TYPE
            FROM tasks t, users u
            WHERE u.id = t.user_id 
            AND t.USER_ID = ?"
        );

        $stmt->bind_param("s", $param_id);
            $param_id = htmlspecialchars($_user_id);

        if($stmt->execute()){
            $stmt->store_result();
            // if($stmt->num_rows > 0) {
                $stmt->bind_result($u_name, $u_email, $u_created_at, $id, $user_id, $name, $description, $subject, $time, $importance, $type);
                $user_array = array();
                while($stmt->fetch()) {
                    array_push($user_array, new Task($id, new User($user_id, $u_name, $u_email, $u_created_at), $name, $description, $subject, $time, $importance, $type));
                }
                return $user_array;
            // } else {
            //     $this->error_msg="No task with that ID found";
            //     $this->http_code=404;
            //     return false;
            // }
        } else {
            $this->error_msg="The query could not be executed, please try another time";
            $this->http_code=500;
            return false;
        }
    }

    public function getTask($_id) {
        $stmt = $this -> prepare(
            "SELECT u.name, u.email, u.created_on, t.TASK_ID, t.USER_ID, t.TASK_NAME, t.TASK_INFO, t.TASK_SUBJECT, t.TASK_TIME, t.TASK_IMPORTANCE, t.TASK_TYPE
            FROM tasks t, users u
            WHERE u.id = t.user_id 
            AND t.TASK_ID = ?"
        );

        $stmt->bind_param("s", $param_id);
            $param_id = htmlspecialchars($_id);

        if($stmt->execute()){
            $stmt->store_result();
            if($stmt->num_rows > 0) {
                $stmt->bind_result($u_name, $u_email, $u_created_at, $id, $user_id, $name, $description, $subject, $time, $importance, $type);
                $stmt->fetch();
                return new Task($id, new User($user_id, $u_name, $u_email, $u_created_at), $name, $description, $subject, $time, $importance, $type);
            } else {
                $this->error_msg="No task with that ID found";
                $this->http_code=404;
                return false;
            }
        } else {
            $this->error_msg="The query could not be executed, please try another time";
            $this->http_code=500;
            return false;
        }
    }

    public function createTask($_user_id, $_name, $_description, $_subject, $_importance, $_type, $_time=null) {
        $_id = $this -> generateID(16, "TASKS", "TASK_ID");
        $stmt = $this -> prepare(
            "INSERT INTO TASKS
            (TASK_ID, USER_ID, TASK_NAME, TASK_INFO, TASK_SUBJECT, TASK_TIME, TASK_IMPORTANCE, TASK_TYPE)
            VALUES 
            (?, ?, ?, ?, ?, ?, ?, ?)"
        );
        
        //Error checking
        //Check if importance is not between 0 and 3. Raise an silent error using the error_msg string atribute of
        //of the object
        if(!($_importance >= 0 and $_importance <= 3)) {
            $this->error_msg="Importance has to be between 0 and 3";
            $this->error_code=400;
            return false;
        }

        //Check if the type is personal or timed. Raise a silent error using the error_msg string atribute
        if(!($_type == "personal" or $_type == "timed")) {
            $this->error_msg="Type has to be 'personal' or 'timed'";
            $this->error_code=400;
            return false;
        }

        //Set up the parameters
        $stmt->bind_param("sssssiis", $id, $user_id, $name, $description, $subject, $time, $importance, $type);
            // Bind the parameters
            $id = $_id;
            $user_id = $_user_id;
            $name = htmlspecialchars($_name);
            $description = htmlspecialchars($_description);
            $subject = htmlspecialchars($_subject);
            $time = $_time;
            $importance = $_importance;
            $type = $_type;
        
        //Execute the query and check for errors. 
        //On success: return a task object
        //On failure, raise a silent error
        if($stmt->execute()){
            $stmt->close();
            return new Task($id, $this->getUser($user_id), $name, $description, $subject, $time, $importance, $type);
        } else {
            $stmt->close();
            echo $this->error;
            $this->error_msg="The query could not be executed, please try another time";
            $this->http_code=500;
            return false;
        }
    }

    public function deleteTask($_id){
        $stmt = $this -> prepare(
            "DELETE FROM TASKS
            WHERE TASK_ID = ?"
        );

        $stmt->bind_param("s", $param_id);
            $param_id = htmlspecialchars($_id);

        if($stmt->execute()){
            if($this->affected_rows > 0) {
                $stmt->close();
                return true;
            } else {
                $this->error_msg="No task with that ID found";
                $this->http_code=404;
                return false;
            }
        } else {
            $stmt->close();
            $this->error_msg="The query could not be executed, please try another time";
            $this->http_code=500;
            return false;
        }
    }

    public function authenticateUser($email, $password, $secret) {
        if (session_status() == PHP_SESSION_NONE) {session_start();};
        
        if(isset($_SESSION['secret']) and $_SESSION['secret'] === $secret) {
            $stmt = $this -> prepare(
                "SELECT ID, PASSWORD
                FROM USERS
                WHERE EMAIL = ?
                "
            );

            $stmt -> bind_param("s", $param_username);
                $param_username = $email;
            
            if($stmt->execute()) {
                $stmt->store_result();
                if($stmt->num_rows > 0) {
                    $stmt -> bind_result($user_id, $database_password);
                    $stmt->fetch();
                    $stmt->close();
                    if(password_verify($password, $database_password)) {
                        if($key = $this -> collectAPIKey($user_id)) {
                            return [$user_id, $key];
                        } else {
                            return false;
                        };
                    } else {
                        $this->error_msg="Credentials are incorrect.";
                        $this->http_code = 401;
                        return false;
                    }   
                } else {
                    $this->error_msg="Credentials are incorrect";
                    $this->http_code=401;
                    return false;
                }
            } else {
                $this->error_msg="Query could not be executed, please try another time";
                $this->http_code=500;
                return false;
            }
        } else {
            $this->error_msg="Secret was not correct";
            $this->http_code=401;
            return false;
        }
    }

    protected function collectAPIKey($user_id, $permissions=5) {
        if (!($key = $this->userAlreadyHasKey($user_id))) {
            $stmt = $this -> prepare(
            "INSERT INTO AUTH
                (KEY_NO, USER_ID, PERMISSIONS, UNTIL_DATE)
                VALUES
                (?, ? , ? , DATE_ADD(NOW(), INTERVAL 30 MINUTE))"
            ); 
    
            $key = $this -> generateID(16, "AUTH", "KEY_NO");
            $stmt -> bind_param("ssi", $param_key, $param_user_id, $param_permissions);
                $param_key = $key;
                $param_user_id = $user_id;
                $param_permissions = $permissions;
            
            if($stmt->execute()) {
                $stmt->close();
                return $key;
            } else {
                $this->error_msg="Query could not be executed, please try another time";
                $this->http_code=500;
                $stmt->close();
                return false;
            }
        } else {
            return $key;
        }
    }

    protected function userAlreadyHasKey($user_id) {
        $stmt = $this -> prepare(
            "SELECT KEY_NO, UNTIL_DATE
            FROM AUTH
            WHERE USER_ID = ?"
        ); 

        $stmt -> bind_param("s", $param_user_id);
            $param_user_id = $user_id;

        if($stmt->execute()) {
            $stmt->store_result();
            if($stmt->num_rows > 0) {
                $stmt -> bind_result($key, $until_date);
                $stmt->fetch();
                $stmt->close();
                
                $until_date = new DateTime($until_date);
                if($until_date > new DateTime('now')) {
                    if ($this->prolongUserKey($key)) {
                        return $key;
                    } else {
                        return False;
                    }
                } else {
                    $this -> removeUserKey($key);
                    return False;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    protected function prolongUserKey($key) {
        $stmt = $this -> prepare(
            "UPDATE AUTH
            SET UNTIL_DATE = DATE_ADD(NOW(), INTERVAL 30 MINUTE)
            WHERE KEY_NO = ?"
        );

        $stmt -> bind_param("s", $param_key);
            $param_key = $key;
        
        if($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            return false;
        }
    }

    protected function removeUserKey($key) {
        $stmt = $this -> prepare(
            "DELETE FROM AUTH
            WHERE KEY_NO = ?"
        );

        $stmt -> bind_param("s", $param_key);
            $param_key = $key;
        
        if($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            return false;
        }
    }

    public function authorizeUser($action_permissions, $requestant_id=NULL) {
        $stmt = $this -> prepare(
            "SELECT USER_ID, PERMISSIONS, UNTIL_DATE
            FROM AUTH
            WHERE KEY_NO = ?"
        ); 
        
        $headers = apache_request_headers();
        $key = $headers['key'];

        $stmt -> bind_param("s", $param_key);
            $param_key = $key;

        if($stmt->execute()) {
            $stmt->store_result();
            if($stmt->num_rows > 0) {
                $stmt -> bind_result($user_id, $database_permissions, $until_date);
                $stmt->fetch();
                $stmt->close();
                $until_date = new DateTime($until_date);
                if($until_date > new DateTime('now')) {
                    if(
                        !(is_null($requestant_id) and $requestant_id == $user_id) 
                        or $database_permissions == 1
                    ) {
                        if ($action_permissions % $database_permissions == 0) {
                            return True;
                        } else {
                            $this->error_msg="Insufficient permissions";
                            $this->http_code = 403;
                        }
                    } else {
                        $this->error_msg="Insufficient permissions";
                        $this->http_code = 403;
                    }
                } else {
                    $this->error_msg="Your key is old, please apply for a new one";
                    $this->http_code = 401;
                }   
            } else {
                $this->error_msg="Key was incorrect, this could be an old key, or just an incorrect one";
                $this->http_code=401;
            }
        } else {
            $this->error_msg="Query could not be executed, please try another time";
            $this->http_code=500;
        }

        return false;
    }

};
?>