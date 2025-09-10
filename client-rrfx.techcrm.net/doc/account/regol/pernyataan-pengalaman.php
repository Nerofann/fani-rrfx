<style>
    .light-theme .swal2-popup .swal2-input {
        color: black;
    }

    .dark-theme .swal2-popup .swal2-input {
        color: white;
    }
</style>

<div class="row">
    <div class="col-md-8 mx-auto mb-3">
        <form method="post" id="form-pernyataan-pengalaman">
            <input type="hidden" name="csrf_token" value="<?= uniqid(); ?>">
            <div class="card">
                <div class="card-body">
                    <div class="text-center"><h5>FORMULIR PERNYATAAN TELAH BERPENGALAMAN MELAKSANAKAN TRANSAKSI  PERDAGANGAN BERJANGKA KOMODITI</h5></div>
                    <hr>
                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive">
                                <p>Yang mengisi formulir di bawah ini :</p>
                                <table class="table table-hover" style="text-align: left; table-layout: fixed;" width="100%">
                                    <tr>
                                        <td width="30%" class="top-align fw-bold">Nama Lengkap</td>
                                        <td width="3%" class="top-align"> : </td>
                                        <td class="top-align text-start"><?= $realAccount['ACC_FULLNAME'] ?></td>
                                    </tr>
                                    <tr>
                                        <td width="30%" class="top-align fw-bold">Tempat Lahir</td>
                                        <td width="3%" class="top-align"> : </td>
                                        <td class="top-align text-start"><?= $realAccount['ACC_TEMPAT_LAHIR'] ?></td>
                                    </tr>
                                    <tr>
                                        <td width="30%" class="top-align fw-bold">Tanggal Lahir</td>
                                        <td width="3%" class="top-align"> : </td>
                                        <td class="top-align text-start"><?= $realAccount['ACC_TANGGAL_LAHIR'] ?></td>
                                    </tr>
                                        <tr>
                                            <td width="30%" class="top-align fw-bold">Alamat Rumah</td>
                                            <td width="3%" class="top-align"> : </td>
                                            <td class="top-align text-start"><?= $realAccount['ACC_ADDRESS'] ?></td>
                                        </tr>
                                        <tr>
                                            <td width="30%" class="top-align fw-bold">Provinsi</td>
                                            <td width="3%" class="top-align"> : </td>
                                            <td class="top-align text-start"><?= $realAccount['ACC_PROVINCE'] ?></td>
                                        </tr>
                                        <tr>
                                            <td width="30%" class="top-align fw-bold">Kabupaten/Kota</td>
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
                                            <td width="30%" class="top-align fw-bold">RW</td>
                                            <td width="3%" class="top-align"> : </td>
                                            <td class="top-align text-start"><?= $realAccount['ACC_RW'] ?></td>
                                        </tr>
                                        <tr>
                                            <td width="30%" class="top-align fw-bold">RT</td>
                                            <td width="3%" class="top-align"> : </td>
                                            <td class="top-align text-start"><?= $realAccount['ACC_RT'] ?></td>
                                        </tr>
                                    <tr>
                                        <td width="30%" class="top-align fw-bold">Kode Pos</td>
                                        <td width="3%" class="top-align"> : </td>
                                        <td class="top-align text-start"><?= $realAccount['ACC_ZIPCODE'] ?></td>
                                    </tr>
                                    <tr>
                                        <td width="30%" class="top-align fw-bold">Tipe Identitas</td>
                                        <td width="3%" class="top-align"> : </td>
                                        <td class="top-align text-start"><?= $realAccount['ACC_TYPE_IDT'] ?></td>
                                    </tr>
                                    <tr>
                                        <td width="30%" class="top-align fw-bold">No. Identitas</td>
                                        <td width="3%" class="top-align"> : </td>
                                        <td class="top-align text-start"><?= $realAccount['ACC_NO_IDT'] ?></td>
                                    </tr>
                                    <tr>
                                        <td width="30%" class="top-align fw-bold">No. Demo Acc</td>
                                        <td width="3%" class="top-align"> : </td>
                                        <td class="top-align text-start"><?= $realAccount['ACC_DEMO'] ?></td>
                                    </tr>
                                    <tr>
                                        <td width="30%" class="top-align fw-bold">Pernyataan Pengalaman</td>
                                        <td width="3%" class="top-align"> : </td>
                                        <td class="top-align text-start">
                                            <select name="pengalaman" id="pengalaman" class="form-control w-50">
                                                <option value="Ya" <?= (strtolower($realAccount['ACC_F_PENGLAMAN_PERYT_YA'] ?? "") == "ya")? "selected" : ""; ?>>Ya</option>
                                                <option value="Tidak" <?= (strtolower($realAccount['ACC_F_PENGLAMAN_PERYT_YA'] ?? "") == "tidak")? "selected" : ""; ?>>Tidak</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <?php if(strtolower($realAccount['ACC_F_PENGLAMAN_PERYT_YA'] ?? "") == "ya") : ?>
                                        <tr>
                                            <td width="30%" class="top-align fw-bold">Nama Perusahaan Pialang Berjangka</td>
                                            <td width="3%" class="top-align"> : </td>
                                            <td class="top-align text-start"><?= $realAccount['ACC_F_PENGLAMAN_PERSH'] ?? "-" ?></td>
                                        </tr>
                                    <?php endif; ?>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12 mb-3">
                            <p class="mt-3">
                                Dengan mengisi kolom "YA" di bawah ini, saya menyatakan bahwa saya telah memiliki pengalaman yang mencukupi dalam melaksanakan 
                                transaksi Perdagangan Berjangka karena pernah bertransaksi pada Perusahaan Pialang Berjangka dan telah memahami tentang tata cara bertransaksi Perdagangan Berjangka.
                            </p>
                            <p>Demikian Pernyataan ini dibuat dengan sebenarnya dalam keadaan sadar, sehat jasmani dan rohani serta tanpa paksaan apapun dari pihak manapun.</p>
                        </div>
                        <div class="col-6 mt-3">
                            Pernyataan menerima/tidak<br>
                            <input type="radio" name="aggree" value="Ya" class="form-check-input radio_css" style="margin-top: 10px;" required <?= !empty($realAccount['ACC_F_PENGLAMAN'])? "checked" : "" ?>>
                            <label style="top: 0.5rem;position: relative;margin-bottom: 0;vertical-align: top;margin-right:1.5rem;">Ya</label>
                            <input type="radio" name="aggree" value="Tidak" class="form-check-input radio_css" style="margin-top: 10px;" required>
                            <label style="top: 0.5rem;position: relative;margin-bottom: 0;vertical-align: top;margin-right:1.5rem;">Tidak</label>
                        </div>
                        <div class="col-6 mt-3">
                            <div class="text-cemter">Menerima pada Tanggal</div>
                            <input type="text" name="agg_date" readonly required value="<?= retnull("ACC_F_PENGLAMAN_DATE", date('Y-m-d H:i:s')) ?>" class="form-control text-center mb-3 <?= empty($realAccount['ACC_F_PENGLAMAN_DATE'])? "realtime-date" : "" ?>">
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
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $("#form-pernyataan-pengalaman").on("submit", function(event) {
            event.preventDefault();
            let data = Object.fromEntries(new FormData(this).entries());
            if($("#pengalaman").val()?.toLowerCase() == "ya") {
                Swal.fire({
                    title: "Pernyataan Pengalaman",
                    input: "text",
                    inputLabel: "Perusahaan pialang berjangka",
                    inputValue: "<?= App\Models\ProfilePerusahaan::get()['PROF_COMPANY_NAME']; ?>",
                    customClass: {
                        inputLabel: 'swal2-title fs-6',
                        inputValue: 'swal2-title'
                    },
                    showCancelButton: true,
                    reverseButtons: true,
                    inputValidator: (value) => {
                        if (!value) {
                            return "You need to write something!";
                        }
                    }
                }).then((value) => {
                    if(value.isConfirmed) {
                        data.perusahaan = value.value;
                        Swal.fire({
                            text: "Please wait...",
                            allowOutsideClick: false,
                            didOpen: function() {
                                Swal.showLoading();
                            }
                        })
                        
                        $.post("/ajax/regol/pernyataanPengalaman", data, function(resp) {
                            Swal.fire(resp.alert).then(() => {
                                if(resp.success) {
                                    location.href = resp.redirect
                                }
                            })
                        }, 'json')
                    }
                });
            
            }else {
                Swal.fire({
                    text: "Please wait...",
                    allowOutsideClick: false,
                    didOpen: function() {
                        Swal.showLoading();
                    }
                })
                
                $.post("/ajax/regol/pernyataanPengalaman", data, function(resp) {
                    Swal.fire(resp.alert).then(() => {
                        if(resp.success) {
                            location.href = resp.redirect
                        }
                    })
                }, 'json')
            }
        });
    });
</script>