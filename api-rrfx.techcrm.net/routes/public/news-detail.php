<?php
$postData = $helperClass->getSafeInput($_POST);
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

if($row['BLOG_TYPE'] == 1){
    $BLOG_TYPE = 'Fundamental & technical Analys';
} else if($row['BLOG_TYPE'] == 2){
    $BLOG_TYPE = 'News';
} else {
    $BLOG_TYPE = 'Unknown';
}
$data = array(
    'id' => md5(md5($row['ID_BLOG'])),
    'title' => $row['BLOG_TITLE'],
    'type' => $BLOG_TYPE,
    'message' => $row['BLOG_MESSAGE'],
    'author' => $row['BLOG_AUTHOR'],
    'tanggal' => default_date($row['BLOG_DATETIME'], "Y-m-d H:i:s"),
    'picture' => $aws_folder.$row['BLOG_IMG'],
);

ApiResponse([
    'status' => true,
    'message' => 'Data news tersedia',
    'response' => $data
], 200);