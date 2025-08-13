<?php

    use App\Models\Admin;
    use App\Models\Account;
    use App\Models\Dpwd;
    use App\Models\Helper;
    use App\Models\FileUpload;
    use App\Models\CompanyProfile;
    use App\Models\ProfilePerusahaan;

    $realAccount     = Account::realAccountDetail(($acc ?? ""));
    $accnd           = Account::accoundCondition($realAccount['ID_ACC']);
    $depositData     = Dpwd::findByRaccId($realAccount["ID_ACC"]);
    $COMPANY_PRF     = CompanyProfile::profilePerusahaan();
    $COMPANY_MOF     = CompanyProfile::getMainOffice();
    $company         = CompanyProfile::$name;
    $list_wpb_satu   = ProfilePerusahaan::list_wpb(-1, 2);
    $list_wpb_satu   = ProfilePerusahaan::list_wpb(2, 2);
    $tgl_lahir       = Helper::bulan(date("m", strtotime($realAccount['ACC_TANGGAL_LAHIR'])));
    $date_day        = Helper::hari(date('w', strtotime($realAccount["ACC_F_PERJ_DATE"])));
    $date_month      = Helper::bulan(date('m', strtotime($realAccount["ACC_F_PERJ_DATE"])));
    $company_name    = $COMPANY_PRF["COMPANY_NAME"];
    $userBank        = (!empty($progressAccount["MBR_BKJSN"])) ? json_decode($progressAccount["MBR_BKJSN"], true) : [];
    $profile         = array_merge(($COMPANY_PRF ?? []), ["OFFICE" => ($COMPANY_MOF ?? [])]);
    $bank            = explode("/", $depositData['DPWD_BANKSRC']);
    $admBanks        = $db->query("SELECT * FROM tb_bankadm");
    $offices         = $db->query("SELECT * FROM tb_office");
    $bankName        = $bank[0] ?? "-";
    $bankAccount     = $bank[1] ?? "-";
    $bankHolder      = $bank[2] ?? "-";

    $bapakatauibu = (!empty($realAccount['ACC_F_APP_PRIBADI_KELAMIN']) && $realAccount['ACC_F_APP_PRIBADI_KELAMIN'] == "Laki-laki")
    ? 'Bapak/<strike>Ibu</strike>'
    : '<strike>Bapak</strike>/Ibu';

    $idAcc = Helper::form_input($_GET['acc'] ?? "");
    $account = Account::realAccountDetail($idAcc);
    if(!$account) {
        exit('Invalid Request');
    }

    
    $steps = [
        [],
        [
            'title' => "Buat Akun Demo",
            'success' => !empty($realAccount['ACC_DEMO']),
            'page' => "create-demo",
            'show' => true
        ],
        [
            'title' => "Rate & Jenis Real Account",
            'success' => !empty($realAccount),
            'page' => "account-type",
            'show' => true
        ],
        [
            'title' => "Profile Perusahaan Pialang",
            'success' => !empty($realAccount['ACC_F_PROFILE']),
            'page' => "profile-perusahaan",
            'show' => true
        ],
        [
            'title' => "Pernyataan Simulasi Perdagangan Berjangka",
            'success' => !empty($realAccount['ACC_F_SIMULASI']),
            'page' => "pernyataan-simulasi",
            'show' => true
        ],
        [
            'title' => "Pernyataan Pengalaman Transaksi Perdagangan Berjangka",
            'success' => !empty($realAccount['ACC_F_PENGLAMAN']),
            'page' => "pernyataan-pengalaman",
            'show' => true
        ],
        [
            'title' => "Pernyataan Pengungkapan #1",
            'success' => !empty($realAccount['ACC_F_DISC']),
            'page' => "pernyataan-pengungkapan-1",
            'show' => true
        ],
        [
            'title' => "Aplikasi Pembukaan Rekening",
            'success' => !empty($realAccount['ACC_F_APP']),
            'page' => "aplikasi-pembukaan-rekening",
            'show' => true
        ],
        [
            'title' => "Pernyataan Pengungkapan #2",
            'success' => !empty($realAccount['ACC_F_DISC2']),
            'page' => "pernyataan-pengungkapan-2",
            'show' => true
        ],
        [
            'title' => "Formulir Dokumen Resiko",
            'success' => !empty($realAccount['ACC_F_RESK']),
            'page' => "formulir-dokumen-resiko",
            'show' => true
        ],
        [
            'title' => "Pernyataan Pengungkapan #3",
            'success' => !empty($realAccount['ACC_F_DISC3']),
            'page' => "pernyataan-pengungkapan-3",
            'show' => true
        ],
        [
            'title' => "Perjanjian Pemberian Amanat",
            'success' => !empty($realAccount['ACC_F_PERJ']),
            'page' => "perjanjian-pemberian-amanat",
            'show' => true
        ],
        [
            'title' => "Peraturan Perdagangan",
            'success' => !empty($realAccount['ACC_F_TRDNGRULE']),
            'page' => "peraturan-perdagangan",
            'show' => true
        ],
        [
            'title' => "Pernyataan Bertanggung Jawab",
            'success' => !empty($realAccount['ACC_F_KODE']),
            'page' => "pernyataan-bertanggung-jawab",
            'show' => true
        ],
        [
            'title' => "Pernyataan Dana Nasabah",
            'success' => !empty($realAccount['ACC_F_DANA']),
            'page' => "pernyataan-dana-nasabah",
            'show' => true
        ],
        [
            'title' => "Pernyataan Pengungkapan #4",
            'success' => !empty($realAccount['ACC_F_DISC4']),
            'page' => "pernyataan-pengungkapan-4",
            'show' => true
        ],
        [
            'title' => "Verifikasi Identitas",
            'success' => (($realAccount['ACC_DOC_VERIF'] ?? 0) == -1),
            'page' => "verifikasi-identitas",
            'show' => true
        ],
        [
            'title' => "Kelengkapan Formulir",
            'success' => !empty($realAccount['ACC_F_CMPLT']),
            'page' => "kelengkapan-formulir",
            'show' => true
        ],
        [
            'title' => "Deposit New Account",
            'success' => !empty($realAccount['ACC_F_CMPLT']),
            'page' => "deposit-new-account",
            'show' => ($realAccount['ACC_STS'] == 1 && $realAccount['ACC_WPCHECK'] >= 1)
        ],
    ];

?>

<!DOCTYPE html>
<html>
    <head>
        <?php require_once(__DIR__  . "/style.php"); ?>
        <style>
            @page {
                margin-left: 50px;
                margin-right: 50px;
            }
        </style>
    </head>
    <body>
        <?php require_once(__DIR__  . "/header.php"); ?><hr>

        <div class="section">
            <h4 class="text-center" style="margin: 0px;">VERIFIKASI KELENGKAPAN PROSES PENERIMAAN NASABAH SECARA ELEKTRONIK ONLINE</h4>
            <table class="table" style="margin-top: 20px;">
                <tbody>
                    <?php foreach($steps as $key => $st) : ?>
                        <?php if(!empty($st) && $st['page'] != "selesai") : ?>
                            <tr>
                                <td width="6%" class="text-center"><?= $key ?></td>
                                <td class="text-start fw-bold"><?= $st['title'] ?></td>
                                <td width="10%" class="text-center"><?= $st['success']? '<div style="font-family: DejaVu Sans, sans-serif;">✔</div>' : '<i class="fa-solid fa-x text-danger"></i>'; ?></td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </body>
</html>