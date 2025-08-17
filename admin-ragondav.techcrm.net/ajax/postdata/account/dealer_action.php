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

                /** create metatrader account */
                $password = Helper::generatePassword();
                $investor = Helper::generatePassword();
                $accountData = [
                    "master_pass" => $password, 
                    "investor_pass" => $investor, 
                    "group" => $ACCOUNT_CHECK['RTYPE_GROUP'], 
                    "fullname" => $ACCOUNT_CHECK['ACC_FULLNAME'], 
                    "email" => $ACCOUNT_CHECK['MBR_EMAIL'], 
                    "leverage" => $ACCOUNT_CHECK['RTYPE_LEVERAGE'], 
                    "comment" => "metaapi"
                ];
                
                $accountCreate = $apiManager->createAccount($accountData);
                if(!is_object($accountCreate) || !property_exists($accountCreate, "Login")) {
                    $db->rollback();
                    JsonResponse([
                        'success'   => false,
                        'message'   => "Gagal membuat akun metatrader",
                        'data'      => []
                    ]);
                }

                /** Test Connection */
                $login = $accountCreate->Login; // Update Login
                $connect = $apiTerminal->connect(['login' => $login, 'password' => $password]); // Test Connection
                if($connect) {
                    $UPDATE_RACC["ACC_TOKEN"] = $connect;
                }

                /** deposit margin */
                $depositMargin = $apiManager->deposit([
                    'login' => $login,
                    'amount' => $ACCOND_CHECK['ACCCND_AMOUNTMARGIN'],
                    'comment' => "acccnd-".$DEPOSIT_CHECK['ID_DPWD']
                ]);
                    
                /** Update RACC */
                $updateRaccData = [
                    'ACC_STS' => -1,
                    'ACC_WPCHECK' => 6,
                    'ACC_LOGIN' => $login,
                    'ACC_PASS' => base64_encode($password),
                    'ACC_INVESTOR' => base64_encode($investor)
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

                /** Send Email */
                $emailData = [
                    'subject'        => "Real Account Info",
                    'name'           => $ACCOUNT_CHECK["ACC_FULLNAME"],
                    'login'          => $login,
                    'metaPassword'   => $password,
                    'metaInvestor'   => $investor
                ];
                $emailSender = EmailSender::init(['email' => $ACCOUNT_CHECK['MBR_EMAIL'], 'name' => $ACCOUNT_CHECK['MBR_NAME']]);
                $emailSender->useFile("dealer", $emailData);
                $send = $emailSender->send();
                break;

            case 'reject':
                $updateRacc = Database::update('tb_racc', ['ACC_WPCHECK' => 4], ["ID_ACC" => $ACCOUNT_CHECK["ID_ACC"]]);
                if($updateRacc) {
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
            "NOTE_DPWD"  => $DEPOSIT_CHECK["ID_DPWD"],
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
        'data'      => [
            "reloc" => '/account/active_real_account/document/'.md5(md5($ACCOUNT_CHECK['ID_ACC']))
        ]
    ]);