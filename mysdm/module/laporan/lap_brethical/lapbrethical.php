<!-- Modal -->
<div class='modal fade' id='myModal' role='dialog'></div>
<div class="">

    <div class="col-md-12 col-sm-12 col-xs-12"><div class="title_left"><h3>Laporan Budget Request Ethical</h3></div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">
        <?php
        
        $fkaryawan=$_SESSION['IDCARD'];
        $pmyjabatanid=$_SESSION['JABATANID'];
        $pmygroupid=$_SESSION['GROUP'];
        $pidcabangpil="";
        
    
        $aksi="eksekusi3.php";
        switch($_GET['act']){
            default:

                $hari_ini = date("Y-m-d");
                $tgl_pertama = date('01 F Y', strtotime($hari_ini));
                $tgl_akhir = date('d F Y', strtotime($hari_ini));
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
                    
                    function getDataDokter(data1, data2){
                        $.ajax({
                            type:"post",
                            url:"module/laporan/lap_brethical/viewdata_drlap.php?module=viewdatadokter",
                            data:"udata1="+data1+"&udata2="+data2+"&uidkry=",
                            success:function(data){
                                $("#myModal").html(data);
                                document.getElementById(data1).value="";
                                document.getElementById(data2).value="";
                            }
                        });
                    }
                    
                    function getDataModalDokter(fildnya1, fildnya2, d1, d2){
                        document.getElementById(fildnya1).value=d1;
                        document.getElementById(fildnya2).value=d2;
                    }

                    function HapusDataDokter(){
                        document.getElementById('e_iddokt').value="";
                        document.getElementById('e_nmdokt').value="";
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
                                        
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_dokter'>Periode By <span class='required'></span></label>
                                            <div class='col-xs-9'>
                                                <select class='form-control input-sm' id="cb_tgltipe" name="cb_tgltipe">
                                                    <option value="1" selected>Tanggal Transfer</option>
                                                    <option value="2">Tanggal Input/Pengajuan</option>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        
                                        <div class='form-group grp-periode'>
                                            <label class='control-label control-periode col-md-3 col-sm-3 col-xs-12' for='tgl01'>Periode <span class='required'></span></label>
                                            <div class='col-md-6'>
                                                <div class="form-group grp-periode">
                                                    <div class='input-group date grp-periode' id='tgl01'>
                                                        <input type='text' id='tgl01' name='e_periode01' required='required' class='form-control' placeholder='tgl awal' value='<?PHP echo $tgl_pertama; ?>' placeholder='dd mmm yyyy' Readonly>
                                                        <span class="input-group-addon">
                                                           <span class="glyphicon glyphicon-calendar"></span>
                                                        </span>
                                                    </div>
                                                    <div class='input-group date' id='tgl02'>
                                                        <input type='text' id='tgl02' name='e_periode02' required='required' class='form-control' placeholder='tgl akhir' value='<?PHP echo $tgl_akhir; ?>' placeholder='dd mmm yyyy' Readonly>
                                                        <span class="input-group-addon">
                                                           <span class="glyphicon glyphicon-calendar"></span>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
                                                <button type='button' class='btn btn-info btn-xs' onclick='HapusDataDokter()'>User</button> <span class='required'></span>
                                                <span class='required'></span></label>
                                            <div class='col-xs-9'>
                                                <div class='input-group '>
                                                    <span class='input-group-btn'>
                                                        <button type='button' class='btn btn-primary' data-toggle='modal' data-target='#myModal' onClick="getDataDokter('e_iddokt', 'e_nmdokt')">Pilih!</button>
                                                    </span>
                                                    <input type='hidden' class='form-control' id='e_iddokt' name='e_iddokt' value='<?PHP echo ""; ?>' Readonly>
                                                    <input type='text' class='form-control' id='e_nmdokt' name='e_nmdokt' value='<?PHP echo ""; ?>' Readonly>
                                                </div>
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>
                            <!--end kiri-->
                            
                            
                            <!--kanan-->
                            <div class='col-md-6 col-xs-12'>
                                <div class='x_panel'>
                                    <div class='x_content form-horizontal form-label-left'><br />
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Divisi <span class='required'></span></label>
                                            <div class='col-xs-9'>
                                                <select class='form-control' name='cb_divisi' id='cb_divisi' style='width: 100%;' onchange="">
                                                    <option value='' selected>--All--</option>
                                                    <?PHP
                                                    $query = "select divprodid, nama from MKT.divprod WHERE br='Y' and divprodid NOT IN ('OTHER', 'OTHERS', 'CAN', 'OTC') ";
                                                    if ($fkaryawan=="0000000159" OR $fkaryawan=="0000000158") {
                                                        $query .=" AND divprodid NOT IN ('HO')";
                                                    }
                                                    $query .=" order by divprodid";
                                                    $tampil= mysqli_query($cnmy, $query);
                                                    while($na= mysqli_fetch_array($tampil)) {
                                                        $niprodid=$na['divprodid'];
                                                        $niprodnm=$na['nama'];
                                                        echo "<option value='$niprodid'>$niprodnm</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_pilihtipe'>Pilih Tipe <span class='required'></span></label>
                                            <div class='col-xs-9'>
                                                <select class='form-control' name='e_pilihtipe' id='e_pilihtipe' style='width: 100%;' onchange="">
                                                <?PHP
                                                if ($fkaryawan=="0000000159" OR $fkaryawan=="0000000158") {
                                                ?>
                                                    <option value='DC' selected>DSS & DCC</option>
                                                    <option value='D'>DSS</option>
                                                    <option value='C'>DCC</option>
                                                <?PHP
                                                }else {

                                                ?>
                                                    <option value='' selected>--All--</option>
                                                    <option value='D'>DSS</option>
                                                    <option value='C'>DCC</option>
                                                    <option value='DC'>DSS & DCC</option>
                                                    <option value='N'>Non DSS & DCC</option>

                                                <?PHP
                                                }
                                                ?>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Cabang <span class='required'></span></label>
                                            <div class='col-xs-9'>
                                                <select class='form-control' name='cb_cabangid' id='cb_cabangid' style='width: 100%;' onchange="">
                                                    <option value='' selected>--All--</option>
                                                    <?PHP
                                                    if ($fkaryawan=="0000000159")
                                                        $query = "select icabangid, nama from MKT.icabang WHERE aktif='Y' AND region='T' order by nama";
                                                    elseif ($fkaryawan=="0000000158")
                                                        $query = "select icabangid, nama from MKT.icabang WHERE aktif='Y' AND region='B' order by nama";
                                                    else
                                                        $query = "select icabangid, nama from MKT.icabang WHERE aktif='Y' order by nama";
                                                    
                                                    $tampil= mysqli_query($cnmy, $query);
                                                    while($na= mysqli_fetch_array($tampil)) {
                                                        $nidcab=$na['icabangid'];
                                                        $nnmcab=$na['nama'];
                                                        echo "<option value='$nidcab'>$nnmcab</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Hanya Via SBY <span class='required'></span></label>
                                            <div class='col-xs-9'>
                                                <input type="checkbox" id="chk_viasby" name="chk_viasby" value="Y">
                                            </div>
                                        </div>
                                        
                                        
                                        
                                        
                                    </div>
                                </div>
                            </div>
                            <!--end kanan-->
                            
                            
                        </form>
                    </div><!--end xpanel-->
                </div>
                
                
                <script>
                    
                </script>

                
                <?PHP
            break;

            case "tambahbaru":

            break;
        }
        ?>
    </div>
    <!--end row-->
</div>