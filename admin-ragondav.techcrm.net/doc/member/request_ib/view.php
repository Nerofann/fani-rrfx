<div class="page-header">
    <div>
        <h2 class="main-content-title tx-24 mg-b-5">Request IB</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0);">Home</a></li>
            <li class="breadcrumb-item"><a href="javascript:void(0);">Member</a></li>
            <li class="breadcrumb-item active" aria-current="page">Request IB</li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-md-12 mb-3">
        <div class="card custom-card">
            <div class="card-header">
                <h5 class="card-title">Request Pending</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered" id="table-pending">
                        <thead>
                            <tr>
                                <th>Tanggal Pengajuan</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Account</th>
                                <th>#</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $('#table-pending').DataTable({
            processing: true,
            serverSide: true,
            deferRender: true,
            order: [[0, 'desc']],
            ajax: {
                url: "/ajax/datatable/member/request_ib_pending/view"
            },
            columnDefs: [
                { targets: 4, className: "text-center" },
            ]
        })
    })
</script>