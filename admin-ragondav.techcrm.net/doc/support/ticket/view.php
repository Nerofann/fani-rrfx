<div class="page-header">
    <div>
        <h2 class="main-content-title tx-24 mg-b-5">Ticket</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= pathbreadcrumb(0) ?>/dashboard">Home</a></li>
            <li class="breadcrumb-item"><a href="javascript:void(0);">Support</a></li>
            <li class="breadcrumb-item active" aria-current="page">Ticket</li>
        </ol>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <div class="d-flex mb-3 justify-content-between">
            <span class="main-content-label my-auto">Ticket List</span>
        </div>
        <hr>
        <div class="table-responsive">
            <table id="tbl_tckt" class="table table-striped table-hover table-bordered">
                <thead>
                    <tr class="text-center">
                        <th style="vertical-align: middle;">Date Req</th>
                        <th style="vertical-align: middle;">Last Confersation Date</th>
                        <th style="vertical-align: middle;">Code</th>
                        <th style="vertical-align: middle;">Email</th>
                        <th style="vertical-align: middle;">Subject</th>
                        <th style="vertical-align: middle;">Status</th>
                        <th style="vertical-align: middle;">#</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<script>
    $(document).ready(() => {
        $('#tbl_tckt').DataTable({
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
                url: "/ajax/datatable/support/ticket/view",
                contentType: "application/json",
                type: "GET"
            },
        });
    });
</script>