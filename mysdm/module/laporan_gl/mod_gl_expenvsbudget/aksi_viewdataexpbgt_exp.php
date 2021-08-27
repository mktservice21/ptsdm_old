<?php
date_default_timezone_set('Asia/Jakarta');
ini_set("memory_limit","512M");
ini_set('max_execution_time', 0);

session_start();
if (!isset($_SESSION['USERID'])) {
    echo "ANDA HARUS LOGIN ULANG....";
    exit;
}

$pmodule="";
if (isset($_GET['module'])) $pmodule=$_GET['module'];

if ($pmodule=="viewdatadetail") {
    
    include "../../../config/koneksimysqli.php";
    include("../../../config/fungsi_sql.php");
    include("../../../config/common.php");

    
    $fkaryawan=$_SESSION['IDCARD'];
    $fstsadmin=$_SESSION['STSADMIN'];
    $flvlposisi=$_SESSION['LVLPOSISI'];
    $fdivisi=$_SESSION['DIVISI'];
    $fgroupid=$_SESSION['GROUP'];
    $fjbtid=$_SESSION['JABATANID'];
    $pmobile=$_SESSION['MOBILE'];
    
    $psemuadep=false;
    $pbolehpilihdep=false;
    $ppilihlini_produk="";
    $query = "select * from dbproses.maping_karyawan_dep WHERE karyawanid='$fkaryawan' AND iddep='ALL'";
    $tampil= mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($tampil);
    if ((INT)$ketemu>0) {
        $psemuadep=true;
        $pbolehpilihdep=true;
    }
    
    $pilihregion="";
    if ($fjbtid=="05") {
        $query = "select region FROM dbmaster.t_karyawan_posisi WHERE karyawanid='$fkaryawan'";
        $tampil= mysqli_query($cnmy, $query);
        $ketemu= mysqli_num_rows($tampil);
        $row= mysqli_fetch_array($tampil);
        $pilihregion=$row['region'];
    }
    
    
    
    $ppilformat=1;
    $ppilihrpt="";

    if ($ppilihrpt=="excel") {
        $ppilformat=3;
    }
    
    
    
    $pcoa_pilih=$_POST['ucoa'];
    $pkodeid_pilih=$_POST['ukodeid'];
    $pkodeidnm_pilih=$_POST['ukodeidnm'];
    $pblnthn_pilih=$_POST['ubln'];
    
    $pperiode_pl = date('F Y', strtotime($pblnthn_pilih."-01"));
    
    $query = "select NAMA4 FROM dbmaster.coa_level4 WHERE COA4='$pcoa_pilih'";
    $tampil= mysqli_query($cnmy, $query);
    $row= mysqli_fetch_array($tampil);
    $pnamacoa=$row['NAMA4'];
    
    
    $ptahun = $_POST['utahun'];
    $piddep = $_POST['udep'];
    $pidpengajuan = $_POST['upengajuan'];
    $pregion = $_POST['uregion'];
    $pidkrysm = $_POST['ukrysm'];
    $pliniproduk = $_POST['ulproduk'];
    $pncabdiviid = $_POST['ucabangiddivid'];
    $pncabid = $_POST['ucabangid'];
    $pndivcb = $_POST['udivcb'];
    $pnallcoa = $_POST['uall_coa'];
    $pnpilsls = $_POST['upilsls'];
    $pnpilslsgsm = $_POST['upilslsgsm'];
    $pnpilslssm = $_POST['upilslssm'];
    $pnpilmkt = $_POST['upilmkt'];
    
    $pnamadep = $_POST['unmdep'];
    $filternamacabang = $_POST['unmcabang'];

    
    $ppilihsales=false;
    $ppilihsales_gsm=false;
    $ppilihsales_sm=false;
    $ppilihmarketing=false;

    if ($piddep=="SLS" OR $piddep=="SLS01") {
        $ppilihsales=true;
    }

    if ($piddep=="SLS02") {
        $ppilihsales_gsm=true;
    }

    if ($piddep=="SLS03") {
        $ppilihsales_sm=true;
    }

    if ($piddep=="MKT") {
        $ppilihmarketing=true;
    }
    
    $pcabangdivisi="";
    $filtercabang="";
    $filterdivisi="";
    $filter_coa="";
    
    if (!empty($pncabdiviid)) {
        $pcabangdivisi_ = explode(",", $pncabdiviid);
        foreach ($pcabangdivisi_ as $idcabdiv) {
            $pcabangdivisi .="'".$idcabdiv."',";
        }
        if (!empty($pcabangdivisi)) $pcabangdivisi="(".substr($pcabangdivisi, 0, -1).")";
    }
    
    if (!empty($pncabid)) {
        $pncabid_ = explode(",", $pncabid);
        foreach ($pncabid_ as $idcabdiv) {
            $filtercabang .="'".$idcabdiv."',";
        }
        if (!empty($filtercabang)) $filtercabang="(".substr($filtercabang, 0, -1).")";
    }
    
    if (!empty($pndivcb)) {
        $pndivcb_ = explode(",", $pndivcb);
        foreach ($pndivcb_ as $niddiv) {
            $filterdivisi .="'".$niddiv."',";
        }
        if (!empty($filterdivisi)) $filterdivisi="(".substr($filterdivisi, 0, -1).")";
    }
    
    if (!empty($pnallcoa)) {
        $pnallcoa_ = explode(",", $pnallcoa);
        foreach ($pnallcoa_ as $nidcoa) {
            $filter_coa .="'".$nidcoa."',";
        }
        if (!empty($filter_coa)) $filter_coa="(".substr($filter_coa, 0, -1).")";
    }
    
    
    $pkaryawanid_user=$_SESSION['IDCARD'];
    $puserid=$_SESSION['USERID'];
    $now=date("mdYhis");
    $tmp01 =" dbtemp.tmpprosbgtexpdtl01_".$puserid."_$now ";
    $tmp02 =" dbtemp.tmpprosbgtexpdtl02_".$puserid."_$now ";
    $tmp03 =" dbtemp.tmpprosbgtexpdtl03_".$puserid."_$now ";
    
    
    $query = "SELECT * FROM dbproses.proses_expenses WHERE "
            . " tahun='$ptahun' AND coa4='$pcoa_pilih' AND kodeid='$pkodeid_pilih' AND nama_kode='$pkodeidnm_pilih' AND IFNULL(biaya,'')='Y' "
            . " AND LEFT(tanggal,7)='$pblnthn_pilih' ";
    if (!empty($piddep)) $query .=" AND iddep='$piddep' ";
    else{
        
        if ($psemuadep==true) {

        }else{
            $query .=" AND iddep IN (select DISTINCT IFNULL(iddep,'') from dbproses.maping_karyawan_dep WHERE karyawanid='$pkaryawanid_user') ";
            if ($fjbtid=="36") {
                $query .=" AND divisi_pengajuan IN ('OTC', 'OT', 'CHC') ";
            }elseif ($fjbtid=="20") {
                $query .=" AND divisi_pengajuan IN ('ETH') ";
            }elseif ($fjbtid=="05") {
                if ($pkaryawanid_user=="0000000158") $query .=" AND region='B' ";
                elseif ($pkaryawanid_user=="0000000159") $query .=" AND region='T' ";
            }
        }
    
    }
    
    if (!empty($filter_coa)) $query .=" AND coa4 IN $filter_coa ";

    if ($ppilihsales == true) {

        if (empty($pidpengajuan)) {
            if (!empty($filterdivisi)) $query .=" AND divisi_pengajuan IN $filterdivisi ";
        }else{
            $query .=" AND divisi_pengajuan='$pidpengajuan' ";
        }
        //if (!empty($filtercabang)) $query .=" AND icabangid IN $filtercabang ";
        if (!empty($pcabangdivisi)) $query .=" AND CONCAT(icabangid, '|', divisi_pengajuan) IN $pcabangdivisi ";
    }elseif ($ppilihsales_gsm==true) {
        if (!empty($pregion)) {
            $query .=" AND region='$pregion' ";
        }
    }elseif ($ppilihmarketing == true) {
        
        /*
        if (!empty($pidpengajuan)) {
            $query .=" AND divisi_pengajuan='$pidpengajuan' ";
        }

        if ($pidpengajuan=="ETH") {
            //if (!empty($filtercabang)) $query .=" AND icabangid IN $filtercabang ";
        }elseif ($pidpengajuan=="OTC" OR $pidpengajuan=="OT" OR $pidpengajuan=="CHC") {
            if (!empty($filtercabang)) $query .=" AND icabangid IN $filtercabang ";
        }
         * 
         */

        //if ($pliniproduk=="EAGLE") $query .=" AND karyawanid='0000000257' ";
        //elseif ($pliniproduk=="PEACO") $query .=" AND karyawanid='0000000910' ";
        //elseif ($pliniproduk=="PIGEO") $query .=" AND karyawanid='0000000157' ";
        //elseif ($pliniproduk=="OT" OR $pliniproduk=="OTC" OR $pliniproduk=="CHC") $query .=" AND karyawanid='0000001556' ";
        
        if ($pliniproduk=="EAGLE") $query .=" AND ( karyawanid='0000000257' OR (divisi_pengajuan='ETH' AND iddep='MKT') ) ";
        elseif ($pliniproduk=="PEACO") $query .=" AND ( karyawanid='0000000910' OR (divisi_pengajuan='ETH' AND iddep='MKT') ) ";
        elseif ($pliniproduk=="PIGEO") $query .=" AND ( karyawanid='0000000157' OR (divisi_pengajuan='ETH' AND iddep='MKT') ) ";
        elseif ($pliniproduk=="OT" OR $pliniproduk=="OTC" OR $pliniproduk=="CHC") $query .=" AND (karyawanid='0000001556' OR (divisi_pengajuan='OTC' AND iddep='MKT') ) ";
        
    }elseif ($ppilihsales_sm == true) {
        if (!empty($pidkrysm)) {
            $query .=" AND karyawanid='$pidkrysm' ";
        }else{
            if ($fjbtid=="05" AND !empty($pilihregion)) {
                $query .= " AND karyawanid IN (select distinct IFNULL(karyawanid,'') from mkt.ism0 as a "
                        . " JOIN mkt.icabang as b on a.icabangid=b.iCabangId WHERE region='$pilihregion') ";
            }
        }
    }
    
    
    //echo "$query<br/>";
    
    $query = "create TEMPORARY table $tmp01 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); 
    if (!empty($erropesan)) {
        echo $erropesan;
        mysqli_close($cnmy);
        exit;
    }
    
    $query = "DELETE FROM $tmp01 WHERE IFNULL(jumlah,0)=0";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy);  if (!empty($erropesan)) { mysqli_query($cnmy, "drop TEMPORARY table if EXISTS $tmp01"); echo $erropesan; mysqli_close($cnmy); exit; }
    
    
    $query = "SELECT keterangan_proses, kodeinput, idkodeinput, karyawanid, nama_pengaju, tanggal, keterangan1, keterangan2, "
            . " dokterid, distid, "
            . " SUM(jumlah) as jumlah "
            . " FROM $tmp01";
    $query .= " GROUP BY 1,2,3,4,5,6,7,8,9,10";
    $query = "create TEMPORARY table $tmp02 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy);  if (!empty($erropesan)) { mysqli_query($cnmy, "drop TEMPORARY table if EXISTS $tmp01"); echo $erropesan; mysqli_close($cnmy); exit; }
    
    
    $query = "ALTER TABLE $tmp02 ADD COLUMN nama_karyawan VARCHAR(200), ADD COLUMN nama_dokter VARCHAR(200), ADD COLUMN nama_dist VARCHAR(200)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy);  if (!empty($erropesan)) { mysqli_query($cnmy, "drop TEMPORARY table if EXISTS $tmp01"); echo $erropesan; mysqli_close($cnmy); exit; }
    
    $query = "UPDATE $tmp02 as a JOIN hrd.karyawan as b on a.karyawanid=b.karyawanId SET a.nama_karyawan=b.nama";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy);  if (!empty($erropesan)) { mysqli_query($cnmy, "drop TEMPORARY table if EXISTS $tmp01"); echo $erropesan; mysqli_close($cnmy); exit; }
    
    
    $query = "UPDATE $tmp02 SET nama_karyawan=nama_pengaju WHERE IFNULL(nama_karyawan,'')='' AND IFNULL(nama_pengaju,'')<>''";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy);  if (!empty($erropesan)) { mysqli_query($cnmy, "drop TEMPORARY table if EXISTS $tmp01"); echo $erropesan; mysqli_close($cnmy); exit; }
    
    
    $query = "UPDATE $tmp02 as a JOIN hrd.dokter as b on a.dokterid=b.dokterId SET a.nama_dokter=b.nama";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy);  if (!empty($erropesan)) { mysqli_query($cnmy, "drop TEMPORARY table if EXISTS $tmp01"); echo $erropesan; mysqli_close($cnmy); exit; }
    
    $query = "UPDATE $tmp02 as a JOIN mkt.distrib0 as b on a.distid=b.Distid SET a.nama_dist=b.nama";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy);  if (!empty($erropesan)) { mysqli_query($cnmy, "drop TEMPORARY table if EXISTS $tmp01"); echo $erropesan; mysqli_close($cnmy); exit; }
    
    
    if (!empty($pnamadep)) {
        echo "<small><b>Departemen : $pnamadep</b></small><br/>";
    }else{
        echo "<small><b>Departemen : All</b><small><br/>";
    }

    if ($ppilihsales == true OR $ppilihmarketing == true) {

        if (!empty($filternamacabang)) {
            echo "<small>$filternamacabang</small><br/>";
        }
        
    }
    
    echo "<br/><b>COA : $pcoa_pilih - $pnamacoa</b><br/>";
    echo "<b>Nama Jenis : $pkodeidnm_pilih</b><br/>";
    echo "<small><b>Bulan : $pperiode_pl</b></small><br/>";
    
    
    echo "<hr/><br/>";
    
    echo "<div id='div-konten3'>";
    
        echo "<table id='tbltable_jenis' border='1' cellspacing='0' cellpadding='1'>";
            echo "<thead>";
                echo "<tr>";
                    echo "<th align='center'><small>No</small></th>";
                    echo "<th align='center'><small>Periode</small></th>";
                    echo "<th align='center'><small>ID</small></th>";
                    echo "<th align='center'><small>Karyawan</small></th>";
                    echo "<th align='center'><small>Jumlah</small></th>";
                    echo "<th align='center'><small>Keterangan</small></th>";
                    
                echo "</tr>";
                
            echo "</thead>";
            
            echo "<tbody>";
            
                $no=1;
                $query = "select DISTINCT keterangan_proses from $tmp02 ORDER BY keterangan_proses";
                $tampil= mysqli_query($cnmy, $query);
                while ($row= mysqli_fetch_array($tampil)) {
                    $pnamaprosesket=$row['keterangan_proses'];
                    
                    echo "<tr>";
                    echo "<td nowrap colspan='5'><b>$pnamaprosesket</b></td>";
                    echo "</tr>";
                    
                    $no=1;
                    $query = "select * from $tmp02 WHERE keterangan_proses='$pnamaprosesket' ORDER BY keterangan_proses, tanggal, nama_karyawan";
                    $tampil2= mysqli_query($cnmy, $query);
                    while ($row2= mysqli_fetch_array($tampil2)) {
                        
                        $pkodeinput=$row2['kodeinput'];
                        $pidinput=$row2['idkodeinput'];
                        $pnamakaryawan=$row2['nama_karyawan'];
                        $pketerangan1=$row2['keterangan1'];
                        $pketerangan2=$row2['keterangan2'];
                        $ptgl=$row2['tanggal'];
                        $pjumlah=$row2['jumlah'];
                        $pnmdokter=$row2['nama_dokter'];
                        $pnmdist=$row2['nama_dist'];
                        
                        if ($pkodeinput=="4" OR $pkodeinput=="5") {
                            $ptgl = date('F Y', strtotime($ptgl));
                        }
                        
                        
                        if (!empty($pketerangan1)) {
                            if (!empty($pketerangan2)) $pketerangan1 .=", ".$pketerangan2;
                        }else{
                            if (!empty($pketerangan2)) $pketerangan1=$pketerangan2;
                        }
                        
                        if (!empty($pketerangan1)) {
                            if (!empty($pnmdokter)) $pketerangan1 ="User : ".$pnmdokter.", ".$pketerangan1;
                        }else{
                            if (!empty($pnmdokter)) $pketerangan1="User : ".$pnmdokter;
                        }
                        
                        if (!empty($pketerangan1)) {
                            if (!empty($pnmdist)) $pketerangan1 ="".$pnmdist.", ".$pketerangan1;
                        }else{
                            if (!empty($pnmdist)) $pketerangan1="".$pnmdist;
                        }
                        
                        
                        
                        $pjumlah=BuatFormatNumberRp($pjumlah, $ppilformat);//1 OR 2 OR 3
                        
                        
                        echo "<tr>";
                        echo "<td nowrap>$no</td>";
                        echo "<td nowrap>$ptgl</td>";
                        echo "<td nowrap>$pidinput</td>";
                        echo "<td nowrap>$pnamakaryawan</td>";
                        echo "<td nowrap align='right'>$pjumlah</td>";
                        echo "<td >$pketerangan1</td>";
                        echo "</tr>";
                        
                        
                        $no++;
                    }
                    
                }
                
                
            echo "</tbody>";
            
        echo "</table>";
    
    echo "</div>";
    
    
    mysqli_query($cnmy, "drop TEMPORARY table if EXISTS $tmp01");
    mysqli_query($cnmy, "drop TEMPORARY table if EXISTS $tmp02");
    mysqli_query($cnmy, "drop TEMPORARY table if EXISTS $tmp03");
    mysqli_close($cnmy);
    ?>

    <style>
        #btn_jmle {
            border: 1px solid #cccccc;
            border-radius: 3px;
            background-color: white;
        }
        #btn_jmle:hover {
            cursor:pointer;
            background-color: #cccccc;
        }
        #btn_jmle:focus {
            border: 1px solid #cc0000;
            background-color: #fff;
        }
    </style>
    
    <style>
        #tbltable_jenis {
            border-collapse: collapse;
        }
        #tbltable_jenis th {
            font-size : 16px;
            padding:5px;
            background-color: #ccccff;
        }
        #tbltable_jenis tr #tbltable_jenis td {
            font-size : 12px;
        }
        #tbltable_jenis tr td {
            padding : 3px;
        }
        #tbltable_jenis tr:hover {background-color:#f5f5f5;}
        #tbltable_jenis thead tr:hover {background-color:#cccccc;}
    </style>
    
    <?PHP
    
    
}
?>
