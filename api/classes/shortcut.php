<?php
require_once "agent.php";

class Shortcut
{
    public $id;
    public $user;
    public $link;
    public $image_url;

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

    public function __construct($_task_id,$_user, $_link, $_image_url) {
        $this -> id = $_task_id;
        $this -> user = $_user;
        $this -> link = $_link;
        $this -> image_url = $_image_url;
    }

    public function encode($multiple=False) {
        if ($multiple == False){
            return json_encode(array(
                "id"=>$this->id,
                "user"=>$this->user->id,
                "shortcut" => array(
                    "link"=>$this -> link,
                    "image_url"=>$this -> image_url,
            )));
        } else {
            return array(
                "id"=>$this-> id,
                "link"=>$this -> link,
                "image_url"=>$this -> image_url,
            );
        }
    }
}
?>