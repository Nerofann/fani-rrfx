<div class="page-header">
    <div>
        <h2 class="main-content-title tx-24 mg-b-5">Bank</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= pathbreadcrumb(0) ?>/dashboard">Home</a></li>
            <li class="breadcrumb-item"><a href="javascript:void(0);">Tools</a></li>
            <li class="breadcrumb-item active" aria-current="page">Bank</li>
        </ol>
    </div>
    <div class="d-flex">
        <div class="justify-content-center">
            <?php require_once __DIR__ . "/create.php"; ?>
        </div>
    </div>
</div>
<div class="row row-sm">
    <div class="col-lg-12 col-md-12 col-md-12">
        <div class="card custom-card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="table-bank" class="table table-striped table-bordered text-nowrap">
                        <thead>
                            <tr class="text-center">
                                <th style="vertical-align: middle">No.</th>
                                <th style="vertical-align: middle">Bank Name</th>
                                <th style="vertical-align: middle">#</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    let table;
    $(document).ready(() => {
        table = $('#table-bank').DataTable({
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
            columnDefs: [
                {
                    targets: 0,
                    className: "text-center"
                },
                {
                    targets: 1
                },
                {
                    targets: 2,
                    className: "text-center"
                }
            ],
            order: [[ 0, "asc" ]],
            ajax: {
                url: "/ajax/datatable/tools/bank/view",
                contentType: "application/json",
                type: "GET"
            },
        });
    });
</script>
<?php require_once __DIR__ . "/delete.php"; ?>
<!-- <div class="row row-sm">
    <div class="col-lg-12 col-md-12 col-md-12">
        <div class="card custom-card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="myOwnTable" class="table table-striped table-bordered text-nowrap">
                        <thead>
                            <tr>
                                <th style="vertical-align: middle" class="text-center">No.</th>
                                <th style="vertical-align: middle" class="text-center">Nama Bank</th>
                                <th style="vertical-align: middle" class="text-center">#</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(() => {
        $('#myOwnTable').DataTable({
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
            columns: [
                { data: 'no' },
                { data: 'bank_name' },
                { 
                    data: 'action', 
                    className: 'text-center',
                    orderable: false,
                    render: function(data, type, row) {
                        return `
                            <div class="btn-group">
                                <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Action
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="/tools/bank/edit/${row.no}">Edit</a>
                                    <a class="dropdown-item text-danger delete-bank" href="#" data-id="${row.no}">Delete</a>
                                </div>
                            </div>
                        `;
                    }
                }
            ],
            lengthMenu: [[10, 50, 100, -1], [10, 50, 100, "All"]],
            scrollX: true,
            order: [[ 0, "asc" ]],
            ajax: {
                url: "/ajax/datatable/tools/bank/view",
                contentType: "application/json",
                type: "GET"
            },
        });
    });
</script> -->