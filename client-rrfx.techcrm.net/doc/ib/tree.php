<?php

use App\Models\Account;
use App\Models\Ib;
use App\Models\Refferal;
use App\Models\User;

$isIB = User::get_ib_data($user['MBR_ID'], [-1]); 
$upline = Ib::userUpline($user['MBR_IDSPN']);
if(!$upline) {
    die("<script>alert('Setup Failed'); location.href = '/ib/become'; ;</script>");
}    
?>

<?php if($isIB) : ?>
    <?php 
    $totalDownline = count(Ib::getNetworks($user['MBR_ID'], "downline"));
    $userRefferal = Refferal::createUserReferral($user['MBR_ID']);
    $groupRefferal = Refferal::createUserGroupReferral($user['MBR_ID']);
    $accountRefferal = Refferal::createAccountReferral($user['MBR_ID']);
    ?>
    
    <link rel="stylesheet" href="/assets/bstreeview/bstreeview.css"/>
    <script src="/assets/bstreeview/bstreeview.js"></script>
    <div class="row">
        <div class="col-md-6 mb-25">
            <div class="panel">
                <div class="panel-header">
                    <h5>Profile</h5>
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" style="table-layout: fixed; word-break: break-word;">
                            <tbody>
                                <tr>
                                    <td width="30%">Upline</td>
                                    <td width="70%"><?= $upline['MBR_NAME'] ?? "-" ?></td>
                                </tr>
                                <tr>
                                    <td width="30%">IB Status</td>
                                    <td width="70%"><?= Ib::$status[ $isIB['BECOME_STS'] ]['html']; ?></td>
                                </tr>
                                <tr>
                                    <td width="30%">Total Downline</td>
                                    <td width="70%"><?= $totalDownline ?></td>
                                </tr>
                                <tr>
                                    <td width="30%">User Refferal</td>
                                    <td width="70%"><a href="javascript:void(0)" class="copytext"><?= $userRefferal ?></a></td>
                                </tr>
                            </tbody>
                            <tbody>
                                <tr>
                                    <td colspan="2" class="bg-secondary text-white text-start">Group Refferal</td>
                                </tr>
                                <?php foreach($groupRefferal as $reff) : ?>
                                    <tr>
                                        <td width="30%"><?= $reff['type'] ?></td>
                                        <td width="70%">
                                            <a href="javascript:void(0)" class="copytext"><?= $reff['link'] ?></a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="panel">
                <div class="panel-header">
                    <h5>Account Refferal</h5>
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" style="table-layout: fixed; word-break: break-word;">
                            <tbody>
                                <?php foreach($accountRefferal as $accRef) : ?>
                                    <tr>
                                        <td width="30%"><?= implode("/", [$accRef['name'], $accRef['rate'], $accRef['commission']]); ?></td>
                                        <td width="70%">
                                            <a href="javascript:void(0)" class="copytext"><?= $accRef['link'] ?></a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="panel">
                <div class="panel-header">
                    <h5>Member Tree</h5>
                </div>
                <div class="panel-body" style="min-height: 100vh;">
                    <div id="tree"></div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function() {
            $.post("/ajax/post/treeview", {}, function(resp) {
                let treeData = resp.data;
                $('#tree').bstreeview({
                    data: treeData,
                    expandIcon:'fa fa-angle-down',
                    collapseIcon:'fa fa-angle-right'
                })
            }, 'json')
        })
    </script>
    
<?php else : ?>
    <?php require_once __DIR__ . "/../../template/404.php"; ?>
<?php endif; ?>