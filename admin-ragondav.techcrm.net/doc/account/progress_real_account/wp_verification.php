<?php
    
    use App\Models\Account;
    use App\Models\Helper;
    use App\Models\FileUpload;
    $data = Helper::getSafeInput($_GET);
    $id_acc = $data["d"] ?? "";

    $COMPANY = App\Models\CompanyProfile::$name;
    $page_title = 'Progress Real Account';
    $web_name_full = $COMPANY;
    $progressAccount = Account::realAccountDetail($id_acc);
    $userBanks = (!empty($progressAccount["MBR_BKJSN"])) ? json_decode($progressAccount["MBR_BKJSN"], true) : [];

    if(!$progressAccount) {
        die("<script>alert('Invalid code'); location.href = '/account/progress_real_account/view'; </script>");
    }
?>
<div class="page-header">
	<div>
		<h2 class="main-content-title tx-24 mg-b-5"><?php echo $page_title; ?></h2>
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="<?= pathbreadcrumb(0) ?>/dashboard">Home</a></li>
			<li class="breadcrumb-item"><a href="javascript:void(0);">Account</a></li>
			<li class="breadcrumb-item"><a href="<?= pathbreadcrumb(2) ?>/view"><?php echo $page_title; ?></a></li>
			<li class="breadcrumb-item active">WP Verification</li>
		</ol>
	</div>
</div>
<div class="row">
    <div class="col-md-12 mb-3">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between flex-wrap align-items-center mb-3">
                    <h5 class="card-title">Agreement</h5>
                    <div class="d-flex flex-start flex-wrap gap-2">
                        <a target="_blank" href="/export/pernyataan-pengungkapan?acc=<?php echo $id_acc; ?>" class="btn btn-primary d-flex align-items-center"><i class="fa fa-eye"></i>&nbsp;Disclosure Statement</a> &nbsp;
                        <a href="/export/all?acc=<?php echo $id_acc; ?>" class="btn btn-primary"><i class="fa fa-eye"></i> All Documents</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <tbody>
                            <tr>
                                <td>1.</td>
                                <td>Formulir Nomor : 107.PBK.01 </td>
                                <td>
                                    Profile Perusahaan <br>
                                    <small><?php echo $web_name_full ?> adalah Perusahaan Pialang yang bergerak di bidang perdagangan kontrak derivatif komoditi, Indeks Saham dan Foreign Exchange.</small>
                                </td>
                                <td><a target="_blank" href="/export/profile-perusahaan?acc=<?php echo $id_acc; ?>" class="btn btn-primary d-flex align-items-center"><i class="fa fa-eye"></i>&nbsp;View</a></td>
                            </tr>
                            <tr>
                                <td>2.</td>
                                <td>Formulir Nomor : 107.PBK.02.1</td>
                                <td>
                                    Pernyataan Telah Melakukan Simulasi perdagangan berjangka komoditi<br>
                                    <small>Calon Nasabah diwajibkan untuk memiliki demo account <?php echo $web_name_full ?> sebagai sarana untuk melakukan simulasi transaksi di <?php echo $web_name_full ?>.</small>
                                </td>
                                <td><a target="_blank" href="/export/pernyataan-simulasi?acc=<?php echo $id_acc; ?>" class="btn btn-primary d-flex align-items-center"><i class="fa fa-eye"></i>&nbsp;View</a></td>
                            </tr>
                            <tr>
                                <td>3.</td>
                                <td>Formulir Nomor : 107.PBK.02.2</td>
                                <td>
                                    Pernyataan telah berpengalaman melaksanakan transaksi perdagangan berjangka komoditi<br>
                                    <small>Dalam hal calon nasabah telah berpengalaman dalam melaksanakan transaksi dalam Perdagangan Berjangka Komoditi, Nasabah memberikan pernyataan dengan Surat Pernyataan Telah Berpengalaman Melaksanakan Transaksi Perdagangan Berjangka Komoditi.</small>
                                </td>
                                <td><a target="_blank" href="/export/pernyataan-pengalaman?acc=<?php echo $id_acc; ?>" class="btn btn-primary d-flex align-items-center"><i class="fa fa-eye"></i>&nbsp;View</a></td>
                            </tr>
                            <tr>
                                <td>4.</td>
                                <td>Formulir Nomor : 107.PBK.03</td>
                                <td>
                                    Aplikasi Pembukaan Rekening Transaksi secara Elektronik On-line<br>
                                    <small>Seluruh data isian dalam Aplikasi Pembukaan Rekening Transaksi Secara Elektronik On-line Dalam Sistem Perdagangan Alternatif wajib di isi sendiri oleh Nasabah, dan Nasabah bertanggung jawab atas kebenaran informasi yang diberikan dalam mengisi dokumen ini.</small>
                                </td>
                                <td><a target="_blank" href="/export/aplikasi-pembukaan-rekening?acc=<?php echo $id_acc; ?>" class="btn btn-primary d-flex align-items-center"><i class="fa fa-eye"></i>&nbsp;View</a></td>
                            </tr>
                            <tr>
                                <td>5.</td>
                                <td>Formulir Nomor : 107.PBK.04.2</td>
                                <td>
                                    Document pemberitahuan adanya resiko<br>
                                    <small>Maksud dokumen ini adalah memberitahukan bahwa kemungkinan kerugian atau keuntungan dalam perdagangan Kontrak derifatif bisa mencapai jumlah yang sangat besar. Oleh karena itu, Anda harus berhati-hati dalam memutuskan untuk melakukan transaksi, apakah kondisi keuangan Anda mencukupi.</small>
                                </td>
                                <td><a target="_blank" href="/export/pemberitahuan-adanya-risiko?acc=<?php echo $id_acc; ?>" class="btn btn-primary d-flex align-items-center"><i class="fa fa-eye"></i>&nbsp;View</a></td>
                            </tr>
                            <tr>
                                <td>6.</td>
                                <td>Formulir Nomor : 107.PBK.05.2</td>
                                <td>
                                    Perjanjian pemberian amanat secara elektronik on-line untuk transaksi kontrak derifatif
                                    <small>Perjanjian kontrak berjangka dan sepakat untuk mengadakan Perjanjian Pemberian Amanat untuk melakukan transaksi penjualan maupun pembelian Kontrak</small>
                                </td>
                                <td><a target="_blank" href="/export/perjanjian-pemberian-amanat?acc=<?php echo $id_acc; ?>" class="btn btn-primary d-flex align-items-center"><i class="fa fa-eye"></i>&nbsp;View</a></td>
                            </tr>
                            <tr>
                                <td>7.</td>
                                <td>Formulir Nomor : 107.PBK.06</td>
                                <td>
                                    Peraturan Perdagangan (Trading Rules)<br>
                                    <small>Peraturan Perdagangan (Trading Rules) dalam siste, aplikasi penerimaan nasabah secara elektronik On-Line</small>
                                </td>
                                <td><a target="_blank" href="/export/trading-rules?acc=<?php echo $id_acc; ?>" class="btn btn-primary d-flex align-items-center"><i class="fa fa-eye"></i>&nbsp;View</a></td>
                            </tr>
                            <tr>
                                <td>8.</td>
                                <td>Formulir Nomor : 107.PBK.07</td>
                                <td>
                                    Pernyataan bertanggung jawab<br>
                                    <small>Pernyataan bertanggung jawab atas kode akses transaksi nasabah(Personal Access Password)</small>
                                </td>
                                <td><a target="_blank" href="/export/personal-access-password?acc=<?php echo $id_acc; ?>" class="btn btn-primary d-flex align-items-center"><i class="fa fa-eye"></i>&nbsp;View</a></td>
                            </tr>
                            <tr>
                                <td>9.</td>
                                <td>-</td>
                                <td>
                                    Formulir Penyataan Dana Nasabah<br>
                                    <small>Pernyataan Bahwa Dana Yang Di Gunakan Sebagai Margin Merupakan Dana Milik Nasabah Sendiri</small>
                                </td>
                                <td><a target="_blank" href="/export/pernyataan-dana-nasabah?acc=<?php echo $id_acc; ?>" class="btn btn-primary d-flex align-items-center"><i class="fa fa-eye"></i>&nbsp;View</a></td>
                            </tr>
                            <tr>
                                <td>10.</td>
                                <td>-</td>
                                <td>
                                    Surat Pernyataan<br>
                                    <small>Surat pernyataan nasabah</small>
                                </td>
                                <td><a target="_blank" href="/export/surat-pernyataan?acc=<?php echo $id_acc; ?>" class="btn btn-primary d-flex align-items-center"><i class="fa fa-eye"></i>&nbsp;View</a></td>
                            </tr>
                            <tr>
                                <td>11.</td>
                                <td>-</td>
                                <td>
                                    Kelengkapan Formulir<br>
                                    <small>Proses Penerimaan Nasabah Secara Elektronik Online</small>
                                </td>
                                <td><a target="_blank" href="/export/kelengkapan-formulir?acc=<?php echo $id_acc; ?>" class="btn btn-primary d-flex align-items-center"><i class="fa fa-eye"></i>&nbsp;View</a></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-12 mb-3">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between flex-wrap align-items-center mb-3">
                    <h5 class="card-title">Summary</h5>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <?php require_once __DIR__ . "/summary.php" ?>
                    </div>
                    <div class="col-md-4">
                        <form action="" method="post" enctype="multipart/form-data" id="form-document">
                            <input type="hidden" name="account" value="<?= $id_acc ?>">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <input type="file" class="dropify" name="app_image_1" id="app_image_1" data-max-file-size="2M" data-show-remove="false" data-allowed-file-extensions="png jpg jpeg" data-default-file="<?php echo App\Models\FileUpload::awsFile($progressAccount['ACC_F_APP_FILE_IMG']); ?>">
                                    <label for="app_image_1" class="form-control-label">
                                        <a target="_blank" href="<?php echo App\Models\FileUpload::awsFile($progressAccount['ACC_F_APP_FILE_IMG']); ?>">
                                            Rekening Koran Bank / Tagihan Kartu Kredit
                                        </a>
                                    </label>
                                </div>
    
                                <div class="col-md-6 mb-3">
                                    <input type="file" class="dropify" name="app_image_2" id="app_image_2" data-max-file-size="2M" data-show-remove="false" data-allowed-file-extensions="png jpg jpeg" data-default-file="<?php echo App\Models\FileUpload::awsFile($progressAccount['ACC_F_APP_FILE_IMG2']); ?>">
                                    <label for="app_image_2" class="form-control-label">
                                        <a target="_blank" href="<?php echo App\Models\FileUpload::awsFile($progressAccount['ACC_F_APP_FILE_IMG2']); ?>">
                                            Rekening Listrik / Telepon
                                        </a>
                                    </label>
                                </div>
    
                                <div class="col-md-6 mb-3">
                                    <input type="file" class="dropify" name="app_image_npwp" id="app_image_npwp" data-max-file-size="2M" data-show-remove="false" data-allowed-file-extensions="png jpg jpeg" data-default-file="<?php echo App\Models\FileUpload::awsFile($progressAccount['ACC_F_APP_FILE_NPWP']); ?>">
                                    <label for="app_image_npwp" class="form-control-label">
                                        <a target="_blank" href="<?php echo App\Models\FileUpload::awsFile($progressAccount['ACC_F_APP_FILE_NPWP']); ?>">
                                           NPWP
                                        </a>
                                    </label>
                                </div>
    
                                <div class="col-md-6 mb-3">
                                    <input type="file" class="dropify" name="app_image_selfie" id="app_image_selfie" data-max-file-size="4M" data-min-width="480" data-min-height="640" data-show-remove="false" data-allowed-file-extensions="png jpg jpeg" data-default-file="<?php echo App\Models\FileUpload::awsFile($progressAccount['ACC_F_APP_FILE_FOTO']); ?>">
                                    <label for="app_image_selfie" class="form-control-label">
                                        <a target="_blank" href="<?php echo App\Models\FileUpload::awsFile($progressAccount['ACC_F_APP_FILE_FOTO']); ?>">
                                           Foto Terbaru (Selfie)
                                        </a>
                                    </label>
                                </div>
    
                                <div class="col-md-6 mb-3">
                                    <input type="file" class="dropify" name="app_image_identitas" id="app_image_identitas" data-max-file-size="2M"  data-min-width="480" data-min-height="320" data-show-remove="false" data-allowed-file-extensions="png jpg jpeg" data-default-file="<?php echo App\Models\FileUpload::awsFile($progressAccount['ACC_F_APP_FILE_ID']); ?>">
                                    <label for="app_image_identitas" class="form-control-label">
                                        <a target="_blank" href="<?php echo App\Models\FileUpload::awsFile($progressAccount['ACC_F_APP_FILE_ID']); ?>">
                                           Foto Identitas
                                        </a>
                                    </label>
                                </div>
                             
                                <div class="col-md-6 mb-3">
                                    <input type="file" class="dropify" name="app_image_3" id="app_image_3" data-max-file-size="2M" data-show-remove="false" data-allowed-file-extensions="png jpg jpeg" data-default-file="<?php echo App\Models\FileUpload::awsFile($progressAccount['ACC_F_APP_FILE_IMG3']); ?>">
                                    <label for="app_image_3" class="form-control-label">
                                        <a target="_blank" href="<?php echo App\Models\FileUpload::awsFile($progressAccount['ACC_F_APP_FILE_IMG3']); ?>">
                                            Dokumen Lainnya
                                        </a>
                                    </label>
                                </div>
    
                                <div class="col-md-6 mb-3">
                                    <input type="file" class="dropify" name="app_image_4" id="app_image_4" data-max-file-size="2M" data-show-remove="false" data-allowed-file-extensions="png jpg jpeg" data-default-file="<?php echo App\Models\FileUpload::awsFile($progressAccount['ACC_F_APP_FILE_IMG4']); ?>">
                                    <label for="app_image_4" class="form-control-label">
                                        <a target="_blank" href="<?php echo App\Models\FileUpload::awsFile($progressAccount['ACC_F_APP_FILE_IMG4']); ?>">
                                            Dokumen Lainnya
                                        </a>
                                    </label>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="d-flex flex-wrap justify-content-center align-items-center gap-3">
                    <?php if($progressAccount['ACC_STS'] == 1 && $progressAccount['ACC_WPCHECK'] == 0) : ?>
                        <button type="button" id="verif_verihub" class="btn btn-primary">Verifikasi Verihub</button>
                        <?php if($permisUpdate = $adminPermissionCore->isHavePermission($moduleId, "update.document")) : ?>
                            <a href="javascript:void(0)" id="update-document" data-url="<?= $permisUpdate['link'] ?>" class="btn btn-primary">Update Document</a>
                        <?php endif; ?>
                        <button type="button" data-act="reject" class="btnAct btn btn-danger px-5">Reject</button>
                        <button type="button" data-act="accept" class="btnAct btn btn-success px-5">Accept</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('#verif_verihub').on('click', function() {
            Swal.fire({
                title: "Verifikasi Verihub",
                text: "Mohon konfirmasi untuk melanjutkan",
                icon: "info",
                showCancelButton: true,
                reverseButtons: true
            }).then((result) => {
                if(result.isConfirmed) {
                    Swal.fire({
                        text: "Please wait...",
                        allowOutsideClick: false,
                        didOpen: function() {
                            Swal.showLoading();
                        }
                    })

                    $.post("/ajax/post/account/verifikasi_verihub", {account: '<?= $id_acc; ?>'}, (resp) => {
                        Swal.fire(resp.alert)
                    }, 'json')
                }
            })
        })

        $('.btnAct').on('click', function() {
            let ARCBTN = {
                title: `${$(this).data('act').toUpperCase()} DATA`,
                text: `Berikan catatan sebelum ${$(this).data('act').toUpperCase()}`,
                icon: 'warning',
                showCancelButton: true,
                reverseButtons: true,
                input: "text",
                inputLabel: `Masukan catatan`,
                inputAttributes: {
                    required: true,
                }
            };
            Swal.fire(ARCBTN).then((result) => {
                if(result.isConfirmed) {
                    Swal.fire({
                        text: "Please wait...",
                        allowOutsideClick: false,
                        didOpen: function() {
                            Swal.showLoading();
                        }
                    });

                    let id  = '<?= $id_acc ?>';
                    let act = $(this).data('act');
                    $.post("/ajax/post/account/wp_verification_acion", {sbmt_id: id, sbmt_act: act, sbmt_note: result.value}, function(resp) {
                        Swal.fire(resp.alert).then(() => {
                            if(resp.success) {
                                if(resp?.data?.reloc?.length){
                                    location.href = resp?.data?.reloc;
                                }else{ location.reload(); }
                            }
                        })
                    }, 'json');
                }
            });
        });

        $('#update-document').on('click', function(event) {
            let url = $(this).data('url')
            Swal.fire({
                title: "Update User Document",
                text: "Konfirmasi untuk melanjutkan",
                icon: "question",
                showCancelButton: true,
                reverseButtons: true
            }).then((result) => {
                if(result.isConfirmed) {
                    Swal.fire({
                        text: "Loading...",
                        allowOutsideClick: false,
                        didOpen: function() {
                            Swal.showLoading();
                        } 
                    })

                    $.ajax({
                        url: `/ajax/post${url}`,
                        type: "post",
                        dataType: "json",
                        data: new FormData($('#form-document')[0]),
                        contentType: false,
                        processData: false,
                        cache: false
                    }).done((resp) => {
                        Swal.fire(resp.alert).then(() => {
                            if(resp.success) {
                                location.reload();
                            }
                        })
                    })
                }
            })
        })
    })
</script>