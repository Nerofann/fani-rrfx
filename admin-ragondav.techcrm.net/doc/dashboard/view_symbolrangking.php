
<div class="col-md-4">
    <div class="card custom-card">
        <div class="card-body">
            <label class="main-content-label mb-3 pt-1">Symbol Rangking</label>
            <div class="table-responsive">
                <table class="table table-bordered mg-b-0 table-striped table-hover" id="tabel_SymbolRangking">
                    <thead>
                        <tr class="text-center">
                            <th>Symbol</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    let tabel_SymbolRangking;
    $(document).ready(function() {
        tabel_SymbolRangking = $('#tabel_SymbolRangking').DataTable({
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
                url: "/ajax/datatable/dashboard/symbolrangking/view",
                contentType: "application/json",
                type: "GET",
            },
            columns: [
                { data: 'SYMBOL' },
                { data: 'AMOUNT', className: 'text-end' }
            ],
        });
    });
</script>