<!-- main content start -->
<div class="main-content">
    <?php
    use App\Models\Helper;

    switch($pageFile) {
        case "verif": 
            if($user['MBR_VERIF'] != -1) {
                $stepPage = Helper::form_input($_GET['b']);
                $user_step  = explode("-", $_GET['b'])[1] ?? $user['MBR_VERIF'];
                if($user_step > $user['MBR_VERIF']) {
                    die("<script>location.href = '/verif/step-".$user['MBR_VERIF']."'</script>");
                }

                $filename   = WEB_ROOT ."/doc/verif/$stepPage.php";
                file_exists($filename) 
                    ? include $filename
                    : include __DIR__ . "/404.php";
            }
            break;

        default: 
            if($user['MBR_STS'] != -1) die("<script>location.href = '/verif/step-1'; </script>");
            $getInput = array_filter($_GET, fn($key) => in_array($key, range('a', 'f'), true), ARRAY_FILTER_USE_KEY);
            $fileUrl = Allmedia\Shared\AdminPermission\Core\UrlParser::urlToPath(Helper::getSafeInput($getInput));
            $filename = WEB_ROOT ."/doc/$fileUrl.php";
            file_exists($filename) 
                ? require_once $filename
                : require_once __DIR__ ."/"."404.php";
            break;
    }
    ?>
    
    <?php require_once __DIR__ . "/footer.php"; ?>
</div>
<!-- main content end -->

<div class="ini-modal-file">
    <style>
        .modal-backdrop {
            z-index: -1 !important;
        }
    </style>

    <?php 
        // create modal file at doc/modal/
        if(!empty($_SESSION['modal']) && is_array($_SESSION['modal'])) {
            foreach($_SESSION['modal'] as $modal) {
                if(file_exists(__DIR__ . "/modal/{$modal}.php")) {
                    require_once __DIR__ . "/modal/{$modal}.php";
                }
            }
        }  
    ?>
</div>