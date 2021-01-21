<?php
include "config/koneksimysqli_ms.php";


$pidmodule=$_GET['module'];
$pidmenu=$_GET['idmenu'];
$pidact=$_GET['act'];
$pstsmobile=$_SESSION['MOBILE'];
$piduser=$_SESSION['USERID']; 
$pidcard=$_SESSION['IDCARD'];
$pidjbt=$_SESSION['JABATANID']; 


$pidinput="";
$pnama="";
$palamat1="";
$palamat2="";
$pkota="";
$ptelp="";
$pfax="";
$pkontak="";
$pkodepos="";

$pidcabang="";
$pidarea="";
$psektorid="";

$act="input";
if ($pidact=="editdata"){
    $act="update";
    $pidinput=$_GET['id'];
    
    $sql = "select icabangid, areaid, icustid, isektorid, nama, "
            . " alamat1, alamat2, kota, telp, fax, contact, kodepos "
            . " FROM MKT.icust WHERE concat(icabangid, areaid, icustid)='$pidinput'";
    $edit = mysqli_query($cnms, $sql);
    $r    = mysqli_fetch_array($edit);
    
    $pidinput=$r['icustid'];
    $pidcabang=$r['icabangid'];
    $pidarea=$r['areaid'];
    $psektorid=$r['isektorid'];
    $pnama=$r['nama'];
    $palamat1=$r['alamat1'];
    $palamat2=$r['alamat2'];
    $pkota=$r['kota'];
    
    $ptelp=$r['telp'];
    $pfax=$r['fax'];
    $pkontak=$r['contact'];
    $pkodepos=$r['kodepos'];
    
}

?>



<script> window.onload = function() { document.getElementById("e_id").focus(); } </script>

<div class="">
    
    
    <!--row-->
    <div class="row">
        
        <div class='col-md-12 col-sm-12 col-xs-12'>
            
            <div class='x_panel'>
                
                <form method='POST' action='<?PHP echo "$aksi?module=$pidmodule&act=input&idmenu=$pidmenu"; ?>' 
                      id='form_data1' name='form1' data-parsley-validate class='form-horizontal form-label-left'  enctype='multipart/form-data'>
                    
                    <div class='col-md-12 col-sm-12 col-xs-12'>
                        <h2>
                            <a class='btn btn-default' href="<?PHP echo "?module=$pidmodule&idmenu=$pidmenu&act=$pidmenu"; ?>">Back</a>
                        </h2>
                        <div class='clearfix'></div>
                    </div>
                    
                    
                    <div class='x_panel'>
                        <div class='x_content'>
                            
                            
                            <div class='col-md-12 col-sm-12 col-xs-12'>
                                
                                
                                <div hidden class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>ID <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input type='text' id='e_id' name='e_id' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidinput; ?>' Readonly>
                                        <input type='hidden' id='e_idinputuser' name='e_idinputuser' class='form-control col-md-7 col-xs-12' value='<?PHP echo $piduser; ?>' Readonly>
                                        <input type='hidden' id='e_idcarduser' name='e_idcarduser' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidcard; ?>' Readonly>
                                    </div>
                                </div>

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Cabang <span class='required'></span></label>
                                    <div class='col-xs-4'>
                                        <select class='soflow' name='cb_cabangid' id='cb_cabangid' onchange="ShowDataCabangArea()">
                                            <?php
                                            echo "<option value='' selected>--Pilih--</option>";
                                            if ($pidact=="editdata"){
                                                $query = "select icabangid as icabangid, nama as nama from MKT.icabang WHERE icabangid='$pidcabang' ";
                                            }else{
                                                if ($fjbtid=="38") {
                                                    $query = "select DISTINCT a.icabangid as icabangid, a.nama as nama from MKT.icabang as a "
                                                            . " JOIN hrd.rsm_auth as b on a.icabangid=b.icabangid WHERE b.karyawanid='$pidcard' ";
                                                    $query .=" order by a.nama";
                                                }elseif ($fjbtid=="10" OR $fjbtid=="18") {
                                                    $query = "select DISTINCT a.icabangid as icabangid, a.nama as nama from MKT.icabang as a "
                                                            . " JOIN MKT.ispv0 as b on a.icabangid=b.icabangid WHERE b.karyawanid='$pidcard' ";
                                                    $query .=" order by a.nama";
                                                }elseif ($fjbtid=="15") {
                                                    $query = "select DISTINCT a.icabangid as icabangid, a.nama as nama from MKT.icabang as a "
                                                            . " JOIN MKT.imr0 as b on a.icabangid=b.icabangid WHERE b.karyawanid='$pidcard' ";
                                                    $query .=" order by a.nama";
                                                }else{
                                                    $query = "select icabangid as icabangid, nama as nama from MKT.icabang WHERE 1=1 ";
                                                    $query .=" AND LEFT(nama,5) NOT IN ('OTC -', 'PEA -') ";
                                                    $query .=" AND IFNULL(aktif,'')<>'N' ";
                                                    $query .=" order by nama";
                                                }
                                            }
                                            $tampiledu= mysqli_query($cnmy, $query);
                                            while ($du= mysqli_fetch_array($tampiledu)) {
                                                $nidcab=$du['icabangid'];
                                                $nnmcab=$du['nama'];

                                                if ($nidcab==$pidcabang) 
                                                    echo "<option value='$nidcab' selected>$nnmcab</option>";
                                                else
                                                    echo "<option value='$nidcab'>$nnmcab</option>";

                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Area <span class='required'></span></label>
                                    <div class='col-xs-4'>
                                        <select class='soflow' name='cb_areaid' id='cb_areaid' onchange="">
                                            <?php
                                            echo "<option value='' selected>--Pilih--</option>";
                                            if ($pidact=="editdata"){
                                                $query = "select icabangid as icabangid, areaid as areaid, nama as nama from MKT.iarea WHERE icabangid='$pidcabang' AND areaid='$pidarea' ";
                                            }else{
                                                $query = "select icabangid as icabangid, areaid as areaid, nama as nama from MKT.iarea WHERE icabangid='$pidcabang' ";
                                                $query .=" AND IFNULL(aktif,'')<>'N' ";
                                                $query .=" order by nama";
                                            }
                                            
                                            if (!empty($pidcabang)) {
                                                $tampiledu= mysqli_query($cnmy, $query);
                                                while ($du= mysqli_fetch_array($tampiledu)) {
                                                    $nidarea=$du['areaid'];
                                                    $nnmarea=$du['nama'];

                                                    if ($nidarea==$pidarea) 
                                                        echo "<option value='$nidarea' selected>$nnmarea</option>";
                                                    else
                                                        echo "<option value='$nidarea'>$nnmarea</option>";

                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Sektor/Segmen <span class='required'></span></label>
                                    <div class='col-xs-4'>
                                        <select class='soflow' name='cb_sektorid' id='cb_sektorid' onchange="">
                                            <?php
                                            echo "<option value='' selected>--Pilih--</option>";
                                            $query = "select iSektorId as isektorid, nama as nama from MKT.isektor WHERE "
                                                    . " 1=1 "
                                                    . " order by 2,1";
                                            $tampiledu= mysqli_query($cnmy, $query);
                                            while ($du= mysqli_fetch_array($tampiledu)) {
                                                $nidsektro=$du['isektorid'];
                                                $nnmsektro=$du['nama'];

                                                if ($nidsektro==$psektorid) 
                                                    echo "<option value='$nidsektro' selected>$nnmsektro ($nidsektro)</option>";
                                                else
                                                    echo "<option value='$nidsektro'>$nnmsektro ($nidsektro)</option>";

                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Nama  <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input type='text' id='e_nama' name='e_nama' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnama; ?>' maxlength="40">
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Alamat <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input type='text' id='e_alamat1' name='e_alamat1' class='form-control col-md-7 col-xs-12' value='<?PHP echo $palamat1; ?>' maxlength="40">
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input type='text' id='e_alamat2' name='e_alamat2' class='form-control col-md-7 col-xs-12' value='<?PHP echo $palamat2; ?>' maxlength="40">
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Kota <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input type='text' id='e_kota' name='e_kota' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pkota; ?>' maxlength="40">
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Kode Pos <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input type='text' id='e_kdpos' name='e_kdpos' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pkodepos; ?>' maxlength="10">
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Telp <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input type='text' id='e_telp' name='e_telp' class='form-control col-md-7 col-xs-12' value='<?PHP echo $ptelp; ?>' maxlength="30">
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Fax <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input type='text' id='e_fax' name='e_fax' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pfax; ?>' maxlength="30">
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Contact Person <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input type='text' id='e_kontakperson' name='e_kontakperson' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pkontak; ?>' maxlength="40">
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <button type='button' class='btn btn-success' onclick='disp_confirm("Simpan ?", "<?PHP echo $act; ?>")'>Save</button>
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

<link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />
<link href="css/stylenew.css" rel="stylesheet" type="text/css" />

<script>
    
    function ShowDataCabangArea() {
        var idcab=document.getElementById('cb_cabangid').value;
        $.ajax({
            type:"post",
            url:"module/map_customersdm/viewdatacust.php?module=viewdataareacabang",
            data:"udcab="+idcab,
            success:function(data){
                $("#cb_areaid").html(data);
            }
        });
    }
    
    function disp_confirm(pText_,ket)  {

        var iid = document.getElementById('e_id').value;
        var icab = document.getElementById('cb_cabangid').value;
        var iarea = document.getElementById('cb_areaid').value;
        var isektor = document.getElementById('cb_sektorid').value;
        var inama = document.getElementById('e_nama').value;
        var ialamat1 = document.getElementById('e_alamat1').value;
        var ikota = document.getElementById('e_kota').value;

        if (icab=="") {
            alert("cabang masih kosong...");
            return false;
        }

        if (iarea=="") {
            alert("area masih kosong...");
            return false;
        }

        if (isektor=="") {
            alert("Sektor/Segmen masih kosong...");
            return false;
        }

        if (inama=="") {
            alert("nama customer masih kosong...");
            return false;
        }

        if (ialamat1=="") {
            alert("alamat masih kosong...");
            return false;
        }
        

        ok_ = 1;
        if (ok_) {
            var r=confirm(pText_)
            if (r==true) {
                var myurl = window.location;
                var urlku = new URL(myurl);
                var module = urlku.searchParams.get("module");
                var idmenu = urlku.searchParams.get("idmenu");
                //document.write("You pressed OK!")
                document.getElementById("form_data1").action = "module/map_customersdm/aksi_customersdm.php?module="+module+"&act="+ket+"&idmenu="+idmenu;
                document.getElementById("form_data1").submit();
                return 1;
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }

    }


</script>