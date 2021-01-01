<div class="">

    <div class="col-md-12 col-sm-12 col-xs-12"><div class="title_left"><h3>Laporan Terima Barang Gimmick</h3></div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">
        <?php
        include "config/koneksimysqli_ms.php";
        
        $fkaryawan=$_SESSION['IDCARD'];
        $pmyjabatanid=$_SESSION['JABATANID'];
        $pmygroupid=$_SESSION['GROUP'];
        $pidcabangpil="";
        
        
        $query = "select PILIHAN from dbmaster.t_barang_wewenang WHERE karyawanid='$fkaryawan'";
        $tampil_= mysqli_query($cnmy, $query);
        $pn= mysqli_fetch_array($tampil_);
        $ppilihanwewenang=$pn['PILIHAN'];
    
        $aksi="eksekusi3.php";
        switch($_GET['act']){
            default:

                $hari_ini = date("Y-m-d");
                //$tgl_pertama=date('F Y', strtotime('-1 month', strtotime($hari_ini)));
                $tgl_pertama = date('F Y', strtotime($hari_ini));
                ?>
                <script>
                    function disp_confirm(pText)  {
                        var pildivprodgrp=false;
                        var chk_arrgrpp =  document.getElementsByName('chkbox_divisiprodgrp[]');
                        var chklength = "0";
                        chklength = chk_arrgrpp.length;
                        for(k=0;k< chklength;k++)
                        {
                            if (chk_arrgrpp[k].checked == true) {
                                pildivprodgrp=true;
                                break;
                            }
                        }
                        
                        if (pildivprodgrp==false) {
                            alert("Group Produk Harus dipilih...");
                            return false;
                        }
                        
                        var pilkatid=false;
                        var chk_arrkat =  document.getElementsByName('chkbox_kategori[]');
                        chklength = chk_arrkat.length;
                        for(k=0;k< chklength;k++)
                        {
                            if (chk_arrkat[k].checked == true) {
                                pilkatid=true;
                                break;
                            }
                        }
                        
                        if (pilkatid==false) {
                            alert("Kategori Harus dipilih...");
                            return false;
                        }
                        
                        
                        
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
                
                <style>
                    .grp-periode, .input-periode, .control-periode {
                        margin-bottom:2px;
                    }
                </style>
                
                
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
                                        
                                        
                                        <div class='form-group grp-periode'>
                                            <label class='control-label control-periode col-md-3 col-sm-3 col-xs-12' for='cbln01'>Periode <span class='required'></span></label>
                                            <div class='col-md-6'>
                                                <div class="form-group grp-periode">
                                                    <div class='input-group date grp-periode' id='cbln01'>
                                                        <input type='text' id='e_periode01' name='e_periode01' required='required' class='form-control' placeholder='tgl awal' value='<?PHP echo $tgl_pertama; ?>' placeholder='dd mmm yyyy' Readonly>
                                                        <span class="input-group-addon">
                                                           <span class="glyphicon glyphicon-calendar"></span>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class='form-group grp-periode'>
                                            <label class='control-label control-periode col-md-3 col-sm-3 col-xs-12' for='cbln02'>s/d. <span class='required'></span></label>
                                            <div class='col-md-6'>
                                                <div class="form-group grp-periode">
                                                    
                                                    <div class='input-group date' id='cbln02'>
                                                        <input type='text' id='e_periode02' name='e_periode02' required='required' class='form-control' placeholder='tgl akhir' value='<?PHP echo $tgl_pertama; ?>' placeholder='dd mmm yyyy' Readonly>
                                                        <span class="input-group-addon">
                                                           <span class="glyphicon glyphicon-calendar"></span>
                                                        </span>
                                                    </div>
                                                    
                                                </div>
                                            </div>
                                        </div>
                                        
                                        
                                        <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='distibutor'>Untuk Divisi <span class='required'></span></label>
                                            <div class='col-md-9 col-sm-9 col-xs-12'>
                                                <select class='form-control input-sm' id="cb_udiv" name="cb_udiv" onchange="ShowDataUntukDivisi()">
                                                    <?PHP
                                                        if ($ppilihanwewenang=="AL") {
                                                            echo "<option value='ET' selected>ETHICAL</option>";
                                                            echo "<option value='OT'>CHC</option>";
                                                        }elseif ($ppilihanwewenang=="ET") {
                                                            echo "<option value='ET' selected>ETHICAL</option>";
                                                        }elseif ($ppilihanwewenang=="OT") {
                                                            echo "<option value='OT' selected>CHC</option>";
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12'>Grp. Prod. &nbsp;<input type="checkbox" id="chkbtndivprodgrp" value="deselect" onClick="SelAllCheckBox('chkbtndivprodgrp', 'chkbox_divisiprodgrp[]')" checked/><span class='required'></span></label>
                                            <div class='col-md-9 col-sm-9 col-xs-12'>
                                                <div id="kotak-multi" class="jarak">
                                                <?PHP
                                                    //cBoxIsiDivisiProd("selectKodeDivisiCekBox('chkbox_divisiprod[]')");
                                                ?>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12'>Kategori &nbsp;<input type="checkbox" id="chkbtnkategori" value="deselect" onClick="SelAllCheckBox('chkbtnkategori', 'chkbox_kategori[]')" checked/><span class='required'></span></label>
                                            <div class='col-md-9 col-sm-9 col-xs-12'>
                                                <div id="kotak-multi2" class="jarak">
                                                <?PHP
                                                    $query = "select IDKATEGORI, NAMA_KATEGORI from dbmaster.t_barang_kategori WHERE STSAKTIF='Y'";
                                                    $tampil= mysqli_query($cnmy, $query);
                                                    while ($Xt=mysqli_fetch_array($tampil)){
                                                        $pidkat=$Xt['IDKATEGORI'];
                                                        $pnmkat=$Xt['NAMA_KATEGORI'];
                                                        $cek="checked";
                                                        echo "<input type=checkbox value='$pidkat' name='chkbox_kategori[]' $cek> $pnmkat<br/>";
                                                    }
                                                ?>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        
                                    </div>
                                </div>
                            </div>
                            <!--end kiri-->
                            
                            
                            <!--kanan-->
                            <div class='col-md-6 col-xs-12'>
                                <div class='x_panel'>
                                    <div class='x_content form-horizontal form-label-left'><br />
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12'>Produk &nbsp;<input type="checkbox" id="chkbtnprdukid" value="deselect" onClick="SelAllCheckBox('chkbtnprdukid', 'chkbox_produkid[]')" checked/><span class='required'></span></label>
                                            <div class='col-md-9 col-sm-9 col-xs-12'>
                                                <button type='button' class='btn btn-warning btn-xs' onclick='RefreshProduk()'>Refresh Produk</button><p/>
                                                <div id="kotak-multi5" class="jarak">
                                                <?PHP
                                                    
                                                ?>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12'>&nbsp;<span class='required'></span></label>
                                            <div class='col-md-9 col-sm-9 col-xs-12'>
                                                <input type="checkbox" id="chkvalidate" name="chkvalidate" value="Y" checked />
                                                <b>Sudah Validate</b>
                                            </div>
                                        </div>
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12'>&nbsp;<span class='required'></span></label>
                                            <div class='col-md-9 col-sm-9 col-xs-12'>
                                                <input type="checkbox" id="chkhanya" name="chkhanya" value="Y" />
                                                <b>Hanya yang Reject/Batal</b>
                                            </div>
                                        </div>
                                        
                                        
                                        
                                        
                                    </div>
                                </div>
                            </div>
                            <!--end kanan-->
                            
                            
                        </form>
                    </div><!--end xpanel-->
                </div>
                
                
                <script>
                    
                    $(document).ready(function() {
                        ShowDataUntukDivisi();
                        /*
                        var myurl = window.location;
                        var urlku = new URL(myurl);
                        var iact = urlku.searchParams.get("act");
                        if (iact=="editdata") {
                            CariDataBarang();
                        }
                        */
                    } );
    
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
    
    
                    function ShowDataUntukDivisi() {
                        ShowDataGrpProduk();
                        $("#kotak-multi5").html("");
                    }
                    
                    function ShowDataGrpProduk() {
                        var edivuntuk = document.getElementById("cb_udiv").value;
                        
                        $.ajax({
                            type:"post",
                            url:"module/mod_brg_lapgimstb/viewdata.php?module=caridatagrpprod",
                            data:"udivuntuk="+edivuntuk,
                            success:function(data){
                                $("#kotak-multi").html(data);
                            }
                        });
                    }
                    
                    
                    function RefreshProduk() {
                        var allnobrgprprd="";
                        var chk_arrgrpp =  document.getElementsByName('chkbox_divisiprodgrp[]');
                        var chklength = "0";
                        chklength = chk_arrgrpp.length;
                        for(k=0;k< chklength;k++)
                        {
                            if (chk_arrgrpp[k].checked == true) {
                                var kata = chk_arrgrpp[k].value;
                                var fields = kata.split('-');
                                allnobrgprprd =allnobrgprprd +"'"+fields[0]+"',";
                            }
                        }
                        
                        var allnobrkatid="";
                        var chk_arrkat =  document.getElementsByName('chkbox_kategori[]');
                        chklength = chk_arrkat.length;
                        for(k=0;k< chklength;k++)
                        {
                            if (chk_arrkat[k].checked == true) {
                                var kata = chk_arrkat[k].value;
                                var fields = kata.split('-');
                                allnobrkatid =allnobrkatid +"'"+fields[0]+"',";
                            }
                        }
                        
                        
                        $.ajax({
                            type:"post",
                            url:"module/mod_brg_lapgimstb/viewdata.php?module=caridataprduk",
                            data:"uallnobrgprprd="+allnobrgprprd+"&uallnobrkatid="+allnobrkatid,
                            success:function(data){
                                $("#kotak-multi5").html(data);
                            }
                        });
                        
                    }
                    
                </script>

                
                <?PHP
            break;

            case "tambahbaru":

            break;
        }
        ?>
    </div>
    <!--end row-->
</div>