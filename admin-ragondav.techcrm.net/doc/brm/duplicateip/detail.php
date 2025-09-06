<?php
use App\Models\Helper;
$data   = Helper::getSafeInput($_GET);
?>
<div class="page-header">
	<div>
		<h2 class="main-content-title tx-24 mg-b-5">Duplicate IP</h2>
		<ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= pathbreadcrumb(0) ?>/dashboard">Home</a></li>
			<li class="breadcrumb-item">Business Relation Manager</li>
			<li class="breadcrumb-item"><a href="<?= pathbreadcrumb(2) ?>/view">Duplicate IP</a></li>
			<li class="breadcrumb-item active">Detail</li>
		</ol>
	</div>
</div>
<div class="card custom-card overflow-hidden">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover table-bordered" width="100%" id="table">
                <thead>
                    <tr>
                        <th style="vertical-align: middle" class="text-center">IP</th>
                        <th style="vertical-align: middle" class="text-center">Last Access</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<script>
    let table;
    $(document).ready(function() {
        table = $('#table').DataTable({
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
                url: "/ajax/datatable/brm/duplicateipdetail/view?ip=<?= $data["e"] ?>",
                contentType: "application/json",
                type: "GET"
            },
            columns: [
                { data: 'LOGIN' },
                { data: 'LASTACCESS' }
            ],
            order: [[0, 'desc']],
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
        });
    });
</script>