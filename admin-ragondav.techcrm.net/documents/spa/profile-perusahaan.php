<!DOCTYPE html>
<html>
    <head>
        <?php require_once(__DIR__  . "/../style.php"); ?>
    </head>
    <body>
        <?php require_once(__DIR__  . "/../header.php"); ?><hr>
        <div class="row" style="font-size: 14px;">
            <div class="col-md-8 mx-auto">
                <div class="card">
                    <div class="card-body">
                        <div class="text-center" style="margin-bottom:10px;"><strong>PROFIL PERUSAHAAN PIALANG BERJANGKA</strong></div>
                        <div class="table-responsive">
                            <table class="table table-fixed table-hover" style="table-layout: auto">
                                <tr>
                                    <td width="1%" style="white-space: nowrap;vertical-align:top;">Nama</td>
                                    <td width="1%" style="border-right:none;vertical-align:top;">:</td>
                                    <td style="border-left:none;vertical-align:top;"><?= $profile['COMPANY_NAME'] ?? "-"; ?></td>
                                </tr>
                                <tr>
                                    <td width="1%" style="white-space: nowrap;vertical-align:top;">Alamat</td>
                                    <td width="1%" style="border-right:none;vertical-align:top;">:</td>
                                    <td style="border-left:none;vertical-align:top;"><?= $profile['OFFICE']['OFC_ADDRESS'] ?? "-"; ?></td>
                                </tr>
                                <tr>
                                    <td width="1%" style="white-space: nowrap;vertical-align:top;">No. Telepon</td>
                                    <td width="1%" style="border-right:none;vertical-align:top;">:</td>
                                    <td style="border-left:none;vertical-align:top;"><?= $profile['OFFICE']['OFC_PHONE'] ?? "-"; ?></td>
                                </tr>
                                <tr>
                                    <td width="1%" style="white-space: nowrap;vertical-align:top;">Faksimili</td>
                                    <td width="1%" style="border-right:none;vertical-align:top;">:</td>
                                    <td style="border-left:none;vertical-align:top;"><?= $profile['OFFICE']['OFC_FAX'] ?? "-"; ?></td>
                                </tr>
                                <tr>
                                    <td width="1%" style="white-space: nowrap;vertical-align:top;">E-mail</td>
                                    <td width="1%" style="border-right:none;vertical-align:top;">:</td>
                                    <td style="border-left:none;vertical-align:top;"><?= $profile['OFFICE']['OFC_EMAIL'] ?? "-"; ?></td>
                                </tr>
                                <tr>
                                    <td width="1%" style="white-space: nowrap;vertical-align:top;">Home-page</td>
                                    <td width="1%" style="border-right:none;vertical-align:top;">:</td>
                                    <td style="border-left:none;vertical-align:top;"><?= $profile['PROF_HOMEPAGE'] ?? "-"; ?></td>
                                </tr>
                                <tr>
                                    <td colspan="3">
                                        Susunan Pengurus Perusahaan:<br>
                                        <ol style="margin-left:-20px;margin-top:0px;margin-bottom:0px;">
                                            <li>Komisaris Utama : <?= $profile['PROF_KOMISARIS_UTAMA'] ?></li>
                                            <li>Komisaris : <?= $profile['PROF_KOMISARIS'] ?></li>
                                            <li>Direktur Utama : <?= $profile['PROF_DEWAN_DIREKSI'] ?></li>
                                            <li>Direktur Kepatuhan : <?= $profile['PROF_DIREKTUR'] ?></li>
                                            <li>Direktur Operasional : <?= $profile['PROF_OPERATIONAL'] ?></li>
                                        </ol>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3">
                                        Susunan Pemegang Saham Perusahaan:<br>
                                        <ol style="margin-left:-20px;margin-top:0px;margin-bottom:0px;">
                                            <?php foreach(explode(",", $profile['PROF_PEMEGANG_SAHAM']) as $key =>  $pemegangSaham) : ?>
                                                <li><?= $pemegangSaham ?></li>
                                            <?php endforeach; ?>
                                        </ol>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3">
                                        Nomor dan Tanggal Izin Usaha dari Bappebti:<br>
                                        <?= $profile['PROF_NO_IZIN_USAHA'] ?> Tanggal: <?= date('d M Y', strtotime($profile['PROF_TGL_IZIN_USAHA'])) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3">
                                        Nomor dan Tanggal Keanggotaan Bursa Berjangka:<br>
                                        <?= $profile['PROF_NO_KEANGGOTAAN_BURSA'] ?> Tanggal: <?= date('d M Y', strtotime($profile['PROF_TGL_KEANGGOTAAN_BURSA'])) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3">
                                        Nomor dan Tanggal Keanggotaan Lembaga Kliring Berjangka:<br>
                                        <?= $profile['PROF_NO_KEANGGOTAAN_LEMBAGA'] ?> Tanggal: <?= date('d M Y', strtotime($profile['PROF_TGL_KEANGGOTAAN_LEMBAGA'])) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3">
                                        Nomor dan Tanggal Persetujuan sebagai Peserta Sistem Perdagangan Alternatif:<br>
                                        <?= $profile['PROF_NO_PERSETUJUAN_PESERTA'] ?> Tanggal: <?= date('d M Y', strtotime($profile['PROF_TGL_PERSETUJUAN_PESERTA'])) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3">
                                        Nama Penyelenggara Sistem Perdagangan Alternatif<br>
                                        <?= $profile['FOREX_SYS'] ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3">
                                        Kontrak Berjangka Yang Diperdagangkan*):<br>
                                        <ol style="margin-left:-20px;margin-top:0px;margin-bottom:0px;">
                                            <li>Kontrak berjangka Emas ( GOL, GOL 250, GOL 100 )</li>
                                            <li>Kontrak berjangka Kopi ( ACF, RCF )</li>
                                            <li>Kontrak Berjangka Olein ( OLE, OLE 10 )</li>
                                            <li>Kontrak Berjangka Indeks emas ( KBIE )</li>
                                            <li>Kontrak Berjangka Coklat ( CC5 )</li>
                                        </ol>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3">
                                        Kontrak Derivatif Syariah Yang Diperdagangkan*):<br>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3">
                                        Kontrak Derivatif dalam Sistem Perdagangan Alternatif (SPA):<br>
                                        Kontrak CFD Mata Uang Asing (FOREX) dan Loco Emas (XAU) , Silver (XAG) , Oil (CLSK),
                                        Indeks Saham Jepang, Indeks Saham Hongkong, NAS100, DOW, SPX500
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3">
                                        Kontrak Derivatif dalam Sistem Perdagangan Alternatif dengan volume minimum 0,1 (nol koma satu) lot Yang Diperdagangkan*):<br>
                                        Kontrak CFD Mata Uang Asing (FOREX) dan Loco Emas (XAU) , Silver (XAG) , Oil (CLSK)
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3">
                                        Biaya secara rinci yang dibebankan kepada Nasabah:<br>
                                        Berdasarkan Jenis produk Komisi $50/lot settled / Interest / Swap / Rollover fee
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3">
                                        Nomor atau alamat email jika terjadi keluhan:<br>
                                        Email : <?= $profile['PROF_EML_PENGADUAN'] ?><br>
                                        No. Telepon : <?= $profile['OFFICE']['OFC_PHONE'] ?><br>
                                        Faks : <?= $profile['PROF_FAX'] ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3">
                                        Sarana penyelesaian perselisihan yang dipergunakan apabila terjadi perselisihan:<br>
                                        <ol style="margin-left:-20px;margin-top:0px;margin-bottom:0px;">
                                            <li>Secara musyawarah untuk mencapai mufakat antara Para Pihak;</li>
                                            <li>Memanfaatkan sarana penyelesaian perselisihan yang tersedia di Bursa Berjangka (JFX);</li>
                                            <li>Badan Arbitrase Perdagangan Berjangka Komoditi (BAKTI) atau Pengadilan Negeri</li>
                                        </ol>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3">
                                        Nama-Nama Wakil Pialang Berjangka yang bekerja di Perusahaan Pialang Berjangka:<br>
                                        <ol style="margin-left:-20px;margin-top:0px;margin-bottom:0px;">
                                            <?php 
                                                foreach($list_wpb_satu as $wpb){
                                                    foreach($wpb as $w){
                                                        echo "<li>".$w['WPB_NAMA']."</li>";
                                                    }; 
                                                };
                                            ?>
                                        </ol>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3">
                                        Nama-Nama Wakil Pialang Berjangka yang secara khusus ditunjuk oleh Pialang Berjangka untuk melakukan verifikasi dalam rangka penerimaan Nasabah elektronik on- line:<br>
                                        <ol style="margin-left:-20px;margin-top:0px;margin-bottom:0px;">
                                            <?php 
                                                foreach($list_wpb_satu as $wpb){
                                                    foreach($wpb as $w){
                                                        echo "<li>".$w['WPB_NAMA']."</li>";
                                                    }; 
                                                };
                                            ?>
                                        </ol>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3">
                                        Nomor Rekening Terpisah (Segregated Account) Perusahaan Pialang Berjangka:<br>
                                        <?php $sql_get_bankadm = $db->query("SELECT * FROM tb_bankadm"); ?>
                                        <?php if($sql_get_bankadm) : ?>
                                            <?php foreach($sql_get_bankadm->fetch_all(MYSQLI_ASSOC) as $key => $bkadm) : ?>
                                                    
                                                    <?= $bkadm['BKADM_ACCOUNT'] ?> <?= $bkadm['BKADM_NAME'] ?> (<?= $bkadm['BKADM_CURR'] ?>)<br>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <!-- <div class="table-responsive">
                            <table class="table table-fixed table-hover">
                                <tbody>
                                    <tr>
                                        <td colspan="3"><strong>Susunan Pengurus Perusahaan</strong></td>
                                    </tr>
                                    <tr>
                                        <td width="20%" class="top-align">Komisaris Utama</td>
                                        <td width="3%" class="top-align">:</td>
                                        <td class="top-align text-start"><?= $profile['PROF_KOMISARIS_UTAMA'] ?></td>
                                    </tr>
                                    <tr>
                                        <td width="20%" class="top-align">Komisaris</td>
                                        <td width="3%" class="top-align">:</td>
                                        <td class="top-align text-start"><?= $profile['PROF_KOMISARIS'] ?></td>
                                    </tr>
                                    <tr>
                                        <td width="20%" class="top-align">Direktur Utama</td>
                                        <td width="3%" class="top-align">:</td>
                                        <td class="top-align text-start"><?= $profile['PROF_DEWAN_DIREKSI'] ?></td>
                                    </tr>
                                    <tr>
                                        <td width="20%" class="top-align">Direktur Kepatuhan</td>
                                        <td width="3%" class="top-align">:</td>
                                        <td class="top-align text-start"><?= $profile['PROF_DIREKTUR'] ?></td>
                                    </tr>
                                    <tr>
                                        <td width="20%" class="top-align">Direktur Operasional</td>
                                        <td width="3%" class="top-align">:</td>
                                        <td class="top-align text-start"><?= $profile['PROF_OPERATIONAL'] ?></td>
                                    </tr>
                                </tbody>    
                            </table>
                        </div>
                        <hr>
                        <div class="table-responsive">
                            <table class="table table-fixed table-hover">
                                <tbody>
                                    <tr>
                                        <td colspan="2">
                                            <h6 class="fw-bold mb-2">Susunan Pemegang Saham Perusahaan</h6>
                                        </td>
                                    </tr>
                                    <?php foreach(explode(",", $profile['PROF_PEMEGANG_SAHAM']) as $key =>  $pemegangSaham) : ?>
                                        <tr>
                                            <td width="5%" class="top-align"><?= $key + 1; ?>.</td>
                                            <td class="top-align text-start"><?= $pemegangSaham ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>    
                            </table>
                        </div>
                        <hr>
                        <div class="table-responsive">
                            <table class="table table-fixed table-hover">
                                <tbody>
                                    <tr>
                                        <td>
                                            <h6 class="fw-bold mb-2">Nomor dan Tanggal Izin Usaha dari Bappebti</h6>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="top-align text-start"><?= $profile['PROF_NO_IZIN_USAHA'] ?> Tanggal: <?= $profile['PROF_TGL_IZIN_USAHA']; ?></td>
                                    </tr>
                                </tbody>    
                            </table>
                        </div>
                        <hr>
                        <div class="table-responsive">
                            <table class="table table-fixed table-hover">
                                <tbody>
                                    <tr>
                                        <td>
                                            <h6 class="fw-bold mb-2">Nomor dan Tanggal Keanggotaan Bursa Berjangka</h6>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="top-align text-start"><?= $profile['PROF_NO_KEANGGOTAAN_BURSA'] ?> Tanggal: <?= $profile['PROF_TGL_KEANGGOTAAN_BURSA']; ?></td>
                                    </tr>
                                </tbody>    
                            </table>
                        </div>
                        <hr>
                        <div class="table-responsive">
                            <table class="table table-fixed table-hover">
                                <tbody>
                                    <tr>
                                        <td>
                                            <h6 class="fw-bold mb-2">Nomor dan Tanggal Keanggotaan Lembaga Kliring Berjangka</h6>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="top-align text-start"><?= $profile['PROF_NO_KEANGGOTAAN_LEMBAGA'] ?> Tanggal: <?= $profile['PROF_TGL_KEANGGOTAAN_LEMBAGA']; ?></td>
                                    </tr>
                                </tbody>    
                            </table>
                        </div>
                        <hr>
                        <div class="table-responsive">
                            <table class="table table-fixed table-hover">
                                <tbody>
                                    <tr>
                                        <td>
                                            <h6 class="fw-bold mb-2">Nomor dan Tanggal Persetujuan sebagai Peserta Sistem Perdagangan Alternatif</h6>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="top-align text-start"><?= $profile['PROF_NO_PERSETUJUAN_PESERTA'] ?> Tanggal: <?= $profile['PROF_TGL_PERSETUJUAN_PESERTA']; ?></td>
                                    </tr>
                                </tbody>    
                            </table>
                        </div>
                        <hr>
                        <div class="table-responsive">
                            <table class="table table-fixed table-hover">
                                <tbody>
                                    <tr>
                                        <td>
                                            <h6 class="fw-bold mb-2">Nama Penyelenggara Sistem Perdagangan Alternatif</h6>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="top-align text-start"><?= $profile['FOREX_SYS'] ?></td>
                                    </tr>
                                </tbody>    
                            </table>
                        </div>
                        <hr>
                        <div class="table-responsive">
                            <table class="table table-fixed table-hover">
                                <tbody>
                                    <tr>
                                        <td colspan="3">
                                            <h6 class="fw-bold mb-2">Kontrak Berjangka Yang Diperdagangkan</h6>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="15%" class="top-align">Kontrak berjangka Emas</td>
                                        <td width="3%" class="top-align">:</td>
                                        <td class="top-align text-start">( GOL, GOL 250, GOL 100 )</td>
                                    </tr>
                                    <tr>
                                        <td width="15%" class="top-align">Kontrak berjangka Kopi</td>
                                        <td width="3%" class="top-align">:</td>
                                        <td class="top-align text-start">( ACF, RCF )</td>
                                    </tr>
                                    <tr>
                                        <td width="15%" class="top-align">Kontrak Berjangka Olein</td>
                                        <td width="3%" class="top-align">:</td>
                                        <td class="top-align text-start">( OLE, OLE 10 )</td>
                                    </tr>
                                    <tr>
                                        <td width="15%" class="top-align">Kontrak Berjangka Indeks emas</td>
                                        <td width="3%" class="top-align">:</td>
                                        <td class="top-align text-start">( KBIE )</td>
                                    </tr>
                                    <tr>
                                        <td width="15%" class="top-align">Kontrak Berjangka Coklat</td>
                                        <td width="3%" class="top-align">:</td>
                                        <td class="top-align text-start">( CC5 )</td>
                                    </tr>
                                </tbody>    
                            </table>
                        </div>
                        <hr>
                        <div class="table-responsive">
                            <table class="table table-fixed table-hover">
                                <tbody>
                                    <tr>
                                        <td>
                                            <h6 class="fw-bold mb-2">Kontrak Derivatif Syariah Yang Diperdagangkan</h6>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="top-align text-start">Kontrak Derivatif dalam Sistem Perdagangan Alternatif (SPA)</td>
                                    </tr>
                                    <tr>
                                        <td class="top-align text-start">Kontrak CFD Mata Uang Asing (FOREX) dan Loco Emas (XAU) , Silver (XAG) , Oil (CLSK)</td>
                                    </tr>
                                    <tr>
                                        <td class="top-align text-start">Indeks Saham Jepang, Indeks Saham Hongkong, NAS100, DOW, SPX500</td>
                                    </tr>
                                </tbody>    
                            </table>
                        </div>
                        <hr>
                        <div class="table-responsive">
                            <table class="table table-fixed table-hover">
                                <tbody>
                                    <tr>
                                        <td>
                                            <h6 class="fw-bold mb-2">Kontrak Derivatif dalam Sistem Perdagangan Alternatif dengan volume minimum 0,1 (nol koma satu) lot Yang Diperdagangkan</h6>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="top-align text-start">Kontrak CFD Mata Uang Asing (FOREX) dan Loco Emas (XAU) , Silver (XAG) , Oil (CLSK)</td>
                                    </tr>
                                </tbody>    
                            </table>
                        </div>
                        <hr>
                        <div class="table-responsive">
                            <table class="table table-fixed table-hover">
                                <tbody>
                                    <tr>
                                        <td>
                                            <h6 class="fw-bold mb-2">Biaya secara rinci yang dibebankan pada Nasabah</h6>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="top-align text-start">Berdasarkan Jenis produk Komisi $50/lot settled / Interest / Swap / Rollover fee</td>
                                    </tr>
                                </tbody>    
                            </table>
                        </div>
                        <hr>
                        <div class="table-responsive">
                            <table class="table table-fixed table-hover">
                                <tbody>
                                    <tr>
                                        <td colspan="3">
                                            <h6 class="fw-bold mb-2">Nomor atau alamat email jika terjadi keluhan</h6>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="15%" class="top-align">Email</td>
                                        <td width="3%" class="top-align">:</td>
                                        <td class="top-align text-start"><?= $profile['PROF_EML_PENGADUAN'] ?></td>
                                    </tr>
                                    <tr>
                                        <td width="15%" class="top-align">No. Telepon</td>
                                        <td width="3%" class="top-align">:</td>
                                        <td class="top-align text-start"><?= $profile['OFFICE']['OFC_PHONE'] ?></td>
                                    </tr>
                                    <tr>
                                        <td width="15%" class="top-align">Fax</td>
                                        <td width="3%" class="top-align">:</td>
                                        <td class="top-align text-start"><?= $profile['PROF_FAX'] ?></td>
                                    </tr>
                                </tbody>    
                            </table>
                        </div>
                        <hr>
                        <div class="table-responsive">
                            <table class="table table-fixed table-hover">
                                <tbody>
                                    <tr>
                                        <td colspan="2">
                                            <h6 class="fw-bold mb-2">Sarana penyelesaian perselisihan yang dipergunakan apabila terjadi perselisihan</h6>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="5%">1.</td>
                                        <td class="top-align text-start">Secara musyawarah untuk mencapai mufakat antara Para Pihak</td>
                                    </tr>
                                    <tr>
                                        <td width="5%">2.</td>
                                        <td class="top-align text-start">Memanfaatkan sarana penyelesaian perselisihan yang tersedia di Bursa Berjangka (JFX)</td>
                                    </tr>
                                    <tr>
                                        <td width="5%">3.</td>
                                        <td class="top-align text-start">Badan Arbitrase Perdagangan Berjangka Komoditi (BAKTI) atau Pengadilan Negeri</td>
                                    </tr>
                                </tbody>    
                            </table>
                        </div>
                        <hr>
                        <div class="table-responsive">
                            <table class="table table-fixed table-hover">
                                <tbody>
                                    <tr>
                                        <td>
                                            <h6 class="fw-bold mb-2">Nama-Nama Wakil Pialang Berjangka yang Bekerja di Perusahaan Pialang Berjangka</h6>
                                        </td>
                                    </tr>
                                    <?php foreach($list_wpb_satu as $wpb) : ?>
                                        <tr>
                                            <?php foreach($wpb as $w) : ?>
                                                <td width="30%" class="top-align text-start"><?php echo $w['WPB_NAMA'] ?></td>
                                            <?php endforeach; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>    
                            </table>
                        </div>
                        <hr>
                        <div class="table-responsive">
                            <table class="table table-fixed table-hover">
                                <tbody>
                                    <tr>
                                        <td>
                                            <h6 class="fw-bold mb-2">Nama-Nama Wakil Pialang Berjangka yang secara khusus ditunjuk oleh Pialang Berjangka untuk melakukan verifikasi dalam rangka penerimaan Nasabah elektronik online</h6>
                                        </td>
                                    </tr>
                                    <?php foreach($list_wpb_satu as $wpb) : ?>
                                        <tr>
                                            <?php foreach($wpb as $w) : ?>
                                                <td width="30%" class="top-align text-start"><?php echo $w['WPB_NAMA'] ?></td>
                                            <?php endforeach; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>    
                            </table>
                        </div>
                        <hr>
                        <div class="table-responsive">
                            <table class="table table-fixed table-hover">
                                <tbody>
                                    <tr>
                                        <td colspan="3">
                                            <h6 class="fw-bold mb-2">Nomor Rekening Terpisah (Segregated Account) Perusahaan Pialang Berjangka</h6>
                                        </td>
                                    </tr>
                                    <?php $sql_get_bankadm = $db->query("SELECT * FROM tb_bankadm"); ?>
                                    <?php if($sql_get_bankadm) : ?>
                                        <?php foreach($sql_get_bankadm->fetch_all(MYSQLI_ASSOC) as $key => $bkadm) : ?>
                                            <tr>
                                                <td width="5%"><?= $key + 1; ?>.</td>
                                                <td class="top-align text-start"><?= $bkadm['BKADM_NAME'] ?></td>
                                                <td class="top-align text-start"><?= $bkadm['BKADM_ACCOUNT'] ?> (<?= $bkadm['BKADM_CURR'] ?>)</td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>    
                            </table>
                        </div> -->

                        <p>*) Isi sesuai dengan kontrak yang diperdagangkan</p>
                        <p style="text-align: justify;">
                            PERNYATAAN TELAH MEMBACA PROFIL PERUSAHAAN PIALANG BERJANGKA<br>
                            Dengan mengisi kolom “YA” di bawah ini, saya menyatakan bahwa saya telah membaca dan menerima informasi
                            PROFIL PERUSAHAAN PIALANG BERJANGKA, mengerti dan memahami isinya.
                        </p>
                        <p>Pernyataan menerima/tidak: Ya</p>
                        <p>Menerima pada Tanggal (DD/MM/YYYY): <?= date('Y-m-d', strtotime($realAccount["ACC_F_PROFILE_DATE"])) ?></p>
                        <p>IP Address: <?= $realAccount["ACC_F_PROFILE_IP"] ?></p>
                        <!-- <div style="text-align:center;margin-top:25px;margin-left:25%">
                            <table>
                                <tr>
                                    <td>Pernyataan menerima/tidak</td>
                                    <td style="vertical-align: top;"><div style="margin:0px 5px;">:</div></td>
                                    <td><strong>YA</strong></td>
                                </tr>
                                <tr>
                                    <td>Menerima pada Tanggal (DD/MM/YYYY): </td>
                                    <td style="vertical-align: top;"><div style="margin:0px 5px;">:</div></td>
                                    <td><strong><?= date('Y-m-d H:i:s', strtotime($realAccount["ACC_F_PROFILE_DATE"])) ?></strong></td>
                                </tr>
                                <tr>
                                    <td>IP Address</td>
                                    <td style="vertical-align: top;"><div style="margin:0px 5px;">:</div></td>
                                    <td><strong><?= $realAccount["ACC_F_PROFILE_IP"] ?></strong></td>
                                </tr>
                            </table>
                        </div> -->
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>