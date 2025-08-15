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
    if(!$adminPermissionCore->hasPermission($authorizedPermission, "/account/wp_check_action")) {
        JsonResponse([
            'code'      => 200,
            'success'   => false,
            'message'   => "Authorization Failed",
            'data'      => []
        ]);
    }

    $data = Helper::getSafeInput($_POST);
    foreach(["sbmt_id", "sbmt_act", "sbmt_note", "forex"] as $req) {
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
    if(($ACCOUNT_CHECK["ACC_STS"] != 1 || $ACCOUNT_CHECK["ACC_WPCHECK"] != 3)){
        JsonResponse([
            'code'      => 200,
            'success'   => false,
            'message'   => "Invalid Account",
            'data'      => []
        ]);
    }

    /** Check Id Deposit */
    $DEPOSIT_CHECK = Dpwd::findByRaccId($ACCOUNT_CHECK["ID_ACC"]);
    if(!$DEPOSIT_CHECK){
        JsonResponse([
            'code'      => 200,
            'success'   => false,
            'message'   => "Deposit id not found",
            'data'      => []
        ]);
    }

    /** Check Valid Deposit New Account */
    if(($DEPOSIT_CHECK["DPWD_TYPE"] != 3 || $DEPOSIT_CHECK["DPWD_STS"] != -1)){
        JsonResponse([
            'code'      => 200,
            'success'   => false,
            'message'   => "Invalid deposit new account",
            'data'      => []
        ]);
    }

    /** Update RACC data*/
    $UPDATE_RACC = [
        "ACC_WPCHECK_DATE" => date("Y-m-d H:i:s")
    ];

    /**Execute database*/
    try {
        global $db;
        mysqli_report(MYSQLI_REPORT_ERROR|MYSQLI_REPORT_STRICT);
        mysqli_begin_transaction($db);


        /**Accept || Reject Processing*/
        switch ($data["sbmt_act"]) {
            case 'accept':
                $UPDATE_RACC["ACC_WPCHECK"] = 4;
                if(empty($data['login'])) {
                    $db->rollback();
                    JsonResponse([
                        'success'   => false,
                        'message'   => "Kolom login diperlukan",
                        'data'      => []
                    ]);
                }

                /** Login filter RACC*/
                $CHECK_LOGIN = $db->query('SELECT 1 FROM tb_racc WHERE tb_racc.ACC_TYPE = 1 AND tb_racc.ACC_LOGIN = "'.$data['login'].'"');
                if((!$CHECK_LOGIN) || ($CHECK_LOGIN->num_rows > 0)){
                    $db->rollback();
                    JsonResponse([
                        'success'   => false,
                        'message'   => "Nomer login sudah digunakan.",
                        'data'      => []
                    ]);
                }

                /** Login filter account condition*/
                $CHECK_ACCNDLGN = $db->query('SELECT 1 FROM tb_acccond WHERE tb_acccond.ACCCND_MBR != '.$ACCOUNT_CHECK["ACC_MBR"].' AND tb_acccond.ACCCND_LOGIN = "'.$data['login'].'"');
                if((!$CHECK_ACCNDLGN) || ($CHECK_ACCNDLGN->num_rows > 0)){
                    $db->rollback();
                    JsonResponse([
                        'success'   => false,
                        'message'   => "Nomer login sudah digunakan.",
                        'data'      => []
                    ]);
                }

                /** Insert account condition */
                Database::insert('tb_acccond', [
                    "ACCCND_MBR"            => $ACCOUNT_CHECK["ACC_MBR"],
                    "ACCCND_ACC"            => $ACCOUNT_CHECK["ID_ACC"],
                    "ACCCND_AMOUNTMARGIN"   => $DEPOSIT_CHECK["DPWD_AMOUNT"],
                    "ACCCND_CASH_FOREX"     => $data["forex"],
                    "ACCCND_LOGIN"          => $data["login"],
                    "ACCCND_DATEMARGIN"     => date("Y-m-d H:i:s")
                ]);
                break;

            case 'reject':
                $UPDATE_RACC["ACC_WPCHECK"] = 2;

                /** Update Deposit */
                Database::update('tb_dpwd', ["DPWD_STS" => 0], ["ID_DPWD" => $DEPOSIT_CHECK["ID_DPWD"]]);

                /** Delete account condition previous record(s) */
                Database::delete('tb_acccond', ["ACCCND_ACC" => $ACCOUNT_CHECK["ID_ACC"], "ACCCND_STS" => 0]);
                break;
            
            default:
                $db->rollback();
                JsonResponse([
                    'code'      => 200,
                    'success'   => false,
                    'message'   => "Invalid action",
                    'data'      => []
                ]);
            break;
        }

        
        /** Update RACC */
        Database::update('tb_racc', $UPDATE_RACC, ["ID_ACC" => $ACCOUNT_CHECK["ID_ACC"]]);

        /** Insert note */
        Database::insert('tb_note', [
            "NOTE_MBR"   => $ACCOUNT_CHECK["ACC_MBR"],
            "NOTE_RACC"  => $ACCOUNT_CHECK["ID_ACC"],
            "NOTE_DPWD"  => $DEPOSIT_CHECK["ID_DPWD"],
            "NOTE_ACCDN" => ($db->insert_id ?? 0),
            "NOTE_TYPE"  => 'WP CHECK '.strtoupper($data["sbmt_act"]),
            "NOTE_NOTE"  => $data["sbmt_note"],
        ]);

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
        'module' => "account/progress_real_account/wp_check",
        'message' => strtoupper($data["sbmt_act"])." wp check",
        'data'  => $data
    ]);

    JsonResponse([
        'code'      => 200,
        'success'   => true,
        'message'   => "Success ".$data["sbmt_act"],
        'data'      => [
            "reloc" => '/account/progress_real_account/view'
        ]
    ]);