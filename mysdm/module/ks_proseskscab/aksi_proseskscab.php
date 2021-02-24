<?php

session_start();

date_default_timezone_set('Asia/Jakarta');
ini_set("memory_limit","512M");
ini_set('max_execution_time', 0);


$module=$_GET['module'];
$act=$_GET['act'];
$idmenu=$_GET['idmenu'];
    
$puserid="";
$pnamalengkap="";
if (isset($_SESSION['USERID'])) $puserid=$_SESSION['USERID'];
if (isset($_SESSION['NAMALENGKAP'])) $pnamalengkap=$_SESSION['NAMALENGKAP'];

if (empty($puserid)) {
    echo "ANDA HARUS LOGIN ULANG...";
    exit;
}

if ($module=="ksprosesdatakscab" AND $module="prosesdatakscab") {
    include "config/koneksimysqli.php";
    
    $pidcabang=$_POST['cb_cabang'];

    $query = "select nama from mkt.icabang where icabangid='$pidcabang'";
    $tampilk=mysqli_query($cnmy, $query);
    $rowk=mysqli_fetch_array($tampilk);
    $pnamacabpl=$rowk['nama'];


    $now=date("mdYhis");
    $tmp01 =" dbtemp.tmpprosks1pcab01_".$puserid."_".$now;
    $tmp02 =" dbtemp.tmpprosks1pcab02_".$puserid."_".$now;
    $tmp03 =" dbtemp.tmpprosks1pcab03_".$puserid."_".$now;
    $tmp04 =" dbtemp.tmpprosks1pcab04_".$puserid."_".$now;
    $tmp05 =" dbtemp.tmpprosks1pcab05_".$puserid."_".$now;
    $tmp06 =" dbtemp.tmpprosks1pcab06_".$puserid."_".$now;

    $query = "create TEMPORARY table $tmp01 (icabangid varchar(10), karyawanid varchar(10))";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    $query = "insert into $tmp01 (icabangid, karyawanid) select distinct icabangid, karyawanid from mkt.imr0 where 
        icabangid='$pidcabang' AND IFNULL(karyawanid,'')<>''"; //AND karyawanid='0000000896'
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    $query = "insert into $tmp01 (icabangid, karyawanid) select distinct icabangid, karyawanid from mkt.ispv0 where 
        icabangid='$pidcabang' AND IFNULL(karyawanid,'')<>''";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    $query = "insert into $tmp01 (icabangid, karyawanid) select distinct icabangid, karyawanid from mkt.idm0 where 
        icabangid='$pidcabang' AND IFNULL(karyawanid,'')<>''";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "select b.icabangid, a.srid, a.dokterid, a.apttype, a.bulan, a.iprodid, a.cn_ks as cn, sum(a.qty) as qty, sum(a.qty*a.hna) as tvalue 
        from hrd.ks1 as a JOIN (select distinct karyawanid, icabangid from $tmp01) as b on a.srid=b.karyawanid
        GROUP BY 1,2,3,4,5,6,7";
    $query = "CREATE TEMPORARY TABLE $tmp02 ($query)";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    $query = "ALTER TABLE $tmp02 ADD awal DECIMAL(20,2), ADD ki DECIMAL(20,2), ADD saldocn DECIMAL(20,2), ADD saldoakhir DECIMAL(20,2)";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    $query = "select b.icabangid, a.tgl, DATE_FORMAT(a.tgl, '%Y-%m-01') as bulan, a.karyawanid, a.dokterid, a.awal, a.cn from hrd.mrdoktbaru as a 
        JOIN (select distinct srid, icabangid from $tmp02) as b on 
        a.karyawanid=b.srid";
    $query = "CREATE TEMPORARY TABLE $tmp03 ($query)";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    $query = "UPDATE $tmp03 SET tgl='0000-00-00' WHERE IFNULL(tgl,'')=''";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    $query = "UPDATE $tmp03 SET bulan=left(tgl,7)";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


    $query = "UPDATE $tmp02 SET saldocn=case when IFNULL(cn,0)=0 then 0 else IFNULL(tvalue,0)*(IFNULL(cn,0)/100) end";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp02 SET saldocn=case when IFNULL(cn,0)=0 then 0 else (IFNULL(tvalue,0)*0.8) * (IFNULL(cn,0)/100) end WHERE apttype<>'1'";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    

    $query = "select a.brid, a.tgl, DATE_FORMAT(tgl,'%Y-%m') as bulan, a.mrid, a.dokterid, a.jumlah, a.jumlah1 
        from hrd.br0 as a JOIN hrd.br_kode as b on a.kode=b.kodeid join 
        (select distinct srid, dokterid from $tmp02) as c 
        on a.mrid=c.srid AND a.dokterid=c.dokterid where b.ks='Y' and IFNULL(a.batal,'')<>'Y' AND 
        IFNULL(a.retur,'')<>'Y' and a.brid not in (select distinct IFNULL(brid,'') from hrd.br0_reject)";
    $query = "CREATE TEMPORARY TABLE $tmp04 ($query)";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    $query = "UPDATE $tmp04 set jumlah=jumlah1 WHERE IFNULL(jumlah1,0)<>0";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "CREATE TEMPORARY TABLE $tmp05 (select distinct bulan from $tmp02)";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    $query = "insert into $tmp02 (srid, dokterid, bulan) 
        select distinct mrid, dokterid, bulan 
        FROM $tmp04 WHERE bulan NOT IN (select distinct IFNULL(bulan,'') FROM $tmp05)";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    $query = "UPDATE $tmp02 as a JOIN $tmp01 as b on a.srid=b.karyawanid SET a.icabangid=b.icabangid WHERE IFNULL(a.icabangid,'')=''";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    
    $query = "select icabangid, srid, dokterid, sum(qty) as qty, sum(tvalue) as tvalue, sum(saldocn) as saldocn FROM $tmp02 GROUP BY 1,2,3";
    $query = "CREATE TEMPORARY TABLE $tmp06 ($query)";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


    $query = "ALTER TABLE $tmp06 ADD saldoawal DECIMAL(20,2), ADD ki DECIMAL(20,2), ADD saldoakhir DECIMAL(20,2)";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


    $query = "update $tmp06 as a JOIN (select karyawanid, dokterid, awal from $tmp03 WHERE IFNULL(awal,0)<>0) as b 
        on a.srid=b.karyawanid and a.dokterid=b.dokterid SET a.saldoawal=b.awal";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    $query = "update $tmp06 as a JOIN (select mrid, dokterid, sum(jumlah) as jumlah from $tmp04 GROUP BY 1,2) as b 
        on a.srid=b.mrid and a.dokterid=b.dokterid SET a.ki=b.jumlah";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    $query = "update $tmp06 SET saldoakhir=IFNULL(saldoawal,0)+IFNULL(ki,0)-IFNULL(saldocn,0)";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


    $query = "delete from hrd.ks1_proses WHERE icabangid='$pidcabang'";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    $query = "delete from hrd.ks1_proses2 WHERE icabangid='$pidcabang'";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    $query = "delete from hrd.ks1_proses3 WHERE icabangid='$pidcabang'";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    $query = "insert into hrd.ks1_proses (icabangid, srid, dokterid, iprodid, cn, qty, tvalue, saldocn)
        select icabangid, srid, dokterid, iprodid, cn, sum(qty) as qty, sum(tvalue) as tvalue, sum(saldocn) as saldocn 
        from $tmp02 WHERE IFNULL(iprodid,'')<>'' GROUP BY 1,2,3,4,5";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    $query = "insert into hrd.ks1_proses2 (icabangid, srid, dokterid, cn) 
        select distinct icabangid, srid, dokterid, cn from $tmp02 WHERE IFNULL(iprodid,'')<>''";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    $query = "insert into hrd.ks1_proses3 (icabangid, srid, dokterid, saldoawal, saldocn, ki, saldoakhir) 
        select icabangid, srid, dokterid, saldoawal, saldocn, ki, saldoakhir from $tmp06";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }



?>

<HTML>
<head>
  <title>Berhasil Proses KS MR Cabang</title>
    <meta http-equiv="Expires" content="Mon, 01 Jan 2030 1:00:00 GMT">
    <meta http-equiv="Pragma" content="no-cache">
    <?php header("Cache-Control: no-cache, must-revalidate"); ?>
    <link rel="shortcut icon" href="images/icon.ico" />
</head>


<BODY class="nav-md">
    Cabang : <?PHP echo "$pnamacabpl &nbsp; ..."; ?> <br/>data berhasil diproses, silakan tutup halaman ini...!!!
</BODY>
</HTML>

<?PHP
    hapusdata:
        mysqli_query($cnmy, "drop TEMPORARY table $tmp01");
        mysqli_query($cnmy, "drop TEMPORARY table $tmp02");
        mysqli_query($cnmy, "drop TEMPORARY table $tmp03");
        mysqli_query($cnmy, "drop TEMPORARY table $tmp04");
        mysqli_query($cnmy, "drop TEMPORARY table $tmp05");
        mysqli_query($cnmy, "drop TEMPORARY table $tmp06");
        mysqli_close($cnmy);
}
?>
