<?php

$tanggalLahir = ($progressAccount['ACC_TANGGAL_LAHIR'] ?? $user['MBR_TGLLAHIR']);

?>
<div class="row">
    <div class="col-md-9 mx-auto mb-3">
        <form method="post" enctype="multipart/form-data" id="form-pernyataan-simulasi">
            <input type="hidden" name="csrf_token" value="<?= uniqid(); ?>">
            <div class="card">
                <div class="card-body">
                    <div class="text-center"><h5>FORMULIR PERNYATAAN TELAH MELAKUKAN SIMULASI PERDAGANGAN BERJANGKA KOMODITI</h5></div>
                    <hr>
                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive">
                                <p>Yang mengisi formulir di bawah ini :</p>
                                <table class="table table-hover" style="text-align: left; table-layout: fixed;" width="100%">
                                    <tbody>
                                        <tr>
                                            <td width="20%" class="top-align fw-bold">Nama Lengkap</td>
                                            <td width="3%" class="top-align"> : </td>
                                            <td class="top-align text-start">
                                                <input type="text" autocomplete="off" placeholder="Nama Lengkap" name="smls_namleng" value="<?php echo $realAccount['ACC_FULLNAME'] ?? $user['MBR_NAME']; ?>" class="form-control" required>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="20%" class="top-align fw-bold">Tempat Lahir</td>
                                            <td width="3%" class="top-align"> : </td>
                                            <td class="top-align text-start">
                                                <input type="text" autocomplete="off" placeholder="Tempat Lahir" name="smls_tmptlhr" value="<?php echo $realAccount['ACC_TEMPAT_LAHIR'] ?? $user['MBR_TMPTLAHIR'] ?>" class="form-control" required>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="20%" class="top-align fw-bold">Tanggal Lahir</td>
                                            <td width="3%" class="top-align"> : </td>
                                            <td class="top-align text-start">
                                                <input type="date" max="<?php echo date("Y-12-t", strtotime("-17 years")) ?>" name="smls_tgllhr" value="<?php echo (!empty($tanggalLahir)) ? date("Y-m-d", strtotime($tanggalLahir)) : NULL ?>" class="form-control" required>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="20%" class="top-align fw-bold">Alamat Rumah</td>
                                            <td width="3%" class="top-align"> : </td>
                                            <td class="top-align text-start">
                                                <input type="text" autocomplete="off" placeholder="Alamat Rumah" name="smls_almtrmh" value="<?php echo $realAccount['ACC_ADDRESS'] ?? $user['MBR_ADDRESS'] ?>" class="form-control" required>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="20%" class="top-align fw-bold">Provinsi</td>
                                            <td width="3%" class="top-align"> : </td>
                                            <td class="top-align text-start">
                                                <select class="form-control" name="smls_almtrmh_prov" id="smls_almtrmh_prov" required>
                                                    <option value disabled selected>Select</option>
                                                    <?php
                                                        $SQL_PROVINCE = mysqli_query($db, '
                                                            SELECT
                                                                tb_kodepos.KDP_PROV
                                                            FROM tb_kodepos
                                                            GROUP BY tb_kodepos.KDP_PROV
                                                            ORDER BY tb_kodepos.KDP_PROV
                                                        ');
                                                        if($SQL_PROVINCE && mysqli_num_rows($SQL_PROVINCE) > 0){
                                                            foreach(mysqli_fetch_all($SQL_PROVINCE, MYSQLI_ASSOC) as $RSLT_PROVINCE){
                                                    ?>
                                                        <option value="<?= base64_encode($RSLT_PROVINCE["KDP_PROV"]) ?>" <?= (($realAccount['ACC_PROVINCE'] ?? $user['MBR_PROVINCE']) == $RSLT_PROVINCE["KDP_PROV"])? "selected" : ""; ?>><?= $RSLT_PROVINCE["KDP_PROV"] ?></option>
                                                    <?php
                                                            }
                                                        }
                                                    ?>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="20%" class="top-align fw-bold">Kabupaten/Kota</td>
                                            <td width="3%" class="top-align"> : </td>
                                            <td class="top-align text-start">
                                                <select class="form-control" name="smls_almtrmh_kabkot" id="smls_almtrmh_kabkot" required>
                                                    <option value disabled selected>Select</option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="20%" class="top-align fw-bold">Kecamatan</td>
                                            <td width="3%" class="top-align"> : </td>
                                            <td class="top-align text-start">
                                                <select class="form-control" name="smls_almtrmh_kcmtn" id="smls_almtrmh_kcmtn" required>
                                                    <option value disabled selected>Select</option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="20%" class="top-align fw-bold">Desa</td>
                                            <td width="3%" class="top-align"> : </td>
                                            <td class="top-align text-start">
                                                <select class="form-control" name="smls_almtrmh_desa" id="smls_almtrmh_desa" required>
                                                    <option value disabled selected>Select</option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="20%" class="top-align fw-bold">Kode Pos</td>
                                            <td width="3%" class="top-align"> : </td>
                                            <td class="top-align text-start">
                                                <input type="text" autocomplete="off" placeholder="RW" name="smls_kodepos" id="smls_kodepos" value="<?php echo $realAccount['ACC_ZIPCODE'] ?>" class="form-control" required readonly>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="20%" class="top-align fw-bold">RW</td>
                                            <td width="3%" class="top-align"> : </td>
                                            <td class="top-align text-start">
                                                <input type="text" autocomplete="off" placeholder="RW" name="smls_almtrmh_rw" value="<?php echo $realAccount['ACC_RW'] ?>" class="form-control" required>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="20%" class="top-align fw-bold">RT</td>
                                            <td width="3%" class="top-align"> : </td>
                                            <td class="top-align text-start">
                                                <input type="text" autocomplete="off" placeholder="RT" name="smls_almtrmh_rt" value="<?php echo $realAccount['ACC_RT'] ?>" class="form-control" required>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="20%" class="top-align fw-bold">Tipe Identitas</td>
                                            <td width="3%" class="top-align"> : </td>
                                            <td class="top-align text-start">
                                                <select name="smls_tipeidt" class="form-control form-control-sm" required>
                                                    <option disabled selected value>Pilih Jenis Identitas</option>
                                                    <?php foreach(App\Models\Account::$tipeIdentitas as $type) : ?>
                                                        <option value="<?= $type ?>" <?= ($realAccount['ACC_TYPE_IDT'] == $type ) ? 'selected' : NULL ; ?> ><?= $type ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="20%" class="top-align fw-bold">No. Identitas</td>
                                            <td width="3%" class="top-align"> : </td>
                                            <td class="top-align text-start">
                                                <input type="number" autocomplete="off" placeholder="No. Identitas" name="smls_nomidt" value="<?php echo $realAccount['ACC_NO_IDT'] ?>" class="form-control" required>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="20%" class="top-align fw-bold">No. Demo Acc</td>
                                            <td width="3%" class="top-align"> : </td>
                                            <td class="top-align text-start">
                                                <input type="text" autocomplete="off" value="<?= $demoAccount['ACC_LOGIN'] ?>" class="form-control" readonly>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="20%" class="top-align fw-bold">Demo Account File Upload</td>
                                            <td width="3%" class="top-align"> : </td>
                                            <td class="top-align text-start">
                                                <input type="file" class="dropify" id="smls_demofile" name="smls_demofile" data-allowed-file-extensions="png jpg jpeg" data-default-file="<?= App\Models\FileUpload::awsFile($realAccount['ACC_F_SIMULASI_IMG'] ?? "") ?>">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12 text-center">
                            <p>
                                Dengan mengisi kolom "YA" di bawah ini, saya menyatakan bahwa 
                                saya telah melakukan simulasi bertransaksi di bidang Perdagangan 
                                Berjangka Komoditi pada <strong><?= App\Models\ProfilePerusahaan::get()['PROF_COMPANY_NAME']; ?></strong>, 
                                dan telah memahami tentang tata cara bertransaksi di bidang Perdagangan 
                                Berjangka Komoditi.
                            </p>
                            <p>Demikian Pernyataan ini dibuat dengan sebenarnya dalam keadaan sadar, sehat jasmani dan rohani serta tanpa paksaan apapun dari pihak manapun</p>
                        </div>
                        <div class="col-6 mt-3">
                            Pernyataan menerima/tidak<br>
                            <input type="radio" name="aggree" value="Ya" class="form-check-input radio_css" style="margin-top: 10px;" required <?= !empty($realAccount['ACC_F_SIMULASI'])? "checked" : "" ?>>
                            <label style="top: 0.5rem;position: relative;margin-bottom: 0;vertical-align: top;margin-right:1.5rem;">Ya</label>
                            <input type="radio" name="aggree" value="Tidak" class="form-check-input radio_css" style="margin-top: 10px;" required>
                            <label style="top: 0.5rem;position: relative;margin-bottom: 0;vertical-align: top;margin-right:1.5rem;">Tidak</label>
                        </div>
                        <div class="col-6 mt-3">
                            <div class="text-cemter">Menerima pada Tanggal</div>
                            <input type="text" name="agg_date" readonly required value="<?= retnull("ACC_F_SIMULASI_DATE", date('Y-m-d H:i:s')) ?>" class="form-control text-center mb-3 <?= empty($realAccount['ACC_F_SIMULASI_DATE'])? "realtime-date" : "" ?>">
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
        $('.dropify').dropify();

        /** Get Regency */
        $('#smls_almtrmh_prov').on('change', function() {
            $.post("/ajax/regol/getRegency", {province: $(this).val()}, function(resp) {
                if(resp.success) {
                    let target = $('#smls_almtrmh_kabkot');
                    target.empty();
                    target.append(`<option value="" selected disabled>Select</option>`)
                    $.each(resp.data, (i, val) => {
                        target.append(`<option value="${val.name}" ${(val.selected == true ? "selected" : "")}>${val.name}</option>`)
                    })

                    if(target.find("option:selected").length) {
                        target.change();
                    }
                }
            }, 'json')
        })
        $('#smls_almtrmh_prov').change();
        

        /** Get District */
        $('#smls_almtrmh_kabkot').on('change', function() {
            $.post("/ajax/regol/getDistrict", {regency: $(this).val()}, async function(resp) {
                if(resp.success) {
                    let target = $('#smls_almtrmh_kcmtn');
                    target.empty();
                    target.append(`<option value="" selected disabled>Select</option>`)
                    await $.each(resp.data, (i, val) => {
                        target.append(`<option value="${val.name}" ${(val.selected == true ? "selected" : "")}>${val.name}</option>`);
                    })

                    if(target.find("option:selected").length) {
                        target.change();
                    }
                }
            }, 'json')
        })

        /** Get Villages */
        $('#smls_almtrmh_kcmtn').on('change', function() {
            $.post("/ajax/regol/getVillages", {district: $(this).val()}, async function(resp) {
                if(resp.success) {
                    let target = $('#smls_almtrmh_desa');
                    target.empty();
                    target.append(`<option value="" selected disabled>Select</option>`)
                    await $.each(resp.data, (i, val) => {
                        target.append(`<option value="${val.village}" ${(val.selected == true ? "selected" : "")} data-postalcode="${val.postalCode}">${val.village}</option>`)
                    })

                    if(target.find("option:selected").length) {
                        target.change();
                    }
                }
            }, 'json')
        })

        /** Get Kode Pos */
        $('#smls_almtrmh_desa').on('change', function() {
            $('#smls_kodepos').val( $('#smls_almtrmh_desa option:selected').data('postalcode') );
        })

        $('#form-pernyataan-simulasi').on('submit', function(event) {
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
                url: "/ajax/regol/pernyataanSimulasi",
                type: "POST",
                dataType: "json",
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false
            }).done(function(resp) {
                Swal.fire(resp.alert).then(() => {
                    if(resp.success) {
                        location.href = resp.redirect
                    }
                })
            })
        })
    })
</script>