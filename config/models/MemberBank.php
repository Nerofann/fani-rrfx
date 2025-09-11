<?php
namespace App\Models;

class MemberBank {

    public static $statusNotVerified = 0;    
    public static $statusVerified = -1;    
    public static $statusAccepted = 1;    
    public static $statusRejected = 2;  
    
    public static function status(int $status) {
        switch($status) {
            case self::$statusNotVerified: 
                return [
                    'text' => "Not Verified",
                    'html' => '<span class="badge bg-warning">Not Verified</span>'
                ];

            case self::$statusVerified: 
                return [
                    'text' => "Verified",
                    'html' => '<span class="badge bg-info">Verified</span>'
                ];

            case self::$statusAccepted: 
                return [
                    'text' => "Aktif",
                    'html' => '<span class="badge bg-success">Aktif</span>'
                ];

            case self::$statusRejected: 
                return [
                    'text' => "Ditolak",
                    'html' => '<span class="badge bg-danger">Ditolak</span>'
                ];
        }
    }
}
