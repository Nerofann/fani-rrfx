<?php

use App\Models\Blog;
use App\Models\FileUpload;
use App\Models\Helper;

$postData = Helper::getSafeInput($_POST);
$id_news = $postData['id'] ?? "";
$result = $db->query("SELECT * FROM tb_blog WHERE MD5(MD5(ID_BLOG)) = '".$id_news."' LIMIT 1");
if($result->num_rows == 0){
    ApiResponse([
        'status' => false,
        'message' => 'Data news belum tersedia',
        'response' => []
    ], 400);
};

$row = $result->fetch_assoc();


$data = array(
    'id' => md5(md5($row['ID_BLOG'])),
    'title' => $row['BLOG_TITLE'],
    'type' => Blog::$type[ $row['BLOG_TYPE'] ],
    'message' => strip_tags(html_entity_decode($row['BLOG_MESSAGE'])),
    'author' => $row['BLOG_AUTHOR'],
    'tanggal' => Helper::default_date($row['BLOG_DATETIME'], "Y-m-d H:i:s"),
    'picture' => FileUpload::awsFile($row['BLOG_IMG']),
);

ApiResponse([
    'status' => true,
    'message' => 'Data news tersedia',
    'response' => $data
], 200);