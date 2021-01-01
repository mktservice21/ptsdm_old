

<?PHP
    $fkaryawan=$_SESSION['IDCARD'];
    $fstsadmin=$_SESSION['STSADMIN'];
    $flvlposisi=$_SESSION['LVLPOSISI'];
    $fdivisi=$_SESSION['DIVISI'];
?>
<!-- Modal -->
<div class='modal fade' id='myModal' role='dialog'></div>

<div class="">

    <div class="page-title"><div class="title_left"><h3>Laporan BR By Tanggal</h3></div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">

        <?php
        $aksi="eksekusi3.php";
        switch($_GET['act']){
            default:
                include "config/koneksimysqli_it.php";
                $hari_ini = date("Y-m-d");
                $tgl_pertama = date('01 F Y', strtotime($hari_ini));
                $tgl_akhir = date('d F Y', strtotime($hari_ini));
                ?>
                <form name='form1' id='form1' method='POST' action="<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]"; ?>"  enctype='multipart/form-data' target="_blank">
                    <div class='col-md-12 col-sm-12 col-xs-12'>
                        <div class='x_panel'>

                            <div class='col-md-12 col-sm-12 col-xs-12'>
                                <h2>
                                    <button type='submit' class='btn btn-success'>Preview</button>
                                </h2>
                                <div class='clearfix'></div>
                            </div>
                            
                            <!--kiri-->
                            <div class='col-md-6 col-xs-12'>
                                <div class='x_panel'>
                                    <div class='x_content form-horizontal form-label-left'><br />
                                        
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_idcabang'>Cabang <span class='required'></span></label>
                                            <div class='col-xs-9'>
                                                <select class='form-control' id='e_idcabang' name='e_idcabang'>
                                                    <option value='' selected>-- Pilihan --</option>
                                                    <?PHP
                                                      $tampil=mysqli_query($cnit, "SELECT distinct icabangid_o, nama from dbmaster.v_icabang_o where aktif='Y'");
                                                      while($a=mysqli_fetch_array($tampil)){
                                                          echo "<option value='$a[icabangid_o]'>$a[nama]</option>";
                                                      }
                                                      ?>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for='tgl01'>Tgl. Transfer <span class='required'></span></label>
                                            <div class='col-md-6'>
                                                <div class="form-group">
                                                    <div class='input-group date' id='tgl01'>
                                                        <input type='text' id='tgl01' name='e_periode01' required='required' class='form-control' placeholder='tgl awal' value='<?PHP echo $tgl_pertama; ?>' placeholder='dd mmm yyyy' Readonly>
                                                        <span class="input-group-addon">
                                                           <span class="glyphicon glyphicon-calendar"></span>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_lampiran'>Lampiran <span class='required'></span></label>
                                            <div class='col-xs-9'>
                                                <select class='form-control' id="cb_lampiran" name="cb_lampiran">
                                                    <option value="" selected>All</option>
                                                    <option value="1" >Ada</option>
                                                    <option value="2" >Tidak Ada</option>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        
                                        
                                        
                                        
                                        
                                    </div>
                                </div>           
                            </div>
                            
                        </div>
                    </div>
                </form>
                <?PHP
            break;

        }
        ?>

    </div>
    <!--end row-->
</div>

