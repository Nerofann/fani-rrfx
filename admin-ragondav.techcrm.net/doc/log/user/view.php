<div class="page-header">
    <div>
        <h2 class="main-content-title tx-24 mg-b-5">Log User</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0);">Home</a></li>
            <li class="breadcrumb-item"><a href="javascript:void(0);">Log</a></li>
            <li class="breadcrumb-item active" aria-current="page">Log User</li>
        </ol>
    </div>
</div>
<!-- Row -->
<div class="row row-sm">
	<div class="col-lg-12">
		<div class="card custom-card overflow-hidden">
			<div class="card-body">
				<div class="table-responsive">
					<table id="table" class="table table-bordered table-striped table-hover key-buttons text-nowrap w-100" >
						<thead>
							<tr class="text-center">
								<th>Date</th>
								<th>Create By</th>
								<th>Full Name</th>
								<th>Email</th>
								<th>Desc</th>
								<th>IP</th>
								<th>Device</th>
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
        $('#table').DataTable({
            dom: 'Blfrtip',
            processing: true,
            serverSide: true,
            deferRender: true,
            lengthMenu: [[10, 50, 100, -1], [10, 50, 100, "All"]],
            scrollX: true,
            order: [[ 0, "desc" ]],
            ajax: {
                url: "/ajax/datatable/log/user/view",
                contentType: "application/json",
                type: "GET"
            },
        });
    });
</script>