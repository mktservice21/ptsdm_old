<?php
session_start();

$pmodule="";
if (isset($_GET['module'])) $pmodule=$_GET['module'];


if ($pmodule=="viewdivisibytipe"){
    
    $fkaryawan=$_SESSION['IDCARD'];
    $fstsadmin=$_SESSION['STSADMIN'];
    $flvlposisi=$_SESSION['LVLPOSISI'];
    $fdivisi=$_SESSION['DIVISI'];
    $fgroupid=$_SESSION['GROUP'];
    
    include "../../../config/koneksimysqli.php";
    
    $prpttipe=$_POST['utipe'];
    
    if ($prpttipe=="BMB") {
        echo "<option value='ETH' selected>ETHICAL</option>";
        echo "<option value='OTC'>CHC</option>";
        echo "<option value='HO'>HO</option>";
        echo "<option value='EAGLE'>EAGLE</option>";
        echo "<option value='PEACO'>PEACOCK</option>";
        echo "<option value='PIGEO'>PIGEON</option>";
    }else{
        
        $ppilihdivisi="";
        if ($fkaryawan=="0000000148") $ppilihdivisi = "HO";
        elseif ($fkaryawan=="0000001043xx") $ppilihdivisi = "EAGLE";
        else{
            if ($fdivisi=="OTC" OR $fdivisi=="CHC") {
                $ppilihdivisi="OTC";
            }
        }
    
        $pbukasemua=false;
        $pbukdivisiall=false;
        $pnot_otc=false;
        if ($fgroupid=="1" OR $fgroupid=="25" OR $fgroupid=="24" OR $fgroupid=="2" OR $fgroupid=="22" OR $fgroupid=="46" OR $fgroupid=="50") {
            $pbukasemua=true;
            $pbukdivisiall=true;
        }


        if (empty($ppilihdivisi)) {
            $pbukdivisiall=true;
        }

        if ($fgroupid=="28") {
            $pnot_otc=true;
        }

        if ($fkaryawan=="0000001043") $pbukdivisiall=true;
    
        $query = "select DivProdId from MKT.divprod WHERE br='Y' AND DivProdId<>'OTHER' ";
        if ($pbukdivisiall==false) {
            $query .=" AND DivProdId='$ppilihdivisi' ";
        }

        if ($pnot_otc == true) {
            $query .=" AND DivProdId NOT IN ('CHC', 'OTC') ";
        }
        $query .=" order by DivProdId";
        $tampil = mysqli_query($cnmy, $query);
        if ($pbukdivisiall==true) echo "<option value='' selected>All</option>";
        while ($z= mysqli_fetch_array($tampil)) {
            $pgetdivisi=$z['DivProdId'];

            $pdivisinm=$pgetdivisi;
            if ($pgetdivisi=="CAN") $pdivisinm="CANARY";
            if ($pgetdivisi=="PIGEO") $pdivisinm="PIGEON";
            if ($pgetdivisi=="PEACO") $pdivisinm="PEACOCK";
            if ($pgetdivisi=="OTC") $pdivisinm="CHC";

            if ($pgetdivisi==$ppilihdivisi)
                echo "<option value='$pgetdivisi' selected>$pdivisinm</option>";
            else
                echo "<option value='$pgetdivisi'>$pdivisinm</option>";
        }

        if ($fgroupid=="1" OR $fgroupid=="24" OR $fgroupid=="61" OR $fgroupid=="28" OR $fgroupid=="25") {
            echo "<option value='ETH'>ETHICAL</option>";
        }
        
    }
    
    
    mysqli_close($cnmy);
    
    
}elseif ($pmodule=="viewcoadivisichk"){
    $fgroupid=$_SESSION['GROUP'];
    
    include "../../../config/koneksimysqli.php";
    $mydivisi = $_POST['udivi'];
    $fil = " AND c.DIVISI2 = '$mydivisi'";
    
    if ($fgroupid=="28" OR $fgroupid=="61") {
        if (empty($mydivisi)) {
            $fil = " AND c.DIVISI2 NOT IN ('CHC', 'OTC')";
        }
    }else{
        if (empty($mydivisi)) $fil = " ";
    }
    
    if ($mydivisi=="ETH") {
        //$fil = " AND c.DIVISI2 NOT IN ('CHC', 'OTC')";
    }
    
    $query = "select a.COA4, a.NAMA4 from dbmaster.coa_level4 a 
        LEFT JOIN dbmaster.coa_level3 b on a.COA3=b.COA3
        LEFT JOIN dbmaster.coa_level2 c on b.COA2=c.COA2
        WHERE 1=1 $fil ";
    $query .= " ORDER BY a.COA4";
    $tampil = mysqli_query($cnmy, $query);
    echo "&nbsp; <input type=checkbox value='' name='chkbox_coa[]' checked> empty<br/>";
    while ($z= mysqli_fetch_array($tampil)) {
      $pcoa4=$z['COA4'];
      $pnmcoa4=$z['NAMA4'];
      echo "&nbsp; <input type=checkbox value='$pcoa4' name='chkbox_coa[]' checked> $pcoa4 - $pnmcoa4<br/>";
    }
    
    
    if ($mydivisi=="OTC" OR $mydivisi=="CHC") {
        echo "<br/>";
        $query = "select a.COA4, a.NAMA4 from dbmaster.coa_level4 a 
            LEFT JOIN dbmaster.coa_level3 b on a.COA3=b.COA3
            LEFT JOIN dbmaster.coa_level2 c on b.COA2=c.COA2
            WHERE IFNULL(c.DIVISI2,'')='HO'";
        $query .= " ORDER BY a.COA4";
        $tampil_ = mysqli_query($cnmy, $query);
        while ($za= mysqli_fetch_array($tampil_)) {
          $pcoa4_=$za['COA4'];
          $pnmcoa4_=$za['NAMA4'];
          echo "&nbsp; <input type=checkbox value='$pcoa4_' name='chkbox_coa[]' checked> $pcoa4_ - $pnmcoa4_ (HO)<br/>";
        }
    }
    
    
    
    if (!empty($mydivisi)) {
        echo "<br/>";
        $query = "select a.COA4, a.NAMA4 from dbmaster.coa_level4 a 
            LEFT JOIN dbmaster.coa_level3 b on a.COA3=b.COA3
            LEFT JOIN dbmaster.coa_level2 c on b.COA2=c.COA2
            WHERE IFNULL(c.DIVISI2,'')='' OR IFNULL(c.DIVISI2,'')='OTHER' OR IFNULL(c.DIVISI2,'')='OTHERS' ";
        $query .= " ORDER BY a.COA4";
        $tampil_ = mysqli_query($cnmy, $query);
        while ($za= mysqli_fetch_array($tampil_)) {
          $pcoa4_=$za['COA4'];
          $pnmcoa4_=$za['NAMA4'];
          echo "&nbsp; <input type=checkbox value='$pcoa4_' name='chkbox_coa[]' checked> $pcoa4_ - $pnmcoa4_ (OTHER)<br/>";
        }
    }
    
    
}