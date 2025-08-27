<?php
    use App\Models\Helper;
    use App\Models\FileUpload;
    $SFD  = Helper::getSafeInput($_GET);
    $usrx = $SFD["d"];
    $USER_QWR = mysqli_query($db, '
        SELECT
            tb_member.MBR_EMAIL,
            IFNULL(tb_racc.ACC_FULLNAME, tb_member.MBR_NAME) AS ACC_FULLNAME,
            IFNULL(tb_racc.ACC_TANGGAL_LAHIR, tb_member.MBR_TGLLAHIR) AS ACC_TANGGAL_LAHIR,
            IFNULL(tb_racc.ACC_ADDRESS, tb_member.MBR_ADDRESS) AS ACC_ADDRESS,
            IFNULL(tb_racc.ACC_ZIPCODE, tb_member.MBR_ZIP) AS ACC_ZIPCODE,
            IFNULL(tb_racc.ACC_TYPE_IDT, tb_member.MBR_TYPE_IDT) AS ACC_TYPE_IDT,
            IFNULL(tb_racc.ACC_F_APP_PRIBADI_KELAMIN, tb_member.MBR_JENIS_KELAMIN) AS ACC_F_APP_PRIBADI_KELAMIN,
            IFNULL(tb_racc.ACC_F_APP_PRIBADI_TLP, tb_member.MBR_PHONE) AS ACC_F_APP_PRIBADI_TLP,
            tb_racc.ACC_F_APP_PRIBADI_IBU,
            tb_racc.ACC_F_APP_PRIBADI_STSRMH,
            tb_racc.ACC_F_APP_KRJ_NAMA,
            tb_racc.ACC_F_APP_KRJ_ALAMAT,
            tb_racc.ACC_F_APP_KRJ_BDNG,
            tb_racc.ACC_F_APP_KRJ_JBTN,
            tb_racc.ACC_F_APP_KRJ_LAMA,
            tb_member_bank.MBANK_NAME,
            tb_member_bank.MBANK_ACCOUNT,
            tb_racc.ACC_F_APP_KEKYAN_NJOP,
            tb_racc.ACC_F_APP_KEKYAN_DPST,
            tb_racc.ACC_F_APP_KEKYAN_NILAI,
            tb_racc.ACC_F_APP_KEKYAN_LAIN,
            tb_racc.ACC_F_APP_DRRT_NAMA,
            tb_racc.ACC_F_APP_DRRT_ALAMAT,
            tb_racc.ACC_F_APP_DRRT_ZIP,
            tb_racc.ACC_F_APP_DRRT_TLP,
            tb_racc.ACC_F_APP_DRRT_HUB,
            tb_member.MBR_STS,
            tb_racc.ACC_F_APP_FILE_TYPE,
            tb_racc.ACC_F_APP_FILE_IMG,
            tb_racc.ACC_F_APP_FILE_FOTO,
            tb_racc.ACC_F_APP_FILE_ID
        FROM tb_member
        LEFT JOIN tb_member_bank
        ON(tb_member.MBR_ID = tb_member_bank.MBANK_MBR)
        LEFT JOIN tb_racc
        ON(tb_member.MBR_ID = tb_racc.ACC_MBR
        AND tb_racc.ACC_DERE = 1)
        WHERE MD5(MD5(tb_member.ID_MBR)) = "'.$usrx.'"
    ');
    if($USER_QWR && mysqli_num_rows($USER_QWR) > 0){
        $RSLT_USER = mysqli_fetch_assoc($USER_QWR);
    }
?>
<div class="page-header">
	<div>
		<h2 class="main-content-title tx-24 mg-b-5">Member User Detail</h2>
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="javascript:void(0);">Member</a></li>
			<li class="breadcrumb-item active" aria-current="page">User</li>
			<li class="breadcrumb-item active" aria-current="page">Detail</li>
		</ol>
	</div>
</div>

<div class="row">
    <div class="col-md-7">
        <div class="card custom-card overflow-hidden">
            <form method="post">
                <div class="card-header font-weight-bold">Data user</div>
                <div class="card-body">
                    <table style="width:100%">
                        <h5>Data Pribadi</h5>
                        <tr>
                            <td width="45%" style="vertical-align: top;">Email</td>
                            <td width="1%" style="vertical-align: top; text-align:center;"><div style="margin:0px 3px;">:</div></td>
                            <td style="vertical-align: top;"><?php echo ($RSLT_USER["MBR_EMAIL"] ?? '') ?></td>
                        </tr>
                        <tr>
                            <td width="45%" style="vertical-align: top;">Nama Lengkap</td>
                            <td width="1%" style="vertical-align: top; text-align:center;"><div style="margin:0px 3px;">:</div></td>
                            <td style="vertical-align: top;"><?php echo ($RSLT_USER["ACC_FULLNAME"] ?? '') ?></td>
                        </tr>
                        <tr>
                            <td width="45%" style="vertical-align: top;">Tanggal Lahir</td>
                            <td width="1%" style="vertical-align: top; text-align:center;"><div style="margin:0px 3px;">:</div></td>
                            <td style="vertical-align: top;"><?php echo ($RSLT_USER["ACC_TANGGAL_LAHIR"] ?? '') ?></td>
                        </tr>
                        <tr>
                            <td width="45%" style="vertical-align: top;">Alamat</td>
                            <td width="1%" style="vertical-align: top; text-align:center;"><div style="margin:0px 3px;">:</div></td>
                            <td style="vertical-align: top;"><?php echo ($RSLT_USER["ACC_ADDRESS"] ?? '') ?></td>
                        </tr>
                        <tr>
                            <td width="45%" style="vertical-align: top;">Kode Pos</td>
                            <td width="1%" style="vertical-align: top; text-align:center;"><div style="margin:0px 3px;">:</div></td>
                            <td style="vertical-align: top;"><?php echo ($RSLT_USER["ACC_ZIPCODE"] ?? '') ?></td>
                        </tr>
                        <tr>
                            <td width="45%" style="vertical-align: top;">Identitas</td>
                            <td width="1%" style="vertical-align: top; text-align:center;"><div style="margin:0px 3px;">:</div></td>
                            <td style="vertical-align: top;"><?php echo ($RSLT_USER["ACC_TYPE_IDT"] ?? '') ?> | <?php echo ($RSLT_USER[""] ?? '') ?></td>
                        </tr>
                        <tr>
                            <td width="45%" style="vertical-align: top;">Jenis Kelamin</td>
                            <td width="1%" style="vertical-align: top; text-align:center;"><div style="margin:0px 3px;">:</div></td>
                            <td style="vertical-align: top;"><?php echo ($RSLT_USER["ACC_F_APP_PRIBADI_KELAMIN"] ?? '') ?></td>
                        </tr>
                        <tr>
                            <td width="45%" style="vertical-align: top;">Nomor telepon</td>
                            <td width="1%" style="vertical-align: top; text-align:center;"><div style="margin:0px 3px;">:</div></td>
                            <td style="vertical-align: top;"><?php echo ($RSLT_USER["ACC_F_APP_PRIBADI_TLP"] ?? '') ?></td>
                        </tr>
                        <br>
                        <tr>
                            <td width="45%" style="vertical-align: top;">Nama Ibu Kandung</td>
                            <td width="1%" style="vertical-align: top; text-align:center;"><div style="margin:0px 3px;">:</div></td>
                            <td style="vertical-align: top;"><?php echo ($RSLT_USER["ACC_F_APP_PRIBADI_IBU"] ?? '') ?></td>
                        </tr>
                        <tr>
                            <td width="45%" style="vertical-align: top;">Status Kepemilikan Rumah</td>
                            <td width="1%" style="vertical-align: top; text-align:center;"><div style="margin:0px 3px;">:</div></td>
                            <td style="vertical-align: top;"><?php echo ($RSLT_USER["ACC_F_APP_PRIBADI_STSRMH"] ?? '') ?></td>
                        </tr>
                    </table>
                    <div style="border-top:1px solid black;text-align:left;vertical-align: middle;padding: 10px 0 10px 0;"></div>
                    <table style="width:100%">
                        <h5>Keterangan Pekerjaan</h5>
                        <tr>
                            <td width="45%" style="vertical-align: top;">Pekerjaan</td>
                            <td width="1%" style="vertical-align: top; text-align:center;"><div style="margin:0px 3px;">:</div></td>
                            <td style="vertical-align: top;"><?php echo ($RSLT_USER["ACC_F_APP_KRJ_NAMA"] ?? '') ?></td>
                        </tr>
                        <tr>
                            <td width="45%" style="vertical-align: top;">Tempat kerja</td>
                            <td width="1%" style="vertical-align: top; text-align:center;"><div style="margin:0px 3px;">:</div></td>
                            <td style="vertical-align: top;"><?php echo ($RSLT_USER["ACC_F_APP_KRJ_ALAMAT"] ?? '') ?></td>
                        </tr>
                        <tr>
                            <td width="45%" style="vertical-align: top;">Bidang Usaha</td>
                            <td width="1%" style="vertical-align: top; text-align:center;"><div style="margin:0px 3px;">:</div></td>
                            <td style="vertical-align: top;"><?php echo ($RSLT_USER["ACC_F_APP_KRJ_BDNG"] ?? '') ?></td>
                        </tr>
                        <tr>
                            <td width="45%" style="vertical-align: top;">Posisi</td>
                            <td width="1%" style="vertical-align: top; text-align:center;"><div style="margin:0px 3px;">:</div></td>
                            <td style="vertical-align: top;"><?php echo ($RSLT_USER["ACC_F_APP_KRJ_JBTN"] ?? '') ?></td>
                        </tr>
                        <tr>
                            <td width="45%" style="vertical-align: top;">Lama Bekerja</td>
                            <td width="1%" style="vertical-align: top; text-align:center;"><div style="margin:0px 3px;">:</div></td>
                            <td style="vertical-align: top;"><?php echo ($RSLT_USER["ACC_F_APP_KRJ_LAMA"] ?? '') ?></td>
                        </tr>
                    </table>
                    <div style="border-top:1px solid black;text-align:left;vertical-align: middle;padding: 10px 0 10px 0;"></div>
                    <table style="width:100%">
                        <h5>Informasi Bank</h5>
                        <tr>
                            <td width="45%" style="vertical-align: top;">Nama Bank</td>
                            <td width="1%" style="vertical-align: top; text-align:center;"><div style="margin:0px 3px;">:</div></td>
                            <td style="vertical-align: top;"><?php echo ($RSLT_USER["MBANK_NAME"] ?? '') ?></td>
                        </tr>
                        <tr>
                            <td width="45%" style="vertical-align: top;">Nomor Rekening</td>
                            <td width="1%" style="vertical-align: top; text-align:center;"><div style="margin:0px 3px;">:</div></td>
                            <td style="vertical-align: top;"><?php echo ($RSLT_USER["MBANK_ACCOUNT"] ?? '') ?></td>
                        </tr>
                    </table>
                    <div style="border-top:1px solid black;text-align:left;vertical-align: middle;padding: 10px 0 10px 0;"></div>
                    <table style="width:100%">
                        <h5>Alasan Investasi</h5>
                        <tr>
                            <td width="45%" style="vertical-align: top;">Nilai NJOP</td>
                            <td width="1%" style="vertical-align: top; text-align:center;"><div style="margin:0px 3px;">:</div></td>
                            <td style="vertical-align: top;"><?= ( $RSLT_USER["ACC_F_APP_KEKYAN_NJOP"] ?? '')?></td>
                        </tr>
                        <tr>
                            <td width="45%" style="vertical-align: top;">Jumlah Deposit</td>
                            <td width="1%" style="vertical-align: top; text-align:center;"><div style="margin:0px 3px;">:</div></td>
                            <td style="vertical-align: top;"><?= ( $RSLT_USER["ACC_F_APP_KEKYAN_DPST"] ?? '')?></td>
                        </tr>
                        <tr>
                            <td width="45%" style="vertical-align: top;">Total Harta</td>
                            <td width="1%" style="vertical-align: top; text-align:center;"><div style="margin:0px 3px;">:</div></td>
                            <td style="vertical-align: top;"><?php echo ($RSLT_USER["ACC_F_APP_KEKYAN_NILAI"] ?? '') ?></td>
                        </tr>
                        <tr>
                            <td width="45%" style="vertical-align: top;">Harta lainya</td>
                            <td width="1%" style="vertical-align: top; text-align:center;"><div style="margin:0px 3px;">:</div></td>
                            <td style="vertical-align: top;"><?php echo ($RSLT_USER["ACC_F_APP_KEKYAN_LAIN"] ?? '') ?></td>
                        </tr>
                    </table>
                </div>
            </form>
        </div>
    </div>
    <div class="col-sm-5">
        <div class="card custom-card overflow-hidden">
            <div class="card-header font-weight-bold">Keterangan Tambahan</div>
            <div class="card-body">
                <table style="width:100%">
                    <h5>Kontak Darurat</h5>
                    <tr>
                        <td width="45%" style="vertical-align: top;">Nama</td>
                        <td width="1%" style="vertical-align: top; text-align:center;"><div style="margin:0px 3px;">:</div></td>
                        <td style="vertical-align: top;"><?php echo ($RSLT_USER["ACC_F_APP_DRRT_NAMA"] ?? '') ?></td>
                    </tr>
                    <tr>
                        <td width="45%" style="vertical-align: top;">Alamat</td>
                        <td width="1%" style="vertical-align: top; text-align:center;"><div style="margin:0px 3px;">:</div></td>
                        <td style="vertical-align: top;"><?php echo ($RSLT_USER["ACC_F_APP_DRRT_ALAMAT"] ?? '') ?></td>
                    </tr>
                    <tr>
                        <td width="45%" style="vertical-align: top;">Kode Pos</td>
                        <td width="1%" style="vertical-align: top; text-align:center;"><div style="margin:0px 3px;">:</div></td>
                        <td style="vertical-align: top;"><?php echo ($RSLT_USER["ACC_F_APP_DRRT_ZIP"] ?? '') ?></td>
                    </tr>
                    <tr>
                        <td width="45%" style="vertical-align: top;">Nomor Telepon</td>
                        <td width="1%" style="vertical-align: top; text-align:center;"><div style="margin:0px 3px;">:</div></td>
                        <td style="vertical-align: top;"><?php echo ($RSLT_USER["ACC_F_APP_DRRT_TLP"] ?? '') ?></td>
                    </tr>
                    <tr>
                        <td width="45%" style="vertical-align: top;">Hubungan</td>
                        <td width="1%" style="vertical-align: top; text-align:center;"><div style="margin:0px 3px;">:</div></td>
                        <td style="vertical-align: top;"><?php echo ($RSLT_USER["ACC_F_APP_DRRT_HUB"] ?? '') ?></td>
                    </tr>
                </table>
                <div style="border-top:1px solid black;text-align:left;vertical-align: middle;padding: 10px 0 10px 0;"></div>
                <div class="row">
                    <div class="col-sm-4 text-center">
                        <?php if($RSLT_USER["ACC_F_APP_FILE_IMG"] == ''|| $RSLT_USER["ACC_F_APP_FILE_IMG"] == '-' ){ ?>
                            <img src="/assets/img/unknown-file.png" width="100%">
                        <?php } else { ?>
                            <img src="<?php echo FileUpload::awsFile($RSLT_USER["ACC_F_APP_FILE_IMG"]); ?>" width="100%">
                            <hr>
                        <?php }; ?>
                        <br>
                        <small>Dokumen Pendukung <br> (<?php echo ($RSLT_USER["ACC_F_APP_FILE_TYPE"] ?? '') ?>)</small>
                    </div>
                    <div class="col-sm-4 text-center">
                        <?php if($RSLT_USER["ACC_F_APP_FILE_FOTO"] == ''|| $RSLT_USER["ACC_F_APP_FILE_FOTO"] == '-' ){ ?>
                            <img src="/assets/img/unknown-file.png" width="100%">
                        <?php } else { ?>
                            <img src="<?php echo FileUpload::awsFile($RSLT_USER["ACC_F_APP_FILE_FOTO"]); ?>" width="100%">
                            <hr>
                        <?php }; ?>
                        <br>
                        <small>Foto Terbaru</small>
                    </div>
                    <div class="col-sm-4 text-center">
                        <?php if($RSLT_USER["ACC_F_APP_FILE_ID"] == ''|| $RSLT_USER["ACC_F_APP_FILE_ID"] == '-' ){ ?>
                            <img src="/assets/img/unknown-file.png" width="100%">
                        <?php } else { ?>
                            <img src="<?php echo FileUpload::awsFile($RSLT_USER["ACC_F_APP_FILE_ID"]); ?>" width="100%">
                            <hr>
                        <?php }; ?>
                        <br>
                        <small>Foto Identitas</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="card custom-card overflow-hidden mt-3">
            <div class="card-header font-weight-bold">Akun User</div>
            <div class="card-body">
                <table style="width:100%">
                    <h5>Status user</h5>
                    <tr>
                        <td width="45%" style="vertical-align: top;">Email</td>
                        <td width="1%" style="vertical-align: top; text-align:center;"><div style="margin:0px 3px;">:</div></td>
                        <td style="vertical-align: top;"><?php echo ($RSLT_USER["MBR_EMAIL"] ?? '') ?></td>
                    </tr>
                    <tr>
                        <td width="45%" style="vertical-align: top;">Status</td>
                        <td width="1%" style="vertical-align: top; text-align:center;"><div style="margin:0px 3px;">:</div></td>
                        <td style="vertical-align: top;">
                            <?php
                                $STATS = [
                                    0  => ["clr" => "warning", "sts" => "Registered"],
                                    1  => ["clr" => "danger",  "sts" => "Disabled"],
                                    2  => ["clr" => "primary", "sts" => "Unverified"],
                                    -1 => ["clr" => "success", "sts" => "Verified"]
                                ];
                                echo '
                                    <div class="text-start">
                                        <span class="badge bg-'.($STATS[$RSLT_USER["MBR_STS"]]["clr"] ??  'dark').'">'.($STATS[$RSLT_USER["MBR_STS"]]["sts"] ?? 'Unknown').'</span>
                                    </div>
                                ';
                            ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-3"></div>
</div>