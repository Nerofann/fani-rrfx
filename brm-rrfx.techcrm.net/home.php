<?php
require_once __DIR__ . "/../config/setting.php";
use App\Models\Helper;
use App\Models\Admin;
use App\Models\CompanyProfile;
use Config\Core\Database;
use App\Factory\AdminPermissionFactory;

$queryParam = Helper::getSafeInput($_GET);
$page = $queryParam['a'] ?? "";
if($page == "logout") {
	Admin::logout();
    die("<script>location.href = '/';</script>");
}

/** Authentication */
$user = Admin::authentication();
if(empty($user)) {
    die("<script>alert('Invalid Session, please re-login'); location.href = '/';</script>");
}

/** update token expired */
$userid = md5(md5($user['ADM_ID']));
$newExpired = date("Y-m-d H:i:s", strtotime("+1 hour"));
Database::update("tb_admin", ['ADM_TOKEN_EXPIRED' => $newExpired], ['ADM_ID' => $user['ADM_ID']]);

?>
<!DOCTYPE html>
<html lang="en">
	<head>

        <meta charset="utf-8">
		<meta content="width=device-width, initial-scale=1, shrink-to-fit=no" name="viewport">
        <meta name="description" content="<?= CompanyProfile::$name ?>">
        <meta name="author" content="<?= CompanyProfile::$name ?>">
        <meta name="keywords" content="<?= CompanyProfile::$name ?>">
        
        <!-- TITLE -->
        <title> <?= ucwords($page) ?> - <?= CompanyProfile::$name ?> </title>

        <!-- FAVICON -->
        <link rel="apple-touch-icon" sizes="57x57" href="/assets/img/favicon/apple-icon-57x57.png">
        <link rel="apple-touch-icon" sizes="60x60" href="/assets/img/favicon/apple-icon-60x60.png">
        <link rel="apple-touch-icon" sizes="72x72" href="/assets/img/favicon/apple-icon-72x72.png">
        <link rel="apple-touch-icon" sizes="76x76" href="/assets/img/favicon/apple-icon-76x76.png">
        <link rel="apple-touch-icon" sizes="114x114" href="/assets/img/favicon/apple-icon-114x114.png">
        <link rel="apple-touch-icon" sizes="120x120" href="/assets/img/favicon/apple-icon-120x120.png">
        <link rel="apple-touch-icon" sizes="144x144" href="/assets/img/favicon/apple-icon-144x144.png">
        <link rel="apple-touch-icon" sizes="152x152" href="/assets/img/favicon/apple-icon-152x152.png">
        <link rel="apple-touch-icon" sizes="180x180" href="/assets/img/favicon/apple-icon-180x180.png">
        <link rel="icon" type="image/png" sizes="192x192"  href="/assets/img/favicon/android-icon-192x192.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/assets/img/favicon/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="96x96" href="/assets/img/favicon/favicon-96x96.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/assets/img/favicon/favicon-16x16.png">
        <link rel="manifest" href="/assets/img/favicon/manifest.json">
        <meta name="msapplication-TileColor" content="#ffffff">
        <meta name="msapplication-TileImage" content="/assets/img/favicon/ms-icon-144x144.png">
        <meta name="theme-color" content="#ffffff">

		<!-- BOOTSTRAP CSS -->
		<link  id="style" href="/assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet">

		<!-- ICONS CSS -->
		<link href="/assets/plugins/web-fonts/icons.css" rel="stylesheet">
		<link href="/assets/plugins/web-fonts/font-awesome/font-awesome.min.css" rel="stylesheet">
		<link href="/assets/plugins/web-fonts/plugin.css" rel="stylesheet">

		<!-- STYLE CSS -->
		<link href="/assets/css/style.css" rel="stylesheet">
		<link href="/assets/css/custom.css" rel="stylesheet">
		<link href="/assets/css/plugins.css" rel="stylesheet">

		<!-- SWITCHER CSS -->
		<link href="/assets/switcher/css/switcher.css" rel="stylesheet">
		<link href="/assets/switcher/demo.css" rel="stylesheet">

        <!-- JQUERY JS -->
        <script src="/assets/plugins/jquery/jquery.min.js"></script>      

		<!-- BOOTSTRAP JS -->
		<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>
    <body class="ltr main-body leftmenu">
        <!-- LOADEAR -->
		<!-- <div id="global-loader">
			<img src="/assets/img/loader.svg" class="loader-img" alt="Loader">
		</div> -->

        <!-- PAGE -->
        <div class="page">
			<?php require_once __DIR__ . "/template/header.php"; ?>
            <?php require_once __DIR__ . "/template/sidebar.php"; ?>
            <?php require_once __DIR__ . "/template/content.php"; ?>
            <?php require_once __DIR__ . "/template/footer.php"; ?>
        </div>
        <!-- END PAGE -->

		<a href="#top" id="back-to-top"><i class="fe fe-arrow-up"></i></a>
        
        <?php if($page == 'dashboard'){ ?>
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns"></script>
        <?php }; ?>
		<script src="/assets/plugins/jquery-ui/ui/widgets/datepicker.js"></script>
		<script src="/assets/plugins/bootstrap/js/popper.min.js"></script>
		<script src="/assets/plugins/bootstrap/js/bootstrap.min.js"></script>
		<script src="/assets/plugins/perfect-scrollbar/perfect-scrollbar.min.js"></script>
		<script src="/assets/plugins/sidemenu/sidemenu.js" id="leftmenu"></script>
		<script src="/assets/plugins/sidebar/sidebar.js"></script>
		<script src="/assets/plugins/select2/js/select2.min.js"></script>
		<script src="/assets/js/select2.js"></script>
        <script src="/assets/plugins/datatable/js/jquery.dataTables.min.js"></script>
        <script src="/assets/plugins/datatable/js/dataTables.bootstrap5.js"></script>
        <script src="/assets/plugins/datatable/js/dataTables.buttons.min.js"></script>
        <script src="/assets/plugins/datatable/js/buttons.bootstrap5.min.js"></script>
        <script src="/assets/plugins/datatable/js/jszip.min.js"></script>
        <script src="/assets/plugins/datatable/pdfmake/pdfmake.min.js"></script>
        <script src="/assets/plugins/datatable/pdfmake/vfs_fonts.js"></script>
        <script src="/assets/plugins/datatable/js/buttons.html5.min.js"></script>
        <script src="/assets/plugins/datatable/js/buttons.print.min.js"></script>
        <script src="/assets/plugins/datatable/js/buttons.colVis.min.js"></script>
        <script src="/assets/plugins/datatable/dataTables.responsive.min.js"></script>
        <script src="/assets/plugins/datatable/responsive.bootstrap5.min.js"></script>
        <script src="/assets/js/table-data.js"></script>
		<script src="/assets/plugins/jquery.maskedinput/jquery.maskedinput.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/parsley.js/2.9.2/parsley.min.js"></script>
		<script src="/assets/js/form-elements.js"></script>
		<script src="/assets/plugins/jquery-steps/jquery.steps.min.js"></script>
		<script src="/assets/plugins/spectrum-colorpicker/spectrum.js"></script>
		<script src="/assets/plugins/bootstrap-datepicker/bootstrap-datepicker.js"></script>
		<script src="/assets/plugins/amazeui-datetimepicker/js/amazeui.datetimepicker.min.js"></script>
		<script src="/assets/plugins/ion-rangeslider/js/ion.rangeSlider.min.js"></script>
		<script src="/assets/plugins/accordion-Wizard-Form/jquery.accordion-wizard.min.js"></script>
		<script src="/assets/js/form-wizard.js"></script>
		<script src="/assets/js/form-layouts.js"></script>
		<script src="/assets/js/sticky.js"></script>
        <script src="/assets/js/themeColors.js"></script>
        <script src="/assets/js/custom.js"></script>
        <script src="/assets/switcher/js/switcher.js"></script>
		<script src="/assets/plugins/fileuploads/js/fileupload.js"></script>
		<script src="/assets/plugins/fileuploads/js/file-upload.js"></script>
		<script src="/assets/plugins/fancyuploder/jquery.ui.widget.js"></script>
		<script src="/assets/plugins/fancyuploder/jquery.fileupload.js"></script>
		<script src="/assets/plugins/fancyuploder/jquery.iframe-transport.js"></script>
		<script src="/assets/plugins/fancyuploder/jquery.fancy-fileupload.js"></script>
		<script src="/assets/plugins/fancyuploder/fancy-uploader.js"></script>
		<script src="/assets/plugins/gallery/picturefill.js"></script>
		<script src="/assets/plugins/gallery/lightgallery.js"></script>
		<script src="/assets/plugins/gallery/lightgallery-1.js"></script>
        <script src="/assets/plugins/gallery/lg-pager.js"></script>
        <script src="/assets/plugins/gallery/lg-autoplay.js"></script>
        <script src="/assets/plugins/gallery/lg-fullscreen.js"></script>
        <script src="/assets/plugins/gallery/lg-zoom.js"></script>
        <script src="/assets/plugins/gallery/lg-hash.js"></script>
        <script src="/assets/plugins/gallery/lg-share.js"></script>
		<script type="text/javascript">
            $(document).ready(function() {
                $('.nav-item').removeClass('active show')
                let currentModule = window.location.pathname
                $.each($('.menu-nav .nav-link'), (i, el) => {
                    let target = $(el)
                    if(target.hasClass('with-sub')) {
                        /** Handle Dropdown */
                        subTarget = target.parent().find('.nav-sub-link').map((el, val) => {
                            if($(val).attr('href') == currentModule) {
                                return val;
                            }
                        })

                        if(subTarget?.attr('href') == currentModule) {
                            if(!subTarget.hasClass('active')) subTarget.addClass('active');
                            if(!subTarget.parent().hasClass('active')) subTarget.parent().addClass('active');
                            if(!subTarget.parent().parent().hasClass('open')) subTarget.parent().parent().addClass('open');
                            if(!subTarget.parent().parent().parent().hasClass('active show')) subTarget.parent().parent().parent().addClass('active show');
                        }
                    
                    }else {
                        /** Handle Single */
                        if(target?.attr('href') == currentModule) {
                            target.parent().addClass('active')
                        }
                    }
                })

				$('.amount-formatter').on('keyup', function(evt) {
					$(evt.currentTarget).val( formatter( $(evt.currentTarget).val() ) )
				})
            })

			function formatter(angka, prefix = null){
				var number_string = angka.replace(/[^\.\d]/g, '').toString(),
				split   		= number_string.split('.'),
				sisa     		= split[0].length % 3,
				rupiah     		= split[0].substr(0, sisa),
				ribuan     		= split[0].substr(sisa).match(/\d{3}/gi);
				// tambahkan titik jika yang di input sudah menjadi angka ribuan
				if(ribuan){
					separator = sisa ? ',' : '';
					rupiah += separator + ribuan.join(',');
				}

				rupiah = split[1] != undefined ? rupiah + '.' + split[1] : rupiah;
				return prefix == undefined ? rupiah : (rupiah ? prefix + rupiah : '');
			}
			

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