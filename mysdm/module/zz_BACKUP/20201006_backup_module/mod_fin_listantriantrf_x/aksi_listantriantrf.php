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
    
    $ptglpengajuan= date("Y-m-d", strtotime($ptgl));
    $pjumlah=str_replace(",","", $pjumlah);
    $nnamatrf="Payroll";
    if ($pststrf=="T") $nnamatrf="Transfer";
    
    $pkaryawanid=$_POST['cb_karyawan'];
    $pidinputspd=$_POST['e_idnobr'];
    $pketerangan=$_POST['e_ket'];
    if (!empty($pketerangan)) $pketerangan = str_replace("'", '', $pketerangan);
    
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
        $pjmlakaninput=number_format($pjumlah,0,",",",");
        
        $bolehinput="Batas Input $nnamatrf Rp. $pjmlbatas, sudah ada input Rp. $pjmlsudhinput. Anda akan input Rp. $pjmlakaninput";
        
        echo $bolehinput; mysqli_close($cnmy); exit;
    }
    
    
    $puserinput=$_SESSION['IDCARD'];
    
    
    
    //echo "$pidinput, $ptglpengajuan, $pkaryawanid, $pidinputspd, $pjumlah, $pststrf : $nnamatrf, $pketerangan";
    
    
    if ($act=="input") {
        
        $pnourut=1;
        
        $query = "select MAX(nourut) as pnourut from dbmaster.t_br_antrian WHERE tanggal='$ptglpengajuan' "
                . " AND status_trf='$pststrf'";
        $tampilu=mysqli_query($cnmy, $query);
        $ketemuu= mysqli_num_rows($tampilu);
        if ($ketemuu>0) {
            $ur= mysqli_fetch_array($tampilu);
            
            if (!empty($ur['pnourut'])) $pnourut=(DOUBLE)$ur['pnourut']+1;
        }
    
        $query_eksekusi = "INSERT INTO dbmaster.t_br_antrian (karyawanid, tanggal, idinput, jumlah, nourut, status_trf, keterangan, userid)"
                . "VALUES('$pkaryawanid', '$ptglpengajuan', '$pidinputspd', '$pjumlah', '$pnourut', '$pststrf', '$pketerangan', '$puserinput')";
        mysqli_query($cnmy, $query_eksekusi);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
        
    }elseif ($act=="update") {
        
        $query_eksekusi = "UPDATE dbmaster.t_br_antrian SET karyawanid='$pkaryawanid', "
                . " tanggal='$ptglpengajuan', idinput='$pidinputspd',  jumlah='$pjumlah', status_trf='$pststrf', keterangan='$pketerangan', "
                . " userid='$puserinput' WHERE idantrian='$pidinput'";
        mysqli_query($cnmy, $query_eksekusi);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
        
    }
    
    
    mysqli_close($cnmy);
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}

?>