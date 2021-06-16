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
$pidbr=$_POST['uidkry'];

$pidkry=$_POST['uidkry'];
$pnmkry=$_POST['unmkry'];
$piddokt=$_POST['uiddokt'];
$pnmdokt=$_POST['unmdokt'];
$pidapt=$_POST['uidapt'];
$pnmapt=$_POST['unmapt'];
$ptypapt=$_POST['utypapt'];

$query = "select a.brId as brid, a.aktivitas1, a.aktivitas2, "
        . " a.tgl, a.tgltrans, a.realisasi1, a.karyawanid, b.nama as nama_karyawan "
        . " FROM hrd.br0 as a LEFT JOIN hrd.karyawan as b on a.karyawanid=b.karyawanId where a.brId='$pidbr'";
$tampil=mysqli_query($cnmy, $query);
$row= mysqli_fetch_array($tampil);

$ptanggal=$row['tgl'];

?>

<div class='modal-dialog modal-lg'>
    <!-- Modal content-->
    <div class='modal-content'>
        
        <div class='modal-header'>
            <button type='button' class='close' data-dismiss='modal'>&times;</button>
            <h4 class='modal-title'>Mapping Data KS</h4>
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
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-4 col-sm-4 col-xs-12' for=''>User <span class='required'></span></label>
                                    <div class='col-md-8'>
                                        <input type='hidden' id='e_iddokt' name='e_iddokt' class='form-control col-md-7 col-xs-12' value='<?PHP echo $piddokt; ?>' Readonly>
                                        <input type='text' id='e_nmdokt' name='e_nmdokt' class='form-control col-md-7 col-xs-12' value='<?PHP echo $piddokt." - ".$pnmdokt; ?>' Readonly>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-4 col-sm-4 col-xs-12' for=''>Apotik <span class='required'></span></label>
                                    <div class='col-md-8'>
                                        <input type='hidden' id='e_idapt' name='e_idapt' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidapt; ?>' Readonly>
                                        <input type='text' id='e_nmapt' name='e_nmapt' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidapt." - ".$pnmapt; ?>' Readonly>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-4 col-sm-4 col-xs-12' for=''>Cabang <span class='required'></span></label>
                                    <div class='col-md-8'>
                                        
                                        <select class='form-control' id="cb_cabang" name="cb_cabang" onchange="ShowDataCabang()">
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
                                                        if ($pcabid==$ppilihancabang)
                                                            echo "<option value='$pcabid' selected>$pcabnm ($pcbid)</option>";
                                                        else
                                                            echo "<option value='$pcabid'>$pcabnm ($pcbid)</option>";
                                                    }
                                                }

                                            ?>
                                        </select>
                                        
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-4 col-sm-4 col-xs-12' for=''>Area <span class='required'></span></label>
                                    <div class='col-md-8'>
                                        
                                        <select class='form-control' id="cb_area" name="cb_area" onchange="ShowDataOutelt()">
                                            <?PHP
                                                echo "<option value='' selected>-- All --</option>";
                                            ?>
                                        </select>
                                        
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-4 col-sm-4 col-xs-12' for=''>Outelt <span class='required'></span></label>
                                    <div class='col-md-8'>
                                        
                                        <select class='form-control' id="cb_outlet" name="cb_outlet" onchange="">
                                            <?PHP
                                                echo "<option value='' selected>-- Pilih --</option>";
                                            ?>
                                        </select>
                                        
                                    </div>
                                </div>
                                
                                <!--
                                <div class='form-group'>
                                    <label class='control-label col-md-4 col-sm-4 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        &nbsp;
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-4 col-sm-4 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <button type='button' class='btn btn-info btn-xs' onClick="TampilkanData()">Tampilkan Data Outlet</button>
                                    </div>
                                </div>
                                -->
                                

                                
                                
                            </div>
                            
                        </div>
                        
                    </div>

                </div>
                
                <!--
                <div class="col-xs-12">

                    <div class="x_panel">
                        <div id="div_dataoutelt">
                            
                            <table id='datatable' class='table table-striped table-bordered' width='100%'>
                                <thead>
                                    <tr>
                                        <th width='5px'>No</th>
                                        <th width='50px'></th>
                                        <th width='20px'>Area</th>
                                        <th width='40px'>Outlet</th>
                                        <th width='15px'>Nama Type</th>
                                        <th width='10px'>Dispensing</th>
                                        <th width='50px'>Alamat</th>
                                        <th width='30px'>User</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                            
                        </div>
                    </div>
                    
                </div>
                -->
                
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
                ShowDataOutelt();
            }
        });
    }
    
    
    function ShowDataOutelt() {
        var eidcab =document.getElementById('cb_cabang').value;
        var eidarea =document.getElementById('cb_area').value;
        
        $.ajax({
            type:"post",
            url:"module/ks_lihatks/viewdata.php?module=viewdataoutlet",
            data:"uidcab="+eidcab+"&uidarea="+eidarea,
            success:function(data){
                $("#cb_outlet").html(data);
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
</script>
    
<?PHP
mysqli_close($cnmy);
?>