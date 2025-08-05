<?php

use App\Models\Helper;
use App\Models\Ticket;

$ticketCode = Helper::form_input($_GET['code'] ?? "-");
$ticket = Ticket::findByCode($ticketCode);
if(!$ticket) {
    die("<script>alert('Invalid Code'); location.href = '/ticket';</script>");
}


?>

<style>
    .chat-area {
        height: 350px;
        padding: 1rem;
        background-color: #141414;
        border-radius: 5px;
        overflow-y: auto;
    }

    /* width */
    .chat-area::-webkit-scrollbar {
        width: 6px;
    }

    /* Track */
    .chat-area::-webkit-scrollbar-track {
        background: transparent;
    }

    /* Handle */
    .chat-area::-webkit-scrollbar-thumb {
        background: #848484;
        border-radius: 10px;
    }

    .chat-area .single-message-outgoing {
        justify-content: flex-end !important;
    }

    .chat-area .single-message-outgoing,
    .chat-area .single-message {
        display: flex;
        flex-direction: row;
    }

    .chat-area .single-message-outgoing .msg-box-inner {
        margin-inline-start: 15%;
        display: flex;
        margin-bottom: 1rem;
        flex-direction: column;
        justify-content: start;
        width: fit-content;
        background: #242526;
        padding: 3px 10px;
        border-radius: 3px;
        border-right-color: white;
        border-right-width: 3px;
        border-right-style: solid;
    }

    .chat-area .single-message .msg-box-inner {
        margin-inline-end: 15%;
        display: flex;
        margin-bottom: 1rem;
        flex-direction: column;
        justify-content: start;
        width: fit-content;
        background: #0D99FF;
        padding: 3px 10px;
        border-radius: 3px;
        border-left-color: white;
        border-left-width: 3px;
        border-left-style: solid;
    }

    .chat-area .single-message-outgoing .msg-box-inner:has(.msg-img) {
        max-width: 250px;
    }


    .chat-area .single-message-outgoing .msg-box-inner .msg-img,
    .chat-area .single-message .msg-box-inner .msg-img {
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 100%;
        height: 100%;
        padding-top: 5px;
    }

    .chat-area .single-message-outgoing .msg-box-inner .msg-img img,
    .chat-area .single-message .msg-box-inner .msg-img img {
        max-width: 100%;
        height: max-content;
        margin-bottom: 1rem;
        border-radius: 5px;
    }

    .chat-area .single-message-outgoing .msg-box-inner .msg-option .msg-time,
    .chat-area .single-message .msg-box-inner .msg-option .msg-time {
        float: inline-end;
        font-size: 10px !important;
        font-style: italic;
    }


    .chat-area .single-message-outgoing .msg-box-inner .msg-option .msg-text, 
    .chat-area .single-message-outgoing .msg-box-inner .msg-option p, 
    .chat-area .single-message-outgoing .msg-box-inner .msg-option .msg-time,

    .chat-area .single-message .msg-box-inner .msg-option .msg-text, 
    .chat-area .single-message .msg-box-inner .msg-option p, 
    .chat-area .single-message .msg-box-inner .msg-option .msg-time 
    {
        text-align: justify;
        font-size: 13px;
        letter-spacing: 0;
        color: #fff;
    }

    .msg-type-area {
        width: 100%;
    }

    .msg-type-area form {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .msg-type-area form input[type="file"] {
        display: none;
    }
</style>

<div class="dashboard-breadcrumb mb-25">
    <h2>Tiket <b class="text-primary">#<?= $ticketCode ?></b></h2>
</div>

<div class="panel">
    <div class="panel-body">
        <div class="panel-header px-0">
            <div class="d-flex w-100 justify-content-between align-items-center gap-3">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar">
                        <img src="/assets/images/logo-icon.png" alt="User">
                    </div>
                    <div class="small d-flex flex-column align-items-start ms-2">
                        <span class="user-name fw-bold">Customer Service</span>
                        <small class="text-success">Online</small>
                    </div>
                </div>
                <div>
                    <a href="javascript:void(0)" class="btn btn-sm btn-danger" id="close">Tutup Tiket</a>
                </div>
            </div>
        </div>

        <div class="chat-area">
            <div class="chat-content">
                <div class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>

<!-- 
                <div class="single-message-outgoing">
                    <div class="msg-box-inner">
                        <div class="msg-option">
                            <p>Saya mengirim foto</p>
                            <span class="msg-time">2 Apr 2024</span>
                        </div>
                    </div>
                </div>


                <div class="single-message-outgoing">
                    <div class="msg-box-inner">
                        <div class="msg-option">
                            <p>
                            Omnis distinctio eaque voluptatibus. Reiciendis natus harum ea ipsam, et facere? Omnis distinctio eaque voluptatibus. Reiciendis natus harum ea ipsam, et facere? Omnis distinctio eaque voluptatibus. Reiciendis natus harum ea ipsam, et facere? Omnis distinctio eaque voluptatibus. Reiciendis natus harum ea ipsam, et facere? Omnis distinctio eaque voluptatibus. Reiciendis natus harum ea ipsam, et facere? Omnis distinctio eaque voluptatibus. Reiciendis natus harum ea ipsam, et facere?
                            </p>
                            <span class="msg-time">2 Apr 2024</span>
                        </div>
                    </div>
                </div> -->
            </div>
        </div>
        <div class="panel-body msg-type-area">
            <?php if($ticket['TICKET_STS'] == -1) : ?>
                <form id="form-send-message" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="code" value="<?= $ticketCode ?>">
                    <label class="btn btn-icon btn-outline-primary" for="chatAttachment"><i class="fa-light fa-link"></i></label>
                    <input type="file" name="attachment" class="chat-attachment" id="chatAttachment">
                    <input autocomplete="off" type="text" class="form-control chat-input" autofocus="" name="message" id="chat-input" placeholder="Type your message...">
                    <button class="btn btn-icon btn-send btn-outline-primary" style="width: 50px;"><i class="fa-light fa-paper-plane"></i></button>
                </form>
            <?php else : ?>
                <input type="hidden" name="code" value="<?= $ticketCode ?>">
                <p class="text-center text-decoration-underline"><i>Ticket Closed at <?= date("Y-m-d", strtotime($ticket['TICKET_DATETIME_CLOSE'])) ?></i></p>
            <?php endif; ?>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        let code = $('input[name="code"]').val();
        load_chat(code);

        // Send Message
        $('#form-send-message').on('submit', function(event) {
            event.preventDefault();
            
            if(!$("#chat-input").val()?.length && !$('#chatAttachment').val()?.length) {
                Swal.fire("Failed", "Mohon isi pesan", "error")
                return false
            }
            
            let formData = new FormData(this)
            $.ajax({
                url: "/ajax/post/ticket/send-chat",
                type: "post",
                data: formData,
                processData: false,
                contentType: false,
                cache: false
            })
            .done(function(resp) {
                resp = JSON.parse(resp);

                if(!resp?.success) {
                    Swal.fire("Error", (resp?.error || "Gagal mengirim pesan"), 'error')
                    return false
                }

                $('#chat-input').val("");
                $('#chatAttachment').val("")
                load_chat(code)
            })
        })

        $('#close').on('click', function() {
            Swal.fire({
                title: "Tutup Tiket?",
                text: "Konfirmasi untuk melanjutkan",
                icon: "question",
                showCancelButton: true,
                reverseButtons: true
            }).then((result) => {
                if(result.isConfirmed) {
                    Swal.fire({
                        text: "Loading...",
                        allowOutsideClick: false,
                        didOpen: function() {
                            Swal.showLoading();
                        }
                    })

                    $.post("/ajax/post/ticket/close", {code: code}, (resp) => {
                        Swal.fire(resp.alert).then(() => {
                            if(resp.success) {
                                location.reload();
                            }
                        })
                    }, 'json')
                }
            })
        })
    })

    async function load_chat(code) {
        $.ajax({
            url: "/ajax/post/ticket/load-chat",
            type: "post",
            dataType: "html",
            data: {
                code: code
            }
        }).done((html) => {
            $('.chat-content').empty().html(html)
            $('.chat-area').animate({scrollTop: $('.chat-content').height()})
        })
    }
</script>
