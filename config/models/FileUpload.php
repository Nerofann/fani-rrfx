<?php
namespace App\Models;

if(!class_exists('Aws\S3\S3Client')) {
    require_once __DIR__ . "/../vendor/autoload.php";
}

use Exception;
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

class FileUpload {
    
    public static $defaultUploadFolder= "/assets/uploads";
    public static $error_messages     = array(
        UPLOAD_ERR_OK         => 'There is no error, the file uploaded with success',
        UPLOAD_ERR_INI_SIZE   => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
        UPLOAD_ERR_FORM_SIZE  => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
        UPLOAD_ERR_PARTIAL    => 'The uploaded file was only partially uploaded',
        UPLOAD_ERR_NO_FILE    => 'No file was uploaded',
        UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder',
        UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
        UPLOAD_ERR_EXTENSION  => 'A PHP extension stopped the file upload',
    );

    /** Aws Credential */
    public static $awsCredential = [
        'region'    => "ap-southeast-1",
        'bucketName' => "allmediaindo-2",
        'folder' => "trident",
        'key' => "AKIASPLPQWHJMMXY2KPR",
        'secretKey' => "d7xvrwOUl8oxiQ/8pZ1RrwONlAE911Qy0S9WHbpG"
    ];

    public function __construct()
    {
        //Do your magic here
    }

    public static function awsUrl(): string {
        $awsUrl = "https://";
        $awsUrl .= Self::$awsCredential['bucketName'];
        $awsUrl .= ".s3";
        $awsUrl .= ".amazonaws.com";
        $awsUrl .= "/" . Self::$awsCredential['folder'];

        return $awsUrl;
    }

    public static function awsFile(string $filename): string {
        return Self::awsUrl() . "/" . $filename;
    }

    public static function upload_myfile($files, $dir = "uploads", bool $compress = false, int $quality = 25): string|array {
        try {
            if(empty($files) || $files['error'] != 0) {
                return $error_messages[ $files['error'] ] ?? "[ERROR] Upload file gagal";
            }
    
            $target_dir     = WEB_ROOT . Self::$defaultUploadFolder;
            $file_info      = pathinfo($files['name']); #file info
            $new_file_name  = $dir."_".time().rand(1000000, 9999999).".".$file_info['extension']; #create new file name
    
            /** check if extension allowed */
            $image_ext = ["image/png", "image/jpeg", "image/jpg", "image/webp", "png", "jpg", "jpeg", "webp"];
            if(!in_array($files['type'], $image_ext) && !in_array($file_info['extension'], $image_ext)) {
                return "[Invalid file type], Mohon upload file ".implode(", ", $image_ext);
            }
    
            $image_size     = getimagesize($files['tmp_name']);
            if(!is_array($image_size)) {
                return "Fail to get detail of image";
            }
    
            $image_width    = $image_size[0] ?? 0;
            $image_height   = $image_size[1] ?? 0;
            if(!$image_width || !$image_height) { // Cek Dimensi
                return "Invalid Dimension";    
            } 
    
            /** Cek file size , max 5mb */
            if(!$image_size || $files['size'] > 5000000) {
                return "[Invalid file size], Max ukuran file 5mb";
            }
    
            /** check directory */
            if(!file_exists($target_dir)) {
                return "[Invalid Directory], Folder tidak ditemukan";
            }
    
            /** Compress image ?? */
            $status_upload  = false;
            $destination    = $target_dir . "/" . $new_file_name;
            switch ($compress) {
                case (true): 
                    $status_upload = Self::compressImage($files['tmp_name'], $destination, $quality); 
                    break;
    
                case (false): 
                    $status_upload = move_uploaded_file($files['tmp_name'], $destination); 
                    break;
    
                default: return "[Invalid Action] Unknown Action";
            }
    
            
            /** Check Upload File */
            if($status_upload !== TRUE) {
                return "Gagal upload file, mohon coba lagi";
            }
    
            /** New File Path for AWS */
            $credential  = Self::$awsCredential;
            $file_path   = $credential['folder'] . "/" . $new_file_name;
            $local_dir   = $destination;
    
            
            /** Get File Mime Type */
            $mime = mime_content_type($local_dir);
            if($mime === FALSE) {
                return "Invalid File Type";
            }
    
            /** Upload to AWS */
            $s3 = new S3Client([
                'region'  => $credential['region'],
                'version' => 'latest',
                'credentials' => [
                    'key'    => $credential['key'],
                    'secret' => $credential['secretKey'],
                ]
            ]);

            $result = $s3->putObject([
                'Bucket' => $credential['bucketName'],
                'Key'    => $file_path,
                'Body'   => fopen($local_dir, 'r'),
                'ACL'    => 'public-read', // make file 'public'
            ]);

            /** Delete file from local disk */
            unlink($local_dir);

            return [
                'filename'  => $new_file_name,
                'dir'       => $target_dir,
                'size'      => $files['size'],
                'extension' => $files['type'],
                'mime'      => $mime
            ];
            
        } catch (Exception $e) {
            return "Internal Server Error: FileUpload";

        } catch (S3Exception $e) {
            if(ini_get("display_errors") == 1) {
                throw $e;
            }
            
            return "Internal Server Error: FileUploadAWS";
        }
    }

    public static function compressImage($source, $destination, $quality) {
        try {
            // Get image info 
            $imgInfo = getimagesize($source); 
            $mime = $imgInfo['mime']; 
            
            // Create a new image from file 
            switch($mime){ 
                case 'image/jpeg': 
                    $image = imagecreatefromjpeg($source); 
                    break; 
                case 'image/png': 
                    $image = imagecreatefrompng($source); 
                    break; 
                case 'image/gif': 
                    $image = imagecreatefromgif($source); 
                    break; 
                default: 
                    $image = imagecreatefromjpeg($source); 
            } 

            if(is_bool($image)) {
                return false;
            }
            
            // Return compressed image 
            return imagejpeg($image, $destination, $quality); 
            
        } catch (Exception $e) {
            if(ini_get("display_errors") == "1") {
                throw $e;
            }

            return "Internal Server Error: Compress Upload";
        }
    }
}