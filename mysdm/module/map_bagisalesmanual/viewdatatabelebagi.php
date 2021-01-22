<?php
    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","512M");
    ini_set('max_execution_time', 0);
    
    session_start();
    
    
    $puserid="";
    if (isset($_SESSION['USERID'])) $puserid=$_SESSION['USERID'];

    if (empty($puserid)) {
        echo "ANDA HARUS LOGIN ULANG...";
        exit;
    }
    
    
    $piddist=$_POST['udistid'];
    $pidecab=$_POST['ucabid'];
    $pnmfilter=$_POST['unamafilter'];
    $pbln=$_POST['ubln'];
    $pbulan = date('Y-m', strtotime($pbln));
    
    
    $_SESSION['MAPCUSTBAGIDCAB']=$piddist;
    $_SESSION['MAPCUSTBAGIIDARE']=$pidecab;
    $_SESSION['MAPCUSTBAGIFILTE']=$pnmfilter;
    $_SESSION['MAPCUSTBAGIBULAN']=$pbln;
    
    
    /*
SELECT SUM(qbeli) qbeli, salespv.cabangid, brgid, custid, tgljual, harga, fakturid, iprodid, eproduk.iprodid, nama nmprod 
FROM MKT.salespv as salespv JOIN MKT.eproduk ON salespv.brgid=eproduk.eprodid  WHERE salespv.cabangid='27' AND LEFT(tgljual,7)='2021-01' AND fakturid='400121210000247' AND eproduk.distid='0000000005' GROUP BY iprodid ORDER BY nmprod;

select b.* from MKT.msales0 as a LEFT JOIN MKT.msales1 as b on a.nomsales=b.nomsales WHERE a.distid='0000000005' and a.ecabangid='27' and a.fakturid='400121210000247' AND left(tgl,7)='2021-01'

select * from MKT.msales0 where left(tgl,7)='2021-01' order by fakturid
     */
    include "../../config/koneksimysqli_ms.php";
    
    $query = "SELECT distid, nama, sls_data, initial FROM MKT.distrib0 WHERE distid='$piddist'";
    $tampil=mysqli_query($cnms, $query);
    $row=mysqli_fetch_array($tampil);
    $pnamadist=$row['nama'];
    $pnmtblsales=$row['sls_data'];
    
    $query = "SELECT nama FROM MKT.ecabang WHERE distid='$piddist' AND ecabangid='$pidecab'";
    $tampil=mysqli_query($cnms, $query);
    $row=mysqli_fetch_array($tampil);
    $pnamaecabang=$row['nama'];
    
    $query = "SELECT DISTINCT a.custid FROM MKT.$pnmtblsales as a WHERE a.cabangid='$pidecab' AND a.fakturid='$pnmfilter' AND LEFT(a.tgljual,7)='$pbulan'";
    $tampil=mysqli_query($cnms, $query);
    $row=mysqli_fetch_array($tampil);
    $pecusid=$row['custid'];
    
    $query = "SELECT nama, icabangid, areaid, icustid FROM MKT.ecust WHERE distid='$piddist' AND cabangid='$pidecab' AND ecustid='$pecusid'";
    $tampil=mysqli_query($cnms, $query);
    $row=mysqli_fetch_array($tampil);
    $pnmecust=$row['nama'];
    $pidcabang=$row['icabangid'];
    $pidarea=$row['areaid'];
    $picusid=$row['icustid'];
    
    $query = "SELECT nama FROM MKT.icust WHERE icabangid='$pidcabang' AND areaid='$pidarea' AND icustid='$picusid'";
    $tampil=mysqli_query($cnms, $query);
    $row=mysqli_fetch_array($tampil);
    $pnmicust=$row['nama'];
    
    $query = "SELECT icabang.nama as nmcab, iarea.nama as nmarea FROM MKT.icabang JOIN MKT.iarea ON icabang.icabangid=iarea.icabangid WHERE icabang.icabangid='$pidcabang' AND iarea.areaid='$pidarea'";
    $tampil=mysqli_query($cnms, $query);
    $row=mysqli_fetch_array($tampil);
    $pnmcabang=$row['nmcab'];
    $pnmarea=$row['nmarea'];
    
    
    $query = "SELECT a.cabangid, a.brgid, a.custid, a.tgljual, a.harga, a.fakturid, "
            . " e.iprodid, e.nama as nmprod, SUM(a.qbeli) qbeli "
            . " FROM MKT.$pnmtblsales as a "
            . " JOIN MKT.eproduk as e ON a.brgid=e.eprodid  WHERE a.cabangid='$pidecab' "
            . " AND LEFT(tgljual,7)='$pbulan' AND a.fakturid='$pnmfilter' AND e.distid='$piddist' "
            . " GROUP BY 1,2,3,4,5,6,7,8 ORDER BY nmprod";
    
    //echo "$query";
    
    //echo "$pnamadist, $pnmtblsales, $pnamaecabang, $pecusid - $pnmecust - $pidcabang ($pnmcabang) - $pidarea ($pnmarea), $picusid - $pnmicust";
?>


<?PHP
hapusdata:
    mysqli_close($cnms);
?>
