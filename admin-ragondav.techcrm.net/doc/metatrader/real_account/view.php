
<div class="page-header">
    <div>
        <h2 class="main-content-title tx-24 mg-b-5">Real Account</h2>
        <ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="<?= pathbreadcrumb(0) ?>/dashboard">Home</a></li>
            <li class="breadcrumb-item">MetaTrader</li>
            <li class="breadcrumb-item active" aria-current="page">Real Account</li>
        </ol>
    </div>
</div>
<div class="row row-sm">
    <div class="col-lg-12">
        <div class="card custom-card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="myOwnTable" class="table table-striped table-bordered text-nowrap">
                        <thead>
                            <tr>
                                <th style="vertical-align: middle" class="text-center">Date Reg</th>
                                <th style="vertical-align: middle" class="text-center">Login</th>
                                <th style="vertical-align: middle" class="text-center">Name</th>
                                <th style="vertical-align: middle" class="text-center">Email</th>
                                <th style="vertical-align: middle" class="text-center">Balance</th>
                                <th style="vertical-align: middle" class="text-center">Credit</th>
                                <th style="vertical-align: middle" class="text-center">EquityPrevDay</th>
                                <th style="vertical-align: middle" class="text-center">EquityPrevMonth</th>
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
                { data: 'REGDATE', className: 'text-center' },
                { data: 'LOGIN' },
                { data: 'NAME' },
                { data: 'EMAIL' },
                { data: 'BALANCE', className: 'text-end', render: $.fn.dataTable.render.number(',', '.', 2, '') },
                { data: 'CREDIT', className: 'text-end', render: $.fn.dataTable.render.number(',', '.', 2, '') },
                { data: 'PREVBALANCE', className: 'text-end', render: $.fn.dataTable.render.number(',', '.', 2, '') },
                { data: 'PREVMONTHBALANCE', className: 'text-end', render: $.fn.dataTable.render.number(',', '.', 2, '') }
            ],
            lengthMenu: [[10, 50, 100, -1], [10, 50, 100, "All"]],
            scrollX: true,
            order: [[ 0, "desc" ]],
            ajax: {
                url: "/ajax/datatable/metatrader/real_account/view",
                contentType: "application/json",
                type: "GET"
            },
        });
    });
</script>