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
        }
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
            return new Task($id, $this->getUser($user_id), $name, $description, $subject, $time, $importance, $type);
        } else {
            echo $this->error;
            $this->error_msg="The query could not be executed, please try another time";
            $this->http_code=500;
            return false;
        }
        $stmt->close();
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
                return true;
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
};
?>