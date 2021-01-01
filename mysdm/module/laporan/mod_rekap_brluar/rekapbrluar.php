<?PHP
    $jabatan_="";
    $fildiv="";
    $tampilbawahan = "N";
    $filkaryawncabang = "";
    $hanyasatukaryawan = "";
    $fildiv = "('OTC')";
    if (!empty($_SESSION['AKSES_JABATAN'])) {
        $jabatan_ = $_SESSION['AKSES_JABATAN'];
    }

    if (!empty($_SESSION['AKSES_CABANG'])) {
        $filkaryawncabang = $_SESSION['AKSES_CABANG'];
    }
    $hari_ini = date("Y-m-d");
    $tgl_pertama = date('F Y', strtotime($hari_ini));
    $tgl_terakhir = date('F Y', strtotime($hari_ini));
    
    
    $pdivnomor="";
    $pkode="2";
    $psubkode="21";
    $pidspd="";
    
    
    $aksi="eksekusi3.php";
    include "config/koneksimysqli_it.php";
?>
<div class="">

    <div class="page-title"><div class="title_left"><h3>Realisasi Luar Kota</h3></div></div><div class="clearfix"></div>
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
                                            <div class='input-group date' id='cbln01x'>
                                                <input type='text' id='bulan1' name='bulan1' required='required' class='form-control' placeholder='tgl awal' value='<?PHP echo $tgl_pertama; ?>' placeholder='dd mmm yyyy' Readonly>
                                                <span class="input-group-addon">
                                                   <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div hidden class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Approve <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='form-control' id="sts_apv" name="sts_apv">
                                            <!--<option value="">All</option>
                                            <option value="belumfin">Belum Proses Finance</option>-->
                                            <option value="fin" selected>Sudah Proses Finance</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Status <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='form-control' id="sts_rpt" name="sts_rpt" onchange="pilihSudahProses()"><!--onchange="ShowData()"-->
                                            <option value="" selected>All</option>
                                            <option value="C">Sudah Closing</option>
                                            <option value="S">Susulan</option>
                                            <option value="B">Belum Closing</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div id="div_pilihproses">
                                    <div class='form-group' hidden>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Pilih Sudah Proses <span class='required'></span></label>
                                        <div class='col-xs-9'>
                                            <select class='form-control' id="sts_sudahprosesid" name="sts_sudahprosesid">
                                                
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                
                                
                                <div hidden class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>No. BR <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <input type='hidden' id='e_idspd' name='e_idspd' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidspd; ?>'>
                                        <input type='text' id='e_nomordiv' name='e_nomordiv' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pdivnomor; ?>'>
                                    </div>
                                </div>
                                
                                <div hidden class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <button type='button' class='btn btn-info btn-xs' onclick='HitungTotalJumlahData()'>Input Pengajuan Dana</button> <span class='required'></span>
                                        <button type='button' class='btn btn-default btn-xs' onclick='TutupData()'>Tutup</button> <span class='required'></span>
                                    </div>
                                </div>

                                <div id='loading2'></div>
                                <div id="div_jumlah">
                                    
                                    
                                    
                                </div>
                                

                            </div>
                        </div>           
                    </div>

                </div>
            </div>
        </form>

    </div>
    <!--end row-->
</div>

<script>
    $(document).ready(function() {
        ShowData();
        var ests=document.getElementById('sts_rpt').value;
        if (ests=="C") {
            pilihSudahProses();
        }
        
    } );
    
    function pilihSudahProses(){
        var ests=document.getElementById('sts_rpt').value;
        var etgl1=document.getElementById('bulan1').value;
        $.ajax({
            type:"post",
            url:"module/laporan/mod_realisasibl/viewdata.php?module=tampilkansudahproses",
            data:"usts="+ests+"&utgl="+etgl1,
            success:function(data){
                $("#div_pilihproses").html(data);
            }
        });
    }
    
    
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
    
    function TutupData(){
        $("#div_jumlah").html("");
    }
    
    function hapus_data(pText, act) {
        var cmt = confirm('Apakah akan hapus data...?');
        if (cmt == false) {
            return false;
        }
        
        var ibulan = document.getElementById('bulan1').value;
        var istsapv = document.getElementById('sts_apv').value;
        var istsrpt = document.getElementById('sts_rpt').value;
        
        var inobrdiv = document.getElementById('e_nomordiv').value;
        var inodata = document.getElementById('e_idspd').value;
        
        $("#loading2").html("<center><img src='images/loading.gif' width='50px'/></center>");
        $.ajax({
            type:"post",
            url:"module/laporan/mod_rekap_brluar/viewdata.php?module=hapusdata&act=hapus",
            data:"bulan1="+ibulan+"&sts_apv="+istsapv+"&unobukti="+inobrdiv+"&uiddata="+inodata+"&sts_rpt="+istsrpt,
            success:function(data){
                $("#loading2").html("");
                document.getElementById('e_idspd').value="";
                document.getElementById('e_nomordiv').style.color="red";
                ShowData();
                alert(data);
            }
        });
        
    }
    function simpan_data(pText, act) {
        var etotcan =document.getElementById('e_totcan').value;
        var ican = etotcan.replace(/\,/g,'');
        
        var etoteagle =document.getElementById('e_totegl').value;
        var ieagle = etoteagle.replace(/\,/g,'');
        
        var etotho =document.getElementById('e_totho').value;
        var iho = etotho.replace(/\,/g,'');
        
        var etotpeaco =document.getElementById('e_totpeac').value;
        var ipeaco = etotpeaco.replace(/\,/g,'');
        
        var etotpeago =document.getElementById('e_totpeog').value;
        var ipeago = etotpeago.replace(/\,/g,'');
        
        var etototc =document.getElementById('e_tototh').value;
        var iother = etototc.replace(/\,/g,'');
        
        itotal = parseFloat(ican)+parseFloat(ieagle)+parseFloat(iho)+parseFloat(ipeaco)+parseFloat(ipeago)+parseFloat(iother);
        
        
        var ibulan = document.getElementById('bulan1').value;
        var istsapv = document.getElementById('sts_apv').value;
        var istsrpt = document.getElementById('sts_rpt').value;
        
        var inobrdiv = document.getElementById('e_nomordiv').value;
        var inodata = document.getElementById('e_idspd').value;
        var itotrans = document.getElementById('e_tottrans').value;
        
        //simpan data ke DB
        var cmt = confirm('Apakah akan simpan data...?');
        if (cmt == false) {
            return false;
        }
            
        $("#loading2").html("<center><img src='images/loading.gif' width='50px'/></center>");
        $.ajax({
            type:"post",
            url:"module/laporan/mod_rekap_brluar/viewdata.php?module=simpandata&act=simpan",
            data:"bulan1="+ibulan+"&sts_apv="+istsapv+"&unobukti="+inobrdiv+"&uiddata="+inodata+"&sts_rpt="+istsrpt+"&utotrans="+itotrans,
            success:function(data){
                $("#loading2").html("");
                document.getElementById('e_idspd').value=inobrdiv;
                document.getElementById('e_nomordiv').style.color="black";
                HitungTotalJumlahData();
                alert(data);
            }
        });
        
        
    }
    
    
    $(function() {
        $('#bulan1').datepicker({
            showButtonPanel: true,
            changeMonth: true,
            changeYear: true,
            numberOfMonths: 1,
            firstDay: 1,
            dateFormat: 'MM yy',
            onSelect: function(dateStr) {
                
            },
            onClose: function() {
                var iMonth = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                var iYear = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                $(this).datepicker('setDate', new Date(iYear, iMonth, 1));
                ShowData();
                pilihSudahProses();
            },

            beforeShow: function() {
                if ((selDate = $(this).val()).length > 0) 
                {
                    iYear = selDate.substring(selDate.length - 4, selDate.length);
                    iMonth = jQuery.inArray(selDate.substring(0, selDate.length - 5), $(this).datepicker('option', 'monthNames'));
                    $(this).datepicker('option', 'defaultDate', new Date(iYear, iMonth, 1));
                    $(this).datepicker('setDate', new Date(iYear, iMonth, 1));
                }
            }
        });
    });
    
    function ShowData() {
        CariBuktiSudahAda();
        ShowNoBukti();
    }
    
    function CariBuktiSudahAda() {
        var ibulan = document.getElementById('bulan1').value;
        var istsrpt = document.getElementById('sts_rpt').value;
        
        $.ajax({
            type:"post",
            url:"module/laporan/mod_rekap_brluar/viewdata.php?module=caribuktisudahada",
            data:"utgl="+ibulan+"&sts_rpt="+istsrpt,
            success:function(data){
                document.getElementById('e_idspd').value=data;
                document.getElementById('e_nomordiv').style.color="black";
                if (data=="") {
                    document.getElementById('e_nomordiv').style.color="red";
                }
            }
        });
    }
    
    function ShowNoBukti() {
        var ibulan = document.getElementById('bulan1').value;
        var istsrpt = document.getElementById('sts_rpt').value;
        
        $.ajax({
            type:"post",
            url:"module/laporan/mod_rekap_brluar/viewdata.php?module=viewnomorbukti",
            data:"utgl="+ibulan+"&sts_rpt="+istsrpt,
            success:function(data){
                document.getElementById('e_nomordiv').value=data;
                $("#div_jumlah").html("");
            }
        });
    }
    
    function HitungTotalJumlahData() {
        var ibulan = document.getElementById('bulan1').value;
        var istsapv = document.getElementById('sts_apv').value;
        var istsrpt = document.getElementById('sts_rpt').value;
        var inodata = document.getElementById('e_idspd').value;
        
        $("#loading2").html("<center><img src='images/loading.gif' width='50px'/></center>");
        $.ajax({
            type:"post",
            url:"module/laporan/mod_rekap_brluar/viewdata.php?module=hitungtotaldata&act=hitung",
            data:"bulan1="+ibulan+"&sts_apv="+istsapv+"&uiddata="+inodata+"&sts_rpt="+istsrpt,
            success:function(data){
                $("#loading2").html("");
                $("#div_jumlah").html(data);
            }
        });
    }
</script>

<style>
    .ui-datepicker-calendar {
        display: none;
    }
    
    .divnone {
        display: none;
    }
    #datatableuc th {
        font-size: 12px;
    }
    #datatableuc td { 
        font-size: 12px;
        padding: 3px;
        margin: 1px;
    }
</style>