<!DOCTYPE html>
<html>
    <head>
        <?php require_once(__DIR__  . "/../style.php"); ?>
        <style>
            ul.sub-btr {
                counter-reset: item;   
                list-style: none;
            }
            ul.sub-btr > li::before {
                display: inline-block;
                content: "(" counter(item) ")";
                counter-increment: item;
                /* width: 2em; */
                margin-left: -2em;
                margin-top: 5%;
            }
            ul.sub-btr-rum {
                counter-reset: itm;   
                list-style: none;
            }
            ul.sub-btr-rum > li::before {
                display: inline-block;
                content: counter(itm, lower-roman) ").";
                counter-increment: itm;
                /* width: 2em; */
                margin-left: -2em;
                margin-top: 5%;
            }
        </style>
    </head>
    <body>
        <div class="header">
            <img style="object-fit: cover; max-height: 100%; max-width: 100%;" src="data:image/png;base64,<?= base64_encode(file_get_contents($logo_pdf)); ?>">
        </div>

        <div class="section">
            <div style="text-align:center;vertical-align: middle;padding: 10px 0 10px 0;">
                <h3>PERJANJIAN PEMBERIAN AMANAT SECARA ELEKTRONIK ONLINE UNTUK TRANSAKSI KONTRAK DERIVATIF DALAM SISTEM PERDAGANGAN ALTERNATIF</h3>
            </div>
                <div style="text-align:center;border:3px solid black;vertical-align: middle;padding: 2px;">
                    <div class="text-center" style="border:1px solid black;vertical-align: middle;padding: 10px 0;">
                        <strong>PERHATIAN !</strong><br>
                        PERJANJIAN INI MERUPAKAN KONTRAK HUKUM. HARAP DIBACA DENGAN SEKSAMA
                    </div>
                </div>
                <p class="text-justify">Pada hari ini <?= $date_day ?>, tanggal <?= date('d', strtotime($realAccount["ACC_F_PERJ_DATE"])) ?>, bulan <?= $date_month ?>, tahun <?= date('Y', strtotime($realAccount["ACC_F_PERJ_DATE"])) ?>, kami
                yang mengisi perjanjian di bawah ini:</p>
                <table width="100%">
                    <tr>
                        <td rowspan="3" width="1%" style="padding:0px 5px;white-space:nowrap;vertical-align: top;"> 1. </td>
                        <td width="1%" style="padding:0px 5px;white-space:nowrap;" > Nama </td>
                        <td width="1%" style="padding:0px 5px;white-space:nowrap;"> : </td>
                        <td><?= $realAccount["ACC_FULLNAME"] ?></td>
                    </tr>
                    <tr>
                        <td width="1%" style="padding:0px 5px;white-space:nowrap;" > Pekerjaan / Jabatan </td>
                        <td width="1%" style="padding:0px 5px;white-space:nowrap;"> : </td>
                        <td> <?= $realAccount["ACC_F_APP_KRJ_TYPE"] ?> / <?= $realAccount["ACC_F_APP_KRJ_JBTN"] ?></td>
                    </tr>
                    <tr>
                        <td width="1%" style="padding:0px 5px;white-space:nowrap;vertical-align: top;" > Alamat </td>
                        <td width="1%" style="padding:0px 5px;white-space:nowrap;vertical-align: top;"> : </td>
                        <td> <?= $realAccount["ACC_ADDRESS"] ?></td>
                    </tr>
                </table>
                <p class="text-justify">dalam hal ini bertindak untuk dan atas nama sendiri, yang selanjutnya di sebut Nasabah,</p>
                <table width="100%">
                    <tr>
                        <td rowspan="3" width="1%" style="padding:0px 5px;white-space:nowrap;vertical-align: top;"> 2. </td>
                        <td width="1%" style="padding:0px 5px;white-space:nowrap;" > Nama </td>
                        <td width="1%" style="padding:0px 5px;white-space:nowrap;"> : </td>
                        <td> <?= $realAccount["ACC_F_PERJ_WPB"] ?></td>
                    </tr>
                    <tr>
                        <td width="1%" style="padding:0px 5px;white-space:nowrap;" > Pekerjaan / Jabatan </td>
                        <td width="1%" style="padding:0px 5px;white-space:nowrap;"> : </td>
                        <td>Wakil Pialang Berjangka</td>
                    </tr>
                    <tr>
                        <td width="1%" style="padding:0px 5px;white-space:nowrap;vertical-align: top;" > Alamat </td>
                        <td width="1%" style="padding:0px 5px;white-space:nowrap;vertical-align: top;"> : </td>
                        <td> 
                            <?= $COMPANY_MOF["OFC_ADDRESS"] ?>
                        </td>
                    </tr>
                </table>
                <p class="text-justify">dalam hal ini bertindak untuk dan atas nama <strong><?= $COMPANY_PRF["COMPANY_NAME"] ?></strong> yang selanjutnya
                disebut <strong>Pialang Berjangka</strong>,</p>
                <p class="text-justify">Nasabah dan Pialang Berjangka secara bersama – sama selanjutnya disebut <strong>Para Pihak</strong>.</p>
                <p class="text-justify">
                    Para pihak sepakat untuk mengadakan Perjanjian Pemberian Amanat 
                    untuk melakukan transaksi penjualan maupun pembelian Kontrak Derivatif 
                    dalam Sistem Perdagangan Alternatif dengan ketentuan sebagai berikut:
                </p>
            <ol>
                <li class="text-justify">
                    <p>Margin dan Pembayaran Lainnya</p>
                    <P>
                        <ul class="sub-btr text-justify">
                            <li>
                                Nasabah menempatkan sejumlah dana (Margin) ke Rekening 
                                Terpisah (<i>Segregated Account</i>) Pialang Berjangka sebagai Margin 
                                awal dan wajib mempertahankannya sebagaimana ditetapkan. 
                            </li>
                            <li>
                                membayar biaya-biaya yang diperlukan untuk transaksi yaitu biaya 
                                transaksi, pajak, komisi, dan biaya pelayanan, biaya bunga sesuai 
                                tingkat 
                                yang 
                                berlaku, 
                                dan 
                                biaya 
                                lainnya 
                                yang dapat 
                                dipertanggungjawabkan berkaitan dengan transaksi sesuai amanat 
                                Nasabah, maupun biaya rekening Nasabah. 
                            </li>
                        </ul>
                    </P>
                    <div style="vertical-align: top;"><input type="checkbox" checked="true" value="YA" /> Saya sudah membaca dan memahami</div>
                </li>
                <li class="text-justify">
                    <p>Pelaksanaan Transaksi</p>
                    <P>
                        <ul class="sub-btr text-justify">
                            <li>
                                Setiap transaksi Nasabah dilaksanakan secara elektronik Online 
                                oleh Nasabah yang bersangkutan; 
                            </li>
                            <li>
                                Setiap amanat Nasabah yang diterima dapat langsung dilaksanakan 
                                sepanjang nilai Margin yang tersedia pada rekeningnya mencukupi 
                                dan eksekusinya dapat menimbulkan perbedaan waktu terhadap 
                                proses pelaksanaan transaksi tersebut. Nasabah harus mengetahui 
                                posisi Margin dan posisi terbuka sebelum memberikan amanat 
                                untuk transaksi berikutnya.
                            </li>
                            <li>
                                Setiap transaksi Nasabah secara bilateral dilawankan dengan 
                                Penyelenggara Sistem Perdagangan Alternatif <?= $COMPANY_PRF["COMPANY_NAME"] ?>
                                yang bekerjasama dengan Pialang Berjangka.
                            </li>
                            <li>
                                Nasabah bertanggung jawab atas keamanan dan penggunaan 
                                username dan password dalam transaksi Perdagangan Berjangka, 
                                oleh karenanya Nasabah dilarang memberitahukan, menyerahkan 
                                atau meminjamkan username dan password kepada pihak lain, 
                                termasuk kepada pegawai Pialang Berjangka.
                            </li>
                        </ul>
                    </P>
                    <div style="vertical-align: top;"><input type="checkbox" checked="true" value="YA" /> Saya sudah membaca dan memahami</div>
                </li>
                <li class="text-justify">
                    <p>Kewajiban Memelihara Margin</p>
                    <P>
                        <ul class="sub-btr text-justify">
                            <li>
                                Nasabah wajib memelihara/memenuhi tingkat Margin yang harus 
                                tersedia di rekening pada Pialang Berjangka sesuai dengan jumlah 
                                yang telah ditetapkan baik diminta ataupun tidak oleh Pialang 
                                Berjangka. 
                            </li>
                            <li>
                                Apabila jumlah Margin memerlukan penambahan maka Pialang 
                                Berjangka wajib memberitahukan dan memintakan kepada 
                                Nasabah untuk menambah Margin segera.
                            </li>
                            <li>
                                Apabila jumlah Margin memerlukan tambahan (Call Margin) maka 
                                Nasabah wajib melakukan penyerahan Call Margin selambat
                                lambatnya sebelum dimulai hari perdagangan berikutnya. 
                                Kewajiban Nasabah sehubungan dengan penyerahan Call Margin 
                                tidak terbatas pada jumlah Margin awal. 
                            </li>
                            <li>
                                Pialang Berjangka tidak berkewajiban melaksanakan amanat untuk 
                                melakukan transaksi yang baru dari Nasabah sebelum Call Margin 
                                dipenuhi.
                            </li>
                            <li>
                                Untuk memenuhi kewajiban Call Margin dan keuangan lainnya dari 
                                Nasabah, Pialang Berjangka dapat mencairkan dana Nasabah yang 
                                ada di Pialang Berjangka. 
                            </li>
                        </ul>
                    </P>
                    <div style="vertical-align: top;"><input type="checkbox" checked="true" value="YA" /> Saya sudah membaca dan memahami</div>
                </li>
                <li class="text-justify">
                    <p>Hak Pialang Berjangka Melikuidasi Posisi Nasabah</p>
                    <P>
                        Nasabah bertanggung jawab memantau/mengetahui posisi terbukanya 
                        secara terus- menerus dan memenuhi kewajibannya. Apabila dalam 
                        jangka waktu tertentu dana pada rekening Nasabah kurang dari yang 
                        dipersyaratkan, Pialang Berjangka dapat menutup posisi terbuka 
                        Nasabah secara keseluruhan atau sebagian, membatasi transaksi, atau 
                        tindakan lain untuk melindungi diri dalam pemenuhan Margin tersebut 
                        dengan terlebih dahulu memberitahu atau tanpa memberitahu Nasabah 
                        dan Pialang Berjangka tidak bertanggung jawab atas kerugian yang 
                        timbul akibat tindakan tersebut.
                    </P>
                    <div style="vertical-align: top;"><input type="checkbox" checked="true" value="YA" /> Saya sudah membaca dan memahami</div>
                </li>
                <li class="text-justify">
                    <p>Penggantian Kerugian Tidak Adanya Penutupan Posisi</p>
                    <P>
                        Apabila Nasabah tidak mampu melakukan penutupan atas transaksi 
                        yang jatuh tempo, Pialang Berjangka dapat melakukan penutupan atas 
                        transaksi Nasabah yang terjadi. Nasabah wajib membayar biaya-biaya, 
                        termasuk biaya kerugian dan premi yang telah dibayarkan oleh Pialang 
                        Berjangka, dan apabila Nasabah lalai untuk membayar biaya-biaya 
                        tersebut, Pialang Berjangka berhak untuk mengambil pembayaran dari 
                        dana Nasabah.
                    </P>
                    <div style="vertical-align: top;"><input type="checkbox" checked="true" value="YA" /> Saya sudah membaca dan memahami</div>
                </li>
                <li class="text-justify">
                    <p>Pialang Berjangka Dapat Membatasi Posisi</p>
                    <P>
                        Nasabah mengakui hak Pialang Berjangka untuk membatasi posisi 
                        terbuka Kontrak dan Nasabah tidak melakukan transaksi melebihi 
                        batas yang telah ditetapkan tersebut.
                    </P>
                    <div style="vertical-align: top;"><input type="checkbox" checked="true" value="YA" /> Saya sudah membaca dan memahami</div>
                </li>
                <li class="text-justify">
                    <p>Tidak Ada Jaminan atas Informasi atau Rekomendasi</p>
                    <p>Nasabah mengakui bahwa: </p>
                    <P>
                        <ul class="sub-btr text-justify">
                            <li>
                                Informasi dan rekomendasi yang diberikan oleh Pialang Berjangka 
                                kepada Nasabah tidak selalu lengkap dan perlu diverifikasi.
                            </li>
                            <li>
                                Pialang Berjangka tidak menjamin bahwa informasi dan 
                                rekomendasi yang diberikan merupakan informasi yang akurat dan 
                                lengkap.
                            </li>
                            <li>
                                Informasi dan rekomendasi yang diberikan oleh Wakil Pialang 
                                Berjangka yang satu dengan yang lain mungkin berbeda karena 
                                perbedaan analisis fundamental atau teknikal. Nasabah menyadari 
                                bahwa ada kemungkinan Pialang Berjangka dan pihak 
                                terafiliasinya 
                                memiliki 
                                posisi 
                                di 
                                pasar 
                                rekomendasi tidak konsisten kepada Nasabah. 
                            </li>
                        </ul>
                    </P>
                    <div style="vertical-align: top;"><input type="checkbox" checked="true" value="YA" /> Saya sudah membaca dan memahami</div>
                </li>
                <li class="text-justify">
                    <p>Pembatasan Tanggung Jawab Pialang Berjangka.</p>
                    <P>
                        <ul class="sub-btr text-justify">
                            <li>
                                Pialang Berjangka tidak bertanggung jawab untuk memberikan 
                                penilaian kepada Nasabah mengenai iklim, pasar, keadaan politik 
                                dan ekonomi nasional dan internasional, nilai Kontrak Derivatif, 
                                kolateral, atau memberikan nasihat mengenai keadaan pasar. 
                                Pialang Berjangka hanya memberikan pelayanan untuk melakukan 
                                transaksi secara jujur serta memberikan laporan atas transaksi 
                                tersebut. 
                            </li>
                            <li>
                                Perdagangan sewaktu-waktu dapat dihentikan oleh pihak yang 
                                memiliki otoritas (Bappebti/Bursa Berjangka) tanpa pemberitahuan 
                                terlebih dahulu kepada Nasabah. Atas posisi terbuka yang masih 
                                dimiliki oleh Nasabah pada saat perdagangan tersebut dihentikan, 
                                maka 
                                akan 
                                diselesaikan 
                                (likuidasi) 
                                berdasarkan 
                                pada 
                                peraturan/ketentuan yang dikeluarkan dan ditetapkan oleh pihak 
                                otoritas tersebut, dan semua kerugian serta biaya yang timbul 
                                sebagai akibat dihentikannya transaksi oleh pihak otoritas 
                                perdagangan tersebut, menjadi beban dan tanggung jawab Nasabah 
                                sepenuhnya.
                            </li>
                        </ul>
                    </P>
                    <div style="vertical-align: top;"><input type="checkbox" checked="true" value="YA" /> Saya sudah membaca dan memahami</div>
                </li>
                <li class="text-justify">
                    <p>Transaksi Harus Mematuhi Peraturan Yang Berlaku</p>
                    <P>
                        Semua transaksi dilakukan sendiri oleh Nasabah dan wajib mematuhi 
                        peraturan perundang-undangan di bidang Perdagangan Berjangka, 
                        kebiasaan dan interpretasi resmi yang ditetapkan oleh Bappebti atau 
                        Bursa Berjangka. 
                    </P>
                    <div style="vertical-align: top;"><input type="checkbox" checked="true" value="YA" /> Saya sudah membaca dan memahami</div>
                </li>
                <li class="text-justify">
                    <p>Pialang Berjangka tidak Bertanggung jawab atas Kegagalan Komunikasi</p>
                    <P>
                        Pialang Berjangka tidak bertanggung jawab atas keterlambatan atau 
                        tidak tepat waktunya pengiriman amanat atau informasi lainnya yang 
                        disebabkan oleh kerusakan fasilitas komunikasi atau sebab lain diluar 
                        kontrol Pialang Berjangka. 
                    </P>
                    <div style="vertical-align: top;"><input type="checkbox" checked="true" value="YA" /> Saya sudah membaca dan memahami</div>
                </li>
                <li class="text-justify">
                    <p>Konfirmasi</p>
                    <P>
                        <ul class="sub-btr text-justify">
                            <li>
                                Konfirmasi dari Nasabah dapat berupa surat, telex, media lain, 
                                surat elektronik, secara tertulis ataupun rekaman suara.
                            </li>
                            <li>
                                Pialang Berjangka berkewajiban menyampaikan konfirmasi 
                                transaksi, laporan rekening, permintaan Call Margin, dan 
                                pemberitahuan lainnya kepada Nasabah secara akurat, benar dan 
                                secepatnya pada alamat (email) Nasabah sesuai dengan yang 
                                tertera dalam rekening Nasabah. Apabila dalam jangka waktu 2 x 
                                24 jam setelah amanat jual atau beli disampaikan, tetapi Nasabah 
                                belum menerima konfirmasi melalui alamat email Nasabah 
                                dan/atau sistem transaksi, Nasabah segera memberitahukan hal 
                                tersebut kepada Pialang Berjangka melalui telepon dan disusul 
                                dengan pemberitahuan tertulis.
                            </li>
                            <li>
                                Jika dalam waktu 2 x 24 jam sejak tanggal penerimaan konfirmasi 
                                tersebut tidak ada sanggahan dari Nasabah maka konfirmasi 
                                Pialang Berjangka dianggap benar dan sah. 
                            </li>
                            <li>
                                Kekeliruan atas konfirmasi yang diterbitkan Pialang Berjangka 
                                akan diperbaiki oleh Pialang Berjangka sesuai keadaan yang 
                                sebenarnya dan demi hukum konfirmasi yang lama batal.
                            </li>
                            <li>
                                Nasabah tidak bertanggung jawab atas transaksi yang 
                                dilaksanakan atas rekeningnya apabila konfirmasi tersebut tidak 
                                disampaikan secara benar dan akurat. 
                            </li>
                        </ul>
                    </P>
                    <div style="vertical-align: top;"><input type="checkbox" checked="true" value="YA" /> Saya sudah membaca dan memahami</div>
                </li>
                <li class="text-justify">
                    <p>Kebenaran Informasi Nasabah</p>
                    <P>
                        Nasabah memberikan informasi yang benar dan akurat mengenai data 
                        Nasabah yang diminta oleh Pialang Berjangka dan akan 
                        memberitahukan paling lambat dalam waktu 3 (tiga) hari kerja setelah 
                        terjadi perubahan, termasuk perubahan kemampuan keuangannya 
                        untuk terus melaksanakan transaksi. 
                    </P>
                    <div style="vertical-align: top;"><input type="checkbox" checked="true" value="YA" /> Saya sudah membaca dan memahami</div>
                </li>
                <li class="text-justify">
                    <p>Komisi Transaksi</p>
                    <P>
                        Nasabah mengetahui dan menyetujui bahwa Pialang Berjangka berhak 
                        untuk memungut komisi atas transaksi yang telah dilaksanakan, dalam 
                        jumlah sebagaimana akan ditetapkan dari waktu ke waktu oleh Pialang 
                        Berjangka. Perubahan beban (fees) dan biaya lainnya harus disetujui 
                        secara tertulis oleh Para Pihak. 
                    </P>
                    <div style="vertical-align: top;"><input type="checkbox" checked="true" value="YA" /> Saya sudah membaca dan memahami</div>
                </li>
                <li class="text-justify">
                    <p>Pemberian Kuasa</p>
                    <P>
                        Nasabah memberikan kuasa kepada Pialang Berjangka untuk 
                        menghubungi bank, lembaga keuangan, Pialang Berjangka lain, atau 
                        institusi lain yang terkait untuk memperoleh keterangan atau verifikasi 
                        mengenai informasi yang diterima dari Nasabah. Nasabah mengerti 
                        bahwa penelitian mengenai data hutang pribadi dan bisnis dapat 
                        dilakukan oleh Pialang Berjangka apabila diperlukan. Nasabah 
                        diberikan kesempatan untuk memberitahukan secara tertulis dalam 
                        jangka waktu yang telah disepakati untuk melengkapi persyaratan yang 
                        diperlukan.
                    </P>
                    <div style="vertical-align: top;"><input type="checkbox" checked="true" value="YA" /> Saya sudah membaca dan memahami</div>
                </li>
                <li class="text-justify">
                    <p>Pemindahan Dana</p>
                    <P>
                        Pialang Berjangka dapat setiap saat mengalihkan dana dari satu 
                        rekening ke rekening lainnya berkaitan dengan kegiatan transaksi yang 
                        dilakukan Nasabah seperti pembayaran komisi, pembayaran biaya 
                        transaksi, kliring dan keterlambatan dalam memenuhi kewajibannya, 
                        tanpa terlebih dahulu memberitahukan kepada Nasabah. Transfer yang 
                        telah dilakukan akan segera diberitahukan secara tertulis kepada 
                        Nasabah. 
                    </P>
                    <div style="vertical-align: top;"><input type="checkbox" checked="true" value="YA" /> Saya sudah membaca dan memahami</div>
                </li>
                <li class="text-justify">
                    <p>Pemberitahuan</p>
                    <P>
                        <ul class="sub-btr text-justify">
                            <li>
                                Semua komunikasi, uang, surat berharga, dan kekayaan lainnya 
                                harus dikirimkan langsung ke alamat Nasabah seperti tertera 
                                dalam 
                                rekeningnya 
                                atau 
                                alamat 
                                lain 
                                ditetapkan/diberitahukan secara tertulis oleh Nasabah.
                            </li>
                            <li>
                                Semua uang, harus disetor atau ditransfer langsung oleh 
                                Nasabah ke Rekening Terpisah (Segregated Account) Pialang 
                                Berjangka:
                                <table width="100%" style="margin-left:5px;">
                                    <tr>
                                        <td width="36%">Nama</td>
                                        <td width="2%">&nbsp;:&nbsp;</td>
                                        <td><?= $COMPANY_PRF["COMPANY_NAME"] ?></td>
                                    </tr>
                                    <tr>
                                        <td valign="top">Alamat</td>
                                        <td valign="top">&nbsp;:&nbsp;</td>
                                        <td>
                                            <?= $COMPANY_MOF["OFC_ADDRESS"] ?>
                                        </td>
                                    </tr>
                                    <?php if($admBanks) : ?>
                                        <?php foreach(mysqli_fetch_all($admBanks, MYSQLI_ASSOC) as $bkadm) : ?>
                                            <tr>
                                                <td>Bank</td>
                                                <td>&nbsp;:&nbsp;</td>
                                                <td><?= $bkadm['BKADM_NAME'] ?></td>
                                            </tr>
                                            <tr>
                                                <td valign="top">No. Rekening Terpisah</td>
                                                <td valign="top">&nbsp;:&nbsp;</td>
                                                <td><?= $bkadm['BKADM_ACCOUNT'] ?> (<?= $bkadm['BKADM_CURR'] ?>)</td>
                                            </tr>
                                            <tr><td colspan="3" style="margin-top: 7px;"></td></tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </table>
                                dan dianggap sudah diterima oleh Pialang Berjangka apabila sudah ada tanda terima bukti setor atau transfer dari pegawai Pialang Berjangka.
                            </li>
                        </ul>
                    </P>
                    <div style="vertical-align: top;"><input type="checkbox" checked="true" value="YA" /> Saya sudah membaca dan memahami</div>
                </li>
                <li class="text-justify">
                    <p>Dokumen Pemberitahuan Adanya Risiko </p>
                    <P>
                        Nasabah mengakui menerima dan mengerti Dokumen Pemberitahuan Adanya Risiko.
                    </P>
                    <div style="vertical-align: top;"><input type="checkbox" checked="true" value="YA" /> Saya sudah membaca dan memahami</div>
                </li>
                <li class="text-justify">
                    <p>Jangka Waktu Perjanjian dan Pengakhiran </p>
                    <P>
                        <ul class="sub-btr text-justify">
                            <li>
                                Perjanjian ini mulai berlaku terhitung sejak tanggal dilakukannya 
                                konfirmasi oleh Pialang Berjangka dengan diterimanya Bukti 
                                Konfirmasi Penerimaan Nasabah dari Pialang Berjangka oleh 
                                Nasabah.
                            </li>
                            <li>
                                Nasabah dapat mengakhiri Perjanjian ini hanya jika Nasabah sudah 
                                tidak lagi memiliki posisi terbuka dan tidak ada kewajiban Nasabah 
                                yang diemban oleh atau terhutang kepada Pialang Berjangka.
                            </li>
                            <li>
                                Pengakhiran tidak membebaskan salah satu Pihak dari tanggung 
                                jawab atau kewajiban yang terjadi sebelum pemberitahuan tersebut.
                            </li>
                        </ul>
                    </P>
                    <div style="vertical-align: top;"><input type="checkbox" checked="true" value="YA" /> Saya sudah membaca dan memahami</div>
                </li>
                <li class="text-justify">
                    <p>Berakhirnya Perjanjian </p>
                    <p>Perjanjian dapat berakhir dalam hal Nasabah:  </p>
                    <P>
                        <ul class="sub-btr text-justify">
                            <li>
                                dinyatakan pailit, memiliki hutang yang sangat besar, dalam proses 
                                peradilan, menjadi hilang ingatan, mengundurkan diri atau 
                                meninggal; 
                            </li>
                            <li>
                                tidak dapat memenuhi atau mematuhi perjanjian ini dan/atau 
                                melakukan pelanggaran terhadapnya; 
                            </li>
                            <li>
                                berkaitan dengan butir (1) dan (2) tersebut diatas, Pialang Berjangka 
                                dapat:
                                <ul class="sub-btr-rum">
                                    <li>
                                        meneruskan atau menutup posisi Nasabah tersebut setelah 
                                        mempertimbangkannya secara cermat dan jujur; dan
                                    </li>
                                    <li>
                                        menolak perintah dari  Nasabah atau kuasanya. 
                                    </li>
                                </ul>
                            </li>
                            <li>
                                Pengakhiran Perjanjian sebagaimana dimaksud dengan angka (1) 
                                dan (2) tersebut di atas tidak melepaskan kewajiban dari Para Pihak 
                                yang berhubungan dengan penerimaan atau kewajiban pembayaran 
                                atau pertanggungjawaban kewajiban lainnya yang timbul dari 
                                Perjanjian.
                            </li>
                        </ul>
                    </P>
                    <div style="vertical-align: top;"><input type="checkbox" checked="true" value="YA" /> Saya sudah membaca dan memahami</div>
                </li>
                <li class="text-justify">
                    <p>Force Majeur</p>
                    <p>
                        Tidak ada satupun pihak di dalam Perjanjian dapat diminta 
                        pertanggungjawabannya untuk suatu keterlambatan atau terhalangnya 
                        memenuhi kewajiban berdasarkan Perjanjian yang diakibatkan oleh 
                        suatu sebab yang berada di luar kemampuannya atau kekuasaannya 
                        (force majeur), sepanjang pemberitahuan tertulis mengenai sebab itu 
                        disampaikannya kepada pihak lain dalam Perjanjian dalam waktu tidak 
                        lebih dari 24 (dua puluh empat) jam sejak timbulnya sebab itu.  
                        Yang dimaksud dengan Force Majeur dalam Perjanjian adalah peristiwa 
                        kebakaran, bencana alam (seperti gempa bumi, banjir, angin topan, 
                        petir), pemogokan umum, huru hara, peperangan, perubahan terhadap 
                        peraturan perundang-undangan yang berlaku dan kondisi di bidang 
                        ekonomi, keuangan dan Perdagangan Berjangka, pembatasan yang 
                        dilakukan oleh otoritas Perdagangan Berjangka dan Bursa Berjangka 
                        serta terganggunya sistem perdagangan, kliring dan penyelesaian 
                        transaksi Kontrak Berjangka di mana transaksi dilaksanakan yang 
                        secara langsung mempengaruhi pelaksanaan pekerjaan berdasarkan 
                        Perjanjian.
                    </p>
                    <div style="vertical-align: top;"><input type="checkbox" checked="true" value="YA" /> Saya sudah membaca dan memahami</div>
                </li>
                <li class="text-justify">
                    <p> Perubahan atas Isian dalam Perjanjian Pemberian Amanat</p>
                    <p>
                        Perubahan atas isian dalam Perjanjian ini hanya dapat dilakukan atas 
                        persetujuan Para Pihak, atau Pialang Berjangka telah memberitahukan 
                        secara tertulis perubahan yang diinginkan, dan Nasabah tetap 
                        memberikan perintah untuk transaksi dengan tanpa memberikan 
                        tanggapan secara tertulis atas usul perubahan tersebut. Tindakan 
                        Nasabah tersebut dianggap setuju atas usul perubahan tersebut. 
                    </p>
                    <div style="vertical-align: top;"><input type="checkbox" checked="true" value="YA" /> Saya sudah membaca dan memahami</div>
                </li>
                <li class="text-justify">
                    <p>Tanggung Jawab Kepada Nasabah</p>
                    <p>
                        <ol type="a">
                            <li>
                                Penyelenggara Sistem Perdagangan Alternatif yang merupakan 
                                pihak yang menguasai dan/atau memiliki sistem perdagangan 
                                elektronik bertanggung jawab atas pelanggaran penyalahgunaan 
                                sistem perdagangan elektronik sesuai dengan ketentuan yang diatur 
                                dalam Perjanjian Kerjasama (PKS) dan peraturan perdagangan 
                                (trading rules) antara Penyelenggara Sistem Perdagangan Alternatif 
                                dan Peserta Sistem Perdagangan Alternatif yang mengakibatkan 
                                kerugian Nasabah.
                            </li>
                            <li>
                                Peserta Sistem Perdagangan Alternatif yang merupakan pihak yang 
                                menggunakan sistem perdagangan elektronik bertanggung jawab 
                                atas pelanggaran penyalahgunaan sistem perdagangan elektronik 
                                sebagaimana dimaksud pada angka 22 huruf (a) yang 
                                mengakibatkan kerugian Nasabah. 
                            </li>
                            <li>
                                Dalam pemanfaatan sistem perdagangan elektronik, Penyelenggara 
                                Sistem 
                                Perdagangan 
                                Alternatif 
                                dan/atau 
                                Peserta 
                                Sistem 
                                Perdagangan Alternatif tidak bertanggung jawab atas kerugian 
                                Nasabah diluar hal-hal yang telah diatur pada angka 22 huruf (a) 
                                dan (b), antara lain: kerugian yang diakibatkan oleh risiko-risiko 
                                yang disebutkan di dalam Dokumen Pemberitahuan Adanya Risiko 
                                yang telah dimengerti dan disetujui oleh Nasabah.
                            </li>
                        </ol>
                    </p>
                    <div style="vertical-align: top;"><input type="checkbox" checked="true" value="YA" /> Saya sudah membaca dan memahami</div>
                </li>
                <li class="text-justify">
                    <p> Penyelesaian Perselisihan </p>
                    <p>
                        <ul class="sub-btr text-justify">
                            <li>
                                Semua perselisihan dan perbedaan pendapat yang timbul dalam 
                                pelaksanaan Perjanjian ini wajib diselesaikan terlebih dahulu 
                                secara musyawarah untuk mencapai mufakat antara Para Pihak. 
                            </li>
                            <li>
                                Apabila perselisihan dan perbedaan pendapat yang timbul tidak 
                                dapat diselesaikan secara musyawarah untuk mencapai mufakat, 
                                Para Pihak wajib memanfaatkan sarana penyelesaian perselisihan 
                                yang tersedia di Bursa Berjangka.
                            </li>
                            <li>
                                Kantor atau kantor cabang Pialang Berjangka terdekat dengan 
                                domisili Nasabah tempat penyelesaian dalam hal terjadi 
                                perselisihan. 
                                
                                <table width="50%" style="margin-left:5px;">
                                    <tr>
                                        <td width="30%">Daftar Kantor</td>
                                        <td>Kantor yang dipilih (salah satu)</td>
                                    </tr>
                                    <?php if($offices) : ?>
                                        <?php 
                                            $rnum = 0;
                                            $RAL  = range('a', 'z');
                                            foreach(mysqli_fetch_all($offices, MYSQLI_ASSOC) as $ofc) : 
                                        ?>
                                            <tr>
                                                <td><?php echo $RAL["$rnum"] ?>.<?= strtoupper($ofc['OFC_CITY']) ?></td>
                                                <td><input type="radio" name="step07_kantorpenyelesaian" <?= (strtoupper($ofc['OFC_CITY']) == strtoupper($realAccount["ACC_F_PERJ_KANTOR"]) ? "checked" : "") ?>  required ></td>
                                            </tr>
                                        <?php 
                                            $rnum++;
                                            endforeach; 
                                        ?>
                                    <?php endif ?>
                                </table>
                            </li>
                        </ul>
                    </p>
                    <div style="vertical-align: top;"><input type="checkbox" checked="true" value="YA" /> Saya sudah membaca dan memahami</div>
                </li>
                <li class="text-justify">
                    <p> Bahasa </p>
                    <p>Perjanjian ini dibuat dalam Bahasa Indonesia. </p>
                    <div style="vertical-align: top;"><input type="checkbox" checked="true" value="YA" /> Saya sudah membaca dan memahami</div>
                </li>
            </ol>
            
            <div style="margin-top:25px;text-align:center;">
                <p>Demikian Perjanjian Pemberian Amanat ini dibuat oleh Para Pihak 
                    dalam keadaan sadar, sehat jasmani rohani dan tanpa unsur paksaan dari 
                    pihak manapun. 
                </p>
                <p>“Saya telah membaca, mengerti dan setuju terhadap semua ketentuan yang tercantum dalam perjanjian ini”.</p>
                <p>Dengan mengisi kolom “YA” di bawah, saya menyatakan bahwa saya telah menerima</p>
                <p>PERJANJIAN PEMBERIAN AMANAT TRANSAKSI KONTRAK DERIVATIF  SISTEM PERDAGANGAN ALTERNATIF </p>
                <p>mengerti dan menyetujui isinya.</p>
            </div>
            <div style="text-align:center;margin-top:10px;margin-left:25%">
                <table>
                    <tr>
                        <td>Pernyataan menerima/tidak </td>
                        <td style="vertical-align: top;"><div style="margin:0px 5px;">:</div></td>
                        <td><strong>YA</strong></td>
                    </tr>
                    <tr>
                        <td>Menerima pada Tanggal</td>
                        <td style="vertical-align: top;"><div style="margin:0px 5px;">:</div></td>
                        <td><strong><?= date('Y-m-d H:i:s', strtotime($realAccount["ACC_F_RESK_DATE"])) ?></strong></td>
                    </tr>
                    <tr>
                        <td>IP Address</td>
                        <td style="vertical-align: top;"><div style="margin:0px 5px;">:</div></td>
                        <td><strong><?= $realAccount["ACC_F_RESK_IP"] ?></strong></td>
                    </tr>
                </table>
            </div>
        </div>
    </body>
</html>