<?php
namespace App\Factory;

use Allmedia\Shared\Metatrader\ApiManager;
use Allmedia\Shared\Metatrader\ApiTerminal;

class MetatraderFactory {

    public static $server = "ICDX-Demo";
    public static $tokenManager = "47c8fe4e-4733-4fe3-bdcf-5c2a97e9dca5";

    public static function apiManager(): ApiManager {
        return new ApiManager(self::$tokenManager);
    }

    public static function apiTerminal(): ApiTerminal {
        return new ApiTerminal(self::$server);
    }

}