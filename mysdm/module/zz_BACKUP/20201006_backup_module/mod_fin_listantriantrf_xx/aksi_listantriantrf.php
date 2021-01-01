<?php
session_start();

$puserid=$_SESSION['USERID'];
if (empty($puserid)) {
    echo "ANDA HARUS LOGIN ULANG...";
    exit;
}

$module=$_GET['module'];
$act=$_GET['act'];
$idmenu=$_GET['idmenu'];

if ($module=='listantriantransfer' AND $act=="hapus")
{
    include "../../config/koneksimysqli.php";
    $pidinput=$_GET['id'];
    $puserinput=$_SESSION['IDCARD'];
    
    if (!empty($pidinput)) {
        $query = "UPDATE dbmaster.t_br_antrian SET stsnonaktif='Y', userid='$puserinput' WHERE idantrian='$pidinput'";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
    }
    
    mysqli_close($cnmy);
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}
elseif ($module=='listantriantransfer' AND $act=="selesai")
{
    include "../../config/koneksimysqli.php";
    $pidinput=$_GET['id'];
    $puserinput=$_SESSION['IDCARD'];
    
    if (!empty($pidinput)) {
        $query = "UPDATE dbmaster.t_br_antrian SET selesai='Y', userid='$puserinput', tgl_selesai=NOW() WHERE idantrian='$pidinput'";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
    }
    
    mysqli_close($cnmy);
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}
elseif ($module=='listantriantransfer')
{
    
    include "../../config/koneksimysqli.php";
    
    
    $pidinput=$_POST['e_id'];
    $ptgl = str_replace('/', '-', $_POST['e_tglberlaku']);
    $pjumlah=$_POST['e_jmltrf'];
    $pststrf=$_POST['cb_ststrf'];
    
    $pjmlca=$_POST['e_jmlcashrp'];
    $pjmlbc=$_POST['e_jmlbcarp'];
    $pjmlnb=$_POST['e_jmlnonbcarp'];
    $pjmlva=$_POST['e_jmlvarp'];
    $pjmlpy=$_POST['e_jmlpayrolrp'];
    $pjmltg=$_POST['e_jmltagihrp'];
    
    $ptglpengajuan= date("Y-m-d", strtotime($ptgl));
    $pjumlah=str_replace(",","", $pjumlah);
    
    $pjmlca=str_replace(",","", $pjmlca);
    $pjmlbc=str_replace(",","", $pjmlbc);
    $pjmlnb=str_replace(",","", $pjmlnb);
    $pjmlva=str_replace(",","", $pjmlva);
    $pjmlpy=str_replace(",","", $pjmlpy);
    $pjmltg=str_replace(",","", $pjmltg);
    
    //$pjmltg=(double)$pjmltg+0.1;//untuk test batas / limit
    
    $nnamatrf="Payroll";
    if ($pststrf=="T") $nnamatrf="Transfer";
    
    $pkaryawanid=$_POST['cb_karyawan'];
    $pidinputspd=$_POST['e_idnobr'];
    $pketerangan=$_POST['e_ket'];
    if (!empty($pketerangan)) $pketerangan = str_replace("'", '', $pketerangan);
    
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
    
    //echo "$pjmlbatasca<br/>$pjmlbatasbc<br/>$pjmlbatasnb<br/>$pjmlbatasva<br/>$pjmlbataspy<br/>$pjmlbatastg<br/>"; exit;
    
    $query = "select status_trf, sum(jumlah) as jumlah from dbmaster.t_br_antrian WHERE IFNULL(stsnonaktif,'')<>'Y' AND tanggal='$ptglpengajuan' "
            . " AND idantrian<>'$pidinput' GROUP BY 1";
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
    
    
    //echo "$pjmlsudhinputca<br/>$pjmlsudhinputbc<br/>$pjmlsudhinputnb<br/>$pjmlsudhinputva<br/>$pjmlsudhinputpy<br/>$pjmlsudhinputtg<br/>"; exit;
    
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
    
    
    //echo "$pjmlca<br/>$pjmlbc<br/>$pjmlnb<br/>$pjmlva<br/>$pjmlpy<br/>$pjmltg<br/>"; exit;
    
    
    $puserinput=$_SESSION['IDCARD'];
    
    
    
    //echo "$pidinput, $ptglpengajuan, $pkaryawanid, $pidinputspd, $pjumlah, $pststrf : $nnamatrf, $pketerangan";
    
    
    if ($act=="input") {
        
        
        $pnourutca=1;
        $pnourutbc=1;
        $pnourutnb=1;
        $pnourutva=1;
        $pnourutpy=1;
        $pnouruttg=1;
        
        $query = "select status_trf, MAX(nourut) as pnourut from dbmaster.t_br_antrian WHERE tanggal='$ptglpengajuan' GROUP BY 1";
        $tampilu=mysqli_query($cnmy, $query);
        $ketemuu= mysqli_num_rows($tampilu);
        if ($ketemuu>0) {
            while ($ur= mysqli_fetch_array($tampilu)) {
                $pnmsts=$ur['status_trf'];
                $pnourutnya=$ur['pnourut'];
                
                if (empty($pnourutnya)) $pnourutnya=0;
                $pnourutnya=(DOUBLE)$pnourutnya+1;
                
                if ($pnmsts=="CA") $pnourutca=$pnourutnya;
                elseif ($pnmsts=="BC") $pnourutbc=$pnourutnya;
                elseif ($pnmsts=="NB") $pnourutnb=$pnourutnya;
                elseif ($pnmsts=="VA") $pnourutva=$pnourutnya;
                elseif ($pnmsts=="PY") $pnourutpy=$pnourutnya;
                elseif ($pnmsts=="TG") $pnouruttg=$pnourutnya;
                
            }
        }
        
        $pgroupid=1;
        
        $query = "select MAX(idgroup) as idgroup from dbmaster.t_br_antrian";
        $tampilg=mysqli_query($cnmy, $query);
        $ketemug= mysqli_num_rows($tampilg);
        if ($ketemug>0) {
            $gr= mysqli_fetch_array($tampilg);
            
            $pigroupnya=$gr['idgroup'];
            if (empty($pigroupnya)) $pigroupnya=0;
            $pgroupid=(DOUBLE)$pigroupnya+1;
            
        }
        
        
        $query = "select idgroup from dbmaster.t_br_antrian WHERE idgroup='$pgroupid'";
        $tampilgn=mysqli_query($cnmy, $query);
        $ketemugn= mysqli_num_rows($tampilgn);
        if ($ketemugn>0) {
            $bolehinput="ID GROUP SUDAH ADA....";
            echo $bolehinput; mysqli_close($cnmy); exit;
        }
        
        
        
        //echo "$pnourutca<br/>$pnourutbc<br/>$pnourutnb<br/>$pnourutva<br/>$pnourutpy<br/>$pnouruttg<br/>idgroup : $pgroupid"; exit;
        
        unset($insert_antrian);
        $pbolehsave=false;
        
        
        

        if ((DOUBLE)$pjmlca>0) {
            $insert_antrian[] = "('$pkaryawanid', '$ptglpengajuan', '$pidinputspd', '$pjmlca', '$pnourutca', 'CA', '$pketerangan', '$puserinput', '$pgroupid')";
            $pbolehsave=true;
        }
        
        if ((DOUBLE)$pjmlbc>0) {
            $insert_antrian[] = "('$pkaryawanid', '$ptglpengajuan', '$pidinputspd', '$pjmlbc', '$pnourutbc', 'BC', '$pketerangan', '$puserinput', '$pgroupid')";
            $pbolehsave=true;
        }
        
        if ((DOUBLE)$pjmlnb>0) {
            $insert_antrian[] = "('$pkaryawanid', '$ptglpengajuan', '$pidinputspd', '$pjmlnb', '$pnourutnb', 'NB', '$pketerangan', '$puserinput', '$pgroupid')";
            $pbolehsave=true;
        }
        
        if ((DOUBLE)$pjmlva>0) {
            $insert_antrian[] = "('$pkaryawanid', '$ptglpengajuan', '$pidinputspd', '$pjmlva', '$pnourutva', 'VA', '$pketerangan', '$puserinput', '$pgroupid')";
            $pbolehsave=true;
        }
        
        if ((DOUBLE)$pjmlpy>0) {
            $insert_antrian[] = "('$pkaryawanid', '$ptglpengajuan', '$pidinputspd', '$pjmlpy', '$pnourutpy', 'PY', '$pketerangan', '$puserinput', '$pgroupid')";
            $pbolehsave=true;
        }
        
        if ((DOUBLE)$pjmltg>0) {
            $insert_antrian[] = "('$pkaryawanid', '$ptglpengajuan', '$pidinputspd', '$pjmltg', '$pnouruttg', 'TG', '$pketerangan', '$puserinput', '$pgroupid')";
            $pbolehsave=true;
        }
        
        
        if ($pbolehsave==true) {
            $query_antrian = "INSERT INTO dbmaster.t_br_antrian (karyawanid, tanggal, idinput, jumlah, nourut, status_trf, keterangan, userid, idgroup) VALUES "
                . " ".implode(', ', $insert_antrian);
            mysqli_query($cnmy, $query_antrian);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
        }
        
        
    
        $query_eksekusi = "INSERT INTO dbmaster.t_br_antrian (karyawanid, tanggal, idinput, jumlah, nourut, status_trf, keterangan, userid)"
                . "VALUES('$pkaryawanid', '$ptglpengajuan', '$pidinputspd', '$pjumlah', '$pnourut', '$pststrf', '$pketerangan', '$puserinput')";
        //mysqli_query($cnmy, $query_eksekusi);
        //$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
        
    }elseif ($act=="update") {
        
        
        if ((DOUBLE)$pjmlca>0) {
            $query_eksekusi = "UPDATE dbmaster.t_br_antrian SET karyawanid='$pkaryawanid', tanggal='$ptglpengajuan', idinput='$pidinputspd',  "
                    . " jumlah='$pjmlca', keterangan='$pketerangan', userid='$puserinput' WHERE idgroup='$pidinput' AND "
                    . " status_trf='CA' LIMIT 1";
            mysqli_query($cnmy, $query_eksekusi);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
        }
        
        if ((DOUBLE)$pjmlbc>0) {
            $query_eksekusi = "UPDATE dbmaster.t_br_antrian SET karyawanid='$pkaryawanid', tanggal='$ptglpengajuan', idinput='$pidinputspd',  "
                    . " jumlah='$pjmlbc', keterangan='$pketerangan', userid='$puserinput' WHERE idgroup='$pidinput' AND "
                    . " status_trf='BC' LIMIT 1";
            mysqli_query($cnmy, $query_eksekusi);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
        }
        
        if ((DOUBLE)$pjmlnb>0) {
            $query_eksekusi = "UPDATE dbmaster.t_br_antrian SET karyawanid='$pkaryawanid', tanggal='$ptglpengajuan', idinput='$pidinputspd',  "
                    . " jumlah='$pjmlnb', keterangan='$pketerangan', userid='$puserinput' WHERE idgroup='$pidinput' AND "
                    . " status_trf='NB' LIMIT 1";
            mysqli_query($cnmy, $query_eksekusi);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
        }
        
        if ((DOUBLE)$pjmlva>0) {
            $query_eksekusi = "UPDATE dbmaster.t_br_antrian SET karyawanid='$pkaryawanid', tanggal='$ptglpengajuan', idinput='$pidinputspd',  "
                    . " jumlah='$pjmlva', keterangan='$pketerangan', userid='$puserinput' WHERE idgroup='$pidinput' AND "
                    . " status_trf='VA' LIMIT 1";
            mysqli_query($cnmy, $query_eksekusi);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
        }
        
        if ((DOUBLE)$pjmlpy>0) {
            $query_eksekusi = "UPDATE dbmaster.t_br_antrian SET karyawanid='$pkaryawanid', tanggal='$ptglpengajuan', idinput='$pidinputspd',  "
                    . " jumlah='$pjmlpy', keterangan='$pketerangan', userid='$puserinput' WHERE idgroup='$pidinput' AND "
                    . " status_trf='PY' LIMIT 1";
            mysqli_query($cnmy, $query_eksekusi);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
        }
        
        if ((DOUBLE)$pjmltg>0) {
            $query_eksekusi = "UPDATE dbmaster.t_br_antrian SET karyawanid='$pkaryawanid', tanggal='$ptglpengajuan', idinput='$pidinputspd',  "
                    . " jumlah='$pjmltg', keterangan='$pketerangan', userid='$puserinput' WHERE idgroup='$pidinput' AND "
                    . " status_trf='TG' LIMIT 1";
            mysqli_query($cnmy, $query_eksekusi);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
        }
        
        
        
        
        
        $query_eksekusi = "UPDATE dbmaster.t_br_antrian SET karyawanid='$pkaryawanid', "
                . " tanggal='$ptglpengajuan', idinput='$pidinputspd',  jumlah='$pjumlah', status_trf='$pststrf', keterangan='$pketerangan', "
                . " userid='$puserinput' WHERE idantrian='$pidinput'";
        //mysqli_query($cnmy, $query_eksekusi);
        //$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
        
    }
    
    
    mysqli_close($cnmy);
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}

?>