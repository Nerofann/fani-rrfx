<?php

use App\Models\Account;
use App\Models\Helper;
use App\Models\ProfilePerusahaan;

if(empty($_GET['id'])) {
    die("<script>alert('Invalid ID'); location.href = '/account';</script>");
}

$id_acc = Helper::form_input($_GET['id'] ?? "-");
$account = Account::realAccountDetail($id_acc);
$profile = ProfilePerusahaan::get();
if(empty($account)) {
    die("<script>alert('Invalid Account'); location.href = '/account';</script>");
}
?>
<style>
    .top_aling {
        vertical-align: top !important;
    }
    .white_left {
        white-space: normal !important;
        text-align: left !important;
    }
</style>
<div class="dashboard-breadcrumb mb-25">
    <h2>Document</h2>
</div>
<div class="row">
    <div class="col-12">
        <div class="panel">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-2">Nomor : 107.PBK.01</div>
                        <div class="col-md-8">
                            <strong>Profile Perusahaan</strong><br>
                            <small><?= $profile['PROF_COMPANY_NAME']?> adalah Perusahaan Pialang yang bergerak di bidang perdagangan kontrak derivatif komoditi, Indeks Saham dan Foreign Exchange.</small>
                        </div>
                        <div class="col-md-2 text-center"><a href="/export/profile-perusahaan?acc=<?php echo md5(md5($account['ID_ACC'])) ?>" target="_blank" class="btn btn-primary btn-sm"><i class="fa fa-eye"></i> View</a></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-2">Nomor : 107.PBK.02.1</div>
                        <div class="col-md-8">
                            <strong>Pernyataan Telah Melakukan Simulasi perdagangan berjangka komoditi</strong><br>
                            <small>Calon Nasabah diwajibkan untuk memiliki demo account <?= $profile['PROF_COMPANY_NAME']?> sebagai sarana untuk melakukan simulasi transaksi di <?= $profile['PROF_COMPANY_NAME']?>.</small>
                        </div>
                        <div class="col-md-2 text-center"><a href="/export/pernyataan-simulasi?acc=<?php echo md5(md5($account['ID_ACC'])) ?>" target="_blank" class="btn btn-primary btn-sm"><i class="fa fa-eye"></i> View</a></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-2">Nomor : 107.PBK.02.2</div>
                        <div class="col-md-8">
                            <strong>Pernyataan telah berpengalaman melaksanakan transaksi perdagangan berjangka komoditi</strong><br>
                            <small>Dalam hal calon nasabah telah berpengalaman dalam melaksanakan transaksi dalam Perdagangan Berjangka Komoditi, Nasabah memberikan pernyataan dengan Surat Pernyataan Telah Berpengalaman Melaksanakan Transaksi Perdagangan Berjangka Komoditi.</small>
                        </div>
                        <div class="col-md-2 text-center"><a href="/export/pernyataan-pengalaman?acc=<?php echo md5(md5($account['ID_ACC'])) ?>" target="_blank" class="btn btn-primary btn-sm"><i class="fa fa-eye"></i> View</a></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-2">Nomor : 107.PBK.03</div>
                        <div class="col-md-8">
                            <strong>Aplikasi Pembukaan Rekening Transaksi secara Elektronik On-line</strong><br>
                            <small>Seluruh data isian dalam Aplikasi Pembukaan Rekening Transaksi Secara Elektronik On-line Dalam Sistem Perdagangan Alternatif wajib di isi sendiri oleh Nasabah, dan Nasabah bertanggung jawab atas kebenaran informasi yang diberikan dalam mengisi dokumen ini.</small>
                        </div>
                        <div class="col-md-2 text-center"><a href="/export/aplikasi-pembukaan-rekening?acc=<?php echo md5(md5($account['ID_ACC'])) ?>" target="_blank" class="btn btn-primary btn-sm"><i class="fa fa-eye"></i> View</a></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-2">Nomor : 107.PBK.04.1</div>
                        <div class="col-md-8">
                            <strong>Document pemberitahuan adanya resiko</strong><br>
                            <small>Maksud dokumen ini adalah memberitahukan bahwa kemungkinan kerugian atau keuntungan dalam perdagangan Kontrak Berjangka bisa mencapai jumlah yang sangat besar. Oleh karena itu, Anda harus berhati-hati dalam memutuskan untuk melakukan transaksi, apakah kondisi keuangan Anda mencukupi.</small>
                        </div>
                        <div class="col-md-2 text-center"><a href="/export/pemberitahuan-adanya-risiko?acc=<?php echo md5(md5($account['ID_ACC'])) ?>" target="_blank" class="btn btn-primary btn-sm"><i class="fa fa-eye"></i> View</a></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-2">Nomor : 107.PBK.05.1</div>
                        <div class="col-md-8">
                            <strong>Perjanjian pemberian amanat secara elektronik on-line untuk transaksi kontrak berjangka</strong><br>
                            <small>Perjanjian kontrak berjangka dan sepakat untuk mengadakan Perjanjian Pemberian Amanat untuk melakukan transaksi penjualan maupun pembelian Kontrak.</small>
                        </div>
                        <div class="col-md-2 text-center"><a href="/export/perjanjian-pemberian-amanat?acc=<?php echo md5(md5($account['ID_ACC'])) ?>" target="_blank" class="btn btn-primary btn-sm"><i class="fa fa-eye"></i> View</a></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-2">Nomor : 107.PBK.06</div>
                        <div class="col-md-8">
                            <strong>Peraturan Perdagangan (Trading Rules)</strong><br>
                            <small>Peraturan Perdagangan (Trading Rules) dalam siste, aplikasi penerimaan nasabah secara elektronik On-Line.</small>
                        </div>
                        <div class="col-md-2 text-center"><a href="/export/trading-rules?acc=<?php echo md5(md5($account['ID_ACC'])) ?>" target="_blank" class="btn btn-primary btn-sm"><i class="fa fa-eye"></i> View</a></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-2">Nomor : 107.PBK.07</div>
                        <div class="col-md-8">
                            <strong>Pernyataan bertanggung jawab</strong><br>
                            <small>Pernyataan bertanggung jawab atas kode akses transaksi nasabah(Personal Access Password).</small>
                        </div>
                        <div class="col-md-2 text-center"><a href="/export/personal-access-password?acc=<?php echo md5(md5($account['ID_ACC'])) ?>" target="_blank" class="btn btn-primary btn-sm"><i class="fa fa-eye"></i> View</a></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-2">-</div>
                        <div class="col-md-8">
                            <strong>Formulir Penyataan Dana Nasabah</strong><br>
                            <small>Pernyataan bahwa dana yang digunakan sebagai margin merupakan dana milik nasabah sendiri.</small>
                        </div>
                        <div class="col-md-2 text-center"><a href="/export/pernyataan-dana-nasabah?acc=<?php echo md5(md5($account['ID_ACC'])) ?>" target="_blank" class="btn btn-primary btn-sm"><i class="fa fa-eye"></i> View</a></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-2">-</div>
                        <div class="col-md-8">
                            <strong>Surat Pernyataan</strong><br>
                            <small>Proses Penerimaan Nasabah Secara Elektronik Online.</small>
                        </div>
                        <div class="col-md-2 text-center"><a href="/export/surat-pernyataan?acc=<?php echo md5(md5($account['ID_ACC'])) ?>" target="_blank" class="btn btn-primary btn-sm"><i class="fa fa-eye"></i> View</a></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-2">-</div>
                        <div class="col-md-8">
                            <strong>Formulir Verifikasi Kelengkapan</strong><br>
                            <small>Proses Penerimaan Nasabah Secara Elektronik Online.</small>
                        </div>
                        <div class="col-md-2 text-center"><a href="/export/kelengkapan-formulir?acc=<?php echo md5(md5($account['ID_ACC'])) ?>" target="_blank" class="btn btn-primary btn-sm"><i class="fa fa-eye"></i> View</a></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-2">-</div>
                        <div class="col-md-8">
                            <strong>All Document</strong><br>
                        </div>
                        <div class="col-md-2 text-center"><a href="/export/all?acc=<?php echo md5(md5($account['ID_ACC'])) ?>" target="_blank" class="btn btn-primary btn-sm"><i class="fa fa-eye"></i> View</a></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>