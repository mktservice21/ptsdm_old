<?php
session_start();

    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","10G");
    ini_set('max_execution_time', 0);
    
$puserid="";
$pidcard="";
$pidgroup="";
if (isset($_SESSION['USERID'])) $puserid=$_SESSION['USERID'];
if (isset($_SESSION['IDCARD'])) $pidcard=$_SESSION['IDCARD'];
if (isset($_SESSION['GROUP'])) $pidgroup=$_SESSION['GROUP'];


$module=$_GET['module'];
$act=$_GET['act'];
$idmenu=$_GET['idmenu'];
if ($module=='mapsalescust')
{
    if ($act=="update") {
        
        $pcardidlog=$_POST['e_idcardlogin'];
        if (empty($pcardidlog)) $pcardidlog=$pidcard;
        
        if (empty($pcardidlog)) {
            echo "ANDA HARUS LOGIN ULANG...";
            exit;
        }
        
        include "../../config/koneksimysqli_ms.php";
        include "../../config/koneksimysqli_it.php";
        
        $pidfaktur=$_POST['e_id'];
        $ptgl1=$_POST['e_tgl01'];
        $ptgl2=$_POST['e_tgl02'];
        $pidcab=$_POST['e_idcabang'];
        $pidarea=$_POST['e_idarea'];
        $pidcustlama=$_POST['e_idcustlama'];
        
        $pidcustganti=$_POST['e_idcust'];
        
        $query = "select s.fakturid, s.iprodid from sls.mr_sales2 s WHERE s.tgljual BETWEEN '$ptgl1' AND '$ptgl2'
                AND s.icabangid NOT IN (30,31) 
                AND s.fakturid='$pidfaktur' AND s.icustid='$pidcustlama'";
        
        $tampil= mysqli_query($cnms, $query);
        $pjmlrec= mysqli_num_rows($tampil);
        if ((DOUBLE)$pjmlrec>0) {
            
            
            
            $query = "UPDATE MKT.mr_sales2 SET icustid='$pidcustganti' WHERE tgljual BETWEEN '$ptgl1' AND '$ptgl2'
                    AND icabangid NOT IN (30,31) 
                    AND fakturid='$pidfaktur' AND icabangid='$pidcab' AND areaid='$pidarea' AND icustid='$pidcustlama' LIMIT $pjmlrec";
            mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnit); exit; }
            
            $query = "UPDATE sls.mr_sales2 SET icustid='$pidcustganti' WHERE tgljual BETWEEN '$ptgl1' AND '$ptgl2'
                    AND icabangid NOT IN (30,31) 
                    AND fakturid='$pidfaktur' AND icabangid='$pidcab' AND areaid='$pidarea' AND icustid='$pidcustlama' LIMIT $pjmlrec";
            mysqli_query($cnms, $query); $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnms); exit; }
            
        
            //echo "jml ($pjmlrec) - Faktur : $pidfaktur, tgl $ptgl1 - $ptgl2, C : $pidcab, A : $pidarea, Cust Lama : $pidcustlama<br/>Cust Baru : $pidcustganti<br/>";
            
            
        }
        
        
        mysqli_close($cnms);
        mysqli_close($cnit);
        header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=sudahsimpan');
        
    }
    
    
}
?>