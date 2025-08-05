<?php

use App\Models\FileUpload;
use App\Models\Helper;
use App\Models\Ticket;

$ticketCode = Helper::form_input($_POST['code'] ?? "-");
$ticket = Ticket::findByCode($ticketCode);
if(!$ticket) {
    JsonResponse([
        'success' => false,
        'message' => "Invalid Code",
        'data' => []
    ]);
}

$history = [];
$chatHistory = Ticket::historyChatByTicketCode($ticketCode);
?>

<?php foreach($chatHistory as $chat) : ?>
    <?php $timestamp = (date("Y-m-d", strtotime($chat['TDETAIL_DATETIME'])) == date("Y-m-d"))? date("H:i", strtotime($chat['TDETAIL_DATETIME'])) : date("Y/m/d H:i", strtotime($chat['TDETAIL_DATETIME'])); ?>
    <?php if($chat['TDETAIL_TYPE'] == "member") : ?>
        <div class="single-message-outgoing">
            <div class="msg-box-inner">
                <?php if($chat['TDETAIL_CONTENT_TYPE'] == "image") : ?>
                    <div class="msg-img">
                        <img src="<?= $chat['TDETAIL_CONTENT']; ?>" alt="Image" />
                    </div>

                <?php else : ?>
                    <div class="msg-option">
                        <p><?= $chat['TDETAIL_CONTENT'] ?></p>
                        <span class="msg-time"><?= $timestamp ?></span>
                    </div>

                <?php endif; ?>
            </div>        
        </div>
    <?php endif; ?>
<?php endforeach; ?>