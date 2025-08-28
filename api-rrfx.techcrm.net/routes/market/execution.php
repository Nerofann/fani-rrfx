<?php
$c = form_input($_GET['c']) ?? "";
if(empty($c) || !file_exists(__DIR__ . "/execution/{$c}.php")) {
    ApiResponse([
        'status' => false,
        'message' => "Invalid action",
        'response' => []
    ], 400);
}

require_once __DIR__ . "/execution/{$c}.php";