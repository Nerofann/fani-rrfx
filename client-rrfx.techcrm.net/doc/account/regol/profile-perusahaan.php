<?php
use App\Models\ProfilePerusahaan;
$profile = App\Models\ProfilePerusahaan::get(); 
?>

<form method="post" id="form-profile-perusahaan">
    <input type="hidden" name="csrf_token" value="<?= uniqid(); ?>">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="text-center mb-25"><h5>PROFIL PERUSAHAAN PIALANG BERJANGKA</h5></div>
                    <div class="table-responsive">
                        <table class="table table-fixed table-hover">
                            <tr>
                                <td width="20%" class="top-align">Nama</td>
                                <td width="3%" class="top-align">:</td>
                                <td class="top-align text-start"><?= $profile['PROF_COMPANY_NAME'] ?? "-"; ?></td>
                            </tr>
                            <tr>
                                <td width="20%" class="top-align">Alamat</td>
                                <td width="3%" class="top-align">:</td>
                                <td class="top-align text-start"><?= $profile['OFFICE']['OFC_ADDRESS'] ?></td>
                            </tr>
                            <tr>
                                <td width="20%" class="top-align">No. Telepon</td>
                                <td width="3%" class="top-align">:</td>
                                <td class="top-align text-start"><?php echo $profile['OFFICE']['OFC_PHONE'] ?></td>
                            </tr>
                            <tr>
                                <td width="20%" class="top-align">No Fax</td>
                                <td width="3%" class="top-align">:</td>
                                <td class="top-align text-start"><?php echo $profile['OFFICE']['OFC_FAX'] ?></td>
                            </tr>
                            <tr>
                                <td width="20%" class="top-align">E-Mail</td>
                                <td width="3%" class="top-align">:</td>
                                <td class="top-align text-start"><?php echo $profile['OFFICE']['OFC_EMAIL'] ?></td>
                            </tr>
                            <tr>
                                <td width="20%" class="top-align">Home Page</td>
                                <td width="3%" class="top-align">:</td>
                                <td class="top-align text-start"><?php echo $profile['PROF_HOMEPAGE'] ?></td>
                            </tr>
                        </table>
                    </div>
                    <hr>
                    <h6 class="fw-bold mb-2">Susunan Pengurus Perusahaan</h6>
                    <div class="table-responsive">
                        <table class="table table-fixed table-hover">
                            <tbody>
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
                    <h6 class="fw-bold mb-2">Susunan Pemegang Saham Perusahaan</h6>
                    <div class="table-responsive">
                        <table class="table table-fixed table-hover">
                            <tbody>
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
                    <h6 class="fw-bold mb-2">Nomor dan Tanggal Izin Usaha dari Bappebti</h6>
                    <div class="table-responsive">
                        <table class="table table-fixed table-hover">
                            <tbody>
                                <tr>
                                    <td class="top-align text-start"><?= $profile['PROF_NO_IZIN_USAHA'] ?> Tanggal: <?= $profile['PROF_TGL_IZIN_USAHA']; ?></td>
                                </tr>
                            </tbody>    
                        </table>
                    </div>
                    <hr>
                    <h6 class="fw-bold mb-2">Nomor dan Tanggal Keanggotaan Bursa Berjangka</h6>
                    <div class="table-responsive">
                        <table class="table table-fixed table-hover">
                            <tbody>
                                <tr>
                                    <td class="top-align text-start"><?= $profile['PROF_NO_KEANGGOTAAN_BURSA'] ?> Tanggal: <?= $profile['PROF_TGL_KEANGGOTAAN_BURSA']; ?></td>
                                </tr>
                            </tbody>    
                        </table>
                    </div>
                    <hr>
                    <h6 class="fw-bold mb-2">Nomor dan Tanggal Keanggotaan Lembaga Kliring Berjangka</h6>
                    <div class="table-responsive">
                        <table class="table table-fixed table-hover">
                            <tbody>
                                <tr>
                                    <td class="top-align text-start"><?= $profile['PROF_NO_KEANGGOTAAN_LEMBAGA'] ?> Tanggal: <?= $profile['PROF_TGL_KEANGGOTAAN_LEMBAGA']; ?></td>
                                </tr>
                            </tbody>    
                        </table>
                    </div>
                    <hr>
                    <h6 class="fw-bold mb-2">Nomor dan Tanggal Persetujuan sebagai Peserta Sistem Perdagangan Alternatif</h6>
                    <div class="table-responsive">
                        <table class="table table-fixed table-hover">
                            <tbody>
                                <tr>
                                    <td class="top-align text-start"><?= $profile['PROF_NO_PERSETUJUAN_PESERTA'] ?> Tanggal: <?= $profile['PROF_TGL_PERSETUJUAN_PESERTA']; ?></td>
                                </tr>
                            </tbody>    
                        </table>
                    </div>
                    <hr>
                    <h6 class="fw-bold mb-2">Nama Penyelenggara Sistem Perdagangan Alternatif</h6>
                    <div class="table-responsive">
                        <table class="table table-fixed table-hover">
                            <tbody>
                                <tr>
                                    <td class="top-align text-start"><?= $profile['FOREX_SYS'] ?></td>
                                </tr>
                            </tbody>    
                        </table>
                    </div>
                    <hr>
                    <h6 class="fw-bold mb-2">Kontrak Berjangka Yang Diperdagangkan</h6>
                    <div class="table-responsive">
                        <table class="table table-fixed table-hover">
                            <tbody>
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
                    <h6 class="fw-bold mb-2">Kontrak Derivatif Syariah Yang Diperdagangkan</h6>
                    <div class="table-responsive">
                        <table class="table table-fixed table-hover">
                            <tbody>
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
                    <h6 class="fw-bold mb-2">Kontrak Derivatif dalam Sistem Perdagangan Alternatif dengan volume minimum 0,1 (nol koma satu) lot Yang Diperdagangkan</h6>
                    <div class="table-responsive">
                        <table class="table table-fixed table-hover">
                            <tbody>
                                <tr>
                                    <td class="top-align text-start">Kontrak CFD Mata Uang Asing (FOREX) dan Loco Emas (XAU) , Silver (XAG) , Oil (CLSK)</td>
                                </tr>
                            </tbody>    
                        </table>
                    </div>
                    <hr>
                    <h6 class="fw-bold mb-2">Biaya secara rinci yang dibebankan pada Nasabah</h6>
                    <div class="table-responsive">
                        <table class="table table-fixed table-hover">
                            <tbody>
                                <tr>
                                    <td class="top-align text-start">Berdasarkan Jenis produk Komisi $50/lot settled / Interest / Swap / Rollover fee</td>
                                </tr>
                            </tbody>    
                        </table>
                    </div>
                    <hr>
                    <h6 class="fw-bold mb-2">Nomor atau alamat email jika terjadi keluhan</h6>
                    <div class="table-responsive">
                        <table class="table table-fixed table-hover">
                            <tbody>
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
                    <h6 class="fw-bold mb-2">Sarana penyelesaian perselisihan yang dipergunakan apabila terjadi perselisihan</h6>
                    <div class="table-responsive">
                        <table class="table table-fixed table-hover">
                            <tbody>
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
                    <h6 class="fw-bold mb-2">Nama-Nama Wakil Pialang Berjangka yang Bekerja di Perusahaan Pialang Berjangka</h6>
                    <div class="table-responsive">
                        <table class="table table-fixed table-hover">
                            <tbody>
                                <?php $list_wpb_satu = ProfilePerusahaan::list_wpb(-1, 2); ?>
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
                    <h6 class="fw-bold mb-2">Nama-Nama Wakil Pialang Berjangka yang secara khusus ditunjuk oleh Pialang Berjangka untuk melakukan verifikasi dalam rangka penerimaan Nasabah elektronik online</h6>
                    <div class="table-responsive">
                        <table class="table table-fixed table-hover">
                            <tbody>
                                <?php $list_wpb_satu = ProfilePerusahaan::list_wpb(2, 2); ?>
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
                    <h6 class="fw-bold mb-2">Nomor Rekening Terpisah (Segregated Account) Perusahaan Pialang Berjangka</h6>
                    <div class="table-responsive">
                        <table class="table table-fixed table-hover">
                            <tbody>
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
                    </div>

                    <div class="row mt-3">
                        <div class="col-12 text-center">
                            <h5>PERNYATAAN TELAH MEMBACA PROFIL PERUSAHAAN PIALANG BERJANGKA</h5>
                            <p>
                                Dengan mengisi kolom “YA” di bawah ini, saya menyatakan bahwa saya telah membaca dan menerima informasi
                                <strong>PROFIL PERUSAHAAN PIALANG BERJANGKA</strong>, mengerti dan memahami isinya.
                            </p>
                        </div>
                        <div class="col-6 mt-3">
                            Pernyataan menerima/tidak<br>
                            <input type="radio" name="aggree" value="Ya" class="form-check-input radio_css" style="margin-top: 10px;" required <?php echo $realAccount['ACC_F_PROFILE'] ? 'checked' : NULL ?>>
                            <label style="top: 0.5rem;position: relative;margin-bottom: 0;vertical-align: top;margin-right:1.5rem;">Ya</label>
                            <input type="radio" name="aggree" value="Tidak" class="form-check-input radio_css" style="margin-top: 10px;" required>
                            <label style="top: 0.5rem;position: relative;margin-bottom: 0;vertical-align: top;margin-right:1.5rem;">Tidak</label>
                        </div>
                        <div class="col-6 mt-3">
                            <div class="text-cemter">Menerima pada Tanggal</div>
                            <input type="text" name="agg_date" readonly required value="<?= $realAccount['ACC_F_PROFILE_DATE'] ?? date("Y-m-d H:i:s"); ?>" class="form-control text-center mb-3 <?= (empty($realAccount['ACC_F_PROFILE']))? "realtime-date" : ""; ?>">
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
        </div>
    </div>
</form>

<script type="text/javascript">
    $(document).ready(function() {
        $('#form-profile-perusahaan').on('submit', function(event) {
            event.preventDefault();
            let data = Object.fromEntries(new FormData(this).entries());
            Swal.fire({
                text: "Please wait...",
                allowOutsideClick: false,
                didOpen: function() {
                    Swal.showLoading();
                }
            })

            $.post("/ajax/regol/profilePerusahaan", data, function(resp) {
                Swal.fire(resp.alert).then(function() {
                    console.log(resp);
                    if(resp.success) {
                        location.href = resp.redirect
                    }
                })
            }, 'json')
        })
    })
</script>