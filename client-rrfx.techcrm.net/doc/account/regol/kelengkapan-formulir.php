<div class="row">
    <div class="col-md-9 mx-auto">
        <form method="post" id="form-check-kelengkapan">
            <input type="hidden" name="csrf_token" value="<?= uniqid(); ?>">
            <div class="card">
                <div class="card-body">
                    <div class="text-center"><h5>VERIFIKASI KELENGKAPAN PROSES PENERIMAAN NASABAH SECARA ELEKTRONIK ONLINE</h5></div>
                    <hr>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-start">No</th>
                                    <th class="text-start">Prosess</th>
                                    <th class="text-start">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($steps as $key => $st) : ?>
                                    <?php if(!empty($st) && $st['page'] != "selesai") : ?>
                                        <tr>
                                            <td width="6%" class="text-center"><?= $key ?></td>
                                            <td class="text-start fw-bold"><?= $st['title'] ?></td>
                                            <td width="10%" class="text-center"><?= $st['success']? '<i class="fa-solid fa-check text-primary"></i>' : '<i class="fa-solid fa-x text-danger"></i>'; ?></td>
                                        </tr>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                 
                    <div class="row mt-3">
                        <div class="col-12 text-center">
                            <p>
                                Dengan mengisi kolom “YA” dibawah ini, saya menyatakan bahwa saya telah membaca dan memahami seluruh isi dokumen yang disampaikan dalam FORMULIR NOMOR 1 sampai dengan FORMULIR NOMOR 10.
                            </p>
                        </div>
                        <div class="col-md-12 text-center">
                            <p class="mt-3">Demikian Pernyataan ini dibuat dengan sebenarnya dalam keadaan sadar, sehat jasmani dan rohani serta tanpa paksaan apapum dari pihak manapun</p>
                        </div>
                        <div class="col-6 mt-3">
                            Pernyataan menerima/tidak<br>
                            <input type="radio" name="aggree" value="Ya" class="form-check-input radio_css" style="margin-top: 10px;" required <?= !empty($realAccount['ACC_F_CMPLT'])? "checked" : ""; ?>>
                            <label style="top: 0.5rem;position: relative;margin-bottom: 0;vertical-align: top;margin-right:1.5rem;">Ya</label>
                            <input type="radio" name="aggree" value="Tidak" class="form-check-input radio_css" style="margin-top: 10px;" required>
                            <label style="top: 0.5rem;position: relative;margin-bottom: 0;vertical-align: top;margin-right:1.5rem;">Tidak</label>
                        </div>
                        <div class="col-6 mt-3">
                            <div class="text-center">Pernyataan pada Tanggal</div>
                            <input type="text" name="agg_date" readonly required value="<?= retnull("ACC_F_CMPLT_DATE", date('Y-m-d H:i:s')) ?>" class="form-control text-center mb-3 <?= empty($realAccount['ACC_F_CMPLT_DATE'])? "realtime-date" : "" ?>">
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
        $('#form-check-kelengkapan').on('submit', function(event) {
            event.preventDefault();
            let data = Object.fromEntries(new FormData(this).entries());

            Swal.fire({
                text: "Please wait...",
                allowOutsideClick: false,
                didOpen: function() {
                    Swal.showLoading();
                }
            })
            
            $.post("/ajax/regol/kelengkapanFormulir", data, function(resp) {
                Swal.fire(resp.alert).then(() => {
                    if(resp.success) {
                        location.href = resp.redirect
                    }
                })
            }, 'json')
        })
    })
</script>