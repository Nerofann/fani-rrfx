<div class="modal fade" id="modal-edit-bank" style="background-color: #0000008a;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Bank</h5>
                <button type="button" class="btn-close" aria-label="Close" data-bs-dismiss="modal">&times;</button>
            </div>
            <form action="" method="post" id="form-edit-bank">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-2">
                            <label for="bank-name" class="form-label">Nama Bank</label>
                            <select name="bank-name" class="form-control form-select">
                                <?php foreach(App\Models\BankList::all() as $bank) : ?>
                                    <option value="<?= $bank['BANKLST_NAME'] ?>"><?= $bank['BANKLST_NAME'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-12 mb-2">
                            <label for="name" class="form-label">Nama Pemilik Rekening</label>
                            <input type="text" class="form-control" name="name" value="<?= $user['MBR_NAME'] ?>" placeholder="Nama Pemilik Rekening">
                        </div>
                        <div class="col-md-12 mb-2">
                            <label for="bank-number" class="form-label">No. Rekening</label>
                            <input type="number" class="form-control" name="bank-number" placeholder="Nomor Rekening">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="id" value="">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $('#modal-edit-bank').on('show.bs.modal', function(evt) {
            let target = $(evt.relatedTarget);
            if(target) {
                let data = target.data();
                $(this).find('input[name="id"]').val(data.id);
                $(this).find('select[name="bank-name"]').val(data.name);
                $(this).find('input[name="name"]').val(data.holder);
                $(this).find('input[name="bank-number"]').val(data.account);

            }
        })

        $('#form-edit-bank').on('submit', function(event) {
            event.preventDefault();
            let data = $(this).serialize();
            let button = $(this).find('button[type="submit"]');

            button.addClass('loading');
            $.post("/ajax/post/profile/update-bank", data, (resp) => {
                button.removeClass('loading');
                Swal.fire(resp.alert).then(() => {
                    if(resp.success) {
                        location.reload();
                    }
                })
            }, 'json')
        })
    })
</script>