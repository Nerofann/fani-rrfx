<?php
$realAccount = $classAcc->realAccountDetail(($acc ?? ""));
$pemegangSaham = explode(",", $profile_perusahaan['PROF_PEMEGANG_SAHAM']);
?>

<!DOCTYPE html>
<html>
    <head>
        <?php require_once(__DIR__  . "/style.php"); ?>
    </head>
    <body>
        <div class="header">
            <img style="object-fit: cover; max-height: 100%; max-width: 100%;" src="data:image/png;base64,<?= base64_encode(file_get_contents($logo_pdf)); ?>">
        </div>

        <table class="table">
            <tbody>
                <tr>
                    <td width="8%" class="text-center v-align-middle">No</td>
                    <td><b>Daftar Jenis Kontrak Derivatif Dalam Sistem Perdagangan Alternatif Dengan Volume minimum 0,1 (nol koma satu) Lot Dalam Rangka Penerimaan Nasabah Secara Elektronik Online Di Bidang Perdagangan Berjangka Komoditi</b></td>
                </tr>
                <tr>
                    <td width="8%" class="text-center v-align-middle">1.</td>
                    <td>
                        <b><i>Contract For Difference</i> Indeks Saham:</b>
                        <p style="margin: 0px;">Index Saham Jepang</p>
                        <p style="margin: 0px;">Index Saham Hongkong</p>
                        <p style="margin: 0px;">Index Saham Amerika</p>
                    </td>
                </tr>
                <tr>
                    <td width="8%" class="text-center v-align-middle">2.</td>
                    <td>
                        <b><i>Contract For Difference</i> Mata uang Asing:</b>
                        <p style="margin: 0px;">Forex Major</p>
                        <p style="margin: 0px;">Cross Rate</p>
                    </td>
                </tr>
                <tr>
                    <td width="8%" class="text-center v-align-middle">3.</td>
                    <td>
                        <b><i>Contract For Difference</i> Komoditi:</b>
                        <p style="margin: 0px;">LLG (XAUUSD)</p>
                        <p style="margin: 0px;">Silver (XAGUSD)</p>
                        <p style="margin: 0px;">Crude Oil</p>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="break-before section">
            <h4 class="text-center" style="margin: 0px;">PROFIL PERUSAHAAN PIALANG BERJANGKA</h4>
            <table class="table" style="margin-top: 10px;">
                <tbody>
                    <tr>
                        <td width="15%" class="v-align-middle">Nama</td>
                        <td><?= $company_name ?></td>
                    </tr>
                    <tr>
                        <td width="18%" class="v-align-middle">Alamat</td>
                        <td><?= $company_address ?></td>
                    </tr>
                    <tr>
                        <td width="18%" class="v-align-middle">No. Telepon</td>
                        <td><?= $profile_perusahaan['OFFICE'][0]['OFC_PHONE'] ?></td>
                    </tr>
                    <tr>
                        <td width="18%" class="v-align-middle">No. Telepon</td>
                        <td><?= $profile_perusahaan['OFFICE'][0]['OFC_FAX'] ?></td>
                    </tr>
                    <tr>
                        <td width="18%" class="v-align-middle">E-mail</td>
                        <td><?= $profile_perusahaan['OFFICE'][0]['OFC_EMAIL'] ?></td>
                    </tr>
                    <tr>
                        <td width="18%" class="v-align-middle">Home-page</td>
                        <td><?= $profile_perusahaan['PROF_HOMEPAGE'] ?></td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            Susunan Pengurus Perusahaan:
                            <p style="margin: 0px;">1. Komisaris Utama: <?= $profile_perusahaan['PROF_KOMISARIS_UTAMA'] ?></p>
                            <p style="margin: 0px;">2. Komisaris: <?= $profile_perusahaan['PROF_KOMISARIS'] ?></p>
                            <p style="margin: 0px;">3. Direktur Utama: <?= $profile_perusahaan['PROF_DEWAN_DIREKSI'] ?></p>
                            <p style="margin: 0px;">4. Direktur Kepatuhan: <?= $profile_perusahaan['PROF_DIREKTUR'] ?></p>
                            <p style="margin: 0px;">5. Direktur Operasional: <?= $profile_perusahaan['PROF_OPERATIONAL'] ?></p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            Susunan Pemegang Saham Perusahaan:
                            <?php foreach($pemegangSaham as $key => $val) : ?>
                                <p style="margin: 0px;"><?= $key ?>. <?= $val ?></p>
                            <?php endforeach; ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            Nomor dan Tanggal Izin Usaha dari Bappebti:
                            <p style="margin: 0px;">
                                <?= $profile_perusahaan['PROF_NO_IZIN_USAHA'] ?> 
                                tanggal 
                                <?= date("d", strtotime($profile_perusahaan['PROF_TGL_IZIN_USAHA'])) ?>
                                <?= $helperClass->bulan($profile_perusahaan['PROF_TGL_IZIN_USAHA']); ?>
                                <?= date("Y", strtotime($profile_perusahaan['PROF_TGL_IZIN_USAHA'])) ?>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            Nomor dan Tanggal Keanggotaan Bursa Berjangka:
                            <p style="margin: 0px;">
                                <?= $profile_perusahaan['PROF_NO_KEANGGOTAAN_BURSA'] ?> 
                                tanggal 
                                <?= date("d", strtotime($profile_perusahaan['PROF_TGL_KEANGGOTAAN_BURSA'])) ?>
                                <?= $helperClass->bulan($profile_perusahaan['PROF_TGL_KEANGGOTAAN_BURSA']); ?>
                                <?= date("Y", strtotime($profile_perusahaan['PROF_TGL_KEANGGOTAAN_BURSA'])) ?>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            Nomor dan Tanggal Keanggotaan Lembaga Kliring Berjangka:
                            <p style="margin: 0px;">
                                <?= $profile_perusahaan['PROF_NO_KEANGGOTAAN_LEMBAGA'] ?> 
                                tanggal 
                                <?= date("d", strtotime($profile_perusahaan['PROF_TGL_KEANGGOTAAN_LEMBAGA'])) ?>
                                <?= $helperClass->bulan($profile_perusahaan['PROF_TGL_KEANGGOTAAN_LEMBAGA']); ?>
                                <?= date("Y", strtotime($profile_perusahaan['PROF_TGL_KEANGGOTAAN_LEMBAGA'])) ?>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            Nomor dan Tanggal Persetujuan sebagai Peserta Sistem Perdagangan Alternatif:
                            <p style="margin: 0px;">
                                <?= $profile_perusahaan['PROF_NO_PERSETUJUAN_PESERTA'] ?> 
                                tanggal 
                                <?= date("d", strtotime($profile_perusahaan['PROF_TGL_PERSETUJUAN_PESERTA'])) ?>
                                <?= $helperClass->bulan($profile_perusahaan['PROF_TGL_PERSETUJUAN_PESERTA']); ?>
                                <?= date("Y", strtotime($profile_perusahaan['PROF_TGL_PERSETUJUAN_PESERTA'])) ?>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            Nama Penyelenggara Sistem Perdagangan Alternatif:
                            <p style="margin: 0px;"><?= $profile_perusahaan['FOREX_SYS'] ?></p>
                        </td>
                    </tr>

                    <?php foreach($profile_perusahaan['PRODUCTS'] as $prd) : ?>
                        <tr>
                            <td colspan="2">
                                <?= $prd['name'] ?>
                                <?php if($prd['type'] != "sub") : ?>
                                    <ol>
                                        <?php foreach($prd['detail'] as $detail) : ?>
                                            <li><?= $detail['text'] ?></li>
                                        <?php endforeach; ?>
                                    </ol>
    
                                <?php else : ?>
                                    <ul>
                                        <?php foreach($prd['detail'] as $detail) : ?>
                                            <li><b><?= $detail['text'] ?>: </b><?= implode(", ", $detail['sublist']) ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <tr>
                        <td colspan="2">
                            Nama-Nama Wakil Pialang Berjangka yang bekerja di Perusahaan Pialang Berjangka:
                            <?php foreach($wpb as $key => $arr) : ?>
                                <?php foreach($arr as $key2 => $val) : ?>
                                    <p style="margin: 0px;"><?= $key2 + 1; ?>. <?= $val['WPB_NAMA'] ?></p>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            Nama-Nama Wakil Pialang Berjangka yang secara khusus ditunjuk oleh Pialang Berjangka untuk melakukan verifikasi dalam rangka penerimaan Nasabah elektronik on- line:
                            <table>
                                <tbody>
                                    <tr>
                                        <?php foreach($wpb_verifikator as $key => $arr) : ?>
                                            <td width="20%" class="v-align-middle" style="border: 0px;">
                                                <?php foreach($arr as $key2 => $val) : ?>
                                                    <p style="margin: 0px;"><?= $key2 + 1; ?>. <?= $val['WPB_NAMA'] ?></p>
                                                <?php endforeach; ?>
                                            </td>
                                        <?php endforeach; ?>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            Nomor Rekening Terpisah (Segregated Account) Perusahaan Pialang Berjangka:
                            <?php $sqlGet = $db->query("SELECT * FROM tb_bankadm"); ?>
                            <?php foreach($sqlGet->fetch_all(MYSQLI_ASSOC) as $key => $val) : ?>
                                <p style="margin: 0px;"><?= implode(" / ", [$val['BKADM_ACCOUNT'], $val['BKADM_NAME'], '('.$val['BKADM_CURR'].')']) ?></p>
                            <?php endforeach; ?>
                        </td>
                    </tr>
                </tbody>
            </table>

            <p style="margin-bottom: 0px;">*) Isi sesuai dengan kontrak yang diperdagangkan </p>
            <p>PERNYATAAN TELAH MEMBACA PROFIL PERUSAHAAN PIALANG BERJANGKA Dengan mengisi kolom “YA” di bawah ini, saya menyatakan bahwa saya telah membaca dan menerima informasi PROFIL PERUSAHAAN PIALANG BERJANGKA, mengerti dan memahami isinya.</p>

            <p style="margin: 0px;">Pernyataan menerima/tidak: Ya</p>
            <p style="margin: 0px;">Menerima pada Tanggal (DD/MM/YYYY): <?= date("d/m/Y", strtotime($realAccount['ACC_F_PROFILE_DATE'])) ?></p>
        </div>
    </body>
</html>