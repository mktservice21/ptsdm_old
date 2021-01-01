<?PHP include "module/lap_br_klaim/fungsi_combo.php"; ?>
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
            $.ajax({
                type:"post",
                url:"module/lap_br_klaim/viewdata.php?module=viewkodedivisi&data1="+mycek,
                data:"udata1="+mycek,
                success:function(data){
                    $("#kotak-multi3").html(data);
                }
            });
            
        }
        
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

<!-- Modal -->
<div class='modal fade' id='myModal' role='dialog'></div>

<div class="">

    <div class="page-title"><div class="title_left"><h3>Laporan Klaim Diskon</h3></div></div><div class="clearfix"></div>
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


                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_dokter'>Periode By <span class='required'></span></label>
                                            <div class='col-xs-9'>
                                                <select class='form-control input-sm' id="cb_tgltipe" name="cb_tgltipe">
                                                    <option value="2" selected>Tanggal Transfer</option>
                                                    <option value="4">Tanggal Pengajuan / Buat</option>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for='tgl01'>Periode <span class='required'></span></label>
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
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12'>Lampiran &nbsp;<span class='required'></span></label>
                                            <div class='col-md-9 col-sm-9 col-xs-12'>
                                                <?PHP
                                                    cBoxLampiran();
                                                ?>
                                            </div>
                                        </div>
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12'>Distributor &nbsp;<input type="checkbox" id="chkbtndist" value="deselect" onClick="SelAllCheckBox('chkbtndist', 'chkbox_dist[]')" checked/><span class='required'></span></label>
                                            <div class='col-md-9 col-sm-9 col-xs-12'>
                                                <div id="kotak-multi5" class="jarak">
                                                <?PHP
                                                    cBoxIsiDistributor("");
                                                ?>
                                                </div>
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

