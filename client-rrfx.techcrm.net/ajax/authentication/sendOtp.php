<?php
use App\Models\Helper;
use App\Factory\VerihubFactory;

$verihub = VerihubFactory::init();
$data = Helper::getSafeInput($_POST);
$required = [
    'phone_code' => "Phone Code",
    'phone' => "Phone Number"
];

foreach($required as $req => $text) {
    if(empty($data[$req])) {
        JsonResponse([
            'success' => false,
            'message' => "{$text} is required",
            'data' => []
        ]);
    }
} 

// $type = Helper::form_input($_POST['type'] ?? "");
// $type = strtolower($type);
$type = "sms";
if(empty($type) || !in_array($type, ['whatsapp', 'sms'])) {
    JsonResponse([
        'success' => false,
        'message' => "Invalid Type",
        'data' => []
    ]);
}

$otp = random_int(1000, 9999);
$sendOtp = false;
switch($type) {
    case "whatsapp":
        // $sendOtp = Zenziva::sendOtp_WaReguler($user['MBR_PHONE'], $otp);
        $sendOtp = false;
        break;

    case "sms":
        $phone = $verihub->phoneValidation($data['phone_code'], $data['phone']);
        if(!$phone){
            JsonResponse([
                'success' => false,
                'message' => "Invalid Phone Number",
                'data' => []
            ]);
        }

        /** Check Terakhir Mengirim */
        $sqlCheckOtp = $db->query("SELECT LOGVER_DATA, LOGVER_DATETIME FROM tb_log_verihub WHERE LOGVER_MBR = {$phone} AND LOGVER_MODULE = '/v1/otp/send' ORDER BY ID_LOGVER DESC LIMIT 1");
        if($sqlCheckOtp->num_rows == 1) {
            $timelimit = 90;
            $timenow = time();
            $logver = $sqlCheckOtp->fetch_assoc();
            $logverdata = json_decode($logver['LOGVER_DATA'], true);
            if($logverdata['time_limit']) {
                $timelimit = $logverdata['time_limit'];
            }

            $delay = strtotime("+{$timelimit} second", strtotime($logver['LOGVER_DATETIME']));
            if($delay > $timenow) {
                JsonResponse([
                    'success' => false,
                    'message' => "You have to wait a few minutes to send again",
                    'data' => []
                ]);
            }
        }

        $sendOtp = $verihub->sendOtp_sms(['phone' => $phone, 'otp' => $otp]);
        break;
}

if(!$sendOtp) {
    JsonResponse([
        'success' => false,
        'message' => "Invalid Status",
        'data' => []
    ]);
}

if(!$sendOtp['success']) {
    JsonResponse([
        'success' => false,
        'message' => $sendOtp['message'],
        'data' => []
    ]);
}

JsonResponse([
    'success' => true,
    'message' => "OTP code was successfully sent",
    'data' => [
        'delay' => 90
    ]
]);
