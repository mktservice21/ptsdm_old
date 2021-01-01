<?php
    ini_set("memory_limit","512M");
    ini_set('max_execution_time', 0);
    
session_start();
$pmodule="";
if (isset($_GET['module'])) $pmodule=$_GET['module'];


if ($pmodule=="viewdatacabang") {
    $piddivisi=$_POST['udivsi'];
    
    include "../../config/koneksimysqli.php";
    $query ="select PILIHAN from dbmaster.t_divisi_gimick WHERE DIVISIID='$piddivisi' LIMIT 1";
    $tampilk=mysqli_query($cnmy, $query);
    $nr= mysqli_fetch_array($tampilk);
    $divpiliih=$nr['PILIHAN'];
    
    echo "<option value='' selected>--Pilihan--</option>";
    if (!empty($piddivisi)) {
        if ($divpiliih=="OT") {//MKT.icabang_o
            $query = "select icabangid_o as icabangid, nama as nama from dbmaster.v_icabang_o WHERE aktif='Y' ";
        }else{
            $query = "select icabangid as icabangid, nama as nama from MKT.icabang WHERE aktif='Y' ";
        }
        $query .=" ORDER BY nama";
        $tampil= mysqli_query($cnmy, $query);
        while ($row= mysqli_fetch_array($tampil)) {
            $npidcab=$row['icabangid'];
            $npnmcab=$row['nama'];

            if ($npidcab==$pcabangid)
                  echo "<option value='$npidcab' selected>$npnmcab</option>";
            else
                echo "<option value='$npidcab'>$npnmcab</option>";
        }
    }
    
    mysqli_close($cnmy);
    
}elseif ($pmodule=="viewdatadivisi") {
    $pdivisiid=$_POST['udivawal'];//divisi awal
    $ppilihanwewenang=$_POST['udivwwn'];
    
    include "../../config/koneksimysqli.php";
    
    if ($ppilihanwewenang=="AL") echo "<option value='' >--Pilihan--</option>";
    $query = "select distinct DIVISIID, DIVISINM from dbmaster.t_divisi_gimick WHERE IFNULL(STSAKTIF,'')='Y' AND IFNULL(STS,'')='M'";
    if ($ppilihanwewenang=="AL") {
    }else{
      $query .=" AND PILIHAN='$ppilihanwewenang' ";
    }
    //if ($pgetact=="editdata") $query .=" AND DIVISIID='$pdivisiid' ";
    $query .=" ORDER BY DIVISINM";
    $tampil= mysqli_query($cnmy, $query);
    while ($row= mysqli_fetch_array($tampil)) {
      $npdivid=$row['DIVISIID'];
      $npdivnm=$row['DIVISINM'];

      if ($npdivid==$pdivisiid)
            echo "<option value='$npdivid' selected>$npdivnm</option>";
      else
          echo "<option value='$npdivid'>$npdivnm</option>";
    }
    mysqli_close($cnmy);
    
}elseif ($pmodule=="viewdataarea") {
    $npdivisiid=$_POST['udivsi'];
    $pidcabang=$_POST['ucabang'];
    $pareaid="";
    
    include "../../config/koneksimysqli.php";
    
    $query = "select PILIHAN FROM dbmaster.t_divisi_gimick WHERE DIVISIID='$npdivisiid'";
    $tampild= mysqli_query($cnmy, $query);
    $rowd= mysqli_fetch_array($tampild);
    $npilih=$rowd['PILIHAN'];
    if (!empty($npilih)) $npdivisiid=$npilih;
    
    if ($npdivisiid=="OT" OR $npdivisiid=="OTC" OR $npdivisiid=="CHC") {
        $query = "select icabangid_o as icabangid, areaid_o as areaid, nama from MKT.iarea_o WHERE icabangid_o='$pidcabang' AND (aktif='Y' OR areaid_o='$pareaid') ";
    }else{
        $query = "select iCabangId as icabangid, areaId as areaid, Nama as nama from MKT.iarea WHERE icabangid='$pidcabang' AND (aktif='Y' OR areaid='$pareaid') ";
    }
    
    $query .=" ORDER BY nama";
    $tampil= mysqli_query($cnmy, $query);
    echo "<option value='' selected>--All--</option>";
    while ($row= mysqli_fetch_array($tampil)) {
        $npidarea=$row['areaid'];
        $npnmarea=$row['nama'];

        if ($npidarea==$pareaid)
              echo "<option value='$npidarea' selected>$npnmarea</option>";
        else
            echo "<option value='$npidarea'>$npnmarea</option>";
    }
    
    mysqli_close($cnmy);
                                                    
}elseif ($pmodule=="viewdataareanama") {
    $npiid=$_POST['uid'];
    $npdivisiid=$_POST['udivsi'];
    $pidcabang=$_POST['ucabang'];
    $pareaid=$_POST['uarea'];
    include "../../config/koneksimysqli.php";
    
    if ($npdivisiid=="OT" OR $npdivisiid=="OTC" OR $npdivisiid=="CHC") {
        $querycab ="select * from dbmaster.t_barang_penerima WHERE IGROUP<>'$npiid' AND ICABANGID_O='$pidcabang' AND IFNULL(AREAID_O,'')='$pareaid' AND IFNULL(AKTIF,'')='Y'";
        $ketemucab=mysqli_num_rows(mysqli_query($cnmy, $querycab));
        if ((DOUBLE)$ketemucab>0) {
            mysqli_close($cnmy);
            echo "sudahada";
            exit;
        }
        
        $query = "select icabangid_o, areaid_o, nama from MKT.iarea_o WHERE icabangid_o='$pidcabang' AND areaid_o='$pareaid' ";
    }else{
        $querycab ="select * from dbmaster.t_barang_penerima WHERE IGROUP<>'$npiid' AND ICABANGID='$pidcabang' AND IFNULL(AREAID,'')='$pareaid' AND IFNULL(AKTIF,'')='Y'";
        $ketemucab=mysqli_num_rows(mysqli_query($cnmy, $querycab));
        if ((DOUBLE)$ketemucab>0) {
            mysqli_close($cnmy);
            echo "sudahada";
            exit;
        }
        
        $query = "select icabangid, areaid, nama from MKT.iarea WHERE icabangid='$pidcabang' AND areaid='$pareaid'";
    }
    $tampil= mysqli_query($cnmy, $query);
    $row= mysqli_fetch_array($tampil);
    $npnmarea=$row['nama'];
    
    mysqli_close($cnmy);
    
    echo $npnmarea;
}elseif ($pmodule=="cekdataposting") {
    
    $ptgl=$_POST['utgl'];
    $piddivisi=$_POST['udivisi'];
    $pidinput=$_POST['uidinput'];
    
    $pbulan= date("Ym", strtotime($ptgl));
    
    include "../../config/koneksimysqli.php";
    $bolehinput="boleh";
    
    $pdivpilih="ET";
    if ($piddivisi=="OTC") $pdivpilih="OT";
    
    $query = "SELECT * FROM dbmaster.t_barang_opname WHERE DATE_FORMAT(BULAN,'%Y%m')='$pbulan' AND PILIHAN='$pdivpilih'";
    $tampilkan=mysqli_query($cnmy, $query);
    $ketemukan=mysqli_num_rows($tampilkan);
    if ($ketemukan>0) {
        $bolehinput="Bulan yang dipilih sudah closing/posting stcok, pilih bulan selanjutnya...!!!";
    }
    
    mysqli_close($cnmy);
    
    echo $bolehinput;
}

?>