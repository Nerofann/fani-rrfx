<?php
    $dt->query('
        SELECT
            tb_salesstuc.SLSSTRC_NAME,
            tb_racctype.RTYPE_NAME,
            tb_racctype.RTYPE_RATE,
            tb_racctype.RTYPE_KOMISI,
            tb_symbolcat.SYMCAT_NAME,
            tb_commset.COMMSET_AMOUNT
        FROM tb_commset
        JOIN tb_symbolcat ON(tb_symbolcat.ID_SYMCAT = tb_commset.COMMSET_SYMCAT)
        JOIN tb_racctype ON(tb_racctype.ID_RTYPE = tb_commset.COMMSET_PRODUCT)
        JOIN tb_salesstuc ON(tb_salesstuc.ID_SLSSTRC = tb_commset.COMMSET_SALESCAT)
    ');

    echo $dt->generate()->toJson();