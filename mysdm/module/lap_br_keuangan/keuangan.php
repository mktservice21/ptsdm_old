<?PHP //include "module/lap_br_realisasi/fungsi_combo.php"; ?>
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

    function selectKodeDivisiCekBox(nmceck){
        var checkboxes = document.getElementsByName(nmceck);
        var epiltipe = document.getElementById('e_pilihtipe').value;

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
        
        $.ajax({
            type:"post",
            url:"config/viewdata2.php?module=viewkodedivisi&data1="+mycek,
            data:"udata1="+mycek+"&upilihtipe="+epiltipe,
            success:function(data){
                $("#kotak-multi3").html(data);
            }
        });
    }


    function getDataKaryawanDiv(data1, data2, logstsadmin, loglvlposisi, logdivisi, idivprod, rbtipe){
        if (idivprod=="")
            var edivprod ="";
        else
            var edivprod =document.getElementById(idivprod).value;


        var etipe="T";
        if (rbtipe=="") {
        }else{
            var ertipe =document.getElementsByName(rbtipe);
            for (var i =0 , length = ertipe.length; i < length; i++) {
                if (ertipe[i].checked) {
                    etipe=ertipe[i].value;
                }
            }
        }
        //alert(etipe);

        $.ajax({
            type:"post",
            url:"config/viewdata.php?module=viewkaryawandiv&data1="+data1+"&data2="+data2,
            data:"udata1="+data1+"&udata2="+data2+"&ulogstatus="+logstsadmin+"&uloglvl="+loglvlposisi+"&ulogdivisi="+logdivisi+"&uedivprod="+edivprod,
            success:function(data){
                $("#myModal").html(data);
            }
        });
    }

    function getDataModalKaryawan(fildnya1, fildnya2, d1, d2, icabang){
        document.getElementById(fildnya1).value=d1;
        document.getElementById(fildnya2).value=d2;
    }

    function getFungsiTipe() {
        //alert("ada");
    }
    
    function selectRegionCekBox(data, rbtipe){
        var cB = document.getElementById("B").checked;
        var cT = document.getElementById("T").checked;


        var etipe="T";
        if (rbtipe=="") {
        }else{
            var ertipe =document.getElementsByName(rbtipe);
            for (var i =0 , length = ertipe.length; i < length; i++) {
                if (ertipe[i].checked) {
                    etipe=ertipe[i].value;
                }
            }
        }
        //alert(etipe);
        
        $.ajax({
            type:"post",
            url:"config/viewdata2.php?module=viewregioncab&data1="+cB+"&data2="+cB,
            data:"udata1="+cB+"&udata2="+cT,
            success:function(data){
                $("#kotak-multi2").html(data);
            }
        });
    }
</script>

<?PHP
    $fkaryawan=$_SESSION['IDCARD'];
    $fstsadmin=$_SESSION['STSADMIN'];
    $flvlposisi=$_SESSION['LVLPOSISI'];
    $fdivisi=$_SESSION['DIVISI'];
?>
<!-- Modal -->
<div class='modal fade' id='myModal' role='dialog'></div>

<div class="">

    <div class="page-title"><div class="title_left"><h3>Laporan Keuangan Marketing</h3></div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">

        <?php
        $aksi="eksekusi2.php";
        switch($_GET['act']){
            default:
                $hari_ini = date("Y-m-d");
                $tgl_pertama = date('Y', strtotime($hari_ini));
                $tgl_akhir = date('Y', strtotime($hari_ini));
                ?>
                <form name='form1' id='form1' method='POST' action="<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]"; ?>"  enctype='multipart/form-data' target="_blank">
                    <div class='col-md-12 col-sm-12 col-xs-12'>
                        <div class='x_panel'>

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
                                        
                                        <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_ket'>Report By <span class='required'></span></label>
                                        <div class='col-md-6 col-sm-6 col-xs-12'>
                                                <div class='btn-group' data-toggle='buttons'>
                                                    <label onclick="getFungsiTipe()"  class='btn btn-default'><input type='radio' onclick="getFungsiTipe()" class='flat' name='rb_rpttipe' id='rb_rptby1' value='P'> Person </label>
                                                    <label onclick="getFungsiTipe()"  class='btn btn-default'><input type='radio' onclick="getFungsiTipe()" class='flat' name='rb_rpttipe' id='rb_rptby2' value='T' checked> Team </label>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_karyawan'>Employee <span class='required'></span></label>
                                            <div class='col-md-9 col-sm-9 col-xs-12'>
                                                <div class='input-group'>
                                                    <span class='input-group-btn'>
                                                        <button type='button' class='btn btn-primary' data-toggle='modal' data-target='#myModal' 
                                                            onClick="getDataKaryawanDiv('e_idkaryawan', 'e_karyawan', 
                                                            '<?PHP echo $fstsadmin; ?>',
                                                            '<?PHP echo $flvlposisi; ?>',
                                                            '<?PHP echo $fdivisi; ?>',
                                                            '', 'rb_rpttipe'
                                                            )">Pilih
                                                        </button>
                                                        
                                                    </span>
                                                    <input type='text' class='form-control' id='e_idkaryawan' name='e_idkaryawan' value='<?PHP echo $_SESSION['IDCARD']; ?>' Readonly>
                                                </div>
                                                <input type='text' class='form-control' id='e_karyawan' name='e_karyawan' value='<?PHP echo $_SESSION['NAMALENGKAP']; ?>' Readonly>
                                            </div>
                                        </div>


                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for='thn01'>Periode <span class='required'></span></label>
                                            <div class='col-md-6'>
                                                <div class="form-group">
                                                    <div class='input-group date' id='thn01'>
                                                        <input type='text' id='thn01' name='e_periode01' required='required' class='form-control' placeholder='tgl awal' value='<?PHP echo $tgl_pertama; ?>' placeholder='dd mmm yyyy' Readonly>
                                                        <span class="input-group-addon">
                                                           <span class="glyphicon glyphicon-calendar"></span>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12'>Region <span class='required'></span></label>
                                            <div class='col-md-9 col-sm-9 col-xs-12'>
                                                <div id="kotak-multi11">
                                                    <input type=checkbox value='B' id="B" name=chkbox_region[] onclick="selectRegionCekBox('B', 'rb_rpttipe');" checked> B - Barat<br/>
                                                    <input type=checkbox value='T' id="T" name=chkbox_region[] onclick="selectRegionCekBox('T', 'rb_rpttipe');" checked> T - Timur
                                                </div>
                                            </div>
                                        </div>
                                        
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12'>Cabang &nbsp;<input type="checkbox" id="chkbtndaerah" value="deselect" onClick="SelAllCheckBox('chkbtndaerah', 'chkbox_cabang[]')" checked/><span class='required'></span></label>
                                            <div class='col-md-9 col-sm-9 col-xs-12'>
                                                <div id="kotak-multi2" class="jarak">
                                                <?PHP
                                                    cBoxIsiCabangFilter("", "selectKodeDivisiCekBox('chkbox_divisiprod[]')",
                                                            "", "$fstsadmin", "$flvlposisi", "$fdivisi", "T");
                                                ?>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        
                                        
                                        
                                    </div>
                                </div>           
                            </div>           
                            
                            <!--kanan-->
                            <div class='col-md-6 col-xs-12'>
                                <div class='x_panel'>
                                    <div class='x_content form-horizontal form-label-left'><br />
                            
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_pilihtipe'>Pilih Tipe <span class='required'></span></label>
                                            <div class='col-xs-9'>
                                                <select class='form-control' name='e_pilihtipe' id='e_pilihtipe' style='width: 100%;' onchange="selectKodeDivisiCekBox('chkbox_divisiprod[]')">
                                                    <option value='' selected>-- All Akun --</option>
                                                    <option value='Y'>DSS / DCC</option>
                                                    <option value='N'>Non DSS / DCC</option>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12'>Divisi &nbsp;<input type="checkbox" id="chkbtndivprod" value="deselect" onClick="SelAllCheckBox('chkbtndivprod', 'chkbox_divisiprod[]')" checked/><span class='required'></span></label>
                                            <div class='col-md-9 col-sm-9 col-xs-12'>
                                                <div id="kotak-multi" class="jarak">
                                                <?PHP
                                                    cBoxIsiDivisiProdFilter("", "selectKodeDivisiCekBox('chkbox_divisiprod[]')",
                                                            "", "$fstsadmin", "$flvlposisi", "$fdivisi", "T");
                                                ?>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12'>Kode &nbsp;<input type="checkbox" id="chkbtnkode" value="deselect" onClick="SelAllCheckBox('chkbtnkode', 'chkbox_kode[]')" checked/><span class='required'></span></label>
                                            <div class='col-md-9 col-sm-9 col-xs-12'>
                                                <div id="kotak-multi3" class="jarak">
                                                <?PHP
                                                    cBoxIsiKodePosting("");
                                                ?>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12'>Lampiran &nbsp;<span class='required'></span></label>
                                            <div class='col-md-9 col-sm-9 col-xs-12'>
                                                <?PHP
                                                    cBoxLampiranAll();
                                                ?>
                                            </div>
                                        </div>
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12'>CA &nbsp;<span class='required'></span></label>
                                            <div class='col-md-9 col-sm-9 col-xs-12'>
                                                <?PHP
                                                    cBoxCAAll();
                                                ?>
                                            </div>
                                        </div>
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12'>Via Surabaya &nbsp;<span class='required'></span></label>
                                            <div class='col-md-9 col-sm-9 col-xs-12'>
                                                <?PHP
                                                    cBoxVIAAll();
                                                ?>
                                            </div>
                                        </div>
                                        
                                        
                            
                                    </div>
                                </div>           
                            </div>      
    
                            
                        </div>
                    </div>
                </form>
                <?PHP
            break;

        }
        ?>

    </div>
    <!--end row-->
</div>

