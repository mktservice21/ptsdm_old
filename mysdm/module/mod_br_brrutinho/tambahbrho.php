<?php

$pidmodule=$_GET['module'];
$pidmenu=$_GET['idmenu'];
$pidact=$_GET['act'];
$pstsmobile=$_SESSION['MOBILE'];

$piduser=$_SESSION['USERID'];
$pidcard=$_SESSION['IDCARD'];
$pidgroup=$_SESSION['GROUP'];

$hari_ini = date("Y-m-d");
//$pbln = date('F Y', strtotime($hari_ini));
$pbln = date('F Y', strtotime('-1 month', strtotime($hari_ini)));

$mytglini="";
$mytglini = getfield("select CURRENT_DATE as lcfields");
if ($mytglini==0) $mytglini="";
if (!empty($mytglini)) $hari_ini = date('Y-m-d', strtotime($mytglini));
$iniharinya=date('d', strtotime($mytglini));

$tglhariini = getfield("select DATE_FORMAT(CURRENT_DATE(),'%d') as lcfields ");
if ($tglhariini=="0") $tglhariini="";
if (empty($tglhariini)) $tglhariini = date("d");

//$ptgl1 = date('01/m/Y', strtotime($hari_ini));
//$ptgl2 = date('t/m/Y', strtotime($hari_ini));
$ptgl1 = date('01/m/Y', strtotime('-1 month', strtotime($hari_ini)));
$ptgl2 = date('t/m/Y', strtotime('-1 month', strtotime($hari_ini)));

$pidrutin="";
$pkaryawanid=$_SESSION['IDCARD'];
$pkaryawannm=$_SESSION['NAMALENGKAP'];

$pfilterkrypilih="";
if ($pidgroup=="50") {
    $query ="select karyawanid as karyawanid from dbmaster.t_karyawan_mkt_dir";
    $tampiln= mysqli_query($cnmy, $query);
    while ($nrow= mysqli_fetch_array($tampiln)) {
        $pkryplid=$nrow['karyawanid'];
        
        $pfilterkrypilih="'".$pkryplid."',";
    }
}
if (!empty($pfilterkrypilih)) $pfilterkrypilih="(".substr($pfilterkrypilih, 0, -1).")";
else $pfilterkrypilih="('00XXX00')";



$pkdperiode="";
$pselper0="";
$pselper1="";
$pselper2="selected";

$pketerangan="";

$ptotalsemua=0;

$pdivisi="HO";
$pjabatanid=$_SESSION['JABATANID'];

$pidcabang="0000000001";//ETH HO
$pidarea="0000000001";//ETH HO

$pidnopol = getfield("select nopol as lcfields from dbmaster.t_kendaraan_pemakai where karyawanid='$pkaryawanid' AND IFNULL(stsnonaktif,'')<>'Y' order by tglawal desc LIMIT 1");

$pidatasan = getfield("select atasanid as lcfields from hrd.karyawan WHERE karyawanid='$pkaryawanid'");
if (empty($pidatasan)) {
    $pidatasan = getfield("select atasanid as lcfields from dbmaster.t_karyawan_posisi WHERE karyawanid='$pkaryawanid'");
}
if (empty($pidatasan)) {
    $ptampil = mysqli_query($cnmy, "SELECT atasanid as atasanid FROM dbmaster.t_brrutin0 WHERE "
            . " kode=1 AND karyawanid='$pidcard' AND IFNULL(stsnonaktif,'')<>'Y' order by idrutin desc LIMIT 1 ");
    $nrow= mysqli_fetch_array($ptampil);
    $pidatasan=$nrow['atasanid'];
}
$act="input";
if ($pidact=="editdata"){
    $act="update";
    $pidrutin=$_GET['id'];
    if ($pidgroup=="50") {
        $edit = mysqli_query($cnmy, "SELECT * FROM dbmaster.t_brrutin0 WHERE kode=1 AND idrutin='$pidrutin' AND ( karyawanid='$pidcard' OR karyawanid IN $pfilterkrypilih )");
    }else{
        $edit = mysqli_query($cnmy, "SELECT * FROM dbmaster.t_brrutin0 WHERE kode=1 AND idrutin='$pidrutin' AND karyawanid='$pidcard'");
    }
    $pketemu    = mysqli_num_rows($edit);
    if ((DOUBLE)$pketemu<=0) { exit; }
    $r    = mysqli_fetch_array($edit);
    
    $pbln = date('F Y', strtotime($r['bulan']));
    $ptgl1 = date('d/m/Y', strtotime($r['periode1']));
    $ptgl2 = date('d/m/Y', strtotime($r['periode2']));
        
    $pdivisi=$r['divisi'];
    $pjabatanid=$r['jabatanid'];
    $pidcabang=$r['icabangid'];
    $pidarea=$r['areaid'];
    $pidnopol=$r['nopol'];
    
    $pkaryawanid=$r['karyawanid'];
    $pkaryawannm = getfield("select nama as lcfields from hrd.karyawan where karyawanid='$pkaryawanid'");
    
    $pkdperiode=$r['kodeperiode'];
    if ($pkdperiode==1) {
        $pselper1="selected";
        $pselper2="";
    }elseif ($pkdperiode==2) {
        $pselper1="";
        $pselper2="selected";
    }
    
    $pketerangan=$r['keterangan'];
    $pidatasan=$r['atasan4'];
    
    $ptotalsemua=$r['jumlah'];
    
    
}

?>

<script> window.onload = function() { document.getElementById("e_id").focus(); } </script>

<div class="">
    
    
    <!--row-->
    <div class="row">
        
    
        <div class='col-md-12 col-sm-12 col-xs-12'>
            
            <div class='x_panel'>
                
                <form method='POST' action='<?PHP echo "$aksi?module=$pidmodule&act=input&idmenu=$pidmenu"; ?>' 
                      id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left'  enctype='multipart/form-data'>
                    
                    <div class='col-md-12 col-sm-12 col-xs-12'>
                        <h2>
                            <a class='btn btn-default' href="<?PHP echo "?module=$pidmodule&idmenu=$pidmenu&act=$pidmenu"; ?>">Back</a>
                        </h2>
                        <div class='clearfix'></div>
                    </div>
                    
                    
                    <div class='x_panel'>
                        <div class='x_content'>
                            
                            
                            <div class='col-md-12 col-sm-12 col-xs-12'>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>ID <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input type='text' id='e_id' name='e_id' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidrutin; ?>' Readonly>
                                        <input type='hidden' id='e_idinputuser' name='e_idinputuser' class='form-control col-md-7 col-xs-12' value='<?PHP echo $piduser; ?>' Readonly>
                                        <input type='hidden' id='e_idcarduser' name='e_idcarduser' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidcard; ?>' Readonly>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Yang Membuat <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input type='hidden' id='e_divisiid' name='e_divisiid' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pdivisi; ?>' Readonly>
                                        <input type='hidden' id='e_jabatanid' name='e_jabatanid' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pjabatanid; ?>' Readonly>
                                        <input type='hidden' id='e_cabangid' name='e_cabangid' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidcabang; ?>' Readonly>
                                        <input type='hidden' id='e_areaid' name='e_areaid' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidarea; ?>' Readonly>
                                        <input type='hidden' id='e_nopolid' name='e_nopolid' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidnopol; ?>' Readonly>
                                        <?PHP
                                        if ($pidgroup=="50") {
                                            echo "<input type='hidden' id='e_nmkaryawan' name='e_nmkaryawan' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pkaryawannm; ?>' Readonly>";
                                            echo "<select class='form-control input-sm' id='e_idkaryawan' name='e_idkaryawan'>";
                                            $query = "select karyawanid as karyawanid, nama as nama FROM hrd.karyawan as a WHERE 1=1 "
                                                    . " AND ( karyawanid IN ('$pidcard', '$pkaryawanid') OR karyawanid IN $pfilterkrypilih )";
                                            $tampilk=mysqli_query($cnmy, $query);
                                            while ($krow= mysqli_fetch_array($tampilk)) {
                                                $npkryid=$krow['karyawanid'];
                                                $npkrynm=$krow['nama'];
                                                
                                                if ($npkryid==$pkaryawanid)
                                                    echo "<option value='$npkryid' selected>$npkrynm</option>";
                                                else
                                                    echo "<option value='$npkryid'>$npkrynm</option>";
                                            }
                                            echo "</select>";
                                        }else{
                                            echo "<input type='text' id='e_nmkaryawan' name='e_nmkaryawan' class='form-control col-md-7 col-xs-12' value='$pkaryawannm' Readonly>";
                                            echo "<input type='hidden' id='e_idkaryawan' name='e_idkaryawan' class='form-control col-md-7 col-xs-12' value='$pkaryawanid;' Readonly>";
                                        }
                                        ?>
                                    </div>
                                </div>
                                
                                
                               <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Bulan </label>
                                    <div class='col-md-3'>
                                        <div class='input-group date' id='thnbln01x'>
                                            <input type='text' class='form-control' id='e_bulan' name='e_bulan' autocomplete='off' value='<?PHP echo $pbln; ?>' />
                                            <span class='input-group-addon'>
                                                <span class='glyphicon glyphicon-calendar'></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Kode Periode <span class='required'></span></label>
                                    <div class='col-xs-7'>
                                        <select class='form-control input-sm' id='e_periode' name='e_periode' onchange="showPeriode()">
                                            <?PHP
                                                if ((int)$tglhariini > 20) {
                                                    echo "<option value='' $pselper0>-- Pilihan --</option>";
                                                    echo "<option value='2' $pselper2>Periode 2</option>";
                                                }else{
                                                    echo "<option value='' $pselper0>-- Pilihan --</option>";
                                                    echo "<option value='1' $pselper1>Periode 1</option>";
                                                    echo "<option value='2' $pselper2>Periode 2</option>";
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </div>


                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='mytgl01'>Periode <span class='required'></span></label>
                                    <div class='col-md-3'>
                                        <div class="form-group">
                                            <div class='input-group date' id='mytgl01x'>
                                                <input type='text' id='e_periode01' name='e_periode01' autocomplete='off' required='required' class='form-control' placeholder='dd/MM/yyyy' value='<?PHP echo $ptgl1; ?>' data-inputmask="'mask': '99/99/9999'" Readonly>
                                                <span class="input-group-addon">
                                                   <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                            </div>
                                            <div class='input-group date' id='mytgl02x'>
                                                <input type='text' id='e_periode02' name='e_periode02' autocomplete='off' required='required' class='form-control' placeholder='dd/MM/yyyy' value='<?PHP echo $ptgl2; ?>' data-inputmask="'mask': '99/99/9999'" Readonly>
                                                <span class="input-group-addon">
                                                   <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Keterangan <span class='required'></span></label>
                                    <div class='col-md-6 col-sm-6 col-xs-12'>
                                        <textarea class='form-control' id='e_ket' name='e_ket' rows='3' placeholder='Aktivitas'><?PHP echo $pketerangan; ?></textarea>
                                    </div><!--disabled='disabled'-->
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Atasan <span class='required'></span></label>
                                    <div class='col-xs-7'>
                                        <select class='form-control input-sm' id='e_atasan' name='e_atasan' onchange="">
                                            <?PHP
                                                echo "<option value='' selected>-- Pilihan --</option>";
                                                $query = "select karyawanId as karyawanid, nama as nama From hrd.karyawan WHERE karyawanid='$pidatasan'";
                                                $query .= " ORDER BY nama";
                                                $tampil = mysqli_query($cnmy, $query);
                                                $ketemu= mysqli_num_rows($tampil);
                                                
                                                if ((DOUBLE)$ketemu==0) {
                                                    $query = "select karyawanId as karyawanid, nama as nama From hrd.karyawan WHERE 1=1 ";
                                                    $query .= " AND (IFNULL(tglkeluar,'0000-00-00')='0000-00-00' OR IFNULL(tglkeluar,'')='') ";
                                                    $query .=" AND LEFT(nama,4) NOT IN ('NN -', 'DR -', 'DM -', 'BDG ', 'OTH.', 'TO. ', 'BGD-', 'JKT ', 'MR -', 'MR S')  "
                                                            . " and LEFT(nama,7) NOT IN ('NN DM - ', 'MR SBY1')  "
                                                            . " and LEFT(nama,3) NOT IN ('TO.', 'TO-', 'DR ', 'DR-', 'JKT', 'NN-', 'TO ') "
                                                            . " AND LEFT(nama,5) NOT IN ('OTH -', 'NN AM', 'NN DR', 'TO - ', 'SBY -', 'RS. P') "
                                                            . " AND LEFT(nama,6) NOT IN ('SBYTO-', 'MR SBY') ";
                                                    $query .= " AND nama NOT IN ('ACCOUNTING')";
                                                    $query .= " AND karyawanid NOT IN ('0000002200', '0000002083')";
                                                    $query .= " ORDER BY nama";
                                                    $tampil = mysqli_query($cnmy, $query);
                                                }
                                                
                                                while ($z= mysqli_fetch_array($tampil)) {
                                                    $pkaryid=$z['karyawanid'];
                                                    $pkarynm=$z['nama'];
                                                    $pkryid=(INT)$pkaryid;
                                                    
                                                    if ($pkaryid==$pidatasan)
                                                        echo "<option value='$pkaryid' selected>$pkarynm ($pkryid)</option>";
                                                    else
                                                        echo "<option value='$pkaryid'>$pkarynm ($pkryid)</option>";
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Total Rp. <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input type='text' id='e_totalsemua' name='e_totalsemua' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $ptotalsemua; ?>' readonly>
                                    </div>
                                </div>
                                
                                
                                
                                
                            </div>
                            
                            
                            
                            <style>
                                .form-group, .input-group, .control-label {
                                    margin-bottom:2px;
                                }
                                .control-label {
                                    font-size:11px;
                                }
                                #datatable input[type=text], #tabelnobr input[type=text] {
                                    box-sizing: border-box;
                                    color:#000;
                                    font-size:11px;
                                    height: 25px;
                                }
                                select.soflow {
                                    font-size:12px;
                                    height: 30px;
                                }
                                .disabledDiv {
                                    pointer-events: none;
                                    opacity: 0.4;
                                }

                                table.datatable, table.tabelnobr {
                                    color: #000;
                                    font-family: Helvetica, Arial, sans-serif;
                                    width: 100%;
                                    border-collapse:
                                    collapse; border-spacing: 0;
                                    font-size: 11px;
                                    border: 0px solid #000;
                                }

                                table.datatable td, table.tabelnobr td {
                                    border: 1px solid #000; /* No more visible border */
                                    height: 10px;
                                    transition: all 0.1s;  /* Simple transition for hover effect */
                                }

                                table.datatable th, table.tabelnobr th {
                                    background: #DFDFDF;  /* Darken header a bit */
                                    font-weight: bold;
                                }

                                table.datatable td, table.tabelnobr td {
                                    background: #FAFAFA;
                                }

                                /* Cells in even rows (2,4,6...) are one color */
                                tr:nth-child(even) td { background: #F1F1F1; }

                                /* Cells in odd rows (1,3,5...) are another (excludes header cells)  */
                                tr:nth-child(odd) td { background: #FEFEFE; }

                                tr td:hover.biasa { background: #666; color: #FFF; }
                                tr td:hover.left { background: #ccccff; color: #000; }

                                tr td.center1, td.center2 { text-align: center; }

                                tr td:hover.center1 { background: #666; color: #FFF; text-align: center; }
                                tr td:hover.center2 { background: #ccccff; color: #000; text-align: center; }
                                /* Hover cell effect! */
                                tr td {
                                    padding: -10px;
                                }

                            </style>
                            
                            
                            <?PHP if ($pstsmobile=="Y") { ?>
                                <br/>&nbsp;<div style="overflow-x:auto;">
                                    <?PHP
                                        include "module/mod_br_brrutinho/inputdetailmobileho.php";
                                    ?>
                                </div>
                            <?PHP }else{
                                include "module/mod_br_brrutinho/inputdetailbrho.php";
                            }
                            ?>
                            
                                
                                
                        </div>
                    </div>
                    
                    <div class='col-md-12 col-sm-12 col-xs-12'>
                        <h2>
                            <?PHP
                            if (empty($sudahapv)) {
                                if ($pidact=="editdata" ) {
                                    ?><button type='button' class='btn btn-success' onclick='disp_confirm("Simpan ?", "<?PHP echo $act; ?>")'>Save</button><?PHP
                                }else{
                                echo "<div class='col-sm-5'>";
                                include "module/mod_br_brrutinho/ttd_brrutinho.php";
                                echo "</div>";
                                }
                            ?>
                            <!--<button type='button' class='btn btn-success' onclick='disp_confirm("Simpan ?", "<?PHP echo $act; ?>")'>Save</button>-->
                            <?PHP
                            }elseif ($sudahapv=="reject") {
                                echo "data sudah hapus";
                            }else{
                                echo "tidak bisa diedit, sudah approve";
                            }
                            ?>
                        </h2>
                        <div class='clearfix'></div>
                    </div>
                    
                    
                </form>
                
            </div>
            
        </div>
        
    </div>
    
    
</div>

<link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />
<link href="css/stylenew.css" rel="stylesheet" type="text/css" />

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


<script>
                                    
    $(document).ready(function() {

        var dataTable = $('#datatable').DataTable( {
            "ordering": false,
            bFilter: false, bInfo: false, "bLengthChange": false, "bLengthChange": false,
            "bPaginate": false
        } );
        
        $('#e_bulan').datepicker({
            showButtonPanel: true,
            changeMonth: true,
            changeYear: true,
            dateFormat: 'MM yy',
            <?PHP
            if ($iniharinya=="01") {
            ?>
                minDate: '-1M',
            <?PHP
            }else{
            ?>
                minDate: '-1M',
            <?PHP
            }
            ?>
            onSelect: function(dateStr) {
                
            },
            onClose: function() {
                var iMonth = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                var iYear = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                $(this).datepicker('setDate', new Date(iYear, iMonth, 1));
                showKodePeriode();
                showPeriode();
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


    function showKodePeriode() {
        var ibulan = document.getElementById('e_bulan').value;
        $.ajax({
            type:"post",
            url:"module/mod_br_brrutinho/viewdatabrho.php?module=getkodeperiode",
            data:"ubulan="+ibulan,
            success:function(data){
                $("#e_periode").html(data);
            }
        });
    }
    function showPeriode() {
        var ikode = document.getElementById('e_periode').value;
        var ibulan = document.getElementById('e_bulan').value;
        
        $.ajax({
            type:"post",
            url:"module/mod_br_brrutinho/viewdatabrho.php?module=getperiode",
            data:"ubulan="+ibulan+"&ukode="+ikode,
            success:function(data){
                var arr_date = data.split(",");
                document.getElementById('e_periode01').value=arr_date[0];
                document.getElementById('e_periode02').value=arr_date[1];
            }
        });
    }
    
    
    
    
    function disp_confirm(pText_, ket)  {
        
        var iid = document.getElementById('e_id').value;
        var ikry = document.getElementById('e_idkaryawan').value;
        var ibln = document.getElementById('e_bulan').value;
        var ikdperiode = document.getElementById('e_periode').value;
        var iperiode01 = document.getElementById('e_periode01').value;
        var iatasan = document.getElementById('e_atasan').value;
        var etotsem =document.getElementById('e_totalsemua').value;


        if (ikry=="") {
            alert("Pembuat masih kosong...");
            return false;
        }

        if (ibln=="") {
            alert("Bulan masih kosong...");
            return false;
        }

        if (ikdperiode=="") {
            alert("Kode periode masih kosong...");
            return false;
        }

        if (iperiode01=="") {
            alert("periode masih kosong...");
            return false;
        }

        if (iatasan=="") {
            alert("Atasan masih kosong...");
            return false;
        }

        if (parseFloat(etotsem)==0) {
            alert("Total Rupiah Masih Kosong....");
            return 0;
        }
        
        $.ajax({
            type:"post",
            url:"module/mod_br_brrutinho/viewdatabrho.php?module=cekdatasudahada",
            data:"uid="+iid+"&ukry="+ikry+"&ubln="+ibln+"&ukdperiode="+ikdperiode,
            success:function(data){
                //var tjml = data.length;
                //alert(data);
                //return false;

                if (data=="boleh") {
            
                    ok_ = 1;
                    if (ok_) {
                        var r=confirm(pText_)
                        if (r==true) {
                            //document.write("You pressed OK!")
                            var myurl = window.location;
                            var urlku = new URL(myurl);
                            var module = urlku.searchParams.get("module");
                            var idmenu = urlku.searchParams.get("idmenu");

                            document.getElementById("demo-form2").action = "module/mod_br_brrutinho/aksi_brrutinho.php?module="+module+"&act="+ket+"&idmenu="+idmenu;
                            document.getElementById("demo-form2").submit();
                            return 1;
                        }
                    } else {
                        //document.write("You pressed Cancel!")
                        return 0;
                    }
                        
                        
                }else{
                    alert(data);
                }
            }
        });
                        
                        
    }
    
    
</script>

