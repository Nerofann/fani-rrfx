
<div class="col-md-4">
    <div class="card custom-card">
        <div class="card-header">
            <h6 class="main-content-label mb-1"><?= $adminPermissionCore->isHavePermission($moduleId, $permission)['desc'] ?></h6>
            <p class="text-muted card-sub-title">Top 10 <?= $adminPermissionCore->isHavePermission($moduleId, $permission)['desc'] ?></p>
        </div>
        <div class="card-body" style="overflow-y: hidden;height: 485px;">
            <div class="table-responsive">
                <table class="table table-bordered mg-b-0 table-striped table-hover" id="tabel_ProfitRangking">
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
    let tabel_ProfitRangking;
    $(document).ready(function() {
        tabel_ProfitRangking = $('#tabel_ProfitRangking').DataTable({
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
                url: "/ajax/datatable/dashboard/profitrangking/view",
                contentType: "application/json",
                type: "GET",
            },
            columns: [
                { data: 'LOGIN' },
                { data: 'AMOUNT', className: 'text-end' }
            ],
        });
    });
</script>