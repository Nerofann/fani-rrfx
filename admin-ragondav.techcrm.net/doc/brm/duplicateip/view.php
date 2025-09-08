
<div class="page-header">
	<div>
		<h2 class="main-content-title tx-24 mg-b-5">Duplicate IP</h2>
		<ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= pathbreadcrumb(0) ?>/dashboard">Home</a></li>
			<li class="breadcrumb-item">Business Relation Manager</li>
			<li class="breadcrumb-item active">Duplicate IP</li>
			<!-- <li class="breadcrumb-item active" aria-current="page"><a href="javascript:void(0);">Duplicate IP</a></li> -->
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
                        <th style="vertical-align: middle" class="text-center">Total</th>
                        <th style="vertical-align: middle" class="text-center">#</th>
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
                url: "/ajax/datatable/brm/duplicateip/view",
                contentType: "application/json",
                type: "GET"
            },
            columns: [
                { data: 'IP' },
                { 
                    data: 'TOTALIP', 
                    className: 'text-end'
                },
                { 
                    data: 'HIDIP', 
                    className: 'text-end',
                    render: function(data, type, row, meta){
                        return `<a href="/brm/duplicateip/detail/view/${data}" class="btn btn-sm btn-primary">Detail</a>`;
                    }
                }
            ],
            order: [[1, 'desc']],
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
        });
    });
</script>