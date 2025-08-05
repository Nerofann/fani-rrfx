<?php
namespace App\Factory;

use Allmedia\Shared\Verihubs\Verihubs;
use Config\Core\Database;

class VerihubFactory {

    public static function init(?Database $db = null): Verihubs {
        $db ??= new Database();
        return new Verihubs($db);
    }

}