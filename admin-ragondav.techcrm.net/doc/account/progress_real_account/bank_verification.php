<?php
use App\Models\Account;
use App\Models\Helper;
use App\Models\FileUpload;
use App\Models\MemberBank;
$data = Helper::getSafeInput($_GET);
$id_acc = $data["d"] ?? "";

$COMPANY = App\Models\CompanyProfile::$name;
$page_title = 'Progress Real Account';
$web_name_full = $COMPANY;
$progressAccount = Account::realAccountDetail($id_acc);
if(!$progressAccount) {
    die("<script>alert('Invalid code'); location.href = '/account/progress_real_account/view'; </script>");
}

$userBanks = MemberBank::activeBanks($progressAccount['ACC_MBR']);
$permisAction = $adminPermissionCore->isHavePermission($moduleId, "action.bank_verification");
?>

<div class="page-header">
	<div>
		<h2 class="main-content-title tx-24 mg-b-5"><?php echo $page_title; ?></h2>
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="<?= pathbreadcrumb(0) ?>/dashboard">Home</a></li>
			<li class="breadcrumb-item">Account</li>
			<li class="breadcrumb-item"><a href="<?= pathbreadcrumb(2) ?>/view"><?php echo $page_title; ?></a></li>
			<li class="breadcrumb-item active">Bank Verification</li>
		</ol>
	</div>
</div>

<div class="card custom-card">
    <div class="card-header">
        <h5 class="card-title mb-0">Bank Verification</h5>
        <p class="text-muted card-sub-title">A/N <?php echo $progressAccount["ACC_FULLNAME"].' ('.$progressAccount["MBR_EMAIL"].')' ?> - <?= $progressAccount['RTYPE_TYPE'] ?></p>
    </div>
    <div class="card-body">
        <div class="row">
            <?php foreach($userBanks as $key => $bank) : ?>
                <div class="col-md-6">
                    <h5 class="text-decoration-underline">Bank <?= $key + 1 ?></h5>
                    <div class="form-group">
                        <label for="" class="form-control-label">Nama Bank</label>
                        <input type="text" class="form-control" value="<?= $bank['MBANK_NAME'] ?>" readonly>
                    </div>

                    <div class="form-group">
                        <label for="" class="form-control-label">Nama Pemilik Rekening</label>
                        <input type="text" class="form-control" value="<?= $bank['MBANK_HOLDER'] ?>" readonly>
                    </div>

                    <div class="form-group">
                        <label for="" class="form-control-label">Cover Buku Tabungan</label>
                        <input type="file" class="dropify" data-default-file="<?= FileUpload::awsFile($bank['MBANK_IMG']) ?>" disabled data-show-remove="false">
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="card-footer">
        <?php if($permisAction) : ?>
            <div class="d-flex gap-3 justify-content-center">
                <a class="btn text-white btn-danger px-5 btn-action" data-type="reject" data-id="<?= $id_acc ?>">Reject</a>
                <a class="btn text-white btn-success px-5 btn-action" data-type="accept" data-id="<?= $id_acc ?>">Accept</a>
            </div>

            <script type="text/javascript">
                $(document).ready(function() {
                    $('.btn-action').on('click', function(e) {
                        let target = $(e.currentTarget);
                        let type = target.data('type');
                        if(type) {
                            Swal.fire({
                                title: `${type.charAt(0).toUpperCase() + type.slice(1)} Member Bank`,
                                icon: "info",
                                input: "text",
                                inputLabel: "Note",
                                inputPlaceholder: "Tulis disini",
                                inputAttributes: {
                                    required: true
                                },
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
            
                                    $.post("/ajax/post<?= $permisAction['link'] ?>", {type: type, note: result.value, id: target.data('id')}, (resp) => {
                                        Swal.fire(resp.alert).then(() => {
                                            location.href = resp.data.redirect;
                                        })
                                    }, 'json')
                                }
                            })
                        }
                    })
                    $('#bank-verification').on('submit', function(event) {
                        event.preventDefault();
                        console.log(event)
                        let button = $(this).find('button[type="submit"]');
                        
                        // Swal.fire({
                        //     title: ""
                        // })
                    });
                })
            </script>
        <?php endif; ?>
    </div>  
</div>
