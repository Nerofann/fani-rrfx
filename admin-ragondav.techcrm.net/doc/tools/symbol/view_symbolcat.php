
<div class="card custom-card">
    <div class="card-body">
        <div class="d-flex flex-row justify-content-between">
            <div class="pd-10">
                <?php require_once __DIR__ . "/create_symbolcat.php"; ?>
            </div>
            <div class="pd-10 text-end">
                <h4>Symbol Category</h4>
            </div>
        </div>
        <hr>
        <div class="table-responsive">
            <table id="tabel_symbolcategory" class="table table-striped table-bordered text-nowrap">
                <thead>
                    <tr>
                        <th style="vertical-align: middle" class="text-center">Name</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<script>
    let tabel_symbolcategory;
    $(document).ready(function() {
        tabel_symbolcategory = $('#tabel_symbolcategory').DataTable({
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
                url: "/ajax/datatable/tools/symbolcategory/view",
                contentType: "application/json",
                type: "GET"
            },
            columns: [
                { data: 'SYMBOL' }
            ],
            order: [[0, 'asc']],
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
        });
    });
</script>