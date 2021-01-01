<?PHP
    $jabatan_="";
    $fildiv="";
    $tampilbawahan = "N";
    $filkaryawncabang = "";
    $hanyasatukaryawan = "";
    $fildiv = "('OTC')";
    if (!empty($_SESSION['AKSES_JABATAN'])) {
        $jabatan_ = $_SESSION['AKSES_JABATAN'];
    }

    if (!empty($_SESSION['AKSES_CABANG'])) {
        $filkaryawncabang = $_SESSION['AKSES_CABANG'];
    }
    $hari_ini = date("Y-m-d");
    $tgl_pertama = date('Y', strtotime($hari_ini));
    
    
    $aksi="eksekusi3.php";
?>
<div class="">

    <div class="page-title"><div class="title_left"><h3>Realisasi Biaya Marketing Per Cabang</h3></div></div><div class="clearfix"></div>
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
                            <?PHP
                            if ($_SESSION['MOBILE']!="Y") {
                                echo "<button type='button' class='btn btn-danger' onclick=\"disp_confirm('excel')\">Excel</button>";
                            }
                            ?>
                            <a class='btn btn-default' href="<?PHP echo "?module=home"; ?>">Home</a>
                        </h2>
                        <div class='clearfix'></div>
                    </div>

                    <!--kiri-->
                    <div class='col-md-6 col-xs-12'>
                        <div class='x_panel'>
                            <div class='x_content form-horizontal form-label-left'><br />
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Divisi <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='form-control' id="cb_divisi" name="cb_divisi">
                                            <?PHP
                                            if ($_SESSION['DIVISI']=="OTC") {
                                                echo "<option value='OTC' selected>OTC</option>";
                                            }else{
                                                echo "<option value='ETHICAL' selected>ETHICAL</option>";
                                                echo "<option value='OTC'>OTC</option>";
                                            }
                                                
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Periode <span class='required'></span></label>
                                    <div class='col-md-6'>
                                        <div class="form-group">
                                            <div class='input-group date' id='thn01'>
                                                <input type='text' id='tglfrom' name='bulan1' required='required' class='form-control' placeholder='tgl awal' value='<?PHP echo $tgl_pertama; ?>' placeholder='dd mmm yyyy' Readonly>
                                                <span class="input-group-addon">
                                                   <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                            </div>
                                            
                                        </div>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Region <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='form-control' id="cb_region" name="cb_region">
                                            <?PHP
                                            echo "<option value=''>--All--</option>";
                                            echo "<option value='B' selected>Barat</option>";
                                            echo "<option value='T'>Timur</option>";   
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div hidden class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Report Type <span class='required'></span></label>
                                    <div class='col-md-6'>
                                        <input type="radio" id="radio1" name="radio1" value="S"> Sesuai SPD
                                        <input type="radio" id="radio1" name="radio1" value="A" checked> All
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
            document.getElementById("demo-form2").action = "<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]&ket=excel"; ?>";
            document.getElementById("demo-form2").submit();
            return 1;
        }else{
            document.getElementById("demo-form2").action = "<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]&ket=bukan"; ?>";
            document.getElementById("demo-form2").submit();
            return 1;
        }
    }
</script>