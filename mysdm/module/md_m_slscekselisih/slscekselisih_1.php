<div class="">

    <div class="col-md-12 col-sm-12 col-xs-12"><div class="title_left"><h3>Cek Selisih Closing Sales Per Distributor</h3></div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">
        <?php
        $aksi="eksekusi3.php";
        switch($_GET['act']){
            default:

                $hari_ini = date("Y-m-d");
                $tgl_pertama=date('F Y', strtotime('-1 month', strtotime($hari_ini)));
                //$tgl_pertama = date('F Y', strtotime($hari_ini));
                ?>
                <div class='col-md-12 col-sm-12 col-xs-12'>
                    <div class='x_panel'>
                        <form name='form1' id='form1' method='POST' action="<?PHP echo "?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]&act=preview"; ?>" enctype='multipart/form-data'>
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
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for='cbln01'>Periode <span class='required'></span></label>
                                            <div class='col-md-6'>
                                                <div class="form-group">
                                                    <div class='input-group date' id='cbln01'>
                                                        <input type='text' id='cbln01' name='bulan' required='required' class='form-control' placeholder='tgl awal' value='<?PHP echo $tgl_pertama; ?>' placeholder='dd mmm yyyy' Readonly>
                                                        <span class="input-group-addon">
                                                           <span class="glyphicon glyphicon-calendar"></span>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        
                                        <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_ket'>Distributor <span class='required'></span></label>
                                            <div class='col-md-9 col-sm-9 col-xs-12'>
                                                <select class='form-control' name='distibutor' id='distibutor'>
                                                    <?PHP
                                                        cComboDistibutor('', '2');
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12'>Regional <span class='required'></span></label>
                                            <div class='col-md-9 col-sm-9 col-xs-12'>

                                                <select class='form-control' name='region' id='e_region'>
                                                    <option value='B'>B - Barat</option>
                                                    <option value='T'>T - Timur</option>
                                                </select>

                                            </div>
                                        </div>
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12'> <span class='required'></span></label>
                                            <div class='col-md-9 col-sm-9 col-xs-12'>
                                                <input type=checkbox value='selisih' name='chkselisih' class='chkselisih' selected> Hanya Yang Selisih
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

            case "preview":
                include "aksi_slscekselisih.php";
            break;
        }
        ?>
    </div>
    <!--end row-->
</div>