<?php
namespace Config\Core;

require_once(__DIR__ . '/../vendor/phpmailer/phpmailer/src/Exception.php');
require_once(__DIR__ . '/../vendor/phpmailer/phpmailer/src/PHPMailer.php');
require_once(__DIR__ . '/../vendor/phpmailer/phpmailer/src/SMTP.php');
require_once(__DIR__ . "/../vendor/autoload.php");

use PHPmailer\PHPMailer\Exception;
use PHPmailer\PHPMailer\PHPMailer;
use PHPmailer\PHPMailer\SMTP;
use Dotenv\Dotenv;
use Exception as PHPException;

class EmailSender {
    /** Default Email Configuration */
    protected string $host, $email, $password, $port, $name, $secure;
    protected string $folder = (__DIR__ . "/../email/");
    protected PHPMailer $mail;
    protected $file, $fileData, $subject;
    protected $receiverName, $receiverEmail;

    public static function init(array $receiver) {
        if(empty($receiver['name'])) {
            throw new PHPException("Receiver Name is required");
        }

        if(empty($receiver['email'])) {
            throw new PHPException("Receiver Email is required");
        }

        $instance = new Self();
        $instance->mail = new PHPMailer();
        $instance->host = $_ENV['EMAIL_HOST'];
        $instance->email = $_ENV['EMAIL_USER'];
        $instance->password = $_ENV['EMAIL_PASSWORD'];
        $instance->port = $_ENV['EMAIL_PORT'];
        $instance->name = $_ENV['EMAIL_NAME'];
        $instance->receiverName = $receiver['name'];
        $instance->receiverEmail = $receiver['email'];
        $instance->secure = ($_ENV['EMAIL_SECURE'] == "default")? PHPMailer::ENCRYPTION_SMTPS : $_ENV['EMAIL_SECURE'];
        return $instance;
    }

    private function getFilepath($filename) {
        return $this->folder . $filename . ".php";
    }

    public function useFile(string $filename, array $data) {
        $path   = $this->getFilepath($filename);
        if(!file_exists($path)) {
            throw new PHPException("[USEFILE] File tidak ditemukan");
        }

        if(!array_key_exists("subject", $data)) {
            throw new PHPException("[USEFILE] Subject diperlukan");
        }

        $this->file     = $path;
        $this->fileData = $data;
        $this->subject  = $data['subject'];
        return $this;
    }

    private function parseFileContent(string $path, array $data) {
        if(!file_exists($path)) throw new PHPException("[GET] Can't Parsing Files Not Found");

        /** Extract Array */
        $data['content'] = $path;
        extract($data, EXTR_OVERWRITE);
        ob_start();
        require_once $this->folder . "template.php";

        return ob_get_clean();
    }

    public function get() {
        if(empty($this->file)) throw new Exception("[GET] Mohon daftarkan nama file terlebih dahulu");
        return $this->parseFileContent($this->file, $this->fileData);
    }

    public function send() {
        if(!$this->mail->validateAddress($this->receiverEmail)) {
            throw new PHPException("[SEND] Email {$this->receiverEmail} Tidak Valid");
        }

        /** Parse Content */
        $contents = $this->parseFileContent($this->file, $this->fileData);
        if(empty($contents)) {
            throw new PHPException("[SEND] Email Body Kosong");
        }
        
        try {
            $this->mail->isHTML(true);
            $this->mail->isSMTP();
            $this->mail->SMTPSecure = $this->secure;
            $this->mail->SMTPAuth   = true;
            $this->mail->Host       = $this->host;
            $this->mail->Username   = $this->email;
            $this->mail->Password   = $this->password;
            $this->mail->Port       = $this->port;

            /** Destination */
            $this->mail->setFrom($this->email, $this->name);
            $this->mail->addAddress($this->receiverEmail, $this->receiverName);
            
            /** Body */
            $this->mail->Subject    = $this->subject;
            $this->mail->Body       = $contents;
            
            /** Send */
            return $this->mail->send();
            
        } catch (Exception $e) {
            throw $e;
        } 
    }
}