
<div class="page-header">
    <div>
        <h2 class="main-content-title tx-24 mg-b-5">Active Real Account</h2>
        <ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="<?= pathbreadcrumb(0) ?>/dashboard">Home</a></li>
            <li class="breadcrumb-item"><a href="javascript:void(0);">Account</a></li>
            <li class="breadcrumb-item active" aria-current="page"><a href="javascript:void(0);">Active Real Account</a></li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <span style="vertical-align: -webkit-baseline-middle;">
                    Active Real Account
                </span>
                <form class="form-inline" style="display: inline-flex; float: inline-end;" method="GET" id="fltFrm">
                    <div class="form-group mb-2">
                        <label>Filter</label>
                    </div>
                    <div class="form-group mx-sm-3 mb-2">
                        <input type="month" class="form-control" id="inputPassword2" name="mnthfltr">
                    </div>
                    <button type="submit" class="btn btn-success mb-2" id="fltrBtn">Filter</button>
                    &nbsp;
                    <button type="reset" class="btn btn-danger mb-2">Reset</button>
                </form>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered table-striped" id="table">
                        <thead>
                            <tr>
                                <th style="vertical-align: middle" class="text-center">Date Reg</th>
                                <th style="vertical-align: middle" class="text-center">Login</th>
                                <th style="vertical-align: middle" class="text-center">Nama</th>
                                <th style="vertical-align: middle" class="text-center">Email</th>
                                <th style="vertical-align: middle" class="text-center">Type Acc</th>
                                <th style="vertical-align: middle" class="text-center">Product</th>
                                <th style="vertical-align: middle" class="text-center">Rate</th>
                                <th style="vertical-align: middle" class="text-center">Status</th>
                                <th style="vertical-align: middle" class="text-center">Active Date</th>
                                <th style="vertical-align: middle" class="text-center">Action</th>
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
        let prm = {};
        let tbl = $('#table').DataTable( {
            dom: 'Blfrtip',
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "/ajax/datatable/account/active_real_account/view",
                "contentType": "application/json",
                "type": "GET",
                "data": function (d) {
                    return  $.extend(d, prm);
                }
            },
            "deferRender": true,
            "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
            "scrollX": true,
            "order": [[ 0, "desc" ]],
            buttons: ['copy', 'excel', 'csv']
        } );
        $('#fltFrm').on('submit', (elm) => {
            elm.preventDefault();
            let dt = new FormData(elm.target);
            prm = Object.fromEntries(dt);
            tbl.ajax.reload();
        });
        $('#fltFrm').on('reset', (elm) => {
            setTimeout(() => {
                $('#fltrBtn').click();  
            }, 500);  
        });
    });
</script>