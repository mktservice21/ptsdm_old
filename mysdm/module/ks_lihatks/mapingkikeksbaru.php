<?PHP
session_start();
$aksi="";
$fkaryawan=$_SESSION['IDCARD'];
$fjbtid=$_SESSION['JABATANID'];
$fgroupid=$_SESSION['GROUP'];
$fstsadmin=$_SESSION['STSADMIN'];
$flvlposisi=$_SESSION['LVLPOSISI'];
$fdivisi=$_SESSION['DIVISI'];
        
include "../../config/koneksimysqli.php";
include "../../config/koneksimysqli_ms.php";
$pidbr=$_POST['uidkry'];

$pidkry=$_POST['uidkry'];
$pnmkry=$_POST['unmkry'];
$piddokt=$_POST['uiddokt'];
$pnmdokt=$_POST['unmdokt'];
$pidapt="";
$pnmapt="";
$ptypapt="";

$pidpraktek="";
$piddokter="";

$query = "select a.brId as brid, a.aktivitas1, a.aktivitas2, "
        . " a.tgl, a.tgltrans, a.realisasi1, a.karyawanid, b.nama as nama_karyawan "
        . " FROM hrd.br0 as a LEFT JOIN hrd.karyawan as b on a.karyawanid=b.karyawanId where a.brId='$pidbr'";
$tampil=mysqli_query($cnmy, $query);
$row= mysqli_fetch_array($tampil);

$ptanggal=$row['tgl'];

$npcabangid="";
$npareaid="";

$psudahmaping=false;
$query = "SELECT iddokter, outletid FROM ms2.mapping_ks_dsu WHERE dokterid='$piddokt' AND karyawanid='$pidkry' AND idapotik='$pidapt'";
$tampild= mysqli_query($cnms, $query);
$ketemud= mysqli_num_rows($tampild);
if ((INT)$ketemud>0) {
    $nro= mysqli_fetch_array($tampild);
    $psudahmaping=true;
    $pidpraktek="";//$nro['idpraktek'];
    $gsdsudoktit=$nro['iddokter'];
    $gsouteltid=$nro['outletid'];
    
    $query = "SELECT a.id, a.outletid, b.iCabangId as icabangid, b.areaId as areaid FROM ms2.tempatpraktek as a "
            . " JOIN ms2.outlet_customer as b on a.outletId=b.outletId where a.iddokter='$gsdsudoktit' AND a.outletid='$gsouteltid'";
    $tampilp= mysqli_query($cnms, $query);
    $np= mysqli_fetch_array($tampilp);
    $npcabangid=$np['icabangid'];
    $npareaid=$np['areaid'];
}
            
?>

<div class='modal-dialog modal-lg'>
    <!-- Modal content-->
    <div class='modal-content'>
        
        <div class='modal-header'>
            <button type='button' class='close' data-dismiss='modal'>&times;</button>
            <h4 class='modal-title'>Mapping Data KI</h4>
        </div>
        <br/>
        <div class="">
            
            <?PHP //echo $query; ?>
            
            <div class="row">

                <div class="col-md-8 col-sm-8 col-xs-12">

                    <div class="x_panel">
                        
                        <div class="x_content">
                            <div class="dashboard-widget-content">
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-4 col-sm-4 col-xs-12' for=''>MR <span class='required'></span></label>
                                    <div class='col-md-8'>
                                        <input type='hidden' id='e_idkry' name='e_idkry' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidkry; ?>' Readonly>
                                        <input type='text' id='e_nmkry' name='e_nmkry' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidkry." - ".$pnmkry; ?>' Readonly>
                                    </div>
                                </div>
                                
                                <div class='form-group' style="margin-top:40px;">
                                    <label class='control-label col-md-4 col-sm-4 col-xs-12' for=''>User <span class='required'></span></label>
                                    <div class='col-md-8'>
                                        <input type='hidden' id='e_iddokt' name='e_iddokt' class='form-control col-md-7 col-xs-12' value='<?PHP echo $piddokt; ?>' Readonly>
                                        <input type='text' id='e_nmdokt' name='e_nmdokt' class='form-control col-md-7 col-xs-12' value='<?PHP echo $piddokt." - ".$pnmdokt; ?>' Readonly>
                                    </div>
                                </div>
                                
                                <div class='form-group' style="margin-top:80px;">
                                    <label class='control-label col-md-4 col-sm-4 col-xs-12' for=''>Cabang <span class='required'></span></label>
                                    <div class='col-md-8'>
                                        
                                        <select class='soflow' id="cb_cabang" name="cb_cabang" onchange="ShowDataCabang()">
                                            <?PHP                                                  

                                                $nojm=1;
                                                $query_cb = "select icabangid as icabangid, nama as nama, "
                                                        . " CASE WHEN IFNULL(aktif,'')='' then 'Y' else aktif end as aktif "
                                                        . " from MKT.icabang WHERE 1=1 ";
                                                if ($fgroupid=="24" or $fgroupid=="1") {
                                                }else{
                                                    if (!empty($pfiltercabpilih)) {
                                                        //$query_cb .=" AND iCabangId IN $pfiltercabpilih ";
                                                    }
                                                }
                                                $query_cb .=" AND LEFT(nama,5) NOT IN ('OTC -', 'ETH -', 'PEA -')";
                                                $query_cb .=" AND IFNULL(aktif,'')<>'N' ";
                                                $query_cb .=" order by CASE WHEN IFNULL(aktif,'')='' then 'Y' else aktif end desc, nama";
                                                $tampil = mysqli_query($cnmy, $query_cb);

                                                $ketemu= mysqli_num_rows($tampil);
                                                echo "<option value='' selected>-- Pilih --</option>";
                                                $pketaktif=false;
                                                while ($z= mysqli_fetch_array($tampil)) {
                                                    $pcabid=$z['icabangid'];
                                                    $pcabnm=$z['nama'];
                                                    $pstsaktif=$z['aktif'];
                                                    $pcbid=(INT)$pcabid;

                                                    if ($pstsaktif=="N" AND $nojm<=1) { $pketaktif=true; $nojm++; }

                                                    if ($pketaktif==true) {
                                                        echo "<option value=''>&nbsp;</option>";
                                                        echo "<option value=''>-- non aktif --</option>";
                                                        $pketaktif=false;
                                                    }
                                                    if ($fjbtid=="15" OR $fjbtid=="10" OR $fjbtid=="18" OR $fjbtid=="08") {
                                                        echo "<option value='$pcabid' selected>$pcabnm ($pcbid)</option>";
                                                        $pcabangselected=$pcabid;
                                                    }else {
                                                        if ($pcabid==$npcabangid)
                                                            echo "<option value='$pcabid' selected>$pcabnm ($pcbid)</option>";
                                                        else
                                                            echo "<option value='$pcabid'>$pcabnm ($pcbid)</option>";
                                                    }
                                                }

                                            ?>
                                        </select>
                                        
                                    </div>
                                </div>
                                
                                
                                <div class='form-group' style="margin-top:120px;">
                                    <label class='control-label col-md-4 col-sm-4 col-xs-12' for=''>Area <span class='required'></span></label>
                                    <div class='col-md-8'>
                                        
                                        <select class='soflow' id="cb_area" name="cb_area" onchange="ShowDataDokter()">
                                            <?PHP
                                                echo "<option value='' selected>-- All --</option>";
                                                if (!empty($npcabangid)) {
                                                    $query = "select areaid as areaid, nama as nama from mkt.iarea WHERE iCabangId='$npcabangid' AND IFNULL(aktif,'')<>'N' ";
                                                    $query .=" order by nama";
                                                    $tampil= mysqli_query($cnmy, $query);
                                                    while ($row= mysqli_fetch_array($tampil)) {
                                                        $nareaid=$row['areaid'];
                                                        $nareanm=$row['nama'];
                                                        if ($nareaid==$npareaid)
                                                            echo "<option value='$nareaid' selected>$nareanm</option>";
                                                        else
                                                            echo "<option value='$nareaid' >$nareanm</option>";
                                                    }
                                                }
                                            ?>
                                        </select>
                                        
                                    </div>
                                </div>
                                
                                
                                <div class='form-group' style="margin-top:160px;">
                                    <label class='control-label col-md-4 col-sm-4 col-xs-12' for=''>User <span class='required'></span></label>
                                    <div class='col-md-8'>
                                        <select class='soflow s3' id="cb_dokt" name="cb_dokt" onchange="" style="width: 340px;">
                                            <?PHP
                                                echo "<option value='' selected>-- Pilih --</option>";
                                                if (!empty($npcabangid)) {
                                                    
                                                    $query = "SELECT DISTINCT d.iCabangId as icabangid, e.nama as nama_cabang, d.areaId as areaid, f.Nama as nama_area, 
                                                        a.iddokter, g.namalengkap as nama_dokter, g.spesialis, h.nama as nama_spesialis  
                                                        FROM ms2.tempatpraktek as a 
                                                        JOIN ms2.outlet_master as b on a.outletId=b.id 
                                                        LEFT JOIN ms2.outlet_type as c on b.type=c.id 
                                                        JOIN ms2.outlet_customer as d on a.outletId=d.outletId 
                                                        LEFT JOIN mkt.icabang as e on d.iCabangId=e.iCabangId 
                                                        LEFT JOIN mkt.iarea as f on d.iCabangId=f.iCabangId and d.areaId=f.areaId 
                                                        JOIN ms2.masterdokter as g on a.iddokter=g.id 
                                                        LEFT JOIN ms2.lookup as h on g.spesialis=h.id 
                                                        WHERE d.icabangid='$npcabangid' ";
                                                    if (!empty($npareaid)) {
                                                        $query .=" AND d.areaid='$npareaid' ";
                                                    }
                                                    $query .=" ORDER BY g.namalengkap, a.iddokter";
                                                    $tampil= mysqli_query($cnms, $query);
                                                    while ($row= mysqli_fetch_array($tampil)) {
                                                        $pniddokt=$row['iddokter'];
                                                        $pnnmdokt=$row['nama_dokter'];
                                                        
                                                        if ($pniddokt==$piddokter)
                                                            echo "<option value='$pniddokt' selected>$pnnmdokt - ($pniddokt)</option>";
                                                        else
                                                            echo "<option value='$pniddokt' >$pnnmdokt - ($pniddokt)</option>";
                                                    }
    
                                                }
                                            ?>
                                        </select>
                                        
                                    </div>
                                </div>
                                
                                


                                <div class='form-group' style="margin-top:200px;">
                                    <label class='control-label col-md-4 col-sm-4 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
                                    <div class='col-md-8'>
                                        <?PHP
                                        if ($psudahmaping==true) {
                                            echo "SUDAH MAPPING...";
                                        }else{
                                        ?>
                                            <button type='button' class='btn btn-success' id="ibuttonsave" onclick='disp_confirm_maping("Simpan ?", "<?PHP echo "simpan"; ?>")'>Simpan</button>

                                        <?PHP
                                        }
                                        ?>
                                    </div>
                                </div>
                                

                                
                                
                            </div>
                            
                        </div>
                        
                    </div>

                </div>
                
                
            </div>
        
        </div>
        
        
        <div class='modal-footer'>
            <button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
        </div>
        
    </div>
</div>

<link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />
<link href="css/stylenew.css" rel="stylesheet" type="text/css" />

<style>
    .divnone {
        display: none;
    }
    #datatable th {
        font-size: 13px;
    }
    #datatable td { 
        font-size: 11px;
    }
</style>

<script>
    
    
    function ShowDataCabang() {
        ShowDataArea();
    }
    
    function ShowDataArea() {
        var eidcab =document.getElementById('cb_cabang').value;

        $.ajax({
            type:"post",
            url:"module/ks_lihatks/viewdata.php?module=viewdataareacab",
            data:"uidcab="+eidcab,
            success:function(data){
                $("#cb_area").html(data);
                ShowDataDokter();
            }
        });
    }
    
    
    function ShowDataDokter() {
        var eidcab =document.getElementById('cb_cabang').value;
        var eidarea =document.getElementById('cb_area').value;
        
        $.ajax({
            type:"post",
            url:"module/ks_lihatks/viewdata.php?module=viewdatadokter",
            data:"uidcab="+eidcab+"&uidarea="+eidarea,
            success:function(data){
                $("#cb_dokt").html(data);
            }
        });
    }
    
    
    function TampilkanData() {
        var eidcab =document.getElementById('cb_cabang').value;
        var eidarea =document.getElementById('cb_area').value;

        $.ajax({
            type:"post",
            url:"module/ks_lihatks/viewdata_outletksnew.php?module=viewdataoutletksnew",
            data:"uidcab="+eidcab+"&uidarea="+eidarea,
            success:function(data){
                $("#div_dataoutelt").html(data);
            }
        });
    }
    
    function disp_confirm_maping(pText_, eact)  {
        var ikryid =document.getElementById('e_idkry').value;
        var idoktid =document.getElementById('e_iddokt').value;
        var idcabid =document.getElementById('cb_cabang').value;
        var iareaid =document.getElementById('cb_area').value;
        var idsudoktid =document.getElementById('cb_dokt').value;
        
        
        if (ikryid=="") {
            alert("MR / Karyawan Kosong...");
            return false;    
        }
        
        if (idoktid=="") {
            alert("USER Kosong...");
            return false;    
        }
        
        if (idsudoktid=="") {
            alert("New User DSU Kosong...");
            return false;    
        }
        
        pText_="Apakah akan melakukan simpan data...?\n\
Data yang sudah tersimpan tidak bisa diubah kembali...";
        ok_ = 1;
        if (ok_) {
            var r=confirm(pText_)
            if (r==true) {
                var myurl = window.location;
                var urlku = new URL(myurl);
                var module = urlku.searchParams.get("module");
                var idmenu = urlku.searchParams.get("idmenu");
                //document.write("You pressed OK!")
                
                $.ajax({
                    type:"post",
                    url:"module/ks_lihatks/simpandatamapingnewkiks.php?module="+module+"&act=simpanksnew&idmenu="+idmenu,
                    data:"ukryid="+ikryid+"&udoktid="+idoktid+"&udcabid="+idcabid+"&uareaid="+iareaid+"&udsudoktid="+idsudoktid,
                    success:function(data){
                        document.getElementById("ibuttonsave").disabled = true;
                        alert(data);
                    }
                });
                
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }
        
        
    }
    
</script>
    
<?PHP
mysqli_close($cnmy);
mysqli_close($cnms);
?>
<style>
    .idivpilih {
        margin-top: 40px;
    }
</style>
<script src="vendors/jquery/dist/jquery.min.js"></script>
<link href="module/ks_lihatks/select2.min.css" rel="stylesheet" type="text/css" />
<script src="module/ks_lihatks/select2.min.js"></script>
<script>
$(document).ready(function() {
        $('.s3').select2();
    });
</script>