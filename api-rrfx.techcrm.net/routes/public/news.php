<?php

use App\Models\Blog;
use App\Models\FileUpload;
use App\Models\Helper;

$getData = Helper::getSafeInput($_GET);
$type_news = $getData['type_news'] ?? "";
$result = $db->query("SELECT * FROM tb_blog");
$data = [];
if($result->num_rows == 0){
    ApiResponse([
        'status' => false,
        'message' => 'Data news belum tersedia',
        'response' => []
    ], 400);
}

foreach($result->fetch_all(MYSQLI_ASSOC) as $row){
    if(!empty($type_news)) {
        if(strtoupper($type_news) != $row['BKADM_CURR']) {
            continue;
        }
    }
   
    $data[] = array(
        'id' => md5(md5($row['ID_BLOG'])),
        'title' => $row['BLOG_TITLE'],
        'type' => Blog::$type[ $row['BLOG_TYPE'] ],
        'message' => strip_tags(html_entity_decode($row['BLOG_MESSAGE'])),
        'author' => $row['BLOG_AUTHOR'],
        'tanggal' => Helper::default_date($row['BLOG_DATETIME'], "Y-m-d H:i:s"),
        'picture' => FileUpload::awsFile($row['BLOG_IMG']),
    );
}

ApiResponse([
    'status' => true,
    'message' => 'Data news tersedia',
    'response' => $data
], 200);