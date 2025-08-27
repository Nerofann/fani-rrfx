<?php
    
    use App\Models\Account;
    use App\Models\Dpwd;
    use App\Models\Helper;
    use App\Models\FileUpload;
    $data = Helper::getSafeInput($_GET);
    $id_acc = $data["d"];
    $COMPANY = App\Models\CompanyProfile::$name;
    $progressAccount = Account::realAccountDetail($data["d"]);
    $progressAccount = array_merge((Account::accoundCondition($progressAccount["ID_ACC"]) ?? []), $progressAccount);
    $depositData = Dpwd::findByRaccId($progressAccount["ID_ACC"]);
    $page_title = 'Progress Real Account';
    $web_name_full = $COMPANY;
    $userBanks = (!empty($progressAccount["MBR_BKJSN"])) ? json_decode($progressAccount["MBR_BKJSN"], true) : [];
    $date_month = Helper::bulan(date("m"));
    $accountCondition = Account::accoundCondition($progressAccount['ID_ACC']);

    if((!$depositData) || (!$progressAccount)){
        die("<script>alert('Invalid Account');location.href = '/account/progress_real_account/view'</script>");
    }

    if(!$accountCondition) {
        die("<script>alert('Invalid Account Condition');location.href = '/account/progress_real_account/view'</script>");
    }

    /** explode bank admin */
    $bankAdmin = explode("/", $depositData['DPWD_BANK']);

    /** explode bank nasabah */
    $userBank = explode("/", $depositData['DPWD_BANKSRC']);

    
    
    $amountIDR = ($depositData["DPWD_CURR_FROM"] == "IDR") ? $depositData['DPWD_AMOUNT_SOURCE'] : $depositData['DPWD_AMOUNT'];
    $amountUSD = ($depositData["DPWD_CURR_FROM"] == "USD") ? $depositData['DPWD_AMOUNT_SOURCE'] : $depositData['DPWD_AMOUNT'];
?>
<div class="page-header">
	<div>
		<h2 class="main-content-title tx-24 mg-b-5">Dealer</h2>
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="javascript:void(0);">Account</a></li>
			<li class="breadcrumb-item"><a href="javascript:void(0);"><?php echo $page_title; ?></a></li>
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
                        <div class="col-md-3 mb-2 mt-auto"><label for="" class="form-label">Kondisi ini efektif bulan</label></div>
                        <div class="col-md-9 mb-2"><input type="text" class="form-control" readonly value="<?php echo date('m');?> (<?php echo $date_month ;?>)" required></div>
                    
                        <div class="col-md-3 mb-2 mt-auto"><label for="" class="form-label">No. Account</label></div>
                        <div class="col-md-9 mb-2"><input type="text" class="form-control" readonly value="<?php echo $progressAccount['ACCCND_LOGIN']; ?>" required></div>
    
                        <div class="col-md-3 mb-2 mt-auto">Nama Investor</div>
                        <div class="col-md-9 mb-2"><input type="text" class="form-control" value="<?php echo $progressAccount['MBR_NAME'] ?>" readonly></div>
    
                        <div class="col-md-3 mb-2 mt-auto">E-Mail Investor</div>
                        <div class="col-md-9 mb-2"><input type="text" class="form-control" value="<?php echo $progressAccount['MBR_EMAIL'] ?>" readonly></div>
    
                        <div class="col-md-3 mb-2 mt-auto">No.Telp</div>
                        <div class="col-md-9 mb-2"><input type="text" class="form-control" value="<?php echo $progressAccount['MBR_PHONE'] ?>" readonly></div>
                    </div>
    
                    <div class="row">
                        <div class="col-md-3 mb-2 mt-auto">Tanggal Deposit</div>
                        <div class="col-md-2 mb-2"><input type="number" class="form-control" value="<?php echo date('d');?>" name="" required></div>
    
                        <div class="col-md-4 mb-2">
                            <div class="input-group">
                                <span class="input-group-text">Bulan</span>
                                <select name="" id="" class="form-control" required>
                                    <option value="01" <?php if(date('m') == '01'){echo 'selected';}?> >1 (Januari)</option>
                                    <option value="02" <?php if(date('m') == '02'){echo 'selected';}?> >2 (Februari)</option>
                                    <option value="03" <?php if(date('m') == '03'){echo 'selected';}?> >3 (Maret)</option>
                                    <option value="04" <?php if(date('m') == '04'){echo 'selected';}?> >4 (April)</option>
                                    <option value="05" <?php if(date('m') == '05'){echo 'selected';}?> >5 (Mei)</option>
                                    <option value="06" <?php if(date('m') == '06'){echo 'selected';}?> >6 (Juni)</option>
                                    <option value="07" <?php if(date('m') == '07'){echo 'selected';}?> >7 (Juli)</option>
                                    <option value="08" <?php if(date('m') == '08'){echo 'selected';}?> >8 (Agustus)</option>
                                    <option value="09" <?php if(date('m') == '09'){echo 'selected';}?> >9 (September)</option>
                                    <option value="10" <?php if(date('m') == '10'){echo 'selected';}?> >10 (Oktober)</option>
                                    <option value="11" <?php if(date('m') == '11'){echo 'selected';}?> >11 (November)</option>
                                    <option value="12" <?php if(date('m') == '12'){echo 'selected';}?> >12 (Desember)</option>
                                </select>
                            </div>
                        </div>
    
                        <div class="col-md-3 mb-2">
                            <div class="input-group">
                                <span class="input-group-text">Tahun</span>
                                <input type="number" class="form-control" value="<?php echo date('Y');?>" name="" required>
                            </div>
                        </div>
                    </div>
    
                    <div class="row">
                        <div class="col-md-3 mb-2 mt-auto">Nilai Deposit</div>
                        <div class="col-md-4 mb-2">
                            <div class="input-group">
                                <span class="input-group-text"><?php echo ($progressAccount['RTYPE_CURR']); ?></span>
                                <input type="text" class="form-control" name="initial_margin" id="rupiah" value="<?php echo Helper::formatCurrency($depositData["DPWD_AMOUNT_SOURCE"]); ?>" readonly required>
                            </div>
                        </div>
    
                        <div class="col-md-4 mb-2">
                            <div class="input-group">
                                <span class="input-group-text">Fixed Rate</span>
                                <input type="text" class="form-control text-center" name="margin" value="<?php echo number_format($progressAccount['RTYPE_RATE'], 0) ?>" readonly required>

                                <!-- <select name="fix_rate" id="" class="form-control text-center" required>
                                    <option value="10.000" <?php echo ($progressAccount['RTYPE_RATE'] == '10000') ? 'selected' : ""; ?>>10.000</option>
                                    <option value="14.000" <?php echo ($progressAccount['RTYPE_RATE'] == '14000') ? 'selected' : ""; ?>>14.000</option>
                                    <option value="0" <?php echo ($progressAccount['RTYPE_RATE'] == '0') ? 'selected' : ""; ?>>Floating</option>
                                </select> -->
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3 mb-2 mt-auto">Nilai Margin</div>
                        <div class="col-md-9 mb-2">
                            <div class="input-group">
                                <span class="input-group-text">USD</span>
                                <input type="text" class="form-control" value="<?= Helper::formatCurrency($amountUSD); ?>" readonly required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-3 mb-2 mt-auto">Commission Charge</div>
                        <div class="col-md-4 mb-2">
                            <div class="input-group">
                                <span class="input-group-text">Forex</span>
                                <input type="text" class="form-control" value="<?php echo $progressAccount['RTYPE_KOMISI']?>" readonly required>
                            </div>
                        </div>

                        <!-- <div class="col-md-4 mb-2">
                            <div class="input-group">
                                <span class="input-group-text">Loco</span>
                                <input type="text" class="form-control" value="<?//php echo $progressAccount['ACCCND_CASH_LOCO']?>" readonly required>
                            </div>
                        </div> -->
                    </div>
    
                    <div class="row">
                        <div class="col-md-3 mb-2 mt-auto">Note</div>
                        <div class="col-md-9 mb-2">
                            <input type="text" class="form-control" value="<?php echo $note['NOTE_NOTE'] ?? "-" ?>" readonly required>
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