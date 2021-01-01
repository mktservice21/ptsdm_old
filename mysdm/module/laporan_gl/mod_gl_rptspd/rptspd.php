<?PHP
	//server 2020 10 20
	include "config/cek_akses_modul.php";
    $hari_ini = date("Y-m-d");
    $tgl_pertama = date('F Y', strtotime($hari_ini));
    $tgl_terakhir = date('F Y', strtotime($hari_ini));
    
    
    $aksi="eksekusi3.php";
    //$aksi="mrpt0000001.php";
?>
<div class='modal fade' id='myModal' role='dialog'></div>
<div class="">

    <div class="page-title"><div class="title_left"><h3>Rekap Surat Permintaan Dana</h3></div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">

        <?php

        ?>
        <form method='POST' action='<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]"; ?>' id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left' target="_blank">
            <div class='col-md-12 col-sm-12 col-xs-12'>
                <div class='x_panel'>

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
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Bulan <span class='required'></span></label>
                                    <div class='col-md-6'>
                                        <div class="form-group">
                                            <div class='input-group date' id='cbln01'>
                                                <input type='text' id='bulan1' name='bulan1' required='required' class='form-control' placeholder='tgl awal' value='<?PHP echo $tgl_pertama; ?>' placeholder='dd mmm yyyy' Readonly>
                                                <span class="input-group-addon">
                                                   <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>s/d. <span class='required'></span></label>
                                    <div class='col-md-6'>
                                        <div class="form-group">
                                            <div class='input-group date' id='cbln02'>
                                                <input type='text' id='bulan2' name='bulan2' required='required' class='form-control' placeholder='tgl awal' value='<?PHP echo $tgl_pertama; ?>' placeholder='dd mmm yyyy' Readonly>
                                                <span class="input-group-addon">
                                                   <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
								
								
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Report By <span class='required'></span></label>
                                    <div class='col-md-6'>
                                        <div class="form-group">
                                            <select class='form-control' id="cb_rptby" name="cb_rptby" onchange="ShowPeriodeDiv()">
                                                <?PHP
                                                echo "<option value='R' selected>Rincian</option>";
                                                echo "<option value='S'>Summary</option>";
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div id="div_periode">
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Periode By <span class='required'></span></label>
                                        <div class='col-md-6'>
                                            <div class="form-group">
                                                <select class='form-control' id="cb_periodepil" name="cb_periodepil" onchange="">
                                                    <?PHP
                                                    echo "<option value='NS' selected>Nomor SPD</option>";
                                                    echo "<option value='ND'>No. Divisi</option>";
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
            </div>
            
            
            <div class='col-md-12 col-sm-12 col-xs-12'>
                
                <div class='x_panel'>
                
                    
                    <div class="well" style="overflow: auto; margin-top: -5px; margin-bottom: 5px; padding-top: 10px; padding-bottom: 6px;">
                        <input onclick="pilihData('1')" class='btn btn-primary btn-sm' type='button' name='buttonview1' value='Preview Lampiran SPD'>
                        <?PHP
                        if ($_SESSION['GROUP']=="1" OR $_SESSION['GROUP']=="25" OR $_SESSION['GROUP']=="34" OR $_SESSION['GROUP']=="22" OR $_SESSION['GROUP']=="24") {
                        ?>
                        <input onclick="pilihDataSPD('approve')" class='btn btn-warning btn-sm' type='button' name='buttonview1' value='Isi Adjustment SPD'>
                        <?PHP
                        }
                        ?>
                        <input onclick="pilihData('2')" class='btn btn-success btn-sm' type='button' name='buttonview1' value='Outstanding SPD'>
						
						<input onclick="pilihData('3')" class='btn btn-dark btn-sm' type='button' name='buttonview1' value='Preview Lampiran PC-M'>
						
                    </div>
                    
                    <div id='loading'></div>
                    <div id='c-data'>
                        <div class='x_content'>
                            
                            <table id='datatable' class='table table-striped table-bordered' width='100%'>
                                <thead>
                                    <tr>
                                        <th width='10px'>No</th>
                                        <th width='100px'>No Divisi/NOBR</th>
                                        <th width='50px'>Jumlah</th>
                                        <th width='30px'>Divisi</th>
                                        <th width='50px'>Tgl Pengajuan</th>
                                        <th width='50px'>Bulan</th>
                                        <th width='30px'>Kode</th>
                                        <th width='250px'>Sub</th>
                                        <th width='30px'>Finance</th>
                                        <th width='30px'>Checker</th>
                                        <th width='30px'>Approved</th>
                                        <th width='30px'>Approved</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                </tbody>
                            </table>
                            
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
            document.getElementById("demo-form2").action = "<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]&ket=excel"; ?>";
            document.getElementById("demo-form2").submit();
            return 1;
        }else{
            document.getElementById("demo-form2").action = "<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]&ket=bukan"; ?>";
            document.getElementById("demo-form2").submit();
            return 1;
        }
    }
</script>

<script>
    $(document).ready(function() {
        //var eapvpilih=document.getElementById('e_apvpilih').value;
        //pilihData(eapvpilih);
		ShowPeriodeDiv();
    } );
    
    function ShowPeriodeDiv(){
        var myurl = window.location;
        var urlku = new URL(myurl);
        var nact = urlku.searchParams.get("act");
        
        var eperby = document.getElementById('cb_rptby').value;

        if (eperby=="" || eperby=="R"){
            div_periode.style.display = 'none';
        }else{
            div_periode.style.display = 'block';
        }
    }
	
	
    function pilihData(ket){
        var etgl1=document.getElementById('bulan1').value;
        var etgl2=document.getElementById('bulan2').value;
        
        $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
        $.ajax({
            type:"post",
            url:"module/laporan_gl/mod_gl_rptspd/viewdatatable.php?module="+ket,
            data:"eket="+ket+"&uperiode1="+etgl1+"&uperiode2="+etgl2,
            success:function(data){
                $("#c-data").html(data);
                $("#loading").html("");
            }
        });
        
    }
    
    function pilihDataSPD(ket){
        var etgl1=document.getElementById('bulan1').value;
        var etgl2=document.getElementById('bulan2').value;
        
        $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
        $.ajax({
            type:"post",
            url:"module/laporan_gl/mod_gl_rptspd/viewdatatablespd.php?module="+ket,
            data:"eket="+ket+"&uperiode1="+etgl1+"&uperiode2="+etgl2,
            success:function(data){
                $("#c-data").html(data);
                $("#loading").html("");
            }
        });
        
    }
</script>

<link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />
<style>
    .form-group, .input-group, .control-label {
        margin-bottom:3px;
    }
    .control-label {
        font-size:12px;
    }
    input[type=text] {
        box-sizing: border-box;
        color:#000;
        font-size:12px;
        height: 30px;
    }
    select.soflow {
        font-size:12px;
        height: 30px;
    }
    .disabledDiv {
        pointer-events: none;
        opacity: 0.4;
    }

    .disabledDiv {
        pointer-events: none;
        opacity: 0.4;
    }
</style>


