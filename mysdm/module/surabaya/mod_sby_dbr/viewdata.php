<?php
session_start();
$module="";
if (isset($_GET['module'])) $module=$_GET['module'];
    
if ($module=="viewdatajmltrans") {
    include "../../../config/koneksimysqli.php";
    ?>
    <script src="js/inputmask.js"></script>
    <?PHP
    $tgl1="";
    $pjumlah=0;
    
    $pidbr=$_POST['ubrid'];
    $pdivisipilih=$_POST['udivpilih'];
    
    $njml=$_POST['ujml'];
    
    $warna1=" style='color:black;' ";
    $warna2=" style='color:blue;' ";
    $warna=$warna2;
    $nwarna1=false;
    
    $nm_tabel_pilih=" dbmaster.t_br0_via_sby ";
    if ($pdivisipilih=="OTC") $nm_tabel_pilih=" dbmaster.t_br_otc_via_sby ";
    if ($pdivisipilih=="KD") $nm_tabel_pilih=" dbmaster.t_klaim_via_sby ";
        
    $nlimit=0;
    
    for($ix=1;$ix<=$njml;$ix++) {
        
        $sql = "SELECT * FROM $nm_tabel_pilih WHERE bridinput='$pidbr' order by tgltermin, tgltransfersby, jumlah LIMIT $nlimit, 1";
        $query=mysqli_query($cnmy, $sql);
        $row=mysqli_fetch_array($query);
        
        $ni_tgltermin=$row['tgltermin'];
        $ni_tgltrans = $row["tgltransfersby"];
        $ni_jumlah = $row["jumlah"];
        $ni_nobukti = $row["nobukti"];
        
    ?>
        <div class='form-group'>
            <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' <?PHP echo $warna; ?>>Tgl. Termin <?PHP echo $ix; ?> <span class='required'></span></label>
            <div class='col-md-4'>
                <input type='date' id='e_tgltermin[<?PHP echo $ix; ?>]' name='e_tgltermin[<?PHP echo $ix; ?>]' class='form-control col-md-7 col-xs-12' value='<?PHP echo $ni_tgltermin; ?>'>
            </div>
        </div>


        <div class='form-group'>
            <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' <?PHP echo $warna; ?>>Tgl. Transfer <?PHP echo $ix; ?> <span class='required'></span></label>
            <div class='col-md-4'>
                <input type='date' id='e_tgltrans[<?PHP echo $ix; ?>]' name='e_tgltrans[<?PHP echo $ix; ?>]' class='form-control col-md-7 col-xs-12' value='<?PHP echo $ni_tgltrans; ?>'>
            </div>
        </div>

        <div class='form-group'>
            <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' <?PHP echo $warna; ?>>Jumlah <?PHP echo $ix; ?> <span class='required'></span></label>
            <div class='col-md-4'>
                <input type='text' id='e_jumlah[<?PHP echo $ix; ?>]' name='e_jumlah[<?PHP echo $ix; ?>]' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $ni_jumlah; ?>'>
            </div>
        </div>

        <div class='form-group'>
            <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' <?PHP echo $warna; ?>>No Bukti <?PHP echo $ix; ?> <span class='required'></span></label>
            <div class='col-md-4'>
                <input type='text' id='e_nobukti[<?PHP echo $ix; ?>]' name='e_nobukti[<?PHP echo $ix; ?>]' autocomplete='off' class='form-control col-md-7 col-xs-12' value='<?PHP echo $ni_nobukti; ?>'>
            </div>
        </div>
    
    <?PHP
        if ($nwarna1==false){ $warna=$warna1; $nwarna1=true; }
        else { $warna=$warna2; $nwarna1=false; }
        
        $nlimit++;
    }
    
    ?>
    <script src="vendors/jquery.inputmask/dist/min/jquery.inputmask.bundle.min.js"></script>
    <?PHP
    mysqli_close($cnmy);
    
}elseif ($module=="xxx") {
    
}
    
?>

