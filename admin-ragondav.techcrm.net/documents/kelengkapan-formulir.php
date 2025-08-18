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
    $bank            = explode("/", ($depositData['DPWD_BANKSRC'] ?? ''));
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
                    <?php 
                    $arrayList = [
                        "PROFILE PERUSAHAAN PIALANG BERJANGKA",
                        "PERNYATAAN TELAH MELAKUKAN SIMULASI PERDAGANGAN BERJANGKA ATAU PERNYATAAN TELAH BERPENGALAMAN DALAM MELAKSANAKAN TRANSAKSI PERDAGANGAN BERJANGKA",
                        "PERNYATAAN PENGUNGKAPAN (DISCLOSURE STATEMENT)",
                        "APLIKASI PEMBUKAAN REKENING TRANSAKSI",
                        "PERNYATAAN PENGUNGKAPAN (DISCLOSURE STATEMENT)",
                        "DOKUMEN PEMBERITAHUAN ADANYA RISIKO",
                        "PERNYATAAN PENGUNGKAPAN (DISCLOSURE STATEMENT)",
                        "PERJANJIAN PEMBERIAN AMANAT",
                        "DAFTAR KONTRAK BERJANGKA, KONTRAK DERIVATIF DAN KONTRAK DERIVATIF LAINNYA BESERTA PERATURAN PERDAGANGAN (TRADING RULES)",
                        "PERNYATAAN BERTANGGUNG JAWAB ATAS KODE AKSES TRANSAKSI NASABAH (PERSONAL ACCESS PASSWORD)",
                        "PERNYATAAN BAHWA DANA YANG DIGUNAKAN SEBAGAI MARGIN MERUPAKAN DANA MILIK NASABAH SENDIRI",
                    ];
                    ?>
                        
                    <?php foreach($arrayList as $key => $val) : ?>
                        <tr>
                            <td width="6%" class="text-center"><?= $key + 1 ?></td>
                            <td class="text-start fw-bold"><?= $val ?></td>
                            <td width="10%" class="text-center"><div style="font-family: DejaVu Sans, sans-serif;">✔</div></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <p style="text-align:justify;">Dengan mengisi kolom "YA" dibawah ini, saya menyatakan bahwa saya telah membaca dan memahami seluruh isi document yg disampaikan dalm FORMULIR NOMOR 1 sampai dengan FORMULIR NOMOR <?= $key ?>.</p>
            <p style="text-align:justify;">Demikian pernyataan ini dibuat dengan sebenarnya dalam keadaan sadar, sehat jasmani dan rohani serta tanpa paksaan apapun dari pihak manapun</p>
            <p>Pernyataan menerima/tidak: Ya</p>
            <p>Menerima pada Tanggal (DD/MM/YYYY): <?= date('Y-m-d', strtotime($realAccount["ACC_F_PROFILE_DATE"])) ?></p>
            <p>IP Address: <?= $realAccount["ACC_F_PROFILE_IP"] ?></p>
        </div>
    </body>
</html>