<?php
    
    use App\Models\Account;
    use App\Models\Dpwd;
    use App\Models\Helper;
    use App\Models\Admin;
    use App\Models\Logger;
    use App\Models\FileUpload;
    use Config\Core\Database;
    
    $listGrup = $adminPermissionCore->availableGroup();
    $adminRoles = Admin::adminRoles();
    if(!$adminPermissionCore->hasPermission($authorizedPermission, "/news/news-corner/update")) {
        JsonResponse([
            'code'      => 200,
            'success'   => false,
            'message'   => "Authorization Failed",
            'data'      => []
        ]);
    }

    $REQ_POST = [
        "edt-btn",
        "title",
        "author",
        "content",
    ];
    $data = Helper::getSafeInput($_POST);
    foreach($REQ_POST as $req) {
        if(in_array($req, ["k-fax-kantor"])){
            if(!isset($data[ $req ])) {
                $req = str_replace("add_", "", $req);
                JsonResponse([
                    'code'      => 402,
                    'success'   => false,
                    'message'   => "{$req} diperlukan",
                    'data'      => []
                ]);
            }
        }else{
            if(empty($data[ $req ])) {
                $req = str_replace("add_", "", $req);
                JsonResponse([
                    'code'      => 402,
                    'success'   => false,
                    'message'   => "{$req} diperlukan",
                    'data'      => []
                ]);
            }
        }
    }

    /** Check ID */
    $SQL_CHECK = $db->query('SELECT * FROM tb_blog WHERE MD5(MD5(tb_blog.ID_BLOG)) = "'.$data["edt-btn"].'"');
    if((!$SQL_CHECK) || $SQL_CHECK->num_rows == 0){
        JsonResponse([
            'code'      => 200,
            'success'   => false,
            'message'   => "News data not found.",
            'data'      => []
        ]);
    }
    $RSLT_CHECK = $SQL_CHECK->fetch_assoc();

    /**Stored data for update*/
    $UPDATE_DATA = [
        "BLOG_TITLE"      => $data["title"],
        "BLOG_MESSAGE"    => htmlentities($_POST["content"]),
        "BLOG_AUTHOR"     => $data["author"],
        "BLOG_SLUG"       => $data["content"]
    ];

    /** Cek file post */
    if((isset($_FILES["files"])) && $_FILES["files"]["error"] == 0){

        /** Upload file*/ 
        $PRCSF = FileUpload::upload_myfile($_FILES["files"], 'news');
        if(!is_array($PRCSF)){
            JsonResponse([
                'success'   => false,
                'message'   => "Failed to upload file. Please try again!. ErrMessage: ".$PRCSF,
                'data'      => []
            ]);
        }
        $UPDATE_DATA["BLOG_IMG"] = $PRCSF["filename"];

    }
    

    /**Eksekusi database*/
    $update = Database::update('tb_blog', $UPDATE_DATA, ["ID_BLOG" => $RSLT_CHECK["ID_BLOG"]]);
    if(!$update){
        JsonResponse([
            'code'      => 200,
            'success'   => false,
            'message'   => "Failed to update data.",
            'data'      => []
        ]);
    }

    Logger::admin_log([
        'admid' => $user['ADM_ID'],
        'module' => "news/news-corner",
        'message' => "Update news",
        'data'  => $data
    ]);

    JsonResponse([
        'code'      => 200,
        'success'   => true,
        'message'   => "Success Update News",
        'data'      => []
    ]);