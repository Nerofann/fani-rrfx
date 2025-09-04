<?php if(count(App\Models\Account::myAccount($user['MBR_ID'])) > 0) : ?>
    
    <?php require_once __DIR__ . "/custom-web-trade/index.php"; ?>

<?php else : ?>
    <div class="panel">
        <div class="card">
            <div class="card-body">
                <div class="text-center">
                    <a href="/account/create" class="text-center btn btn-md btn-primary mt-3 mb-3">Create Real Account</a>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
