<!-- MAIN-CONTENT -->
<div class="main-content side-content pt-0">
    <div class="main-container container-fluid">
        <div class="inner-body">
            <?php 
            $getInput = array_filter($_GET, fn($key) => in_array($key, range('a', 'f'), true), ARRAY_FILTER_USE_KEY);
            $fileUrl = Allmedia\Shared\AdminPermission\Core\UrlParser::urlToPath(App\Models\Helper::getSafeInput($getInput), "view");
            $filename = __DIR__ ."/../doc/$fileUrl.php";
            file_exists($filename) 
                ? require_once $filename
                : require_once __DIR__ ."/"."404.php";
            ?>
        </div>
    </div>
</div>
<!-- END MAIN-CONTENT -->