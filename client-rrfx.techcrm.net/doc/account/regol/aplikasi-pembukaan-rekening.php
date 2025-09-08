<?php

use App\Models\Helper;

$myBanks = App\Models\User::myBank($user['MBR_ID']);
$_SESSION['modal'] = ['create-bank'];
?>
<style>
    .row_dash {
        border-bottom: 1px dashed #ddd;
        padding-bottom: 10px;
        margin-bottom: .5rem !important;
    }
</style>

<form method="post" enctype="multipart/form-data" id="form-aplikasi-pembukaan-rekening">
    <input type="hidden" name="csrf_token" value="<?= uniqid(); ?>">
    <div class="card">
        <div class="card-body">
            <div class="text-center"><h5>APLIKASI PEMBUKAAN REKENING TRANSAKSI SECARA ELEKTRONIK ONLINE</h5></div>
            <hr>
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header text-center">
                            DATA PRIBADI
                        </div>
                        <div class="card-body mb-3">
                            <div class="table-responsive">
                                <table class="table table-fixed table-hover">
                                    <tbody>
                                        <tr>
                                            <td width="30%" class="top-align fw-bold">Nama Lengkap</td>
                                            <td width="3%" class="top-align"> : </td>
                                            <td class="top-align text-start"><?= $realAccount['ACC_FULLNAME'] ?></td>
                                        </tr>
                                        <tr>
                                            <td width="30%" class="top-align fw-bold">Tempat / Tanggal Lahir</td>
                                            <td width="3%" class="top-align"> : </td>
                                            <td class="top-align text-start"><?= $realAccount['ACC_TEMPAT_LAHIR'] ?>, <?= date("Y-m-d", strtotime($realAccount['ACC_TANGGAL_LAHIR'])) ?></td>
                                        </tr>
                                        <tr>
                                            <td width="30%" class="top-align fw-bold">Jenis Identitas</td>
                                            <td width="3%" class="top-align"> : </td>
                                            <td class="top-align text-start"><?= $realAccount['ACC_TYPE_IDT'] ?>, <?= $realAccount['ACC_NO_IDT'] ?></td>
                                        </tr>
                                        <tr>
                                            <td width="30%" class="top-align fw-bold">No. NPWP<?= (!empty(unrequireNPWP())) ? '<span class="text-danger">*</span>' : '' ; ?></td>
                                            <td width="3%" class="top-align"> : </td>
                                            <td class="top-align text-start">
                                                <input type="text" data-kind="npwp" inputmode="numeric" autocomplete="off" placeholder="No. Npwp" name="app_npwp" value="<?= $realAccount['ACC_F_APP_PRIBADI_NPWP'] ?>" class="form-control" <?= unrequireNPWP() ?>>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="30%" class="top-align fw-bold">Jenis kelamin<span class="text-danger">*</span></td>
                                            <td width="3%" class="top-align"> : </td>
                                            <td class="top-align text-start">
                                                <select name="app_gender" id="app_gender" class="form-control">
                                                    <?php foreach(['Laki-laki', 'Perempuan'] as $gender) : ?>
                                                        <option value="<?= $gender ?>" <?= (strtolower($realAccount['ACC_F_APP_PRIBADI_KELAMIN'] ?? $user['MBR_JENIS_KELAMIN'] ?? "") == strtolower($gender))? "selected" : ""; ?>><?= $gender ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="30%" class="top-align fw-bold">Nama Ibu Kandung<span class="text-danger">*</span></td>
                                            <td width="3%" class="top-align"> : </td>
                                            <td class="top-align text-start">
                                                <input type="text" data-kind="nama" inputmode="text" autocomplete="off" placeholder="Nama Ibu Kandung" name="app_nama_ibu" value="<?= $realAccount['ACC_F_APP_PRIBADI_IBU'] ?>" class="form-control" required>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="30%" class="top-align fw-bold">Status Perkawinan<span class="text-danger">*</span></td>
                                            <td width="3%" class="top-align"> : </td>
                                            <td class="top-align text-start">
                                                <select name="app_status_perkawinan" id="app_status_perkawinan" class="form-control">
                                                    <?php foreach(["Tidak Kawin", "Kawin", "Janda", "Duda"] as $status_perkawinan) : ?>
                                                        <option value="<?= $status_perkawinan ?>" <?= (strtolower($realAccount['ACC_F_APP_PRIBADI_STSKAWIN'] ?? "") == strtolower($status_perkawinan))? "selected" : ""; ?>><?= $status_perkawinan ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr id="tr_acc_app_nama_istri">
                                            <td width="30%" class="top-align fw-bold">Nama Istri/Suami</td>
                                            <td width="3%" class="top-align"> : </td>
                                            <td class="top-align text-start">
                                                <input type="text" data-kind="nama" inputmode="text" name="acc_app_nama_istri" class="form-control" id="acc_app_nama_istri" value="<?= $realAccount['ACC_F_APP_PRIBADI_NAMAISTRI']; ?>">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="30%" class="top-align fw-bold">Provinsi</td>
                                            <td width="3%" class="top-align"> : </td>
                                            <td class="top-align text-start"><?= $realAccount['ACC_PROVINCE'] ?></td>
                                        </tr>
                                        <tr>
                                            <td width="30%" class="top-align fw-bold">Kabupaten / Kota</td>
                                            <td width="3%" class="top-align"> : </td>
                                            <td class="top-align text-start"><?= $realAccount['ACC_REGENCY'] ?></td>
                                        </tr>
                                        <tr>
                                            <td width="30%" class="top-align fw-bold">Kecamatan</td>
                                            <td width="3%" class="top-align"> : </td>
                                            <td class="top-align text-start"><?= $realAccount['ACC_DISTRICT'] ?></td>
                                        </tr>
                                        <tr>
                                            <td width="30%" class="top-align fw-bold">Desa</td>
                                            <td width="3%" class="top-align"> : </td>
                                            <td class="top-align text-start"><?= $realAccount['ACC_VILLAGE'] ?></td>
                                        </tr>
                                        <tr>
                                            <td width="30%" class="top-align fw-bold">Kode Pos</td>
                                            <td width="3%" class="top-align"> : </td>
                                            <td class="top-align text-start"><?= $realAccount['ACC_ZIPCODE'] ?></td>
                                        </tr>
                                        <tr>
                                            <td width="30%" class="top-align fw-bold">Alamat</td>
                                            <td width="3%" class="top-align"> : </td>
                                            <td class="top-align text-start"><?= $realAccount['ACC_ADDRESS'] ?></td>
                                        </tr>
                                        <tr>
                                            <td width="30%" class="top-align fw-bold">RT</td>
                                            <td width="3%" class="top-align"> : </td>
                                            <td class="top-align text-start"><?= $realAccount['ACC_RT'] ?></td>
                                        </tr>
                                        <tr>
                                            <td width="30%" class="top-align fw-bold">RW</td>
                                            <td width="3%" class="top-align"> : </td>
                                            <td class="top-align text-start"><?= $realAccount['ACC_RW'] ?></td>
                                        </tr>
                                        <tr>
                                            <td width="30%" class="top-align fw-bold">No. Telp Rumah</td>
                                            <td width="3%" class="top-align"> : </td>
                                            <td class="top-align text-start">
                                                <input type="text" data-kind="phone" inputmode="tel" autocomplete="off" placeholder="No. Telp Rumah" name="app_telepon_rumah" value="<?= $realAccount['ACC_F_APP_PRIBADI_TLP'] ?? 0 ?>" class="form-control">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="30%" class="top-align fw-bold">No. Faksimili Rumah</td>
                                            <td width="3%" class="top-align"> : </td>
                                            <td class="top-align text-start">
                                                <input type="text" data-kind="phone" inputmode="tel" autocomplete="off" placeholder="No. Faksimili Rumah" name="app_faksimili_rumah" value="<?= $realAccount['ACC_F_APP_PRIBADI_FAX'] ?? 0 ?>" class="form-control">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="30%" class="top-align fw-bold">No. Telp Handphone<span class="text-danger">*</span></td>
                                            <td width="3%" class="top-align"> : </td>
                                            <td class="top-align text-start">
                                                <input type="text" data-kind="phone" inputmode="tel" autocomplete="off" placeholder="62xxxxxxxxx" name="app_no_handphone" value="<?= ($realAccount['ACC_F_APP_PRIBADI_HP'] == 0)? $user['MBR_PHONE'] : ($realAccount['ACC_F_APP_PRIBADI_HP'] ?? 0); ?>" class="form-control" required>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="30%" class="top-align fw-bold">Status Kepemilikan Rumah<span class="text-danger">*</span></td>
                                            <td width="3%" class="top-align"> : </td>
                                            <td class="top-align text-start">
                                                <div>
                                                    <select id="app_status_rumah" class="form-control" required>
                                                        <?php foreach(["Pribadi", "Keluarga", "Sewa/Kontrak", "Lainnya"] as $status_rumah) : ?>
                                                            <option value="<?= $status_rumah ?>" <?= (strtolower($realAccount['ACC_F_APP_PRIBADI_STSRMH'] ?? "") == strtolower($status_rumah))? "selected" : ""; ?>><?= $status_rumah ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="mt-2" id="div_app_status_rumah">
                                                    <input type="text" class="form-control" name="app_status_rumah" placeholder="Lainnya..." value="<?= $realAccount['ACC_F_APP_PRIBADI_STSRMH']; ?>">
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="30%" class="top-align fw-bold">Tujuan Pembukaan Rekening<span class="text-danger">*</span></td>
                                            <td width="3%" class="top-align"> : </td>
                                            <td class="top-align text-start">
                                                <div>
                                                    <select id="app_tujuan_pembukaan_rek" class="form-control" required>
                                                        <?php foreach(["Lindungi Nilai", "Gain", "Spekulasi", "Lainnya"] as $tujuan_pembukaan) : ?>
                                                            <option value="<?= $tujuan_pembukaan ?>" <?= (strtolower($realAccount['ACC_F_APP_TUJUANBUKA'] ?? "") == strtolower($tujuan_pembukaan))? "selected" : ""; ?>><?= $tujuan_pembukaan ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="mt-2" id="div_app_tujuan_pembukaan_rek">
                                                    <input type="text" class="form-control" name="app_tujuan_pembukaan_rek" placeholder="Lainnya..." value="<?= $realAccount['ACC_F_APP_TUJUANBUKA']; ?>">
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="30%" class="top-align fw-bold">Pengalaman Investasi<span class="text-danger">*</span></td>
                                            <td width="3%" class="top-align"> : </td>
                                            <td class="top-align text-start">
                                                <select name="app_pengalaman_investasi" id="app_pengalaman_investasi" class="form-control" required>
                                                    <?php foreach(["Ya", "Tidak"] as $pengalaman_investasi) : ?>
                                                        <option value="<?= $pengalaman_investasi ?>" <?= (strtolower($realAccount['ACC_F_APP_PENGINVT'] ?? "") == strtolower($pengalaman_investasi))? "selected" : ""; ?>><?= $pengalaman_investasi ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr id="div_app_pengalaman_investasi">
                                            <td width="30%" class="top-align fw-bold">Bidang Investasi<span class="text-danger">*</span></td>
                                            <td width="3%" class="top-align"> : </td>
                                            <td class="top-align text-start">
                                                <input type="text" name="bidang_investasi" class="form-control" id="bidang_investasi" value="<?= $realAccount['ACC_F_APP_PENGINVT_BIDANG']; ?>">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="top-align text-start" colspan="3">
                                                <div class="d-flex justify-content-between mb-25">
                                                    <div class="form-check">
                                                        <input class="form-check-input" name="app_anggota_berjangka" id="app_anggota_berjangka" type="checkbox" required checked>
                                                        <label class="form-check-label" for="app_anggota_berjangka">
                                                            Saya menyetujui bahwa tidak memiliki anggota keluarga yang<br>bekerja di BAPPEBTI / Bursa Berjangka / Kliring Berjangka
                                                        </label>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="top-align text-start" colspan="3">
                                                <div class="d-flex justify-content-between mb-25">
                                                    <div class="form-check">
                                                        <input class="form-check-input" name="app_pailit" id="app_pailit" type="checkbox" required checked>
                                                        <label class="form-check-label" for="app_pailit">
                                                            Saya menyetujui bahwa tidak dinyatakan pailit oleh Pengadilan
                                                        </label>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-header text-center">
                            REKENING BANK NASABAH UNTUK PENYETORAN DAN PENARIKAN MARGIN
                        </div>
                        <div class="card-body mb-3">
                            <p>
                                Rekening Bank Nasabah Untuk Penyetoran dan Penarikan Margin 
                                (hanya rekening dibawah ini yang dapat Saudara pergunakan untuk lalulintas margin)
                            </p>
                            <?php if(!isset($myBanks[0])){ ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover table-fixed" style="text-align: left; table-layout: fixed;" width="100%">
                                            <tbody>
                                                <tr>
                                                    <td width="30%" class="top-align fw-bold">Nama Bank<span class="text-danger">*</span></td>
                                                    <td width="3%" class="top-align"> : </td>
                                                    <td class="top-align text-start">
                                                        <select name="bank_name1" class="form-control form-select input-sm" required>
                                                            <?php foreach(App\Models\BankList::all() as $bank) : ?>
                                                                <option value="<?= $bank['BANKLST_NAME'] ?>"><?= $bank['BANKLST_NAME'] ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td width="30%" class="top-align fw-bold">Nama Pemilik Rekening<span class="text-danger">*</span></td>
                                                    <td width="3%" class="top-align"> : </td>
                                                    <td class="top-align text-start"><?= $realAccount['ACC_FULLNAME']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td width="30%" class="top-align fw-bold">No. Rekening<span class="text-danger">*</span></td>
                                                    <td width="3%" class="top-align"> : </td>
                                                    <td class="top-align text-start">
                                                        <input type="text" data-kind="bankaccount" inputmode="numeric" autocomplate="off" class="form-control input-sm" name="bank_number1" placeholder="Nomor Rekening" required>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                            <?php
                                }
                                if(!isset($myBanks[1])){
                            ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover" style="text-align: left; table-layout: fixed;" width="100%">
                                            <tbody>
                                                <tr>
                                                    <td width="30%" class="top-align fw-bold">Nama Bank<span class="text-danger">*</span></td>
                                                    <td width="3%" class="top-align"> : </td>
                                                    <td class="top-align text-start">
                                                        <select name="bank_name2" class="form-control form-select input-sm">
                                                            <?php foreach(App\Models\BankList::all() as $bank) : ?>
                                                                <option value="<?= $bank['BANKLST_NAME'] ?>"><?= $bank['BANKLST_NAME'] ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td width="30%" class="top-align fw-bold">Nama Pemilik Rekening<span class="text-danger">*</span></td>
                                                    <td width="3%" class="top-align"> : </td>
                                                    <td class="top-align text-start"><?= $realAccount['ACC_FULLNAME']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td width="30%" class="top-align fw-bold">No. Rekening<span class="text-danger">*</span></td>
                                                    <td width="3%" class="top-align"> : </td>
                                                    <td class="top-align text-start">
                                                        <input type="text" data-kind="bankaccount" inputmode="numeric" autocomplate="off" class="form-control input-sm" name="bank_number2" placeholder="Nomor Rekening">
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                            <?php } ?>
                            <hr>
                            <div class="row">
                                <?php foreach($myBanks as $mbank) : ?>
                                    <div class="col-md-6">
                                        <div class="card h-100 border-primary">
                                            <div class="card-body">
                                                <div class="d-flex flex-column justify-content-between gap-3">
                                                    <div class="bank-info">
                                                        <p class="mb-0 lh-1"><?php echo $mbank['MBANK_HOLDER'] ?></p>
                                                        <small style="font-size: 11px;" class="d-flex flex-column mt-0 font-15 text-upper">
                                                            <i><?php echo $mbank['MBANK_NAME'] ?></i>
                                                        </small>
                                                    </div>
                                                    <div class="mt-auto">
                                                        <div class="float-end font-weight-bold"><?php echo $mbank['MBANK_ACCOUNT'] ?></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header text-center">
                            PIHAK YANG DAPAT DIHUBUNGI DALAM KEADAAN DARURAT
                        </div>
                        <div class="card-body">
                            <small>Dalam keadaan darurat, pihak yang dapat dihubungi</small>
                            <div class="table-responsive">
                                <table class="table table-hover" style="text-align: left; table-layout: fixed;" width="100%">
                                    <tbody>
                                        <tr>
                                            <td width="30%" class="top-align fw-bold">Nama Lengkap<span class="text-danger">*</span></td>
                                            <td width="3%" class="top-align"> : </td>
                                            <td class="top-align text-start">
                                                <input type="text" data-kind="nama" inputmode="text" autocomplete="off" placeholder="Nama Lengkap" name="app_darurat_nama" value="<?= $realAccount['ACC_F_APP_DRRT_NAMA'] ?>" class="form-control" required>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="30%" class="top-align fw-bold">Alamat Rumah<span class="text-danger">*</span></td>
                                            <td width="3%" class="top-align"> : </td>
                                            <td class="top-align text-start">
                                                <input type="text" autocomplete="off" placeholder="Alamat" name="app_darurat_alamat" value="<?= $realAccount['ACC_F_APP_DRRT_ALAMAT'] ?>" class="form-control" required>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="30%" class="top-align fw-bold">Kode Pos<span class="text-danger">*</span></td>
                                            <td width="3%" class="top-align"> : </td>
                                            <td class="top-align text-start">
                                                <input type="text" data-kind="kodepos" inputmode="numeric" autocomplete="off" placeholder="Kode Pos" name="app_darurat_kodepos" value="<?= $realAccount['ACC_F_APP_DRRT_ZIP'] ?>" class="form-control" required>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="30%" class="top-align fw-bold">No. Telepon<span class="text-danger">*</span></td>
                                            <td width="3%" class="top-align"> : </td>
                                            <td class="top-align text-start">
                                                <input type="text" data-kind="phone" inputmode="tel" autocomplete="off" placeholder="No. Telp" name="app_darurat_telepon" value="<?= $realAccount['ACC_F_APP_DRRT_TLP'] ?>" class="form-control" required>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="30%" class="top-align fw-bold">Hubungan dengan anda<span class="text-danger">*</span></td>
                                            <td width="3%" class="top-align"> : </td>
                                            <td class="top-align text-start">
                                                <input type="text" data-kind="nama" inputmode="text" autocomplete="off" placeholder="Hubungan dengan anda" name="app_darurat_hubungan" value="<?= $realAccount['ACC_F_APP_DRRT_HUB'] ?>" class="form-control" required>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="card mt-2">
                        <div class="card-header text-center">
                            PEKERJAAN
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-fixed table-hover" width="100%">
                                    <tbody>
                                        <tr>
                                            <td width="8%" class="top-align fw-bold">Pekerjaan<span class="text-danger">*</span></td>
                                            <td width="1%" class="top-align">:</td>
                                            <td class="top-align text-start">
                                                <div>
                                                    <select id="app_pekerjaan" class="form-control">
                                                        <?php foreach(App\Models\Regol::$listPekerjaan as $pekerjaan) : ?>
                                                            <option value="<?= $pekerjaan ?>" <?= (strtolower($realAccount['ACC_F_APP_KRJ_TYPE'] ?? "") == strtolower($pekerjaan))? "selected" : ""; ?>><?= $pekerjaan ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="mt-2" id="div_app_pekerjaan">
                                                    <input type="text" class="form-control" name="app_pekerjaan" placeholder="Lainnya..." value="<?= $realAccount['ACC_F_APP_KRJ_TYPE']; ?>">
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="8%" class="top-align fw-bold">Nama Perusahaan<span class="text-danger">*</span></td>
                                            <td width="1%" class="top-align"> : </td>
                                            <td class="top-align text-start">
                                                <input type="text" autocomplete="off" placeholder="Nama Perusahaan" name="app_nama_perusahaan" value="<?= $realAccount['ACC_F_APP_KRJ_NAMA'] ?>" class="form-control" required>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="8%" class="top-align fw-bold">Bidang Usaha<span class="text-danger">*</span></td>
                                            <td width="1%" class="top-align"> : </td>
                                            <td class="top-align text-start">
                                                <input type="text" autocomplete="off" placeholder="Bidang Usaha" name="app_bidang_usaha" value="<?= $realAccount['ACC_F_APP_KRJ_BDNG'] ?>" class="form-control" required>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="8%" class="top-align fw-bold">Jabatan<span class="text-danger">*</span></td>
                                            <td width="1%" class="top-align"> : </td>
                                            <td class="top-align text-start">
                                                <input type="text" autocomplete="off" placeholder="Nama Jabatan" name="app_jabatan_pekerjaan" value="<?= $realAccount['ACC_F_APP_KRJ_JBTN'] ?>" class="form-control" required>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="8%" class="top-align fw-bold">Lama Bekerja<span class="text-danger">*</span></td>
                                            <td width="1%" class="top-align"> : </td>
                                            <td class="top-align text-start">
                                                <input type="text" autocomplete="off" placeholder="Contoh: 3 Tahun" name="app_lama_bekerja" value="<?= $realAccount['ACC_F_APP_KRJ_LAMA'] ?>" class="form-control" required>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="8%" class="top-align fw-bold">Lama Bekerja (Kantor Sebelumnya)<span class="text-danger">*</span></td>
                                            <td width="1%" class="top-align"> : </td>
                                            <td class="top-align text-start">
                                                <input type="text" autocomplete="off" placeholder="Contoh: 3 Tahun" name="app_lama_bekerja_sebelumnya" value="<?= $realAccount['ACC_F_APP_KRJ_LAMASBLM'] ?>" class="form-control" required>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="8%" class="top-align fw-bold">Alamat Kantor<span class="text-danger">*</span></td>
                                            <td width="1%" class="top-align"> : </td>
                                            <td class="top-align text-start">
                                                <input type="text" autocomplete="off" placeholder="Alamat Kantor" name="app_alamat_kantor" value="<?= $realAccount['ACC_F_APP_KRJ_ALAMAT'] ?>" class="form-control" required>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="8%" class="top-align fw-bold">Kode Pos</td>
                                            <td width="1%" class="top-align"> : </td>
                                            <td class="top-align text-start">
                                                <input type="text" data-kind="kodepos" inputmode="numeric" autocomplete="off" inputmode="numeric" autocomplete="off" placeholder="Kode Pos" name="app_kodepos_kantor" value="<?= $realAccount['ACC_F_APP_KRJ_ZIP'] ?>" class="form-control">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="8%" class="top-align fw-bold">No. Telp Kantor</td>
                                            <td width="1%" class="top-align"> : </td>
                                            <td class="top-align text-start">
                                                <input type="text" data-kind="phone" inputmode="tel" autocomplete="off" placeholder="No. Telp Kantor" name="app_nomor_kantor" value="<?= $realAccount['ACC_F_APP_KRJ_TLP'] ?>" class="form-control">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="8%" class="top-align fw-bold">No. Faksimili</td>
                                            <td width="1%" class="top-align"> : </td>
                                            <td class="top-align text-start">
                                                <input type="text" data-kind="phone" inputmode="tel" autocomplete="off" placeholder="No. Faksimili" name="app_nomor_fax_kantor" value="<?= $realAccount['ACC_F_APP_KRJ_FAX'] ?>" class="form-control">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="card mt-2">
                        <div class="card-header text-center">
                            DAFTAR KEKAYAAN
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover" style="text-align: left; table-layout: fixed;" width="100%">
                                    <tbody>
                                        <tr>
                                            <td width="8%" class="top-align fw-bold">Penghasilan Per tahun<span class="text-danger">*</span></td>
                                            <td width="1%" class="top-align"> : </td>
                                            <td class="top-align text-start">
                                                <select name="app_penghasilan" id="app_penghasilan" class="form-control">
                                                    <?php foreach(['Antara 100-250 juta', 'Antara 250-500 juta', '> 500 juta'] as $penghasilan) : ?>
                                                        <option value="<?= $penghasilan ?>" <?= (strtolower($realAccount['ACC_F_APP_KEKYAN'] ?? "") == strtolower($penghasilan))? "selected" : ""; ?>><?= $penghasilan ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="8%" class="top-align fw-bold">Lokasi Rumah<span class="text-danger">*</span></td>
                                            <td width="1%" class="top-align"> : </td>
                                            <td class="top-align text-start">
                                                <input type="text" autocomplete="off" placeholder="Lokasi Rumah" name="app_lokasi_rumah" value="<?= $realAccount['ACC_F_APP_KEKYAN_RMHLKS'] ?? "" ?>" class="form-control" required>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="8%" class="top-align fw-bold">Nilai NJOP<span class="text-danger">*</span></td>
                                            <td width="1%" class="top-align"> : </td>
                                            <td class="top-align text-start">
                                                <input type="number" autocomplete="off" placeholder="Nilai NJOP" name="app_nilai_njop" id="app_nilai_njop" value="<?= $realAccount['ACC_F_APP_KEKYAN_NJOP'] ?? "" ?>" class="form-control" required>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="8%" class="top-align fw-bold">Deposit Bank<span class="text-danger">*</span></td>
                                            <td width="1%" class="top-align"> : </td>
                                            <td class="top-align text-start">
                                                <input type="number" autocomplete="off" placeholder="Deposit Bank" name="app_deposit_bank" id="app_deposit_bank" value="<?= $realAccount['ACC_F_APP_KEKYAN_DPST'] ?? "" ?>" class="form-control" required>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="8%" class="top-align fw-bold">Lainnya<span class="text-danger">*</span></td>
                                            <td width="1%" class="top-align"> : </td>
                                            <td class="top-align text-start">
                                                <input type="text" autocomplete="off" placeholder="Lainnya" name="app_kekayaan_lainnya" id="app_kekayaan_lainnya" value="<?= $realAccount['ACC_F_APP_KEKYAN_LAIN'] ?? "" ?>" class="form-control" required>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="8%" class="top-align fw-bold">Jumlah<span class="text-danger">*</span></td>
                                            <td width="1%" class="top-align"> : </td>
                                            <td class="top-align text-start">
                                                <input type="text" autocomplete="off" placeholder="Jumlah" name="app_jumlah" readonly value="IDR <?= Helper::formatCurrency(($realAccount['ACC_F_APP_KEKYAN_NILAI'] ?? 0), 0) ?>" class="form-control" required>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12">
                    <div class="card component-jquery-uploader">
                        <div class="card-header text-center">
                            DOKUMEN YANG DILAMPIRKAN
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="" class="form-label required text-sm" style="font-size: 14px;">Rekening Koran Bank / Tagihan Kartu Kredit</label>
                                    <input type="file" class="dropify" id="app_image_1" name="app_image_1" data-allowed-file-extensions="png jpg jpeg" data-default-file="<?= App\Models\FileUpload::awsFile($realAccount['ACC_F_APP_FILE_IMG'] ?? "") ?>">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="" class="form-label required">Rekening Listrik / Telepon</label>
                                    <input type="file" class="dropify" id="app_image_2" name="app_image_2" data-allowed-file-extensions="png jpg jpeg" data-default-file="<?= App\Models\FileUpload::awsFile($realAccount['ACC_F_APP_FILE_IMG2'] ?? "") ?>">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="" class="form-label required">NPWP</label>
                                    <input type="file" class="dropify" id="app_image_npwp" name="app_image_npwp" data-allowed-file-extensions="png jpg jpeg" data-default-file="<?= App\Models\FileUpload::awsFile($realAccount['ACC_F_APP_FILE_NPWP'] ?? "") ?>">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="" class="form-label">Dokumen Lainnya</label>
                                    <input type="file" class="dropify" id="app_image_3" name="app_image_3" data-allowed-file-extensions="png jpg jpeg" data-default-file="<?= App\Models\FileUpload::awsFile($realAccount['ACC_F_APP_FILE_IMG3'] ?? "") ?>">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="" class="form-label">Dokumen Lainnya</label>
                                    <input type="file" class="dropify" id="app_image_4" name="app_image_4" data-allowed-file-extensions="png jpg jpeg" data-default-file="<?= App\Models\FileUpload::awsFile($realAccount['ACC_F_APP_FILE_IMG4'] ?? "") ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <div class="row mt-3">
                <div class="col-md-12">
                    <div class="text-center fw-bold">
                        PERNYATAAN KEBENARAN DAN TANGGUNG JAWAB
                    </div>
                </div>
            </div>
            <p class="mt-3">
                Dengan mengisi kolom “YA” di bawah ini, saya menyatakan bahwa semua informasi dan
                semua dokumen yang saya lampirkan dalam <strong>APLIKASI PEMBUKAAN REKENING
                TRANSAKSI SECARA ELEKTRONIK ONLINE</strong> adalah benar dan tepat, Saya akan
                bertanggung jawab penuh apabila dikemudian hari terjadi sesuatu hal sehubungan
                dengan ketidakbenaran data yang saya berikan. 
            </p>
            <div class="row mt-3">
                <div class="col-6 mt-3">
                    Pernyataan Kebenaran dan Tanggung Jawab<br>
                    <input type="radio" name="aggree" value="Ya" class="form-check-input radio_css" style="margin-top: 10px;" required <?= !empty($realAccount['ACC_F_APP'])? "checked" : "" ?>>
                    <label style="top: 0.5rem;position: relative;margin-bottom: 0;vertical-align: top;margin-right:1.5rem;">Ya</label>
                    <input type="radio" name="aggree" value="Tidak" class="form-check-input radio_css" style="margin-top: 10px;" required>
                    <label style="top: 0.5rem;position: relative;margin-bottom: 0;vertical-align: top;margin-right:1.5rem;">Tidak</label>
                </div>
                <div class="col-6 mt-3">
                    <div class="text-cemter">Menyatakan pada Tanggal</div>
                    <input type="text" name="agg_date" readonly required value="<?= retnull("ACC_F_APP_DATE", date('Y-m-d H:i:s')) ?>" class="form-control text-center mb-3 <?= empty($realAccount['ACC_F_APP_DATE'])? "realtime-date" : "" ?>">
                </div>
            </div>
        </div>
        <div class="card-footer">
            <div class="d-flex flex-row justify-content-end align-items-center gap-2 mt-25">
                <a href="<?= ($prevPage['page'])? ("/account/create?page=".$prevPage['page']) : "javascript:void(0)"; ?>" class="btn btn-secondary">Previous</a>
                <button type="submit" class="btn btn-primary">Next</button>
            </div>
        </div>
    </div>
</form>

<script>
    (() => {
        // Konfigurasi ringkas: title, pattern, minimal & maksimal KARAKTER (bukan hanya digit)
        const CONFIG = {
            "nama": {
                title: "Nama hanya boleh huruf, spasi, titik, apostrof, dan tanda minus",
                // huruf latin + spasi . ' ’ -
                pattern: "^[A-Za-zÀ-ÖØ-öø-ÿ .,'’\\-]+$",
                min: 2,  
                max: 80
            },
            "bankaccount": { 
                title: "Pastikan nomer bank benar", 
                pattern: "^\\d{10,16}$", 
                min: 10,  
                max: 16 
            },
            "kodepos": { 
                title: "Kode pos harus 5 digit angka", 
                pattern: "^\\d{5}$", 
                min: 5,  
                max: 5 
            },
            "npwp":    { 
                title: "NPWP harus 16 digit angka (tanpa titik/strip)", 
                pattern: "^\\d{16}$", 
                min: 16, 
                max: 16 
            },
            "phone": { 
                title: "Nomor telepon Indonesia diawali +62",
                pattern: "^(?:0\\d{8,12}|\\+62\\d{8,12})$",
                min: 9, 
                max: 15 
            }
        };


        // --- Filter nilai sesuai tipe ---
        function sanitizeByKind(val, kind) {
            if (kind === "nama") {
                // huruf latin + spasi . ' ’ -
                return (val || "").replace(/[^A-Za-zÀ-ÖØ-öø-ÿ .,'’\-]/g, "");
            }
            if (kind === "phone") {
                // angka + opsional satu '+' di depan
                const hasPlusFirst = (val || "").startsWith("+");
                const digitsOnly = (val || "").replace(/\D/g, "");
                return hasPlusFirst ? ("+" + digitsOnly) : digitsOnly;
            }
            // kodepos & npwp: angka saja
            return (val || "").replace(/\D/g, "");
        }

        // Blokir karakter tidak valid saat KETIK (paste dibersihkan di 'input')
        document.addEventListener("beforeinput", (e) => {
            const el = e.target;
            if (!el.matches('input[data-kind]')) return;

            const kind = el.dataset.kind;
            const t = e.inputType;
            const ch = e.data ?? "";

            if (t === "insertText") {
                if (kind === "nama") {
                    // izinkan huruf latin + spasi . ' ’ -
                    if (!/^[A-Za-zÀ-ÖØ-öø-ÿ .,'’\-]$/.test(ch)) e.preventDefault();
                } else if (kind === "phone") {
                    const selStart = el.selectionStart ?? 0;
                    const insertingPlus = ch === "+";
                    const alreadyPlus = el.value.includes("+");
                    const isDigit = /\d/.test(ch);
                    if (insertingPlus) {
                        if (selStart !== 0 || alreadyPlus) e.preventDefault();
                    } else if (!isDigit) {
                        e.preventDefault();
                    }
                } else {
                    // kodepos/npwp -> hanya digit
                    if (!/\d/.test(ch)) e.preventDefault();
                }
            }
        });

        // Terapkan aturan + balon error bawaan browser
        function applyRules(el, { showNow = false } = {}) {
            const kind = el.dataset.kind;
            const cfg = CONFIG[kind];
            if (!cfg) return;

            // sanitize
            const cleaned = sanitizeByKind(el.value, kind);
            if (cleaned !== el.value) el.value = cleaned;

            // atribut validasi
            el.setAttribute("title", cfg.title);
            el.setAttribute("pattern", cfg.pattern);
            el.setAttribute("minlength", String(cfg.min));
            el.setAttribute("maxlength", String(cfg.max));

            // cek validitas ringan untuk pesan cepat
            const val = el.value;
            let msg = "";
            if (val.length === 0) {
                msg = ""; // biarkan required
            } else if (val.length < cfg.min) {
                msg = `Minimal ${cfg.min} karakter.`;
            } else if (val.length > cfg.max) {
                msg = `Maksimal ${cfg.max} karakter.`;
            } else if (!(new RegExp(cfg.pattern).test(val))) {
                msg = cfg.title;
            }
            el.setCustomValidity(msg);

            if (showNow) el.reportValidity();
        }

        // keyup -> tampilkan balon sekarang
        document.addEventListener("keyup", (e) => {
            if (e.target.matches('input[data-kind]')) applyRules(e.target, { showNow: true });
        });

        // input -> handle paste/autofill
        document.addEventListener("input", (e) => {
            if (e.target.matches('input[data-kind]')) applyRules(e.target);
        });

        // init
        window.addEventListener("DOMContentLoaded", () => {
            document.querySelectorAll('input[data-kind]').forEach(el => applyRules(el));
        });
    })();
</script>

<script type="text/javascript">
    $(document).ready(function() {
        $('.dropify').dropify();

        $.each(['#app_nilai_njop', '#app_deposit_bank', '#app_kekayaan_lainnya'], (i, val) => {
            $(val).on('focus keyup', function() {
                let njop = $('#app_nilai_njop').val() || 0;
                let deposit = $('#app_deposit_bank').val() || 0;
                let kekayaan = $('#app_kekayaan_lainnya').val() || 0;

                $('input[name="app_jumlah"]').val(new Intl.NumberFormat("en-US", {style: "currency", currency: "IDR", minimumFractionDigits: 0}).format((parseFloat(njop) + parseFloat(deposit) + parseFloat(kekayaan))))
            })
        })

        $('#form-aplikasi-pembukaan-rekening').on('submit', function(event) {
            event.preventDefault();
            let data = Object.fromEntries(new FormData(this).entries());
            Swal.fire({
                text: "Please wait...",
                allowOutsideClick: false,
                didOpen: function() {
                    Swal.showLoading();
                }
            })

            $.ajax({
                url: "/ajax/regol/aplikasiPembukaanRekening",
                type: "POST",
                dataType: "json",
                data: new FormData(this),
                contentType: false,
                processData: false,
                cache: false
            }).done(function(resp) {
                Swal.fire(resp.alert).then(() => {
                    if(resp.success) {
                        location.href = resp.redirect;
                    }
                })
            })
        })


        $('#app_status_perkawinan').on('change', function() {
            return ($(this).val()?.toLowerCase() != "tidak kawin")
                ? $('#tr_acc_app_nama_istri').show().find('input').attr('required', 'required')
                : $('#tr_acc_app_nama_istri').hide().find('input').removeAttr('required')
        }).change()

        $('#app_status_rumah').on('change', function() {
            return ($(this).val().toLowerCase() == "lainnya")
                ? $('#div_app_status_rumah').show().find('input').attr('required', 'required').val("")
                : $('#div_app_status_rumah').hide().find('input').removeAttr('required').val($(this).val())
        }).change();

        $('#app_tujuan_pembukaan_rek').on('change', function() {
            return ($(this).val().toLowerCase() == "lainnya")
                ? $('#div_app_tujuan_pembukaan_rek').show().find('input').attr('required', 'required').val("")
                : $('#div_app_tujuan_pembukaan_rek').hide().find('input').removeAttr('required').val($(this).val())
        }).change();

        $('#app_pengalaman_investasi').on('change', function() {
            return ($(this).val().toLowerCase() == "ya")
                ? $('#div_app_pengalaman_investasi').show().find('input').attr('required', 'required')
                : $('#div_app_pengalaman_investasi').hide().find('input').removeAttr('required')
        }).change();

        $('#app_pekerjaan').on('change', function() {
            return ($(this).val().toLowerCase() == "lainnya")
                ? $('#div_app_pekerjaan').show().find('input').attr('required', 'required').val("")
                : $('#div_app_pekerjaan').hide().find('input').removeAttr('required').val($(this).val())
        }).change();

        $('#app_dokumen_pendukung').on('change', function() {
            return ($(this).val().toLowerCase() == "lainnya")
                ? $('#div_app_dokumen_pendukung').show().find('input').attr('required', 'required').val("")
                : $('#div_app_dokumen_pendukung').hide().find('input').removeAttr('required').val($(this).val())
        }).change();
    })
</script>