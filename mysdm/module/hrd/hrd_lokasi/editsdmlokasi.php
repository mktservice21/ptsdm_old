<?php

include "config/fungsi_ubahget_id.php";

$pidmodule=$_GET['module'];
$pidmenu=$_GET['idmenu'];
$pidact=$_GET['act'];
$pstsmobile=$_SESSION['MOBILE'];


$piduser=$_SESSION['USERID'];
$pidcard=$_SESSION['IDCARD'];
$pidgroup=$_SESSION['GROUP'];

$aksi="";
$pidrutin="";
$pidsts="";
$pidjkt="";

$pl_lat="";
$pl_long="";
$pl_radius="";

$act="inputsdmlokasi";
if ($pidact=="editdatalsdm"){
    $act="updatesmlokasi";
    
    $pidinput_ec=$_GET['id'];
    $pidinput_ec02=$_GET['s'];
    $pidinput_ec03=$_GET['n']; //HO = sdmholok / CAB = sdmcablok
    
    $pidrutin = decodeString($pidinput_ec);
    $pidsts = decodeString($pidinput_ec02); // HO1, ....
    $pidjkt = decodeString($pidinput_ec03); //HO = sdmholok / CAB = sdmcablok
    
    $query = "select * from hrd.sdm_lokasi WHERE id='$pidrutin' AND id_status='$pidsts'";
    $edit= mysqli_query($cnmy, $query);
    
    $pketemu    = mysqli_num_rows($edit);
    if ((DOUBLE)$pketemu<=0) { exit; }
    $r    = mysqli_fetch_array($edit);
    
    $pl_lat=$r['sdm_latitude'];
    $pl_long=$r['sdm_longitude'];
    $pl_radius=$r['sdm_radius'];
    
}
?>


<script> window.onload = function() { document.getElementById("e_id").focus(); } </script>

<div class="">
    
    <div class="row">
        
        <div class='col-md-12 col-sm-12 col-xs-12'>
            
            <div class='x_panel'>
                
                <div class='col-md-12 col-sm-12 col-xs-12'>
                    <h2>
                        <a class='btn btn-default' href="<?PHP echo "?module=$pidmodule&idmenu=$pidmenu&act=$pidmenu"; ?>">Back</a>
                    </h2>
                    <div class='clearfix'></div>
                </div>
                
                <form method='POST' action='<?PHP echo "$aksi?module=$pidmodule&act=input&idmenu=$pidmenu"; ?>' 
                      id='d-form1' name='form1' data-parsley-validate class='form-horizontal form-label-left'  enctype='multipart/form-data'>
                    
                    <div class='x_panel'>
                        <div class='x_content'>
                            
                            <div class='col-md-12 col-sm-12 col-xs-12'>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>ID <span class='required'></span></label>
                                    <div class='col-md-2 col-sm-2 col-xs-7'>
                                        <input type='text' id='e_id' name='e_id' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidrutin; ?>' Readonly>
                                        <input type='hidden' id='e_idinputuser' name='e_idinputuser' class='form-control col-md-7 col-xs-12' value='<?PHP echo $piduser; ?>' Readonly>
                                        <input type='hidden' id='e_idcarduser' name='e_idcarduser' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidcard; ?>' Readonly>
                                        <input type='hidden' id='e_act' name='e_act' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidact; ?>' Readonly>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Status <span class='required'></span></label>
                                    <div class='col-md-3 col-sm-3 col-xs-7'>
                                        <input type='text' id='e_idstatus' name='e_idstatus' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidsts; ?>' Readonly>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Latitude <span class='required'></span></label>
                                    <div class='col-md-4 col-sm-4 col-xs-7'>
                                        <input type='text' id='e_lat' name='e_lat' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pl_lat; ?>' Readonly>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Longitude <span class='required'></span></label>
                                    <div class='col-md-4 col-sm-4 col-xs-7'>
                                        <input type='text' id='e_long' name='e_long' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pl_long; ?>' Readonly>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Radius <span class='required'></span></label>
                                    <div class='col-md-4 col-sm-4 col-xs-7'>
                                        <input type='text' id='e_radius' name='e_radius' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pl_radius; ?>' >
                                    </div>
                                </div>
                                
                                
                                <div hidden class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
                                    <div class='col-md-4 col-sm-4 col-xs-12'>
                                        <button type='button' class='tombol-simpan btn-xs btn-dark' id='ibuttontampil' onclick="getLocation()">Tampilkan Lokasi</button>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-4 col-sm-4 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
                                    <div class='col-md-5 col-sm-5 col-xs-12'>
                                        <?PHP
                                        echo "<button type='button' class='tombol-simpan btn btn-success' id='ibuttonsave' onclick=\"disp_confirm('$act')\">Simpan</button>";
                                        ?>
                                    </div>
                                </div>
                                
                                
                            </div>
                            
                        </div>
                    </div>
                    
                    
                </form>
                
                
                
            </div>
            
            
        </div>
        
    </div>
    
</div>


<script>
    function disp_confirm(ket)  {
        
        //getLocation();
        
        //setTimeout(function () {
            disp_confirm_ext(ket)
        //}, 500);
        
    }
    
    function disp_confirm_ext(ket)  {
        
        var eid=document.getElementById('e_id').value;
        var estsid=document.getElementById('e_idstatus').value;
        var nlat=document.getElementById('e_lat').value;
        var nlong=document.getElementById('e_long').value;
        
        if (eid==""){
            alert("id kosong....");
            return 0;
        }
        
        if (estsid==""){
            alert("status id kosong....");
            return 0;
        }
        
        if (nlat=="" || nlong=="") {
            alert("Lokasi Kosong"); return false;
        }
        
        var pText_="Apakah akan melakukan simpan...?";
        var r=confirm(pText_)
        if (r==true) {
        }else{
            return false;
        }
        
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var idmenu = urlku.searchParams.get("idmenu");
        //document.write("You pressed OK!")
        document.getElementById("d-form1").action = "module/hrd/hrd_lokasi/aksi_lokasi.php?module="+module+"&act="+ket+"&idmenu="+idmenu;
        document.getElementById("d-form1").submit();
        return 1;
        
    }
</script>