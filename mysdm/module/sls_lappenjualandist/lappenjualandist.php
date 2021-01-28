<?PHP
include "config/cek_akses_modul.php";
include "config/koneksimysqli_ms.php";
$pidcard=$_SESSION['IDCARD'];
?>
<div class="">

    <div class="col-md-12 col-sm-12 col-xs-12"><div class="title_left"><h3>Laporan Penjualan Distributor Ethical</h3></div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">
        <?php
        $aksi="eksekusi3.php";
        switch($_GET['act']){
            default:

                $hari_ini = date("Y-m-d");
                $tgl_pertama = date('F Y', strtotime($hari_ini));
                $tgl_akhir = date('t F Y', strtotime($hari_ini));
                ?>
                <script>
                    
                    function disp_confirm(pText)  {
                        if (pText == "excel") {
                            document.getElementById("form1").action = "<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]&ket=excel"; ?>";
                            document.getElementById("form1").submit();
                            return 1;
                        }else{
                            document.getElementById("form1").action = "<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]&ket=bukan"; ?>";
                            document.getElementById("form1").submit();
                            return 1;
                        }
                    }
                    
                    function ShowDataEcabangDist() {
                        var ecabang=document.getElementById('distid').value;
                        
                        $.ajax({
                            type:"post",
                            url:"module/sls_lappenjualandist/viewdata.php?module=caridataecust",
                            data:"ucabang="+ecabang,
                            success:function(data){
                                $("#ecabangid").html(data);
                            }
                        });
                    }
                </script>
                
                <style>
                    .grp-periode, .input-periode, .control-periode {
                        margin-bottom:2px;
                    }
                </style>
                
                <div class='col-md-12 col-sm-12 col-xs-12'>
                    <div class='x_panel'>
                        <form name='form1' id='form1' method='POST' action="<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]"; ?>" enctype='multipart/form-data' target="_blank">
                            <div class='col-md-12 col-sm-12 col-xs-12'>
                                <h2>
                                    <button type='button' class='btn btn-success' onclick="disp_confirm('')">Preview</button>
                                    <button type='button' class='btn btn-danger' onclick="disp_confirm('excel')">Excel</button>
                                    <a class='btn btn-default' href="<?PHP echo "?module=home"; ?>">Home</a>
                                </h2>
                                <div class='clearfix'></div>
                            </div>

                            <!--kiri-->
                            <div class='col-md-6 col-xs-12'>
                                <div class='x_panel'>
                                    <div class='x_content form-horizontal form-label-left'><br />
                                        
                                        
                                        <div class='form-group grp-periode'>
                                            <label class='control-label control-periode col-md-3 col-sm-3 col-xs-12' for=''>Periode <span class='required'></span></label>
                                            <div class='col-md-6'>
                                                <div class="form-group grp-periode">
                                                    <div class='input-group date grp-periode' id='cbln01'>
                                                        <input type='text' id='e_periode01' name='e_periode01' required='required' class='form-control' placeholder='tgl awal' value='<?PHP echo $tgl_pertama; ?>' placeholder='dd mmm yyyy' Readonly>
                                                        <span class="input-group-addon">
                                                           <span class="glyphicon glyphicon-calendar"></span>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        
                                        <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Distributor <span class='required'></span></label>
                                            <div class='col-md-9 col-sm-9 col-xs-12'>
                                                <select class='form-control input-sm' name='distid' id='distid' onchange="ShowDataEcabangDist()">
                                                <?PHP
                                                    $pinsel="('0000000002', '0000000003', '0000000005', '0000000006', '0000000010', "
                                                            . " '0000000011', '0000000016', '0000000023', '0000000030', '0000000031')";
                                                    //cComboDistibutorHanya('', '', $pinsel);

                                                    //cComboDistibutor('', '');

                                                    $sql=mysqli_query($cnmy, "SELECT distinct Distid, nama, alamat1 from dbmaster.distrib0 WHERE"
                                                            . " IFNULL(aktif,'') <> 'N' order by nama");
                                                    echo "<option value=''>~ All ~</option>";
                                                    while ($Xt=mysqli_fetch_array($sql)){
                                                        $pdisid=$Xt['Distid'];
                                                        $pdisnm=$Xt['nama'];
                                                        $cidcek=(INT)$pdisid;
                                                        echo "<option value='$pdisid'>$pdisnm ($cidcek)</option>";
                                                    }
                                                    /*
                                                    $sql=mysqli_query($cnmy, "SELECT distinct Distid, nama, alamat1 from dbmaster.distrib0 WHERE"
                                                            . " Distid NOT IN $pinsel order by Distid, nama");
                                                    echo "<option value=''></option>";
                                                    while ($Xt=mysqli_fetch_array($sql)){
                                                        $pdisid=$Xt['Distid'];
                                                        $pdisnm=$Xt['nama'];
                                                        $cidcek=(INT)$pdisid;
                                                        echo "<option value='$pdisid'>$cidcek - $pdisnm</option>";
                                                    }
                                                    */

                                                ?>
                                                </select>
                                            </div>
                                        </div>

                                        
                                        <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Cabang Dist. <span class='required'></span></label>
                                            <div class='col-md-9 col-sm-9 col-xs-12'>
                                                <select class='form-control' name='ecabangid' id='ecabangid'>
                                                    <option value='' selected>~ All ~</option>
                                                    <?PHP
                                                    
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12'>Divisi <span class='required'></span></label>
                                            <div class='col-md-9 col-sm-9 col-xs-12'>
                                                <select class='form-control' name='divprodid' id='divprodid' onchange="">
                                                    <?PHP
                                                        echo "<option value='A' selected>ALL</option>";
                                                        echo "<option value='E'>ETHICAL</option>";
                                                        echo "<option value='N'>N/A</option>";
                                                        echo "<option value='O'>OTC</option>";
                                                        // tambahan 20170124
                                                        echo "<option value='P'>PEACOCK</option>";
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12'>Jenis <span class='required'></span></label>
                                            <div class='col-md-9 col-sm-9 col-xs-12'>
                                                <select class='form-control' name='pilihan' id='pilihan'>
                                                    <?PHP
                                                    echo "<option value='A' selected>ALL</option>";
                                                    echo "<option value='D'>DISPENSING</option>";
                                                    echo "<option value='RE'>REGULER</option>";
                                                    echo "<option value='R'>RETUR</option>";
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12'>Cust. Blank <span class='required'></span></label>
                                            <div class='col-md-9 col-sm-9 col-xs-12'>
                                                <input type="checkbox" id="chkBlank" name="chkBlank" value="1">
                                            </div>
                                        </div>
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12'>Qty tidak 0 <span class='required'></span></label>
                                            <div class='col-md-9 col-sm-9 col-xs-12'>
                                                <input type="checkbox" id="chkZero" name="chkZero" value="1">
                                            </div>
                                        </div>
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12'>Qty Bonus tidak 0 <span class='required'></span></label>
                                            <div class='col-md-9 col-sm-9 col-xs-12'>
                                                <input type="checkbox" id="chkBonus" name="chkBonus" value="1">
                                            </div>
                                        </div>
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12'>View Bonus <span class='required'></span></label>
                                            <div class='col-md-9 col-sm-9 col-xs-12'>
                                                <input type="checkbox" id="chkDataBonus" name="chkDataBonus" value="1">
                                            </div>
                                        </div>
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12'>View All Data <span class='required'></span></label>
                                            <div class='col-md-9 col-sm-9 col-xs-12'>
                                                <input type="checkbox" id="chkAll" name="chkAll" value="1">
                                            </div>
                                        </div>
                                        
                                        
                                    </div>
                                </div>
                            </div>
                            <!--end kiri-->

                        </form>
                    </div><!--end xpanel-->
                </div>
			
				
				
                <?PHP
            break;

            case "tambahbaru":

            break;
        }
        ?>
    </div>
    <!--end row-->
</div>