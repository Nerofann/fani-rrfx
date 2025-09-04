<?php

use App\Factory\MetatraderFactory;
use App\Models\Account;
    use App\Models\Dpwd;
    use App\Models\Helper;
    use App\Models\Admin;
    use App\Models\Logger;
    use App\Models\FileUpload;
    use Config\Core\Database;
    use Config\Core\EmailSender;
    
    $apiManager = MetatraderFactory::apiManager();
    $apiTerminal = MetatraderFactory::apiTerminal();
    $listGrup = $adminPermissionCore->availableGroup();
    $adminRoles = Admin::adminRoles();
    if(!$adminPermissionCore->hasPermission($authorizedPermission, "/account/dealer_action")) {
        JsonResponse([
            'code'      => 200,
            'success'   => false,
            'message'   => "Authorization Failed",
            'data'      => []
        ]);
    }

    $data = Helper::getSafeInput($_POST);
    foreach(["sbmt_id", "sbmt_act", "sbmt_note", "password", "investor"] as $req) {
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
    if(!in_array(strtolower($data["sbmt_act"]), ["accept", "reject"])){
        JsonResponse([
            'code'      => 200,
            'success'   => false,
            'message'   => "Invalid action",
            'data'      => []
        ]);
    }

    /** Check Id Account */
    $ACCOUNT_CHECK = Account::realAccountDetail($data["sbmt_id"]);
    if(!$ACCOUNT_CHECK){
        JsonResponse([
            'code'      => 200,
            'success'   => false,
            'message'   => "Account id not found",
            'data'      => []
        ]);
    }

    /** Check Valid Account to proceeds */
    if(($ACCOUNT_CHECK["ACC_STS"] != 1 || $ACCOUNT_CHECK["ACC_WPCHECK"] != 5)){
        JsonResponse([
            'code'      => 200,
            'success'   => false,
            'message'   => "Invalid Account",
            'data'      => []
        ]);
    }

    /** Check Id Account Condition */
    $ACCOND_CHECK = Account::accoundCondition($ACCOUNT_CHECK["ID_ACC"]);
    if(!$ACCOND_CHECK){
        JsonResponse([
            'code'      => 200,
            'success'   => false,
            'message'   => "Account condition id not found",
            'data'      => []
        ]);
    }

    /** Update RACC data*/
    $UPDATE_RACC = [
        "ACC_WPCHECK_DATE" => date("Y-m-d H:i:s")
    ];

    /** Update Account condition data*/
    $UPDATE_ACCND = [
        "ACCCND_DATEMARGIN" => date("Y-m-d H:i:s")
    ];

    /**Execute database*/
    try {
        global $db;
        mysqli_report(MYSQLI_REPORT_ERROR|MYSQLI_REPORT_STRICT);
        mysqli_begin_transaction($db);


        /**Accept || Reject Processing*/
        switch ($data["sbmt_act"]) {
            case 'accept':
                /** Update RACC */
                $updateRaccData = [
                    'ACC_STS' => -1,
                    'ACC_LOGIN' => $ACCOND_CHECK['ACCCND_LOGIN'],
                    'ACC_WPCHECK' => 6,
                ];

                $updateRacc = Database::update('tb_racc', $updateRaccData, ["ID_ACC" => $ACCOUNT_CHECK["ID_ACC"]]);
                if(!$updateRacc) {
                    $db->rollback();
                    JsonResponse([
                        'success'   => false,
                        'message'   => "Gagal memperbarui data akun",
                        'data'      => []
                    ]);
                }

                /** Update Account condition */
                $updateAccnd = Database::update('tb_acccond', ['ACCCND_STS' => -1], ["ID_ACCCND" => $ACCOND_CHECK["ID_ACCCND"]]);
                if(!$updateAccnd) {
                    $db->rollback();
                    JsonResponse([
                        'success'   => false,
                        'message'   => "Gagal memperbarui account condition ",
                        'data'      => []
                    ]);
                }
                break;

            case 'reject':
                $updateRacc = Database::update('tb_racc', ['ACC_WPCHECK' => 0], ["ID_ACC" => $ACCOUNT_CHECK["ID_ACC"]]);
                if(!$updateRacc) {
                    $db->rollback();
                    JsonResponse([
                        'success' => false,
                        'message' => "Gagal memperbarui data akun",
                        'data' => []
                    ]);
                }
                break;
            
            default:
                $db->rollback();
                JsonResponse([
                    'success'   => false,
                    'message'   => "Invalid action",
                    'data'      => []
                ]);
            break;
        }
        
        /** Insert note */
        Database::insert('tb_note', [
            "NOTE_MBR"   => $ACCOUNT_CHECK["ACC_MBR"],
            "NOTE_RACC"  => $ACCOUNT_CHECK["ID_ACC"],
            "NOTE_ACCDN" => $ACCOND_CHECK["ID_ACCCND"],
            "NOTE_TYPE"  => 'DEALER '.strtoupper($data["sbmt_act"]),
            "NOTE_NOTE"  => $data["sbmt_note"],
        ]);

        mysqli_commit($db);
        // $db->rollback();
        
    } catch (Exception | mysqli_sql_exception $e) {
        mysqli_rollback($db);
        JsonResponse([
            'code'      => 200,
            'success'   => false,
            'message'   => "Exception occured. Please try again!. Exception : ".str_replace("'", "", $e->getMessage()),
            'data'      => []
        ]);
    }

    Logger::admin_log([
        'admid' => $user['ADM_ID'],
        'module' => "account/progress_real_account/dealer",
        'message' => strtoupper($data["sbmt_act"])." dealer",
        'data'  => $data
    ]);

    JsonResponse([
        'code'      => 200,
        'success'   => true,
        'message'   => "Success ".$data["sbmt_act"],
        'data'      => (strtoupper($data["sbmt_act"] ?? "") == "ACCEPT") 
            ? ["reloc" => '/account/active_real_account/document/'.md5(md5($ACCOUNT_CHECK['ID_ACC']))]
            : ["reloc" => '/account/progress_real_account/view']
    ]);