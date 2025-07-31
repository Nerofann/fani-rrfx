<?php 
require_once(__DIR__ . "/../../class/setting.php");
require_once(CONFIG_ROOT . "/vendor/autoload.php");
use Dompdf\Dompdf;
use Dompdf\Options;

if(!isset($_GET['filename'])) {
	exit(json_encode([
		'success'	=> false,
		'message'	=> 'Invalid Request'
	]));
}

function parseHtmlText(string $filename, array $data) {
    try {
        global $db;
        extract($data, EXTR_OVERWRITE);
        ob_start();
        require(CRM_ROOT . "/documents/$filename.php");
        return ob_get_clean();

    } catch (Exception $e) {
        return false;
    }
}

try {
    $filename = form_input($_GET['filename'] ?? "-");
    if(!file_exists(CRM_ROOT . "/documents/{$filename}.php")) {
        exit(json_encode([
            'success'	=> false,
            'message'	=> 'Invalid Route'
        ]));
    }

    $profile_perusahaan = profile_perusahaan();
    $profile_perusahaan['setting_telp_pmbr'] = $setting_telp_pmbr ?? 0;

    $_GET['logo_pdf'] = "https://cabinet-tridentprofutures.techcrm.net/assets/images/logo-email.png";
    $_GET['aws_folder'] = $aws_folder;
    $_GET['profile_perusahaan'] = $profile_perusahaan;
    $_GET['company_name'] = $web_name ?? "PT Tridentprofutures";
    $_GET['company_address'] = $address_company ?? "-";
    $_GET['wpb'] = list_wpb() ?? [];
    $_GET['wpb_verifikator'] = list_wpb(2) ?? [];

    loadModel("Account");
    loadModel("Helper");
    $classAcc = new Account();
    $helperClass = new Helper();

    $options = new Options();
    $options->set('isRemoteEnabled', true);
    $options->set('isHtml5ParserEnabled', true);
    $dompdf = new Dompdf($options);
    $html = parseHtmlText($filename, [...$_GET, 'dompdf' => $dompdf, 'classAcc' => $classAcc, 'helperClass' => $helperClass]);
    $dompdf->loadHtml($html);
    $dompdf->render();
    $dompdf->stream("{$filename}.pdf", array("Attachment" => 0));
	// $output = $dompdf->output();

} catch (Exception $e) {
    throw $e;
}