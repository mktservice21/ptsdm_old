<?PHP
    $fkaryawan=$_SESSION['IDCARD'];
    $fstsadmin=$_SESSION['STSADMIN'];
    $flvlposisi=$_SESSION['LVLPOSISI'];
    $fdivisi=$_SESSION['DIVISI'];
    
    $aksi="eksekusi3.php";
    $hari_ini = date("Y-m-d");
    $tgl_pertama = date('F Y', strtotime($hari_ini));
    $tgl_tempostnk = date('F Y', strtotime('+1 month', strtotime($hari_ini)));
?>
<div class="">

    <div class="page-title"><div class="title_left"><h3>Daftar Kendaraan</h3></div></div><div class="clearfix"></div>
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
                                
                                <div hidden class='form-group'>
                                    <label class='control-label col-md-6 col-sm-6 col-xs-12' for=''>Tahun <span class='required'></span></label>
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
                                    <label class='control-label col-md-6 col-sm-6 col-xs-12' for=''>Status Kendaraan <span class='required'></span></label>
                                    <div class='col-md-6'>
                                        <div class="form-group">
                                            <select class='form-control input-sm' id='e_ststkendaraan' name='e_ststkendaraan'>
                                            <?PHP
                                                
                                                echo "<option value='' selected>-- All --</option>";
                                                echo "<option value='AKTIF'>Aktif</option>";
                                                echo "<option value='JUAL'>Di Jual</option>";
                                                echo "<option value='TIDAKTERPAKAI'>Tidak Terpakai</option>";

                                            ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-6 col-sm-6 col-xs-12' for=''>Bulan Jatuh Tempo STNK <input type="checkbox" id="chktgl" name="chktgl" onclick="myShowHide()" value="Y"> <span class='required'></span></label>
                                    <div class='col-md-6'>
                                        <div class="form-group">
                                            <div id="divtglakhir">
                                            <div class='input-group date' id='cbln01'>
                                                <input type='text' id='e_blnstnk' name='e_blnstnk' required='required' class='form-control' placeholder='tgl awal' value='<?PHP echo $tgl_tempostnk; ?>' placeholder='mmm yyyy' Readonly>
                                                <span class="input-group-addon">
                                                   <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                            </div>
                                            </div>
                                        </div>
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
    $(document).ready(function() {
        var xchec=$("#chktgl").is(":checked");
        if (xchec==false) {
            var x = document.getElementById("divtglakhir");
            x.style.display = "none";
        }
    } );

    function myShowHide() {
        var xchec=$("#chktgl").is(":checked");
        var x = document.getElementById("divtglakhir");
        if (xchec==false) {
            x.style.display = "none";
        }else{
            x.style.display = "block";
        }


    }

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