<?php

    use App\Models\Account;
    use App\Models\Dpwd;
    use App\Models\Helper;
    use App\Models\FileUpload;
    use App\Models\CompanyProfile;

    $realAccount = Account::realAccountDetail(($acc ?? ""));
    $accnd = Account::accoundCondition($realAccount['ID_ACC']);
    $depositData     = Dpwd::findByRaccId($realAccount["ID_ACC"]);
    $bank = explode("/", $depositData['DPWD_BANKSRC']);
    $bankName = $bank[0] ?? "-";
    $bankAccount = $bank[1] ?? "-";
    $bankHolder = $bank[2] ?? "-";
    $companyProfile = CompanyProfile::profilePerusahaan();
    $mainOffice = CompanyProfile::getMainOffice();
?>

<!DOCTYPE html>
<html>
    <head>
        <?php require_once(__DIR__  . "/style.php"); ?>
        <style>
            @page {
                margin-left: 50px;
                margin-right: 50px;
            }
        </style>
    </head>
    <body>
        <?php require_once(__DIR__  . "/header.php"); ?><hr>

        <div class="section">
            <h4 class="text-center" style="margin: 0px;">ACCOUNT CONDITION</h4>
            <table class="table no-border" style="margin-top: 20px;">
                <tbody>
                    <tr>
                        <th width="60%" class="text-center" style="background-color: #edebe0;">Detail</th>
                        <th width="2%"></th>
                        <th class="text-center" style="background-color: #edebe0;">Product</th>
                    </tr> 
                    <tr>
                        <td class="v-align-top" style="font-size: 15px; text-align: left;">
                            <table class="table no-border" style="font-size: 15px;">
                                <tbody>
                                    <tr>
                                        <td width="30%" class="v-align-top">Kondisi ini efektif mulai bulan</td>
                                        <td width="3%" class="v-align-top">:</td>
                                        <td class="v-align-top">
                                            <?= date('m', strtotime($realAccount['ACC_WPCHECK_DATE'])).' ('.Helper::bulan(date('m', strtotime($realAccount['ACC_WPCHECK_DATE']))).')'; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="30%" class="v-align-top">No. Account</td>
                                        <td width="3%" class="v-align-top">:</td>
                                        <td class="v-align-top"><?= $realAccount['ACC_LOGIN'] ?></td>
                                    </tr>
                                    <tr>
                                        <td width="30%" class="v-align-top">Nama Investor</td>
                                        <td width="3%" class="v-align-top">:</td>
                                        <td class="v-align-top"><?= $realAccount['ACC_FULLNAME'] ?></td>
                                    </tr>
                                    <tr>
                                        <td width="30%" class="v-align-top">Email Investor</td>
                                        <td width="3%" class="v-align-top">:</td>
                                        <td class="v-align-top"><?= $realAccount['MBR_EMAIL'] ?></td>
                                    </tr>
                                    <tr>
                                        <td width="30%" class="v-align-top">No. Telepon</td>
                                        <td width="3%" class="v-align-top">:</td>
                                        <td class="v-align-top"><?= $realAccount['ACC_F_APP_PRIBADI_TLP'] ?></td>
                                    </tr>
                                    <tr>
                                        <td width="30%" class="v-align-top">Tanggal Margin</td>
                                        <td width="3%" class="v-align-top">:</td>
                                        <td class="v-align-top"><?= date("d/m/Y", strtotime($accnd['ACCCND_DATEMARGIN'])) ?></td>
                                    </tr>
                                    <tr>
                                        <td width="30%" class="v-align-top">Nilai Margin</td>
                                        <td width="3%" class="v-align-top">:</td>
                                        <td class="v-align-top"><?= $realAccount['RTYPE_CURR'] ?> <?= Helper::formatCurrency($accnd['ACCCND_AMOUNTMARGIN']) ?></td>
                                    </tr>
                                    <tr>
                                        <td width="30%" class="v-align-top">Nama Bank</td>
                                        <td width="3%" class="v-align-top">:</td>
                                        <td class="v-align-top"><?= $bankName ?></td>
                                    </tr>
                                    <tr>
                                        <td width="30%" class="v-align-top" style="text-align: left;">Nomor akun Bank</td>
                                        <td width="3%" class="v-align-top">:</td>
                                        <td class="v-align-top"><?= $bankAccount ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                        <td></td>
                        <td class="v-align-top" style="font-size: 15px; text-align: left;">
                            <table class="table no-border">
                                <tbody>
                                    <tr>
                                        <td width="30%" class="v-align-top">Product</td>
                                        <td width="3%" class="v-align-top">:</td>
                                        <td class="v-align-top"><?= $realAccount['RTYPE_NAME'] ?></td>
                                    </tr>
                                    <tr>
                                        <td width="30%" class="v-align-top">Rate</td>
                                        <td width="3%" class="v-align-top">:</td>
                                        <td class="v-align-top"><?= ($realAccount['RTYPE_ISFLOATING'] == 1) ? 'Floating' : $realAccount['RTYPE_RATE'] ?></td>
                                    </tr>
                                    <tr>
                                        <td width="30%" class="v-align-top">Commision</td>
                                        <td width="3%" class="v-align-top">:</td>
                                        <td class="v-align-top">$ <?= Helper::formatCurrency($realAccount['RTYPE_KOMISI']) ?></td>
                                    </tr>
                                    <!-- <tr>
                                        <td width="30%" class="v-align-top">Introducing Broker</td>
                                        <td width="3%" class="v-align-top">:</td>
                                        <td class="v-align-top"><?= $accnd['MBR_NAME'] ?></td>
                                    </tr> -->
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table>
            <!-- <table class="table no-border" style="margin-top: 20px; border-bottom: 1px solid black !important;">
                <tbody>
                    <tr>
                        <th colspan="2" class="text-center" style="background-color: #edebe0;">Commission Charge</th>
                    </tr>
                    <tr>
                        <td width="50%" class="v-align-top" style="font-size: 15px; text-align: left;">
                            <table class="table no-border">
                                <tbody>
                                    <tr>
                                        <td width="35%" class="v-align-top">Forex</td>
                                        <td width="3%" class="v-align-top">:</td>
                                        <td class="v-align-top"><?= $accnd['ACCCND_CASH_FOREX'] ?? 0 ?></td>
                                    </tr>
                                    <tr>
                                        <td width="35%" class="v-align-top">Locco London</td>
                                        <td width="3%" class="v-align-top">:</td>
                                        <td class="v-align-top"><?= $accnd['ACCCND_CASH_LOCO'] ?? 0 ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                        <td class="v-align-top" style="font-size: 15px; text-align: left;">
                            <table class="table no-border">
                                <tbody>
                                    <tr>
                                        <td width="35%" class="v-align-top">JPK50</td>
                                        <td width="3%" class="v-align-top">:</td>
                                        <td class="v-align-top"><?= $accnd['ACCCND_CASH_JPK50'] ?? 0 ?></td>
                                    </tr>
                                    <tr>
                                        <td width="35%" class="v-align-top">JPK30</td>
                                        <td width="3%" class="v-align-top">:</td>
                                        <td class="v-align-top"><?= $accnd['ACCCND_CASH_JPK30'] ?? 0 ?></td>
                                    </tr>
                                    <tr>
                                        <td width="35%" class="v-align-top">HKK50/HKJ50</td>
                                        <td width="3%" class="v-align-top">:</td>
                                        <td class="v-align-top"><?= $accnd['ACCCND_CASH_HK50'] ?? 0 ?></td>
                                    </tr>
                                    <tr>
                                        <td width="35%" class="v-align-top">KRJ35</td>
                                        <td width="3%" class="v-align-top">:</td>
                                        <td class="v-align-top"><?= $accnd['ACCCND_CASH_KRJ35'] ?? 0 ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table> -->
            <table class="table no-border" style="margin-top: 20px;">
                <tbody>
                    <tr>
                        <td width="50%" class="text-center v-align-top">Accounting</td>
                        <td width="50%" class="text-center v-align-top">Direktur Utama</td>
                    </tr>
                    <tr>
                        <td><div style="height: 50px;"></div></td>
                        <td><div style="height: 50px;"></div></td>
                    </tr>
                    <tr>
                        <td width="50%" class="text-center v-align-top">( &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; )</td>
                        <td width="50%" class="text-center v-align-top">( <?= $companyProfile['PROF_DEWAN_DIREKSI'] ?> )</td>
                    </tr>
                </tbody>
            </table>
            <p class="text-center" style="margin-bottom: 0px;">Menyatakan pada tanggal : <b><?= date("Y-m-d H:i:s", strtotime($accnd['ACCCND_DATEMARGIN'])) ?></b></p>
        </div>
    </body>
</html>