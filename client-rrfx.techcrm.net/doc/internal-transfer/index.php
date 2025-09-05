<?php if(count(App\Models\Account::myAccount($user['MBR_ID'])) > 0) : ?>
    <div class="dashboard-breadcrumb mb-25">
        <h2>Internal Transfer</h2>
        <div class="input-group-a dashboard-filter">
            <a href="/internal-transfer/create" class="btn btn-sm btn-primary"><i class="fa-light fa-arrow-right-to-bracket"></i> Tambah Baru</a>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="panel">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-dashed table-hover digi-dataTable dataTable-resize table-striped" id="it-table">
                                <thead>
                                    <tr>
                                        <th width="15%" rowspan="2" class="text-center">Date</th>
                                        <th colspan="2" class="text-center">Account</th>
                                        <th rowspan="2" class="text-center">Amount</th>
                                        <th rowspan="2" class="text-center">Code</th>
                                    </tr>
                                    <tr>
                                        <th class="text-center">From</th>
                                        <th class="text-center">To</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#it-table').DataTable({
                scrollX: true,
                processing: true,
                serverSide: true,
                order: [[0, 'desc']],
                ajax: {
                    url: "/ajax/datatable/internal-transfer",
                },
                columnDefs: [
                    { targets: 0, className: "text-center" },
                    { targets: 3, className: "text-end" },
                    { targets: 4, className: "text-center" }
                ]
            })
        })
    </script>

<?php else : ?>
    <div class="panel">
        <div class="card">
            <div class="card-body">
                <div class="text-center">
                    <a href="/account" class="text-center btn btn-md btn-primary mt-3 mb-3">Create Real Account</a>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>