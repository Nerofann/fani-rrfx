<?php
namespace App\Models;

use Config\Core\Database;

class ProfilePerusahaan {

    public static function get($id = 1) {
        $db = Database::connect();
        
        /** Profile Perusahaan */
        $sql_get_profile = mysqli_query($db, "SELECT * FROM tb_profile WHERE ID_PROF = $id LIMIT 1");
        $profile = [];
        if($sql_get_profile && mysqli_num_rows($sql_get_profile)) {
            $profile = mysqli_fetch_assoc($sql_get_profile);
        }

        /** List Office */
        $sql_get_office  = mysqli_query($db, "SELECT * FROM tb_office"); 
        $office = [];
        if($sql_get_office) {
            $office = mysqli_fetch_all($sql_get_office, MYSQLI_ASSOC);
        }

        return [
            'PROF_COMPANY_NAME'             => $profile['PROF_COMPANY_NAME'],
            'PROF_NO_KEANGGOTAAN_LEMBAGA'   => $profile['PROF_NO_KEANGGOTAAN_LEMBAGA'] ?? "",
            'PROF_TGL_KEANGGOTAAN_LEMBAGA'  => $profile['PROF_TGL_KEANGGOTAAN_LEMBAGA'] ?? "",
            'PROF_NO_PERSETUJUAN_PESERTA'   => $profile['PROF_NO_PERSETUJUAN_PESERTA'] ?? "",
            'PROF_TGL_PERSETUJUAN_PESERTA'  => $profile['PROF_TGL_PERSETUJUAN_PESERTA'] ?? "",
            'PROF_DEWAN_DIREKSI'            => $profile['PROF_DEWAN_DIREKSI'] ?? "",
            'PROF_DIREKTUR'                 => $profile['PROF_DIREKTUR'] ?? "",
            'PROF_OPERATIONAL'              => $profile['PROF_OPERATIONAL'] ?? "",
            'PROF_KOMISARIS_UTAMA'          => $profile['PROF_KOMISARIS_UTAMA'] ?? "",
            'PROF_KOMISARIS'                => $profile['PROF_KOMISARIS'] ?? "",
            'PROF_PEMEGANG_SAHAM'           => $profile['PROF_PEMEGANG_SAHAM'] ?? "-,-",
            'PROF_NO_IZIN_USAHA'            => $profile['PROF_NO_IZIN_USAHA'] ?? "",
            'PROF_TGL_IZIN_USAHA'           => $profile['PROF_TGL_IZIN_USAHA'] ?? "",
            'PROF_NO_KEANGGOTAAN_BURSA'     => $profile['PROF_NO_KEANGGOTAAN_BURSA'] ?? "",
            'PROF_TGL_KEANGGOTAAN_BURSA'    => $profile['PROF_TGL_KEANGGOTAAN_BURSA'] ?? "",
            'PROF_HOMEPAGE'                 => $profile['PROF_HOMEPAGE'] ?? "",
            'PROF_EML_PENGADUAN'            => $profile['PROF_EML_PENGADUAN'] ?? "",
            'PROF_FAX'                      => $profile['PROF_FAX'] ?? "",
            'OFFICE'                        => $office,
            'FOREX_SYS'                     => 'PT. Capital Megah Mandiri ',
            'TRANS_REPRTR'                  => 'PT. Bursa Komoditi dan Derivatif Indonesia',
            'TRANS_CLRNGS'                  => 'Indonesia Clearing House(ICH)'
        ];
    }

    public static function wpb_verifikator() {
        $db = Database::connect();
        $sql_get_wpb = mysqli_query($db, "SELECT WPB_NAMA FROM tb_wpb WHERE WPB_STS = -1 AND WPB_VERIFY = -1 LIMIT 1");
        if($sql_get_wpb) {
            return mysqli_fetch_assoc($sql_get_wpb);
        }

        return [];
    } 

    public static function list_wpb($type = -1, $chunk = 16, $status = -1) {
        global $db;
        $sql_get_wpb = mysqli_query($db, "SELECT * FROM tb_wpb WHERE WPB_STS = ".$status."");
        $list_wpb = [];

        if($sql_get_wpb) {
            foreach(mysqli_fetch_all($sql_get_wpb, MYSQLI_ASSOC) as $wpb) {
                if($type == -1) {
                    $list_wpb[] = $wpb;
                }else {
                    if($wpb['WPB_TYPE'] == $type) {
                        $list_wpb[] = $wpb;
                    }
                }
            }
        }

        return ($chunk != 0) ? array_chunk($list_wpb, $chunk) : $list_wpb;
    }

}