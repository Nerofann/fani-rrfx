<?php

use App\Models\Helper;
use App\Models\Product;
use Config\Core\Database;

if(!$adminPermissionCore->hasPermission($authorizedPermission, $url)) {
    JsonResponse([
        'code'      => 200,
        'success'   => false,
        'message'   => "Authorization Failed",
        'data'      => []
    ]);
}

$data = Helper::getSafeInput($_POST);
$required = [
    'suffix' => "Suffix",
    'name' => "Name",
    'type' => "Type",
    'rate' => "Rate",
    'type_as' => "SPA/MULTI",
    'leverage' => "Leverage",
    'group' => "Group",
    'minimum_deposit' => "Minimum Margin",
    'maximum_deposit' => "Maximum Margin",
    'minimum_topup' => "Minimum Deposit",
    'maximum_topup' => "Maximum Deposit",
    'minimum_withdrawal' => "Minimum Withdrawal",
    'maximum_withdrawal' => "Maximum Withdrawal",
];

foreach($required as $req => $text) {
    if(empty($data[ $req ])) {
        JsonResponse([
            'success' => false,
            'message' => "{$text} field is required",
            'data' => []
        ]);
    }
}

/** validasi suffix */
$accountSuffix = Product::findBySuffix($data['suffix']);
if($accountSuffix) {
    JsonResponse([
        'success' => false,
        'message' => "Suffix already used",
        'data' => []
    ]);
}

/** validasi type */
$data['type'] = strtoupper($data['type']);
if(!in_array($data['type'], Product::$type)) {
    JsonResponse([
        'success' => false,
        'message' => "Invalid Type, must be " . implode(", ", Product::$type),
        'data' => []
    ]);
}

/** validasi type as */
$data['type_as'] = strtoupper($data['type_as']);
if(!in_array($data['type_as'], Product::$typeAs)) {
    JsonResponse([
        'success' => false,
        'message' => "Invalid Type, must be " . implode(", ", Product::$typeAs),
        'data' => []
    ]);
}

/** validasi rate */
$data['rate'] = strtoupper($data['rate']);
if(!in_array($data['rate'], Product::$rates)) {
    JsonResponse([
        'success' => false,
        'message' => "Invalid Rate",
        'data' => []
    ]);
}

/** validasi max account */
if(is_numeric($data['max_account']) === FALSE) {
    JsonResponse([
        'success' => false,
        'message' => "Invalid Max Account",
        'data' => []
    ]);
}

/** validasi komisi */
$commission = Helper::stringTonumber($data['commission']);
if(is_numeric($commission) === FALSE && $commission < 0) {
    JsonResponse([
        'success' => false,
        'message' => "Invalid Commission",
        'data' => []
    ]);
}

/** validasi leverage */
$leverage = Helper::stringTonumber($data['leverage']);
if(is_numeric($leverage) === FALSE && $leverage <= 0) {
    JsonResponse([
        'success' => false,
        'message' => "Invalid Leverage",
        'data' => []
    ]);
}

/** validasi minimum margin */
$minimumMargin = Helper::stringTonumber($data['minimum_deposit']);
if(is_numeric($minimumMargin) === FALSE && $minimumMargin < 0) {
    JsonResponse([
        'success' => false,
        'message' => "Invalid Minimum Deposit",
        'data' => []
    ]);
}

/** validasi maximum margin */
$maximumMargin = Helper::stringTonumber($data['maximum_deposit']);
if(is_numeric($maximumMargin) === FALSE && $maximumMargin < 0) {
    JsonResponse([
        'success' => false,
        'message' => "Invalid Maximum Deposit",
        'data' => []
    ]);
}

/** validasi minimum deposit */
$minimumDeposit = Helper::stringTonumber($data['minimum_topup']);
if(is_numeric($minimumDeposit) === FALSE && $minimumDeposit < 0) {
    JsonResponse([
        'success' => false,
        'message' => "Invalid Minimum Deposit",
        'data' => []
    ]);
}

/** validasi maximum deposit */
$maximumDeposit = Helper::stringTonumber($data['maximum_topup']);
if(is_numeric($maximumDeposit) === FALSE && $maximumDeposit < 0) {
    JsonResponse([
        'success' => false,
        'message' => "Invalid Maximum Deposit",
        'data' => []
    ]);
}

/** validasi minimum withdrawal */
$minimumWithdrawal = Helper::stringTonumber($data['minimum_withdrawal']);
if(is_numeric($minimumWithdrawal) === FALSE && $minimumWithdrawal < 0) {
    JsonResponse([
        'success' => false,
        'message' => "Invalid Minimum Withdrawal",
        'data' => []
    ]);
}

/** validasi maximum withdrawal */
$maximumWithdrawal = Helper::stringTonumber($data['maximum_withdrawal']);
if(is_numeric($maximumWithdrawal) === FALSE && $maximumWithdrawal < 0) {
    JsonResponse([
        'success' => false,
        'message' => "Invalid Maximum Withdrawal",
        'data' => []
    ]);
}

/** Insert Product */
$rate = $data['rate'] == "FLOATING"? 0 : $data['rate'];
$isFloating = $rate == 0? 1 : 0;

/** validasi currency */
$currency = !empty($data['currency'])? strtoupper($data['currency']) : (($isFloating)? "USD" : "IDR");
$currencySymbol = ($currency == "IDR")? "Rp" : "$";
if(!in_array($currency, Product::$currency)) {
    JsonResponse([
        'success' => false,
        'message' => "Invalid Currency",
        'data' => []
    ]);
}

$insertProduct = Database::insert("tb_racctype", [
    'RTYPE_SUFFIX' => $data['suffix'],
    'RTYPE_NAME' => $data['name'],
    'RTYPE_TYPE_AS' => $data['type_as'],
    'RTYPE_TYPE' => $data['type'],
    'RTYPE_RATE' => $rate,
    'RTYPE_MAXACC' => $data['max_account'],
    'RTYPE_ISFLOATING' => $isFloating,
    'RTYPE_CURR' => $currency,
    'RTYPE_CURR_SYMBOL' => $currencySymbol,
    'RTYPE_MINDEPOSIT' => $minimumMargin,
    'RTYPE_MAXDEPOSIT' => $maximumMargin,
    'RTYPE_MINTOPUP' => $minimumDeposit,
    'RTYPE_MAXTOPUP' => $maximumDeposit,
    'RTYPE_MINWITHDRAWAL' => $minimumWithdrawal,
    'RTYPE_MAXWITHDRAWAL' => $maximumWithdrawal,
    'RTYPE_LEVERAGE' => $leverage,
    'RTYPE_KOMISI' => $commission,
    'RTYPE_GROUP' => $data['group'],
    'RTYPE_STS' => -1,
]);

if(!$insertProduct) {
    JsonResponse([
        'success' => false,
        'message' => "Failed to create product",
        'data' => []
    ]);
}


$permisView = $adminPermissionCore->isHavePermission($permission['module_id'], "view");
JsonResponse([
    'success' => true,
    'message' => "Successfull",
    'data' => [
        'redirect' => $permisView['link'] ?? "/tools/product/view"
    ]
]);