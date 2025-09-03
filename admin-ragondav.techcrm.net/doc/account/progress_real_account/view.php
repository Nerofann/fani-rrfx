
<div class="page-header">
	<div>
		<h2 class="main-content-title tx-24 mg-b-5">Progress Real Account</h2>
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
			<li class="breadcrumb-item"><a href="javascript:void(0);">Account</a></li>
			<li class="breadcrumb-item active" aria-current="page">Progress Real Account</li>
		</ol>
	</div>
</div>

<div class="row row-sm">
	<div class="col-lg-12">
		<div class="card custom-card overflow-hidden">
			<div class="card-body">
				<div class="table-responsive">
					<table id="table" class="table table-bordered table-striped table-hover key-buttons text-nowrap w-100" >
						<thead>
							<tr class="text-center">
								<th>Date Reg.</th>
								<th>Full Name</th>
								<th>Email</th>
								<th>Type Acc</th>
								<th>Product</th>
								<th>Rate</th>
								<th>Status</th>
								<th>Detail</th>
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
        var table = $('#table').DataTable( {
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
                url: "/ajax/datatable/account/progress_real_account/view",
                contentType: "application/json",
                type: "GET",
            },
        });
    });
</script>