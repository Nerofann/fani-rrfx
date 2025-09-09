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

                                use App\Factory\VerihubFactory;

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
                        <td width="10%" class="text-start">Jenis Pekerjaan</td>
                        <td width="3%">:</td>
                        <td class="text-start" colspan="4"><?= $progressAccount['ACC_F_APP_KRJ_NAMA'] ?></td>
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

        <div class="table-responsive">
            <table class="table table-hover table-bordered">
                <thead>
                    <tr>
                        <th colspan="3" class="bg-secondary text-muted">Log Verihub</th>
                    </tr>
                    <tr>
                        <th width="25%">Tanggal</th>
                        <th width="10%">Kode</th>
                        <th>Message</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach(VerihubFactory::findAccountLog($progressAccount['ACC_MBR'], md5($progressAccount['ID_ACC'])) as $logVer) : ?>
                        <tr>
                            <td><?= $logVer['LOGVER_DATETIME'] ?></td>
                            <td><?= $logVer['LOGVER_CODE'] ?></td>
                            <td><?= $logVer['LOGVER_MESSAGE'] ?></td>
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
                    <a target="_blank" href="<?php echo App\Models\FileUpload::awsFile($progressAccount['ACC_F_APP_FILE_IMG']); ?>">
                        <img width="75%" src="<?php echo App\Models\FileUpload::awsFile($progressAccount['ACC_F_APP_FILE_IMG']); ?>">
                    </a>
                    <div class="card-body">
                        <a target="_blank" href="<?php echo App\Models\FileUpload::awsFile($progressAccount['ACC_F_APP_FILE_IMG']); ?>">
                            Rekening Koran Bank / Tagihan Kartu Kredit
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <div class="card text-center h-100">
                    <a target="_blank" href="<?php echo App\Models\FileUpload::awsFile($progressAccount['ACC_F_APP_FILE_IMG2']); ?>">
                        <img width="75%" src="<?php echo App\Models\FileUpload::awsFile($progressAccount['ACC_F_APP_FILE_IMG2']); ?>">
                    </a>
                    <div class="card-body">
                        <a target="_blank" href="<?php echo App\Models\FileUpload::awsFile($progressAccount['ACC_F_APP_FILE_IMG2']); ?>">
                            Rekening Listrik / Telepon
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <div class="card text-center h-100">
                    <a target="_blank" href="<?php echo App\Models\FileUpload::awsFile($progressAccount['ACC_F_APP_FILE_NPWP']); ?>">
                        <img width="75%" src="<?php echo App\Models\FileUpload::awsFile($progressAccount['ACC_F_APP_FILE_NPWP']); ?>">
                    </a>
                    <div class="card-body">
                        <a target="_blank" href="<?php echo App\Models\FileUpload::awsFile($progressAccount['ACC_F_APP_FILE_NPWP']); ?>">
                            NPWP
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <div class="card text-center h-100">
                    <a target="_blank" href="<?php echo App\Models\FileUpload::awsFile($progressAccount['ACC_F_APP_FILE_FOTO']); ?>">
                        <img width="75%" src="<?php echo App\Models\FileUpload::awsFile($progressAccount['ACC_F_APP_FILE_FOTO']); ?>">
                    </a>
                    <div class="card-body">
                        <a target="_blank" href="<?php echo App\Models\FileUpload::awsFile($progressAccount['ACC_F_APP_FILE_FOTO']); ?>">
                            Foto Terbaru (Selfie)
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <div class="card text-center h-100">
                    <a target="_blank" href="<?php echo App\Models\FileUpload::awsFile($progressAccount['ACC_F_APP_FILE_ID']); ?>">
                        <img width="75%" src="<?php echo App\Models\FileUpload::awsFile($progressAccount['ACC_F_APP_FILE_ID']); ?>">
                    </a>
                    <div class="card-body">
                        <a target="_blank" href="<?php echo App\Models\FileUpload::awsFile($progressAccount['ACC_F_APP_FILE_ID']); ?>">
                            Foto Identitas
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <div class="card text-center h-100">
                    <a target="_blank" href="<?php echo App\Models\FileUpload::awsFile($progressAccount['ACC_F_APP_FILE_IMG3']); ?>">
                        <img width="75%" src="<?php echo App\Models\FileUpload::awsFile($progressAccount['ACC_F_APP_FILE_IMG3']); ?>">
                    </a>
                    <div class="card-body">
                        <a target="_blank" href="<?php echo App\Models\FileUpload::awsFile($progressAccount['ACC_F_APP_FILE_IMG3']); ?>">
                            Dokumen Lainnya
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <div class="card text-center h-100">
                    <a target="_blank" href="<?php echo App\Models\FileUpload::awsFile($progressAccount['ACC_F_APP_FILE_IMG4']); ?>">
                        <img width="75%" src="<?php echo App\Models\FileUpload::awsFile($progressAccount['ACC_F_APP_FILE_IMG4']); ?>">
                    </a>
                    <div class="card-body">
                        <a target="_blank" href="<?php echo App\Models\FileUpload::awsFile($progressAccount['ACC_F_APP_FILE_IMG4']); ?>">
                            Dokumen Lainnya
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>