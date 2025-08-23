<?php
    
    use App\Models\Helper;
    use App\Models\Admin;
    use App\Models\Account;
    use App\Models\Logger;
    use App\Models\FileUpload;
    use Config\Core\Database;
    use App\Factory\MetatraderFactory;
    
    $listGrup = $adminPermissionCore->availableGroup();
    $adminRoles = Admin::adminRoles();
    if(!$adminPermissionCore->hasPermission($authorizedPermission, "/transaction/withdrawal_accounting")) {
        JsonResponse([
            'code'      => 200,
            'success'   => false,
            'message'   => "Authorization Failed",
            'data'      => []
        ]);
    }

    $data = Helper::getSafeInput($_POST);
    foreach(["note", "acc-dpx", "acc-act"] as $req) {
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

    /** Check accept or reject */
    if(!in_array(strtolower($data["acc-act"]), ["accept", "reject"])){
        JsonResponse([
            'code'      => 200,
            'success'   => false,
            'message'   => "Invalid action",
            'data'      => []
        ]);
    }

    /** Check withdrawal id */
    $SQL_CHECK = mysqli_query($db, '
        SELECT 
            tb_dpwd.ID_DPWD,
            CASE
                WHEN tb_dpwd.DPWD_CURR_FROM = "USD" THEN tb_dpwd.DPWD_AMOUNT_SOURCE
                WHEN tb_dpwd.DPWD_CURR_FROM = "IDR" THEN tb_dpwd.DPWD_AMOUNT
                ELSE tb_dpwd.DPWD_AMOUNT_SOURCE
            END AS JMLH,
            IFNULL((
                SELECT
                    tb_racc.ACC_LOGIN
                FROM tb_racc
                WHERE tb_racc.ID_ACC = tb_dpwd.DPWD_RACC
                LIMIT 1
            ), 0) AS ACC_LOGIN
        FROM tb_dpwd 
        WHERE MD5(MD5(tb_dpwd.ID_DPWD)) = "'.$data["acc-dpx"].'" 
        AND tb_dpwd.DPWD_STS = 0
        AND tb_dpwd.DPWD_STSVER = -1
        AND tb_dpwd.DPWD_TYPE = 2
    ');
    if((!$SQL_CHECK) || $SQL_CHECK->num_rows == 0){
        JsonResponse([
            'code'      => 200,
            'success'   => false,
            'message'   => "Withdrawal id not found",
            'data'      => []
        ]);
    }
    $RSLT_CHECK = $SQL_CHECK->fetch_assoc();

    /** check akun */
    $LOGIN_ACC = Account::realAccountDetail_byLogin($RSLT_CHECK['ACC_LOGIN']);
    if(!$LOGIN_ACC) {
        JsonResponse([
            'success' => false,
            'message' => "Akun tidak ditemukan",
            'data' => []
        ]);
    }

    $UPDATE_DATA = [
        "DPWD_NOTE1"     => $data["note"],
        "DPWD_TIMESTAMP" => date("Y-m-d H:i:s")
    ];
    switch (strtolower($data["acc-act"])) {
        case 'accept':
            $UPDATE_DATA["DPWD_STS"]  = -1;
            $mt_act                   = true;
            break;
        
        case 'reject':
            $UPDATE_DATA["DPWD_STS"]  = 1;
            $mt_act                   = false;
            break;
        
        default:
            $mt_act                   = false;
            JsonResponse([
                'code'      => 200,
                'success'   => false,
                'message'   => "Invalid action",
                'data'      => []
            ]);
        break;
    }

    /**Execute database*/
    try {
        global $db;
        mysqli_report(MYSQLI_REPORT_ERROR|MYSQLI_REPORT_STRICT);
        mysqli_begin_transaction($db);


        /** Update data */
        $update = Database::update('tb_dpwd', $UPDATE_DATA, ["ID_DPWD" => $RSLT_CHECK["ID_DPWD"]]);
        if(!$update){
            JsonResponse([
                'code'      => 200,
                'success'   => false,
                'message'   => "Failed to ".$data["acc-act"],
                'data'      => []
            ]);
        }

        /** MetaTrader action if accept */
        if($mt_act){
            $apiManager = MetatraderFactory::apiManager();
            /** Proses isi balance MetaTrader */
            $deposit = $apiManager->deposit([
                'login' => $LOGIN_ACC['ACC_LOGIN'],
                'amount' => '-'.$RSLT_CHECK["JMLH"],
                'comment' => "withdrawal_".$RSLT_CHECK["ID_DPWD"]
            ]);

            if(is_object($deposit) === FALSE || !property_exists($deposit, "ticket")) {
                $db->rollback();
                JsonResponse([
                    'success' => false,
                    'message' => "Invalid Status Withdrawal",
                    'data' => []
                ]);
            }
        }


        mysqli_commit($db);
    } catch (Exception | mysqli_sql_exception $e) {
        mysqli_rollback($db);
        JsonResponse([
            'code'      => 200,
            'success'   => false,
            'message'   => "Exception occured. Please try again!.",
            'data'      => []
        ]);
    }

    
    Logger::admin_log([
        'admid' => $user['ADM_ID'],
        'module' => "transaction/withdrawal/authorization",
        'message' => strtoupper($data["acc-act"])." Withdrawal",
        'data'  => $data
    ]);

    JsonResponse([
        'code'      => 200,
        'success'   => true,
        'message'   => "Success ".$data["acc-act"],
        'data'      => []
    ]);