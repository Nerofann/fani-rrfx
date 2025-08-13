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
    if(!$adminPermissionCore->hasPermission($authorizedPermission, "/news/news-corner/create")) {
        JsonResponse([
            'code'      => 200,
            'success'   => false,
            'message'   => "Authorization Failed",
            'data'      => []
        ]);
    }

    $REQ_POST = [
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


    /**Stored data for update*/
    $UPDATE_DATA = [
        "BLOG_TYPE"       => 1,
        "BLOG_TITLE"      => $data["title"],
        "BLOG_MESSAGE"    => htmlentities($_POST["content"]),
        "BLOG_AUTHOR"     => $data["author"],
        "BLOG_SLUG"       => $data["content"],
        "BLOG_DATETIME"   => date("Y-m-d H:i:s")
    ];

    /** Cek file post */
    if((!isset($_FILES["files"])) || $_FILES["files"]["error"] != 0){
        JsonResponse([
            'code'      => 200,
            'success'   => false,
            'message'   => "Image Required!.",
            'data'      => []
        ]);
    }
    
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

    /**Eksekusi database*/
    $insert = Database::insert('tb_blog', $UPDATE_DATA);
    if(!$insert){
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
        'message' => "Upload news",
        'data'  => $data
    ]);

    JsonResponse([
        'code'      => 200,
        'success'   => true,
        'message'   => "Success Insert News",
        'data'      => []
    ]);