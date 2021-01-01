<?php
session_start();

$pmodule="";
if (isset($_GET['module'])) $pmodule=$_GET['module'];

if ($pmodule=="viewcoadivisichk"){
    $fgroupid=$_SESSION['GROUP'];
    
    include "../../../config/koneksimysqli.php";
    $mydivisi = $_POST['udivi'];
    
    $fil = " AND c.DIVISI2 = '$mydivisi'";
    
    if ($fgroupid=="28") {
        if (empty($mydivisi)) {
            $fil = " AND c.DIVISI2 NOT IN ('CHC', 'OTC')";
        }
    }else{
        if (empty($mydivisi)) $fil = " ";
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
            WHERE IFNULL(c.DIVISI2,'')='HO' ";
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