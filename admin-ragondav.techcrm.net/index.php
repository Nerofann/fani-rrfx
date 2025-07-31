<?php 
require_once __DIR__ . "/../config/setting.php";

use App\Models\CompanyProfile;
use App\Models\Helper;
use App\Models\Database;

$queryParam = Helper::getSafeInput($_GET);
$authPage = $queryParam['a'] ?? "";
if(empty($authPage)) {
	$authPage = "signin";
}
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta content="width=device-width, initial-scale=1, shrink-to-fit=no" name="viewport">
        <meta name="description" content="Gfsprime">
        <meta name="author" content="Gfsprime">
        <meta name="keywords" content="Gfsprime">
        
        <!-- TITLE -->
        <title><?= ucwords($authPage) ?> - <?= CompanyProfile::$name ?></title>

        <!-- FAVICON -->
        <link rel="icon" href="/assets/img/brand/favicon.ico">
		<!-- BOOTSTRAP CSS -->
		<link  id="style" href="/assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet">
		<!-- ICONS CSS -->
		<link href="/assets/plugins/web-fonts/icons.css" rel="stylesheet">
		<link href="/assets/plugins/web-fonts/font-awesome/font-awesome.min.css" rel="stylesheet">
		<link href="/assets/plugins/web-fonts/plugin.css" rel="stylesheet">

		<!-- STYLE CSS -->
		<link href="/assets/css/style.css" rel="stylesheet">
		<link href="/assets/css/plugins.css" rel="stylesheet">

		<!-- SWITCHER CSS -->
		<link href="/assets/switcher/css/switcher.css" rel="stylesheet">
		<link href="/assets/switcher/demo.css" rel="stylesheet">

        <!-- JQUERY JS -->
        <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
        <!-- sweetalert2 -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    </head>

    <body class="ltr main-body leftmenu error-1">
        <!-- LOADEAR -->
		<!-- <div id="global-loader">
			<img src="/assets/img/loader.svg" class="loader-img" alt="Loader">
		</div> -->
		<!-- END LOADEAR -->

        <!-- END PAGE -->
        <div class="page main-signin-wrapper">
            <?php require_once __DIR__ . "/auth/$authPage.php"; ?>
        </div>
		<!-- END PAGE -->

        <!-- SCRIPTS -->
		<!-- BOOTSTRAP JS -->
		<script src="/assets/plugins/bootstrap/js/popper.min.js"></script>
		<script src="/assets/plugins/bootstrap/js/bootstrap.min.js"></script>

		<!-- PERFECT SCROLLBAR JS -->
		<script src="/assets/plugins/perfect-scrollbar/perfect-scrollbar.min.js"></script>

		<!-- SELECT2 JS -->
		<script src="/assets/plugins/select2/js/select2.min.js"></script>
		<script src="/assets/js/select2.js"></script>
        
        <!-- COLOR THEME JS -->
		<script src="/assets/js/themeColors.js"></script>

        <!-- CUSTOM JS -->
        <script src="/assets/js/custom.js"></script>
    </body>
</html>
