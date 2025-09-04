<?php if($permisCreate = $adminPermissionCore->isHavePermission($moduleId, "create.detail.symbolcategory")){ ?>
    <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#modal-create-category" class="btn btn-primary my-2 btn-icon-text"><i class="fas fa-plus"></i> Add Category</a>
    <div class="modal fade" tabindex="-1" id="modal-create-category">
        <div class="modal-dialog modal-dialog-top">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Category</h5>
                </div>
                <form action="<?= $permisCreate['link'] ?>" method="post" id="form-create-category">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="" class="form-label required">Category Name</label>
                            <input type="text" name="category_name" class="form-control" placeholder="Category Name" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function() {
            $('#form-create-category').on('submit', function(event) {
                event.preventDefault();
                let data = $(this).serialize(),
                    button = $(this).find('button[type="submit"]'),
                    url = "/ajax/post".concat($(this).attr('action'));

                button.addClass('loading');
                $.post(url, data, (resp) => {
                    $('#modal-create-category').modal('hide')
                    Swal.fire(resp.alert).then(() => {
                        if(resp.success) {
                            location.reload();
                        }
                    })
                }, 'json')
            })
        })
    </script>
<?php }; ?>