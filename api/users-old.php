<?php

include_once "../classes/agent.php";
include_once "../classes/user.php";

$agent = new Agent();

switch($_SERVER["REQUEST_METHOD"])
{
    case 'GET':
        prepareHeader("GET, POST");
        if(isset($_GET["id"])){
            $user = $agent -> getUser(htmlspecialchars($_GET["id"]));
            if ($user) {
                echo $user->encode();
                http_response_code(200);
            } else {
                echo json_encode(array("message" => $agent->error_msg));
                http_response_code($agent->http_code);
            }
        } else {
            echo json_encode(array("message" => "Invalid parameters"));
            http_response_code(400);
        }
    break;

    case 'POST':
        prepareHeader('GET, POST');
        $message_contents = file_get_contents('php://input');
        $message = json_decode($message_contents, true);
        if(isset($message["name"]) and isset($message["email"]) and isset($message["password"])){
            $user = $agent -> createUser($message["name"], $message["email"], $message["password"]);
            if ($user) {
                echo $user->encode();
                http_response_code(200);
            } else {
                echo json_encode(array("message" => $agent->error_msg));
                http_response_code($agent->http_code);
            }
        } else {
            echo json_encode(array("message" => "Invalid parameters"));
            http_response_code(400);
        }
    break;

    case 'MAGIC':
        prepareHeader("GET, POST");
        echo json_encode(array("message" => "WTF why is this working?!"));
        http_response_code(405);
    break;

    default:
        prepareHeader("GET, POST");
        echo json_encode(array("message" => "That method is not allowed!"));
        http_response_code(405);

};

function prepareHeader($type=null) {
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    header("Access-Control-Allow-Methods: $type");
}
?>