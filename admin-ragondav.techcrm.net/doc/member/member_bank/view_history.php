<?php if($permisPendingView = $adminPermissionCore->isHavePermission($moduleId, "view.pending")) : ?>
    <div class="card custom-card mb-3">
        <div class="card-header">
            <h5 class="card-title">Riwayat</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered" id="table-history">
                    <thead>
                        <tr>
                            <th width="15%">Tanggal Pengajuan</th>
                            <th width="15%">Tanggal Diterima</th>
                            <th>User</th>
                            <th>Detail</th>
                            <th>File</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        let table_history;
        $(document).ready(function() {
            table_history = $('#table-history').DataTable({
                processing: true,
                serverSide: true,
                deferRender: true,
                order: [[0, 'desc']],
                ajax: {
                    url: "/ajax/datatable/member/member_bank/history"
                },
            })
        })
    </script>
<?php endif; ?>