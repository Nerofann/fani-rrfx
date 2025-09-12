<?php
    
    use App\Models\Helper;
    use App\Models\Admin;
    use App\Models\Logger;
    use App\Models\ProfilePerusahaan;
    use Config\Core\Database;
    use Config\Core\EmailSender;

    $listGrup = $adminPermissionCore->availableGroup();
    $adminRoles = Admin::adminRoles();
    if(!$adminPermissionCore->hasPermission($authorizedPermission, "/member/delete_user_action")) {
        JsonResponse([
            'code'      => 200,
            'success'   => false,
            'message'   => "Authorization Failed",
            'data'      => []
        ]);
    }

    $data = Helper::getSafeInput($_POST);
    foreach(['xid', 'val'] as $req) {
        if(empty($data[ $req ])) {
            // $req = str_replace("add-", "", $req);
            JsonResponse([
                'success'   => false,
                'message'   => "{$req} diperlukan",
                'data'      => []
            ]);
        }
    }

    /** Check accept or reject */
    if(!in_array(strtolower($data["val"]), ["accept", "reject"])){
        JsonResponse([
            'code'      => 200,
            'success'   => false,
            'message'   => "Invalid action",
            'data'      => []
        ]);
    }

    /** Check ID */ 
    $SQL_CHECK = $db->query('
        SELECT 
            tb_dlt_account.ID_DLTACC, 
            tb_dlt_account.DLTACC_MBR, 
            tb_member.MBR_EMAIL,
            tb_member.MBR_NAME
        FROM tb_dlt_account 
        JOIN tb_member
        ON(tb_dlt_account.DLTACC_MBR = tb_member.MBR_ID)
        WHERE MD5(MD5(tb_dlt_account.ID_DLTACC)) = "'.$data['xid'].'"
    ');
    if($SQL_CHECK->num_rows == 0){
        JsonResponse([
            'code'      => 200,
            'success'   => false,
            'message'   => "ID not found",
            'data'      => []
        ]);
    }
    $RSLT_CHECK = $SQL_CHECK->fetch_assoc();


    /**Execute database*/
    try {
        global $db;
        mysqli_report(MYSQLI_REPORT_ERROR|MYSQLI_REPORT_STRICT);
        mysqli_begin_transaction($db);

        /**Update tabel member*/
        $db->query('
            UPDATE tb_member SET
                tb_member.MBR_ID    = (tb_member.MBR_ID * 10),
                tb_member.MBR_EMAIL = CONCAT(tb_member.MBR_EMAIL, "_deleted"),
                tb_member.MBR_STS   = 1
            WHERE tb_member.MBR_ID = '.$RSLT_CHECK["DLTACC_MBR"].'
        ');

        /**Update delete account tabel*/
        $UPDATE_DATA = [
            "DLTACC_STS"    => ($data["val"] == 'accept') ? -1 : (($data["val"] == 'reject') ? 1 : 0)
        ];
        Database::update('tb_dlt_account', $UPDATE_DATA, ["ID_DLTACC" => $RSLT_CHECK["ID_DLTACC"], "DLTACC_STS" => 0]);

        switch ($data["val"]) {
            case 'accept':
                $wrd = 'disetujui';
                $fml = 'otp-delete-success';
                break;
            case 'accept':
                $wrd = 'ditolak';
                $fml = 'otp-delete-reject';
                break;
            
            default:
                $wrd = '';
                $fml = '';
            break;
        }

        /** Notifikasi email untuk admin dan client */
        if(!empty($fml)){
            $emailData = [
                'subject' => "Penghapusan akun anda telah ".$wrd,
                'email'   => $RSLT_CHECK['MBR_EMAIL']
            ];
    
            $emailSender = EmailSender::init(['email' => $RSLT_CHECK['MBR_EMAIL'], 'name' => $RSLT_CHECK['MBR_NAME']]);
            $emailSender->useFile($fml, $emailData);
            $emailSender->addBcc(ProfilePerusahaan::$emailDealing, ProfilePerusahaan::$namaDealing);
            $send = $emailSender->send();
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
        'module' => "delete_user",
        'message' => ucfirst($data["val"])." delete user request",
        'data'  => $data
    ]);

    JsonResponse([
        'success'   => true,
        'message'   => "Success ".$data["val"]." delete user request",
        'data'      => []
    ]);

