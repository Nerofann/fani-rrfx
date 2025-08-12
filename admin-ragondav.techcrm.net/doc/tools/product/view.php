<div class="page-header">
    <div>
        <h2 class="main-content-title tx-24 mg-b-5">Product</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0);">Home</a></li>
            <li class="breadcrumb-item"><a href="javascript:void(0);">Tools</a></li>
            <li class="breadcrumb-item active" aria-current="page">Product</li>
        </ol>
    </div>
    <div class="right">
        <?php if($permisCreate = $adminPermissionCore->isHavePermission($moduleId, "create")) : ?>
            <a href="<?= $permisCreate['link'] ?>" class="btn btn-sm btn-primary"><i class="fas fa-plus"></i> Tambah Product</a>
        <?php endif; ?>
    </div>
</div>
<div class="row row-sm">
    <div class="col-lg-12 col-md-12 col-md-12">
        <div class="card custom-card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered" id="table-product" data-url="<?= $filePermission['link']; ?>">
                        <thead>
                            <tr>
                                <th width="8%">Suffix</th>
                                <th>Nama</th>
                                <th>Detail</th>
                                <th>Group</th>
                                <th>Trading Rules</th>
                                <th>Status</th>
                                <th>#</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    let table;
    $(document).ready(function() {
        table = $('#table-product').DataTable({
            processing: true,
            serverSide: true,
            deferRender: true,
            order: [[0, 'asc']],
            lengthMenu: [[50, 100, -1], [50, 100, "All"]],
            buttons: [
                { extend: 'excel', text: "Excel" },
                { extend: 'csv', text: "CSV" },
                { extend: 'copy', text: "Copy" },
            ],
            ajax: {
                url: "/ajax/datatable".concat($('#table-product').data('url')),
                data: function() {

                }
            },
            columnDefs: [
                { targets: 4, className: "text-center" },
                { targets: 5, className: "text-center" },
                { targets: 6, className: "text-center" },
            ]
        })
    });
</script>

<?php require_once __DIR__ . "/update_button.php"; ?>