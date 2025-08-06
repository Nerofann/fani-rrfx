<?php
require_once __DIR__ . "/../../config/setting.php";

use App\Factory\MetatraderFactory;
use App\Factory\VerihubFactory;
use App\Models\Account;
use App\Models\Admin;
use App\Models\FileUpload;
use App\Models\Helper;
use App\Models\Logger;
use App\Models\ProfilePerusahaan;
use App\Models\SendEmail;
use App\Models\User;
use Config\Core\Database;

class AppPost {
    private $db;
    private $ip_address;

    public function __construct()
    {
        //Do your magic here
        $this->db = Database::connect(); 
    }
    
    private function required(array $key, $from)
    {
        foreach($key as $k) {
            if(!array_key_exists($k, $from)) {
                return "Required data  [{$k}] not found.";
            }
        }

        return true;
    }

    private function generatePassword(int $len = 8) {
        $lower = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');
        $upper = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
        $specials = array('!','#','$','%','&','(',')','*','+',',','-','.',':',';','=','?','@','[',']','^','_','{','|','}','~');
        $digits = array('0','1','2','3','4','5','6','7','8','9');
        $all = array($lower, $upper, $specials, $digits);

        $pwd = $lower[array_rand($lower, 1)];
        $pwd = $pwd . $upper[array_rand($upper, 1)];
        $pwd = $pwd . $specials[array_rand($specials, 1)];
        $pwd = $pwd . $digits[array_rand($digits, 1)];

        for($i = strlen($pwd); $i < max(8, $len); $i++)
        {
            $temp = $all[array_rand($all, 1)];
            $pwd = $pwd . $temp[array_rand($temp, 1)];
        }

        return str_shuffle($pwd);
    } 

    private function checkCsrfToken(array $data = []) {
        if(empty($data['csrf_token'])) {
            exit(json_encode([
                'success'   => false,
                'alert'     => [
                    'title' => "Gagal",
                    'text'  => "Invalid CSRF_TOKEN",
                    'icon'  => "error"
                ]
            ]));
        }

        // if(!isValidCSRFToken($data['csrf_token'])) {
        //     exit(json_encode([
        //         'success'   => false,
        //         'alert'     => [
        //             'title' => "Gagal",
        //             'text'  => "CSRF_TOKEN Expired",
        //             'icon'  => "error"
        //         ]
        //     ]));
        // }

        return true;
    }

    private function checkProgressAccount(string $userid) {
        $progressAccount = Account::getProgressRealAccount($userid);
        if(empty($progressAccount)) {
            exit(json_encode([
                'success'   => false,
                'alert'     => [
                    'title' => "Gagal",
                    'text'  => "Invalid account",
                    'icon'  => "error"
                ]
            ]));  
        }

        return $progressAccount;
    }

    private function isAllowToEdit(int $status) {
        if(in_array($status, [1, -1])) {
            exit(json_encode([
                'success'   => false,
                'alert'     => [
                    'title' => "Gagal",
                    'text'  => "Akun sedang dalam prosess verifikasi",
                    'icon'  => "error"
                ]
            ]));  
        }

        return true;
    }

    public function getRegency($data, $user) {
        $progressAccount = $this->checkProgressAccount(md5(md5($user['MBR_ID'])));
        if(empty($data['province'])) {
            exit(json_encode([
                'success'   => false,
                'alert'     => [
                    'title' => "Gagal",
                    'text'  => "Province is required",
                    'icon'  => "error"
                ]
            ]));  
        }

        $province = base64_decode($data['province']);
        $sqlGet = $this->db->query("SELECT KDP_KABKO FROM tb_kodepos WHERE UPPER(KDP_PROV) = UPPER('{$province}') GROUP BY KDP_KABKO ORDER BY KDP_KABKO");
        if($sqlGet->num_rows == 0) {
            exit(json_encode([
                'success'   => false,
                'alert'     => [
                    'title' => "Gagal",
                    'text'  => "Province not found",
                    'icon'  => "error"
                ]
            ]));  
        }

        $list = [];
        foreach($sqlGet->fetch_all(MYSQLI_ASSOC) as $prov) {
            $list[] = [
                'name' => $prov['KDP_KABKO'],
                'selected' => ($progressAccount['ACC_REGENCY'] == $prov['KDP_KABKO'])
            ];
        }

        exit(json_encode([
            'success'   => true,
            'data'      => $list,
            'alert'     => [
                'title' => "Berhasil",
                'text'  => "Province found",
                'icon'  => "success"
            ]
        ]));  
    }

    public function getDistrict($data, $user) {
        $progressAccount = $this->checkProgressAccount(md5(md5($user['MBR_ID'])));
        if(empty($data['regency'])) {
            exit(json_encode([
                'success'   => false,
                'alert'     => [
                    'title' => "Gagal",
                    'text'  => "Regency is required",
                    'icon'  => "error"
                ]
            ]));  
        }

        $regency = $data['regency'];
        $sqlGet = $this->db->query("SELECT KDP_KECAMATAN FROM tb_kodepos WHERE UPPER(KDP_KABKO) = UPPER('{$regency}') GROUP BY KDP_KECAMATAN ORDER BY KDP_KECAMATAN");
        if($sqlGet->num_rows == 0) {
            exit(json_encode([
                'success'   => false,
                'alert'     => [
                    'title' => "Gagal",
                    'text'  => "Regency not found",
                    'icon'  => "error"
                ]
            ]));  
        }

        $list = [];
        foreach($sqlGet->fetch_all(MYSQLI_ASSOC) as $prov) {
            $list[] = [
                'name' => $prov['KDP_KECAMATAN'],
                'selected' => ($progressAccount['ACC_DISTRICT'] == $prov['KDP_KECAMATAN'])
            ];
        }

        exit(json_encode([
            'success'   => true,
            'data'      => $list,
            'alert'     => [
                'title' => "Berhasil",
                'text'  => "Regency found",
                'icon'  => "success"
            ]
        ]));  
    }

    public function getVillages($data, $user) {
        $progressAccount = $this->checkProgressAccount(md5(md5($user['MBR_ID'])));
        if(empty($data['district'])) {
            exit(json_encode([
                'success'   => false,
                'alert'     => [
                    'title' => "Gagal",
                    'text'  => "District is required",
                    'icon'  => "error"
                ]
            ]));  
        }

        $district = $data['district'];
        $sqlGet = $this->db->query("SELECT KDP_KELURAHAN, KDP_POS FROM tb_kodepos WHERE UPPER(KDP_KECAMATAN) = UPPER('{$district}') GROUP BY KDP_KELURAHAN ORDER BY KDP_KELURAHAN");
        if($sqlGet->num_rows == 0) {
            exit(json_encode([
                'success'   => false,
                'alert'     => [
                    'title' => "Gagal",
                    'text'  => "District not found",
                    'icon'  => "error"
                ]
            ]));  
        }

        $list = [];
        foreach($sqlGet->fetch_all(MYSQLI_ASSOC) as $vil) {
            $list[] = [
                'village'   => $vil['KDP_KELURAHAN'],
                'selected'  => ($progressAccount['ACC_VILLAGE'] == $vil['KDP_KELURAHAN']),
                'postalCode'=> $vil['KDP_POS'] 
            ];
        }

        exit(json_encode([
            'success'   => true,
            'data'      => $list,
            'alert'     => [
                'title' => "Berhasil",
                'text'  => "District found",
                'icon'  => "success"
            ]
        ]));  
    }

    public function unrequireNPWP($type_acc = 0){
        $progressAccount = $type_acc;
        $SQL_ACC_TYPE  = mysqli_query($this->db, 'SELECT tb_racctype.RTYPE_TYPE FROM tb_racctype WHERE tb_racctype.ID_RTYPE = '.$progressAccount.' LIMIT 1');
        $rlst_acc_type = ($SQL_ACC_TYPE && mysqli_num_rows($SQL_ACC_TYPE) > 0) ? mysqli_fetch_assoc($SQL_ACC_TYPE)["RTYPE_TYPE"] : 0;
        return (strtolower($rlst_acc_type) == strtolower('micro') || strtolower($rlst_acc_type) == strtolower('mikro')) ? false : true;
    }

    public function createDemo($data, $user) {
        global $web_name_full;
        $this->checkCsrfToken($data);
       
        
        
        $mbrid = $user['MBR_ID'] ?? 0;
        $demoAccount = Account::getDemoAccount(md5(md5($mbrid)));
        if(!empty($demoAccount)) {
            JsonResponse([
                'success' => false,
                'message' => "Sudah memiliki akun demo",
                'data' => []
            ]);
        }

        $init_margin = 10000;
        $meta_pass = Account::generatePassword();
        $meta_investor = Account::generatePassword();
        $meta_phone = Account::generatePassword();

        /** Create Demo */
        $apiManager = MetatraderFactory::apiManager();
        $apiData = [
            'master_pass' => $meta_pass, 
            'investor_pass' => $meta_investor, 
            'group' => "demo\MandiriInvestindo\MMUSD", 
            'fullname' => $user['MBR_NAME'], 
            'email' => $user['MBR_EMAIL'], 
            'leverage' => 200,
            'comment' => "metaapi"
        ];

        $createDemo = $apiManager->createAccount($apiData);
        if(!is_object($createDemo) || !property_exists($createDemo, "Login")) {
            JsonResponse([
                'success' => false,
                'message' => "Gagal membuat akun demo",
                'data' => []
            ]);
        }

        /** Insert Balance **/
        $deposit = $apiManager->deposit([
            'login' => $createDemo->Login,
            'amount' => $init_margin,
            'comment' => "metaapi"
        ]);

        /** Get Demo Type */
        $sqlGetType = $this->db->query("SELECT ID_RTYPE FROM tb_racctype WHERE UPPER(RTYPE_TYPE) = 'DEMO' LIMIT 1");
        $demoType = $sqlGetType->fetch_assoc()['ID_RTYPE'] ?? 0;
        if($sqlGetType->num_rows == 0 || $demoType == 0) {
            JsonResponse([
                'success' => false,
                'message' => "Gagal membuat akun demo, Jenis akun tidak valid",
                'data' => []
            ]);

            exit(json_encode([
                'success'   => false,
                'error'     => ""
            ]));
        }

        /** Insert Demo */
        $insertDemo = Database::insert("tb_racc", [
            'ACC_MBR' => $user['MBR_ID'],
            'ACC_DERE' => 2,
            'ACC_TYPE' => $demoType,
            'ACC_LOGIN' => $createDemo->Login,
            'ACC_PASS' => $meta_pass,
            'ACC_INVESTOR' => $meta_investor,
            'ACC_PASSPHONE' => $meta_phone,
            'ACC_INITIALMARGIN' => $init_margin,
            'ACC_FULLNAME' => $user['MBR_NAME'],
            'ACC_DATETIME' => date("Y-m-d H:i:s"),
        ]);

        /** Send Notification Email */
        $data   = [
            "name"          => $user["MBR_NAME"],
            "login"         => $createDemo->Login,
            "metaPassword"  => $meta_pass,
            "metaInvestor"  => $meta_investor,
            "metaPassPhone" => $meta_phone,
            "subject"       => "Demo Account Information {$web_name_full} ".date('Y-m-d H:i:s')
        ];
        
        $sendEmail = new SendEmail();
        $sendEmail->useDefault()->useFile("create-demo", $data)->destination($user['MBR_EMAIL'], $user['MBR_NAME'])->send();

        Logger::client_log([
            'mbrid' => $mbrid,
            'module' => "create-demo",
            'message' => "Create Demo Account {$createDemo->Login}",
            'data'  => json_encode($_POST)
        ]);

        exit(json_encode([
            'success'   => true,
            'error'     => "",
            'message'   => "Buat akun demo berhasil",
            'data'      => [
                'login' => $createDemo->Login,
                'passw' => $meta_pass,
                'invst' => $meta_investor,
                'phone' => $meta_phone,
                'mails' => "Silakan periksa email Anda.Dan jangan beri tahu password, investor, passphone Anda kepada siapa pun!"
            ]
        ]));
    }

    public function accountType($data, $user) {
        $this->checkCsrfToken($data);
        if(empty($data['account-type'])) {
            exit(json_encode([
                'success'   => false,
                'alert'     => [
                    'title' => "Gagal",
                    'text'  => "Jenis akun diperlukan",
                    'icon'  => "error"
                ]
            ]));
        }

        /** Account Suffix */
        $suffix = $data['account-type'];
        $raccType = Account::checkAccountSuffix($suffix);
        if(empty($raccType)) {
            exit(json_encode([
                'success'   => false,
                'alert'     => [
                    'title' => "Gagal",
                    'text'  => "Jenis Akun Tidak Ditemukan",
                    'icon'  => "error"
                ]
            ]));
        }
        
        /** Cek apakah produk compatibel dengan akunnya */
        if(!empty($user['MBR_SUFFIX'])) {
            if($user['MBR_SUFFIX'] != $suffix) {
                exit(json_encode([
                    'success'   => false,
                    'alert'     => [
                        'title' => "Gagal",
                        'text'  => "Jenis akun tidak valid",
                        'icon'  => "error"
                    ]
                ]));
            }
        }

        if(!empty($user['MBR_SUFFIX_EXCLUDE'])) {
            $explode = explode(",", $user['MBR_SUFFIX_EXCLUDE']);
            if(in_array($suffix, $explode)) {
                exit(json_encode([
                    'success'   => false,
                    'alert'     => [
                        'title' => "Gagal",
                        'text'  => "Jenis akun tidak valid (2)",
                        'icon'  => "error"
                    ]
                ]));
            }
        }

        /** Check create akun multi */
        if(strtoupper($raccType['RTYPE_TYPE_AS']) == "MULTILATERAL") {
            if($user['MBR_ACCMULTI'] != -1) {
                exit(json_encode([
                    'success'   => false,
                    'alert'     => [
                        'title' => "Gagal",
                        'text'  => "Mohon hubungi CS untuk membuat akun Multilateral",
                        'icon'  => "error"
                    ]
                ]));
            }
        }

        /** Check max account */
        $realAcc = Account::all($user['MBR_ID']);
        $microAcc = [];
        foreach($realAcc as $acc) {
            if(strtolower($acc['RTYPE_TYPE']) == "micro") {
                $microAcc[] = $acc;
            }
        }

        if(count($realAcc) >= $user['MBR_ACCMAX']) {
            exit(json_encode([
                'success'   => false,
                'alert'     => [
                    'title' => "Gagal",
                    'text'  => "Sudah mencapai limit pembuatan real account",
                    'icon'  => "error"
                ]
            ]));
        }

        if(strtoupper($raccType['RTYPE_TYPE']) == "MICRO") {
            if(count($microAcc) >= $user['MBR_ACCMAX_MICRO']) {
                exit(json_encode([
                    'success'   => false,
                    'alert'     => [
                        'title' => "Gagal",
                        'text'  => "Sudah mencapai limit pembuatan real account (micro)",
                        'icon'  => "error"
                    ]
                ]));
            }
        }

        /** Get Progress Real Account */
        $progressAccount = Account::getProgressRealAccount($user['userid']);
        if(empty($progressAccount)) {
            /** Jika sebelumnya sudah punya akun, dapat diduplicate */
            if(!empty(Account::getLastAccount($user['userid']))) {
                $duplicate = Account::duplicateLastAccount($user['userid']);
                if(empty($duplicate) || !is_array($duplicate)) {
                    exit(json_encode([
                        'success'   => false,
                        'alert'     => [
                            'title' => "Gagal",
                            'text'  => "Status salinan tidak valid",
                            'icon'  => "error"
                        ]
                    ]));
                }

            }else {
                /** Insert row baru, Jika belum punya akun sama sekali / baru pertama create akun */
                $insert = Database::insert("tb_racc", [
                    'ACC_MBR'   => $user['MBR_ID'],
                    'ACC_TYPE'  => $raccType['ID_RTYPE'],
                    'ACC_DERE'  => 1,
                    'ACC_LOGIN' => 0,
                    'ACC_STS'   => 0
                ]);

                if(empty($insert)) {
                    exit(json_encode([
                        'success'   => false,
                        'alert'     => [
                            'title' => "Gagal",
                            'text'  => "Gagal membuat akun",
                            'icon'  => "error"
                        ]
                    ]));
                }
            }
        }
        
        /** Get Ulang Progress real account */
        $progressAccount = Account::getProgressRealAccount($user['userid']);

        /** Check Status */
        $this->isAllowToEdit(status: $progressAccount['ACC_STS']);

        /** Update Type */
        if($progressAccount['ACC_TYPE'] != $raccType['ID_RTYPE']) {
            $updateData = [
                'ACC_TYPE' => $raccType['ID_RTYPE'],
                'ACC_LAST_STEP' => "profile-perusahaan",
            ];

            $update = Database::update("tb_racc", $updateData, ['ID_ACC' => $progressAccount['ID_ACC']]);
            if(!$update) {
                exit(json_encode([
                    'success'   => false,
                    'alert'     => [
                        'title' => "Gagal",
                        'text'  => "Perbarui Jenis Akun Gagal",
                        'icon'  => "error"
                    ]
                ]));
            }
        }

        Logger::client_log([
            'mbrid' => $user['MBR_ID'],
            'module' => "create-account",
            'message' => "Progress Real Account (Account Type)",
            'data'  => ($data)
        ]);

        exit(json_encode([
            'success'   => true,
            'redirect'  => "/account/create?page=profile-perusahaan",
            'alert'     => [
                'title' => "Berhasil",
                'text'  => "Pilih Jenis Akun Sukses",
                'icon'  => "success"
            ]
        ]));
    }

    public function profilePerusahaan($data, $user) {
        $this->checkCsrfToken($data);
        
        $agree = $data['aggree'] ?? "Tidak";
        if(strtolower($agree) != "ya") {
            exit(json_encode([
                'success'   => false,
                'alert'     => [
                    'title' => "Gagal",
                    'text'  => "Anda harus menyetujui untuk melanjutkan ke tahap berikutnya",
                    'icon'  => "error"
                ]
            ]));  
        }

        /** Get Account */
        $progressAccount = $this->checkProgressAccount($user['userid']);
        
        /** Check Status */
        $this->isAllowToEdit(status: $progressAccount['ACC_STS']);

        /** Update */
        $updateData = [
            'ACC_F_PROFILE' => 1,
            'ACC_F_PROFILE_IP' => Helper::get_ip_address(),
            'ACC_F_PROFILE_PERYT' => "Yes",
            'ACC_F_PROFILE_DATE' => date("Y-m-d H:i:s"),
            'ACC_LAST_STEP' => "pernyataan-simulasi",
        ];

        $update = Database::update("tb_racc", $updateData, ['ID_ACC' => $progressAccount['ID_ACC']]);
        if(!$update) {
            exit(json_encode([
                'success'   => false,
                'alert'     => [
                    'title' => "Gagal",
                    'text'  => "Perbarui Jenis Akun Gagal",
                    'icon'  => "error"
                ]
            ]));
        }
        
        $sqlUpdate = $this->db->prepare("UPDATE tb_racc SET  = 1,  = ?,  = 'Yes',  = ? WHERE ID_ACC = ?");
        $sqlUpdate->bind_param("ssi", $ipAddress, $datetime, $progressAccount['ID_ACC']);
        if(!$sqlUpdate->execute()) {
            exit(json_encode([
                'success'   => false,
                'alert'     => [
                    'title' => "Gagal",
                    'text'  => "Gagal menyimpan kemajuan saat ini",
                    'icon'  => "error"
                ]
            ])); 
        }

        Logger::client_log([
            'mbrid' => $user['MBR_ID'],
            'module' => "create-account",
            'ref' => $progressAccount['ID_ACC'],
            'message' => "Progress Real Account (Profile Perusahaan)",
            'data'  => json_encode($data)
        ]);

        exit(json_encode([
            'success'   => true,
            'redirect'  => "/account/create?page=pernyataan-simulasi",
            'alert'     => [
                'title' => "Berhasil",
                'text'  => "Successfull",
                'icon'  => "success"
            ]
        ]));
    }

    public function pernyataanSimulasi($data, $user) {
        $this->checkCsrfToken($data);
        $progressAccount = $this->checkProgressAccount(md5(md5($user['MBR_ID'])));
        
        /** Check Status */
        $this->isAllowToEdit(status: $progressAccount['ACC_STS']);

        $required = [
            "smls_namleng"  => "Nama Lengkap", 
            "smls_tmptlhr" => "Tempat Lahir", 
            "smls_tgllhr" => "Tanggal Lahir", 
            "smls_almtrmh" => "Alamat Rumah", 
            "smls_almtrmh_prov" => "Provinsi", 
            "smls_almtrmh_kabkot" => "Kabupaten", 
            "smls_almtrmh_kcmtn" => "Kecamatan", 
            "smls_almtrmh_desa" => "Kelurahan", 
            "smls_kodepos" => "Kode Pos", 
            "smls_almtrmh_rw" => "RW",
            "smls_almtrmh_rt" => "RT",
            "smls_tipeidt" => "Tipe Identitas",
            "smls_nomidt" => "Nomor Identitas",
            "aggree" => "Persetujuan"
        ];

        foreach($required as $r => $text) {
            if(empty($data[ $r ])) {
                exit(json_encode([
                    'success'   => false,
                    'alert'     => [
                        'title' => "Gagal",
                        'text'  => "{$text} diperlukan",
                        'icon'  => "error"
                    ]
                ]));
            }
        }

        /** Check Peresetujuan */
        $agree = $data['aggree'] ?? "Tidak";
        if(strtolower($agree) != "ya") {
            exit(json_encode([
                'success'   => false,
                'alert'     => [
                    'title' => "Gagal",
                    'text'  => "Anda harus menyetujui untuk melanjutkan ke tahap berikutnya",
                    'icon'  => "error"
                ]
            ]));  
        }

        $demoAccount = Account::getDemoAccount(md5(md5($user['MBR_ID'])));
        if(empty($demoAccount)) {
            exit(json_encode([
                'success'   => false,
                'alert'     => [
                    'title' => "Gagal",
                    'text'  => "Akun demo tidak valid",
                    'icon'  => "error"
                ]
            ]));  
        }

        
        /** Upload File */
        if(empty($progressAccount['ACC_F_SIMULASI_IMG'])) {
            /** Check file */
            if(empty($_FILES['smls_demofile']) || $_FILES['smls_demofile']['error'] != 0) {
                exit(json_encode([
                    'success'   => false,
                    'alert'     => [
                        'title' => "Gagal",
                        'text'  => "Mohon upload file demo account",
                        'icon'  => "error"
                    ]
                ]));
            }

            $uploadFile = FileUpload::upload_myfile($_FILES['smls_demofile'], "demo_img_");
            if(!is_array($uploadFile) || !array_key_exists("filename", $uploadFile)) {
                exit(json_encode([
                    'success'   => false,
                    'alert'     => [
                        'title' => "Gagal",
                        'text'  => "Gagal mengunggah file, {$uploadFile}",
                        'icon'  => "error"
                    ]
                ]));
            }

            /** Update Image */
            $updateImage = Database::update("tb_racc", ['ACC_F_SIMULASI_IMG' => $uploadFile['filename']], ['ID_ACC' => $progressAccount['ID_ACC']]);
            if($updateImage !== TRUE) {
                exit(json_encode([
                    'success'   => false,
                    'alert'     => [
                        'title' => "Gagal",
                        'text'  => "Gagal menyimpan file, {$updateImage}",
                        'icon'  => "error"
                    ]
                ]));
            }
        }

        /** Check Alamat */
        $province = base64_decode($data['smls_almtrmh_prov']);
        $regency = $data['smls_almtrmh_kabkot'];
        $district = $data['smls_almtrmh_kcmtn'];
        $village = $data['smls_almtrmh_desa'];
        $postalCode = $data['smls_kodepos'];
        $sqlCheckAddress = $this->db->query("SELECT ID_KDP FROM tb_kodepos WHERE UPPER(KDP_KELURAHAN) = '{$village}' AND KDP_KECAMATAN = '{$district}' AND KDP_KABKO = '{$regency}' AND KDP_PROV = '{$province}' AND KDP_POS = $postalCode LIMIT 1");
        if($sqlCheckAddress->num_rows != 1) {
            exit(json_encode([
                'success'   => false,
                'alert'     => [
                    'title' => "Gagal",
                    'text'  => "Kode pos tidak ditemukan / salah",
                    'icon'  => "error"
                ]
            ]));
        }

        /** Check Tipe identitas */
        if(!in_array($data['smls_tipeidt'], ["KTP"])) {
            exit(json_encode([
                'success'   => false,
                'alert'     => [
                    'title' => "Gagal",
                    'text'  => "Tipe identitas tidak didukung",
                    'icon'  => "error"
                ]
            ]));
        }

        /** Check No Identitas */
        switch(strtoupper($data['smls_tipeidt'])) {
            case "KTP":
                $noKtp = $data['smls_nomidt'];
                if(is_numeric($noKtp) === FALSE) {
                    exit(json_encode([
                        'success'   => false,
                        'alert'     => [
                            'title' => "Gagal",
                            'text'  => "Nomor KTP tidak valid",
                            'icon'  => "error"
                        ]
                    ]));
                }

                if(strlen($noKtp) < 16) {
                    exit(json_encode([
                        'success'   => false,
                        'alert'     => [
                            'title' => "Gagal",
                            'text'  => "Nomor KTP harus 16 digit atau lebih",
                            'icon'  => "error"
                        ]
                    ]));
                }

                /** Check No KTP apakah sudah digunakan user lain */
                $sqlCheckKTP = $this->db->query("
                    SELECT 
                        ID_ACC 
                    FROM tb_racc 
                    JOIN tb_member ON (MBR_ID = ACC_MBR)
                    WHERE ACC_NO_IDT = '{$noKtp}' 
                    AND ACC_MBR != '".$user['MBR_ID']."'
                    LIMIT 1
                ");

                if($sqlCheckKTP->num_rows != 0) {
                    exit(json_encode([
                        'success'   => false,
                        'alert'     => [
                            'title' => "Gagal",
                            'text'  => "Nomor KTP telah terdaftar/digunakan",
                            'icon'  => "error"
                        ]
                    ]));
                }
                break;
        }

        /** Update tb_racc */
        $updateRacc = Database::update("tb_racc", [
            'ACC_F_SIMULASI'        => 1,
            'ACC_F_SIMULASI_IP'     => Helper::get_ip_address(),
            'ACC_F_SIMULASI_PERYT'  => "Ya",
            'ACC_F_SIMULASI_DATE'   => date("Y-m-d H:i:s"),
            'ACC_PROVINCE'          => $province,
            'ACC_REGENCY'           => $regency,
            'ACC_DISTRICT'          => $district,
            'ACC_VILLAGE'           => $village,
            'ACC_ZIPCODE'           => $postalCode,
            'ACC_RW'                => $data['smls_almtrmh_rw'],
            'ACC_RT'                => $data['smls_almtrmh_rt'],
            'ACC_ADDRESS'           => $data['smls_almtrmh'],
            'ACC_FULLNAME'          => $data['smls_namleng'],
            'ACC_TEMPAT_LAHIR'      => $data['smls_tmptlhr'],
            'ACC_TANGGAL_LAHIR'     => $data['smls_tgllhr'],
            'ACC_TYPE_IDT'          => $data['smls_tipeidt'],
            'ACC_NO_IDT'            => $data['smls_nomidt'],
            'ACC_DEMO'              => $demoAccount['ACC_LOGIN']
        ], [
            'ID_ACC' => $progressAccount['ID_ACC']
        ]);

        if($updateRacc !== TRUE) {
            exit(json_encode([
                'success'   => false,
                'alert'     => [
                    'title' => "Gagal",
                    'text'  => $updateRacc ?? "Gagal memperbarui akun pengguna",
                    'icon'  => "error"
                ]
            ]));
        }

        Logger::client_log([
            'mbrid' => $user['MBR_ID'],
            'module' => "create-account",
            'message' => "Progress Real Account (Pernyataan Simulasi)",
            'data'  => ($data)
        ]);

        exit(json_encode([
            'success'   => true,
            'redirect'  => "/account/create?page=pernyataan-pengalaman",
            'alert'     => [
                'title' => "Berhasil",
                'text'  => "Berhasil disimpan",
                'icon'  => "success"
            ]
        ]));
    }

    public function pernyataanPengalaman($data, $user) {
        $this->checkCsrfToken($data);
        $progressAccount = $this->checkProgressAccount(md5(md5($user['MBR_ID'])));

        /** Check Status */
        $this->isAllowToEdit(status: $progressAccount['ACC_STS']);

        if(empty($data['pengalaman'])) {
            exit(json_encode([
                'success'   => false,
                'alert'     => [
                    'title' => "Gagal",
                    'text'  => "Pernyataan pengalaman diperlukan",
                    'icon'  => "error"
                ]
            ]));
        }

        /** Check Peresetujuan */
        $agree = $data['aggree'] ?? "Tidak";
        if(strtolower($agree) != "ya") {
            exit(json_encode([
                'success'   => false,
                'alert'     => [
                    'title' => "Gagal",
                    'text'  => "Anda harus menyetujui untuk melanjutkan ke tahap berikutnya",
                    'icon'  => "error"
                ]
            ]));  
        }

        if(strtolower($data['pengalaman']) == "Ya") {
            if(empty($data['perusahaan'])) {
                exit(json_encode([
                    'success'   => false,
                    'alert'     => [
                        'title' => "Gagal",
                        'text'  => "Nama Perusahaan Berjangka diperlukan",
                        'icon'  => "error"
                    ]
                ])); 
            }
        }

        
        
        $update = Database::update("tb_racc", [
            'ACC_F_PENGLAMAN'       => 1,
            'ACC_F_PENGLAMAN_IP'    => Helper::get_ip_address(),
            'ACC_F_PENGLAMAN_PERYT' => "Ya",
            'ACC_F_PENGLAMAN_PERYT_YA' => $data['pengalaman'],
            'ACC_F_PENGLAMAN_PERSH' => $data['perusahaan'] ?? NULL,
            'ACC_F_PENGLAMAN_DATE'  => date("Y-m-d H:i:s")
        ], [
            'ID_ACC' => $progressAccount['ID_ACC']
        ]);

        if($update !== TRUE) {
            exit(json_encode([
                'success'   => false,
                'alert'     => [
                    'title' => "Gagal",
                    'text'  => $update ?? "Gagal Memperbarui kemajuan Akun Real",
                    'icon'  => "error"
                ]
            ])); 
        }

        Logger::client_log([
            'mbrid' => $user['MBR_ID'],
            'module' => "create-account",
            'ref' => $progressAccount['ID_ACC'],
            'message' => "Progress Real Account (Pernyataan Pengalaman Transaksi Perdagangan Berjangka)",
            'data'  => ($data)
        ]);

        exit(json_encode([
            'success'   => true,
            'redirect'  => "/account/create?page=pernyataan-pengungkapan-1",
            'alert'     => [
                'title' => "Berhasil",
                'text'  => "Berhasil disimpan",
                'icon'  => "success"
            ]
        ])); 
    }

    public function pernyataanPengungkapan_1($data, $user) {
        $this->checkCsrfToken($data);
        $progressAccount = $this->checkProgressAccount(md5(md5($user['MBR_ID'])));  
        
        /** Check Status */
        $this->isAllowToEdit(status: $progressAccount['ACC_STS']);

        /** Check Peresetujuan */
        $agree = $data['aggree'] ?? "Tidak";
        if(strtolower($agree) != "ya") {
            exit(json_encode([
                'success'   => false,
                'alert'     => [
                    'title' => "Gagal",
                    'text'  => "Anda harus menyetujui untuk melanjutkan ke tahap berikutnya",
                    'icon'  => "error"
                ]
            ]));  
        }

        
        
        $update = Database::update("tb_racc", [
            'ACC_F_DISC'        => 1,
            'ACC_F_DISC_IP'     => Helper::get_ip_address(),
            'ACC_F_DISC_PERYT'  => "Ya",
            'ACC_F_DISC_DATE'   => date("Y-m-d H:i:s")
        ], [
            'ID_ACC' => $progressAccount['ID_ACC']
        ]);

        if($update !== TRUE) {
            exit(json_encode([
                'success'   => false,
                'alert'     => [
                    'title' => "Gagal",
                    'text'  => $update ?? "Gagal Memperbarui kemajuan Akun Real",
                    'icon'  => "error"
                ]
            ])); 
        }

        Logger::client_log([
            'mbrid' => $user['MBR_ID'],
            'module' => "create-account",
            'ref' => $progressAccount['ID_ACC'],
            'message' => "Progress Real Account (Pernyataan Pengungkapan 1)",
            'data'  => ($data)
        ]);

        exit(json_encode([
            'success'   => true,
            'redirect'  => "/account/create?page=aplikasi-pembukaan-rekening",
            'alert'     => [
                'title' => "Berhasil",
                'text'  => "Berhasil disimpan",
                'icon'  => "success"
            ]
        ])); 
    }

    public function pernyataanPengungkapan_2($data, $user) {
        $this->checkCsrfToken($data);
        $progressAccount = $this->checkProgressAccount(md5(md5($user['MBR_ID'])));  
        
        /** Check Status */
        $this->isAllowToEdit(status: $progressAccount['ACC_STS']);

        /** Check Peresetujuan */
        $agree = $data['aggree'] ?? "Tidak";
        if(strtolower($agree) != "ya") {
            exit(json_encode([
                'success'   => false,
                'alert'     => [
                    'title' => "Gagal",
                    'text'  => "Anda harus menyetujui untuk melanjutkan ke tahap berikutnya",
                    'icon'  => "error"
                ]
            ]));  
        }

        
        
        $update = Database::update("tb_racc", [
            'ACC_F_DISC2'        => 1,
            'ACC_F_DISC_IP2'     => Helper::get_ip_address(),
            'ACC_F_DISC_PERYT2'  => "Ya",
            'ACC_F_DISC_DATE2'   => date("Y-m-d H:i:s")
        ], [
            'ID_ACC' => $progressAccount['ID_ACC']
        ]);

        if($update !== TRUE) {
            exit(json_encode([
                'success'   => false,
                'alert'     => [
                    'title' => "Gagal",
                    'text'  => $update ?? "Gagal Memperbarui kemajuan Akun real",
                    'icon'  => "error"
                ]
            ])); 
        }

        Logger::client_log([
            'mbrid' => $user['MBR_ID'],
            'module' => "create-account",
            'ref' => $progressAccount['ID_ACC'],
            'message' => "Progress Real Account (Pernyataan Pengungkapan 2)",
            'data'  => ($data)
        ]);

        exit(json_encode([
            'success'   => true,
            'redirect'  => "/account/create?page=formulir-dokumen-resiko",
            'alert'     => [
                'title' => "Berhasil",
                'text'  => "Berhasil disimpan",
                'icon'  => "success"
            ]
        ])); 
    }

    public function pernyataanPengungkapan_3($data, $user) {
        $this->checkCsrfToken($data);
        $progressAccount = $this->checkProgressAccount(md5(md5($user['MBR_ID'])));  
        
        /** Check Status */
        $this->isAllowToEdit(status: $progressAccount['ACC_STS']);

        /** Check Peresetujuan */
        $agree = $data['aggree'] ?? "Tidak";
        if(strtolower($agree) != "ya") {
            exit(json_encode([
                'success'   => false,
                'alert'     => [
                    'title' => "Gagal",
                    'text'  => "Anda harus menyetujui untuk melanjutkan ke tahap berikutnya",
                    'icon'  => "error"
                ]
            ]));  
        }

        
        
        $update = Database::update("tb_racc", [
            'ACC_F_DISC3'        => 1,
            'ACC_F_DISC_IP3'     => Helper::get_ip_address(),
            'ACC_F_DISC_PERYT3'  => "Ya",
            'ACC_F_DISC_DATE3'   => date("Y-m-d H:i:s")
        ], [
            'ID_ACC' => $progressAccount['ID_ACC']
        ]);

        if($update !== TRUE) {
            exit(json_encode([
                'success'   => false,
                'alert'     => [
                    'title' => "Gagal",
                    'text'  => $update ?? "Gagal Memperbarui kemajuan Akun real",
                    'icon'  => "error"
                ]
            ])); 
        }

        Logger::client_log([
            'mbrid' => $user['MBR_ID'],
            'module' => "create-account",
            'ref' => $progressAccount['ID_ACC'],
            'message' => "Progress Real Account (Pernyataan Pengungkapan 3)",
            'data'  => ($data)
        ]);

        exit(json_encode([
            'success'   => true,
            'redirect'  => "/account/create?page=perjanjian-pemberian-amanat",
            'alert'     => [
                'title' => "Berhasil",
                'text'  => "Berhasil disimpan",
                'icon'  => "success"
            ]
        ])); 
    }

    public function pernyataanPengungkapan_4($data, $user) {
        $this->checkCsrfToken($data);
        $progressAccount = $this->checkProgressAccount(md5(md5($user['MBR_ID'])));  
        
        /** Check Status */
        $this->isAllowToEdit(status: $progressAccount['ACC_STS']);

        /** Check Peresetujuan */
        $agree = $data['aggree'] ?? "Tidak";
        if(strtolower($agree) != "ya") {
            exit(json_encode([
                'success'   => false,
                'alert'     => [
                    'title' => "Gagal",
                    'text'  => "Anda harus menyetujui untuk melanjutkan ke tahap berikutnya",
                    'icon'  => "error"
                ]
            ]));  
        }

        
        
        $update = Database::update("tb_racc", [
            'ACC_F_DISC4'        => 1,
            'ACC_F_DISC_IP4'     => Helper::get_ip_address(),
            'ACC_F_DISC_PERYT4'  => "Ya",
            'ACC_F_DISC_DATE4'   => date("Y-m-d H:i:s")
        ], [
            'ID_ACC' => $progressAccount['ID_ACC']
        ]);

        if($update !== TRUE) {
            exit(json_encode([
                'success'   => false,
                'alert'     => [
                    'title' => "Gagal",
                    'text'  => $update ?? "Gagal Memperbarui kemajuan Akun real",
                    'icon'  => "error"
                ]
            ])); 
        }

        Logger::client_log([
            'mbrid' => $user['MBR_ID'],
            'module' => "create-account",
            'ref' => $progressAccount['ID_ACC'],
            'message' => "Progress Real Account (Pernyataan Pengungkapan 4)",
            'data'  => ($data)
        ]);

        exit(json_encode([
            'success'   => true,
            'redirect'  => "/account/create?page=verifikasi-identitas",
            'alert'     => [
                'title' => "Berhasil",
                'text'  => "Berhasil disimpan",
                'icon'  => "success"
            ]
        ])); 
    }

    public function aplikasiPembukaanRekening($data, $user) {
        $this->checkCsrfToken($data);
        $progressAccount = $this->checkProgressAccount(md5(md5($user['MBR_ID'])));  
    
        /** Check Status */
        $this->isAllowToEdit(status: $progressAccount['ACC_STS']);

        /** Check Peresetujuan */
        $agree = $data['aggree'] ?? "Tidak";
        if(strtolower($agree) != "ya") {
            exit(json_encode([
                'success'   => false,
                'alert'     => [
                    'title' => "Gagal",
                    'text'  => "Anda harus menyetujui untuk melanjutkan ke tahap berikutnya",
                    'icon'  => "error"
                ]
            ]));  
        }

        /** Check Jenis Dokumen */
        if(empty($data['app_dokumen_pendukung'])) {
            exit(json_encode([
                'success' => false,
                'alert' => [
                    'title' => "Gagal",
                    'text'  => "Mohon Pilih Jenis Dokumen Pendukung",
                    'icon'  => "error"
                ] 
            ]));
        }

        $dokumenPendukung = [
            'Cover Buku Tabungan (Recommended)',
            'Tagihan Kartu Kredit',
            'Tagihan Listrik / Air',
            'Scan Kartu NPWP',
            'Rekening Koran Bank',
            'PBB / BPJS',
            'Lainnya'
        ];

        $dokumenPendukung = array_map(fn($ar): string => strtoupper($ar), $dokumenPendukung);
        if(!in_array(strtoupper($data['app_dokumen_pendukung']), $dokumenPendukung)) {
            exit(json_encode([
                'success' => false,
                'alert' => [
                    'title' => "Gagal",
                    'text'  => "Dokumen Pendukung (".$data['app_dokumen_pendukung'].") tidak valid/tersedia",
                    'icon'  => "error"
                ] 
            ]));
        }

        /** Validasi APP Data Pribadi */
        $this->aplikasiPembukaanRekening_DataPribadi($data, $user, $progressAccount);

        /** Validasi APP Pihak Darurat */
        $this->aplikasiPembukaanRekening_PihakDarurat($data, $user, $progressAccount);

        /** Validasi APP Pekerjaan */
        $this->aplikasiPembukaanRekening_Pekerjaan($data, $user, $progressAccount);
        
        /** Validasi APP Daftar Kekayaan */
        $this->aplikasiPembukaanRekening_DaftarKekayaan($data, $user, $progressAccount);

        /** Validasi APP Dokumen Pendukung */
        $this->aplikasiPembukaanRekening_DokumenPendukung($data, $user, $progressAccount);


        
        
        Database::update("tb_racc", [
            'ACC_F_APP' => 1,
            'ACC_F_APP_IP' => Helper::get_ip_address(),
            'ACC_F_APP_FILE_TYPE' => $data['app_dokumen_pendukung'],
            'ACC_F_APPPEMBUKAAN_IP' => Helper::get_ip_address(),
            'ACC_F_APP_PERYT' => "Ya",
            'ACC_F_APPPEMBUKAAN_PERYT' => "Ya",
            'ACC_F_APP_DATE' => date("Y-m-d H:i:s"),
            'ACC_F_APPPEMBUKAAN_DATE' => date("Y-m-d H:i:s"),
        ], [
            'ID_ACC' => $progressAccount['ID_ACC']
        ]);

        Logger::client_log([
            'mbrid' => $user['MBR_ID'],
            'module' => "create-account",
            'ref' => $progressAccount['ID_ACC'],
            'message' => "Progress Real Account (Aplikasi Pembukaan Rekening)",
            'data'  => ($data)
        ]);

        exit(json_encode([
            'success'   => true,
            'redirect'  => "/account/create?page=pernyataan-pengungkapan-2",
            'alert'     => [
                'title' => "Berhasil",
                'text'  => "Aplikasi Pembukaan Rekening berhasil",
                'icon'  => "success"
            ]
        ]));  
    }

    private function aplikasiPembukaanRekening_DataPribadi($data, $user, $progressAccount) {
        $required = [
            'app_npwp'  => "Nomor NPWP",
            'app_gender' => "Jenis Kelamin",
            'app_nama_ibu' => "Nama Ibu Kandung",
            'app_status_perkawinan' => "Status Perkawinan",
            'app_status_rumah' => "Status Kepemilikan Rumah",
            'app_tujuan_pembukaan_rek' => "Tujuan Pembukaan Rekening",
            'app_pengalaman_investasi' => "Pengalaman Investasi",
        ];

        foreach($required as $key => $text) {
            if(empty($data[ $key ])) {
                if($key == 'app_npwp'){
                    if($this->unrequireNPWP($progressAccount["ACC_TYPE"])){
                        exit(json_encode([
                            'success' => false,
                            'alert' => [
                                'title' => "Gagal",
                                'text'  => "{$text} diperlukan",
                                'icon'  => "error"
                            ] 
                        ]));
                    }
                }else{
                    exit(json_encode([
                        'success' => false,
                        'alert' => [
                            'title' => "Gagal",
                            'text'  => "{$text} diperlukan",
                            'icon'  => "error"
                        ] 
                    ]));
                }
            }
        }

        if(strtolower($data['app_status_perkawinan']) != "tidak kawin") {
            if(empty($data['acc_app_nama_istri'])) {
                exit(json_encode([
                    'success' => false,
                    'alert' => [
                        'title' => "Gagal",
                        'text'  => "Nama Istri/Suami diperlukan",
                        'icon'  => "error"
                    ] 
                ]));
            }
        }

        $app_telepon_rumah = $data['app_telepon_rumah'] ?? 0;
        $app_faksimili_rumah = $data['app_faksimili_rumah'] ?? 0;
        $app_no_handphone = $data['app_no_handphone'] ?? 0;
        $acc_app_nama_istri = $data['acc_app_nama_istri'] ?? $progressAccount['ACC_F_APP_PRIBADI_NAMAISTRI'] ?? null;
        $bidang_investasi = null;
        
        /** Bidang Investasi */
        if(strtolower($data['app_pengalaman_investasi']) == "ya") {
            if(empty($data['bidang_investasi'])) {
                exit(json_encode([
                    'success' => false,
                    'alert' => [
                        'title' => "Gagal",
                        'text'  => "Bidang investasi diperlukan",
                        'icon'  => "error"
                    ] 
                ]));
            }

            $bidang_investasi = $data['bidang_investasi'];
        }

        /** Menyetujui tidak Memiliki Anggota keluarga bekerja di bappebti / bursa berjangka */
        if(empty($data['app_anggota_berjangka'])) {
            exit(json_encode([
                'success' => false,
                'alert' => [
                    'title' => "Gagal",
                    'text'  => "Anda tidak bisa mendaftar jika memiliki Anggota keluarga yang bekerja di BAPPEBTI / Bursa Berjangka",
                    'icon'  => "error"
                ] 
            ]));
        }

        /** Menyetujui tidak dinyatakan pailit oleh pengadilan */
        if(empty($data['app_pailit'])) {
            exit(json_encode([
                'success' => false,
                'alert' => [
                    'title' => "Gagal",
                    'text'  => "Anda tidak bisa mendaftar jika dinyatakan pailit oleh Pengadilan",
                    'icon'  => "error"
                ] 
            ]));
        }

        if(is_numeric($data['app_npwp']) === FALSE) {
            if($this->unrequireNPWP($progressAccount["ACC_TYPE"])){
                exit(json_encode([
                    'success' => false,
                    'alert' => [
                        'title' => "Gagal",
                        'text'  => "Nomor NPWP Tidak Valid",
                        'icon'  => "error"
                    ] 
                ]));
            }
        }

        if(strlen($data['app_npwp']) < 16) {
            if($this->unrequireNPWP($progressAccount["ACC_TYPE"])){
                exit(json_encode([
                    'success' => false,
                    'alert' => [
                        'title' => "Gagal",
                        'text'  => "Nomor NPWP harus 16 digit atau lebih",
                        'icon'  => "error"
                    ] 
                ]));
            }
        }

        /** Update Data Pribadi */
        
        
        $updateDataPribadi = Database::update("tb_racc", [
            'ACC_F_APP_PRIBADI_NPWP'    => $data['app_npwp'],
            'ACC_F_APP_PRIBADI_KELAMIN' => $data['app_gender'],
            'ACC_F_APP_PRIBADI_IBU'     => $data['app_nama_ibu'],
            'ACC_F_APP_PRIBADI_STSKAWIN'=> $data['app_status_perkawinan'],
            'ACC_F_APP_PRIBADI_NAMAISTRI' => $acc_app_nama_istri,
            'ACC_F_APP_PRIBADI_TLP'     => $data['app_telepon_rumah'],
            'ACC_F_APP_PRIBADI_FAX'     => $data['app_faksimili_rumah'],
            'ACC_F_APP_PRIBADI_HP'      => $data['app_no_handphone'],
            'ACC_F_APP_PRIBADI_STSRMH'  => $data['app_status_rumah'],
            'ACC_F_APP_TUJUANBUKA'      => $data['app_tujuan_pembukaan_rek'],
            'ACC_F_APP_PENGINVT'        => $data['app_pengalaman_investasi'],
            'ACC_F_APP_PENGINVT_BIDANG' => $bidang_investasi,
            'ACC_F_APP_PAILIT'          => $data['app_pailit'],
        ], [
            'ID_ACC' => $progressAccount['ID_ACC']
        ]);

        if($updateDataPribadi !== TRUE) {
            exit(json_encode([
                'success' => false,
                'alert' => [
                    'title' => "Gagal",
                    'text'  => $updateDataPribadi ?? "Gagal memperbarui data pribadi",
                    'icon'  => "error"
                ] 
            ]));
        }

        return true;
    }
    
    private function aplikasiPembukaanRekening_PihakDarurat($data, $user, $progressAccount) {
        $required = [
            'app_darurat_nama'  => "Nama Lengkap Pihak Darurat",
            'app_darurat_alamat' => "Alamat Pihak Darurat",
            'app_darurat_kodepos' => "Kode Pos Pihak Darurat",
            'app_darurat_telepon' => "No. Telepon Pihak Darurat",
            'app_darurat_hubungan' => "Status Hubungan dengan Pihak Darurat",
        ];

        foreach($required as $key => $text) {
            if(empty($data[ $key ])) {
                exit(json_encode([
                    'success' => false,
                    'alert' => [
                        'title' => "Gagal",
                        'text'  => "{$text} diperlukan",
                        'icon'  => "error"
                    ] 
                ]));
            }
        }

        if(is_numeric($data['app_darurat_kodepos']) === FALSE) {
            exit(json_encode([
                'success' => false,
                'alert' => [
                    'title' => "Gagal",
                    'text'  => "Kode Pos tidak valid",
                    'icon'  => "error"
                ] 
            ]));
        }

        if(is_numeric($data['app_darurat_telepon']) === FALSE) {
            exit(json_encode([
                'success' => false,
                'alert' => [
                    'title' => "Gagal",
                    'text'  => "No, Telepon Pihak Darurat tidak valid",
                    'icon'  => "error"
                ] 
            ]));
        }

        
        
        $updatePihakDarurat = Database::update("tb_racc", [
            'ACC_F_APP_DRRT_NAMA'   => $data['app_darurat_nama'],
            'ACC_F_APP_DRRT_ALAMAT' => $data['app_darurat_alamat'],
            'ACC_F_APP_DRRT_ZIP'    => $data['app_darurat_kodepos'],
            'ACC_F_APP_DRRT_TLP'    => $data['app_darurat_telepon'],
            'ACC_F_APP_DRRT_HUB'    => $data['app_darurat_hubungan']
        ], [
            'ID_ACC' => $progressAccount['ID_ACC']
        ]);

        if($updatePihakDarurat !== TRUE) {
            exit(json_encode([
                'success' => false,
                'alert' => [
                    'title' => "Gagal",
                    'text'  => $updatePihakDarurat ?? "Gagal memperbarui Data Pihak Darurat Yang Dapat Dihubungi",
                    'icon'  => "error"
                ] 
            ]));
        }

        return true;
    }

    private function aplikasiPembukaanRekening_Pekerjaan($data, $user, $progressAccount) {
        $required = [
            'app_pekerjaan'  => "Pekerjaan",
            'app_nama_perusahaan' => "Nama Perusahaan",
            'app_bidang_usaha' => "Bidang Usaha",
            'app_jabatan_pekerjaan' => "Jabatan",
            'app_lama_bekerja' => "Lama Bekerja",
            'app_lama_bekerja_sebelumnya' => "Lama Bekerja (Kantor Sebelumnya)",
            'app_alamat_kantor' => "Alamat Kantor",
        ];

        foreach($required as $key => $text) {
            if(empty($data[ $key ])) {
                exit(json_encode([
                    'success' => false,
                    'alert' => [
                        'title' => "Gagal",
                        'text'  => "{$text} diperlukan",
                        'icon'  => "error"
                    ] 
                ]));
            }
        }

        $app_kodepos_kantor = $data['app_kodepos_kantor'] ?? null;
        $app_nomor_kantor   = $data['app_nomor_kantor'] ?? 0;
        $app_nomor_fax_kantor   = $data['app_nomor_fax_kantor'] ?? 0;

        /** Kode Pos pekerjaan */
        // if(is_numeric($app_kodepos_kantor) === FALSE) {
        //     exit(json_encode([
        //         'success' => false,
        //         'alert' => [
        //             'title' => "Gagal",
        //             'text'  => "Kode Pos Kantor tidak valid",
        //             'icon'  => "error"
        //         ] 
        //     ]));
        // } 

        // if(strlen($app_kodepos_kantor) != 5) {
        //     exit(json_encode([
        //         'success' => false,
        //         'alert' => [
        //             'title' => "Gagal",
        //             'text'  => "KodePos harus berisi 5 digit",
        //             'icon'  => "error"
        //         ] 
        //     ]));
        // } 
        
        /** Nomor telepon kantor */
        // if(is_numeric($app_nomor_kantor) === FALSE) {
        //     exit(json_encode([
        //         'success' => false,
        //         'alert' => [
        //             'title' => "Gagal",
        //             'text'  => "No. Telepon Kantor tidak valid",
        //             'icon'  => "error"
        //         ] 
        //     ]));
        // }

        /** Nomor faksimili kantor */
        // if(is_numeric($app_nomor_fax_kantor) === FALSE) {
        //     exit(json_encode([
        //         'success' => false,
        //         'alert' => [
        //             'title' => "Gagal",
        //             'text'  => "No. Faksimili Kantor tidak valid",
        //             'icon'  => "error"
        //         ] 
        //     ]));
        // }

        
        
        $updatePekerjaan = Database::update("tb_racc", [
            'ACC_F_APP_KRJ_TYPE' => $data['app_pekerjaan'],
            'ACC_F_APP_KRJ_NAMA' => $data['app_nama_perusahaan'],
            'ACC_F_APP_KRJ_BDNG' => $data['app_bidang_usaha'],
            'ACC_F_APP_KRJ_JBTN' => $data['app_jabatan_pekerjaan'],
            'ACC_F_APP_KRJ_LAMA' => $data['app_lama_bekerja'],
            'ACC_F_APP_KRJ_LAMASBLM' => $data['app_lama_bekerja_sebelumnya'],
            'ACC_F_APP_KRJ_ALAMAT' => $data['app_alamat_kantor'],
            'ACC_F_APP_KRJ_ZIP' => $app_kodepos_kantor,
            'ACC_F_APP_KRJ_TLP' => $app_nomor_kantor,
            'ACC_F_APP_KRJ_FAX' => $app_nomor_fax_kantor
        ], [
            'ID_ACC' => $progressAccount['ID_ACC']
        ]);

        if($updatePekerjaan !== TRUE) {
            exit(json_encode([
                'success' => false,
                'alert' => [
                    'title' => "Gagal",
                    'text'  => $updatePekerjaan ?? "Gagal memperbarui data pekerjaan",
                    'icon'  => "error"
                ] 
            ]));
        }

        return true;
    }

    private function aplikasiPembukaanRekening_DaftarKekayaan($data, $user, $progressAccount) {
        $required = [
            'app_penghasilan'   => "Jumlah Penghasilan",
            'app_lokasi_rumah'  => "Lokasi Rumah",
        ];

        foreach($required as $key => $text) {
            if(empty($data[ $key ])) {
                exit(json_encode([
                    'success' => false,
                    'alert' => [
                        'title' => "Gagal",
                        'text'  => "{$text} diperlukan",
                        'icon'  => "error"
                    ] 
                ]));
            }
        }

        $app_nilai_njop = $data['app_nilai_njop'] ?? 0;
        $app_deposit_bank = $data['app_deposit_bank'] ?? 0;
        $app_kekayaan_lainnya = $data['app_kekayaan_lainnya'] ?? 0;

        if(is_numeric($app_nilai_njop) === FALSE) {
            exit(json_encode([
                'success' => false,
                'alert' => [
                    'title' => "Gagal",
                    'text'  => "Jumlah NJOP tidak valid",
                    'icon'  => "error"
                ] 
            ]));
        }
        
        if(is_numeric($app_deposit_bank) === FALSE) {
            exit(json_encode([
                'success' => false,
                'alert' => [
                    'title' => "Gagal",
                    'text'  => "Jumlah Deposit Bank tidak valid",
                    'icon'  => "error"
                ] 
            ]));
        }

        if(is_numeric($app_kekayaan_lainnya) === FALSE) {
            exit(json_encode([
                'success' => false,
                'alert' => [
                    'title' => "Gagal",
                    'text'  => "Jumlah Kekayaan Lainnya tidak valid",
                    'icon'  => "error"
                ] 
            ]));
        }
        

        
         
        $updateKekayaan = Database::update("tb_racc", [
            'ACC_F_APP_KEKYAN'  => $data['app_penghasilan'],
            'ACC_F_APP_KEKYAN_RMHLKS' => $data['app_lokasi_rumah'],
            'ACC_F_APP_KEKYAN_NJOP' => $app_nilai_njop,
            'ACC_F_APP_KEKYAN_DPST' => $app_deposit_bank,
            'ACC_F_APP_KEKYAN_LAIN' => $app_kekayaan_lainnya,
            'ACC_F_APP_KEKYAN_NILAI' => ($app_nilai_njop + $app_deposit_bank)
        ], [
            'ID_ACC' => $progressAccount['ID_ACC']
        ]);

        if($updateKekayaan !== TRUE) {
            exit(json_encode([
                'success' => false,
                'alert' => [
                    'title' => "Gagal",
                    'text'  => $updateKekayaan ?? "Gagal memperbarui Daftar kekayaan",
                    'icon'  => "error"
                ] 
            ]));
        }

        return true;
    }

    private function aplikasiPembukaanRekening_DokumenPendukung($data, $user, $progressAccount) {
        $verihub = VerihubFactory::init();

        /** Upload Dokumen Pendukung */
        if(empty($_FILES['app_pendukung']) || $_FILES['app_pendukung']['error'] != 0) {
            if(empty($progressAccount['ACC_F_APP_FILE_IMG'])) {
                exit(json_encode([
                    'success' => false,
                    'alert' => [
                        'title' => "Gagal",
                        'text'  => "Mohon upload dokumen pendukung",
                        'icon'  => "error"
                    ] 
                ]));
            }
        
        }else {
            $uploadDokumenPendukung = FileUpload::upload_myfile($_FILES['app_pendukung'], "regol");
            if(!is_array($uploadDokumenPendukung) || !array_key_exists("filename", $uploadDokumenPendukung)) {
                exit(json_encode([
                    'success' => false,
                    'alert' => [
                        'title' => "Gagal",
                        'text'  => $uploadDokumenPendukung ?? "Gagal mengunggah file dokumen pendukung",
                        'icon'  => "error"
                    ] 
                ]));
            }
    
            $updateImage = Database::update("tb_racc", ['ACC_F_APP_FILE_IMG' => $uploadDokumenPendukung['filename']], ['ID_ACC' => $progressAccount['ID_ACC']]);
            if($updateImage !== TRUE) {
                exit(json_encode([
                    'success' => false,
                    'alert' => [
                        'title' => "Gagal",
                        'text'  => $updateImage ?? "Gagal memperbarui dokumen pendukung, mohon coba lagi",
                        'icon'  => "error"
                    ] 
                ]));
            }
        }

        /** Upload Dokumen Pendukung Lainnya */
        if(empty($_FILES['app_pendukung_lainnya']) || $_FILES['app_pendukung_lainnya']['error'] != 0) {
            if(empty($progressAccount['ACC_F_APP_FILE_IMG2'])) {
                exit(json_encode([
                    'success' => false,
                    'alert' => [
                        'title' => "Gagal",
                        'text'  => "Mohon upload dokumen pendukung lainnya",
                        'icon'  => "error"
                    ] 
                ]));
            }
        
        }else {
            $uploadDokumenPendukung2 = FileUpload::upload_myfile($_FILES['app_pendukung_lainnya'], "regol");
            if(!is_array($uploadDokumenPendukung2) || !array_key_exists("filename", $uploadDokumenPendukung2)) {
                exit(json_encode([
                    'success' => false,
                    'alert' => [
                        'title' => "Gagal",
                        'text'  => $uploadDokumenPendukung2 ?? "Gagal mengunggah file dokumen pendukung lainnya",
                        'icon'  => "error"
                    ] 
                ]));
            }

            $updateImage = Database::update("tb_racc", ['ACC_F_APP_FILE_IMG2' => $uploadDokumenPendukung2['filename']], ['ID_ACC' => $progressAccount['ID_ACC']]);
            if($updateImage !== TRUE) {
                exit(json_encode([
                    'success' => false,
                    'alert' => [
                        'title' => "Gagal",
                        'text'  => $updateImage ?? "Gagal memperbarui dokumen pendukung lainnya, mohon coba lagi",
                        'icon'  => "error"
                    ] 
                ]));
            }
        }
      
        return true;
    }

    public function formulirDokumenResiko($data, $user) {
        $this->checkCsrfToken($data);
        $progressAccount = $this->checkProgressAccount(md5(md5($user['MBR_ID'])));  
    
        /** Check Status */
        $this->isAllowToEdit(status: $progressAccount['ACC_STS']);

        /** Check Peresetujuan */
        $agree = $data['aggree'] ?? "Tidak";
        if(strtolower($agree) != "ya") {
            exit(json_encode([
                'success'   => false,
                'alert'     => [
                    'title' => "Gagal",
                    'text'  => "Anda harus menyetujui untuk melanjutkan ke tahap berikutnya",
                    'icon'  => "error"
                ]
            ]));  
        }

        /** Validasi jumlah checkbox */
        if(count($_POST['box']) < 13) {
            exit(json_encode([
                'success'   => false,
                'alert'     => [
                    'title' => "Gagal",
                    'text'  => "Mohon menyetujui semua persyaratan yang tertera",
                    'icon'  => "error"
                ]
            ]));  
        }

        
        
        $update = Database::update("tb_racc", [
            'ACC_F_RESK' => 1,
            'ACC_F_RESK_PERYT' => "Ya" ,
            'ACC_F_RESK_IP' => Helper::get_ip_address(),
            'ACC_F_RESK_DATE' => date("Y-m-d H:i:s")
        ], [
            'ID_ACC' => $progressAccount['ID_ACC']
        ]);

        if($update !== TRUE) {
            exit(json_encode([
                'success'   => false,
                'alert'     => [
                    'title' => "Gagal",
                    'text'  => $update ?? "Gagal memperbarui progress account",
                    'icon'  => "error"
                ]
            ])); 
        }

        Logger::client_log([
            'mbrid' => $user['MBR_ID'],
            'module' => "create-account",
            'ref' => $progressAccount['ID_ACC'],
            'message' => "Progress Real Account (Formulir Dokumen Resiko)",
            'data'  => ($data)
        ]);

        exit(json_encode([
            'success'   => true,
            'redirect'  => "/account/create?page=pernyataan-pengungkapan-3",
            'alert'     => [
                'title' => "Berhasil",
                'text'  => "Berhasil Disimpan",
                'icon'  => "success"
            ]
        ])); 
    }

    public function perjanjianPemberianAmanat($data, $user) {
        $this->checkCsrfToken($data);
        $progressAccount = $this->checkProgressAccount(md5(md5($user['MBR_ID'])));  
    
        /** Check Status */
        $this->isAllowToEdit(status: $progressAccount['ACC_STS']);

        /** Check Peresetujuan */
        $agree = $data['aggree'] ?? "Tidak";
        if(strtolower($agree) != "ya") {
            exit(json_encode([
                'success'   => false,
                'alert'     => [
                    'title' => "Gagal",
                    'text'  => "Anda harus menyetujui untuk melanjutkan ke tahap berikutnya",
                    'icon'  => "error"
                ]
            ]));  
        }

        /** Validasi jumlah checkbox */
        if(count($_POST['box']) < 24) {
            exit(json_encode([
                'success'   => false,
                'alert'     => [
                    'title' => "Gagal",
                    'text'  => "Mohon menyetujui semua persyaratan yang tertera",
                    'icon'  => "error"
                ]
            ]));  
        }

        if(empty($data['step07_kotapenyelesaian']) || !in_array($data['step07_kotapenyelesaian'], ["BAKTI", "Pengadilan Negeri Jakarta Selatan"])) {
            exit(json_encode([
                'success'   => false,
                'alert'     => [
                    'title' => "Gagal",
                    'text'  => "Mohon Pilih tempat untuk Penyelesaian Perselisihan",
                    'icon'  => "error"
                ]
            ])); 
        }

        $office = ProfilePerusahaan::office();
        $listOffice = array_values(array_column($office, "OFC_CITY"));
        $listOffice = array_map(fn($ar): string => strtoupper($ar), $listOffice);
        $wpbVerifikator = ProfilePerusahaan::wpb_verifikator();

        if(empty($data['step07_kantorpenyelesaian'])) {
            exit(json_encode([
                'success'   => false,
                'alert'     => [
                    'title' => "Gagal",
                    'text'  => "Mohon Pilih kantor terdekat untuk Penyelesaian Perselisihan",
                    'icon'  => "error"
                ]
            ])); 
        }

        if(!in_array(strtoupper($data['step07_kantorpenyelesaian']), $listOffice)) {
            exit(json_encode([
                'success'   => false,
                'alert'     => [
                    'title' => "Gagal",
                    'text'  => "Kantor yang dipilih tidak valid/tersedia",
                    'icon'  => "error"
                ]
            ])); 
        }

        if(empty($wpbVerifikator)) {
            exit(json_encode([
                'success'   => false,
                'alert'     => [
                    'title' => "Gagal",
                    'text'  => "Belum ada WPB Verifikator yang ditunjuk",
                    'icon'  => "error"
                ]
            ])); 
        }

        
        
        $update = Database::update("tb_racc", [
            'ACC_F_PERJ' => 1,
            'ACC_F_PERJ_IP' => Helper::get_ip_address(),
            'ACC_F_PERJ_PERYT' => "Ya",
            'ACC_F_PERJ_DATE' => date("Y-m-d H:i:s"),
            'ACC_F_PERJ_WPB' => $wpbVerifikator['WPB_NAMA'],
            'ACC_F_PERJ_KANTOR' => $data['step07_kantorpenyelesaian'],
            'ACC_F_PERJ_PERSLISIHAN' => $data['step07_kotapenyelesaian']
        ], [
            'ID_ACC' => $progressAccount['ID_ACC']
        ]);

        if($update !== TRUE) {
            exit(json_encode([
                'success'   => false,
                'alert'     => [
                    'title' => "Gagal",
                    'text'  => $update ?? "Gagal memperbarui progress account",
                    'icon'  => "error"
                ]
            ])); 
        }

        Logger::client_log([
            'mbrid' => $user['MBR_ID'],
            'module' => "create-account",
            'ref' => $progressAccount['ID_ACC'],
            'message' => "Progress Real Account (Formulir Perjanjian Pemberian Amanat)",
            'data'  => json_encode($data)
        ]);

        exit(json_encode([
            'success'   => true,
            'redirect'  => "/account/create?page=peraturan-perdagangan",
            'alert'     => [
                'title' => "Berhasil",
                'text'  => "Berhasil disimpan",
                'icon'  => "success"
            ]
        ])); 
    }

    public function peraturanPerdagangan($data, $user) {
        $this->checkCsrfToken($data);
        $progressAccount = $this->checkProgressAccount(md5(md5($user['MBR_ID'])));  
    
        /** Check Status */
        $this->isAllowToEdit(status: $progressAccount['ACC_STS']);

        /** Check Peresetujuan */
        $agree = $data['aggree'] ?? "Tidak";
        if(strtolower($agree) != "ya") {
            exit(json_encode([
                'success'   => false,
                'alert'     => [
                    'title' => "Gagal",
                    'text'  => "Anda harus menyetujui untuk melanjutkan ke tahap berikutnya",
                    'icon'  => "error"
                ]
            ]));  
        }

        
        
        $update = Database::update("tb_racc", [
            'ACC_F_TRDNGRULE' => 1,
            'ACC_F_TRDNGRULE_IP' => Helper::get_ip_address(),
            'ACC_F_TRDNGRULE_PERYT' => "Ya",
            'ACC_F_TRDNGRULE_DATE' => date("Y-m-d H:i:s")
        ], [
            'ID_ACC' => $progressAccount['ID_ACC']
        ]);

        if($update !== TRUE) {
            exit(json_encode([
                'success'   => false,
                'alert'     => [
                    'title' => "Gagal",
                    'text'  => $update ?? "Gagal memperbarui progress account",
                    'icon'  => "error"
                ]
            ])); 
        }

        Logger::client_log([
            'mbrid' => $user['MBR_ID'],
            'module' => "create-account",
            'ref' => $progressAccount['ID_ACC'],
            'message' => "Progress Real Account (Peraturan Perdagangan / Trading Rules)",
            'data'  => ($data)
        ]);

        exit(json_encode([
            'success'   => true,
            'redirect'  => "/account/create?page=pernyataan-bertanggung-jawab",
            'alert'     => [
                'title' => "Berhasil",
                'text'  => "Berhasil disimpan",
                'icon'  => "success"
            ]
        ])); 
    }

    public function pernyataanBertanggungJawab($data, $user) {
        $this->checkCsrfToken($data);
        $progressAccount = $this->checkProgressAccount(md5(md5($user['MBR_ID'])));  
    
        /** Check Status */
        $this->isAllowToEdit(status: $progressAccount['ACC_STS']);

        /** Check Peresetujuan */
        $agree = $data['aggree'] ?? "Tidak";
        if(strtolower($agree) != "ya") {
            exit(json_encode([
                'success'   => false,
                'alert'     => [
                    'title' => "Gagal",
                    'text'  => "Anda harus menyetujui untuk melanjutkan ke tahap berikutnya",
                    'icon'  => "error"
                ]
            ]));  
        }

        
        
        $update = Database::update("tb_racc", [
            'ACC_F_KODE' => 1,
            'ACC_F_KODE_IP' => Helper::get_ip_address(),
            'ACC_F_KODE_PERYT' => "Ya",
            'ACC_F_KODE_DATE' => date("Y-m-d H:i:s"),
        ], [
            'ID_ACC' => $progressAccount['ID_ACC']
        ]);

        if($update !== TRUE) {
            exit(json_encode([
                'success'   => false,
                'alert'     => [
                    'title' => "Gagal",
                    'text'  => $update ?? "Gagal memperbarui progress account",
                    'icon'  => "error"
                ]
            ])); 
        }

        Logger::client_log([
            'mbrid' => $user['MBR_ID'],
            'module' => "create-account",
            'ref' => $progressAccount['ID_ACC'],
            'message' => "Progress Real Account (Pernyataan Bertanggung Jawab)",
            'data'  => ($data)
        ]);

        exit(json_encode([
            'success'   => true,
            'redirect'  => "/account/create?page=pernyataan-dana-nasabah",
            'alert'     => [
                'title' => "Berhasil",
                'text'  => "Berhasil disimpan",
                'icon'  => "success"
            ]
        ])); 
    }

    public function pernyataanDanaNasabah($data, $user) {
        $this->checkCsrfToken($data);
        $progressAccount = $this->checkProgressAccount(md5(md5($user['MBR_ID'])));  
    
        /** Check Status */
        $this->isAllowToEdit(status: $progressAccount['ACC_STS']);

        /** Check Peresetujuan */
        $agree = $data['aggree'] ?? "Tidak";
        if(strtolower($agree) != "ya") {
            exit(json_encode([
                'success'   => false,
                'alert'     => [
                    'title' => "Gagal",
                    'text'  => "Anda harus menyetujui untuk melanjutkan ke tahap berikutnya",
                    'icon'  => "error"
                ]
            ]));  
        }

        
        
        $update = Database::update("tb_racc", [
            'ACC_F_DANA' => 1,
            'ACC_F_DANA_IP' => Helper::get_ip_address(), 
            'ACC_F_DANA_PERYT' => "Ya",
            'ACC_F_DANA_DATE' => date("Y-m-d H:i:s")
        ], [
            'ID_ACC' => $progressAccount['ID_ACC']
        ]);

        if($update !== TRUE) {
            exit(json_encode([
                'success'   => false,
                'alert'     => [
                    'title' => "Gagal",
                    'text'  => $update ?? "Gagal memperbarui progress account",
                    'icon'  => "error"
                ]
            ])); 
        }

        Logger::client_log([
            'mbrid' => $user['MBR_ID'],
            'module' => "create-account",
            'ref' => $progressAccount['ID_ACC'],
            'message' => "Progress Real Account (Pernyataan Dana Nasabah)",
            'data'  => ($data)
        ]);

        exit(json_encode([
            'success'   => true,
            'redirect'  => "/account/create?page=pernyataan-pengungkapan-4",
            'alert'     => [
                'title' => "Berhasil",
                'text'  => "Berhasil disimpan",
                'icon'  => "success"
            ]
        ])); 
    }

    private function uploadSelfiePhoto($data, $user) {
        $verihub = VerihubFactory::init();
        $progressAccount = $this->checkProgressAccount(md5(md5($user['MBR_ID'])));

        /** Upload Dokumen Foto Terbaru */
        if(empty($_FILES['app_foto_terbaru']) || $_FILES['app_foto_terbaru']['error'] != 0) {
            if(!empty($progressAccount['ACC_F_APP_FILE_FOTO'])) {
                return false;
            }
            
            exit(json_encode([
                'success' => false,
                'alert' => [
                    'title' => "Gagal",
                    'text'  => "Mohon upload foto terbaru",
                    'icon'  => "error"
                ] 
            ]));
        }

        if($progressAccount['ACC_DOC_VERIF'] == -1) {
            exit(json_encode([
                'success' => false,
                'alert' => [
                    'title' => "Gagal",
                    'text'  => "Dokumen telah diverifikasi, tidak dapat dirubah",
                    'icon'  => "error"
                ] 
            ]));
        }

        /** validasi file sebelum di upload */
        $checkSelfie = $verihub->validate_photoSelfie($_FILES['app_foto_terbaru']);
        if(!is_array($checkSelfie)) {
            exit(json_encode([
                'success' => false,
                'alert' => [
                    'title' => "Gagal",
                    'text'  => "Terdapat kesalahan pada foto Selfie Anda",
                    'icon'  => "error"
                ] 
            ]));
        }

        if(empty($checkSelfie['image_scaling'])) {
            exit(json_encode([
                'success' => false,
                'alert' => [
                    'title' => "Gagal",
                    'text'  => "Invalid Data",
                    'icon'  => "error"
                ] 
            ]));
        }

        $newFileName    = "regol_selfie_".time().rand(1000000, 9999999).".jpeg";
        $target_dir     = $_SERVER['DOCUMENT_ROOT'] . "/assets/uploads/{$newFileName}";
        $upload_local   = file_put_contents($target_dir, $checkSelfie['image_scaling']);
        if(!$upload_local) {
            exit(json_encode([
                'success' => false,
                'alert' => [
                    'title' => "Gagal",
                    'text'  => "Gagal saat mengunggah foto",
                    'icon'  => "error"
                ] 
            ]));
        }


        $credential = FileUpload::credential();
        $s3 = new Aws\S3\S3Client([
            'region'  => $credential['region'],
            'version' => 'latest',
            'credentials' => [
                'key'    => $credential['key'],
                'secret' => $credential['secretKey'],
            ]
        ]);

        try {
            /** Upload to AWS */
            $result = $s3->putObject([
                'Bucket' => $credential['bucketName'],
                'Key'    => $credential['folder'] ."/".$newFileName,
                'Body'   => fopen($target_dir, 'r'),
                'ACL'    => 'public-read', // make file 'public'
            ]);

            /** Delete file from local disk */
            unlink($target_dir);

            
        } catch (Aws\S3\Exception\S3Exception $e) {
            exit(json_encode([
                'success' => false,
                'alert' => [
                    'title' => "Gagal",
                    'text'  => "Gagal mengunggah foto Selfie (400)",
                    'icon'  => "error"
                ] 
            ]));
        }

        // /** Detect FaceLiveness */
        // if(strtoupper($progressAccount['RTYPE_TYPE']) == "MICRO") {
        //     $base64Image = file_get_contents($aws_folder . $newFileName);
        //     $base64Image = ("data:image/jpeg;base64,".base64_encode($base64Image));
        //     $validSelfie = $verihub->detectFaceLiveness(['mbrid' => $progressAccount['ACC_MBR'], 'image' => $base64Image]);
        //     if($validSelfie['success'] !== TRUE || $validSelfie['code'] != 200) {
        //         exit(json_encode([
        //             'success' => false,
        //             'alert' => [
        //                 'title' => "Gagal",
        //                 'text'  => $validSelfie['message'] ?? "(Foto Selfie) File yang diupload tidak valid",
        //                 'icon'  => "error"
        //             ] 
        //         ]));
        //     }
        // }
        
        $data = [
            'ACC_F_APP_FILE_FOTO' => $newFileName,
            'ACC_F_APP_FILE_FOTO_MIME' => $checkSelfie['type']
        ];

        $updateImage = Database::update("tb_racc", $data, ['ID_ACC' => $progressAccount['ID_ACC']]);
        if($updateImage !== TRUE) {
            exit(json_encode([
                'success' => false,
                'alert' => [
                    'title' => "Gagal",
                    'text'  => $updateImage ?? "Gagal memperbarui foto terbaru, mohon coba lagi",
                    'icon'  => "error"
                ] 
            ]));
        }

        // exit(json_encode([
        //     'success' => true,
        //     'alert' => [
        //         'title' => "Berhasil",
        //         'text'  => "Foto selfie berhasil disimpan",
        //         'icon'  => "success"
        //     ] 
        // ]));
    }

    private function uploadKtpPhoto($data, $user) {
        $verihub = VerihubFactory::init();
        $progressAccount = $this->checkProgressAccount(md5(md5($user['MBR_ID'])));
        
        /** Upload Dokumen Foto KTP */
        if(empty($_FILES['app_foto_identitas']) || $_FILES['app_foto_identitas']['error'] != 0) {
            if(!empty($progressAccount['ACC_F_APP_FILE_ID'])) {
                return false;
            }

            exit(json_encode([
                'success' => false,
                'alert' => [
                    'title' => "Gagal",
                    'text'  => "Mohon upload foto KTP",
                    'icon'  => "error"
                ] 
            ]));
        }

        if($progressAccount['ACC_DOC_VERIF'] == -1) {
            exit(json_encode([
                'success' => false,
                'alert' => [
                    'title' => "Gagal",
                    'text'  => "Dokumen telah diverifikasi, tidak dapat dirubah",
                    'icon'  => "error"
                ] 
            ]));

        }

        /** validasi file sebelum di upload */
        $validKtp = $verihub->validate_photoKtp($_FILES['app_foto_identitas']);
        if(!is_array($validKtp)) {
            exit(json_encode([
                'success' => false,
                'alert' => [
                    'title' => "Gagal",
                    'text'  => "Terdapat kesalahan pada foto KTP Anda",
                    'icon'  => "error"
                ] 
            ]));
        }

        if(empty($validKtp['image_scaling'])) {
            exit(json_encode([
                'success' => false,
                'alert' => [
                    'title' => "Gagal",
                    'text'  => "Invalid Data",
                    'icon'  => "error"
                ] 
            ]));
        }

        // $uploadFotoKtp = upload_myfile($validKtp, "regol_ktp");
        $newFileName    = "regol_ktp_".time().rand(1000000, 9999999).".jpeg";
        $target_dir     = $_SERVER['DOCUMENT_ROOT'] . "/assets/uploads/{$newFileName}";
        $upload_local   = file_put_contents($target_dir, $validKtp['image_scaling']);
        if(!$upload_local) {
            exit(json_encode([
                'success' => false,
                'alert' => [
                    'title' => "Gagal",
                    'text'  => "Gagal saat mengunggah foto",
                    'icon'  => "error"
                ] 
            ]));
        }


        $credential = FileUpload::credential();
        $s3 = new Aws\S3\S3Client([
            'region'  => $credential['region'],
            'version' => 'latest',
            'credentials' => [
                'key'    => $credential['key'],
                'secret' => $credential['secretKey'],
            ]
        ]);

        try {
            /** Upload to AWS */
            $result = $s3->putObject([
                'Bucket' => $credential['bucketName'],
                'Key'    => $credential['folder'] ."/".$newFileName,
                'Body'   => fopen($target_dir, 'r'),
                'ACL'    => 'public-read', // make file 'public'
            ]);

            /** Delete file from local disk */
            unlink($target_dir);

            
        } catch (Aws\S3\Exception\S3Exception $e) {
            exit(json_encode([
                'success' => false,
                'alert' => [
                    'title' => "Gagal",
                    'text'  => "Gagal mengunggah foto KTP (402)",
                    'icon'  => "error"
                ] 
            ]));
        }
        

        $data = [
            'ACC_F_APP_FILE_ID' => $newFileName,
            'ACC_F_APP_FILE_ID_MIME' => "image/jpeg"
        ];

        $updateImage = Database::update("tb_racc", $data, ['ID_ACC' => $progressAccount['ID_ACC']]);
        if($updateImage !== TRUE) {
            exit(json_encode([
                'success' => false,
                'alert' => [
                    'title' => "Gagal",
                    'text'  => $updateImage ?? "Gagal memperbarui foto KTP, mohon coba lagi",
                    'icon'  => "error"
                ] 
            ]));
        }

        // exit(json_encode([
        //     'success' => true,
        //     'alert' => [
        //         'title' => "Berhasil",
        //         'text'  => "Foto KTP berhasil disimpan",
        //         'icon'  => "success"
        //     ] 
        // ]));
    }

    public function verifikasiIdentitas($data, $user) {
        $this->checkCsrfToken($data);
        $progressAccount = $this->checkProgressAccount(md5(md5($user['MBR_ID'])));

        /** Check Status */
        $this->isAllowToEdit($progressAccount['ACC_STS']);
        
        /** Upload Selfie Photo */
        $this->uploadSelfiePhoto($data, $user);

        /** Upload KTP Photo */
        $this->uploadKtpPhoto($data, $user);
        
        /** Verifikasi ke Verihub (Jika belum pernah berhasil) */
        $statusVerifikasiVerihub = $progressAccount['ACC_DOC_VERIF'] ?? 0;
        if($statusVerifikasiVerihub == 0) {
            // $verif = $this->verifikasiVerihub($data, $user, $progressAccount);
            $verif = 1;
            $statusVerifikasiVerihub = -1;
            $data['reference_id'] = $verif;
        }

        /** Update Status Verifikasi (Jika berbeda dari sebelumnya) */
        if($statusVerifikasiVerihub != $progressAccount['ACC_DOC_VERIF']) {
            $update = Database::update("tb_racc", ['ACC_DOC_VERIF' => $statusVerifikasiVerihub], ['ID_ACC' => $progressAccount['ID_ACC']]);
            if($update !== TRUE) {
                exit(json_encode([
                    'success'   => false,
                    'alert'     => [
                        'title' => "Gagal",
                        'text'  => $update ?? "Gagal memperbarui progress account",
                        'icon'  => "error"
                    ]
                ])); 
            }
        }

        Logger::client_log([
            'mbrid' => $user['MBR_ID'],
            'module' => "create-account",
            'ref' => $progressAccount['ID_ACC'],
            'message' => "Progress Real Account (Verifikasi Identitas)",
            'data'  => ($data)
        ]);

        exit(json_encode([
            'success'   => true,
            'redirect'  => "/account/create?page=kelengkapan-formulir",
            'alert'     => [
                'title' => "Berhasil",
                'text'  => "Berhasil disimpan",
                'icon'  => "success"
            ]
        ])); 
    }

    private function verifikasiVerihub($data, $user, $progressAccount) {
        global $aws_folder;
        $verihub            = VerihubFactory::init();
        $uniqid             = uniqid();
        $reference_id       = md5($user['MBR_ID'] . $uniqid);
        $fileContentKTP     = file_get_contents($aws_folder . $progressAccount['ACC_F_APP_FILE_ID']);
        $fileContentSelfie  = file_get_contents($aws_folder . $progressAccount['ACC_F_APP_FILE_FOTO']);
        $sendVerification   = $verihub->send_idVerification([
            'mbrid' => $user['MBR_ID'],
            'nik'   => $progressAccount['ACC_NO_IDT'],
            'name'  => $progressAccount['ACC_FULLNAME'],
            'birth_date' => $progressAccount['ACC_TANGGAL_LAHIR'],
            'email' => $user['MBR_EMAIL'], 
            'phone' => "6285954536593", 
            'ktp_photo' => ("data:".$progressAccount['ACC_F_APP_FILE_ID_MIME'].";base64,".base64_encode($fileContentKTP)), 
            'selfie_photo' => ("data:".$progressAccount['ACC_F_APP_FILE_FOTO_MIME'].";base64,".base64_encode($fileContentSelfie)), 
            'reference_id' => $reference_id
        ]);

        if(!$sendVerification['success']) {
            exit(json_encode([
                'success'   => false,
                'alert'     => [
                    'title' => "Gagal",
                    'text'  => (ini_get("display_errors") == "1")
                        ? $sendVerification['message']
                        : "Verifikasi Gagal",
                    'icon'  => "error"
                ]
            ])); 
        }

        return $reference_id;
    }

    public function kelengkapanFormulir($data, $user) {
        $this->checkCsrfToken($data);
        $progressAccount = $this->checkProgressAccount(md5(md5($user['MBR_ID'])));  
    
        /** Check Status */
        $this->isAllowToEdit(status: $progressAccount['ACC_STS']);
        
        /** Check Peresetujuan */
        $agree = $data['aggree'] ?? "Tidak";
        if(strtolower($agree) != "ya") {
            exit(json_encode([
                'success'   => false,
                'alert'     => [
                    'title' => "Gagal",
                    'text'  => "Anda harus menyetujui untuk melanjutkan ke tahap berikutnya",
                    'icon'  => "error"
                ]
            ]));  
        }

        
        
        


        $dataUpdate = [
            'ACC_F_CMPLT' => 1,
            'ACC_F_CMPLT_IP' => Helper::get_ip_address(), 
            'ACC_F_CMPLT_PERYT' => "Ya",
            'ACC_F_CMPLT_DATE' => date("Y-m-d H:i:s"),
            'ACC_STS' => 1,
            'ACC_KODE' => uniqid()
        ];

        $update = Database::update("tb_racc", $dataUpdate, ['ID_ACC' => $progressAccount['ID_ACC']]);

        if($update !== TRUE) {
            exit(json_encode([
                'success'   => false,
                'alert'     => [
                    'title' => "Gagal",
                    'text'  => $update ?? "Gagal memperbarui progress account",
                    'icon'  => "error"
                ]
            ])); 
        }

        Logger::client_log([
            'mbrid' => $user['MBR_ID'],
            'module' => "create-account",
            'ref' => $progressAccount['ID_ACC'],
            'message' => "Progress Real Account (Kelengkapan Formulir)",
            'data'  => ($data)
        ]);

        exit(json_encode([
            'success'   => true,
            'redirect'  => "/account/create?page=selesai",
            'alert'     => [
                'title' => "Berhasil",
                'text'  => "Berhasil disimpan",
                'icon'  => "success"
            ]
        ])); 
    }

    public function depositNewAccount($data, $user) {
        // $this->checkCsrfToken($data);
        $progressAccount = $this->checkProgressAccount(md5(md5($user['MBR_ID'])));  
    
        /** Check Status */
        // $this->isAllowToEdit(status: $progressAccount['ACC_STS']);
        
        /** Check Status */
        if($progressAccount['ACC_STS'] != 1 || $progressAccount['ACC_WPCHECK'] != 1) {
            exit(json_encode([
                'success'   => false,
                'alert'     => [
                    'title' => "Gagal",
                    'text'  => "Status akun tidak valid",
                    'icon'  => "error"
                ]
            ]));  
        }


        /** Required form */
        $required = [
            'dpnewacc_bankusr' => "Bank Nasabah",
            'dpnewacc_bankcmpy' => "Bank Penerima",
            'dpnewacc_dpstval' => "Jumlah Deposit",
        ];

        foreach($required as $key => $text) {
            if(empty($data[ $key ])) {
                exit(json_encode([
                    'success'   => false,
                    'alert'     => [
                        'title' => "Gagal",
                        'text'  => "{$text} diperlukan",
                        'icon'  => "error"
                    ]
                ]));  
            }
        }

        /** Check File upload */
        if(empty($_FILES['dpnewacc_tfprove']) || $_FILES['dpnewacc_tfprove']['error'] != 0) {
            exit(json_encode([
                'success'   => false,
                'alert'     => [
                    'title' => "Gagal",
                    'text'  => "Mohon upload file bukti transfer deposit",
                    'icon'  => "error"
                ]
            ]));  
        }

        
        
        
        
        $amountSource = Helper::stringTonumber($data['dpnewacc_dpstval']);
        $amountFinal = 0;
        $currencyFrom = $progressAccount['RTYPE_CURR'];
        $currencyTo = "IDR";

        /** Check Bank nasabah */
        $userBank = User::myBank($user['MBR_ID'], $data['dpnewacc_bankusr']);
        if(empty($userBank)) {
            exit(json_encode([
                'success'   => false,
                'alert'     => [
                    'title' => "Gagal",
                    'text'  => "Data Bank nasabah tidak ditemukan",
                    'icon'  => "error"
                ]
            ]));  
        }

        /** Check bank admin */
        $adminBank = Admin::getAdminBank($data['dpnewacc_bankcmpy']);
        if(empty($adminBank)) {
            exit(json_encode([
                'success'   => false,
                'alert'     => [
                    'title' => "Gagal",
                    'text'  => "Data Bank admin tidak ditemukan",
                    'icon'  => "error"
                ]
            ]));
        }

        /** Check Amount */
        if(is_numeric($amountSource) === FALSE || $amountSource <= 0) {
            exit(json_encode([
                'success'   => false,
                'alert'     => [
                    'title' => "Gagal",
                    'text'  => "Jumlah deposit tidak valid",
                    'icon'  => "error"
                ]
            ]));  
        }

        /** Check Minimum */
        if($amountSource < $progressAccount['RTYPE_MINDEPOSIT']) {
            exit(json_encode([
                'success'   => false,
                'alert'     => [
                    'title' => "Gagal",
                    'text'  => "Minimum deposit " . implode(" ", [$progressAccount['RTYPE_CURR'], Helper::formatCurrency($progressAccount['RTYPE_MINDEPOSIT'])]),
                    'icon'  => "error"
                ]
            ]));  
        }


        /** Convertsation */
        $convert = Account::accountConvertation([
            'account_id' => $progressAccount['ID_ACC'],
            'amount' => $amountSource,
            'from' => $currencyFrom,
            'to' => $currencyTo
        ]);

        if(!is_array($convert)) {
            exit(json_encode([
                'success'   => false,
                'alert'     => [
                    'title' => "Gagal",
                    'text'  => $convert,
                    'icon'  => "error"
                ]
            ])); 
        }

        /** Set Amount Final */
        $amountFinal = ($amountSource * $convert['rate']);
        
        /** Upload File */
        $uploadFile = FileUpload::upload_myfile($_FILES['dpnewacc_tfprove'], "deposit_new_account");
        if(!is_array($uploadFile) || !array_key_exists("filename", $uploadFile)) {
            exit(json_encode([
                'success'   => false,
                'alert'     => [
                    'title' => "Gagal",
                    'text'  => $uploadFile ?? "Gagal upload file",
                    'icon'  => "error"
                ]
            ])); 
        }

        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        mysqli_begin_transaction($this->db);

        /** Insert DPWD */
        $insert = Database::insert("tb_dpwd", [
            'DPWD_MBR' => $user['MBR_ID'],
            'DPWD_TYPE' => 3,
            'DPWD_RACC' => $progressAccount['ID_ACC'],
            'DPWD_BANKSRC' => implode("/", [$userBank['MBANK_NAME'], $userBank['MBANK_ACCOUNT'], $userBank['MBANK_HOLDER']]),
            'DPWD_BANK' => implode("/", [$adminBank['BKADM_NAME'], $adminBank['BKADM_ACCOUNT'], $adminBank['BKADM_HOLDER']]),
            'DPWD_AMOUNT' => $amountFinal,
            'DPWD_AMOUNT_SOURCE' => $amountSource,
            'DPWD_CURR_FROM' => $currencyFrom,
            'DPWD_CURR_TO' => $currencyTo,
            'DPWD_RATE' => $convert['rate'],
            'DPWD_PIC' => $uploadFile['filename'],
            'DPWD_NOTE' => "Deposit New Account",
            'DPWD_IP' => Helper::get_ip_address(),
            'DPWD_DATETIME' => date("Y-m-d H:i:s")
        ]);

        if(!$insert) {
            $this->db->rollback();
            exit(json_encode([
                'success'   => false,
                'alert'     => [
                    'title' => "Gagal",
                    'text'  => "Failed to create transaction",
                    'icon'  => "error"
                ]
            ])); 
        }

        /** Update tb_racc */
        $update = Database::update("tb_racc", ['ACC_WPCHECK' => 2], ['ID_ACC' => $progressAccount['ID_ACC']]);
        if(!$update) {
            $this->db->rollback();
            exit(json_encode([
                'success'   => false,
                'alert'     => [
                    'title' => "Gagal",
                    'text'  => $update ?? "Failed to update account status",
                    'icon'  => "error"
                ]
            ])); 
        }

        $data['filename'] = $uploadFile['filename'];
        Logger::client_log([
            'mbrid' => $user['MBR_ID'],
            'module' => "create-account",
            'ref' => $progressAccount['ID_ACC'],
            'ip' => Helper::get_ip_address(),
            'message' => "Progress Real Account (Deposit New Account)",
            'data'  => json_encode($data)
        ]);


        $this->db->commit();
        exit(json_encode([
            'success'   => true,
            'redirect'  => "/account/create?page=deposit-new-account",
            'alert'     => [
                'title' => "Berhasil",
                'text'  => "Deposit new account sedang diprosess",
                'icon'  => "success"
            ]
        ])); 
    }
}

try {
    $db         = Database::connect();
    $appPost    = new AppPost();
    $method     = $_POST['method'] ?? $_GET['method'] ?? "error"; 
    
    /** Check Session & Cookie */
    $user = User::user();
    if(!$user) {
        exit(json_encode([
            'success'   => false,
            'alert'     => [
                'title' => "Gagal",
                'text'  => "Invalid Credential, Please re-login",
                'icon'  => "error"
            ]
        ]));
    }
    
    if(empty($user) || empty($user['MBR_ID'])) {
        exit(json_encode([
            'success'   => false,
            'alert'     => [
                'title' => "Gagal",
                'text'  => "Invalid Session",
                'icon'  => "error"
            ]
        ]));
    }

    if(empty($method) || !method_exists($appPost, $method)) {
        exit(json_encode([
            'success'   => false,
            'alert'     => [
                'title' => "Gagal",
                'text'  => "Invalid Method",
                'icon'  => "error"
            ]
        ]));
    }

    // call_user_func_array([$appPost, $method], $postData);
    $user['userid'] = md5(md5($user['MBR_ID']));
    call_user_func_array([$appPost, $method], [$_POST, $user]);

} catch(Throwable $th) {
    exit(json_encode([
        'success'   => false,
        'error'     => $th->getMessage()
    ]));
}