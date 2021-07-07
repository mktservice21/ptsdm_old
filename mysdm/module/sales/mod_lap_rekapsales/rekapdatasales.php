<?PHP
    $fkaryawan=$_SESSION['IDCARD'];
    $fstsadmin=$_SESSION['STSADMIN'];
    $flvlposisi=$_SESSION['LVLPOSISI'];
    $fdivisi=$_SESSION['DIVISI'];
    
    $aksi="eksekusi3.php";
    $hari_ini = date("Y-m-d");
    $tgl_pertama = date('F Y', strtotime($hari_ini));
    
    $pmodule=$_GET['module'];
    $pidmenu=$_GET['idmenu'];
    $pact=$_GET['act'];
    
?>
<div class="">

    <div class="page-title"><div class="title_left"><h3>Rekap Data Sales</h3></div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">

        <?php

        ?>
        <form method='POST' action='<?PHP echo "$aksi?module=$pmodule&act=laporan&idmenu=$pidmenu"; ?>' id='d-form1' name='form1' data-parsley-validate class='form-horizontal form-label-left' target="_blank">
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
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Tahun <span class='required'></span></label>
                                    <div class='col-md-6'>
                                        <div class="form-group">
                                            <div class='input-group date' id='thn01'>
                                                <input type='text' id='tahun' name='tahun' required='required' class='form-control' placeholder='tgl awal' value='<?PHP echo $tgl_pertama; ?>' placeholder='dd mmm yyyy' Readonly>
                                                <span class="input-group-addon">
                                                   <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Report Type <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='form-control' id="cb_rpttype" name="cb_rpttype">
                                            <?PHP
                                            echo "<option value='npildivisi' selected>Divisi</option>";                                                
                                            echo "<option value='npilbulan'>Bulan</option>";
                                            ?>
                                        </select>
                                    </div>
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
        if (pText == "excel") {
            document.getElementById("d-form1").action = "<?PHP echo "$aksi?module=$pmodule&act=laporan&idmenu=$pidmenu&ket=excel"; ?>";
            document.getElementById("d-form1").submit();
            return 1;
        }else{
            document.getElementById("d-form1").action = "<?PHP echo "$aksi?module=$pmodule&act=laporan&idmenu=$pidmenu&ket=bukan"; ?>";
            document.getElementById("d-form1").submit();
            return 1;
        }
    }
</script>