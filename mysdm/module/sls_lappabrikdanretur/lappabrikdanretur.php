<?PHP
include "config/cek_akses_modul.php";
?>
<div class="">

    <div class="col-md-12 col-sm-12 col-xs-12"><div class="title_left"><h3>Pabrik Sales dan Retur</h3></div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">
        <?php
        $aksi="eksekusi3.php";
        switch($_GET['act']){
            default:
                $pidgroup=$_SESSION['GROUP'];
                $pjabatanid=$_SESSION['JABATANID'];
                $hari_ini = date("Y-m-d");
                $tgl_pertama = date('01 F Y', strtotime($hari_ini));
                $tgl_akhir = date('t F Y', strtotime($hari_ini));
                $ptahunini = date('Y', strtotime($hari_ini));
                $pawalthn="2020";
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
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12'>Report <span class='required'></span></label>
                                            <div class='col-md-9 col-sm-9 col-xs-12'>

                                                <select class='form-control' name='e_pilrpt' id='e_pilrpt' onchange="">
                                                    <?PHP
                                                    if ($pjabatanid=="05") {
                                                        echo "<option value='R' selected>Retur</option>";
                                                    }else{
                                                        echo "<option value='S' selected>Sales</option>";
                                                        echo "<option value='R'>Retur</option>";
                                                    }
                                                    ?>
                                                </select>

                                            </div>
                                        </div>

                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12'>Periode <span class='required'></span></label>
                                            <div class='col-md-9 col-sm-9 col-xs-12'>

                                            <select class='form-control input-sm' id="cb_tahun" name="cb_tahun" onchange="">
                                                <option value="">All</option>

                                                <?PHP
                                                for($nthn=$pawalthn;$nthn<=$ptahunini;$nthn++) {
                                                    if ($nthn==$ptahunini)
                                                        echo "<option value='$nthn' selected>$nthn</option>";
                                                    else
                                                        echo "<option value='$nthn'>$nthn</option>";
                                                }
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
                
                
                
                <?PHP
            break;

            case "tambahbaru":

            break;
        }
        ?>
    </div>
    <!--end row-->
</div>