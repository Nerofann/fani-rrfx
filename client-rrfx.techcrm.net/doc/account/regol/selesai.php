<div class="row">
    <div class="col-md-9 mx-auto mb-3">
        <div class="card mb-25">
            <div class="card-body">
                <h5 class="card-title text-primary mb-3">Selesai</h5>
                <p>Pendaftaran Real Account anda telah selesai, menunggu konfirmasi WPB</p>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title text-primary mb-3">Riwayat Penolakan</h5>
                <div class="table-responsive">
                    <table class="table table-hover" style="table-layout: fixed;" width="100%">
                        <thead>
                            <tr>
                                <th width="25%" class="text-start">Tanggal</th>
                                <th class="text-start">Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach(Account::getAccountHistoryNote($realAccount['ID_ACC']) as $history) : ?>
                                <tr>
                                    <td width="25%" class="top-align"><?= date("Y-m-d H:i:s", strtotime($history['NOTE_DATETIME'])); ?></td>
                                    <td class="top-align text-start"><?= $history['NOTE_NOTE'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>