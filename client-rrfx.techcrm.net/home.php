<?php
require_once(__DIR__ . "/../config/setting.php");

use App\Models\CompanyProfile;
use App\Models\User;

$pageFile = "404";
$pageTitle = "404";
if(!empty($_GET["a"])){
    $pageFile = htmlentities(str_replace('%', '', str_replace(' ', '_', stripslashes(($_GET['a'])))),ENT_QUOTES,'WINDOWS-1252');
    $pageTitle = ucwords(strtolower(str_replace('-', ' ', $pageFile)));
}

if($pageFile == "logout") {
    User::logout();
    die("<script>location.href ='/'</script>"); 
}

$user = User::user();
if(!$user) {
    die("<script>alert('Session Expired, please re-login'); location.href ='/'</script>"); 
}

$userid = md5(md5($user['MBR_ID'])) ?? "";
?>

<!DOCTYPE html>
<html lang="en" data-menu="vertical" data-nav-size="nav-default">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
        <meta http-equiv="Pragma" content="no-cache">
        <meta http-equiv="Expires" content="0">
        <title><?= "{$pageTitle} - ".CompanyProfile::$name; ?></title>
        <link rel="icon" type="image/png" sizes="16x16" href="/assets/images/favicon/favicon.ico">
        <link rel="manifest" href="/assets/images/favicon/manifest.json">
        <meta name="msapplication-TileColor" content="#ffffff">
        <meta name="msapplication-TileImage" content="/assets/images/favicon/ms-icon-144x144.png">
        <meta name="theme-color" content="#ffffff">
        <meta name="robots" content="noindex, nofollow">

        <link rel="stylesheet" href="/assets/vendor/css/all.min.css">
        <link rel="stylesheet" href="/assets/vendor/css/sharp-solid.min.css">
        <link rel="stylesheet" href="/assets/vendor/css/sharp-regular.min.css">
        <link rel="stylesheet" href="/assets/vendor/css/jquery-ui.min.css">
        <link rel="stylesheet" href="/assets/vendor/css/jquery.uploader.css">
        <link rel="stylesheet" href="/assets/vendor/css/dropzone.min.css">
        <link rel="stylesheet" href="/assets/vendor/css/OverlayScrollbars.min.css">
        <link rel="stylesheet" href="/assets/vendor/css/jquery.dataTables.min.css">
        <link rel="stylesheet" href="/assets/vendor/css/sweetalert2.min.css">
        <link rel="stylesheet" href="/assets/vendor/css/bootstrap.min.css">
        <link rel="stylesheet" href="/assets/css/style.css">
        <link rel="stylesheet" href="/assets/css/custom.css">

        <link rel="stylesheet" id="primaryColor" href="/assets/css/blue-color.css">
        <link rel="stylesheet" id="rtlStyle" href="#">
        <script src="/assets/vendor/js/jquery-3.6.0.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/css/dropify.css" integrity="sha512-In/+MILhf6UMDJU4ZhDL0R0fEpsp4D3Le23m6+ujDWXwl3whwpucJG1PEmI3B07nyJx+875ccs+yX2CqQJUxUw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/css/dropify.min.css" integrity="sha512-EZSUkJWTjzDlspOoPSpUFR0o0Xy7jdzW//6qhUkoZ9c4StFkVsp9fbbd0O06p9ELS3H486m4wmrCELjza4JEog==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/js/dropify.min.js" integrity="sha512-8QFTrG0oeOiyWo/VM9Y8kgxdlCryqhIxVeRpWSezdRRAvarxVtwLnGroJgnVW9/XBRduxO/z1GblzPrMQoeuew==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script src="https://sdk.amazonaws.com/js/aws-sdk-2.179.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <!-- select2 -->
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    </head>

    <body class="body-padding body-p-top light-theme">
        <?php require_once __DIR__ . "/template/header.php"; ?>
        <?php require_once __DIR__ . "/template/sidebar.php"; ?>
        <?php require_once __DIR__ . "/template/content.php"; ?>

        <script src="/assets/vendor/js/jquery-ui.min.js"></script>
        <script src="/assets/vendor/js/jquery.overlayScrollbars.min.js"></script>
        <script src="/assets/vendor/js/jquery.dataTables.min.js"></script>
        <script src="/assets/vendor/js/jquery.uploader.min.js"></script>
        <script src="/assets/vendor/js/dropzone.min.js"></script>
        <script src="/assets/vendor/js/sweetalert2.all.min.js"></script>
        <script src="/assets/vendor/js/bootstrap.bundle.min.js"></script>
        <script src="/assets/js/main.js?v=<?= time(); ?>"></script>
        <script src="/assets/js/custom.js"></script>
        <script>
            $(document).ready(function() {
                $('.select2').select2({
                    height: "resolve"
                });

                let datePickerInputs = $('.datepicker');
                $.each(datePickerInputs, (i, el) => {
                    $(el).datepicker({
                        showOtherMonths: true,
                        selectOtherMonths: true,
                        maxDate: $(el).data('max')
                    });
                })
            })

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