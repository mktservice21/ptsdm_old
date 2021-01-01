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
    $pkode="1";
    $psubkode="03";
    $pidspd="";
    
    $aksi="eksekusi3.php";
    include "config/koneksimysqli_it.php";
?>
<div class="">

    <div class="page-title"><div class="title_left"><h3>Rekap Insentif Rekening Bank</h3></div></div><div class="clearfix"></div>
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
        
    } );
                    
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
        var iperiode = document.getElementById('e_periode').value;
        $.ajax({
            type:"post",
            url:"module/laporan/mod_rekap_brrutin_rek/viewdata.php?module=caribuktisudahada",
            data:"uperiode="+iperiode+"&utgl="+ibulan,
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
        var iperiode = document.getElementById('e_periode').value;
        
        $.ajax({
            type:"post",
            url:"module/laporan/mod_rekap_brrutin_rek/viewdata.php?module=viewnomorbukti",
            data:"uperiode="+iperiode+"&utgl="+ibulan,
            success:function(data){
                document.getElementById('e_nomordiv').value=data;
                $("#div_jumlah").html("");
            }
        });
    }
    
    function HitungTotalJumlahData() {
        var ibulan = document.getElementById('bulan1').value;
        var iperiode = document.getElementById('e_periode').value;
        var istsapv = document.getElementById('sts_apv').value;
        var inodata = document.getElementById('e_idspd').value;
        
        $("#loading2").html("<center><img src='images/loading.gif' width='50px'/></center>");
        $.ajax({
            type:"post",
            url:"module/laporan/mod_rekap_brrutin_rek/viewdata.php?module=hitungtotaldata&act=hitung",
            data:"e_periode="+iperiode+"&bulan1="+ibulan+"&sts_apv="+istsapv+"&uiddata="+inodata,
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