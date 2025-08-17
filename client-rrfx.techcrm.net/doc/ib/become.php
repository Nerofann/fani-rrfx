<?php

use App\Models\Account;
use App\Models\User;

$standartAccount = Account::haveStandartAccount($user['userid']);
$terms_balance = false;
$ibData = User::get_ib_data($user['MBR_ID']);

foreach($standartAccount as $sac) {
    if($detail->message->MARGIN_FREE >= 100) {
        $terms_balance = true;
        break;
    }
}
?>
<pre>
    <?php print_r($standartAccount); ?>
</pre>
<div class="row">
    <div class="col-md-12 mb-25">
        <div class="dashboard-breadcrumb mb-25">
            <h2>Become IB</h2>
            <div class="input-group-a dashboard-filter">
            </div>
        </div>
    </div>

    <?php if($ibData && $ibData['BECOME_STS'] == -1) : ?>
        <div class="col-md-6 m-auto mb-25">
            <div class="panel">
                <div class="panel-header">
                    <h5>Already Become IB</h5>
                </div>
                <div class="panel-body">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <td width="40%">Date Request</td>
                                <td width="60%"><?= date("Y-m-d H:i:s", strtotime($ibData['BECOME_DATETIME'])); ?></td>
                            </tr>
                            <tr>
                                <td width="40%">Date Processed</td>
                                <td width="60%"><?= date("Y-m-d H:i:s", strtotime($ibData['BECOME_TIMESTAMP'])); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    <?php else : ?>
        <?php $havePendingIb = (is_array($ibData) && $ibData['BECOME_STS'] == 0); ?>
        <div class="col-md-6 m-auto mb-25">
            <div class="panel">
                <div class="panel-header">
                    <h5>Terms & Conditions</h5>
                </div>
                <div class="panel-body">
                    <form action="" method="post" id="form-request-ib">
                        <div class="mb-25">
                            <ul>
                                <li>
                                    <?php if(count($standartAccount) >= 1 || $havePendingIb) : ?>
                                        <i class="fas fa-check text-success"></i>
                                    <?php else : ?> 
                                        <i class="fas fa-x text-danger"></i>
                                    <?php endif; ?>
                                    Have a standard account
                                </li>
                                <li>
                                    <?php if($terms_balance === TRUE || $havePendingIb) : ?>
                                        <i class="fas fa-check text-success"></i>
                                    <?php else : ?> 
                                        <i class="fas fa-x text-danger"></i>
                                    <?php endif; ?>
                                    Have at least $100 free margin in real accounts
                                </li>
                            </ul>
                        </div>
    
                        <?php if(empty($ibData) || $ibData['BECOME_STS'] == 1) : ?>
                            <?php if($ibData['BECOME_STS'] == 1) : ?>
                                <div class="alert alert-danger">
                                    <small>Permintaan anda sebelumnya telah ditolak, keterangan: </small>
                                    <br>
                                    <small><?= $ibData['BECOME_NOTE'] ?></small>
                                </div>
                            <?php endif; ?>
    
                            <div class="d-flex justify-content-between mb-25">
                                <div class="form-check">
                                    <input class="form-check-input" name="terms" type="checkbox" required>
                                    <label class="form-check-label text-white" for="loginCheckbox">
                                        I agree <a href="#" class="text-white text-decoration-underline">Terms & Policy</a>
                                    </label>
                                </div>
                            </div>
    
                            <button type="submit" class="btn btn-primary" <?= ($terms_balance === TRUE && count($standartAccount) >= 1)? "" : "disabled" ?>>Request Become IB</button>
    
                        <?php elseif($ibData['BECOME_STS'] == 0) : ?>
                            <button type="button" class="btn btn-info" disabled>In review</button>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>

        <script type="text/javascript">
            $(document).ready(function() {
                $('#form-request-ib').on('submit', function(event) {
                    event.preventDefault();
        
                    Swal.fire({
                        title: "Are you sure to continue?",
                        icon: 'question',
                        showCancelButton: true
                    }).then((result) => {
                        if(result.value) {
                            Swal.fire({
                                text: "Please wait...",
                                didOpen: function() {
                                    Swal.showLoading();
                                }
                            })
        
                            $.post("/ajax/post/becomeIb", Object.fromEntries(new FormData(this).entries()) , function(resp) {
                                if(!resp.success) {
                                    Swal.fire("Failed", resp.error, "error");
                                    return false;
                                }
                
                                location.reload();
                            }, 'json')
                        }
                    })
                })
            })
        </script>
    <?php endif; ?>
</div>