<!DOCTYPE html>
<html>
    <head>
        <?php require_once(__DIR__  . "/../style.php"); ?>
    </head>
    <body>
        <div class="header">
            <img style="object-fit: cover; max-height: 100%; max-width: 100%;" src="data:image/png;base64,<?= base64_encode(file_get_contents($logo_pdf)); ?>">
        </div>

        ini spa
        <table class="table">
            <tbody>
                <tr>
                    <td width="8%" class="text-center v-align-middle">No</td>
                    <td><b>Daftar Jenis Kontrak Derivatif Dalam Sistem Perdagangan Alternatif Dengan Volume minimum 0,1 (nol koma satu) Lot Dalam Rangka Penerimaan Nasabah Secara Elektronik Online Di Bidang Perdagangan Berjangka Komoditi</b></td>
                </tr>
                <tr>
                    <td width="8%" class="text-center v-align-middle">1.</td>
                    <td>
                        <b><i>Contract For Difference</i> Indeks Saham:</b>
                        <p style="margin: 0px;">Index Saham Jepang</p>
                        <p style="margin: 0px;">Index Saham Hongkong</p>
                        <p style="margin: 0px;">Index Saham Amerika</p>
                    </td>
                </tr>
                <tr>
                    <td width="8%" class="text-center v-align-middle">2.</td>
                    <td>
                        <b><i>Contract For Difference</i> Mata uang Asing:</b>
                        <p style="margin: 0px;">Forex Major</p>
                        <p style="margin: 0px;">Cross Rate</p>
                    </td>
                </tr>
                <tr>
                    <td width="8%" class="text-center v-align-middle">3.</td>
                    <td>
                        <b><i>Contract For Difference</i> Komoditi:</b>
                        <p style="margin: 0px;">LLG (XAUUSD)</p>
                        <p style="margin: 0px;">Silver (XAGUSD)</p>
                        <p style="margin: 0px;">Crude Oil</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </body>
</html>