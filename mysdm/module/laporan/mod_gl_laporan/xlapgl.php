<script>
    function ShowCOA(udiv, ucoa) {
        var icar = "";
        var idiv = document.getElementById(udiv).value;
        $.ajax({
            type:"post",
            url:"module/laporan/mod_gl_laporan/viewdata.php?module=viewcoadivisi",
            data:"umr="+icar+"&udivi="+idiv,
            success:function(data){
                $("#"+ucoa).html(data);
            }
        });
    }
</script>
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
    $tgl_pertama = date('01 F Y', strtotime($hari_ini));
    $tgl_terakhir = date('t F Y', strtotime($hari_ini));
    
    
    $aksi="eksekusi3.php";
    include "config/koneksimysqli_it.php";
?>
<div class="">

    <div class="page-title"><div class="title_left"><h3>Laporan Transaksi Budget Request</h3></div></div><div class="clearfix"></div>
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
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Pilihan <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                          <select class='form-control input-sm' id='cb_pilih' name='cb_pilih' data-live-search="true">
                                              <option value=''>-- ALL --</option>
                                              <option value='A' selected>BR</option>
                                              <option value='B'>KLAIM</option>
                                              <option value='C'>KAS</option>
                                              <option value='D'>BR OTC</option>
                                              <!--<option value='E'>BR RUTIN / LUAR KOTA ALL</option>-->
                                              <option value='F'>RUTIN</option>
                                              <option value='G'>LUAR KOTA</option>
                                              <option value='H'>CA RUTIN</option>
                                              <!--<option value='I'>CA LUAR KOTA</option>-->
                                              
                                          </select>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Divisi <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='form-control' id="divprodid" name="divprodid" onchange="ShowCOA('divprodid', 'cb_coa');">
                                            <?PHP
                                            $query = "select DivProdId from MKT.divprod WHERE br='Y' ";
                                            $query .=" order by DivProdId";
                                            $tampil = mysqli_query($cnmy, $query);
                                            echo "<option value='' selected>All</option>";
                                            while ($z= mysqli_fetch_array($tampil)) {
                                                $pdivisi=$z['DivProdId'];
                                                if ($pdivisi=="CAN") $pdivisi="CANARY";
                                                if ($z['DivProdId']==$divisi)
                                                    echo "<option value='$z[DivProdId]' selected>$pdivisi</option>";
                                                else
                                                    echo "<option value='$z[DivProdId]'>$pdivisi</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_coa'>COA / Posting <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                          <select class='form-control input-sm' id='cb_coa' name='cb_coa' data-live-search="true">
                                              <option value='' selected>-- Pilihan --</option>
                                              <?PHP 
                                                    $query = "select a.COA4, a.NAMA4 from dbmaster.coa_level4 a ";
                                                    $query .= " ORDER BY a.COA4";
                                                    $tampil = mysqli_query($cnmy, $query);
                                                    while ($z= mysqli_fetch_array($tampil)) {
                                                        echo "<option value='$z[COA4]'>$z[COA4] - $z[NAMA4]</option>";
                                                    }
                                                
                                              ?>
                                          </select>
                                    </div>
                                </div>
                                
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Periode <span class='required'></span></label>
                                    <div class='col-md-6'>
                                        <div class="form-group">
                                            <div class='input-group date' id=''>
                                                <input type='text' id='tglfrom' name='bulan1' required='required' class='form-control' placeholder='tgl awal' value='<?PHP echo $tgl_pertama; ?>' placeholder='dd mmm yyyy' Readonly>
                                                <span class="input-group-addon">
                                                   <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                            </div>
                                            
                                            <div class='input-group date' id=''>
                                                <input type='text' id='tglto' name='bulan2' required='required' class='form-control' placeholder='tgl awal' value='<?PHP echo $tgl_terakhir; ?>' placeholder='dd mmm yyyy' Readonly>
                                                <span class="input-group-addon">
                                                   <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                            </div>
                                            
                                        </div>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Report Type <span class='required'></span></label>
                                    <div class='col-md-6'>
                                        <input type="radio" id="radio1" name="radio1" value="D" checked> Detail
                                        <input type="radio" id="radio1" name="radio1" value="S"> Summary
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

<script type="text/javascript">
    
    $(function() {
        $('#tglfromxxx').datepicker({
            numberOfMonths: 1, /* bisa dua month*/
            firstDay: 1,
            dateFormat: 'dd MM yy', /* bisa DD dd-mm-yy*/
            <?PHP //if ($_SESSION['USERGRP']=="SPV" or $_SESSION['USERGRP']=="MR" or $_SESSION['USERGRP']=="PM"){ ?>
           // minDate: '-2', /* bisa min bisa plus -1 0 2*/
            /*maxDate: '+1d', /* bisa +2Y*/
            <?PHP //} ?>
            onSelect: function(dateStr) {
                var min = $(this).datepicker('getDate');
                $('#tgltoxxx').datepicker('option', 'minDate', min || '0');
                datepicked();
            } 
        });
    });
    var datepicked = function() {
        var tgl01 = $('#tglfromxxx');
        var to = $('#tgltoxxx');
        var nights = $('#nights');
        var fromDate = tgl01.datepicker('getDate')
        var toDate = to.datepicker('getDate')
    }
</script>

    <script type="text/javascript">
        $(function() {
            $('#tglfrom').datepicker({
                changeMonth: true,
                changeYear: true,
                numberOfMonths: 1,
                firstDay: 1,
                dateFormat: 'dd MM yy',
                /*
                minDate: '0',
                maxDate: '+2Y',
                */
                onSelect: function(dateStr) {
                    var min = $(this).datepicker('getDate');
                    $('#tglto').datepicker('option', 'minDate', min || '0');
                    datepicked();
                } 
            });
            $('#tglto').datepicker({
                changeMonth: true,
                changeYear: true,
                numberOfMonths: 1,
                firstDay: 1,
                dateFormat: 'dd MM yy',
                minDate: '0',
                /*
                minDate: '0',
                maxDate: '+2Y',
                */
                onSelect: function(dateStr) {
                    var max = $(this).datepicker('getDate');
                    $('#tglfrom').datepicker('option', 'maxDate', max || '+2Y');
                    datepicked();
                } 
            });
        });
        var datepicked = function() {
            var from = $('#from');
            var to = $('#to');
            var nights = $('#nights');
            var fromDate = from.datepicker('getDate')
            var toDate = to.datepicker('getDate')
            if (toDate && fromDate) {
                var difference = 0;
                var oneDay = 1000 * 60 * 60 * 24;
                var difference = Math.ceil((toDate.getTime() - fromDate.getTime()) / oneDay);
                nights.val(difference);
            }
        }
    </script>