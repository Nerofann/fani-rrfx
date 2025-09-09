<?php
if(!class_exists('Aws\S3\S3Client')) {
    require_once CONFIG_ROOT . '/vendor/autoload.php';
}

use App\Factory\MetatraderFactory;
use App\Factory\VerihubFactory;
use App\Models\Account;
use App\Models\FileUpload;
use App\Models\Helper;
use App\Models\ProfilePerusahaan;
use Config\Core\Database;
use Config\Core\EmailSender;

class AppRegol {
    private $db;

    public function __construct($mysql)
    {
        //Do your magic here
        $this->db = $mysql;
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
                'status'    => false,
                'message'   => "Invalid CSRF_TOKEN",
                'response'  => []
            ]));
        }

        // if(!isValidCSRFToken($data['csrf_token'])) {
        //     exit(json_encode([
        //         'status'    => false,
        //         'message'   => "CSRF_TOKEN Expired",
        //         'response'  => []
        //     ]));
        // }

        return true;
    }

    private function checkProgressAccount(string $userid) {
        $progressAccount = Account::getProgressRealAccount($userid);
        if(empty($progressAccount)) {
            exit(json_encode([
                'status'   => false,
                'message'  => "Anda belum mulai membuat akun",
                'response' => []
            ]));  
        }

        return $progressAccount;
    }

    private function isAllowToEdit(int $status) {
        if(in_array($status, [-1])) {
            exit(json_encode([
                'status'    => false,
                'message'   => "Akun sedang dalam prosess verifikasi",
                'response'  => []
            ]));
        }

        return true;
    }

    public function unrequireNPWP($type_acc = 0){
        $progressAccount = $type_acc;
        $SQL_ACC_TYPE  = mysqli_query($this->db, 'SELECT tb_racctype.RTYPE_TYPE FROM tb_racctype WHERE tb_racctype.ID_RTYPE = '.$progressAccount.' LIMIT 1');
        $rlst_acc_type = ($SQL_ACC_TYPE && mysqli_num_rows($SQL_ACC_TYPE) > 0) ? mysqli_fetch_assoc($SQL_ACC_TYPE)["RTYPE_TYPE"] : 0;
        return (strtolower($rlst_acc_type) == strtolower('micro') || strtolower($rlst_acc_type) == strtolower('mikro')) ? false : true;
    }

    public function product($data, $user) {
        $availableProduct = Account::getAvailableProduct($user['userid']);
        if(empty($availableProduct)) {
            exit(json_encode([
                'status'    => false,
                'message'   => "No products are available",
                'response'  => []
            ]));
        }

        $products = [];
        foreach($availableProduct as $product) {
            $productType = $product['type'];
            $list = [];
            foreach($product['products'] as $p) {
                $list[] = [
                    'suffix'=> $p['RTYPE_SUFFIX'],
                    'name'  => $p['RTYPE_NAME'],
                    'rate'  => ($p['RTYPE_ISFLOATING'] == 1)? "Floating" : $p['RTYPE_RATE'],
                    'currency'  => $p['RTYPE_CURR']
                ];
            }

            $products[] = [
                'type'  => $productType,
                'products' => $list
            ];
        }

        exit(json_encode([
            'status'    => true,
            'message'   => "Products found",
            'response'  => $products
        ]));
    }

    public function progressAccount($data, $user) {
        $progressAccount = $this->checkProgressAccount($user['userid']);
        if(empty($progressAccount)) {
            exit(json_encode([
                'status'    => false,
                'message'   => "No progress account",
                'response'  => []
            ]));
        }

        $demoFile = empty($progressAccount['ACC_F_SIMULASI_IMG']) ? null : FileUpload::awsFile($progressAccount['ACC_F_SIMULASI_IMG']);
        $fotoIdentitas = (empty($progressAccount['ACC_F_APP_FILE_ID'])) ? null : FileUpload::awsFile($progressAccount['ACC_F_APP_FILE_ID']);
        $fotoTerbaru = (empty($progressAccount['ACC_F_APP_FILE_FOTO'])) ? null : FileUpload::awsFile($progressAccount['ACC_F_APP_FILE_FOTO']);
        $fotoPendukung = (empty($progressAccount['ACC_F_APP_FILE_IMG'])) ? null : FileUpload::awsFile($progressAccount['ACC_F_APP_FILE_IMG']);
        $fotoPendukungLainnya = (empty($progressAccount['ACC_F_APP_FILE_IMG2'])) ? null : FileUpload::awsFile($progressAccount['ACC_F_APP_FILE_IMG2']);

        exit(json_encode([
            'status'    => true,
            'message'   => "Progress account",
            'response'  => [
                'id' => intval($progressAccount['ID_ACC']),
                'id_hash' => md5(md5($progressAccount['ID_ACC'])),
                'type' => $progressAccount['RTYPE_TYPE'],
                'type_acc' => $progressAccount['RTYPE_SUFFIX'],
                'country' => $progressAccount['ACC_COUNTRY'],
                'id_type' => $progressAccount['ACC_TYPE_IDT'],
                'id_number' => $progressAccount['ACC_NO_IDT'],
                'app_foto_simulasi' => $demoFile,
                'app_foto_identitas' => $fotoIdentitas,
                'app_foto_terbaru' => $fotoTerbaru,
                'app_foto_pendukung' => $fotoPendukung,
                'app_foto_pendukung_lainnya' => $fotoPendukungLainnya,
                'npwp' => $progressAccount['ACC_F_APP_PRIBADI_NPWP'],
                'date_of_birth' => $progressAccount['ACC_TANGGAL_LAHIR'],
                'place_of_birth' => $progressAccount['ACC_TEMPAT_LAHIR'],
                'gender' => $progressAccount['ACC_F_APP_PRIBADI_KELAMIN'],
                'province' => $progressAccount['ACC_PROVINCE'],
                'city' => $progressAccount['ACC_REGENCY'],
                'district' => $progressAccount['ACC_DISTRICT'],
                'village' => $progressAccount['ACC_VILLAGE'],
                'rt' => $progressAccount['ACC_RT'],
                'rw' => $progressAccount['ACC_RW'],
                'address' => $progressAccount['ACC_ADDRESS'],
                'postal_code' => $progressAccount['ACC_ZIPCODE'],
                'marital_status' => $progressAccount['ACC_F_APP_PRIBADI_STSKAWIN'],
                'wife_husband_name' => $progressAccount['ACC_F_APP_PRIBADI_NAMAISTRI'],
                'mother_name' => $progressAccount['ACC_F_APP_PRIBADI_IBU'],
                'phone_home' => $progressAccount['ACC_F_APP_PRIBADI_TLP'],
                'fax_home' => $progressAccount['ACC_F_APP_PRIBADI_FAX'],
                'phone_number' => $progressAccount['ACC_F_APP_PRIBADI_HP'],
                'tujuan_investasi' => $progressAccount['ACC_F_APP_TUJUANBUKA'],
                'pengalaman_investasi' => $progressAccount['ACC_F_APP_PENGINVT'],
                'pengalaman_investasi_bidang' => $progressAccount['ACC_F_APP_PENGINVT_BIDANG'],
                'keluarga_bursa' => $progressAccount['ACC_F_APP_KELGABURSA'],
                'pernyataan_pailit' => $progressAccount['ACC_F_APP_PAILIT'],
                'drrt_name' => $progressAccount['ACC_F_APP_DRRT_NAMA'],
                'drrt_status' => $progressAccount['ACC_F_APP_DRRT_HUB'],
                'drrt_phone' => $progressAccount['ACC_F_APP_DRRT_TLP'],
                'drrt_address' => $progressAccount['ACC_F_APP_DRRT_ALAMAT'],
                'drrt_postal_code' => $progressAccount['ACC_F_APP_DRRT_ZIP'],
                'kerja_nama' => $progressAccount['ACC_F_APP_KRJ_NAMA'],
                'kerja_tipe' => $progressAccount['ACC_F_APP_KRJ_TYPE'],
                'kerja_bidang' => $progressAccount['ACC_F_APP_KRJ_BDNG'],
                'kerja_jabatan' => $progressAccount['ACC_F_APP_KRJ_JBTN'],
                'kerja_lama' => $progressAccount['ACC_F_APP_KRJ_LAMA'],
                'kerja_lama_sebelum' => $progressAccount['ACC_F_APP_KRJ_LAMASBLM'],
                'kerja_alamat' => $progressAccount['ACC_F_APP_KRJ_ALAMAT'],
                'kerja_zip' => $progressAccount['ACC_F_APP_KRJ_ZIP'],
                'kerja_telepon' => $progressAccount['ACC_F_APP_KRJ_TLP'],
                'kerja_fax' => $progressAccount['ACC_F_APP_KRJ_FAX'],
                'kekayaan' => $progressAccount['ACC_F_APP_KEKYAN'],
                'kekayaan_rumah_lokasi' => $progressAccount['ACC_F_APP_KEKYAN_RMHLKS'],
                'kekayaan_njop' => $progressAccount['ACC_F_APP_KEKYAN_NJOP'],
                'kekayaan_deposit' => $progressAccount['ACC_F_APP_KEKYAN_DPST'],
                'kekayaan_nilai' => $progressAccount['ACC_F_APP_KEKYAN_NILAI'],
                'kekayaan_lain' => $progressAccount['ACC_F_APP_KEKYAN_LAIN']
            ]
        ]));
    }

    public function createDemo($data, $user) {
        global $web_name_full;
        $this->checkCsrfToken($data);
       
        $demoAccount = Account::getDemoAccount($user['userid']);
        if(!empty($demoAccount)) {
            exit(json_encode([
                'status'    => false,
                'message'   => "Sudah memiliki akun demo",
                'response'  => []
            ]));
        }

       $createDemo = MetatraderFactory::createDemo($user['MBR_NAME'], $user['MBR_EMAIL']);
        if(!$createDemo['success']) {
            $this->db->rollback();
            exit(json_encode([
                'status'    => false,
                'message'   => $createDemo['message'] ?? "Gagal",
                'response'  => []
            ]));
        }

        /** Insert Demo */
        $demoData = $createDemo['data'];
        $insertDemo = Database::insert("tb_racc", [
            'ACC_MBR' => $user['MBR_ID'],
            'ACC_DERE' => 2,
            'ACC_TYPE' => $demoData['type'],
            'ACC_LOGIN' => $demoData['login'],
            'ACC_PASS' => $demoData['password'],
            'ACC_INVESTOR' => $demoData['investor'],
            'ACC_PASSPHONE' => $demoData['passphone'],
            'ACC_INITIALMARGIN' => MetatraderFactory::$initMarginDemo,
            'ACC_FULLNAME' => $user['MBR_NAME'],
            'ACC_DATETIME' => date("Y-m-d H:i:s"),
        ]);
        

        /** Send Notification Email */
        $emailData = [
            "subject" => "Demo Account Information - ". ProfilePerusahaan::get()['PROF_COMPANY_NAME'] ." ".date('Y-m-d H:i:s'),
            "name" => $user["MBR_NAME"],
            "login" => $demoData['login'],
            "metaPassword"  => $demoData['password'],
            "metaInvestor"  => $demoData['investor'],
            "metaPassPhone" => $demoData['passphone'],
        ];
        
        $emailSender = EmailSender::init(['email' => $user['MBR_EMAIL'], 'name' => $user['MBR_NAME']]);
        $emailSender->useFile("create-demo", $emailData);
        $send = $emailSender->send();

        exit(json_encode([
            'status'    => true,
            'redirect'  => "account-type",
            'message'   => "Buat akun demo berhasil, Silakan periksa email Anda.Dan jangan beri tahu password, investor, passphone Anda kepada siapa pun!",
            'response'  => []
        ]));
    }

    public function accountType($data, $user) {
        $this->checkCsrfToken($data);
        if(empty($data['account-type'])) {
            exit(json_encode([
                'status'    => false,
                'message'   => "Jenis akun diperlukan",
                'response'  => []
            ]));
        }

        /** Account Suffix */
        $suffix = $data['account-type'];
        $raccType = Account::checkAccountSuffix($suffix);
        if(empty($raccType)) {
            exit(json_encode([
                'status'    => false,
                'message'   => "Jenis Akun Tidak Ditemukan",
                'response'  => []
            ]));
        }
        
        /** Cek apakah produk compatibel dengan akunnya */
        if(!empty($user['MBR_SUFFIX'])) {
            if($user['MBR_SUFFIX'] != $suffix) {
                exit(json_encode([
                    'status'    => false,
                    'message'   => "Jenis akun tidak valid",
                    'response'  => []
                ]));
            }
        }

        if(!empty($user['MBR_SUFFIX_EXCLUDE'])) {
            $explode = explode(",", $user['MBR_SUFFIX_EXCLUDE']);
            if(in_array($suffix, $explode)) {
                exit(json_encode([
                    'status'    => false,
                    'message'   => "Akun tidak tersedia",
                    'response'  => []
                ]));
            }
        }

        /** Check create akun multi */
        if(strtoupper($raccType['RTYPE_TYPE_AS']) == "MULTILATERAL") {
            if($user['MBR_ACCMULTI'] != -1) {
                exit(json_encode([
                    'status'    => false,
                    'message'   => "Mohon hubungi CS untuk membuat akun Multilateral",
                    'response'  => []
                ]));
            }
        }

        /** Check max account */
        $realAcc = Account::myAccount($user['MBR_ID']);
        $microAcc = [];
        foreach($realAcc as $acc) {
            if(strtolower($acc['RTYPE_TYPE']) == "micro") {
                $microAcc[] = $acc;
            }
        }

        if(count($realAcc) >= $user['MBR_ACCMAX']) {
            exit(json_encode([
                'status'    => false,
                'message'   => "Sudah mencapai limit pembuatan real account",
                'response'  => []
            ]));
        }

        if(strtoupper($raccType['RTYPE_TYPE']) == "MICRO") {
            if(count($microAcc) >= $user['MBR_ACCMAX_MICRO']) {
                exit(json_encode([
                    'status'    => false,
                    'message'   => "Sudah mencapai limit pembuatan real account (micro)",
                    'response'  => []
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
                        'status'    => false,
                        'message'   => "Status salinan tidak valid",
                        'response'  => []
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
                        'status'    => false,
                        'message'   => "Gagal membuat akun",
                        'response'  => []
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
            $sqlUpdate = $this->db->prepare("UPDATE tb_racc SET ACC_TYPE = ? WHERE ID_ACC = ?");
            $sqlUpdate->bind_param("ii", $raccType['ID_RTYPE'], $progressAccount['ID_ACC']);
            if(!$sqlUpdate->execute()) {
                exit(json_encode([
                    'status'    => false,
                    'message'   => "Perbarui Jenis Akun Gagal",
                    'response'  => []
                ]));
            }
        }

        exit(json_encode([
            'status'    => true,
            'redirect'  => "verifikasi-identitas",
            'message'   => "Pilih Jenis Akun Sukses",
            'response'  => []
        ]));
    }

    public function verifikasiIdentitas($data, $user) {
        $this->checkCsrfToken($data);
        $progressAccount = $this->checkProgressAccount(md5(md5($user['MBR_ID'])));

        $required = $this->required(['country', 'id_type', 'number'], $data);
        if($required !== TRUE) {
            exit(json_encode([
                'status'    => false,
                'message'   => $required,
                'response'  => []
            ]));
        }

        /** Check id_type */
        if(!in_array(strtoupper($data['id_type']), ['KTP', 'PASSPORT'])) {
            exit(json_encode([
                'status'    => false,
                'message'   => "Tipe identitas tidak valid",
                'response'  => []
            ]));
        }

        /** check Number  */
        if(is_numeric($data['number']) === FALSE || strlen($data['number']) < 15) {
            exit(json_encode([
                'status'    => false,
                'message'   => "Nomor identitas tidak valid",
                'response'  => []
            ]));
        }

        /** Check No Identitas */
        switch(strtoupper($data['id_type'])) {
            case "KTP":
                $noKtp = $data['number'];
                if(is_numeric($noKtp) === FALSE) {
                    exit(json_encode([
                        'status'    => false,
                        'message'   => "Nomor KTP tidak valid",
                        'response'  => []
                    ]));
                }

                if(strlen($noKtp) < 16) {
                    exit(json_encode([
                        'status'    => false,
                        'message'   => "Nomor KTP harus 16 digit atau lebih",
                        'response'  => []
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
                        'status'    => false,
                        'message'   => "Nomor KTP telah terdaftar/digunakan",
                        'response'  => []
                    ]));
                }
                break;
        }
        
        /** Check Status */
        $this->isAllowToEdit($progressAccount['ACC_STS']);
        
        /** Upload Selfie Photo */
        $this->uploadSelfiePhoto($data, $user);

        /** Upload KTP Photo */
        $this->uploadKtpPhoto($data, $user);
        
        // /** Verifikasi ke Verihub (Jika belum pernah berhasil) */
        // $statusVerifikasiVerihub = $progressAccount['ACC_DOC_VERIF'] ?? 0;
        // if($statusVerifikasiVerihub == 0) {
        //     // $verif = $this->verifikasiVerihub($data, $user, $progressAccount);
        //     $verif = 1;
        //     $statusVerifikasiVerihub = -1;
        //     $data['reference_id'] = $verif;
        // }

        

        // /** Update Status Verifikasi (Jika berbeda dari sebelumnya) */
        // if($statusVerifikasiVerihub != $progressAccount['ACC_DOC_VERIF']) {
        //     $updateData['ACC_DOC_VERIF'] = $statusVerifikasiVerihub;
        // }

        /** Update Progress Account */
        $updateData = [
            'ACC_COUNTRY' => $data['country'],
            'ACC_TYPE_IDT' => strtoupper($data['id_type']),
            'ACC_NO_IDT' => $data['number']
        ];

        $update = Database::update("tb_racc", $updateData, ['ID_ACC' => $progressAccount['ID_ACC']]);
        if($update !== TRUE) {
            exit(json_encode([
                'status'    => false,
                'message'   => $update ?? "Gagal memperbarui progress account",
                'response'  => []
            ]));
        }

        exit(json_encode([
            'status'    => true,
            'redirect'  => "kelengkapan-formulir",
            'message'   => "Berhasil",
            'response'  => []
        ])); 
    }

    private function uploadSelfiePhoto($data, $user) {
        global $region, $IAM_KEY, $IAM_SECRET, $bucketName, $folder;
        $progressAccount = $this->checkProgressAccount($user['userid']);
        $verihub = VerihubFactory::init();

        /** Upload Dokumen Foto Terbaru */
        if(empty($_FILES['app_foto_terbaru']) || $_FILES['app_foto_terbaru']['error'] != 0) {
            if(!empty($progressAccount['ACC_F_APP_FILE_FOTO'])) {
                return false;
            }
            
            exit(json_encode([
                'status'    => false,
                'message'   => "Mohon upload foto terbaru",
                'response'  => []
            ]));
        }

        if($progressAccount['ACC_DOC_VERIF'] == -1) {
            exit(json_encode([
                'status'    => false,
                'message'   => "Dokumen telah diverifikasi, tidak dapat dirubah",
                'response'  => []
            ]));
        }

        /** validasi file sebelum di upload */
        $checkSelfie = $verihub->validate_photoSelfie($_FILES['app_foto_terbaru']);
        if(!is_array($checkSelfie)) {
            exit(json_encode([
                'status'    => false,
                'message'   => "Terdapat kesalahan pada foto Selfie Anda",
                'response'  => []
            ]));
        }

        if(empty($checkSelfie['image_scaling'])) {
            exit(json_encode([
                'status'    => false,
                'message'   => "Invalid Data",
                'response'  => []
            ]));
        }

        $newFileName    = "regol_selfie_".time().rand(1000000, 9999999).".jpeg";
        $target_dir     = $_SERVER['DOCUMENT_ROOT'] . "/assets/uploads/{$newFileName}";
        $upload_local   = file_put_contents($target_dir, $checkSelfie['image_scaling']);
        if(!$upload_local) {
            exit(json_encode([
                'status'    => false,
                'message'   => "Gagal saat mengunggah foto",
                'response'  => []
            ]));
        }

        $awsCredential = FileUpload::credential();
        $s3 = new Aws\S3\S3Client([
            'region'  => $awsCredential['region'],
            'version' => 'latest',
            'credentials' => [
                'key'    => $awsCredential['key'],
                'secret' => $awsCredential['secretKey'],
            ]
        ]);

        try {
            /** Upload to AWS */
            $result = $s3->putObject([
                'Bucket' => $awsCredential['bucketName'],
                'Key'    => $awsCredential['folder'] ."/".$newFileName,
                'Body'   => fopen($target_dir, 'r'),
                'ACL'    => 'public-read', // make file 'public'
            ]);

            /** Delete file from local disk */
            unlink($target_dir);

            
        } catch (Aws\S3\Exception\S3Exception $e) {
            exit(json_encode([
                'status'    => false,
                'message'   => "Gagal mengunggah foto Selfie (400)",
                'response'  => []
            ]));
        }

        // /** Detect FaceLiveness */
        // if(strtoupper($progressAccount['RTYPE_TYPE']) == "MICRO") {
        //     $base64Image = file_get_contents($aws_folder . $newFileName);
        //     $base64Image = ("data:image/jpeg;base64,".base64_encode($base64Image));
        //     $validSelfie = $verihub->detectFaceLiveness(['mbrid' => $progressAccount['ACC_MBR'], 'image' => $base64Image]);
        //     if($validSelfie['status'] !== TRUE || $validSelfie['code'] != 200) {
        //         exit(json_encode([
        //             'status' => false,
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
                'status'    => false,
                'message'   => $updateImage ?? "Gagal memperbarui foto terbaru, mohon coba lagi",
                'response'  => []
            ]));
        }

        // exit(json_encode([
        //     'status' => true,
        //     'alert' => [
        //         'title' => "Berhasil",
        //         'text'  => "Foto selfie berhasil disimpan",
        //         'icon'  => "success"
        //     ] 
        // ]));
    }

    private function uploadKtpPhoto($data, $user) {
        global $region, $IAM_KEY, $IAM_SECRET, $bucketName, $folder;
        $progressAccount = $this->checkProgressAccount(md5(md5($user['MBR_ID'])));
        $verihub = VerihubFactory::init();
        
        /** Upload Dokumen Foto KTP */
        if(empty($_FILES['app_foto_identitas']) || $_FILES['app_foto_identitas']['error'] != 0) {
            if(!empty($progressAccount['ACC_F_APP_FILE_ID'])) {
                return false;
            }

            exit(json_encode([
                'status'    => false,
                'message'   => "Mohon upload foto KTP",
                'response'  => []
            ]));
        }

        // if($progressAccount['ACC_DOC_VERIF'] == -1) {
        //     exit(json_encode([
        //         'status'    => false,
        //         'message'   => "Dokumen telah diverifikasi, tidak dapat dirubah",
        //         'response'  => []
        //     ]));
        // }

        /** validasi file sebelum di upload */
        $validKtp = $verihub->validate_photoKtp($_FILES['app_foto_identitas']);
        if(!is_array($validKtp)) {
            exit(json_encode([
                'status'    => false,
                'message'   => "Terdapat kesalahan pada foto KTP Anda",
                'response'  => []
            ]));
        }

        if(empty($validKtp['image_scaling'])) {
            exit(json_encode([
                'status'    => false,
                'message'   => "Invalid Data",
                'response'  => []
            ]));
        }

        // $uploadFotoKtp = upload_myfile($validKtp, "regol_ktp");
        $newFileName    = "regol_ktp_".time().rand(1000000, 9999999).".jpeg";
        $target_dir     = $_SERVER['DOCUMENT_ROOT'] . "/assets/uploads/{$newFileName}";
        $upload_local   = file_put_contents($target_dir, $validKtp['image_scaling']);
        if(!$upload_local) {
            exit(json_encode([
                'status'    => false,
                'message'   => "Gagal saat mengunggah foto",
                'response'  => []
            ]));
        }


        $awsCredential = FileUpload::credential();
        $s3 = new Aws\S3\S3Client([
            'region'  => $awsCredential['region'],
            'version' => 'latest',
            'credentials' => [
                'key'    => $awsCredential['key'],
                'secret' => $awsCredential['secretKey'],
            ]
        ]);

        try {
            /** Upload to AWS */
            $result = $s3->putObject([
                'Bucket' => $awsCredential['bucketName'],
                'Key'    => $awsCredential['folder'] ."/".$newFileName,
                'Body'   => fopen($target_dir, 'r'),
                'ACL'    => 'public-read', // make file 'public'
            ]);

            /** Delete file from local disk */
            unlink($target_dir);

            
        } catch (Aws\S3\Exception\S3Exception $e) {
            exit(json_encode([
                'status'    => false,
                'message'   => (ini_get("display_errors") == "1") ? $e->getMessage() : "Gagal mengunggah foto KTP (402)",
                'response'  => []
            ]));
        }
        

        $data = [
            'ACC_F_APP_FILE_ID' => $newFileName,
            'ACC_F_APP_FILE_ID_MIME' => "image/jpeg"
        ];

        $updateImage = Database::update("tb_racc", $data, ['ID_ACC' => $progressAccount['ID_ACC']]);
        if($updateImage !== TRUE) {
            exit(json_encode([
                'status'    => false,
                'message'   => $updateImage ?? "Gagal memperbarui foto KTP, mohon coba lagi",
                'response'  => []
            ]));
        }

        // exit(json_encode([
        //     'status' => true,
        //     'alert' => [
        //         'title' => "Berhasil",
        //         'text'  => "Foto KTP berhasil disimpan",
        //         'icon'  => "success"
        //     ] 
        // ]));
    }

    private function verifikasiVerihub($data, $user, $progressAccount) {
        global $aws_folder;
        $verihub = VerihubFactory::init();
        $uniqid = uniqid();
        $reference_id = md5($user['MBR_ID'] . $uniqid);
        $fileContentKTP = file_get_contents($aws_folder . $progressAccount['ACC_F_APP_FILE_ID']);
        $fileContentSelfie = file_get_contents($aws_folder . $progressAccount['ACC_F_APP_FILE_FOTO']);
        $sendVerification = $verihub->send_idVerification([
            'mbrid' => $user['MBR_ID'],
            'account_id' => md5($progressAccount['ID_ACC']),
            'nik'   => $progressAccount['ACC_NO_IDT'],
            'name'  => $progressAccount['ACC_FULLNAME'],
            'birth_date' => $progressAccount['ACC_TANGGAL_LAHIR'],
            'email' => $user['MBR_EMAIL'], 
            'phone' => "6285954536593", 
            'ktp_photo' => ("data:".$progressAccount['ACC_F_APP_FILE_ID_MIME'].";base64,".base64_encode($fileContentKTP)), 
            'selfie_photo' => ("data:".$progressAccount['ACC_F_APP_FILE_FOTO_MIME'].";base64,".base64_encode($fileContentSelfie)), 
            'reference_id' => $reference_id
        ]);

        if(!$sendVerification['status']) {
            exit(json_encode([
                'status'    => false,
                'message'   => (ini_get("display_errors") == "1")? $sendVerification['message'] : "Verifikasi Gagal",
                'response'  => []
            ]));
        }

        return $reference_id;
    }


    public function apr_pengumpulan_data($data, $user) {
        $required = [
            'app_fullname' => "Nama Lengkap",
            'app_phone_code' => "Kode Negara",
            'app_phone' => "Nomor Telepon",
            'app_date_of_birth' => "Tanggal Lahir",
            'app_place_of_birth' => "Tempat Lahir",
            'app_gender' => "Jenis Kelamin",
        ];

        foreach($required as $key => $text) {
            if(empty($data[ $key ])) {
                exit(json_encode([
                    'status' => false,
                    'message' => "Kolom {$text} diperlukan",
                    'response' => []
                ]));
            }
        }

        $progressAccount = $this->checkProgressAccount(md5(md5($user['MBR_ID'])));

        /** Validasi Nomor NPWP */
        $npwp = 0;
        if($progressAccount['RTYPE_TYPE'] != "MICRO") {
            $npwp = Helper::stringTonumber($data['app_npwp']);
            if($npwp == 0) {
                exit(json_encode([
                    'status' => false,
                    'message' => "Nomor NPWP tidak valid",
                    'response' => []
                ]));
            }

            if(strlen($npwp) < 16) {
                exit(json_encode([
                    'status' => false,
                    'message' => "Nomor NPWP harus 16 digit atau lebih",
                    'response' => []
                ]));
            }
        }

        /** Validasi Nomor Telepon */
        $data['app_phone_code'] = Helper::stringTonumber($data['app_phone_code']);
        $data['app_phone'] = Helper::stringTonumber($data['app_phone']);

        if(substr($data['app_phone'], 0, 1) == "0") {
            $data['app_phone'] = substr($data['app_phone'], 1);
        }
        
        $final_phone = $data['app_phone_code'] . $data['app_phone'];
        if($final_phone == 0) {
            exit(json_encode([
                'status' => false,
                'message' => "Nomor Telepon tidak valid",
                'response' => []
            ]));
        }

        /** Validasi Tanggal Lahir */
        $data['app_date_of_birth'] = date("Y-m-d", strtotime($data['app_date_of_birth']));
        if(strpos($data['app_date_of_birth'], "1970") !== false) {
            exit(json_encode([
                'status' => false,
                'message' => "Tanggal Lahir tidak valid",
                'response' => []
            ]));
        }

        /** Validasi jenis kelamin */
        $data['app_gender'] = strtolower($data['app_gender']);
        if(!in_array($data['app_gender'], ['laki-laki', 'perempuan'])) {
            exit(json_encode([
                'status' => false,
                'message' => "Jenis Kelamin tidak valid",
                'response' => []
            ]));
        }

        $updateData = [
            'ACC_FULLNAME' => $data['app_fullname'],
            'ACC_F_APP_PRIBADI_NAMA' => $data['app_fullname'],
            'ACC_F_APP_PRIBADI_NPWP' => $data['app_npwp'],
            'ACC_F_APP_PRIBADI_KELAMIN' => $data['app_gender'],
            'ACC_F_APP_PRIBADI_HP' => $final_phone,
            'ACC_TEMPAT_LAHIR' => $data['app_place_of_birth'],
            'ACC_TANGGAL_LAHIR' => $data['app_date_of_birth'],
        ];

        $update = Database::update("tb_racc", $updateData, ['ID_ACC' => $progressAccount['ID_ACC']]);
        if($update !== TRUE) {
            exit(json_encode([
                'status' => false,
                'message' => "Gagal memperbarui data pribadi",
                'response' => []
            ]));
        }

        exit(json_encode([
            'status' => true,
            'redirect' => "pernyataan-simulasi",
            'message' => "Berhasil memperbarui data pribadi",
            'response' => []
        ]));
    }

    public function pernyataan_simulasi($data, $user) {
        /** Progress Account */
        $progressAccount = $this->checkProgressAccount(md5(md5($user['MBR_ID'])));
        
        /** Check Status */
        $this->isAllowToEdit(status: $progressAccount['ACC_STS']);
        
        $required = [
            'app_province' => "Nama Provinsi",
            'app_city' => "Nama Kabupaten/Kota",
            'app_district' => "Nama Kecamatan",
            'app_village' => "Nama Desa/Kelurahan",
            'app_zipcode' => "Kode Pos",
            'app_rt' => "RT",
            'app_rw' => "RW",
            'app_address' => "Alamat Lengkap"
        ];

        foreach($required as $key => $text) {
            if(empty($data[ $key ])) {
                exit(json_encode([
                    'status' => false,
                    'message' => "Kolom {$text} diperlukan",
                    'response' => []
                ]));
            }
        }

        $progressAccount = $this->checkProgressAccount($user['userid']);

        /** Check Peresetujuan */
        $data['app_agree'] = strtolower($data['app_agree'] ?? "");
        $agree = $data['app_agree'] ?? "tidak";
        if(strtolower($agree) != "ya") {
            exit(json_encode([
                'status' => false,
                'message' => "Anda harus menyetujui untuk melanjutkan ke tahap berikutnya",
                'response' => []
            ]));
        }

        $demoAccount = Account::getDemoAccount($user['userid']);
        if(empty($demoAccount)) {
            exit(json_encode([
                'status' => false,
                'message' => "Akun demo tidak valid",
                'response' => []
            ]));
        }

        /** check apakah sudah pernah transaksi dengan akun demo */
        $sqlGet = $this->db->query("SELECT TICKET FROM mt5_trades WHERE `LOGIN` = " . $demoAccount['ACC_LOGIN']);
        if($sqlGet->num_rows == 0) {
            exit(json_encode([
                'status' => false,
                'message' => "Anda belum melakukan transaksi dengan akun demo",
                'response' => []
            ]));
        }

        /** Check Alamat */
        $province = strtoupper($data['app_province']);
        $regency = strtoupper($data['app_city']);
        $district = strtoupper($data['app_district']);
        $village = strtoupper($data['app_village']);
        $postalCode = $data['app_zipcode'];
        $sqlCheckAddress = $this->db->query("SELECT ID_KDP FROM tb_kodepos WHERE UPPER(KDP_KELURAHAN) = '{$village}' AND KDP_KECAMATAN = '{$district}' AND KDP_KABKO = '{$regency}' AND KDP_PROV = '{$province}' AND KDP_POS = $postalCode LIMIT 1");
        if($sqlCheckAddress->num_rows != 1) {
            exit(json_encode([
                'status' => false,
                'message' => "Kode pos tidak ditemukan / salah",
                'response' => []
            ]));
        }

        /** Update tb_racc */
        $updateData = [
            'ACC_F_SIMULASI'        => 1,
            'ACC_F_SIMULASI_IP'     => Helper::get_ip_address(),
            'ACC_F_SIMULASI_PERYT'  => "Ya",
            'ACC_F_SIMULASI_DATE'   => date("Y-m-d H:i:s"),
            'ACC_PROVINCE'          => $province,
            'ACC_REGENCY'           => $regency,
            'ACC_DISTRICT'          => $district,
            'ACC_VILLAGE'           => $village,
            'ACC_ZIPCODE'           => $postalCode,
            'ACC_RW'                => $data['app_rw'],
            'ACC_RT'                => $data['app_rt'],
            'ACC_ADDRESS'           => $data['app_address'],
            'ACC_DEMO'              => $demoAccount['ACC_LOGIN']
        ];

        $updateRacc = Database::update("tb_racc", $updateData, ['ID_ACC' => $progressAccount['ID_ACC']]);
        if($updateRacc !== TRUE) {
            exit(json_encode([
                'status' => false,
                'message' => "Gagal memperbarui data",
                'response' => []
            ]));
        }

        exit(json_encode([
            'status' => true,
            'redirect' => "marital-status",
            'message' => "Berhasil memperbarui data",
            'response' => []
        ]));
    }

    public function apr_status_perkawinan($data, $user) {
        if(empty($data['app_status_perkawinan'])) {
            exit(json_encode([
                'status' => false,
                'message' => "Status Perkawinan diperlukan",
                'response' => []
            ]));
        }

        if(empty($data['app_status_perkawinan'])) {
            exit(json_encode([
                'status' => false,
                'message' => "Status Perkawinan diperlukan",
                'response' => []
            ]));
        }

        $progressAccount = $this->checkProgressAccount(md5(md5($user['MBR_ID'])));
        
        if(!empty($data['app_nomor_tlp_rumah'])) {
            if(is_numeric($data['app_nomor_tlp_rumah']) === FALSE) {
                exit(json_encode([
                    'status' => false,
                    'message' => "Nomor Telepon harus berupa angka",
                    'response' => []
                ]));
            }
        }

        if(!empty($data['app_nomor_fax'])) {
            if(is_numeric($data['app_nomor_fax']) === FALSE) {
                exit(json_encode([
                    'status' => false,
                    'message' => "Nomor Faksimili harus berupa angka",
                    'response' => []
                ]));
            }
        }

        $updateData = [
            'ACC_F_APP_PRIBADI_STSKAWIN' => $data['app_status_perkawinan'],
            'ACC_F_APP_PRIBADI_TLP' => $data['app_nomor_tlp_rumah'] ?? 0,
            'ACC_F_APP_PRIBADI_FAX' => $data['app_nomor_fax'] ?? 0,
        ];

        $data['app_status_perkawinan'] = strtolower($data['app_status_perkawinan']);
        if($data['app_status_perkawinan'] != "tidak kawin") {
            if(empty($data['app_nama_istri'])) {
                exit(json_encode([
                    'status' => false,
                    'message' => "Nama Istri/Suami diperlukan",
                    'response' => []
                ]));
            }

            $updateData['ACC_F_APP_PRIBADI_NAMAISTRI'] = $data['app_nama_istri'];
        }

        $update = Database::update("tb_racc", $updateData, ['ID_ACC' => $progressAccount['ID_ACC']]);
        if($update !== TRUE) {
            exit(json_encode([
                'status' => false,
                'message' => "Gagal memperbarui data status perkawinan",
                'response' => []
            ]));
        }

        exit(json_encode([
            'status' => true,
            'redirect' => "pihak-darurat",
            'message' => "Berhasil memperbarui data status perkawinan",
            'response' => []
        ]));
    }

    public function apr_pihak_darurat($data, $user) {
        $required = [
            'app_darurat_nama' => "Nama Pihak Darurat",
            'app_darurat_alamat' => "Alamat Pihak Darurat",
            'app_darurat_kodepos' => "Kode Pos Pihak Darurat",
            'app_darurat_telepon' => "No. Telepon Pihak Darurat",
            'app_darurat_hubungan' => "Status Hubungan dengan Pihak Darurat",
        ];

        foreach($required as $key => $text) {
            if(empty($data[ $key ])) {
                exit(json_encode([
                    'status' => false,
                    'message' => "{$text} diperlukan",
                    'response' => []
                ]));
            }
        }

        $progressAccount = $this->checkProgressAccount(md5(md5($user['MBR_ID'])));

        if(!is_numeric($data['app_darurat_telepon'])) {
            exit(json_encode([
                'status' => false,
                'message' => "Nomor Telepon harus berupa angka",
                'response' => []
            ]));
        }

        $updateData = [
            'ACC_F_APP_DRRT_NAMA' => $data['app_darurat_nama'],
            'ACC_F_APP_DRRT_ALAMAT' => $data['app_darurat_alamat'],
            'ACC_F_APP_DRRT_ZIP' => $data['app_darurat_kodepos'],
            'ACC_F_APP_DRRT_TLP' => $data['app_darurat_telepon'],
            'ACC_F_APP_DRRT_HUB' => $data['app_darurat_hubungan'],
        ];

        $update = Database::update("tb_racc", $updateData, ['ID_ACC' => $progressAccount['ID_ACC']]);
        if($update !== TRUE) {
            exit(json_encode([
                'status' => false,
                'message' => "Gagal memperbarui data pihak darurat",
                'response' => []
            ]));
        }

        exit(json_encode([
            'status' => true,
            'redirect' => "tujuan-investasi",
            'message' => "Berhasil memperbarui data pihak darurat",
            'response' => []
        ]));
    }

    public function apr_tujuan_investasi($data, $user) {
        if(empty($data['app_tujuan_investasi'])) {
            exit(json_encode([
                'status' => false,
                'message' => "Tujuan Investasi diperlukan",
                'response' => []
            ]));
        }

        $progressAccount = $this->checkProgressAccount(md5(md5($user['MBR_ID'])));
        $data['app_tujuan_investasi'] = strtolower($data['app_tujuan_investasi']);
        if(!in_array($data['app_tujuan_investasi'], ['lindungi nilai', 'gain', 'spekulasi', 'lainnya'])) {
            exit(json_encode([
                'status' => false,
                'message' => "Tujuan Investasi tidak valid",
                'response' => []
            ]));
        }

        $update = Database::update("tb_racc", ['ACC_F_APP_TUJUANBUKA' => $data['app_tujuan_investasi']], ['ID_ACC' => $progressAccount['ID_ACC']]);
        if($update !== TRUE) {
            exit(json_encode([
                'status' => false,
                'message' => "Gagal memperbarui data tujuan investasi",
                'response' => []
            ]));
        }

        exit(json_encode([
            'status' => true,
            'redirect' => "pengalaman-investasi",
            'message' => "Berhasil memperbarui data tujuan investasi",
            'response' => []
        ]));
    }

    public function apr_pengalaman_investasi($data, $user) {
        if(empty($data['app_pengalaman_investasi'])) {
            exit(json_encode([
                'status' => false,
                'message' => "Pengalaman Investasi diperlukan",
                'response' => []
            ]));
        }

        loadModel("Helper");
        $helperClass = new Helper();
        $progressAccount = $this->checkProgressAccount(md5(md5($user['MBR_ID'])));
        $data['app_pengalaman_investasi'] = strtolower($data['app_pengalaman_investasi']);
        if(!in_array($data['app_pengalaman_investasi'], ['ya', 'tidak'])) {
            exit(json_encode([
                'status' => false,
                'message' => "Pengalaman Investasi tidak valid",
                'response' => []
            ]));
        }

        if($data['app_pengalaman_investasi'] == "ya") {
            if(empty($data['app_nama_perusahaan'])) {
                exit(json_encode([
                    'status' => false,
                    'message' => "Nama perusahaan diperlukan",
                    'response' => []
                ]));
            }   
        }

        $updateData = [
            'ACC_F_PENGLAMAN' => 1,
            'ACC_F_PENGLAMAN_IP' => $helperClass->get_ip_address(),
            'ACC_F_PENGLAMAN_PERYT' => "Ya",
            'ACC_F_PENGLAMAN_PERYT_YA' => $data['app_pengalaman_investasi'],
            'ACC_F_PENGLAMAN_PERSH' => $data['app_nama_perusahaan'] ?? NULL,
            'ACC_F_PENGLAMAN_DATE' => date("Y-m-d H:i:s"),
        ];

        $update = $helperClass->updateWithArray("tb_racc", $updateData, ['ID_ACC' => $progressAccount['ID_ACC']]);  
        if($update !== TRUE) {      
            exit(json_encode([
                'status' => false,
                'message' => "Gagal memperbarui data pengalaman investasi",
                'response' => []
            ]));
        }

        newInsertLog([
            'mbrid' => $user['MBR_ID'],
            'module' => "create-account",
            'ref' => $progressAccount['ID_ACC'],
            'message' => "Progress Real Account (Pengalaman Investasi)",
            'device' => "mobile",
            'data'  => json_encode($data)
        ]);

        exit(json_encode([
            'status' => true,
            'redirect' => "informasi-pekerjaan",
            'message' => "Berhasil memperbarui data pengalaman investasi",
            'response' => []
        ]));
    }

    public function apr_informasi_pekerjaan($data, $user) {
        $required = [
            'nama_pekerjaan' => "Nama Pekerjaan",
            'nama_perusahaan' => "Nama Perusahaan",
            'bidang_usaha' => "Kode Pos Pihak Darurat",
            'jabatan_pekerjaan' => "No. Telepon Pihak Darurat",
            'lama_bekerja' => "Lama Bekerja",
            'alamat_kantor' => "Alamat Kantor",
            'lama_bekerja_sebelumnya' => "Lama Bekerja (Kantor Sebelumnya)",
        ];

        foreach($required as $key => $text) {
            if(empty($data[ $key ])) {
                exit(json_encode([
                    'status' => false,
                    'message' => "{$text} diperlukan",
                    'response' => []
                ]));
            }
        }
        
        /** Optional Input */
        $data['nomor_kantor'] = $data['nomor_kantor'] ?? 0;
        $data['no_fax_kantor'] = $data['no_fax_kantor'] ?? 0;
        $data['kodepos'] = $data['kodepos'] ?? 0;

        /** Check Nama Pekerjaan */
        $data['nama_pekerjaan'] = strtolower($data['nama_pekerjaan']);
        if(!in_array($data['nama_pekerjaan'], ['swasta', 'wiraswasta', 'ibu rt', 'profesional', 'asn', 'mahasiswa', 'pegawai bumn', 'lainnya'])) {
            exit(json_encode([
                'status' => false,
                'message' => "Nama Pekerjaan tidak valid",
                'response' => []
            ]));
        }

        loadModel("Helper");
        $helperClass = new Helper();
        $progressAccount = $this->checkProgressAccount(md5(md5($user['MBR_ID'])));
        
        /** Update */
        $updateData = [
            'ACC_F_APP_KRJ_TYPE' => $data['nama_pekerjaan'],
            'ACC_F_APP_KRJ_NAMA' => $data['nama_perusahaan'],
            'ACC_F_APP_KRJ_BDNG' => $data['bidang_usaha'],
            'ACC_F_APP_KRJ_JBTN' => $data['jabatan_pekerjaan'],
            'ACC_F_APP_KRJ_LAMA' => $data['lama_bekerja'],
            'ACC_F_APP_KRJ_LAMASBLM' => $data['lama_bekerja_sebelumnya'],
            'ACC_F_APP_KRJ_ALAMAT' => $data['alamat_kantor'],
            'ACC_F_APP_KRJ_ZIP' => $data['kodepos'],
            'ACC_F_APP_KRJ_TLP' => $data['nomor_kantor'],
            'ACC_F_APP_KRJ_FAX' => $data['no_fax_kantor'],
        ];

        $update = $helperClass->updateWithArray("tb_racc", $updateData, ['ID_ACC' => $progressAccount['ID_ACC']]);
        if($update !== TRUE) {
            exit(json_encode([
                'status' => false,
                'message' => "Gagal memperbarui data pekerjaan",
                'response' => []
            ]));
        }

        newInsertLog([
            'mbrid' => $user['MBR_ID'],
            'module' => "create-account",
            'ref' => $progressAccount['ID_ACC'],
            'message' => "Progress Real Account (Informasi Pekerjaan)",
            'device' => "mobile",
            'data'  => json_encode($data)
        ]);

        exit(json_encode([
            'status' => true,
            'redirect' => "informasi-bank",
            'message' => "Berhasil memperbarui data pekerjaan",
            'response' => []
        ]));
    }

    public function apr_keterangan_pailit($data, $user) {
        if(empty($data['app_keterangan_pailit'])) {
            exit(json_encode([
                'status' => false,
                'message' => "Keterangan Pailit diperlukan",
                'response' => []    
            ]));
        }

        if(empty($data['app_keluarga_bursa'])) {
            exit(json_encode([
                'status' => false,
                'message' => "Keterangan keluarga bursa diperlukan",
                'response' => []    
            ]));
        }

        $data['app_keterangan_pailit'] = strtolower($data['app_keterangan_pailit']);
        $data['app_keluarga_bursa'] = strtolower($data['app_keluarga_bursa']);
        if(!in_array($data['app_keterangan_pailit'], ['ya', 'tidak'])) {
            exit(json_encode([
                'status' => false,
                'message' => "Keterangan pailit tidak valid",
                'response' => []
            ]));
        }

        if($data['app_keterangan_pailit'] == "ya") {
            exit(json_encode([
                'status' => false,
                'message' => "Anda tidak bisa melanjutkan pendaftaran, karena Anda sudah pernah dinyatakan pailit",
                'response' => []    
            ]));
        }

        /** Cek keluarga bursa */
        if(!in_array($data['app_keluarga_bursa'], ['ya', 'tidak'])) {
            exit(json_encode([
                'status' => false,
                'message' => "Keterangan keluarga bursa tidak valid",
                'response' => []
            ]));
        }

        if($data['app_keluarga_bursa'] == "ya") {
            exit(json_encode([
                'status' => false,
                'message' => "Anda tidak bisa melanjutkan pendaftaran, karena memiliki anggota keluarga Anda yang bekerja di bursa",
                'response' => []    
            ]));
        }
        
        loadModel("Helper");
        $helperClass = new Helper();
        $progressAccount = $this->checkProgressAccount(md5(md5($user['MBR_ID'])));
        
        $updateData = [
            'ACC_F_APP_PAILIT' => strtolower($data['app_keterangan_pailit']),
            'ACC_F_APP_KELGABURSA' => strtolower($data['app_keluarga_bursa']),
        ];

        $update = $helperClass->updateWithArray("tb_racc", $updateData, ['ID_ACC' => $progressAccount['ID_ACC']]);
        if($update !== TRUE) {
            exit(json_encode([
                'status' => false,
                'message' => "Gagal memperbarui data keterangan pailit",
                'response' => []
            ]));
        }

        newInsertLog([
            'mbrid' => $user['MBR_ID'],
            'module' => "create-account",
            'ref' => $progressAccount['ID_ACC'],
            'message' => "Progress Real Account (Keterangan Pailit)",
            'device' => "mobile",
            'data'  => json_encode($data)
        ]);

        exit(json_encode([
            'status' => true,
            'message' => "Berhasil memperbarui data",
            'response' => []
        ]));
    }
    
    public function apr_daftar_kekayaan($data, $user) {
        $required = [
            'annual_income' => "Pendapatan Tahunan",
            'lokasi_rumah' => "Lokasi Rumah",
        ];

        foreach($required as $key => $text) {
            if(empty($data[ $key ])) {
                exit(json_encode([
                    'status' => false,
                    'message' => "Kolom {$text} diperlukan",
                    'response' => []
                ]));
            }
        }

        /** Validasi Numeric */
        $data['njop'] = $data['njop'] ?? 0;
        $data['deposit'] = $data['deposit'] ?? 0;
        $data['lainnya'] = $data['lainnya'] ?? 0;

        if(is_numeric($data['njop']) === false || $data['njop'] < 0) {
            exit(json_encode([  
                'status' => false,
                'message' => "Nilai NJOP tidak valid",
                'response' => []
            ]));
        }

        if(is_numeric($data['deposit']) === false || $data['deposit'] < 0) {
            exit(json_encode([  
                'status' => false,
                'message' => "Nilai deposito tidak valid",
                'response' => []
            ]));
        }

        if(is_numeric($data['lainnya']) === false || $data['lainnya'] < 0) {
            exit(json_encode([  
                'status' => false,
                'message' => "Nilai lainnya tidak valid",
                'response' => []
            ]));
        }

        loadModel("Helper");
        $helperClass = new Helper();
        $progressAccount = $this->checkProgressAccount(md5(md5($user['MBR_ID'])));
        

        $updateData = [
            'ACC_F_APP_KEKYAN' => $data['annual_income'],
            'ACC_F_APP_KEKYAN_RMHLKS' => $data['lokasi_rumah'],
            'ACC_F_APP_KEKYAN_NJOP' => $data['njop'],
            'ACC_F_APP_KEKYAN_DPST' => $data['deposit'],
            'ACC_F_APP_KEKYAN_LAIN' => $data['lainnya'],
            'ACC_F_APP_KEKYAN_NILAI' => $data['njop'] + $data['deposit'] + $data['lainnya'],
        ];

        $update = $helperClass->updateWithArray("tb_racc", $updateData, ['ID_ACC' => $progressAccount['ID_ACC']]);
        if($update !== TRUE) {
            exit(json_encode([
                'status' => false,
                'message' => "Gagal memperbarui data kekayaan",
                'response' => []
            ]));
        }

        newInsertLog([
            'mbrid' => $user['MBR_ID'],
            'module' => "create-account",
            'ref' => $progressAccount['ID_ACC'],
            'message' => "Progress Real Account (Kekayaan)",
            'device' => "mobile",
            'data'  => json_encode($data)
        ]);

        exit(json_encode([
            'status' => true,
            'message' => "Berhasil memperbarui data kekayaan",
            'response' => []
        ]));
    }    

    public function apr_dokumen_pendukung($data, $user) {
        if(empty($data['tipe'])) {
            exit(json_encode([
                'status' => false,
                'message' => "Tipe Dokumen Pendukung diperlukan",
                'response' => []
            ]));
        }   

        $dokumenPendukung = [
            'cover buku tabungan (recommended)',
            'tagihan kartu kredit',
            'tagihan listrik / air',
            'scan kartu npwp',
            'rekening koran bank',
            'pbb / bpjs',
            'lainnya'
        ];


        $data['tipe'] = strtolower($data['tipe']);
        if(!in_array($data['tipe'], $dokumenPendukung)) {
            exit(json_encode([
                'status' => false,
                'message' => "Dokumen Pendukung (".$data['tipe'].") tidak valid/tersedia",
                'response' => []
            ]));
        }

        loadModel("Helper");
        $helperClass = new Helper();
        $progressAccount = $this->checkProgressAccount(md5(md5($user['MBR_ID'])));
        
        /** Upload Dokumen Pendukung */
        if(empty($_FILES['dokumen']) || $_FILES['dokumen']['error'] != 0) {
            if(empty($progressAccount['ACC_F_APP_FILE_IMG'])) {
                exit(json_encode([
                    'status' => false,
                    'message' => "Mohon upload dokumen pendukung",
                    'response' => []
                ]));
            }

        }else {
            $uploadDokumenPendukung = upload_myfile($_FILES['dokumen'], "regol");
            if(!is_array($uploadDokumenPendukung) || !array_key_exists("filename", $uploadDokumenPendukung)) {
                exit(json_encode([
                    'status' => false,
                    'message' => $uploadDokumenPendukung ?? "Gagal mengunggah file dokumen pendukung",
                    'response' => []
                ]));
            }

            $updateImage = $helperClass->updateWithArray("tb_racc", ['ACC_F_APP_FILE_IMG' => $uploadDokumenPendukung['filename']], ['ID_ACC' => $progressAccount['ID_ACC']]);
            if($updateImage !== TRUE) {
                exit(json_encode([
                    'status' => false,
                    'message' => $updateImage ?? "Gagal memperbarui dokumen pendukung, mohon coba lagi",
                    'response' => []
                ]));
            }
        }

        /** Upload Dokumen Pendukung Lainnya */
        if(empty($_FILES['dokumen_lainnya']) || $_FILES['dokumen_lainnya']['error'] != 0) {
            if(empty($progressAccount['ACC_F_APP_FILE_IMG2'])) {
                exit(json_encode([
                    'status' => false,
                    'message' => $updateImage ?? "Gagal memperbarui dokumen pendukung, mohon coba lagi",
                    'response' => []
                ]));
            }
        
        }else {
            $uploadDokumenPendukung2 = upload_myfile($_FILES['dokumen_lainnya'], "regol");
            if(!is_array($uploadDokumenPendukung2) || !array_key_exists("filename", $uploadDokumenPendukung2)) {
                exit(json_encode([
                    'status' => false,
                    'message' => $uploadDokumenPendukung2 ?? "Gagal mengunggah file dokumen pendukung lainnya",
                    'response' => []
                ]));
            }

            $updateImage = $helperClass->updateWithArray("tb_racc", ['ACC_F_APP_FILE_IMG2' => $uploadDokumenPendukung2['filename']], ['ID_ACC' => $progressAccount['ID_ACC']]);
            if($updateImage !== TRUE) {
                exit(json_encode([
                    'status' => false,
                    'message' => $updateImage ?? "Gagal memperbarui dokumen pendukung lainnya, mohon coba lagi",
                    'response' => []
                ]));
            }
        }

        /** Update Tipe Dokumen Pendukung */
        $updateData = [
            'ACC_F_APP' => 1,
            'ACC_F_APP_IP' => $helperClass->get_ip_address(),
            'ACC_F_APP_FILE_TYPE' => $data['tipe'],
            'ACC_F_APPPEMBUKAAN_IP' => $helperClass->get_ip_address(),
            'ACC_F_APP_PERYT' => "Ya",
            'ACC_F_APPPEMBUKAAN_PERYT' => "Ya",
            'ACC_F_APP_DATE' => date("Y-m-d H:i:s"),
            'ACC_F_APPPEMBUKAAN_DATE' => date("Y-m-d H:i:s"),
        ];

        $update = $helperClass->updateWithArray("tb_racc", $updateData, ['ID_ACC' => $progressAccount['ID_ACC']]);
        if($update !== TRUE) {
            exit(json_encode([
                'status' => false,
                'message' => "Gagal memperbarui tipe dokumen pendukung",
                'response' => []
            ]));    
        }

        newInsertLog([
            'mbrid' => $user['MBR_ID'],
            'module' => "create-account",
            'ref' => $progressAccount['ID_ACC'],
            'message' => "Progress Real Account (Dokumen Pendukung)",
            'device' => "mobile",
            'data'  => json_encode($data)
        ]);

        exit(json_encode([
            'status' => true,
            'redirect' => "dokumen",
            'message' => "Berhasil memperbarui data dokumen pendukung",
            'response' => []
        ]));
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
                'status'   => false,
                'alert'     => [
                    'title' => "Gagal",
                    'text'  => "Anda harus menyetujui untuk melanjutkan ke tahap berikutnya",
                    'icon'  => "error"
                ]
            ]));  
        }

        
        loadModel("Helper");
        $helperClass = new Helper();


        $dataUpdate = [
            'ACC_F_CMPLT' => 1,
            'ACC_F_CMPLT_IP' => $helperClass->get_ip_address(), 
            'ACC_F_CMPLT_PERYT' => "Ya",
            'ACC_F_CMPLT_DATE' => date("Y-m-d H:i:s"),
            'ACC_STS' => 1,
            'ACC_KODE' => uniqid()
        ];

        $update = $helperClass->updateWithArray("tb_racc", $dataUpdate, ['ID_ACC' => $progressAccount['ID_ACC']]);

        if($update !== TRUE) {
            exit(json_encode([
                'status'   => false,
                'alert'     => [
                    'title' => "Gagal",
                    'text'  => $update ?? "Gagal memperbarui progress account",
                    'icon'  => "error"
                ]
            ])); 
        }

        newInsertLog([
            'mbrid' => $user['MBR_ID'],
            'module' => "create-account",
            'ref' => $progressAccount['ID_ACC'],
            'message' => "Progress Real Account (Kelengkapan Formulir)",
            'data'  => json_encode($data)
        ]);

        exit(json_encode([
            'status'   => true,
            'redirect'  => "/".$user['MBR_ID']. "/create-acc/selesai",
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
                'status'   => false,
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
                    'status'   => false,
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
                'status'   => false,
                'alert'     => [
                    'title' => "Gagal",
                    'text'  => "Mohon upload file bukti transfer deposit",
                    'icon'  => "error"
                ]
            ]));  
        }

        loadModel("Helper");
        loadModel("Account");
        $helperClass = new Helper();
        $classAcc = new Account();
        $amountSource = $helperClass->stringTonumber($data['dpnewacc_dpstval']);
        $amountFinal = 0;
        $currencyFrom = $progressAccount['RTYPE_CURR'];
        $currencyTo = "IDR";

        /** Check Bank nasabah */
        $userBank = myBank(md5(md5($user['MBR_ID'])), $data['dpnewacc_bankusr']);
        if(empty($userBank)) {
            exit(json_encode([
                'status'   => false,
                'alert'     => [
                    'title' => "Gagal",
                    'text'  => "Data Bank nasabah tidak ditemukan",
                    'icon'  => "error"
                ]
            ]));  
        }

        /** Check bank admin */
        $adminBank = $helperClass->getAdminBank($data['dpnewacc_bankcmpy']);
        if(empty($adminBank)) {
            exit(json_encode([
                'status'   => false,
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
                'status'   => false,
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
                'status'   => false,
                'alert'     => [
                    'title' => "Gagal",
                    'text'  => "Minimum deposit " . implode(" ", [$progressAccount['RTYPE_CURR'], formatCurrency($progressAccount['RTYPE_MINDEPOSIT'])]),
                    'icon'  => "error"
                ]
            ]));  
        }


        /** Convertsation */
        $convert = $classAcc->accountConvertation([
            'account_id' => $progressAccount['ID_ACC'],
            'amount' => $amountSource,
            'from' => $currencyFrom,
            'to' => $currencyTo
        ]);

        if(!is_array($convert)) {
            exit(json_encode([
                'status'   => false,
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
        $uploadFile = upload_myfile($_FILES['dpnewacc_tfprove'], "deposit_new_account");
        if(!is_array($uploadFile) || !array_key_exists("filename", $uploadFile)) {
            exit(json_encode([
                'status'   => false,
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
        $insert = $helperClass->insertWithArray("tb_dpwd", [
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
            'DPWD_IP' => $helperClass->get_ip_address(),
            'DPWD_DATETIME' => date("Y-m-d H:i:s")
        ]);

        if(!$insert) {
            $this->db->rollback();
            exit(json_encode([
                'status'   => false,
                'alert'     => [
                    'title' => "Gagal",
                    'text'  => "Failed to create transaction",
                    'icon'  => "error"
                ]
            ])); 
        }

        /** Update tb_racc */
        $update = $helperClass->updateWithArray("tb_racc", ['ACC_WPCHECK' => 2], ['ID_ACC' => $progressAccount['ID_ACC']]);
        if(!$update) {
            $this->db->rollback();
            exit(json_encode([
                'status'   => false,
                'alert'     => [
                    'title' => "Gagal",
                    'text'  => $update ?? "Failed to update account status",
                    'icon'  => "error"
                ]
            ])); 
        }

        $data['filename'] = $uploadFile['filename'];
        newInsertLog([
            'mbrid' => $user['MBR_ID'],
            'module' => "create-account",
            'ref' => $progressAccount['ID_ACC'],
            'ip' => $helperClass->get_ip_address(),
            'message' => "Progress Real Account (Deposit New Account)",
            'data'  => json_encode($data)
        ]);


        $this->db->commit();
        exit(json_encode([
            'status'   => true,
            'redirect'  => "/".$user['MBR_ID']. "/create-acc/deposit-new-account",
            'alert'     => [
                'title' => "Berhasil",
                'text'  => "Deposit new account sedang diprosess",
                'icon'  => "success"
            ]
        ])); 
    }
}