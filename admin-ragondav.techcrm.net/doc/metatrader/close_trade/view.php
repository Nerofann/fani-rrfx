
<div class="page-header">
    <div>
        <h2 class="main-content-title tx-24 mg-b-5">Close Trade</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0);">Home</a></li>
            <li class="breadcrumb-item"><a href="javascript:void(0);">MetaTrader</a></li>
            <li class="breadcrumb-item active" aria-current="page">Close Trade</li>
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
                                <th style="vertical-align: middle" class="text-center">Date Time</th>
                                <th style="vertical-align: middle" class="text-center">Login</th>
                                <th style="vertical-align: middle" class="text-center">Ticket</th>
                                <th style="vertical-align: middle" class="text-center">Symbol</th>
                                <th style="vertical-align: middle" class="text-center">Price</th>
                                <th style="vertical-align: middle" class="text-center">Volume</th>
                                <th style="vertical-align: middle" class="text-center">SL</th>
                                <th style="vertical-align: middle" class="text-center">TP</th>
                                <th style="vertical-align: middle" class="text-center">Time</th>
                                <th style="vertical-align: middle" class="text-center">Price</th>
                                <th style="vertical-align: middle" class="text-center">Commision</th>
                                <th style="vertical-align: middle" class="text-center">Swap</th>
                                <th style="vertical-align: middle" class="text-center">Profit</th>
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
                { data: 'open_time', className: 'text-center' },
                { data: 'login' },
                { data: 'ticket' },
                { data: 'symbol' },
                { data: 'open_price', className: 'text-end' },
                { data: 'volume', className: 'text-end' },
                { data: 'sl', className: 'text-end' },
                { data: 'tp', className: 'text-end' },
                { data: 'close_time', className: 'text-center' },
                { data: 'close_price', className: 'text-end' },
                { data: 'commission', className: 'text-end', render: $.fn.dataTable.render.number(',', '.', 2, '') },
                { data: 'swap', className: 'text-end', render: $.fn.dataTable.render.number(',', '.', 2, '') },
                { data: 'profit', className: 'text-end', render: $.fn.dataTable.render.number(',', '.', 2, '') }
            ],
            lengthMenu: [[10, 50, 100, -1], [10, 50, 100, "All"]],
            scrollX: true,
            order: [[ 0, "desc" ]],
            ajax: {
                url: "/ajax/datatable/metatrader/close_trade/view",
                contentType: "application/json",
                type: "GET"
            },
        });
    });
</script>