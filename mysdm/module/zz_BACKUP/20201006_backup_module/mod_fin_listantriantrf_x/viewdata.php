<?php

session_start();
$pmodule="";
if (isset($_GET['module'])) $pmodule=$_GET['module'];


if ($pmodule=="ceksaldotransfer") {
    
    $ptgl = str_replace('/', '-', $_POST['utgl']);
    $pjumlah=$_POST['ujumlah'];
    $pidinput=$_POST['uid'];
    $pststrf=$_POST['uststrf'];
    
    $ptglpengajuan= date("Y-m-d", strtotime($ptgl));
    $pjumlah=str_replace(",","", $pjumlah);
    
    $nnamatrf="Payroll";
    if ($pststrf=="T") $nnamatrf="Transfer";
    
    include "../../config/koneksimysqli.php";
    
    $pjmlbatas=0;
    $pjmlsudhinput=0;
    
    $query = "select jumlah as jmlbts from dbmaster.t_br_batas_trf WHERE status_trf='$pststrf'";
    $ntampil= mysqli_query($cnmy, $query);
    $nketemu= mysqli_num_rows($ntampil);
    if ($nketemu>0) {
        $nrx= mysqli_fetch_array($ntampil);
        $pjmlbatas=$nrx['jmlbts'];
    }
    
    
    $query = "select sum(jumlah) as jumlah from dbmaster.t_br_antrian WHERE IFNULL(stsnonaktif,'')<>'Y' AND tanggal='$ptglpengajuan' "
            . " AND idantrian<>'$pidinput' AND status_trf='$pststrf'";
    $tampil=mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($tampil);
    if ($ketemu>0) {
        $nr= mysqli_fetch_array($tampil);
        $pjmlsudhinput=$nr['jumlah'];
    }
    
    $pjmlsudhinput=(DOUBLE)$pjmlsudhinput+(DOUBLE)$pjumlah;
    
    $bolehinput="boleh";
    
    if ((DOUBLE)$pjmlsudhinput>(DOUBLE)$pjmlbatas) {
        $pjmlsudhinput=(DOUBLE)$pjmlsudhinput-(DOUBLE)$pjumlah;
        
        $pjmlbatas=number_format($pjmlbatas,0,",",",");
        $pjmlsudhinput=number_format($pjmlsudhinput,0,",",",");
        
        $bolehinput="Batas Input $nnamatrf Rp. $pjmlbatas, sudah ada input Rp. $pjmlsudhinput ";
    }
    
    mysqli_close($cnmy);
    
    echo "$bolehinput";
    
    
}

?>