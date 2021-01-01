
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
    
    
    $aksi="eksekusi3.php";
?>

<div class="">

    <div class="page-title"><div class="title_left"><h3>Rekap Biaya Marketing</h3></div></div><div class="clearfix"></div>
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
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Divisi <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='form-control' id="divprodid" name="divprodid" onchange="ShowCOA('divprodid', 'cb_coa');">
                                            <?PHP
                                            $query = "select DivProdId from MKT.divprod WHERE br='Y' AND DivProdId NOT IN ('OTHER') ";
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
    function ShowCOA(udiv, ucoa) {
        var icar = "";
        var idiv = document.getElementById(udiv).value;
        $.ajax({
            type:"post",
            url:"module/surabaya/mod_sby_lap_rekapbm/viewdata.php?module=viewcoadivisi",
            data:"umr="+icar+"&udivi="+idiv,
            success:function(data){
                $("#"+ucoa).html(data);
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
</script>

<style>
    .ui-datepicker-calendar {
        display: none;
    }
</style>