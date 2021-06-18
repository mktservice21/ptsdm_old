<?php
    $bulan_array=array(1=> "Januari", "Februari", "Maret", "April", "Mei", 
        "Juni", "Juli", "Agustus", "September", 
        "Oktober", "November", "Desember");

    $hari_array = array(
        'Minggu',
        'Senin',
        'Selasa',
        'Rabu',
        'Kamis',
        'Jumat',
        'Sabtu'
    );
    
$pidmodule=$_GET['module'];
$pidmenu=$_GET['idmenu'];
$pidact=$_GET['act'];

$piduser=$_SESSION['USERID']; 
$pidcard=$_SESSION['IDCARD'];
$pidjbt=$_SESSION['JABATANID']; 
$pidgroup=$_SESSION['GROUP']; 
$pnamalengkap=$_SESSION['NAMALENGKAP'];


$pidinput="";

$act="update";

include "config/fungsi_ubahget_id.php";

$pidinput_ec=$_GET['id'];
$pidinput = decodeString($pidinput_ec);
$pnewdate=$_GET['nid'];
$pidcard=$pidinput;

$platitude="";
$plongitude="";
        
$query = "SELECT a.*, b.namalengkap as nama_dokter FROM hrd.dkd_new1 as a "
        . " JOIN dr.masterdokter as b on a.dokterid=b.id "
        . " WHERE a.nourut='$pidinput'";
$tampil= mysqli_query($cnmy, $query);
$row0= mysqli_fetch_array($tampil);

$nkryid=$row0['karyawanid'];
$ntgl=$row0['tanggal'];
$ptanggal=$ntgl;
$pdokterid=$row0['dokterid'];
$pdokternm=$row0['nama_dokter'];
$pdokter=$pdokternm." - ".$pdokterid;


$ntanggal = date('l d F Y', strtotime($ntgl));

$xhari = $hari_array[(INT)date('w', strtotime($ntgl))];
$xtgl= date('d', strtotime($ntgl));
$xbulan = $bulan_array[(INT)date('m', strtotime($ntgl))];
$xthn= date('Y', strtotime($ntgl));

$ptglhari="$xhari, $xtgl $xbulan $xthn";

?>



<script> window.onload = function() { document.getElementById("e_id").focus(); } </script>

<div class="">
    
    
    <!--row-->
    <div class="row">
        
    
        <div class='col-md-12 col-sm-12 col-xs-12'>
            
            <div class='x_panel'>
                
                <form method='POST' action='<?PHP echo "$aksi?module=$pidmodule&act=input&idmenu=$pidmenu"; ?>' 
                      id='d-form2' name='form2' data-parsley-validate class='form-horizontal form-label-left'  enctype='multipart/form-data'>
                    
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
                                        <input type='text' id='e_id' name='e_id' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidinput; ?>' Readonly>
                                        <input type='hidden' id='e_idinputuser' name='e_idinputuser' class='form-control col-md-7 col-xs-12' value='<?PHP echo $piduser; ?>' Readonly>
                                        <input type='hidden' id='e_idcarduser' name='e_idcarduser' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidcard; ?>' Readonly>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Tanggal <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input type='hidden' id='e_tanggal' name='e_tanggal' class='form-control col-md-7 col-xs-12' value='<?PHP echo $ptanggal; ?>' Readonly>
                                        <input type='text' id='e_tgl' name='e_tgl' class='form-control col-md-7 col-xs-12' value='<?PHP echo $ptglhari; ?>' Readonly>
                                    </div>
                                </div>
                                
                                <div hidden class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Karyawan <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input type='text' id='e_kryid' name='e_kryid' class='form-control col-md-7 col-xs-12' value='<?PHP echo $nkryid; ?>' Readonly>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>User <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input type='hidden' id='e_doktid' name='e_doktid' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pdokterid; ?>' Readonly>
                                        <input type='text' id='e_userdokt' name='e_userdokt' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pdokter; ?>' Readonly>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''><u>Lokasi</u> <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <p id="d_lokasi"></p>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Latitude <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input type='text' id='e_latitude' name='e_latitude' class='form-control col-md-7 col-xs-12' value='<?PHP echo $platitude; ?>' Readonly>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Longitude <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input type='text' id='e_longitude' name='e_longitude' class='form-control col-md-7 col-xs-12' value='<?PHP echo $plongitude; ?>' Readonly>
                                    </div>
                                </div>
                                
                            </div>
                            
                                
                                
                        </div>
                    </div>
                    
                    <div class='col-md-12 col-sm-12 col-xs-12'>
                        <h2>
                            <?PHP
                                echo "<div class='col-sm-5'>";
                                include "module/dkd/dkd_ttddkdvisit/ttd_dkdvisit.php";
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


<script>
    $(document).ready(function() {
        getLocation();
    } );
    
    var x = document.getElementById("d_lokasi");

    function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(showPosition);
        } else { 
            x.innerHTML = "Geolocation is not supported by this browser.";
        }
    }

    function showPosition(position) {
        document.getElementById("e_latitude").value=position.coords.latitude;
        document.getElementById("e_longitude").value=position.coords.longitude;
    }
</script>

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