<div class="dashboard-breadcrumb mb-25">
    <div class="d-flex align-items-center">
        <h2 class="mb-0">Account &nbsp;</h2>
    </div>
    <div class="input-group-a dashboard-filter">
        <a href="/account/create" class="btn btn-sm btn-primary"><i class="fas fa-plus"></i> Create Account</a>
    </div>
</div>
<style>
</style>
<div class="row">
    <div class="col-12">
        <div class="panel">
            <div class="panel-body">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-dashed table-hover digi-dataTable dataTable-resize table-striped" id="account-table">
                                <thead>
                                    <tr class="text-center">
                                        <th class="text-center" rowspan="2">Tanggal Dibuat</th>
                                        <th class="text-center" colspan="4">Account</th>
                                        <th class="text-center" rowspan="2">Status</th>
                                        <th class="text-center" rowspan="2">Note</th>
                                        <th class="text-center" rowspan="2" class="text-center">#</th>
                                    </tr>
                                    <tr>
                                        <th class="text-center">Login</th>
                                        <th class="text-center">Tipe</th>
                                        <th class="text-center">Currency</th>
                                        <th class="text-center">Rate</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $('#account-table').DataTable({
            scrollX: true,
            processing: true,
            serverSide: true,
            order: [[0, 'desc']],
            ajax: {
                url: "/ajax/datatable/account",
            },
            columnDefs: [
                { targets: 2, orderable: false },
                { targets: 3, orderable: false },
                { targets: 5, orderable: false },
                { targets: 6, orderable: false },
                { targets: 7, orderable: false },
            ]
        });

        $('#createDemo').on('submit', function(event) {
            event.preventDefault();
            Swal.fire({
                text: "Please wait...",
                allowOutsideClick: false,
                didOpen: function() {
                    Swal.showLoading();
                }
            })
            
            let object = Object.fromEntries(new FormData(this).entries())
            $.post("/ajax/regol/createDemo", object, function(resp) {
                if(!resp.success) {
                    Swal.fire("Failed", resp.error, "error");
                    return false;
                }

                Swal.fire("Success", resp.message, "success")
                // $('#login').text(resp.data.login);
                // $('#passw').text(resp.data.passw);
                // $('#invst').text(resp.data.invst);
                // $('#phone').text(resp.data.phone);
                // $('#nte').text(resp.data.mails);
            }, 'json')
        });
    })
</script>