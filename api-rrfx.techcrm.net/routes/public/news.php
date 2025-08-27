<?php
$getData = $helperClass->getSafeInput($_GET);
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
    if($row['BLOG_TYPE'] == 1){
        $BLOG_TYPE = 'Fundamental & technical Analys';
    } else if($row['BLOG_TYPE'] == 2){
        $BLOG_TYPE = 'News';
    } else {
        $BLOG_TYPE = 'Unknown';
    }
    $data[] = array(
        'id' => md5(md5($row['ID_BLOG'])),
        'title' => $row['BLOG_TITLE'],
        'type' => $BLOG_TYPE,
        'message' => $row['BLOG_MESSAGE'],
        'author' => $row['BLOG_AUTHOR'],
        'tanggal' => default_date($row['BLOG_DATETIME'], "Y-m-d H:i:s"),
        'picture' => $aws_folder.$row['BLOG_IMG'],
    );
}

ApiResponse([
    'status' => true,
    'message' => 'Data news tersedia',
    'response' => $data
], 200);