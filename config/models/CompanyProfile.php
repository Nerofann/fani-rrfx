<?php
namespace App\Models;

class CompanyProfile {

    public static $name;

    public static function init() {
        self::$name = $_ENV['APP_NAME'];
    }
}