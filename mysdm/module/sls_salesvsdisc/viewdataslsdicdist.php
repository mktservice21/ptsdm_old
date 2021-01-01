<?php

session_start();
$pmodule="";
if (isset($_GET['module'])) $pmodule=$_GET['module'];

$fkaryawan=$_SESSION['IDCARD'];
$pmyjabatanid=$_SESSION['JABATANID'];
$pgroupid=$_SESSION['GROUP'];
$puserid=$_SESSION['USERID'];


if ($pmodule=="caridataregion") {
    
    $pidivisi=$_POST['udivisi'];
    $pidregi=$_POST['uregion'];
    $pselall="selected";
    $pselbrt="";
    $pseltmr="";
    
    if ($pidivisi=="OTC" OR $pidivisi=="CHC") {
    }else{
        if ($pidregi=="B") {
            $pselall="";
            $pselbrt="selected";
            $pseltmr="";
        }elseif ($pidregi=="T") {
            $pselall="";
            $pselbrt="selected";
            $pseltmr="";
        }
    }
    
    if ($pgroupid=="43" OR $pgroupid=="40") {//ahmad dan titik 
        if ($puserid=="144") {
            echo "<option value='T' selected>Timur</option>";
        }else{
            if ($pidivisi=="OTC" OR $pidivisi=="CHC") {
                echo "<option value='BB' selected>Barat & All CHC</option>";
                echo "<option value='B'>Barat</option>";
            }else{
                echo "<option value='BB'>Barat & All CHC</option>";
                echo "<option value='B' selected>Barat</option>";
            }
        }
    }else{
        if ($pidivisi=="OTC" OR $pidivisi=="CHC") {
            echo "<option value='' $pselall>--All--</option>";
        }else{
            echo "<option value='' $pselall>--All--</option>";
            echo "<option value='B' $pselbrt>Barat</option>";
            echo "<option value='T' $pseltmr>Timur</option>";
        }
    }
}

?>

