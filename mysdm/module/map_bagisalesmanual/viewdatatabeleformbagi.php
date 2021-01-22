<?php
    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","512M");
    ini_set('max_execution_time', 0);
    
    session_start();
    
    
    $puserid="";
    if (isset($_SESSION['USERID'])) $puserid=$_SESSION['USERID'];

    if (empty($puserid)) {
        echo "ANDA HARUS LOGIN ULANG...";
        exit;
    }
    
    $fkaryawan=$_SESSION['IDCARD'];
    $fjbtid=$_SESSION['JABATANID'];
    
    
    $piddist=$_POST['udistid'];
    $pidecab=$_POST['ucabid'];
    $pnmfilter=$_POST['unamafilter'];
    $pbln=$_POST['ubln'];
    $pbulan = date('Y-m', strtotime($pbln));
    
    include "../../config/koneksimysqli_ms.php";
    
    $pidcabang="";
    $pidarea="";
?>


    <div class='x_panel'>
        <div class='x_content'>

        <table>
            <tr><td nowrap colspan="3" style="font-weight:bold;">Pembagian Sales Manual : </td></tr>
        </table>
            
            <div class='col-md-12 col-sm-12 col-xs-12'>
                
                <div class='form-group'>
                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Cabang SDM <span class='required'></span></label>
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
                            $tampiledu= mysqli_query($cnms, $query);
                            while ($du= mysqli_fetch_array($tampiledu)) {
                                $nidcab=$du['icabangid'];
                                $nnmcab=$du['nama'];

                                if ($nidcab==$pidcabang) 
                                    echo "<option value='$nidcab' selected>$nnmcab ($nidcab)</option>";
                                else
                                    echo "<option value='$nidcab'>$nnmcab ($nidcab)</option>";

                            }
                            ?>
                        </select>
                    </div>
                </div>    
                
                <div class='form-group'>
                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Area SDM <span class='required'></span></label>
                    <div class='col-xs-4'>
                        <select class='soflow' name='cb_areaid' id='cb_areaid' onchange="ShowDataCustomer()">
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
                                        echo "<option value='$nidarea' selected>$nnmarea ($nidarea)</option>";
                                    else
                                        echo "<option value='$nidarea'>$nnmarea ($nidarea)</option>";

                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
                
                <div class='form-group'>
                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Customer SDM <span class='required'></span></label>
                    <div class='col-xs-4'>
                        <select class='soflow' name='cb_custid' id='cb_custid' onchange="">
                            <?php
                            echo "<option value='' selected>--Pilih--</option>";
                            ?>
                        </select>
                    </div>
                </div>
                
                
            </div>
            
            
        </div>
    </div>

<script>
    function ShowDataCabangArea() {
        var idcab=document.getElementById('cb_cabangid').value;
        $.ajax({
            type:"post",
            url:"module/map_bagisalesmanual/viewdatabagi.php?module=viewdataareacabang",
            data:"udcab="+idcab,
            success:function(data){
                $("#cb_areaid").html(data);
                ShowDataCustomer()
            }
        });
    }
    
    function ShowDataCustomer() {
        var idcab=document.getElementById('cb_cabangid').value;
        var idarea=document.getElementById('cb_areaid').value;
        $.ajax({
            type:"post",
            url:"module/map_bagisalesmanual/viewdatabagi.php?module=viewdatacustomer",
            data:"udcab="+idcab+"&udarea="+idarea,
            success:function(data){
                $("#cb_custid").html(data);
            }
        });
    }
</script>
<?PHP
hapusdata:
    
    mysqli_close($cnms);
?>