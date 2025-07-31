<?php
namespace App\Models;
use App\Models\Helper;
use Config\Core\Database;

class Logger {
    
    public static function client_log(array $data = ['mbrid' => 0, 'module' => '', 'data' => [], 'message' => '', 'device' => 'website']) {
        Database::insert("tb_log", [
            'LOG_MBR' => $data['mbrid'] ?? 0,
            'LOG_MODULE' => $data['module'] ?? "-",
            'LOG_DATA' => json_encode($data['data']),
            'LOG_MESSAGE' => $data['message'] ?? NULL,
            'LOG_IP' => Helper::get_ip_address(),
            'LOG_DEVICENAME' => $data['device'] ?? "website",
            'LOG_DATETIME' => date("Y-m-d H:i:s"),
        ]);
    }   

    public static function admin_log(array $data) {
        Database::insert("tb_log", [
            'LOG_ADM' => $data['admid'] ?? 0,
            'LOG_MODULE' => $data['module'] ?? "-",
            'LOG_DATA' => json_encode($data['data']),
            'LOG_MESSAGE' => $data['message'],
            'LOG_IP' => Helper::get_ip_address(),
            'LOG_DEVICENAME' => 'website',
            'LOG_DATETIME' => date("Y-m-d H:i:s"),
        ]);
    }   
}