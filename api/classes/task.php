<?php

use FFI\Exception;

require_once "agent.php";

class Task
{
    public $id;
    public $user;
    public $name;
    public $description;
    public $subject;
    public $time;
    public $importance;
    public $type;

    public $error;

    /**
     * Create a task.
     *
     * @param int $_task_id
     * @param int $_user
     * @param string $_name
     * @param string $_description
     * @param string $_subject
     * @param string $_time A string containing a timestamp.
     * @param int $_importance An integer ranging from 0 to 3, with 0 being normal and 3 being very important.
     * @param string $_type The type of task. The type has to be 'timed' or 'personal'.
     * @return Task|false Returns a valid task class on success, false on failure.
     */

    public function __construct($_task_id,$_user, $_name, $_description, $_subject, $_time, $_importance, $_type) {
        $this -> id = $_task_id;
        $this -> user = $_user;
        $this -> name = $_name;
        $this -> description = $_description;
        $this -> subject = $_subject;
        $this -> time = $_time;
        if ($_importance >= 0 and $_importance <= 3) {
            $this -> importance = $_importance;
        } else {
            $this -> error = "The value of importance is invalid, has to be between 0 and 3";
        }

        if (strpos("timed personal", $_type) !== false) {
            $this -> type = $_type;
        } else {
            $this -> error = "The value of type is invalid, has to be 'timed' or 'personal'";
        }
    }

    public function encode($multiple=False) {
        if ($multiple == False){
            return json_encode(array(
                "id"=>$this->id,
                "user"=>$this->user->id,
                "task" => array(
                    "name"=>$this->name,
                    "description"=>$this -> description,
                    "subject"=>$this -> subject,
                    "time"=>$this -> time,
                    "importance"=>$this -> importance,
                    "type"=>$this -> type
            )));
        } else {
            return array(
                "id"=>$this->id,
                "name"=>$this->name,
                "description"=>$this -> description,
                "subject"=>$this -> subject,
                "time"=>$this -> time,
                "importance"=>$this -> importance,
                "type"=>$this -> type
            );
        }
    }
}
?>