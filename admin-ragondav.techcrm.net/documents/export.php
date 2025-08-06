<?php 
require_once(__DIR__ . "/../../config/setting.php");

use App\Models\Account;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Models\Helper;

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
        require(__DIR__ . "/$filename.php");
        return ob_get_clean();

    } catch (Exception $e) {
        return false;
    }
}

try {
    $filename = Helper::form_input($_GET['filename'] ?? "-");
    if(!file_exists(__DIR__ . "/{$filename}.php")) {
        exit(json_encode([
            'success'	=> false,
            'message'	=> 'Invalid Route'
        ]));
    }

    $profile_perusahaan = App\Models\ProfilePerusahaan::get();
    $profile_perusahaan['setting_telp_pmbr'] = $setting_telp_pmbr ?? 0;

    $_GET['logo_pdf'] = "https://client-rrfx.techcrm.net/assets/images/logo-white-new.png";
    $_GET['profile_perusahaan'] = $profile_perusahaan;
    $_GET['company_name'] = $web_name ?? "PT Delapan Belas Berjangka";
    $_GET['company_address'] = $address_company ?? "-";
    $_GET['wpb'] = App\Models\ProfilePerusahaan::list_wpb() ?? [];
    $_GET['wpb_verifikator'] = App\Models\ProfilePerusahaan::wpb_verifikator() ?? [];

    $options = new Options();
    $options->set('isRemoteEnabled', true);
    $options->set('isHtml5ParserEnabled', true);
    $dompdf = new Dompdf($options);
    $html = parseHtmlText($filename, [...$_GET, 'dompdf' => $dompdf]);
    $dompdf->loadHtml($html);
    $dompdf->render();
    $dompdf->stream("{$filename}.pdf", array("Attachment" => 0));
	// $output = $dompdf->output();

} catch (Exception $e) {
    throw $e;
}