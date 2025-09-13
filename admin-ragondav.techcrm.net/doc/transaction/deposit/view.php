<?php
    use App\Models\Admin;
    
    $listGrup = $adminPermissionCore->availableGroup();
    $adminRoles = Admin::adminRoles();
?>
<div class="page-header">
    <div>
        <h2 class="main-content-title tx-24 mg-b-5">Deposit</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= pathbreadcrumb(0) ?>/dashboard">Home</a></li>
            <li class="breadcrumb-item">Transaction</li>
            <li class="breadcrumb-item active" aria-current="page">Deposit</li>
        </ol>
    </div>
</div>
<?php 
    if($adminPermissionCore->isHavePermission($moduleId, "view.verficatior")){
        include(__DIR__.'/view_verificator.php');
    } 
    if($adminPermissionCore->isHavePermission($moduleId, "view.accounting")){
        include(__DIR__.'/view_accounting.php');
    } 
    if($adminPermissionCore->isHavePermission($moduleId, "view.authorization")){
        include(__DIR__.'/view_authorization.php');
    } 
?>
<div class="card custom-card overflow-hidden">
    <div class="card-header">
        <h5 class="main-content-label mb-1">History</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="table" class="table table-striped table-hover" width="100%">
                <thead>
                    <tr>
                        <th style="vertical-align: middle" class="text-center">Date</th>
                        <th style="vertical-align: middle" class="text-center">Name</th>
                        <th style="vertical-align: middle" class="text-center">Email</th>
                        <th style="vertical-align: middle" class="text-center">Login</th>
                        <th style="vertical-align: middle" class="text-center">Amount</th>
                        <th style="vertical-align: middle" class="text-center">Pic</th>
                        <th style="vertical-align: middle" class="text-center">Voucher</th>
                        <th style="vertical-align: middle" class="text-center">Note</th>
                        <th style="vertical-align: middle" class="text-center">Ver.</th>
                        <th style="vertical-align: middle" class="text-center">Fin.</th>
                        <th style="vertical-align: middle" class="text-center">Auth.</th>
                        <th style="vertical-align: middle" class="text-center">#</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<script>
    $(document).ready(() => {
        $('#table').DataTable({
            dom: 'Blfrtip',
            processing: true,
            serverSide: true,
            deferRender: true,
			buttons: [
				{
					extend: 'excel',
					text: 'Excel',
				},
				{
					extend: 'copy',
					text: 'Copy'
				}
			],
            lengthMenu: [[10, 50, 100, -1], [10, 50, 100, "All"]],
            scrollX: true,
            order: [[ 0, "desc" ]],
            ajax: {
                url: "/ajax/datatable/transaction/history",
                contentType: "application/json",
                type: "GET"
            }
        });
    });
</script>