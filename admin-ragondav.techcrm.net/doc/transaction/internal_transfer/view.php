<div class="page-header">
    <div>
        <h2 class="main-content-title tx-24 mg-b-5">Internal Transfer</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0);">Home</a></li>
            <li class="breadcrumb-item"><a href="javascript:void(0);">Transaction</a></li>
            <li class="breadcrumb-item active" aria-current="page">Internal Transfer</li>
        </ol>
    </div>
</div>


<div class="row row-sm">
    <div class="col-lg-12 mb-3">
        <div class="card custom-card overflow-hidden">
            <div class="card-header">
                <h5 class="card-title">Internal-transfer</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="table-pending" class="table table-bordered table-striped table-hover key-buttons text-nowrap w-100" >
                        <thead>
                            <tr>
                                <th style="vertical-align: middle" class="text-center">Date</th>
                                <th style="vertical-align: middle" class="text-center">Name</th>
                                <!-- <th style="vertical-align: middle" class="text-center">Username</th> -->
                                <th style="vertical-align: middle" class="text-center">Email</th>
                                <th style="vertical-align: middle" class="text-center">From</th>
                                <th style="vertical-align: middle" class="text-center">To</th>
                                <th style="vertical-align: middle" class="text-center">Ammount</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(() => {
        $('#table-pending').DataTable({
            dom: 'Blfrtip',
            processing: true,
            serverSide: true,
            deferRender: true,
            lengthMenu: [[10, 50, 100, -1], [10, 50, 100, "All"]],
            scrollX: true,
            order: [[ 0, "desc" ]],
            ajax: {
                url: "/ajax/datatable/transaction/internal_transfer/view",
                contentType: "application/json",
                type: "GET"
            },
        });
    });
</script>