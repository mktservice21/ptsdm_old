<?php

session_start();
$pmodule="";
if (isset($_GET['module'])) $pmodule=$_GET['module'];


if ($pmodule=="ceksaldotransfer") {
    
    $ptgl = str_replace('/', '-', $_POST['utgl']);
    $pidinput=$_POST['uid'];
    $pststrf=$_POST['uststrf'];
    
    $ptglpengajuan= date("Y-m-d", strtotime($ptgl));
    
    $nnamatrf="Payroll";
    if ($pststrf=="T") $nnamatrf="Transfer";
    
    
    
    $pjmlca=$_POST['utot1'];
    $pjmlbc=$_POST['utot2'];
    $pjmlnb=$_POST['utot3'];
    $pjmlva=$_POST['utot4'];
    $pjmlpy=$_POST['utot5'];
    $pjmltg=$_POST['utot6'];
    
    
    $pjmlca=str_replace(",","", $pjmlca);
    $pjmlbc=str_replace(",","", $pjmlbc);
    $pjmlnb=str_replace(",","", $pjmlnb);
    $pjmlva=str_replace(",","", $pjmlva);
    $pjmlpy=str_replace(",","", $pjmlpy);
    $pjmltg=str_replace(",","", $pjmltg);
    
    
    $pjmlbatasca=0;
    $pjmlbatasbc=0;
    $pjmlbatasnb=0;
    $pjmlbatasva=0;
    $pjmlbataspy=0;
    $pjmlbatastg=0;
    
    $pjmlsudhinputca=0;
    $pjmlsudhinputbc=0;
    $pjmlsudhinputnb=0;
    $pjmlsudhinputva=0;
    $pjmlsudhinputpy=0;
    $pjmlsudhinputtg=0;
    
    
    include "../../config/koneksimysqli.php";
    
    $pjmlbatas=0;
    $pjmlsudhinput=0;
    
    $query = "select status_trf, jumlah as jmlbts from dbmaster.t_br_batas_trf";//WHERE status_trf IN ('CA', 'BC', 'NB', 'VA', 'PY', 'TG')
    $ntampil= mysqli_query($cnmy, $query);
    $nketemu= mysqli_num_rows($ntampil);
    if ($nketemu>0) {
        while ($nrx= mysqli_fetch_array($ntampil)) {
            $pnmsts=$nrx['status_trf'];
            $pjmlbatas=$nrx['jmlbts'];
            
            if ($pnmsts=="CA") $pjmlbatasca=$pjmlbatas;
            elseif ($pnmsts=="BC") $pjmlbatasbc=$pjmlbatas;
            elseif ($pnmsts=="NB") $pjmlbatasnb=$pjmlbatas;
            elseif ($pnmsts=="VA") $pjmlbatasva=$pjmlbatas;
            elseif ($pnmsts=="PY") $pjmlbataspy=$pjmlbatas;
            elseif ($pnmsts=="TG") $pjmlbatastg=$pjmlbatas;
        }
    }
    
    
    $query = "select status_trf, sum(jumlah) as jumlah from dbmaster.t_br_antrian WHERE IFNULL(stsnonaktif,'')<>'Y' AND tanggal='$ptglpengajuan' "
            . " AND idgroup<>'$pidinput' GROUP BY 1";
    $tampil=mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($tampil);
    if ($ketemu>0) {
        while ($nr= mysqli_fetch_array($tampil)) {
            $pnmsts=$nr['status_trf'];
            $pjmlinputsudh=$nr['jumlah'];
            
            if ($pnmsts=="CA") $pjmlsudhinputca=$pjmlinputsudh;
            elseif ($pnmsts=="BC") $pjmlsudhinputbc=$pjmlinputsudh;
            elseif ($pnmsts=="NB") $pjmlsudhinputnb=$pjmlinputsudh;
            elseif ($pnmsts=="VA") $pjmlsudhinputva=$pjmlinputsudh;
            elseif ($pnmsts=="PY") $pjmlsudhinputpy=$pjmlinputsudh;
            elseif ($pnmsts=="TG") $pjmlsudhinputtg=$pjmlinputsudh;
            
        }
    }
    
    $pjmlsudhinputca=(DOUBLE)$pjmlsudhinputca+(DOUBLE)$pjmlca;
    $pjmlsudhinputbc=(DOUBLE)$pjmlsudhinputbc+(DOUBLE)$pjmlbc;
    $pjmlsudhinputnb=(DOUBLE)$pjmlsudhinputnb+(DOUBLE)$pjmlnb;
    $pjmlsudhinputva=(DOUBLE)$pjmlsudhinputva+(DOUBLE)$pjmlva;
    $pjmlsudhinputpy=(DOUBLE)$pjmlsudhinputpy+(DOUBLE)$pjmlpy;
    $pjmlsudhinputtg=(DOUBLE)$pjmlsudhinputtg+(DOUBLE)$pjmltg;
    
    
    
    $bolehinput="boleh";
    
    
    //TUNAI atau CASH
    if ((DOUBLE)$pjmlca>0) {
        if ((DOUBLE)$pjmlsudhinputca>(DOUBLE)$pjmlbatasca) {
            $pjmlsudhinputca=(DOUBLE)$pjmlsudhinputca-(DOUBLE)$pjmlca;

            $pjmlbatasca=number_format($pjmlbatasca,0,",",",");
            $pjmlsudhinputca=number_format($pjmlsudhinputca,0,",",",");
            $pjmlakaninputca=number_format($pjmlca,2,",",",");

            $bolehinput="Batas Input Tunai/Cash Rp. $pjmlbatasca, sudah ada input Rp. $pjmlsudhinputca. Anda akan input Rp. $pjmlakaninputca";

            echo $bolehinput; mysqli_close($cnmy); exit;
        }
    }
    
    //BCA
    if ((DOUBLE)$pjmlbc>0) {
        if ((DOUBLE)$pjmlsudhinputbc>(DOUBLE)$pjmlbatasbc) {
            $pjmlsudhinputbc=(DOUBLE)$pjmlsudhinputbc-(DOUBLE)$pjmlbc;

            $pjmlbatasbc=number_format($pjmlbatasbc,0,",",",");
            $pjmlsudhinputbc=number_format($pjmlsudhinputbc,0,",",",");
            $pjmlakaninputbc=number_format($pjmlbc,2,",",",");

            $bolehinput="Batas Input BCA Rp. $pjmlbatasbc, sudah ada input Rp. $pjmlsudhinputbc. Anda akan input Rp. $pjmlakaninputbc";

            echo $bolehinput; mysqli_close($cnmy); exit;
        }
    }
    
    //NON BCA
    if ((DOUBLE)$pjmlnb>0) {
        if ((DOUBLE)$pjmlsudhinputnb>(DOUBLE)$pjmlbatasnb) {
            $pjmlsudhinputnb=(DOUBLE)$pjmlsudhinputnb-(DOUBLE)$pjmlnb;

            $pjmlbatasnb=number_format($pjmlbatasnb,0,",",",");
            $pjmlsudhinputnb=number_format($pjmlsudhinputnb,0,",",",");
            $pjmlakaninputnb=number_format($pjmlnb,2,",",",");

            $bolehinput="Batas Input NON BCA Rp. $pjmlbatasnb, sudah ada input Rp. $pjmlsudhinputnb. Anda akan input Rp. $pjmlakaninputnb";

            echo $bolehinput; mysqli_close($cnmy); exit;
        }
    }
    
    //VIRTUAL ACCOUNT
    if ((DOUBLE)$pjmlva>0) {
        if ((DOUBLE)$pjmlsudhinputva>(DOUBLE)$pjmlbatasva) {
            $pjmlsudhinputva=(DOUBLE)$pjmlsudhinputva-(DOUBLE)$pjmlva;

            $pjmlbatasva=number_format($pjmlbatasva,0,",",",");
            $pjmlsudhinputva=number_format($pjmlsudhinputva,0,",",",");
            $pjmlakaninputva=number_format($pjmlva,2,",",",");

            $bolehinput="Batas Input Virtual Account Rp. $pjmlbatasva, sudah ada input Rp. $pjmlsudhinputva. Anda akan input Rp. $pjmlakaninputva";

            echo $bolehinput; mysqli_close($cnmy); exit;
        }
    }
    
    //PAYROLL
    if ((DOUBLE)$pjmlpy>0) {
        if ((DOUBLE)$pjmlsudhinputpy>(DOUBLE)$pjmlbataspy) {
            $pjmlsudhinputpy=(DOUBLE)$pjmlsudhinputpy-(DOUBLE)$pjmlpy;

            $pjmlbataspy=number_format($pjmlbataspy,0,",",",");
            $pjmlsudhinputpy=number_format($pjmlsudhinputpy,0,",",",");
            $pjmlakaninputpy=number_format($pjmlpy,2,",",",");

            $bolehinput="Batas Input Payroll Rp. $pjmlbataspy, sudah ada input Rp. $pjmlsudhinputpy. Anda akan input Rp. $pjmlakaninputpy";

            echo $bolehinput; mysqli_close($cnmy); exit;
        }
    }
    
    //TAGIHAN
    if ((DOUBLE)$pjmltg>0) {
        if ((DOUBLE)$pjmlsudhinputtg>(DOUBLE)$pjmlbatastg) {
            $pjmlsudhinputtg=(DOUBLE)$pjmlsudhinputtg-(DOUBLE)$pjmltg;

            $pjmlbatastg=number_format($pjmlbatastg,0,",",",");
            $pjmlsudhinputtg=number_format($pjmlsudhinputtg,0,",",",");
            $pjmlakaninputtg=number_format($pjmltg,2,",",",");

            $bolehinput="Batas Input Tagihan Rp. $pjmlbatastg, sudah ada input Rp. $pjmlsudhinputtg. Anda akan input Rp. $pjmlakaninputtg";

            echo $bolehinput; mysqli_close($cnmy); exit;
        }
    }
    
    mysqli_close($cnmy);
    
    echo "$bolehinput";
    
    
}

?>