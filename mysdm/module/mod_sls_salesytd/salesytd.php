<?PHP include "module/mod_sls_salesytd/fungsi_combo.php"; ?>
<script src="module/mod_sls_salesytd/transaksi.js"></script>
<!--<script src="config/js/function_global.js" type="text/javascript"></script>-->

<script>
    function SelAllCheckBox(nmbuton, data){
        var checkboxes = document.getElementsByName(data);
        var button = document.getElementById(nmbuton);

        if(button.value == 'select'){
            for (var i in checkboxes){
                checkboxes[i].checked = 'FALSE';
            }
            button.value = 'deselect'
        }else{
            for (var i in checkboxes){
                checkboxes[i].checked = '';
            }
            button.value = 'select';
        }
    }
    
    function selectRegionCekBox(data){
        var cB = document.getElementById("B").checked;
        var cT = document.getElementById("T").checked;

        $.ajax({
            type:"post",
            url:"module/mod_sls_salesytd/viewdata.php?module=viewregioncab&data1="+cB+"&data2="+cB,
            data:"udata1="+cB+"&udata2="+cT,
            success:function(data){
                $("#kotak-multi2").html(data);
            }
        });
    }

</script>

<!-- Modal -->
<div class='modal fade' id='myModal' role='dialog'></div>


<div class="">

    <div class="col-md-12 col-sm-12 col-xs-12"><div class="title_left"><h3>Report Sales Year To Date (YTD) Product</h3></div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">
        <?php
        $aksi="eksekusi.php";
        switch($_GET['act']){
            default:
                ?>
                <div class='col-md-12 col-sm-12 col-xs-12'>
                    <div class='x_panel'>
                        <form name='form1' id='form1' method='POST' action="<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]"; ?>"  enctype='multipart/form-data' target="_blank">
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
                                            echo "<div class='form-group'>";
                                            echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_ket'>Report By <span class='required'></span></label>";
                                            echo "<div class='col-md-6 col-sm-6 col-xs-12'>
                                                    <div class='btn-group' data-toggle='buttons'>
                                                        <label class='btn btn-default'><input type='radio' class='flat' name='rb_rpttipe' id='rb_rptby1' value='P' checked> Person </label>
                                                        <label class='btn btn-default'><input type='radio' class='flat' name='rb_rpttipe' id='rb_rptby2' value='T'> Team </label>
                                                    </div>
                                                </div>";
                                            echo "</div>";

                                            echo "<div class='form-group'>
                                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_karyawan'>Employee <span class='required'></span></label>
                                                <div class='col-md-9 col-sm-9 col-xs-12'>
                                                    <div class='input-group '>
                                                        <span class='input-group-btn'>
                                                            <button type='button' class='btn btn-primary' data-toggle='modal' data-target='#myModal' onClick=\"getDataKaryawan('e_idkaryawan', 'e_karyawan')\">Go!</button>
                                                        </span>
                                                        <input type='text' class='form-control' id='e_idkaryawan' name='e_idkaryawan' value='$_SESSION[IDCARD]' Readonly>
                                                    </div>
                                                    <input type='text' class='form-control' id='e_karyawan' name='e_karyawan' value='$_SESSION[NAMALENGKAP]' Readonly>
                                                </div>
                                            </div>";

                                            $hari_ini = date("Y-m-d");
                                            $tgl_pertama = date('01 F Y', strtotime($hari_ini));
                                            $tgl_akhir = date('d F Y', strtotime($hari_ini));

                                            $gtahun = date("Y")-1;
                                            $gtgl_pertama = date('01 F ', strtotime($hari_ini)).$gtahun;
                                            $gtgl_akhir = date('d F ', strtotime($hari_ini)).$gtahun;
                                            
                                            ?>
                                            <div class='form-group'>
                                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for='tgl01'>Peiode <span class='required'></span></label>
                                                <div class='col-md-6'>
                                                    <div class="form-group">
                                                        <div class='input-group date' id='tgl01'>
                                                            <input type='text' id='tgl01' name='e_periode01' required='required' class='form-control' placeholder='tgl awal' value='<?PHP echo $tgl_pertama; ?>' placeholder='dd mmm yyyy' Readonly>
                                                            <span class="input-group-addon">
                                                               <span class="glyphicon glyphicon-calendar"></span>
                                                            </span>
                                                        </div>
                                                        <div class='input-group date' id='tgl02'>
                                                            <input type='text' id='tgl02' name='e_periode02' required='required' class='form-control' placeholder='tgl akhir' value='<?PHP echo $tgl_akhir; ?>' placeholder='dd mmm yyyy' Readonly>
                                                            <span class="input-group-addon">
                                                               <span class="glyphicon glyphicon-calendar"></span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        
                                            <div class='form-group'>
                                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for='tgl03'>Growth <span class='required'></span></label>
                                                <div class='col-md-6'>
                                                    <div class="form-group">
                                                        <div class='input-group date' id='tgl03'>
                                                            <input type='text' id='tgl03' name='e_gperiode01' required='required' class='form-control' placeholder='tgl awal' value='<?PHP echo $gtgl_pertama; ?>' placeholder='dd mmm yyyy' Readonly>
                                                            <span class="input-group-addon">
                                                               <span class="glyphicon glyphicon-calendar"></span>
                                                            </span>
                                                        </div>
                                                        <div class='input-group date' id='tgl04'>
                                                            <input type='text' id='tgl04' name='e_gperiode02' required='required' class='form-control' placeholder='tgl akhir' value='<?PHP echo $gtgl_akhir; ?>' placeholder='dd mmm yyyy' Readonly>
                                                            <span class="input-group-addon">
                                                               <span class="glyphicon glyphicon-calendar"></span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        
                                            <?PHP
                                            
                                            /*
                                            $hari_ini = date("Y-m-d");
                                            $tgl_pertama = date('01 F Y', strtotime($hari_ini));
                                            $tgl_akhir = date('d F Y', strtotime($hari_ini));

                                            echo "<div class='form-group'>";
                                            echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='tgl01'>Peiode <span class='required'></span></label>";
                                            echo "<div class='col-md-9 col-sm-9 col-xs-12'>
                                                <span class='input-group'>
                                                <input type='text' id='tgl01' name='e_periode01' required='required' class='form-control col-md-7 col-xs-12' placeholder='tgl lahir' value='$tgl_pertama' placeholder='dd mmm yyyy' Readonly>
                                                </span>
                                                </div>";
                                            echo "</div>";

                                            echo "<div class='form-group'>";
                                            echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='tgl02'> <span class='required'>s/d.</span></label>";
                                            echo "<div class='col-md-9 col-sm-9 col-xs-12'>
                                                <span class='input-group'><input type='text' id='tgl02' name='e_periode02' required='required' class='form-control col-md-7 col-xs-12' placeholder='tgl lahir' value='$tgl_akhir' placeholder='dd mmm yyyy' Readonly></span>
                                                </div>";
                                            echo "</div>";

                                            $gtahun = date("Y")-1;
                                            $gtgl_pertama = date('01 F ', strtotime($hari_ini)).$gtahun;
                                            $gtgl_akhir = date('d F ', strtotime($hari_ini)).$gtahun;

                                            echo "<div class='form-group'>";
                                            echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='tgl03'>Growth <span class='required'></span></label>";
                                            echo "<div class='col-md-9 col-sm-9 col-xs-12'>
                                                <span class='input-group'><input type='text' id='tgl03' name='e_gperiode01' required='required' class='form-control col-md-7 col-xs-12' placeholder='tgl lahir' value='$gtgl_pertama' placeholder='dd mmm yyyy' Readonly></span>
                                                </div>";
                                            echo "</div>";

                                            echo "<div class='form-group'>";
                                            echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='tgl04'> <span class='required'>s/d.</span></label>";
                                            echo "<div class='col-md-9 col-sm-9 col-xs-12'>
                                                <span class='input-group'><input type='text' id='tgl04' name='e_gperiode02' required='required' class='form-control col-md-7 col-xs-12' placeholder='tgl lahir' value='$gtgl_akhir' placeholder='dd mmm yyyy' Readonly></span>
                                                </div>";
                                            echo "</div>";
                                             * 
                                             */

                                        ?>
                                    </div>
                                </div>
                            </div>
                            <!--end kiri-->

                            <!--kanan-->
                            <div class='col-md-6 col-xs-12'>
                                <div class='x_panel'>
                                    <div class='x_content form-horizontal form-label-left'><br />
                                        <?PHP
                                            echo "<div class='form-group'>";
                                            echo "<label class='control-label col-md-3 col-sm-3 col-xs-12'>Divisi &nbsp;<input type=\"checkbox\" id=\"chkbtndiv\" value=\"deselect\" onClick=\"SelAllCheckBox('chkbtndiv', 'chkbox_divisi[]')\" checked/><span class='required'></span></label>";
                                            echo "<div class='col-md-9 col-sm-9 col-xs-12'>";
                                            ?>
                                                <!--
                                                <a class="buttonlink" onclick="CheckMultiAll('kotak-multi')"><img src="images/checkmark-blue.png" width="25px"></a>&nbsp;
                                                <a class="buttonlink" onclick="UnCheckMultiAll('kotak-multi')"><img src="images/uncheckmark-blue.png" width="20px"></a>
                                                -->
                                                <div id="kotak-multi" class="jarak">
                                                <?PHP
                                                    cBoxIsiDivisi();
                                                ?>
                                                </div>
                                            <?PHP
                                            echo "</div>";
                                            echo "</div>";
                                            
                                            
                                            ?>
                                                <script>
                                                    function PilProdukFilter($kategori) {
                                                        var kategori=$($kategori).val();
                                                        
                                                        $.ajax({
                                                            type:"post",
                                                            url:"module/mod_sls_salesytd/viewdata.php?module=viewprodukfilter&kategori="+kategori,
                                                            data:"ukategori="+kategori,
                                                            success:function(data){
                                                                $("#kotak-multi3").html(data);
                                                            }
                                                        });
                                                        
                                                    }
                                                </script>
                                                <?PHP
                                            
                                            echo "<div class='form-group'>";
                                            echo "<label class='control-label col-md-3 col-sm-3 col-xs-12'>Product <span class='required'></span></label>";
                                            echo "<div class='col-md-9 col-sm-9 col-xs-12'>";
                                                 ComboGroupProduk("", "", "PilProdukFilter(e_groupprod)", "", "");
                                            echo "</div>";
                                            echo "</div>";
                                            
                                            echo "<div class='form-group'>";
                                            echo "<label class='control-label col-md-3 col-sm-3 col-xs-12'>Multi Select &nbsp;<input type=\"checkbox\" id=\"chkbtnprod\" value=\"deselect\" onClick=\"SelAllCheckBox('chkbtnprod', 'chkbox_produk[]')\" checked/><span class='required'></span></label>";
                                            echo "<div class='col-md-9 col-sm-9 col-xs-12'>";
                                            ?>
                                                <!--
                                                <a class="buttonlink" onclick="CheckMultiAll('kotak-multi3')"><img src="images/checkmark-blue.png" width="25px"></a>&nbsp;
                                                <a class="buttonlink" onclick="UnCheckMultiAll('kotak-multi3')"><img src="images/uncheckmark-blue.png" width="20px"></a>
                                                -->
                                                <div id="kotak-multi3" class="jarak">
                                                <?PHP
                                                    cBoxIsiProduk();
                                                ?>
                                                </div>
                                            <?PHP
                                            echo "</div>";
                                            echo "</div>";
                                            
                                            echo "<div class='form-group'>";
                                            echo "<label class='control-label col-md-3 col-sm-3 col-xs-12'>Region <span class='required'></span></label>";
                                            echo "<div class='col-md-9 col-sm-9 col-xs-12'>";
                                            ?>
                                                <div id="kotak-multi11">
                                                    <input type=checkbox value='B' id="B" name=chkbox_region[] onclick="selectRegionCekBox('B');" checked> B - Barat<br/>
                                                    <input type=checkbox value='T' id="T" name=chkbox_region[] onclick="selectRegionCekBox('T');" checked> T - Timur
                                                </div>
                                            <?PHP
                                            echo "</div>";
                                            echo "</div>";
                                            
                                            
                                            echo "<div class='form-group'>";
                                            echo "<label class='control-label col-md-3 col-sm-3 col-xs-12'>Daerah &nbsp;<input type=\"checkbox\" id=\"chkbtndaerah\" value=\"deselect\" onClick=\"SelAllCheckBox('chkbtndaerah', 'chkbox_cabang[]')\" checked/><span class='required'></span></label>";
                                            echo "<div class='col-md-9 col-sm-9 col-xs-12'>";
                                            ?>
                                                <!--
                                                <a class="buttonlink" onclick="CheckMultiAll('kotak-multi2')"><img src="images/checkmark-blue.png" width="25px"></a>&nbsp;
                                                <a class="buttonlink" onclick="UnCheckMultiAll('kotak-multi2')"><img src="images/uncheckmark-blue.png" width="20px"></a>
                                                -->
                                                <div id="kotak-multi2" class="jarak">
                                                <?PHP
                                                    cBoxIsiCabang();
                                                ?>
                                                </div>
                                            <?PHP
                                            echo "</div>";
                                            echo "</div>";
                                            
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <!--end kanan-->
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