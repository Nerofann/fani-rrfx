
<div class="page-header">
    <div>
        <h2 class="main-content-title tx-24 mg-b-5">Wakil Pialang</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= pathbreadcrumb(0) ?>/dashboard">Home</a></li>
            <li class="breadcrumb-item"><a href="javascript:void(0);">Tools</a></li>
            <li class="breadcrumb-item active" aria-current="page">Wakil Pialang</li>
        </ol>
    </div>
</div>
<div class="row mb-3">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between mb-1">
                    <h5 class="card-title">WPB List</h5>
                    <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#modalAddWpb" class="btn btn-sm btn-primary"><i class="fas fa-plus"></i> Add WPB</a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped table-bordered" width="100%" id="table-wpb">
                        <thead>
                            <tr class="text-center">
                                <th>Nama</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>        
        </div>
    </div>
</div>
<div class="modal fade" id="modalAddWpb" tabindex="-1" aria-labelledby="labelmodalAddWpb">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add WPB</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="" method="POST" id="form_add_wpb">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 col-sm-12 mb-3">
                            <div class="form-group">
                                <label for="nama_wpb" class="form-label">Nama WPB</label>
                                <input type="text" class="form-control" name="nama_wpb" id="nama_wpb" placeholder="nama" required>
                            </div>
                        </div>
    
                        <div class="col-md-12 col-sm-12 mb-3">
                            <div class="form-group">
                                <label for="type" class="form-label">Type</label>
                                <select name="type" id="type" class="form-control" required>
                                    <option value="">Select</option>
                                    <option value="1">WPB Perushaan</option>
                                    <option value="2">WPB Yang Di Tunjuk Untuk Verifikasi</option>
                                </select>
                            </div>
                        </div>
    
                        <div class="col-md-12 col-sm-12 mb-3">
                            <div class="form-group">
                                <label>Status</label>
                                <div>
                                    <label class="custom-switch">
                                        <input type="checkbox" name="wpb_sts" value="-1" class="custom-switch-input sckbx">
                                        <span class="custom-switch-indicator custom-switch-indicator-lg"></span>
                                        <span class="custom-switch-description tx-20 me-2">Unactive</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
    
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success" name="add-wpb">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="modalEdtWpb" tabindex="-1" aria-labelledby="labelmodalEdtWpb">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit WPB</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="" method="POST" id="form_edt_wpb">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 col-sm-12 mb-3">
                            <div class="form-group">
                                <label for="nama_wpb" class="form-label">Nama WPB</label>
                                <input type="text" class="form-control" name="nama_wpb" id="edt_nama" placeholder="nama" required>
                            </div>
                        </div>
    
                        <div class="col-md-12 col-sm-12 mb-3">
                            <div class="form-group">
                                <label for="type" class="form-label">Type</label>
                                <select name="type" id="edt_type" class="form-control" required>
                                    <option value="">Select</option>
                                    <option value="1">WPB Perushaan</option>
                                    <option value="2">WPB Yang Di Tunjuk Untuk Verifikasi</option>
                                </select>
                            </div>
                        </div>
    
                        <div class="col-md-12 col-sm-12 mb-3">
                            <div class="form-group">
                                <label>Status</label>
                                <div>
                                    <label class="custom-switch">
                                        <input type="checkbox" name="wpb_sts" id="edt_stat" value="-1" class="custom-switch-input sckbx">
                                        <span class="custom-switch-indicator custom-switch-indicator-lg"></span>
                                        <span class="custom-switch-description tx-20 me-2">Unactive</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
    
                <div class="modal-footer">
                    <input type="hidden" name="edt_idnt" id="edt_idnt">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success" name="add-wpb">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    $(document).ready(() => {
        let scnkbxh = '';
        $('.sckbx').on('change', function(e){
            scnkbxh = ($(this).prop('checked')) ? 'Active' : 'Unactive';
            $(this).parent().find('.custom-switch-description').html(scnkbxh)
        });
        
        $('#form_add_wpb').on('submit', function(ev){
            ev.preventDefault();
            let sbmtBtn = $(this).find(':submit');
            sbmtBtn.addClass('loading');
            let data = Object.fromEntries(new FormData(this));

            $.post("/ajax/post/tools/wakil_pialang/create", data, function(resp) {
                $('#modalAddWpb').modal('hide');
                Swal.fire(resp.alert).then(() => {
                    if(resp.success) {
                        location.reload();
                    }
                });
            }, 'json');
            sbmtBtn.removeClass('loading');
        });
        
        $('#form_edt_wpb').on('submit', function(ev){
            ev.preventDefault();
            let sbmtBtn = $(this).find(':submit');
            sbmtBtn.addClass('loading');
            let data = Object.fromEntries(new FormData(this));

            $.post("/ajax/post/tools/wakil_pialang/update", data, function(resp) {
                $('#modalEdtWpb').modal('hide');
                Swal.fire(resp.alert).then(() => {
                    if(resp.success) {
                        location.reload();
                    }
                });
                sbmtBtn.removeClass('loading');
            }, 'json');
        });

        $('#table-wpb').DataTable({
            dom: 'Blfrtip',
            processing: true,
            serverSide: true,
            deferRender: true,
			buttons: [
				{
					extend: 'excel',
					text: 'Excel',
				},
				{
					extend: 'copy',
					text: 'Copy'
				}
			],
            lengthMenu: [[10, 50, 100, -1], [10, 50, 100, "All"]],
            scrollX: true,
            order: [[ 0, "desc" ]],
            ajax: {
                url: "/ajax/datatable/tools/wakil_pialang/view",
                contentType: "application/json",
                type: "GET"
            },
            drawCallback: function(tbl){
                $('.verfBtn').on('click', function(e){
                    Swal.fire({
                        title: `Tunjuk WPB verificator`,
                        text: `Tunjuk ${$(this).data('name')} sebagai WPB verificator?`,
                        icon: 'question',
                        showCancelButton: true,
                        reverseButtons: true
                    }).then((result) => {
                        if(result.isConfirmed) {
                            $.post("/ajax/post/tools/wakil_pialang/update_verificator", {x: $(this).data('value')}, function(resp) {
                                Swal.fire(resp.alert).then(() => {
                                    if(resp.success) {
                                        location.reload();
                                    }
                                })
                            }, 'json');
                        }
                    });
                });
                $('.dltBtn').on('click', function(e){
                    Swal.fire({
                        title: `Delete Officer`,
                        text: `Are you sure to delete this officer?`,
                        icon: 'question',
                        showCancelButton: true,
                        reverseButtons: true
                    }).then((result) => {
                        if(result.isConfirmed) {
                            $.post("/ajax/post/tools/wakil_pialang/delete", {x: $(this).data('value')}, function(resp) {
                                Swal.fire(resp.alert).then(() => {
                                    if(resp.success) {
                                        location.reload();
                                    }
                                })
                            }, 'json');
                        }
                    });
                });
                $('.edt-btn').on('click', function(e){
                    for(var [key, value] of Object.entries(JSON.parse(atob($(this).data('jsn'))))) {
                        if($(`#${key}`)[0]?.tagName == 'INPUT'){
                            if($(`#${key}`).attr('type') != 'file'){
                                if($(`#${key}`).attr('type') == 'checkbox'){
                                    if((($(`#${key}`).prop('checked')) && $(`#${key}`).val() != value) || ((!$(`#${key}`).prop('checked')) && $(`#${key}`).val() == value)){
                                        $(`#${key}`)[0].click();
                                    }
                                }else if($(`#${key}`).attr('class')?.includes('frmtRph')){
                                    // console.log(value, $(`#${key}`));
                                    $(`#${key}`).val(formatRupiah(value.toString()));
                                }else{ 
                                    if($(`#${key}`).attr('type') != 'checkbox'){
                                        $(`#${key}`).val(value); 
                                    }
                                }
                            }else{
                                fln = (value !== null) ? `<?= $aws_folder ?>${value}` : dfltflnm;
                                $(`.dropify-render`).children().attr('src', fln);
                                $(`.dropify-filename-inner`).html(value);
                            }
                        }else if($(`#${key}`)[0]?.tagName == 'SELECT' || $(`#${key}`)[0]?.tagName == 'BUTTON'){
                            $(`#${key}`).val(value);
                            // if($(`#${key}`).attr('id') == 'edt_head'){
                            //     $(`#${key}`)[0].dispatchEvent(new Event('change'));
                            //     console.log('dispatched');
                            // }
                            // dsptch();
                        }else if($(`#${key}`)[0]?.tagName == 'TEXTAREA'){
                            $(`#${key}`).html(value.replaceArray(["\\\\r\\\\n", "&amp;nbsp;"], ["&#13;&#10;", " "]));
                        }
                    }
                });
            }
        });
    });
</script>