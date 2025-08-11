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
        <link rel="apple-touch-icon" sizes="57x57" href="../../assets/img/favicon/apple-icon-57x57.png">
        <link rel="apple-touch-icon" sizes="60x60" href="../../assets/img/favicon/apple-icon-60x60.png">
        <link rel="apple-touch-icon" sizes="72x72" href="../../assets/img/favicon/apple-icon-72x72.png">
        <link rel="apple-touch-icon" sizes="76x76" href="../../assets/img/favicon/apple-icon-76x76.png">
        <link rel="apple-touch-icon" sizes="114x114" href="../../assets/img/favicon/apple-icon-114x114.png">
        <link rel="apple-touch-icon" sizes="120x120" href="../../assets/img/favicon/apple-icon-120x120.png">
        <link rel="apple-touch-icon" sizes="144x144" href="../../assets/img/favicon/apple-icon-144x144.png">
        <link rel="apple-touch-icon" sizes="152x152" href="../../assets/img/favicon/apple-icon-152x152.png">
        <link rel="apple-touch-icon" sizes="180x180" href="../../assets/img/favicon/apple-icon-180x180.png">
        <link rel="icon" type="image/png" sizes="192x192"  href="../../assets/img/favicon/android-icon-192x192.png">
        <link rel="icon" type="image/png" sizes="32x32" href="../../assets/img/favicon/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="96x96" href="../../assets/img/favicon/favicon-96x96.png">
        <link rel="icon" type="image/png" sizes="16x16" href="../../assets/img/favicon/favicon-16x16.png">
        <link rel="manifest" href="../../assets/img/favicon/manifest.json">
        <meta name="msapplication-TileColor" content="#ffffff">
        <meta name="msapplication-TileImage" content="../../assets/img/favicon/ms-icon-144x144.png">
        <meta name="theme-color" content="#ffffff">
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
		<script>
			
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', function() {
                navigator.serviceWorker.register('/service-worker.js')
                    .then(function(registration) {
                    console.log('Service Worker registered with scope:', registration.scope);
                    }, function(err) {
                    console.error('Service Worker registration failed:', err);
                    });
                });
            } else {
                console.log('Service Worker is not supported in this browser.');
            }
		</script>
    </body>
</html>
