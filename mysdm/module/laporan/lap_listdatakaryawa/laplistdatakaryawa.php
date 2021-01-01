<div class="">

    <div class="col-md-12 col-sm-12 col-xs-12"><div class="title_left"><h3>Rekap Data Karyawan</h3></div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">
        <?php
        
        $fkaryawan=$_SESSION['IDCARD'];
        $pmyjabatanid=$_SESSION['JABATANID'];
        $pmygroupid=$_SESSION['GROUP'];
        $pmobilepilih=$_SESSION['MOBILE'];
        
        $pidcabangpil="";
        
    
        $aksi="eksekusi3.php";
        switch($_GET['act']){
            default:

                $hari_ini = date("Y-m-d");
                $tgl_pertama = date('F Y', strtotime($hari_ini));
                $tgl_akhir = date('F Y', strtotime($hari_ini));
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
                                    <?PHP if ($pmobilepilih!="Y") { ?>
                                        <button type='button' class='btn btn-danger' onclick="disp_confirm('excel')">Excel</button>
                                    <?PHP } ?>
                                    <a class='btn btn-default' href="<?PHP echo "?module=home"; ?>">Home</a>
                                </h2>
                                <div class='clearfix'></div>
                            </div>

                            <!--kiri-->
                            <div class='col-md-6 col-xs-12'>
                                <div class='x_panel'>
                                    <div class='x_content form-horizontal form-label-left'><br />
                                        
                                        
                                        <div class='form-group grp-periode'>
                                            <label class='control-label control-periode col-md-4 col-sm-4 col-xs-12' for=''>Bulan Masuk &nbsp;<input type="checkbox" id="chk_masuk" name="chk_masuk" onclick="cekBoxPilihMasuk()" value="Y"><span class='required'></span></label>
                                            <div class='col-md-6'>
                                                <div class="form-group grp-periode">
                                                    <div class='input-group date grp-periode' id='cbln01'>
                                                        <input type='hidden' id='e_periode01_' name='e_periode01_' required='required' class='form-control' placeholder='tgl awal' value='<?PHP echo $tgl_pertama; ?>' placeholder='dd mmm yyyy' Readonly>
                                                        <input type='text' id='e_periode01' name='e_periode01' required='required' class='form-control' placeholder='tgl awal' value='<?PHP echo ""; ?>' placeholder='dd mmm yyyy' Readonly>
                                                        <span class="input-group-addon">
                                                           <span class="glyphicon glyphicon-calendar"></span>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class='form-group grp-periode'>
                                            <label class='control-label control-periode col-md-4 col-sm-4 col-xs-12' for=''>Bulan Keluar &nbsp;<input type="checkbox" id="chk_keluar" name="chk_keluar" onclick="cekBoxPilihKeluar()" value="Y"><span class='required'></span></label>
                                            <div class='col-md-6'>
                                                <div class="form-group grp-periode">
                                                    <div class='input-group date grp-periode' id='cbln02'>
                                                        <input type='hidden' id='e_periode02_' name='e_periode02_' required='required' class='form-control' placeholder='tgl awal' value='<?PHP echo $tgl_akhir; ?>' placeholder='dd mmm yyyy' Readonly>
                                                        <input type='text' id='e_periode02' name='e_periode02' required='required' class='form-control' placeholder='tgl awal' value='<?PHP echo ""; ?>' placeholder='dd mmm yyyy' Readonly>
                                                        <span class="input-group-addon">
                                                           <span class="glyphicon glyphicon-calendar"></span>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-4 col-sm-4 col-xs-12' for=''>Divisi <span class='required'></span></label>
                                            <div class='col-xs-7'>
                                                <select class='form-control' name='cb_divisi' id='cb_divisi' style='width: 100%;' onchange="">
                                                    <option value='' selected>--All--</option>
                                                    <?PHP
                                                    $query = "select divprodid, nama from MKT.divprod WHERE br='Y' order by divprodid";
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
                                            <label class='control-label col-md-4 col-sm-4 col-xs-12' for=''>Jabatan <span class='required'></span></label>
                                            <div class='col-xs-7'>
                                                <select class='form-control' name='cb_jabatan' id='cb_jabatan' style='width: 100%;' onchange="">
                                                    <option value='' selected>--All--</option>
                                                    <?PHP
                                                    $query = "select jabatanid, nama from hrd.jabatan order by jabatanid";
                                                    $tampil= mysqli_query($cnmy, $query);
                                                    while($na= mysqli_fetch_array($tampil)) {
                                                        $nijbtid=$na['jabatanid'];
                                                        $ninmjbt=$na['nama'];
                                                        echo "<option value='$nijbtid'>$nijbtid - $ninmjbt</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-4 col-sm-4 col-xs-12' for=''>Status Aktif <span class='required'></span></label>
                                            <div class='col-xs-7'>
                                                <select class='form-control' name='cb_aktif' id='cb_aktif' style='width: 100%;' onchange="">
                                                    <option value='' >--All--</option>
                                                    <option value='A' selected>Hanya Yang Aktif</option>
                                                    <option value='T'>Sudah Tidak Aktif</option>
                                                    
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-4 col-sm-4 col-xs-12' for=''>Tanpa NN <span class='required'></span></label>
                                            <div class='col-xs-6'>
                                                <input type="checkbox" value="NN" id="chk_nn" name="chk_nn" checked>
                                            </div>
                                        </div>
                                        
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-4 col-sm-4 col-xs-12' for=''>Urutkan Sesuai <span class='required'></span></label>
                                            <div class='col-xs-7'>
                                                <select class='form-control' name='cb_urutkan' id='cb_urutkan' style='width: 100%;' onchange="">
                                                    <option value='A' selected>Tgl. Masuk Terakhir</option>
                                                    <option value='B' >Nama Karyawan</option>
                                                    <option value='C' >ID Karyawan</option>
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        
                                    </div>
                                </div>
                            </div>
                            <!--end kiri-->
                            
                            
                            
                            
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