<?PHP
    include "config/cek_akses_modul.php";
    
    $fkaryawan=$_SESSION['IDCARD'];
    $fstsadmin=$_SESSION['STSADMIN'];
    $flvlposisi=$_SESSION['LVLPOSISI'];
    $fdivisi=$_SESSION['DIVISI'];
    
    $aksi="eksekusi3.php";
    $hari_ini = date("Y-m-d");
    $tgl_pertama = date('F Y', strtotime($hari_ini));
    
    $pmodule="";
    $pidmenu="";
    $pmobilelogin="N";
    if (isset($_GET['module'])) $pmodule=$_GET['module'];
    if (isset($_GET['idmenu'])) $pidmenu=$_GET['idmenu'];
    if (isset($_GET['MOBILE'])) $pmobilelogin=$_GET['MOBILE'];
    
    if ($pmodule=="laprinciankaskecilcabotc") {
        $pseldiv0="";
        $pseldiv1="";
        $pseldiv2="selected";
    }else{
        if ($fkaryawan=="0000000144") {
            $pseldiv0="";
            $pseldiv1="selected";
            $pseldiv2="";
        }else{
            $pseldiv0="selected";
            $pseldiv1="";
            $pseldiv2="";
        }
    }
?>
<div class="">

    <div class="page-title"><div class="title_left"><h3>Laporan Kas Kecil Cabang</h3></div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">

        <?php

        ?>
        <form method='POST' action='<?PHP echo "$aksi?module=$pmodule&act=input&idmenu=$pidmenu"; ?>' 
              id='d-form2' name='form2' data-parsley-validate class='form-horizontal form-label-left' target="_blank">
            
            <div class='col-md-12 col-sm-12 col-xs-12'>
                <div class='x_panel'>

                    <div class='col-md-12 col-sm-12 col-xs-12'>
                        <h2>
                            <button type='button' class='btn btn-success' onclick="disp_confirm('')">Preview</button>
                            <?PHP
                            if ($pmobilelogin!="Y") {
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
                                
                                
                                <div class='col-sm-6'>
                                    <b>Periode</b>
                                    <div class="form-group">
                                        <div class='input-group date' id='cbln01'>
                                            <input type='text' id='e_tgl1' name='e_tgl1' required='required' class='form-control' placeholder='tgl awal' value='<?PHP echo $tgl_pertama; ?>' placeholder='dd mmm yyyy' Readonly>
                                            <span class="input-group-addon">
                                               <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class='col-sm-6'>
                                    <b>s/d.</b>
                                    <div class="form-group">
                                        <div class='input-group date' id='cbln02'>
                                            <input type='text' id='e_tgl2' name='e_tgl2' required='required' class='form-control' placeholder='tgl awal' value='<?PHP echo $tgl_pertama; ?>' placeholder='dd mmm yyyy' Readonly>
                                            <span class="input-group-addon">
                                               <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <div class='col-sm-12'>
                                        <b>Divisi</b>
                                        <div class="form-group">
                                            <select class='form-control' id="cb_divisiid" name="cb_divisiid">
                                                <?PHP
                                                if ($fdivisi=="OTC" OR $fdivisi=="CHC") {
                                                    echo "<option value='OTC' $pseldiv2>CHC</option>";
                                                }else{
                                                    if ($fkaryawan=="0000000144") {
                                                        echo "<option value='ETH' $pseldiv1>ETHICAL</option>";
                                                    }else{
                                                        //echo "<option value='' $pseldiv0>--ALL--</option>";
                                                        echo "<option value='ETH' $pseldiv1 $pseldiv0>ETHICAL</option>";
                                                        echo "<option value='OTC' $pseldiv2>CHC</option>";
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <div class='col-sm-12'>
                                        <b>Status</b>
                                        <div class="form-group">
                                            <select class='form-control' id="cb_status" name="cb_status">
                                                <?PHP
                                                echo "<option value=''>-- ALL --</option>";
                                                echo "<option value='apvfin' selected>Sudah Proses Finance</option>";
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <div class='col-sm-12'>
                                        <b>Report By</b>
                                        <div class="form-group">
                                            <select class='form-control' id="cb_rptby" name="cb_rptby">
                                                <?PHP
                                                echo "<option value='rptbybln' selected>Bulan</option>";
                                                echo "<option value='rptbycab'>Cabang</option>";
                                                ?>
                                            </select>
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
    function disp_confirm(pText)  {
        if (pText == "excel") {
            document.getElementById("d-form2").action = "<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]&ket=excel"; ?>";
            document.getElementById("d-form2").submit();
            return 1;
        }else{
            document.getElementById("d-form2").action = "<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]&ket=bukan"; ?>";
            document.getElementById("d-form2").submit();
            return 1;
        }
    }

    
</script>