<?php

use App\Models\Account;
use App\Models\Helper;

try {
    $demoAccount = Account::getDemoAccount($userid);
    $realAccount = Account::getProgressRealAccount($userid);
    $currentPage = Helper::form_input($_GET['page'] ?? "create-demo");

    /** Tidak bisa akses create account saat masih ada progress real account yang seteleh deposit new account */
    if($realAccount && $realAccount['ACC_WPCHECK'] >= 3) {
        die("<script>alert('Akun anda sedang diprosess'); location.href = '/account'; </script>");
    }
    
    function retnull($key, $default = 0){ 
        global $realAccount;
        return ($realAccount[ $key ] ?? $default ?? "");
    }
    
    function unrequireNPWP(){
        global $db, $realAccount;
        return (!empty($realAccount) && in_array(strtolower($realAccount['RTYPE_TYPE']), ["micro", "mikro"]))
            ? "" 
            : "required";
    }

    /** Step */
    $steps = [
        [],
        [
            'title' => "Buat Akun Demo",
            'success' => !empty($demoAccount),
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
            'title' => "Menunggu Konfirmasi Admin",
            'success' => !empty($realAccount['ACC_F_CMPLT']),
            'page' => "selesai",
            'show' => true
        ],
        [
            'title' => "Deposit New Account",
            'success' => !empty($realAccount['ACC_F_CMPLT']),
            'page' => "deposit-new-account",
            'show' => ($realAccount && $realAccount['ACC_STS'] == 1 && $realAccount['ACC_WPCHECK'] >= 1)
        ],
    ];

    /** Pengecekan step selesai / pernah diisi, dan belum pernah diisi */
    $currIndex   = array_search($currentPage, array_column($steps, "page"));
    if($currIndex === FALSE) {
        die("<script>location.href = '/account/create?page=create-demo'; </script>");
    }

    $currIndex  += 1; // Karena array pertama kosong jadi tidak terhitung saat menggunakan array_column
    $nextPage    = $steps[ $currIndex + 1 ] ?? [];
    $prevPage    = $steps[ $currIndex - 1 ] ?? [];


    /** Create Demo Wajib Success */
    if($steps[1]['success'] === FALSE && $currentPage != "create-demo") {
        die("<script>location.href = '/account/create?page=create-demo'; </script>");
    
    } else {
        /** Check Step sebelumnya sudah selesai / belum */
        $prevIndex  = max(1, $currIndex - 1);
        if($steps[ $prevIndex ]['success'] === FALSE) {
            foreach($steps as $key2 => $s) {
                if(empty($s)) {
                    continue;
                }

                if($currentPage == "create-demo") {
                    break;
                }

                if($steps[ $key2 ]['success'] === FALSE) {
                    die("<script>location.href = '/account/create?page=".($steps[ $key2 ]['page'] ?? "create-demo")."'; </script>");
                }
            }
        }
    }

} catch (Exception $e) {
    throw $e;
}
?>

<link rel="stylesheet" href="/assets/css/regol.css">
<div class="row">
    <div class="col-12">
        <div class="panel">
            <div class="mt-2 mb-2 part text-center step-wizard" id="nav-tab" role="tablist">
                <ul class="step-wizard-list">
                    <?php foreach($steps as $step => $info) : ?>
                        <?php if(!empty($info) && $info['show']) : ?>
                            <?php $pageLink = ("/account/create?page=" . $info['page']); ?>
                            <li class="step-wizard-item">
                                <span class="progress-count">
                                    <button type="button" <?= (($steps[ ($step - 1) ]['success'] ?? false) === FALSE)? "disabled" : "onclick='location.href = `{$pageLink}`'" ?> class="btn btn-sm text-dark btn-outline-primary <?= ($currentPage == $info['page'])? "active" : "" ?> <?= ($info['success'])? "done" : "" ?>" id="<?= $info['page'] ?>-tab" aria-selected="false">
                                        <?= $step ?>
                                    </button>
                                </span>
                              <span class="progress-label"><?= $info['title'] ?></span>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="panel-body">
                <?php if($currIndex !== FALSE) : ?>
                    <?php if(file_exists(__DIR__ . "/regol/{$currentPage}.php")) : ?>
                        <?php require (__DIR__ . "/regol/{$currentPage}.php"); ?>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        setInterval(() => {
            let now = new Date();
            let year = now.getFullYear();
            let month = String(now.getMonth() + 1).padStart(2, '0');
            let day = String(now.getDate()).padStart(2, '0');
            let hours = String(now.getHours()).padStart(2, '0');
            let minutes = String(now.getMinutes()).padStart(2, '0');
            let seconds = String(now.getSeconds()).padStart(2, '0');
            
            let formattedDateTime = `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;

            $('.realtime-date').val(formattedDateTime);
        }, 1000)

        $('.amount-formatter').on('keyup', function(evt) {
            $(evt.currentTarget).val( formatter( $(evt.currentTarget).val() ) )
        })

        $('ul.step-wizard-list').animate({
            scrollLeft: $('.progress-count button.active').offset().left - 300
        }, 1000)
    })

    function formatter(angka, prefix = null){
        var number_string = angka.replace(/[^\.\d]/g, '').toString(),
        split   		= number_string.split('.'),
        sisa     		= split[0].length % 3,
        rupiah     		= split[0].substr(0, sisa),
        ribuan     		= split[0].substr(sisa).match(/\d{3}/gi);
        // tambahkan titik jika yang di input sudah menjadi angka ribuan
        if(ribuan){
            separator = sisa ? ',' : '';
            rupiah += separator + ribuan.join(',');
        }

        rupiah = split[1] != undefined ? rupiah + '.' + split[1] : rupiah;
        return prefix == undefined ? rupiah : (rupiah ? prefix + rupiah : '');
    }
</script>