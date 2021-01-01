<?PHP
    $jabatan_="";
    $fildiv="";
    $tampilbawahan = "N";
    $hanyasatukaryawan = "";
    $fildiv = "('OTC')";
    if (!empty($_SESSION['AKSES_JABATAN'])) {
        $jabatan_ = $_SESSION['AKSES_JABATAN'];
    }
    $hari_ini = date("Y-m-d");
    $tgl_pertama = date('F Y', strtotime($hari_ini));
    $tgl_terakhir = date('F Y', strtotime($hari_ini));
    
    $bulan = date('Ym', strtotime($hari_ini));
    
    $aksi="eksekusi3.php";
    //include "config/koneksimysqli_it.php";
    
?>
<div class="">

    <div class="page-title"><div class="title_left"><h3>Laporan Gaji SPG</h3></div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">

        <?php

        ?>
        <form method='POST' action='<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]"; ?>' id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left' target="_blank">
            <div class='col-md-12 col-sm-12 col-xs-12'>
                <div class='x_panel'>

                    <div class='col-md-12 col-sm-12 col-xs-12'>
                        <h2>
                            <button type='button' class='btn btn-success' onclick="disp_confirm('')">Preview</button>
                            <button type='button' class='btn btn-danger' onclick="disp_confirm('excel')">Excel</button>
                        </h2>
                        <div class='clearfix'></div>
                    </div>

                    <!--kiri-->
                    <div class='col-md-6 col-xs-12'>
                        <div class='x_panel'>
                            <div class='x_content form-horizontal form-label-left'><br />
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Cabang <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='form-control' id="icabangid_o" name="icabangid_o">
                                            <?PHP
                                            //$query = "select icabangid_o, nama from MKT.icabang_o WHERE aktif='Y' AND nama NOT IN ('OTHER1', 'OTHER2') ORDER BY nama";
                                            $query = "select icabangid_o, nama from dbmaster.v_icabang_o WHERE aktif='Y' AND i_spg='Y' AND nama NOT IN ('OTHER1', 'OTHER2') ";
                                            if ($_SESSION['GROUP']=="1" OR $_SESSION['GROUP']=="23" OR $_SESSION['GROUP']=="24" OR $_SESSION['GROUP']=="26" OR $_SESSION['GROUP']=="37" OR $_SESSION['GROUP']=="38") {
                                                echo "<option value='' selected>(All)</option>";
                                            }else{
                                                
                                                $icabang=$_SESSION['IDCABANG'];
                                                
                                                if ($_SESSION['ALOKASIID']=="JKT_MT" OR $_SESSION['ALOKASIID']=="JKT_RETAIL") {
                                                    $icabang=$_SESSION['ALOKASIID'];
                                                    $query .= " AND icabangid_o='$icabang' ";
                                                }else{
                                                    $query .= " AND icabangid_o IN (SELECT icabangid FROM dbmaster.otc_cabang_apv WHERE karyawanid='$_SESSION[IDCARD]')";
                                                }
                                            }
                                            $query .= " ORDER BY nama";
                                            
                                            $tampil=mysqli_query($cnmy, $query);
                                            //$tampil=mysqli_query($cnmy, "SELECT distinct icabangid_o, nama from dbmaster.v_icabang_o where aktif='Y' order by nama");
                                            while($s= mysqli_fetch_array($tampil)) {
                                                $pcabangid=$s['icabangid_o'];
                                                $pnmcabang=$s['nama'];
                                                if ($pcabangid==$icabang)
                                                    echo "<option value='$pcabangid' selected>$pnmcabang</option>";
                                                else
                                                    echo "<option value='$pcabangid'>$pnmcabang</option>";
                                                
                                                //echo "<option value='$s[icabangid_o]'>$s[nama]</option>";
                                            }
											
											
											
                                            $query = "select icabangid_o, nama from dbmaster.v_icabang_o WHERE aktif<>'Y' AND i_spg='Y' AND nama NOT IN ('OTHER1', 'OTHER2') ";
                                            if ($_SESSION['GROUP']=="1" OR $_SESSION['GROUP']=="23" OR $_SESSION['GROUP']=="24" OR $_SESSION['GROUP']=="26" OR $_SESSION['GROUP']=="37" OR $_SESSION['GROUP']=="38") {
                                                
                                            }else{
                                                
                                                $icabang=$_SESSION['IDCABANG'];
                                                
                                                if ($_SESSION['ALOKASIID']=="JKT_MT" OR $_SESSION['ALOKASIID']=="JKT_RETAIL") {
                                                    $icabang=$_SESSION['ALOKASIID'];
                                                    $query .= " AND icabangid_o='$icabang' ";
                                                }else{
                                                    $query .= " AND icabangid_o IN (SELECT icabangid FROM dbmaster.otc_cabang_apv WHERE karyawanid='$_SESSION[IDCARD]')";
                                                }
                                            }
                                            $query .= " ORDER BY nama";
                                            
                                            $tampil=mysqli_query($cnmy, $query);
                                            $ketemu=mysqli_num_rows($tampil);
											if ($ketemu>0) {
												echo "<option value='NNNNONAKTIF'>-- Non Aktif --</option>";
												while($s= mysqli_fetch_array($tampil)) {
													$pcabangid=$s['icabangid_o'];
													$pnmcabang=$s['nama'];
													
													echo "<option value='$pcabangid'>$pnmcabang</option>";
													
													
												}
											}
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Periode <span class='required'></span></label>
                                    <div class='col-md-6'>
                                        <div class="form-group">
                                            <div class='input-group date' id='cbln01'>
                                                <input type='text' id='bulan1' name='bulan1' required='required' class='form-control' placeholder='tgl awal' value='<?PHP echo $tgl_pertama; ?>' placeholder='dd mmm yyyy' Readonly>
                                                <span class="input-group-addon">
                                                   <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Status <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='form-control' id="e_tipe" name="e_tipe">
                                            <option value="1">Belum Proses</option>
                                            <option value="2" selected>Sudah Proses Admin OTC</option>
                                            <option value="3">Sudah Proses Finance OTC</option>
                                            <option value="4">Sudah Proses Head of Sales</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div id="div_submit">

                                </div>

                            </div>
                        </div>           
                    </div>

                </div>
            </div>
        </form>

    </div>
    <!--end row-->
</div>

<script>
    function disp_confirm(pText)  {
        
        var ecabang = document.getElementById('icabangid_o').value;
        var etipe = document.getElementById('e_tipe').value;
        
        if (etipe=="1") {
            if (ecabang=="") {
                alert("Untuk Pilihan Status Belum Proses, Cabang harus dipilih...");
                return false;
            }
        }
        
        if (pText == "excel") {
            document.getElementById("demo-form2").action = "<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]&ket=excel"; ?>";
            document.getElementById("demo-form2").submit();
            return 1;
        }else{
            document.getElementById("demo-form2").action = "<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]&ket=bukan"; ?>";
            document.getElementById("demo-form2").submit();
            return 1;
        }
    }
    
    $('#cbln01').on('change dp.change', function(e){
        CekYangBelumSubmit();
    });
    
    function CekYangBelumSubmit(){
        var itgl = document.getElementById('bulan1').value;
        $.ajax({
            type:"post",
            url:"module/laporan/mod_spg_lapgaji/viewdata.php?module=viewcekdatasubmit",
            data:"utgl="+itgl,
            success:function(data){
                $("#div_submit").html(data);
            }
        });
    }
</script>