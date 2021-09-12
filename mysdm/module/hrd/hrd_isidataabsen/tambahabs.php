<?php

include "config/fungsi_ubahget_id.php";

$pidmodule=$_GET['module'];
$pidmenu=$_GET['idmenu'];
$pidact=$_GET['act'];
$pstsmobile=$_SESSION['MOBILE'];


$piduser=$_SESSION['USERID'];
$pidcard=$_SESSION['IDCARD'];
$pidgroup=$_SESSION['GROUP'];

$hari_ini = date("Y-m-d");
$ptanggal = date('d F Y', strtotime($hari_ini));
$ptglini =$hari_ini;
    
$pkaryawanid=$pidcard;
        
$aksi="";
$pidrutin="";
$pl_jam="";
$pketerangan="";
$pkodeabsen="1";

$pseljns01="selected";
$pseljns02="";

$phidekry="";
$phidekdabs="";

$act="input";
if ($pidact=="editdata"){
    $act="update";
    
    $pidinput_ec=$_GET['id'];
    $pidrutin = decodeString($pidinput_ec);
    
    $query = "select * from hrd.t_absen WHERE idabsen='$pidrutin'";
    $edit= mysqli_query($cnmy, $query);
    
    $pketemu    = mysqli_num_rows($edit);
    if ((DOUBLE)$pketemu<=0) { exit; }
    $r    = mysqli_fetch_array($edit);
    
    $pkaryawanid=$r['karyawanid'];
    $ptanggal=$r['tanggal'];
    $pl_jam=$r['jam'];
    $pketerangan=$r['keterangan'];
    $pkodeabsen=$r['kode_absen'];
    $pjnslokasi=$r['l_status'];
    
    if ($pjnslokasi=="WFH") {
        $pseljns01="";
        $pseljns02="selected";
    }else{
        $pseljns01="selected";
        $pseljns02="";
    }
    
    $ptglini=$ptanggal;
    $ptanggal = date('d F Y', strtotime($ptanggal));
    
    if (!empty($pkaryawanid)) $phidekry="hidden";
    if (!empty($pkodeabsen)) $phidekdabs="hidden";
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
                                
                                <div <?PHP echo $phidekry; ?> class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Karyawan <span class='required'></span></label>
                                    <div class='col-md-5 col-sm-5 col-xs-9'>
                                        <?PHP
                                            echo "<select class='form-control input-sm' id='e_idkry' name='e_idkry' onchange=''>";
                                            $query = "select a.karyawanid as karyawanid, a.nama as nama FROM hrd.karyawan as a JOIN "
                                                    . " dbmaster.t_karyawan_posisi as b on a.karyawanId=b.karyawanId WHERE 1=1 "
                                                    . " AND IFNULL(b.ho,'')='Y' ";
                                            $query .=" ORDER BY a.nama";
                                            $tampilk=mysqli_query($cnmy, $query);
                                            while ($krow= mysqli_fetch_array($tampilk)) {
                                                $npkryid=$krow['karyawanid'];
                                                $npkrynm=$krow['nama'];
                                                
                                                if ($npkryid==$pkaryawanid)
                                                    echo "<option value='$npkryid' selected>$npkrynm</option>";
                                                else
                                                    echo "<option value='$npkryid'>$npkrynm</option>";
                                            }
                                            echo "</select>";
                                        ?>
                                        <input type='hidden' id='e_id' name='e_id' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidrutin; ?>' Readonly>
                                        <input type='hidden' id='e_idinputuser' name='e_idinputuser' class='form-control col-md-7 col-xs-12' value='<?PHP echo $piduser; ?>' Readonly>
                                        <input type='hidden' id='e_idcarduser' name='e_idcarduser' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidcard; ?>' Readonly>
                                        <input type='hidden' id='e_act' name='e_act' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidact; ?>' Readonly>
                                        <input type='hidden' id='e_tglini' name='e_tglini' class='form-control col-md-7 col-xs-12' value='<?PHP echo $ptglini; ?>' Readonly>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Tanggal <span class='required'></span></label>
                                    <div class='col-md-4 col-sm-4 col-xs-7'>
                                        <div class='input-group date' id='tgl01'>
                                            <input type='text' id='tgl1' name='e_periode01' required='required' class='form-control input-sm' placeholder='tgl awal' value='<?PHP echo $ptanggal; ?>' placeholder='dd mmm yyyy' Readonly>
                                            <span class="input-group-addon">
                                               <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Jam <span class='required'></span></label>
                                    <div class='col-md-4 col-sm-4 col-xs-7'>
                                        <input type='text' id='e_jam' name='e_jam' class='form-control col-md-7 col-xs-12' placeholder="00:00" value='<?PHP echo $pl_jam; ?>' maxlength="5">
                                    </div>
                                </div>
                                
                                <div <?PHP echo $phidekdabs; ?> class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Absen <span class='required'></span></label>
                                    <div class='col-md-4 col-sm-4 col-xs-9'>
                                        <?PHP
                                            echo "<select class='form-control input-sm' id='e_kdabsen' name='e_kdabsen' onchange=''>";
                                            $query = "select kode_absen, nama_absen FROM hrd.t_absen_kode ";
                                            $query .=" ORDER BY kode_absen";
                                            $tampilk=mysqli_query($cnmy, $query);
                                            while ($krow= mysqli_fetch_array($tampilk)) {
                                                $npkdabs=$krow['kode_absen'];
                                                $npnmabs=$krow['nama_absen'];
                                                
                                                if ($npkdabs==$pkodeabsen)
                                                    echo "<option value='$npkdabs' selected>$npnmabs</option>";
                                                else
                                                    echo "<option value='$npkdabs'>$npnmabs</option>";
                                            }
                                            echo "</select>";
                                        ?>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Jenis <span class='required'></span></label>
                                    <div class='col-md-4 col-sm-4 col-xs-9'>
                                        <?PHP
                                            echo "<select class='form-control input-sm' id='e_jenisabse' name='e_jenisabse' onchange=''>";
                                            echo "<option value='WFO' $pseljns01>WFO</option>";
                                            echo "<option value='WFH' $pseljns02>WFH</option>";
                                            echo "</select>";
                                        ?>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Keterangan <span class='required'></span></label>
                                    <div class='col-md-4 col-sm-4 col-xs-12'>
                                        <textarea id="txt_ket" name="txt_ket" class='form-control'><?PHP echo $pketerangan; ?></textarea>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-4 col-sm-4 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
                                    <div class='col-md-5 col-sm-5 col-xs-12'>
                                        <?PHP
                                        echo "<button type='button' class='tombol-simpan btn btn-success' id='ibuttonsave' onclick=\"disp_confirm('Simpan ?', '$act')\">Simpan</button>";
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
    function disp_confirm(pText_, ket)  {
        
        //getLocation();
        
        //setTimeout(function () {
            disp_confirm_ext(pText_, ket)
        //}, 500);
        
    }
    
    function disp_confirm_ext(pText_, ket)  {
        
        var eid=document.getElementById('e_id').value;
        var ekryid=document.getElementById('e_idkry').value;
        var ejam=document.getElementById('e_jam').value;
        var ekdabs=document.getElementById('e_kdabsen').value;
        var ejenislokasi=document.getElementById('e_jenisabse').value;
        
        if (ekryid==""){
            alert("karyawan kosong....");
            return 0;
        }
        
        if (ejam==""){
            alert("jam masih kosong....");
            return 0;
        }
        
        if (ekdabs=="") {
            alert("Kode Absen Harus dipilih"); return false;
        }
        
        if (ejenislokasi=="") {
            alert("Lokasi WFO/WFH Harus dipilih"); return false;
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
        document.getElementById("d-form1").action = "module/hrd/hrd_isidataabsen/aksi_isidataabsen.php?module="+module+"&act="+ket+"&idmenu="+idmenu;
        document.getElementById("d-form1").submit();
        return 1;
        
    }
</script>