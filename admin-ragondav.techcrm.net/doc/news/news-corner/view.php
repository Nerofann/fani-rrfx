<?php
    use App\Models\FileUpload;
?>
<div class="page-header">
    <div>
        <h2 class="main-content-title tx-24 mg-b-5">News Corner</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= pathbreadcrumb(0) ?>/dashboard">Home</a></li>
            <li class="breadcrumb-item">News</li>
            <li class="breadcrumb-item active" aria-current="page">News Corner</li>
        </ol>
    </div>
</div>
<div class="row row-sm">
    <div class="col-lg-12 col-md-12 col-md-12">
        <form method="post" id="form-picture">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label class="">Title</label>
                                <input type="text" class="form-control" name="title" required placeholder="Enter News Title">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="">Author</label>
                                <input type="text" class="form-control" name="author" required placeholder="Enter Author Name">
                            </div>
                        </div>
                    </div>
                    <div class="ql-wrapper ql-wrapper-demo mb-3">
                        <label for="content" class="form-label">Content</label>
                        <input type="hidden" name="content" id="content" required>
                        <div class="editor"></div>
                    </div>
                    <label class="">Upload Image</label>
                    <div class="p-4 border rounded-6 mb-4 form-group">
                        <div>
                            <input class="dropify" type="file" name="files" accept="image/jpg, image/jpeg, image/png" required>
                        </div>
                    </div>
                </div>
                <div class="card-footer mb-1">
                    <button type="submit" class="btn btn-primary">Post</button>
                    <a href="javascript:void(0);" class="btn btn-danger">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- End Row -->
<div class="row row-sm">
    <?php
        $SQL_NEWS = mysqli_query($db, '
            SELECT
                MD5(MD5(tb_blog.ID_BLOG)) AS ID_BLOG,
                tb_blog.BLOG_TITLE,
                tb_blog.BLOG_MESSAGE,
                tb_blog.BLOG_AUTHOR,
                tb_blog.BLOG_IMG,
                tb_blog.BLOG_SLUG,
                tb_blog.BLOG_DATETIME,
                JSON_OBJECT(
                    "edt-title", tb_blog.BLOG_TITLE,
                    "edt-author", tb_blog.BLOG_AUTHOR,
                    "edt-content", tb_blog.BLOG_MESSAGE,
                    "edt-files", tb_blog.BLOG_IMG,
                    "edt-btn", MD5(MD5(tb_blog.ID_BLOG))
                ) AS JSNDT
            FROM tb_blog
            WHERE tb_blog.BLOG_TYPE = 2
        ');
        if($SQL_NEWS && mysqli_num_rows($SQL_NEWS) > 0){
            foreach(mysqli_fetch_all($SQL_NEWS, MYSQLI_ASSOC) as $RSLT_NEWS){
    ?>
        <div class="col-md-6">
            <div class="card card-aside custom-card">
                <a href="javascript:void(0);" class="card-aside-column  cover-image rounded-start-11" data-image-src="<?= FileUpload::awsFile(($RSLT_NEWS["BLOG_IMG"] ?? '')) ?>" style="background: url(<?= FileUpload::awsFile(($RSLT_NEWS["BLOG_IMG"] ?? '')) ?>) center center;"></a>
                <div class="card-body">
                    <div style="overflow-y: auto;height: 150px;">
                        <a href="javascript:void(0);"><span class="main-content-label tx-16"><?= substr(strip_tags(html_entity_decode($RSLT_NEWS["BLOG_MESSAGE"])), 0, 40).'...'; ?></span></a>
                        <div class="mt-3">
                            <?= substr(strip_tags(html_entity_decode($RSLT_NEWS["BLOG_MESSAGE"])), 0, 200).'...'; ?>
                        </div>
                    </div>
                    <div class="d-flex align-items-center pt-3 mt-auto">
                        <div>
                            <a href="javascript:void(0);" class="text-default"><?= $RSLT_NEWS["BLOG_DATETIME"] ?></a>
                            <small class="d-block text-muted">By: <?= $RSLT_NEWS["BLOG_AUTHOR"] ?></small>
                        </div>
                        <div class="ms-auto text-muted">
                            <a href="javascript:void(0);" class="icon d-none d-md-inline-block ms-3 text-danger dltBtn" data-value="<?= $RSLT_NEWS["ID_BLOG"] ?>"><i class="ti-trash me-1"></i> Delete</a>
                            <a href="javascript:void(0);" class="icon d-none d-md-inline-block ms-3 edt-btn" data-bs-target="#modaldemo3" data-bs-toggle="modal" data-jsn="<?= base64_encode($RSLT_NEWS["JSNDT"]) ?>"><i class="ti-pencil"></i> Edit</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php

            }
        }
    ?>

    <div class="modal" id="modaldemo3">
        <form method="post" id="form-update">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content modal-content-demo">
                    <div class="modal-header">
                        <h6 class="modal-title">Edit News</h6><button aria-label="Close" class="btn-close" data-bs-dismiss="modal" type="button">
                        <span aria-hidden="true">x</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label class="">Title</label>
                                        <input type="text" class="form-control" name="title" id="edt-title" required placeholder="Enter News Title">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="">Author</label>
                                        <input type="text" class="form-control" name="author" id="edt-author" required placeholder="Enter Author Name">
                                    </div>
                                </div>
                            </div>
                            <div class="ql-wrapper ql-wrapper-demo mb-3">
                                <label for="content" class="form-label">Content</label>
                                <textarea style="display: none;" name="content" id="edt-content" required></textarea>
                                <div class="editor"></div>
                            </div>
                            <label class="">Upload Image</label>
                            <div class="p-4 border rounded-6 mb-4 form-group">
                                <div>
                                    <input class="dropify" type="file" name="files" id="edt-files" accept="image/jpg, image/jpeg, image/png">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="edt-btn" id="edt-btn">
                        <button class="btn ripple btn-primary" type="submit">Save changes</button>
                        <button class="btn ripple btn-secondary" data-bs-dismiss="modal" type="button">Close</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
		function htmlDecode(input) {
			var doc = new DOMParser().parseFromString(input, "text/html");
			return doc.documentElement.textContent;
		}

        $('#table-news').DataTable({
            order: [[0, 'desc']],
            autoWidth: false
        });

        $('.editor').each((i, e) => {
            let quill = new Quill(e, {
                theme: 'snow',
            });
            quill.on('text-change', function(delta, oldDelta, source) {
                $(e).parent().find( "input[name='content']" ).val(quill.root.innerHTML);
                $(e).parent().find( "textarea[name='content']" ).val(quill.root.innerHTML);
            });
        });

        
        $('#form-picture').on('submit', function(ev){
            ev.preventDefault();
            $(this).find(':submit').addClass('loading');
            let data = new FormData(this);
            $.ajax({
                url         : '/ajax/post/news/news-corner/create',
                type        : 'POST',
                dataType    : 'JSON',
                enctype     : 'multipart/form-data',
                data        : data,
                contentType : false,
                chache      : false,
                processData : false
            }).done((resp) => {
                $('#modal-datepicker').modal('hide');
                Swal.fire(resp.alert).then(() => {
                    if(resp.success) {
                        if(resp?.data?.reloc?.length){
                            location.href = resp?.data?.reloc;
                        }else{ location.reload(); }
                    }
                });

            });
        });

        
        $('.edt-btn').on('click', function(){
            for(var [key, value] of Object.entries(JSON.parse(atob($(this).data('jsn'))))) {
                if($(`#${key}`)[0]?.tagName == 'INPUT'){
                    if($(`#${key}`).attr('type') != 'file'){
                        if($(`#${key}`).attr('type') == 'checkbox'){
                            if((($(`#${key}`).prop('checked')) && $(`#${key}`).val() == value) || ((!$(`#${key}`).prop('checked')) && $(`#${key}`).val() != value)){
                                $(`#${key}`)[0].click();
                            }
                        }else if($(`#${key}`).attr('class')?.includes('frmtRph')){
                            // console.log(value, $(`#${key}`));
                            $(`#${key}`).val(formatRupiah(value.toString()));
                        }else{ 
                            if($(`#${key}`).attr('type') != 'checkbox'){
                                // console.log(key);
                                $(`#${key}`).val(value); 
                            }
                        }
                    }else{
                        fln = (value !== null) ? `<?= FileUpload::awsUrl().'/' ?>${value}` : 0;
                        if($(`.dropify-render`).children().lenght){
                            $(`.dropify-render`).children().attr('src', fln);
                        }else{
                            $(`.dropify-render`).html(`
                                <img src="${fln}">
                            `);
                        }
                        $(`.dropify-filename-inner`).html(value);
                        $(`.dropify-preview`).css('display', 'block');
                    }
                }else if($(`#${key}`)[0]?.tagName == 'SELECT' || $(`#${key}`)[0]?.tagName == 'BUTTON'){
                    $(`#${key}`).val(value);
                    // if($(`#${key}`).attr('id') == 'edt_head'){
                    //     $(`#${key}`)[0].dispatchEvent(new Event('change'));
                    //     console.log('dispatched');
                    // }
                    // dsptch();
                }else if($(`#${key}`)[0]?.tagName == 'TEXTAREA'){
					let rgx = new RegExp(`(?:&nbsp;|\\\\r\\\\n|$)`, "g");  
					let val = htmlDecode(value.replaceAll(rgx, function(match, ett){
                        if(match == '\\r\\n'){
                            return '<br>';
                        }else if(ett == 213){
                            return ' ';
                        }else{ return ''; }
                    }));
                    $(`#${key}`).html(val);
                    $(`#${key}`).parent().find('.ql-editor').html(val);
                }
            }
        });

        
        $('#form-update').on('submit', function(ev){
            ev.preventDefault();
            $(this).find(':submit').addClass('loading');
            let data = new FormData(this);
            $.ajax({
                url         : '/ajax/post/news/news-corner/update',
                type        : 'POST',
                dataType    : 'JSON',
                enctype     : 'multipart/form-data',
                data        : data,
                contentType : false,
                chache      : false,
                processData : false
            }).done((resp) => {
                $('#modaldemo3').modal('hide');
                $(this).find(':submit').removeClass('loading');
                Swal.fire(resp.alert).then(() => {
                    if(resp.success) {
                        if(resp?.data?.reloc?.length){
                            location.href = resp?.data?.reloc;
                        }else{ location.reload(); }
                    }
                });

            });
        });

        
        $('.dltBtn').on('click', function(e){
            Swal.fire({
                title: `Delete News`,
                text: `Are you sure to delete this news?`,
                icon: 'question',
                showCancelButton: true,
                reverseButtons: true
            }).then((result) => {
                if(result.isConfirmed) {
                    $.post("/ajax/post/news/news-corner/delete", {x: $(this).data('value')}, function(resp) {
                        Swal.fire(resp.alert).then(() => {
                            if(resp.success) {
                                location.reload();
                            }
                        })
                    }, 'json');
                }
            });
        });
    })
</script>