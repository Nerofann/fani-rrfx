<?php

use App\Models\CompanyProfile;
use Config\Core\Database;
use Config\Core\SystemInfo;
use Dotenv\Dotenv;

/** Required Class */
require_once(__DIR__ . "/vendor/autoload.php");
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

SystemInfo::refreshSession();

date_default_timezone_set("Asia/Jakarta");
error_reporting(E_ALL );
ini_set("display_errors", ($_ENV['APP_MODE'] == "production"? 0 : 1));
define("CONFIG_ROOT", __DIR__);
define("WEB_ROOT", str_replace("config", "client-rrfx.techcrm.net", __DIR__));
define("CRM_ROOT", str_replace("config", "admin-ragondav.techcrm.net", __DIR__));

$db = Database::connect();
CompanyProfile::init();

function JsonResponse(array $data = []) {
    /** ini tidak membaca script dibawahnya */
    http_response_code($data['code'] ?? 200);
    exit(json_encode([
        ...$data,
        'alert' => [
            'title' => ($data['success'])? "Success" : "Failed",
            'text' => $data['message'],
            'icon' => ($data['success'])? "success" : "error"
        ]
    ]));
}
