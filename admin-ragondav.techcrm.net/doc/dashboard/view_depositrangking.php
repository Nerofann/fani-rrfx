
<div class="col-md-4">
    <div class="card custom-card">
        <div class="card-body">
            <label class="main-content-label mb-3 pt-1">Deposit Rangking</label>
            <div class="table-responsive">
                <table class="table table-bordered mg-b-0 table-striped table-hover" id="tabel_DepositRangking">
                    <thead>
                        <tr class="text-center">
                            <th>Account</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    let tabel_DepositRangking;
    $(document).ready(function() {
        tabel_DepositRangking = $('#tabel_DepositRangking').DataTable({
            scrollX: true,
            processing: true,
            serverSide: true,
            deferRender: true,
            lengthChange: false,
            searching: false,
            ordering: false,
            paging: false,
            "info": false,
            ajax: {
                url: "/ajax/datatable/dashboard/depositrangking/view",
                contentType: "application/json",
                type: "GET",
            },
            columns: [
                { data: 'LOGIN' },
                { data: 'AMOUNT', className: 'text-end' }
            ],
        });
    })
</script>