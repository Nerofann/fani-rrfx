<?php if(count(App\Models\Account::myAccount($user['MBR_ID'])) > 0) : ?>
    <div class="dashboard-breadcrumb mb-25">
        <h2>Deposit</h2>
        <div class="input-group-a dashboard-filter">
            <a href="/deposit/create" class="btn btn-sm btn-primary"><i class="fa-light fa-arrow-right-to-bracket"></i> Tambah Deposit</a>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="panel">
                <div class="card">
                    <!-- <div class="card-header">
                        Deposit 
                    </div> -->
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-dashed table-hover table-bordered digi-dataTable table-striped" id="deposit-table">
                                <thead>
                                    <tr>
                                        <th rowspan="2" class="text-center">Date</th>
                                        <th rowspan="2" class="text-center">Account</th>
                                        <th colspan="2" class="text-center">Amount</th>
                                        <th rowspan="2" class="text-center">Rate</th>
                                        <th rowspan="2" class="text-center">Img</th>
                                        <th rowspan="2" class="text-center">Status</th>
                                        <th rowspan="2" class="text-center">Note</th>
                                    </tr>
                                    <tr>
                                        <th class="text-center">Request</th>
                                        <th class="text-center">Received</th>
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
            $('#deposit-table').DataTable({
                processing: true,
                serverSide: true,
                order: [[0, 'desc']],
                ajax: {
                    url: "/ajax/datatable/deposit",
                },
                columnDefs: [
                    { targets: 6, className: "text-center" }
                ]
            })
        })
    </script>

<?php else : ?>
    <div class="panel">
        <div class="card">
            <div class="card-body">
                <div class="text-center">
                    <a href="/create-acc" class="text-center btn btn-md btn-primary mt-3 mb-3">Create Real Account</a>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
