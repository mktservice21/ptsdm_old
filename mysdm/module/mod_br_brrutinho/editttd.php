<?php

$pidmodule=$_GET['module'];
$pidmenu=$_GET['idmenu'];
$pidact=$_GET['act'];
$pstsmobile=$_SESSION['MOBILE'];

$piduser=$_SESSION['USERID']; 
$pidcard=$_SESSION['IDCARD']; 

$hari_ini = date("Y-m-d");
//$pbln = date('F Y', strtotime($hari_ini));
$pbln = date('F Y', strtotime('-1 month', strtotime($hari_ini)));

$mytglini="";
$mytglini = getfield("select CURRENT_DATE as lcfields");
if ($mytglini==0) $mytglini="";
if (!empty($mytglini)) $hari_ini = date('Y-m-d', strtotime($mytglini));
$iniharinya=date('d', strtotime($mytglini));

$tglhariini = getfield("select DATE_FORMAT(CURRENT_DATE(),'%d') as lcfields ");
if ($tglhariini=="0") $tglhariini="";
if (empty($tglhariini)) $tglhariini = date("d");

//$ptgl1 = date('01/m/Y', strtotime($hari_ini));
//$ptgl2 = date('t/m/Y', strtotime($hari_ini));
$ptgl1 = date('01/m/Y', strtotime('-1 month', strtotime($hari_ini)));
$ptgl2 = date('t/m/Y', strtotime('-1 month', strtotime($hari_ini)));

$pidrutin="";
$pkaryawanid=$_SESSION['IDCARD'];
$pkaryawannm=$_SESSION['NAMALENGKAP'];

$pkdperiode="";
$pselper0="";
$pselper1="";
$pselper2="selected";

$pketerangan="";

$ptotalsemua=0;

$pdivisi="HO";
$pjabatanid=$_SESSION['JABATANID'];

$pidcabang="0000000001";//ETH HO
$pidarea="0000000001";//ETH HO


if ($pidact=="editttddata"){
    $act="ttdupdate";
    $pidrutin=$_GET['id'];
    
    $edit = mysqli_query($cnmy, "SELECT * FROM dbmaster.t_brrutin0 WHERE kode=1 AND idrutin='$pidrutin' AND karyawanid='$pidcard'");
    $r    = mysqli_fetch_array($edit);
    
    $pbln = date('F Y', strtotime($r['bulan']));
    $ptgl1 = date('d/m/Y', strtotime($r['periode1']));
    $ptgl2 = date('d/m/Y', strtotime($r['periode2']));
        
    $pdivisi=$r['divisi'];
    $pjabatanid=$r['jabatanid'];
    $pidcabang=$r['icabangid'];
    $pidarea=$r['areaid'];
    $pidnopol=$r['nopol'];
    
    $pkaryawanid=$r['karyawanid'];
    $pkaryawannm = getfield("select nama as lcfields from hrd.karyawan where karyawanid='$pkaryawanid'");
    
    $pketerangan=$r['keterangan'];
    $ptotalsemua=$r['jumlah'];
    
    
}

?>

<script> window.onload = function() { document.getElementById("e_id").focus(); } </script>

<div class="">
    
    
    <!--row-->
    <div class="row">
        
    
        <div class='col-md-12 col-sm-12 col-xs-12'>
            
            <div class='x_panel'>
                
                <form method='POST' action='<?PHP echo "$aksi?module=$pidmodule&act=input&idmenu=$pidmenu"; ?>' 
                      id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left'  enctype='multipart/form-data'>
                    
                    <div class='col-md-12 col-sm-12 col-xs-12'>
                        <h2>
                            <a class='btn btn-default' href="<?PHP echo "?module=$pidmodule&idmenu=$pidmenu&act=$pidmenu"; ?>">Back</a>
                        </h2>
                        <div class='clearfix'></div>
                    </div>
                    
                    
                    <div class='x_panel'>
                        <div class='x_content'>
                            
                            
                            <div class='col-md-12 col-sm-12 col-xs-12'>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>ID <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input type='text' id='e_id' name='e_id' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidrutin; ?>' Readonly>
                                        <input type='hidden' id='e_idinputuser' name='e_idinputuser' class='form-control col-md-7 col-xs-12' value='<?PHP echo $piduser; ?>' Readonly>
                                        <input type='hidden' id='e_idcarduser' name='e_idcarduser' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidcard; ?>' Readonly>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Yang Membuat <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input type='hidden' id='e_divisiid' name='e_divisiid' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pdivisi; ?>' Readonly>
                                        <input type='hidden' id='e_jabatanid' name='e_jabatanid' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pjabatanid; ?>' Readonly>
                                        <input type='hidden' id='e_cabangid' name='e_cabangid' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidcabang; ?>' Readonly>
                                        <input type='hidden' id='e_areaid' name='e_areaid' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidarea; ?>' Readonly>
                                        <input type='hidden' id='e_nopolid' name='e_nopolid' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidnopol; ?>' Readonly>
                                        <input type='hidden' id='e_idkaryawan' name='e_idkaryawan' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pkaryawanid; ?>' Readonly>
                                        <input type='text' id='e_nmkaryawan' name='e_nmkaryawan' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pkaryawannm; ?>' Readonly>
                                    </div>
                                </div>
                                
                                
                               <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Bulan </label>
                                    <div class='col-md-3'>
                                        <div class='input-group date' id='thnbln01x'>
                                            <input type='text' class='form-control' id='e_bulan' name='e_bulan' autocomplete='off' value='<?PHP echo $pbln; ?>' readonly />
                                            <span class='input-group-addon'>
                                                <span class='glyphicon glyphicon-calendar'></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Keterangan <span class='required'></span></label>
                                    <div class='col-md-6 col-sm-6 col-xs-12'>
                                        <textarea readonly class='form-control' id='e_ket' name='e_ket' rows='3' placeholder='Aktivitas'><?PHP echo $pketerangan; ?></textarea>
                                    </div><!--disabled='disabled'-->
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Total Rp. <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input type='text' id='e_totalsemua' name='e_totalsemua' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $ptotalsemua; ?>' readonly>
                                    </div>
                                </div>
                                
                                
                                
                                
                            </div>
                            
                                
                                
                        </div>
                    </div>
                    
                    <div class='col-md-12 col-sm-12 col-xs-12'>
                        <h2>
                            <?PHP
                                echo "<div class='col-sm-5'>";
                                include "module/mod_br_brrutinho/ttd_brrutinho_edit.php";
                                echo "</div>";
                                
                            ?>
                        </h2>
                        <div class='clearfix'></div>
                    </div>
                    
                    
                </form>
                
            </div>
            
        </div>
        
    </div>
    
    
</div>

<link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />
<link href="css/stylenew.css" rel="stylesheet" type="text/css" />

<style>
    .ui-datepicker-calendar {
        display: none;
    }
    
    .divnone {
        display: none;
    }
    #datatableuc th {
        font-size: 12px;
    }
    #datatableuc td { 
        font-size: 12px;
        padding: 3px;
        margin: 1px;
    }
</style>


