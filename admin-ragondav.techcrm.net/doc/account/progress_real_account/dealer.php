<?php
    
    use App\Models\Account;
    use App\Models\Dpwd;
    use App\Models\Helper;
    use App\Models\FileUpload;
    use App\Models\Regol;
    $data = Helper::getSafeInput($_GET);
    $id_acc = $data["d"];
    $COMPANY = App\Models\CompanyProfile::$name;
    $progressAccount = Account::realAccountDetail($data["d"]);
    $progressAccount = array_merge((Account::accoundCondition($progressAccount["ID_ACC"]) ?? []), $progressAccount);
    $page_title = 'Progress Real Account';
    $web_name_full = $COMPANY;
    $userBanks = (!empty($progressAccount["MBR_BKJSN"])) ? json_decode($progressAccount["MBR_BKJSN"], true) : [];
    $date_month = Helper::bulan(date("m"));
    $accountCondition = Account::accoundCondition($progressAccount['ID_ACC']);
    $lastNote = Regol::getAccountHistoryLastNote($progressAccount['ID_ACC']);

    if(!$progressAccount){
        die("<script>alert('Invalid Account');location.href = '/account/progress_real_account/view'</script>");
    }

    if(!$accountCondition) {
        die("<script>alert('Invalid Account Condition');location.href = '/account/progress_real_account/view'</script>");
    }
?>
<div class="page-header">
	<div>
		<h2 class="main-content-title tx-24 mg-b-5">Dealer</h2>
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="<?= pathbreadcrumb(0) ?>/dashboard">Home</a></li>
			<li class="breadcrumb-item">Account</li>
			<li class="breadcrumb-item"><a href="<?= pathbreadcrumb(2) ?>/view"><?php echo $page_title; ?></a></li>
			<li class="breadcrumb-item active">Dealer</li>
		</ol>
	</div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card mb-3">
            <div class="card-header  pb-2">
                <h5 class="card-title mb-0">
                    ACCOUNT CONDITION - <?= $progressAccount['RTYPE_TYPE'] ?>
                </h5>
                <div><span><i class="small text-muted">Commisson Charge</i></span></div>
            </div>

            <form method="post" id="dealer-form">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-2 mt-auto">Kondisi ini efektif bulan</div>
                        <div class="col-md-9 mb-2"><input type="text" class="form-control" readonly value="<?php echo date('m');?> (<?php echo $date_month ;?>)" required></div>
                    
                        <div class="col-md-3 mb-2 mt-auto">No. Account</div>
                        <div class="col-md-9 mb-2"><input type="text" class="form-control" readonly value="<?php echo $progressAccount['ACCCND_LOGIN']; ?>" required></div>
    
                        <div class="col-md-3 mb-2 mt-auto">Nama Investor</div>
                        <div class="col-md-9 mb-2"><input type="text" class="form-control" value="<?php echo $progressAccount['ACC_FULLNAME'] ?>" readonly></div>
    
                        <div class="col-md-3 mb-2 mt-auto">E-Mail Investor</div>
                        <div class="col-md-9 mb-2"><input type="text" class="form-control" value="<?php echo $progressAccount['MBR_EMAIL'] ?>" readonly></div>
    
                        <div class="col-md-3 mb-2 mt-auto">No.Telp</div>
                        <div class="col-md-9 mb-2"><input type="text" class="form-control" value="<?php echo $progressAccount['ACC_F_APP_PRIBADI_HP'] ?>" readonly></div>
                    </div>
    
                    <div class="row">
                        <div class="col-md-3 mb-2 mt-auto">Commission Charge</div>
                        <div class="col-md-4 mb-2">
                            <input type="text" class="form-control" value="<?php echo $progressAccount['RTYPE_KOMISI']?>" readonly required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3 mb-2 mt-auto">Rate</div>
                        <div class="col-md-4 mb-2">
                            <input type="text" class="form-control" value="<?= $progressAccount['RTYPE_ISFLOATING']? "Floating" : Helper::formatCurrency($progressAccount['RTYPE_RATE']) ?>" readonly required>
                        </div>
                    </div>
    
                    <div class="row">
                        <div class="col-md-3 mb-2 mt-auto">Note</div>
                        <div class="col-md-9 mb-2">
                            <input type="text" class="form-control" value="<?php echo $lastNote['NOTE_NOTE'] ?? "-" ?>" readonly required>
                        </div>
                    </div>
                </div>
    
                <div class="card-footer text-center">
                    <input type="hidden" name="sbmt_act" id="fld-act">
                    <input type="hidden" name="sbmt_id" id="sbmt_id" value="<?= $id_acc ?>">
                    <button type="submit" id="sbmtacc" style="display: none;"></button>
                    <button type="button" value="reject" data-act="reject" class="btn btn-danger act-btna">Reject</button>
                    <button type="button" value="accept" data-act="accept" class="btn btn-success act-btna">Accept</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(() => {
        $('.act-btna').on('click', function(e){
            $('#fld-act').val($(this).val());
            $('#sbmtacc').click();
        });
        $('#dealer-form').on('submit', function(e){
            e.preventDefault();

            let formData = new FormData(this);
            formData.append('password', $('#mt-pass').val());
            formData.append('investor', $('#mt-invstr').val());

            let data = Object.fromEntries(formData);

            
            let ARCBTN = {
                title: `${$('#fld-act').val().toUpperCase()} DATA`,
                text: `Berikan catatan sebelum ${$('#fld-act').val().toUpperCase()}`,
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
                data["sbmt_note"] = result.value;
                if(result.isConfirmed) {
                    $.post("/ajax/post/account/dealer_action", data, function(resp) {
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
    });
</script>