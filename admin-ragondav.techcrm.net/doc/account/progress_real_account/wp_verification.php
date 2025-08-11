<?php
    
    use App\Models\Account;
    use App\Models\Helper;
    use App\Models\FileUpload;
    $data = Helper::getSafeInput($_GET);

    $COMPANY         = App\Models\CompanyProfile::$name;
    $page_title      = 'Progress Real Account';
    $web_name_full   = $COMPANY;
    $progressAccount = Account::realAccountDetail($data["d"]);
    $userBanks       = (!empty($progressAccount["MBR_BKJSN"])) ? json_decode($progressAccount["MBR_BKJSN"], true) : [];
?>
<div class="page-header">
	<div>
		<h2 class="main-content-title tx-24 mg-b-5"><?php echo $page_title; ?></h2>
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="javascript:void(0);">Account</a></li>
			<li class="breadcrumb-item"><a href="javascript:void(0);"><?php echo $page_title; ?></a></li>
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
                    <!-- <div class="d-flex flex-start flex-wrap gap-2">
                        <a target="_blank" href="/export/all-new?acc=<?php echo $id_acc; ?>" class="btn btn-primary"><i class="fa fa-eye"></i> All Documents</a>
                    </div> -->
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="table-responsive mb-3">
                            <table class="table table-hover table-striped">
                                <tbody>
                                    <tr>
                                        <td class="text-start">Email</td>
                                        <td width="3%">:</td>
                                        <td class="text-start"><?= $progressAccount['MBR_EMAIL'] ?? "-" ?></td>

                                        <td width="10%" class="text-start">No. NPWP</td>
                                        <td width="3%">:</td>
                                        <td class="text-start"><?= $progressAccount['ACC_F_APP_PRIBADI_NPWP'] ?></td>
                                    </tr>
                                    <tr>
                                        <td width="10%" class="text-start">Type</td>
                                        <td width="3%" class="text-start">:</td>
                                        <td class="text-start">
                                            <strong>
                                                <?= $progressAccount['RTYPE_NAME'].'/'.$progressAccount['RTYPE_KOMISI'].'/'.(($progressAccount['RTYPE_ISFLOATING'] == 1) ? 'Floating' : ($progressAccount['RTYPE_RATE']/1000)) ?>
                                            </strong>
                                        </td>

                                        <td width="10%" class="text-start">Rate</td>
                                        <td width="3%">:</td>
                                        <td class="text-start">
                                            <strong>
                                                <?php
                                                    if($progressAccount['RTYPE_ISFLOATING'] == 1){
                                                        echo 'Floating';
                                                    }else{ echo number_format($progressAccount['RTYPE_RATE'], 0); }
                                                ?>
                                            </strong>
                                        </td>
                                    </tr>
        
                                    <tr>
                                        <td width="10%" class="text-start">Nama</td>
                                        <td width="3%">:</td>
                                        <td class="text-start"><?= $progressAccount['ACC_FULLNAME'] ?></td>

                                        <td width="10%" class="text-start">Charge</td>
                                        <td width="3%" class="text-start">:</td>
                                        <td class="text-start"><strong><?= $progressAccount['RTYPE_KOMISI'] ?? 0 ?></strong></td>
                                    </tr>
        
                                    <tr>
                                        <td width="10%" class="text-start">No. Telepon</td>
                                        <td width="3%" class="text-start">:</td>
                                        <td class="text-start"><?= $progressAccount['ACC_F_APP_PRIBADI_HP']; ?></td>
        
                                        <td width="10%" class="text-start">Tempat lahir</td>
                                        <td width="3%">:</td>
                                        <td class="text-start"><?= $progressAccount['ACC_TEMPAT_LAHIR'] ?></td>
                                    </tr>
        
                                    <tr>
                                        <td width="10%" class="text-start">Type Identitas</td>
                                        <td width="3%">:</td>
                                        <td class="text-start"><?= $progressAccount['ACC_TYPE_IDT'] ?></td>

                                        <td width="10%" class="text-start">Ibu Kandung</td>
                                        <td width="3%" class="text-start">:</td>
                                        <td class="text-start"><?= $progressAccount['ACC_F_APP_PRIBADI_IBU'] ?></td>
                                    </tr>

                                    <tr>
                                        <td width="10%" class="text-start">Tanggal lahir</td>
                                        <td width="3%">:</td>
                                        <td class="text-start"><?= $progressAccount['ACC_TANGGAL_LAHIR'] ?></td>

                                        <td width="10%" class="text-start">No. Identitas</td>
                                        <td width="3%">:</td>
                                        <td class="text-start"><?= $progressAccount['ACC_NO_IDT'] ?></td>
                                    </tr>
                                   
                                    <tr>
                                        <td width="10%" class="text-start">Document Type</td>
                                        <td width="3%" class="text-start">:</td>
                                        <td class="text-start"><?= $progressAccount['ACC_F_APP_FILE_TYPE'] ?></td>

                                        <td width="10%" class="text-start">Jenis Pekerjaan</td>
                                        <td width="3%">:</td>
                                        <td class="text-start"><?= $progressAccount['ACC_F_APP_KRJ_NAMA'] ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover table-bordered">
                                <thead>
                                    <tr>
                                        <th colspan="3" class="bg-secondary text-muted">User Bank</th>
                                    </tr>
                                    <tr>
                                        <th>Nama Bank</th>
                                        <th>No. Rekening</th>
                                        <th>Nama</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($userBanks as $bank) : ?>
                                        <tr>
                                            <td><?= $bank['MBANK_NAME'] ?></td>
                                            <td><?= $bank['MBANK_ACCOUNT'] ?></td>
                                            <td><?= $bank['MBANK_HOLDER'] ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="card text-center h-100">
                                    <a target="_blank" href="<?php echo FileUpload::awsFile($progressAccount['ACC_F_APP_FILE_IMG']); ?>">
                                        <img width="75%" src="<?php echo FileUpload::awsFile($progressAccount['ACC_F_APP_FILE_IMG']); ?>">
                                    </a>
                                    <div class="card-body">
                                        <a target="_blank" href="<?php echo FileUpload::awsFile($progressAccount['ACC_F_APP_FILE_IMG']); ?>">
                                            Dokumen Pendukung
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="card text-center h-100">
                                    <a target="_blank" href="<?php echo FileUpload::awsFile($progressAccount['ACC_F_APP_FILE_IMG2']); ?>">
                                        <img width="75%" src="<?php echo FileUpload::awsFile($progressAccount['ACC_F_APP_FILE_IMG2']); ?>">
                                    </a>
                                    <div class="card-body">
                                        <a target="_blank" href="<?php echo FileUpload::awsFile($progressAccount['ACC_F_APP_FILE_IMG2']); ?>">
                                            Dokumen Pendukung Lainnya
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="card text-center h-100">
                                    <a target="_blank" href="<?php echo FileUpload::awsFile($progressAccount['ACC_F_APP_FILE_FOTO']); ?>">
                                        <img width="75%" src="<?php echo FileUpload::awsFile($progressAccount['ACC_F_APP_FILE_FOTO']); ?>">
                                    </a>
                                    <div class="card-body">
                                        <a target="_blank" href="<?php echo FileUpload::awsFile($progressAccount['ACC_F_APP_FILE_FOTO']); ?>">
                                            Foto Terbaru (Selfie)
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="card text-center h-100">
                                    <a target="_blank" href="<?php echo FileUpload::awsFile($progressAccount['ACC_F_APP_FILE_ID']); ?>">
                                        <img width="75%" src="<?php echo FileUpload::awsFile($progressAccount['ACC_F_APP_FILE_ID']); ?>">
                                    </a>
                                    <div class="card-body">
                                        <a target="_blank" href="<?php echo FileUpload::awsFile($progressAccount['ACC_F_APP_FILE_ID']); ?>">
                                            Foto Identitas
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="card text-center h-100">
                                    <a target="_blank" href="<?php echo FileUpload::awsFile($progressAccount['ACC_F_SIMULASI_IMG']); ?>">
                                        <img width="75%" src="<?php echo FileUpload::awsFile($progressAccount['ACC_F_SIMULASI_IMG']); ?>">
                                    </a>
                                    <div class="card-body">
                                        <a target="_blank" href="<?php echo FileUpload::awsFile($progressAccount['ACC_F_SIMULASI_IMG']); ?>">
                                            Demo Account
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group mt-2 w-100">
                                    <?php if($progressAccount['ACC_STS'] == 1 && $progressAccount['ACC_WPCHECK'] == 0) : ?>
                                        <button type="button" data-act="reject" class="btnAct btn btn-danger btn-block">Reject</button>
                                        <button type="button" data-act="accept" class="btnAct btn btn-success btn-block">Accept</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
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
                let id  = '<?= $data["d"] ?>';
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
</script>