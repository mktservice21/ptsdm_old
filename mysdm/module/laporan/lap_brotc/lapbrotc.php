<div class="">

    <div class="col-md-12 col-sm-12 col-xs-12"><div class="title_left"><h3>Laporan Budget Request CHC</h3></div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">
        <?php
        
        $fkaryawan=$_SESSION['IDCARD'];
        $pmyjabatanid=$_SESSION['JABATANID'];
        $pmygroupid=$_SESSION['GROUP'];
        $pidcabangpil="";
        
    
        $aksi="eksekusi3.php";
        switch($_GET['act']){
            default:

                $hari_ini = date("Y-m-d");
                $tgl_pertama = date('01 F Y', strtotime($hari_ini));
                $tgl_akhir = date('d F Y', strtotime($hari_ini));
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
                                        
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_dokter'>Periode By <span class='required'></span></label>
                                            <div class='col-xs-9'>
                                                <select class='form-control input-sm' id="cb_tgltipe" name="cb_tgltipe">
                                                    <option value="1" selected>Tanggal Transfer</option>
                                                    <option value="2">Tanggal Input/Pengajuan</option>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        
                                        <div class='form-group grp-periode'>
                                            <label class='control-label control-periode col-md-3 col-sm-3 col-xs-12' for='tgl01'>Periode <span class='required'></span></label>
                                            <div class='col-md-6'>
                                                <div class="form-group grp-periode">
                                                    <div class='input-group date grp-periode' id='tgl01'>
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
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Area <span class='required'></span></label>
                                            <div class='col-xs-9'>
                                                <select class='form-control' name='cb_cabangid' id='cb_cabangid' style='width: 100%;' onchange="">
                                                    <option value='' selected>--All--</option>
                                                    <?PHP
                                                    $query = "select icabangid_o icabangid, nama from MKT.icabang_o WHERE aktif='Y' order by nama";
                                                    $tampil= mysqli_query($cnmy, $query);
                                                    while($na= mysqli_fetch_array($tampil)) {
                                                        $nidcab=$na['icabangid'];
                                                        $nnmcab=$na['nama'];
                                                        echo "<option value='$nidcab'>$nnmcab</option>";
                                                    }
                                                    ?>
                                                </select>
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
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_pilihtipe'>Posting <span class='required'></span></label>
                                            <div class='col-xs-9'>
                                                <select class='form-control' id='cb_subpost' name='cb_subpost' onchange="selectKodePosting('cb_subpost')">
                                                    <?PHP
                                                    $tampil=mysqli_query($cnmy, "select distinct subpost, nmsubpost from hrd.brkd_otc where ifnull(subpost,'') <> '' order by nmsubpost");
                                                    echo "<option value='none' selected>-- All --</option>";
                                                    echo "<option value=''>blank_</option>";
                                                    while($a=mysqli_fetch_array($tampil)){ 
                                                        $pidsubpost=$a['subpost'];
                                                        $pmnsubpost=$a['nmsubpost'];
                                                        echo "<option value='$pidsubpost'>$pidsubpost - $pmnsubpost</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12'>Kode &nbsp;<input type="checkbox" id="chkbtnkode" value="deselect" onClick="SelAllCheckBox('chkbtnkode', 'chkbox_kodeotc[]')" checked/><span class='required'></span></label>
                                            <div class='col-md-9 col-sm-9 col-xs-12'>
                                                <div id="kotak-multi3" class="jarak">
                                                <?PHP
                                                    cBoxIsiKodePostingOTC("");
                                                ?>
                                                </div>
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


                        if (nmbuton=="chkbtndivprod"){
                            var mycek="";
                            for (var i in checkboxes){
                                if (checkboxes[i].checked) {
                                    mycek=mycek+"'"+checkboxes[i].value+"',";
                                }
                            }
                            if (mycek==""){
                                $("#kotak-multi3").html("");
                                return 0;
                            }
                            var epiltipe = document.getElementById('e_pilihtipe').value;
                            $.ajax({
                                type:"post",
                                url:"config/viewdata2.php?module=viewkodedivisi&data1="+mycek,
                                data:"udata1="+mycek+"&upilihtipe="+epiltipe,
                                success:function(data){
                                    $("#kotak-multi3").html(data);
                                }
                            });

                        }


                    }
    
                    function selectKodePosting(spost){
                        var epost = document.getElementById(spost).value;

                        $.ajax({
                            type:"post",
                            url:"config/viewdata2.php?module=viewkodepostingotc",
                            data:"upost="+epost+"&upilihtipe="+epost,
                            success:function(data){
                                $("#kotak-multi3").html(data);
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