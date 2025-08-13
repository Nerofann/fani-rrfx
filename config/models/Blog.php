<?php
namespace App\Models;

use Config\Core\Database;
use Config\Core\SystemInfo;
use Exception;

class Blog {

    public static $type = [
        '1' => "Fundamental & Technical Analys",
        '2' => "News"
    ];


    public static function get(int $type = 0, int $limit = 0): array {
        try {
            $db = Database::connect();
            $queryLimit = $limit == 0? "" : "LIMIT {$limit}";
            $sqlGet = $db->query("SELECT * FROM tb_blog WHERE (BLOG_TYPE = {$type} OR $type = 0) ORDER BY BLOG_DATETIME DESC {$queryLimit}");
            return $sqlGet->fetch_all(MYSQLI_ASSOC) ?? [];

        } catch (Exception $e) {
            throw $e;
            if(SystemInfo::isDevelopment()) {
            }

            return [];
        }
    }

    public static function formatGrouped(array $news = [], int $limit = 0): array {
        try {
            if(empty($news)) {
                return $news;
            }

            $result = [];
            foreach($news as $n) {
                $type = $n['BLOG_TYPE'];
                $groupObject = [
                    'type' => $type,
                    'alias' => self::$type[ $type ] ?? "Unknown",
                    'data' => []
                ];

                $searchType = array_search($type, array_column($result, "type"));
                if($searchType === FALSE) {
                    $result[] = $groupObject;
                    $searchType = array_search($type, array_column($result, "type"));
                }

                /** Check Limit */
                if($limit != 0 && count($result[$searchType]['data']) >= $limit) {
                    continue;
                }

                $result[$searchType]['data'][] = $n;
            }

            return $result;

        } catch (Exception $e) {
            throw $e;
            if(SystemInfo::isDevelopment()){
            }

            return [];
        }
    }

}