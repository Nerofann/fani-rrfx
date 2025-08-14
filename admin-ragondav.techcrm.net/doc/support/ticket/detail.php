<?php
    use App\Models\Helper;
    
    $data = Helper::getSafeInput($_GET);
    $SQL_TCKTD = mysqli_query($db, '
        SELECT
            tb_ticket.TICKET_SUBJECT AS TIC_TITLE,
            tb_ticket.TICKET_STS AS TIC_STS,
            tb_ticket.TICKET_CODE AS TIC_CATEGORY,
            tb_member.MBR_EMAIL
        FROM tb_ticket
        JOIN tb_member
        ON(tb_ticket.TICKET_MBR = tb_member.MBR_ID)
        WHERE MD5(MD5(tb_ticket.ID_TICKET)) = "'.$data["d"].'"
        LIMIT 1
    ');
    if($SQL_TCKTD && mysqli_num_rows($SQL_TCKTD) > 0){
        $RSLT_TCKTD = mysqli_fetch_assoc($SQL_TCKTD);
    }
?>
<div class="page-header">
    <div>
        <h2 class="main-content-title tx-24 mg-b-5">Ticket Detail</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
            <li class="breadcrumb-item"><a href="/dashboard">Support</a></li>
            <li class="breadcrumb-item"><a href="/dashboard">Ticket</a></li>
            <li class="breadcrumb-item active" aria-current="page"><a href="#">Ticket Detail</a></li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-xl-12">
        <div class="card custom-card">
            <div class="main-content-app pt-0">
                <div class="main-content-body main-content-body-chat">
                    <div class="main-chat-header pt-3" id="chtHdr" style="justify-content: center;">
                        <div class="main-chat-msg-name text-center">
                            <h3><?= $RSLT_TCKTD["MBR_EMAIL"] ?></h3>
                            <h6 id="chtname" class="text-center"><?= $RSLT_TCKTD["TIC_TITLE"] ?></h6>
                            <small class="text-center"><?= $RSLT_TCKTD["TIC_CATEGORY"] ?></small>
                        </div>
                    </div><!-- main-chat-header -->
                    <div class="main-chat-body" id="ChatBody" style="overflow: auto !important;">
                        <div class="content-inner">
                            <div class="row sidemenu-height">
                                <div class="col-md-12">
                                    <div class="construction1 text-center details">
                                        <div class="">
                                            <div class="col-lg-12">
                                                <h1 class="tx-140 mb-0">
                                                    <i class="ti-comment-alt icon"></i>
                                                </h1>
                                            </div>
                                            <div class="col-lg-12 ">
                                                <h1>Please send something to start the conversation</h1>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php if($RSLT_TCKTD["TIC_STS"] == -1){ ?>
                        <form method="POST" id="chtFrm">
                            <div class="main-chat-footer" id="chtFtr">
                                <nav class="nav">
                                    <a class="nav-link" data-bs-target="#modal-datepicker" data-bs-toggle="modal" href="javascript:void(0);" title="Add Photo"><i class="fe fe-image"></i></a>
                                </nav>
                                <input class="form-control" id="chtInpt" name="chtcontent" placeholder="Type your message here..." type="text" required>
                                <a class="main-msg-send" id="sendBtn" href="javascript:void(0);"><i class="far fa-paper-plane"></i></a>
                            </div>
                        </form>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-datepicker">
    <div class="modal-dialog" role="document">
        <div class="modal-content modal-content-demo">
            <form method="post" enctype="multipart/form-data" id="detail-form">
                <div class="modal-body">
                    <div class="form-group" id="uloadMutasi">
                        <label>Upload Gambar</label>
                        <input type="file" name="mutasi" class="dropify dropify1" id="fileMutasi" accept="image/png, image/jpg, image/jpeg" data-height="200">
                    </div>
                </div>
                <div class="modal-footer text-center" style="display : none;">
                    <input type="hidden" name="sbmt_id" value="<?= $data["d"] ?>">
                    <input type="hidden" name="messg" id="acc-act">
                    <button type="submit" class="btn btn-primary ripple btn-block text-white" type="button" id="sendButton2">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(() => {
        async function refreshChat() {
            $.ajax({
                url: "/ajax/post/support/ticket/view_chats",
                type: "post",
                dataType: "html",
                data: {
                    code: '<?= $RSLT_TCKTD["TIC_CATEGORY"] ?>'
                }
            }).done((html) => {
                $('#ChatBody').empty().html(html)
                Swal.close();
            });
        }

        refreshChat();

        $('#chtInpt').on('keyup', function(e){
            $('#acc-act').val($(this).val());
        });

        let DPF = $('.dropify').dropify();
        DPF.on('dropify.fileReady', function(e){
            $('#chtInpt').prop('required', false);
        });
        DPF.on('dropify.afterClear', function(e){
            $('#chtInpt').prop('required', true);
        });

        $('#sendBtn').on('click', function(e){
            if(!$('#chtInpt')[0].checkValidity()) {
                $('#chtInpt')[0].reportValidity();
            }else{ $('#sendButton2').click(); }
        });

        
        $('#detail-form').on('submit', function(ev){
            ev.preventDefault();
            let data = new FormData(this);
            $.ajax({
                url         : '/ajax/post/support/ticket/send_chats',
                type        : 'POST',
                dataType    : 'JSON',
                enctype     : 'multipart/form-data',
                data        : data,
                contentType : false,
                chache      : false,
                processData : false
            }).done((resp) => {
                if(!resp.success){
                    Swal.fire(resp.alert)
                }else{ 
                    $('#chtInpt').val('');
                    DPF.data('dropify').resetPreview();
                    DPF.data('dropify').clearElement();
                    refreshChat(); 
                }
            });
        });

    });
</script>