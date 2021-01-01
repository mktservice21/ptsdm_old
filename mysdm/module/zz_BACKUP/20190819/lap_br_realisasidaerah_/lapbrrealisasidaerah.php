<?PHP include "module/lap_br_realisasidaerah/fungsi_combo.php"; ?>
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
            //var epiltipe = document.getElementById('e_pilihtipe').value;
            var epiltipe = "Y";
            $.ajax({
                type:"post",
                url:"module/lap_br_dcc/viewdata.php?module=viewkodedivisi&data1="+mycek,
                data:"udata1="+mycek+"&upilihtipe="+epiltipe,
                success:function(data){
                    $("#kotak-multi3").html(data);
                }
            });
            
        }
        
    }
    
    function selectRegionCekBox(data){
        var cB = document.getElementById("B").checked;
        var cT = document.getElementById("T").checked;

        $.ajax({
            type:"post",
            url:"module/lap_br_realisasidaerah/viewdata.php?module=viewregioncab&data1="+cB+"&data2="+cB,
            data:"udata1="+cB+"&udata2="+cT,
            success:function(data){
                $("#kotak-multi2").html(data);
            }
        });
    }
    
    function selectKodeDivisiCekBox(nmceck){
        var checkboxes = document.getElementsByName(nmceck);
        //var epiltipe = document.getElementById('e_pilihtipe').value;
        var epiltipe = "Y";
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
            url:"module/lap_br_dcc/viewdata.php?module=viewkodedivisi&data1="+mycek,
            data:"udata1="+mycek+"&upilihtipe="+epiltipe,
            success:function(data){
                $("#kotak-multi3").html(data);
            }
        });
        
    }

function getDataKaryawan(data1, data2){
    $.ajax({
        type:"post",
        url:"config/viewdata.php?module=viewkaryawan&data1="+data1+"&data2="+data2,
        data:"udata1="+data1+"&udata2="+data2,
        success:function(data){
            $("#myModal").html(data);
        }
    });
}

function getDataModalKaryawan(fildnya1, fildnya2, d1, d2){
    document.getElementById(fildnya1).value=d1;
    document.getElementById(fildnya2).value=d2;
}

</script>
<style>
    .grp-periode, .input-periode, .control-periode {
        margin-bottom:2px;
    }
</style>

<!-- Modal -->
<div class='modal fade' id='myModal' role='dialog'></div>

<div class="">

    <div class="page-title"><div class="title_left"><h4>Laporan Realisasi Budget Request Per Daerah</h4></div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">

        <?php
        $aksi="eksekusi2.php";
        switch($_GET['act']){
            default:
                $hari_ini = date("Y-m-d");
                $tgl_pertama = date('01 F Y', strtotime($hari_ini));
                $tgl_akhir = date('d F Y', strtotime($hari_ini));
                ?>
                <form name='form1' id='form1' method='POST' action="<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]"; ?>"  enctype='multipart/form-data' target="_blank">
                    <div class='col-md-12 col-sm-12 col-xs-12'>
                        <div class='x_panel'>

                            <div class='col-md-12 col-sm-12 col-xs-12'>
                                <h2><!--
                                    <input type='button' value='Back' onclick='self.history.back()' class='btn btn-default'>
                                    <button class='btn btn-primary' type='reset'>Reset</button>-->
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
                                                    <label class='btn btn-default'><input type='radio' class='flat' name='rb_rpttipe' id='rb_rptby1' value='P'> Person </label>
                                                    <label class='btn btn-default'><input type='radio' class='flat' name='rb_rpttipe' id='rb_rptby2' value='T' checked> Team </label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_karyawan'>Employee <span class='required'></span></label>
                                            <div class='col-md-9 col-sm-9 col-xs-12'>
                                                <div class='input-group '>
                                                    <span class='input-group-btn'>
                                                        <button type='button' class='btn btn-primary' data-toggle='modal' data-target='#myModal' onClick="getDataKaryawan('e_idkaryawan', 'e_karyawan')">Pilih</button>
                                                    </span>
                                                    <input type='text' class='form-control' id='e_idkaryawan' name='e_idkaryawan' value='<?PHP echo $_SESSION['IDCARD']; ?>' Readonly>
                                                </div>
                                                <input type='text' class='form-control' id='e_karyawan' name='e_karyawan' value='<?PHP echo $_SESSION['NAMALENGKAP']; ?>' Readonly>
                                            </div>
                                        </div>


                                        <div class='form-group grp-periode'>
                                            <label class='control-label control-periode col-md-3 col-sm-3 col-xs-12' for='tgl01'>Periode <span class='required'></span></label>
                                            <div class='col-md-6'>
                                                <div class="form-group grp-periode">
                                                    <div class='input-group date input-periode' id='tgl01'>
                                                        <input type='text' id='tgl01' name='e_periode01' required='required' class='form-control' placeholder='tgl awal' value='<?PHP echo $tgl_pertama; ?>' placeholder='dd mmm yyyy' Readonly>
                                                        <span class="input-group-addon">
                                                           <span class="glyphicon glyphicon-calendar"></span>
                                                        </span>
                                                    </div>
                                                    <div class='input-group date input-periode' id='tgl02'>
                                                        <input type='text' id='tgl02' name='e_periode02' required='required' class='form-control' placeholder='tgl akhir' value='<?PHP echo $tgl_akhir; ?>' placeholder='dd mmm yyyy' Readonly>
                                                        <span class="input-group-addon">
                                                           <span class="glyphicon glyphicon-calendar"></span>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!--
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_dokter'>Dokter <span class='required'></span></label>
                                            <div class='col-xs-9'>
                                                <div class='input-group '>
                                                    <span class='input-group-btn'>
                                                        <button type='button' class='btn btn-primary' data-toggle='modal' data-target='#myModal' onClick="getDataDokterMRCabang('e_iddokter', 'e_dokter', 'e_idcabang', 'cb_mr')">Go!</button>
                                                    </span>
                                                    <input type='hidden' class='form-control' id='e_iddokter' name='e_iddokter' value='' Readonly>
                                                    <input type='text' class='form-control' id='e_dokter' name='e_dokter' value='' Readonly>
                                                </div>
                                            </div>
                                        </div>
                                        -->
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12'>Divisi &nbsp;<input type="checkbox" id="chkbtndivprod" value="deselect" onClick="SelAllCheckBox('chkbtndivprod', 'chkbox_divisiprod[]')" checked/><span class='required'></span></label>
                                            <div class='col-md-9 col-sm-9 col-xs-12'>
                                                <div id="kotak-multi" class="jarak">
                                                <?PHP
                                                    cBoxIsiDivisiProd("selectKodeDivisiCekBox('chkbox_divisiprod[]')");
                                                ?>
                                                </div>
                                            </div>
                                        </div>
                            
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12'>Kode &nbsp;<input type="checkbox" id="chkbtnkode" value="deselect" onClick="SelAllCheckBox('chkbtnkode', 'chkbox_kode[]')" checked/><span class='required'></span></label>
                                            <div class='col-md-9 col-sm-9 col-xs-12'>
                                                <div id="kotak-multi3" class="jarak">
                                                <?PHP
                                                    cBoxIsiKode();
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
                            
                                        <!--
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
                                        -->

                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12'>Region <span class='required'></span></label>
                                            <div class='col-md-9 col-sm-9 col-xs-12'>
                                                <div id="kotak-multi11">
                                                    <input type=checkbox value='B' id="B" name=chkbox_region[] onclick="selectRegionCekBox('B');" checked> B - Barat<br/>
                                                    <input type=checkbox value='T' id="T" name=chkbox_region[] onclick="selectRegionCekBox('T');" checked> T - Timur
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12'>Cabang &nbsp;<input type="checkbox" id="chkbtndaerah" value="deselect" onClick="SelAllCheckBox('chkbtndaerah', 'chkbox_cabang[]')" checked/><span class='required'></span></label>
                                            <div class='col-md-9 col-sm-9 col-xs-12'>
                                                <div id="kotak-multi2" class="jarak">
                                                    <input type="checkbox" name="chkbox_cabang[]" id="chkbox_cabang[]" value="tanpa_cabang" checked>_blank <br/>
                                                <?PHP
                                                    cBoxIsiCabang();
                                                ?>
                                                </div>
                                            </div>
                                        </div>
                                        

                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12'>Lampiran &nbsp;<span class='required'></span></label>
                                            <div class='col-md-9 col-sm-9 col-xs-12'>
                                                <?PHP
                                                    cBoxLampiran();
                                                ?>
                                            </div>
                                        </div>
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12'>CA &nbsp;<span class='required'></span></label>
                                            <div class='col-md-9 col-sm-9 col-xs-12'>
                                                <?PHP
                                                    cBoxCA();
                                                ?>
                                            </div>
                                        </div>
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12'>Via Surabaya &nbsp;<span class='required'></span></label>
                                            <div class='col-md-9 col-sm-9 col-xs-12'>
                                                <?PHP
                                                    cBoxVIA();
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

