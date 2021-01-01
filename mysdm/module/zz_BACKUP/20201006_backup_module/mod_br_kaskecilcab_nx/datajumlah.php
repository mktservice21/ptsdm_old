<script src="js/inputmask.js"></script>
<?php
session_start();
$pidgroup=$_SESSION['GROUP'];
$ptxthidden="hidden";
if ($pidgroup=="40" OR $pidgroup=="23" OR $pidgroup=="26" OR $pidgroup=="1") $ptxthidden="";


$pmodule="";
if (isset($_GET['module'])) $pmodule=$_GET['module'];

    $pidkar=$_POST['ukry'];
    $pidcabang=$_POST['ucab'];
    $pidpengajuan=$_POST['uuntuk'];
    
    
    include "../../config/koneksimysqli.php";
    
    
    $prpjumlah=0;
    $prppc=0;
    $prpsaldo=0;
    $prpots=0;

    $prpsldawal=0;
    $prptambah=0;
    
    $query = "select * from dbmaster.t_uangmuka_kascabang WHERE icabangid='$pidcabang'";
    $tampilp= mysqli_query($cnmy, $query);
    $pr= mysqli_fetch_array($tampilp);
    $prpjumlah=$pr['jumlah'];
    if (empty($prpjumlah)) $prpjumlah=0;
    $prppc=$pr['pcm'];
    if (empty($prppc)) $prppc=0;
    $prpsldawal=$pr['saldoawal'];
    if (empty($prpsldawal)) $prpsldawal=0;
    $prptambah=$pr['jmltambahan'];
    if (empty($prptambah)) $prptambah=0;
    
    
    $query = "select * from dbmaster.t_outstanding_kaskecilcab WHERE icabangid='$pidcabang' AND pengajuan='$pidpengajuan'";
    $tampilp= mysqli_query($cnmy, $query);
    $pr= mysqli_fetch_array($tampilp);
    $prpots=$pr['jmlsisa'];
    if (empty($prpots)) $prpots=0;
    
    
?>


<div hidden class='form-group'>
    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
        <span style="color:red;">Total Petty Cash</span>
    </label>
    <div class='col-md-3'>
        <input type='text' id='e_pcrp' name='e_pcrp' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $prpjumlah; ?>' Readonly>
    </div>
</div>

<div class='form-group'>
    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
        <span style="color:red;">PC-M</span>
    </label>
    <div class='col-md-3'>
        <input type='text' id='e_rppc' name='e_rppc' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $prppc; ?>' Readonly>
    </div>
</div>

<div hidden class='form-group'>
    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
        <span style="color:red;">Tambahan Rp.</span>
    </label>
    <div class='col-md-3'>
        <input type='text' id='e_tambahanrp' name='e_tambahanrp' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $prptambah; ?>' Readonly>
    </div>
</div>

<div <?PHP echo $ptxthidden; ?> class='form-group'>
    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
        <span style="color:red;">Saldo Awal Rp.</span>
    </label>
    <div class='col-md-3'>
        <input type='text' id='e_sldawal' name='e_sldawal' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $prpsldawal; ?>' Readonly>
    </div>
</div>


<div class='form-group'>
    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
        <span style="color:red;">Outstanding</span>
    </label>
    <div class='col-md-3'>
        <input type='text' id='e_otsrp' name='e_otsrp' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $prpots; ?>' Readonly>
    </div>
</div>


<?PHP
mysqli_close($cnmy);
?>