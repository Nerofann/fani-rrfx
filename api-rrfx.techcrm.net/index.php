<?php

use Allmedia\Shared\AdminPermission\Core\UrlParser;
use App\Models\Helper;
use App\Models\Logger;
use App\Models\Token;
use App\Models\TokenGenerator;
use App\Models\User;

try {
    require_once __DIR__ . "/../config/setting.php";

    header("Access-Control-Allow-Origin: *");
    header('Content-Type: application/json');

    function ApiResponse(array $array, int $code = 200) {
        http_response_code($code);
        exit(json_encode($array, JSON_PRETTY_PRINT));
    }

    $getInput = array_filter($_GET, fn($key) => in_array($key, range('a', 'f'), true), ARRAY_FILTER_USE_KEY);
    $filepath = UrlParser::urlToPath($getInput);
    $filepath = str_replace("/index", $filepath, $filepath);

    if(empty($getInput['a'])) {
        ApiResponse([
            'status' => false,
            'message' => "Invalid Request",
            'response' => []
        ], 400);
    }

    switch($getInput['a']) {
        case "auth":
            if(!file_exists(__DIR__ . "/routes/{$filepath}.php")) {
                ApiResponse([
                    'status' => false,
                    'message' => "Invalid Path",
                    'response' => []
                ], 400);
            }

            require __DIR__ . "/routes/{$filepath}.php";
            break;

        case "public":
            if(!file_exists(__DIR__ . "/routes/{$filepath}.php")) {
                ApiResponse([
                    'status' => false,
                    'message' => "Invalid Path",
                    'response' => []
                ], 400);
            }

            require __DIR__ . "/routes/{$filepath}.php";
            break;

        case "regol":
            if(empty($getInput['b'])) {
                ApiResponse([
                    'status' => false,
                    'message' => "Invalid Route",
                    'response' => []
                ], 400);
            }

            $filepath = implode("/", [$getInput['a'], $getInput['b']]);
            if(!file_exists(__DIR__ . "/routes/{$filepath}.php")) {
                ApiResponse([
                    'status' => false,
                    'message' => "Invalid Path",
                    'response' => []
                ], 400);
            }

            $userToken = $_SERVER['HTTP_AUTHORIZATION'] ?? "";
            $userToken = str_replace("Bearer ", "", $userToken);
            $isValid = Token::verifyToken($userToken);
            if(!$isValid || !is_array($isValid)) {
                ApiResponse([
                    'status' => false,
                    'message' => "Invalid Token",
                    'response' => []
                ], 300);
            }

            $user = User::findByMemberId($isValid['user_id']);
            $userId = md5(md5($isValid['user_id']));
            if(empty($user)) {
                ApiResponse([
                    'status' => false,
                    'message' => "Invalid User",
                    'response' => []
                ], 400);
            }

            /** Avatar */
            $avatar = User::avatar($user['MBR_AVATAR']);

            require __DIR__ . "/routes/{$filepath}.php";
            break;

        default: 
            $userToken = $_SERVER['HTTP_AUTHORIZATION'] ?? "";
            $userToken = str_replace("Bearer ", "", $userToken);
            $isValid = Token::verifyToken($userToken);
            if(!$isValid || !is_array($isValid)) {
                ApiResponse([
                    'status' => false,
                    'message' => "Invalid Token",
                    'response' => []
                ], 300);
            }

            $user = User::findByMemberId($isValid['user_id']);
            $userId = md5(md5($isValid['user_id']));
            if(empty($user)) {
                ApiResponse([
                    'status' => false,
                    'message' => "Invalid User",
                    'response' => []
                ], 400);
            }

            if(!file_exists(__DIR__ . "/routes/{$filepath}.php")) {
                ApiResponse([
                    'status' => false,
                    'message' => "Path Unknown",
                    'response' => []
                ], 400);
            }

            /** Avatar */
            $avatar = User::avatar($user['MBR_AVATAR']);

            /** Logger */
            Logger::client_log([
                'mbrid' => $user['MBR_ID'],
                'module' => $filepath,
                'data' => $_POST,
                'device' => implode(", ", array_values(json_decode($_POST['device'] ?? "{}", true))),
                'message' => "Access route " . $filepath . " from ip " . Helper::get_ip_address()
            ]);
            
            require __DIR__ . "/routes/{$filepath}.php";
            break;
    }
    
} catch (Exception $e) {
    ApiResponse([
        'status' => false,
        'message' => (ini_get("display_errors") == "1")? $e->getMessage() : "Internal Server Error (500)",
        'response' => []
    ], 400);
}