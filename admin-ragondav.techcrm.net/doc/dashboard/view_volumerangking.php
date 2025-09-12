
<div class="col-md-4">
    <div class="card custom-card">
        <div class="card-body">
            <label class="main-content-label mb-3 pt-1">Volume Rangking</label>
            <div class="table-responsive">
                <table class="table table-bordered mg-b-0 table-striped table-hover" id="tabel_VolumeRangking">
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
    let tabel_VolumeRangking;
    $(document).ready(function() {
        tabel_VolumeRangking = $('#tabel_VolumeRangking').DataTable({
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
                url: "/ajax/datatable/dashboard/volumerangking/view",
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