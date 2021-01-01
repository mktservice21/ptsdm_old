<?php

session_start();
include "../../../config/koneksimysqli.php";

if ($_GET['module']=="divnonspd"){
    $hari_ini = date("Y-m-d");
    $tgl_pertama = date('F Y', strtotime($hari_ini));
?>
    <div class='form-group'>
        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Divisi <span class='required'></span></label>
        <div class='col-xs-6'>
            <select class='form-control' id="divprodid" name="divprodid" onchange="">
                <?PHP
                $query = "select DivProdId from MKT.divprod WHERE br='Y' AND DivProdId NOT IN ('OTC', 'OTHER', 'CAN')";
                $query .=" order by DivProdId";
                $tampil = mysqli_query($cnmy, $query);
                echo "<option value='' selected>-- All --</option>";
                while ($z= mysqli_fetch_array($tampil)) {
                    $pdivisi=$z['DivProdId'];
                    if ($pdivisi=="CAN") $pdivisi="CANARY";
                    echo "<option value='$z[DivProdId]'>$pdivisi</option>";
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
                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                </div>
            </div>
        </div>
    </div>

    <div hidden class='form-group'>
        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Approve <span class='required'></span></label>
        <div class='col-xs-6'>
            <select class='form-control' id="sts_apv" name="sts_apv">
                <option value="">All</option>
                <option value="belumfin">Belum Proses Finance</option>
                <option value="fin" selected>Sudah Proses Finance</option>
            </select>
        </div>
    </div>

    <div class='form-group'>
        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Status Finance <span class='required'></span></label>
        <div class='col-xs-6'>
            <select class='form-control' id="sts_rpt" name="sts_rpt">
                <option value="">All</option>
                <option value="C" selected>Sudah Closing</option>
                <option value="S">Susulan</option>
                <option value="B">Belum Closing</option>
            </select>
        </div>
    </div>

    <script>
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
                    //ShowData();
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
    
<?PHP
}elseif ($_GET['module']=="divspd"){
    $hari_ini = date("Y-m-d");
    $tgl_pertama = date('F Y', strtotime($hari_ini));
    $tgl1 = date('Ym', strtotime($hari_ini));
?>
    
    <div class='form-group'>
        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Periode <span class='required'></span></label>
        <div class='col-md-6'>
            <div class="form-group">
                <div class='input-group date' id=''>
                    <input type='text' id='bulan1' name='bulan1' required='required' class='form-control' placeholder='tgl awal' value='<?PHP echo $tgl_pertama; ?>' placeholder='dd mmm yyyy' Readonly>
                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                </div>
            </div>
        </div>
    </div>
    
    <div class='form-group'>
        <label class='control-label col-md-3 col-sm-3 col-xs-12'>No. BR/Divisi &nbsp;<input type="checkbox" id="chkbtnnodiv" value="deselect" onClick="SelAllCheckBox('chkbtnnodiv', 'chkbox_nodiv[]')" checked/><span class='required'></span></label>
        <div class='col-md-6 col-sm-6 col-xs-12'>
            <div id="kotak-multi9">
                
            </div>
        </div>
    </div>
    
    <script>
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
                    ShowDataNoBR('2');
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
    
    
<?PHP
}elseif ($_GET['module']=="showdatanobr"){
    $tgl01=$_POST['utgl'];
    $pnopilih=$_POST['unom'];
    if (empty($tgl01) OR $pnopilih=="1") {
        $tgl01 = date("Y-m-d");
    }
    $periode= date("Ym", strtotime($tgl01));
    
?>
    <table border="0px" width="100%">
    <?PHP
    $query = "select a.*, b.nama, b.subnama, FORMAT(a.jumlah,0,'de_DE') rpjumlah from dbmaster.t_suratdana_br a JOIN "
            . " dbmaster.t_kode_spd b on a.kodeid=b.kodeid AND a.subkode=b.subkode "
            . " WHERE IFNULL(a.stsnonaktif,'') <> 'Y' and DATE_FORMAT(a.tgl,'%Y%m')='$periode' "
            . " AND CONCAT(a.kodeid, a.subkode) IN ('221') AND IFNULL(a.pilih,'')='Y' "
            . " AND IFNULL(a.nodivisi,'')<>''"
            . " order by a.divisi, a.nomor, a.nodivisi";
    
    $tampil=mysqli_query($cnmy, $query) or die("error");
    while( $row=mysqli_fetch_array($tampil) ) {
        $cdivisi=$row['divisi'];
        if (empty($cdivisi)) $cdivisi = "ETHICAL";
        $cnodivisi=$row['nodivisi'];
        $cidinput=$row['idinput'];

        $cnmkode=strtolower($row['nama']);
        $cnmsub=strtolower($row['subnama']);

        $cjumlah=$row['rpjumlah'];

        //echo "<input type=checkbox value='$cidinput' id='$cidinput' name=chkbox_nodiv[] onclick=\"\" checked> $cdivisi - $cnodivisi &nbsp; &nbsp; ($cnmkode) &nbsp; &nbsp; <b>Rp. $cjumlah</b><br/>";
        echo "<tr>";
        echo "<td><input type=checkbox value='$cidinput' id='$cidinput' name=chkbox_nodiv[] onclick=\"\" checked></td>";
        echo "<td>$cdivisi &nbsp;&nbsp;</td>";
        echo "<td>$cnodivisi &nbsp;&nbsp;</td>";
        echo "<td>$cnmkode &nbsp;&nbsp;</td>";
        echo "<td><b>Rp.</b></td>";
        echo "<td nowrap align='right'><b>$cjumlah</b></td>";
        echo "<td>&nbsp;</td>";
        echo "</tr>";
    }
    ?>
    </table>
<?PHP  
}elseif ($_GET['module']=="xx"){
    
}
?>


<style>
    .ui-datepicker-calendar {
        display: none;
    }
</style>