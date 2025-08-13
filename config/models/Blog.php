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

    public static function createSlug(string $string): string|bool {
        try {
            global $db;
            $slug = strtolower($string);
            $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
            $slug = trim($slug, '-');
        
            /** Cek di database apakah slug sudah ada */
            $maximalCheck = 5;
            for($i = 1; $i <= $maximalCheck; $i++) {
                $sqlCheck = $db->query("SELECT BLOG_SLUG FROM tb_blog WHERE LOWER(BLOG_SLUG) = LOWER('{$slug}') LIMIT 1");
                if($sqlCheck->num_rows == 0) {
                    return $slug;
                }

                $slug .= "-" . uniqid();
            }
    
            return false;
    
        } catch (Exception $e) {
            if(SystemInfo::isDevelopment()) {
                throw $e;
            }

            return false;
        }
    }

}