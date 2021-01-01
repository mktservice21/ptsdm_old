<div class="">

    <div class="col-md-12 col-sm-12 col-xs-12"><div class="title_left"><h3>Report Sales Year To Date (YTD) Daerah</h3></div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">
        <?php
        $aksi="eksekusi_ytd.php";
        switch($_GET['act']){
            default:
                include "config/koneksimysqli_it.php";
                ?>
                <div class='col-md-12 col-sm-12 col-xs-12'>
                    <div class='x_panel'>
                        <form name='form1' id='form1' method='POST' action="<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]"; ?>"  enctype='multipart/form-data'>
                            <div class='col-md-12 col-sm-12 col-xs-12'>
                                <h2>
                                    <input type='button' value='Back' onclick='self.history.back()' class='btn btn-default'>
                                    <button class='btn btn-primary' type='reset'>Reset</button>
                                    <button type='submit' class='btn btn-success'>Preview</button>
                                </h2>
                                <div class='clearfix'></div>
                            </div>

                            <!--kiri-->
                            <div class='col-md-6 col-xs-12'>
                                <div class='x_panel'>
                                    <div class='x_content form-horizontal form-label-left'><br />
                                        <?PHP


                                            $hari_ini = date("Y-m-d");
                                            $tgl_pertama = date('01 F Y', strtotime($hari_ini));

                                            echo "<div class='form-group'>";
                                            echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='tgl01'>Peiode <span class='required'></span></label>";
                                            echo "<div class='col-md-9 col-sm-9 col-xs-12'>
                                                <input type='text' id='tgl01' name='e_periode01' required='required' class='form-control col-md-7 col-xs-12' placeholder='tgl lahir' value='$tgl_pertama' placeholder='dd mmm yyyy' Readonly>
                                                </div>";
                                            echo "</div>";

                                            echo "<div class='form-group'>";
                                                 echo "<label class='control-label col-md-3 col-sm-3 col-xs-12'>Regional <span class='required'></span></label>";
                                                 echo "<div class='col-md-9 col-sm-9 col-xs-12'>";
                                                 
                                                    echo "<select class='form-control' name='e_region' id='e_region'>";
                                                    echo "<option value='B'>B - Barat</option>";
                                                    echo "<option value='T'>T - Timur</option>";
                                                    echo "</select>";
                                                    
                                                 echo "</div>";
                                            echo "</div>";

                                            echo "<div class='form-group'>";
                                                 echo "<label class='control-label col-md-3 col-sm-3 col-xs-12'>Nama SM <span class='required'></span></label>";
                                                 echo "<div class='col-md-9 col-sm-9 col-xs-12'>";
                                                 
                                                    echo "<select class='form-control' name='e_sm' id='e_sm'>";
                                                    $ssqcombo="SELECT karyawanId, nama FROM hrd.karyawan where jabatanId=20 order by nama";
                                                    $tampilcombo=mysqli_query($cnit, $ssqcombo);
                                                    echo "<option value='0' selected>- Pilih -</option>";
                                                    while($comb=mysqli_fetch_array($tampilcombo)){
                                                        echo "<option value='$comb[karyawanId]'>$comb[nama]</option>";
                                                    }
                                                    echo "</select>";
                                                    
                                                 echo "</div>";
                                            echo "</div>";

                                        ?>
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