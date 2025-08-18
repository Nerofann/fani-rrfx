<div class="card custom-card mb-3">
    <div class="card-header">
        <h5 class="card-title">History</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-bordered" id="table-history">
                <thead>
                    <tr>
                        <th width="15%">Tanggal Pengajuan</th>
                        <th width="20%">Nama</th>
                        <th width="20%">Email</th>
                        <th>Note</th>
                        <th width="10%">Status</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<script type="text/javascript">
    let table_history;
    $(document).ready(function() {
        table_history = $('#table-history').DataTable({
            processing: true,
            serverSide: true,
            deferRender: true,
            order: [[0, 'desc']],
            ajax: {
                url: "/ajax/datatable/member/request_ib/view"
            },
            columnDefs: [
                { targets: 3, className: "text-start" },
                { targets: 4, className: "text-center" },
            ]
        })
    })
</script>