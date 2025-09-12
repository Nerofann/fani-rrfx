<?php if($permisPendingView = $adminPermissionCore->isHavePermission($moduleId, "view.pending")) : ?>
    <div class="card custom-card mb-3">
        <div class="card-header">
            <h5 class="card-title">Request Pending</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered" id="table-pending">
                    <thead>
                        <tr>
                            <th width="15%">Tanggal Pengajuan</th>
                            <th>User</th>
                            <th>Detail</th>
                            <th width="10%">#</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        let table_pending;
        $(document).ready(function() {
            table_pending = $('#table-pending').DataTable({
                processing: true,
                serverSide: true,
                deferRender: true,
                order: [[0, 'desc']],
                ajax: {
                    url: "/ajax/datatable/member/member_bank/pending"
                },
                // columnDefs: [
                //     { targets: 4, className: "text-center" },
                // ]
            })
        })
    </script>

    <?php require_once __DIR__ . "/action_button.php"; ?>
<?php endif; ?>