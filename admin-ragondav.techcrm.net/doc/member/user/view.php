<div class="page-header">
	<div>
		<h2 class="main-content-title tx-24 mg-b-5"><?php echo $vp = 'User'; ?></h2>
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="<?= pathbreadcrumb(0) ?>/dashboard">Home</a></li>
			<li class="breadcrumb-item"><a href="javascript:void(0);">Member</a></li>
			<li class="breadcrumb-item active" aria-current="page"><?php echo $vp; ?></li>
		</ol>
	</div>
</div>

<!-- Row -->
<div class="row row-sm">
	<div class="col-lg-12">
		<div class="card custom-card overflow-hidden">
			<div class="card-body">
				<!-- <div>
					<h6 class="main-content-label mb-1">File export Datatables</h6>
					<p class="text-muted card-sub-title">Exporting data from a table can often be a key part of a complex application. The Buttons extension for DataTables provides three plug-ins that provide overlapping functionality for data export:</p>
				</div> -->
				<div class="table-responsive">
					<table id="table" class="table table-bordered table-striped table-hover key-buttons text-nowrap w-100" >
						<thead>
							<tr class="text-center">
								<th>Date Reg.</th>
								<th>Full Name</th>
								<th>Email</th>
								<th>Status</th>
								<th>#</th>
							</tr>
						</thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){
        // $('#table tfoot th').each(function (i) {
        //     var title = $('#table thead th')
        //         .eq($(this).index())
        //         .text();
        //     $(this).html(
        //         '<input type="text" style="padding:1px 1px 1px 1px" class="form-control" autocomplate="off" id="'+ i +'" placeholder="' + title + '" data-index="' + i + '" />'
        //     );
        // });
        
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
                url: "/ajax/datatable/member/user/view",
                contentType: "application/json",
                type: "GET",
            },
        });

        // $(table.table().container()).on('keyup', 'tfoot input', function () {
        //     table
        //         .column($(this).data('index'))
        //         .search(this.value)
        //         .draw();
        // });
    });
</script>