<?php
use App\Models\FileUpload;

$data = [];
$result = $db->query("SELECT * FROM tb_slide WHERE SLD_TYPE = 1");
if($result->num_rows < 1){
    ApiResponse([
        'status' => false,
        'message' => 'Data slide belum tersedia',
        'response' => []
    ], 400);

}else{
    foreach($result->fetch_all(MYSQLI_ASSOC) as $row){
        $data[] = array(
            "id" => md5(md5($row['ID_SLD'])),
            "picture" => FileUpload::awsFile($row['SLD_IMG']),
            "link" => $row['SLD_LINK']
        );
    }
    
    ApiResponse([
        'status' => true,
        'message' => 'Data slide tersedia',
        'response' => $data
    ], 200);
}