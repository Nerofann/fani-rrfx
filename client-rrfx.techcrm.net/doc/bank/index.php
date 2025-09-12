<?php $_SESSION['modal'] = ['update-bank', 'otp-bank']; 

use App\Models\MemberBank;
use App\Models\FileUpload;?>
<div class="dashboard-breadcrumb mb-25">
    <div class="d-flex align-items-center">
        <h2 class="mb-0">Daftar Bank</h2>
    </div>
</div>

<div class="row">
    <?php require_once __DIR__ . "/create.php"; ?>

    <div class="col-md-8 mb-3">
        <div class="panel">
            <div class="panel-body">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-dashed table-hover digi-dataTable dataTable-resize table-striped" id="table-bank">
                                <thead>
                                    <tr class="text-center">
                                        <th class="text-center">Tanggal Dibuat</th>
                                        <th class="text-center">Rekening</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">#</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach(App\Models\User::myBank($user['MBR_ID']) as $bank) : ?>
                                        <tr>
                                            <td><?= date('Y-m-d H:i:s', strtotime($bank['MBANK_DATETIME'])); ?></td>
                                            <td class="text-start">
                                                <p class="mb-0"><?= $bank['MBANK_NAME'] ?></p>
                                                <p class="mb-0"><?= $bank['MBANK_HOLDER'] ?> / <?= $bank['MBANK_ACCOUNT'] ?></p>
                                            </td>
                                            <td><?= MemberBank::status($bank['MBANK_STS'])['html']; ?></td>
                                            <td><a href="javascript:void(0)" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modal-edit-bank" data-nama="<?= $bank['MBANK_NAME'] ?>" data-rekening="<?= $bank['MBANK_ACCOUNT'] ?>" data-image="<?= FileUpload::awsFile($bank['MBANK_IMG']); ?>" data-id="<?= md5(md5($bank['ID_MBANK'])) ?>"><i class="fas fa-edit"></i></a></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $('#table-bank').DataTable({
            scrollX: true,
            processing: true,
            order: [[0, 'desc']],
        });
    })
</script>