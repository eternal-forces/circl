<?php
if (session_status() == PHP_SESSION_NONE) {session_start();};
$_SESSION['secret'] = "cocaine";

require "../classes/AltoRouter.php";
include_once "../classes/agent.php";
include_once "../classes/user.php";

$agent = new Agent();

$router = new Router();
$router->setBasePath("/circl/api/v1");

$router->map( 'POST', '/auth', function() {
    global $agent;
    prepareHeader("POST");
    $message_contents = file_get_contents('php://input');
    $message = json_decode($message_contents, true);
    if(
        isset($message['email'])
        && isset($message['password'])
        && isset($message['secret'])
    ) {
        $email = $message['email'];
        $password = $message['password'];
        $secret = $message['secret'];
        $keyPair = $agent -> authenticateUser($email, $password, $secret);

        if($keyPair) {
            echo json_encode(array(
                "user_id" => $keyPair[0],
                "key" => $keyPair[1],
            ));
            http_response_code(201);
        } else {
            echo json_encode(array("message" => $agent->error_msg));
            http_response_code($agent->http_code);
        }

    } else {
        echo json_encode(array("message" => "Invalid parameters"));
        http_response_code(400);
    }
});

$router->map('GET','/users/[a:id]', function($id) {
    global $agent;
    if($agent -> authorizeUser(5, htmlspecialchars($id))) {
        prepareHeader("GET");
        $user = $agent -> getUser(htmlspecialchars($id));
        if ($user) {
            echo $user->encode();
            http_response_code(200);
        } else {
            echo json_encode(array("message" => $agent->error_msg));
            http_response_code($agent->http_code);
        }
    } else {
        echo json_encode(array("message" => $agent->error_msg));
        http_response_code($agent->http_code);
    };
});

$router->map('POST','/users', function() {
    global $agent;
    prepareHeader('POST');
    if($agent -> authorizeUser(1)) {
        $message_contents = file_get_contents('php://input');
        $message = json_decode($message_contents, true);
        if(isset($message["name"]) and isset($message["email"]) and isset($message["password"])){
            $user = $agent -> createUser($message["name"], $message["email"], $message["password"]);
            if ($user) {
                echo $user->encode();
                http_response_code(201);
            } else {
                echo json_encode(array("message" => $agent->error_msg));
                http_response_code($agent->http_code);
            }
        } else {
            echo json_encode(array("message" => "Invalid parameters"));
            http_response_code(400);
        }
    } else {
        echo json_encode(array("message" => $agent->error_msg));
        http_response_code($agent->http_code);
    };
});

$router->map('GET','/users/[i:id]/tasks', function($id) {
    global $agent;
    prepareHeader("GET, POST");
    if($agent -> authorizeUser(5, htmlspecialchars($id))) {
        $tasks = $agent -> getTasksByUser(htmlspecialchars($id));
        if (count($tasks) > 0) {
            $message = array(
                "user_id" => $tasks[0]->user->id,
                "tasks" => array()
            );
            foreach ($tasks as $index => $task) {
                array_push($message["tasks"], $task->encode(true));
            }
            echo json_encode($message);
            http_response_code(200);
        } else {
            echo json_encode(array("message" => "No tasks"));
            http_response_code(404);
        }
    } else {
        echo json_encode(array("message" => $agent->error_msg));
        http_response_code($agent->http_code);
    };
});

$router->map('GET','/tasks/[i:id]', function($id) {
    global $agent;
    prepareHeader("GET, POST");
    if($agent -> authorizeUser(5, htmlspecialchars($id))) {
        $task = $agent -> getTask(htmlspecialchars($id));
        if ($task) {
            echo $task -> encode();
            http_response_code(200);
        } else {
            echo json_encode(array("message" => $agent->error_msg));
            http_response_code($agent->http_code);
        }
    } else {
        echo json_encode(array("message" => $agent->error_msg));
        http_response_code($agent->http_code);
    };
});

$router->map('POST','/users/[i:id]/tasks', function($id) {
    global $agent;
    prepareHeader('GET, POST');
    if($agent -> authorizeUser(5, htmlspecialchars($id))) {
        $message_contents = file_get_contents('php://input');
        $message = json_decode($message_contents, true);
        if(
            (count($message) == 5 or count($message) == 6) and
            isset($message["name"]) and
            isset($message["description"]) and 
            isset($message["subject"]) and
            isset($message["importance"]) and
            isset($message["type"])
        ){
            $time = isset($message["time"]) ? $message["time"] : null;
            $task = $agent -> createTask($id, $message["name"], $message["description"], $message["subject"], $message["importance"], $message["type"], $time);
            
            if ($task) {
                echo $task->encode();
                http_response_code(201);
            } else {
                echo json_encode(array("message" => $agent->error_msg));
                http_response_code($agent->http_code);
            }
        } else {
            echo json_encode(array("message" => "Invalid parameters"));
            http_response_code(400);
        }
} else {
    echo json_encode(array("message" => $agent->error_msg));
    http_response_code($agent->http_code);
};
});

$router->map('DELETE','/tasks/[i:id]', function($id) {
    global $agent;
    prepareHeader('DELETE');
    if($agent -> authorizeUser(5, htmlspecialchars($id))) {
        $results = $agent->deleteTask($id);
        if($results){
            http_response_code(204);
        } else {
            echo json_encode(array("message" => $agent->error_msg));
            http_response_code($agent->http_code);
        }
    } else {
        echo json_encode(array("message" => $agent->error_msg));
        http_response_code($agent->http_code);
    };
});

//===============// Shortcuts //===============//
$router->map('GET','/users/[i:id]/shortcuts', function($id) {
    global $agent;
    prepareHeader("GET, POST");
    if($agent -> authorizeUser(5, htmlspecialchars($id))) {
        $shortcuts = $agent -> getShortcutsByUser(htmlspecialchars($id));
        if (count($shortcuts) > 0) {
            $message = array(
                "user_id" => $shortcuts[0]->user->id,
                "shortcuts" => array()
            );

            foreach ($shortcuts as $index => $shortcut) {
                array_push($message["shortcuts"], $shortcut->encode(true));
            }
            
            echo json_encode($message);
            http_response_code(200);
        } else {
            echo json_encode(array("message" => "No tasks"));
            http_response_code(404);
        }
    } else {
        echo json_encode(array("message" => $agent->error_msg));
        http_response_code($agent->http_code);
    };
});

$router->map('GET','/shortcuts/[i:id]', function($id) {
    global $agent;
    prepareHeader("GET, POST");
    if($agent -> authorizeUser(5, htmlspecialchars($id))) {
        $shortcut = $agent -> getShortcut(htmlspecialchars($id));
        if ($shortcut) {
            echo $task -> encode();
            http_response_code(200);
        } else {
            echo json_encode(array("message" => $agent->error_msg));
            http_response_code($agent->http_code);
        }
    } else {
        echo json_encode(array("message" => $agent->error_msg));
        http_response_code($agent->http_code);
    };
});

$router->map('POST','/users/[i:id]/shortcuts', function($id) {
    global $agent;
    prepareHeader('GET, POST');
    if($agent -> authorizeUser(5, htmlspecialchars($id))) {
        $message_contents = file_get_contents('php://input');
        $message = json_decode($message_contents, true);

        if(
            (count($message) == 2) 
            and isset($message["link"])
            and isset($message["image_url"])
        ){
            $task = $agent -> createShortcut($id, $message["link"], $message["image_url"]);
            
            if ($task) {
                echo $task->encode();
                http_response_code(201);
            } else {
                echo json_encode(array("message" => $agent->error_msg));
                http_response_code($agent->http_code);
            }
        } else {
            echo json_encode(array("message" => "Invalid parameters"));
            http_response_code(400);
        }
} else {
    echo json_encode(array("message" => $agent->error_msg));
    http_response_code($agent->http_code);
};
});

$router->map('DELETE','/tasks/[i:id]', function($id) {
    global $agent;
    prepareHeader('DELETE');
    if($agent -> authorizeUser(5, htmlspecialchars($id))) {
        $results = $agent->deleteShortcut($id);
        if($results){
            http_response_code(204);
        } else {
            echo json_encode(array("message" => $agent->error_msg));
            http_response_code($agent->http_code);
        }
    } else {
        echo json_encode(array("message" => $agent->error_msg));
        http_response_code($agent->http_code);
    };
});



//==================// Matching //==================//
$match = $router->match();
if( is_array($match) && is_callable( $match['target'] ) ) {
	call_user_func_array( $match['target'], $match['params'] ); 
} else {
	// no route was matched
	header($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
}

function prepareHeader($type=null) {
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    header("Access-Control-Allow-Methods: $type");
}
?>