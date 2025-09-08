<?php
use App\Models\Helper;
$data   = Helper::getSafeInput($_GET);
?>
<div class="page-header">
	<div>
		<h2 class="main-content-title tx-24 mg-b-5">Scalpers</h2>
		<ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= pathbreadcrumb(0) ?>/dashboard">Home</a></li>
			<li class="breadcrumb-item">Business Relation Manager</li>
			<li class="breadcrumb-item"><a href="<?= pathbreadcrumb(2) ?>/view">Scalpers</a></li>
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
                        <th style="vertical-align: middle" class="text-center">Ticket</th>
                        <th style="vertical-align: middle" class="text-center">Symbol</th>
                        <th style="vertical-align: middle" class="text-center">Lots</th>
                        <th style="vertical-align: middle" class="text-center">Opened At</th>
                        <th style="vertical-align: middle" class="text-center">Closed At</th>
                        <th style="vertical-align: middle" class="text-center">Second</th>
                        <th style="vertical-align: middle" class="text-center">Profit</th>
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
                url: "/ajax/datatable/brm/scalpersdetail/view?login=<?= $data["e"] ?>&startdate=<?= $data["f"] ?>&enddate=<?= $data["g"] ?>",
                contentType: "application/json",
                type: "GET"
            },
            columns: [
                { data: 'TICKET' },
                { data: 'SYMBOL' },
                { data: 'VOLUME', className: 'text-end', render: $.fn.dataTable.render.number( ',', '.', 2, '' ) },
                { data: 'OPENED', className: 'text-center' },
                { data: 'CLOSED', className: 'text-center' },
                { data: 'SECOND', className: 'text-end', render: $.fn.dataTable.render.number( ',', '.', 0, '', ' sec' ) },
                { data: 'PROFIT', className: 'text-end', render: $.fn.dataTable.render.number( ',', '.', 2, '' ) }
            ],
            order: [[5, 'asc']],
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
        });
    });
</script>