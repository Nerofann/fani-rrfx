<div class="col-md-4 mb-3">
    <div class="panel">
        <div class="panel-header">
            <h5 class="panel-title">Tambah Bank</h5>
        </div>
        <div class="panel-body">
            <form action="" method="post" id="form-tambah-bank">
                <div class="row">
                    <div class="col-md-12 mb-2">
                        <label for="bank-name" class="form-label">Nama Bank</label>
                        <select name="bank-name" id="bank-name" class="form-control form-select">
                            <?php foreach(App\Models\BankList::all() as $bank) : ?>
                                <option value="<?= $bank['BANKLST_NAME'] ?>"><?= $bank['BANKLST_NAME'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-12 mb-2">
                        <label for="name" class="form-label">Nama Pemilik Rekening</label>
                        <input type="text" class="form-control" name="name" value="<?= $user['MBR_NAME'] ?>" pattern="[A-Za-z]+(?: [A-Za-z]+)*" placeholder="Nama Pemilik Rekening">
                    </div>
                    <div class="col-md-12 mb-2">
                        <label for="bank-number" class="form-label">No. Rekening</label>
                        <input type="text" class="form-control" name="bank-number" placeholder="Nomor Rekening" pattern="\d{1,16}" maxlength="16">
                    </div>
                    <div class="col-md-12 mb-2">
                        <button type="submit" class="btn btn-primary btn-sm btn-block w-100">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $('#form-tambah-bank').on('submit', function(event) {
            event.preventDefault();
            let data = $(this).serialize();
            let button = $(this).find('button[type="submit"]');
        
            button.addClass('loading');
            $.post("/ajax/post/profile/create-bank", data, (resp) => {
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