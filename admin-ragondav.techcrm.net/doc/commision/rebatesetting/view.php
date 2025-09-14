
<div class="page-header">
	<div>
		<h2 class="main-content-title tx-24 mg-b-5">Rebate Setting</h2>
		<ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= pathbreadcrumb(0) ?>/dashboard">Home</a></li>
			<li class="breadcrumb-item">Commision</li>
			<li class="breadcrumb-item active" aria-current="page">Rebate Setting</li>
		</ol>
	</div>
    <div class="d-flex">
        <div class="justify-content-center">
            <?php if($permisCreate = $adminPermissionCore->isHavePermission($moduleId, "create")){ ?>
                <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#modal-create-rebate" class="btn btn-primary my-2 me-2"><i class="fas fa-plus"></i> Add Rebate Setting</a>
            <?php } ?> 
        </div>
    </div>
</div>
<div class="card custom-card overflow-hidden">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover table-bordered" width="100%" id="rebate_setting">
                <thead>
                    <tr>
                        <th style="vertical-align: middle" class="text-center">Commision</th>
                        <th style="vertical-align: middle" class="text-center">Sturcture</th>
                        <th style="vertical-align: middle" class="text-center">Product</th>
                        <th style="vertical-align: middle" class="text-center">Rate</th>
                        <th style="vertical-align: middle" class="text-center">Symbol Category</th>
                        <th style="vertical-align: middle" class="text-center">Amount</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<?php 
if($permisCreate = $adminPermissionCore->isHavePermission($moduleId, "create")){
    require_once __DIR__ . "/create.php";
};
?>

<script>
    let rebate_setting;
    $(document).ready(function() {
        rebate_setting = $('#rebate_setting').DataTable({
            dom: 'Blfrtip',
            scrollX: true,
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
            ajax: {
                url: "/ajax/datatable/commision/rebatesetting/view",
                contentType: "application/json",
                type: "GET"
            },
            columns: [
                { data: 'RTYPE_KOMISI', className: 'text-end', render: $.fn.dataTable.render.number(',', '.', 0, '') },
                { data: 'SLSSTRC_NAME' },
                { data: 'RTYPE_NAME' },
                { data: 'RTYPE_RATE', className: 'text-end', render: $.fn.dataTable.render.number(',', '.', 0, '') },
                { data: 'SYMCAT_NAME' },
                { data: 'COMMSET_AMOUNT', className: 'text-end', render: $.fn.dataTable.render.number(',', '.', 2, '') }
            ],
            order: [[1, 'asc']],
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
        });
    });
</script>