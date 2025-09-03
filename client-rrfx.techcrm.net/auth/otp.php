<?php

use App\Models\Helper;
use App\Models\User;

$uniqueCode = Helper::form_input($_GET['b'] ?? "");
$sqlGet = $db->query("SELECT * FROM tb_member WHERE MD5(MD5(CONCAT(MBR_ID, ID_MBR))) = '$uniqueCode' AND MBR_STS = 0 LIMIT 1");
if($sqlGet->num_rows != 1) {
    User::logout();
    die("<script>alert('Invalid Code'); location.href='/';</script>");
}

$user = $sqlGet->fetch_assoc();
$split_email = str_split($user['MBR_EMAIL']);
$post_at = strpos($user['MBR_EMAIL'], "@");
$mask_email = "";

foreach ($split_email as $key => $em) {
    if($key == 0 || $key == (($post_at) - 1) || $key >= $post_at) {
        $mask_email .= $em;
    }
    
    else {
        $mask_email .= "*";
    }
}
?>
<!-- main content start -->
<div class="main-content login-panel two-factor-panel">
    <div class="container">
        <div class="row g-4 align-items-center">
            <div class="col-lg-6">
                <div class="text-lg-start text-center logo mb-4">
                    <img src="/assets/images/logo-white-new.png" alt="logo">
                </div>
                <p class="text-lg-start text-center mb-lg-0 mb-4">It's the Bright One, it's the Right One, that's Business.</p>
            </div>
            <div class="col-lg-6">
                <div class="static-body">
                    <div class="panel bg-transparent">
                        <div class="panel-body">
                            <div class="part-img w-25 m-auto mb-lg-5 mb-4 px-lg-4">
                                <img src="/assets/images/phone.png" alt="image">
                            </div>
                            <div class="part-txt text-center">
                                <h2>Two-Factor Verification</h2>
                                <p class="mb-2">Enter the verification code we sent to</p>
                                <p class="fw-semibold fs-5 mb-lg-4 mb-0">
                                    <?php echo $mask_email ?>
                                </p>
                            </div>
                            <div class="verification-area text-center">
                                <div id="otp_target"></div>
                                <p class="mb-4">Type your 4 digit security code</p>
                                <a href="javascript:void(0)" id="resendcode" class="btn btn-sm btn-primary">resend code</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- main content end -->

<script src="/assets/vendor/js/otpdesigner.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#otp_target').otpdesigner({
            length: 4,
            onlyNumbers: true,
            typingDone: function(code) {
                Swal.fire({
                    text: "Please wait...",
                    allowOutsideClick: false,
                    didOpen: function() {
                        Swal.showLoading();
                    }
                })

                $.post("/ajax/auth/otp-verification", {code: '<?= $uniqueCode ?>', otp: code}, (resp) => {
                    Swal.fire(resp.alert).then(() => {
                        if(resp.success) {
                            location.href = resp.data.redirect;
                        }
                    })
                }, 'json');
            }
        })

        $('#resendcode').on('click', function() {
            $.post("/ajax/auth/resend-otp", {code: '<?= $uniqueCode ?>'}, (resp) => {
                Swal.fire(resp.alert).then()
            }, 'json')
        })
    })
</script>
