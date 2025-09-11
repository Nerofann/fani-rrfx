<?php
$tanggalLahir = ($realAccount['ACC_TANGGAL_LAHIR'] ?? $user['MBR_TGLLAHIR']);

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
                                                <input type="text" name="smls_tgllhr" value="<?php echo (!empty($tanggalLahir)) ? date("m/d/Y", strtotime($tanggalLahir)) : NULL ?>" class="form-control datepicker" required data-max="<?= date("m/d/Y", strtotime("-18 years")) ?>">
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
                                                <input type="text" autocomplete="off" data-kind="npwp" inputmode="numeric" placeholder="No. Identitas" name="smls_nomidt" value="<?php echo $realAccount['ACC_NO_IDT'] ?>" class="form-control" required>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="20%" class="top-align fw-bold">No. Demo Acc</td>
                                            <td width="3%" class="top-align"> : </td>
                                            <td class="top-align text-start">
                                                <input type="text" autocomplete="off" value="<?= $demoAccount['ACC_LOGIN'] ?>" class="form-control" readonly>
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
            "NIK":    { 
                title: "NIK harus 16 digit angka (tanpa titik/strip)", 
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