<?php

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
        if ($divpiliih=="OT") {
            $query = "select icabangid_o icabangid, nama from MKT.icabang_o WHERE aktif='Y' ";
        }else{
            $query = "select icabangid, nama from MKT.icabang WHERE aktif='Y' ";
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
    
    
}elseif ($pmodule=="viewdatasupplier") {
    $psupawalid=$_POST['usupawal'];//divisi awal
    $ppilihanwewenang=$_POST['udivwwn'];
    
    include "../../config/koneksimysqli.php";
    
    echo "<option value='' >--Pilihan--</option>";
    $query = "select KDSUPP, NAMA_SUP from dbmaster.t_supplier WHERE AKTIF='Y' ";
    $query .=" ORDER BY NAMA_SUP";
    $tampil= mysqli_query($cnmy, $query);
    while ($row= mysqli_fetch_array($tampil)) {
      $npsupid=$row['KDSUPP'];
      $npsupnm=$row['NAMA_SUP'];

      if ($npsupid==$psupawalid)
            echo "<option value='$npsupid' selected>$npsupnm</option>";
      else
          echo "<option value='$npsupid'>$npsupnm</option>";
    }
    mysqli_close($cnmy);
    
}

?>