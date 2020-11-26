<?php
class User
{
    public $id;
    public $email;
    public $name;
    public $created_at;

    public function __construct($_id, $_name, $_email, $_created_at) {
       $this -> id = $_id;
       $this -> name = $_name;
       $this -> email = $_email;
       $this -> created_at = $_created_at;
    }

    public function encode() {
        return json_encode(array(
            "user" => array(
                "id"=>$this->id,
                "name"=>$this->name,
                "email"=>$this->email,
                "created_at"=>$this->created_at
        )));
    }
}
?>