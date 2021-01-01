<link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />
<link href="css/stylenew.css" rel="stylesheet" type="text/css" />

<?PHP

$hari_ini = date("Y-m-d");
$mytglini="";
$mytglini = getfield("select CURRENT_DATE as lcfields");
if ($mytglini==0) $mytglini="";
if (!empty($mytglini)) $hari_ini = date('Y-m-d', strtotime($mytglini));
$iniharinya=date('d', strtotime($mytglini));

$tglberlku = date('F Y', strtotime($hari_ini));
$tgl1 = date('01/m/Y', strtotime($hari_ini));
$tgl2 = date('t/m/Y', strtotime($hari_ini));


$sudahapv="";
$idklaim="";
$idajukan=$_SESSION['IDCARD'];
$keterangan="";
$totalsemua=0;


$act="input";

if ($_GET['act']=="editdata"){
    $act="update";
    
    $edit = mysqli_query($cnmy, "SELECT * FROM dbmaster.v_brrutin0 WHERE kode=2 AND idrutin='$_GET[id]'");
    $r    = mysqli_fetch_array($edit);
    $idklaim=$r['idrutin'];
    $tglberlku = date('F Y', strtotime($r['bulan']));
    $tgl1 = date('d/m/Y', strtotime($r['periode1']));
    $tgl2 = date('d/m/Y', strtotime($r['periode2']));
        
    $idajukan=$r['karyawanid']; 
    $nmajukan=$r['nama']; 
    $keterangan=$r['keterangan'];
    $kdperiode=$r['kodeperiode'];
    if ($kdperiode==1) $selper1="selected";
    if ($kdperiode==2) $selper2="selected";
    
    $totalsemua=$r['jumlah'];
    
    
        
}
    
?>

<div class="">

    <!--row-->
    <div class="row">

        
        <div class='col-md-12 col-sm-12 col-xs-12'>
            <div class='x_panel'>
                
                
                <form method='POST' action='<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]"; ?>' id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left'  enctype='multipart/form-data'>

                    <div class='col-md-12 col-sm-12 col-xs-12'>
                        <h2>
                            <a class='btn btn-default' href="<?PHP echo "?module=$_GET[module]&idmenu=$_GET[idmenu]&act=$_GET[idmenu]"; ?>">Back</a>
                        </h2>
                        <div class='clearfix'></div>
                    </div>
                    
                    <div class='x_panel'>
                        <div class='x_content'>
                            <div class='col-md-12 col-sm-12 col-xs-12'>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>ID <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input type='text' id='e_id' name='e_id' class='form-control col-md-7 col-xs-12' value='<?PHP echo $idklaim; ?>' Readonly>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_idkaryawan'>Yang Membuat <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='form-control input-sm' id='e_idkaryawan' name='e_idkaryawan' onchange="">
                                            <?PHP
                                            $query = "SELECT DISTINCT karyawanId, nama FROM hrd.karyawan WHERE "
                                                    . " ( IFNULL(tglkeluar,'0000-00-00')='0000-00-00' OR IFNULL(tglkeluar,'')='') "
                                                    . " AND (divisiid='OTC' OR karyawanId='$idajukan') ";
                                            if ($_SESSION['GROUP']!=1) $query .=" AND (karyawanId='$idajukan' OR karyawanId='$_SESSION[IDCARD]')";
                                            $query .=" order by nama, karyawanId";
                                            $tampil = mysqli_query($cnmy, $query);
                                            echo "<option value='' selected>--PILIH--</option>";
                                            while($a=mysqli_fetch_array($tampil)){ 
                                                $nkaryawanid=$a['karyawanId'];
                                                $nnama=$a['nama'];
                                                if ($nkaryawanid==$idajukan)
                                                    echo "<option value='$nkaryawanid' selected>$nnama</option>";
                                                else
                                                    echo "<option value='$nkaryawanid'>$nnama</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='thnbln01x'>Bulan </label>
                                    <div class='col-md-3'>
                                        
                                        
                                        <div class='input-group date' id='thnbln01x'>
                                            <input type='text' class='form-control' id='e_bulan' name='e_bulan' autocomplete='off' value='<?PHP echo $tglberlku; ?>' />
                                            <span class='input-group-addon'>
                                                <span class='glyphicon glyphicon-calendar'></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                
                                <div hidden class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Kode Periode <span class='required'></span></label>
                                    <div class='col-xs-7'>
                                        <select class='form-control input-sm' id='e_periode' name='e_periode' onchange="showPeriode()">
                                            <?PHP
                                            echo "<option value='1' selected>Periode 1</option>";
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='mytgl01'>Periode <span class='required'></span></label>
                                    <div class='col-md-3'>
                                        <div class="form-group">
                                            <div class='input-group date' id='mytgl01x'>
                                                <input type='text' id='e_periode01' name='e_periode01' autocomplete='off' required='required' class='form-control' placeholder='dd/MM/yyyy' value='<?PHP echo $tgl1; ?>' data-inputmask="'mask': '99/99/9999'" Readonly>
                                                <span class="input-group-addon">
                                                   <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                            </div>
                                            <div class='input-group date' id='mytgl02x'>
                                                <input type='text' id='e_periode02' name='e_periode02' autocomplete='off' required='required' class='form-control' placeholder='dd/MM/yyyy' value='<?PHP echo $tgl2; ?>' data-inputmask="'mask': '99/99/9999'" Readonly>
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
                                        <textarea class='form-control' id='e_ket' name='e_ket' rows='3' placeholder='Aktivitas'><?PHP echo $keterangan; ?></textarea>
                                    </div><!--disabled='disabled'-->
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Total Rp. <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input type='text' id='e_totalsemua' name='e_totalsemua' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $totalsemua; ?>' readonly>
                                    </div>
                                </div>
                                
                                
                                
                                
                                <?PHP if ($_SESSION['MOBILE']=="Y") { ?>
                                    <br/>&nbsp;
                                    <div style="overflow-x:auto;">
                                        <?PHP include "module/mod_br_entrybrluarkota/inputdetailmobile.php"; ?>
                                    </div>
                                <?PHP }else{
                                    include "module/mod_br_entrybrluarkota/inputdetail.php";
                                }
                                ?>
                                
                                
                                
                            </div>
                        </div>
                        
                    </div>

                    
                    <div class='col-md-12 col-sm-12 col-xs-12'>
                        <h2>
                            <?PHP
                            if (empty($sudahapv)) {
                                if ($_GET['act']=="editdata" AND !isset($_GET['ca'])) {
                                    ?><button type='button' class='btn btn-success' onclick='disp_confirm("Simpan ?", "<?PHP echo $act; ?>")'>Save</button><?PHP
                                }else{
                                echo "<div class='col-sm-5'>";
                                include "module/mod_br_entrybrluarkotaotc/ttd_biayaluarkotaotc.php";
                                echo "</div>";
                                }
                            ?>
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
    <!--END row-->
    
</div>


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
            if ($_SESSION['DIVISI']!="OTC") {
                if ($_SESSION['GROUP']=="1" OR $_SESSION['GROUP']=="28") {
                    ?>
                     minDate: '-5M',
                    <?PHP
                }else{
                    if ($iniharinya=="01") {
                    ?>
                        minDate: '-1M',
                    <?PHP
                    }else{
                    ?>
                        minDate: '0M',
                    <?PHP
                    }
                }
            }
            ?>
            onSelect: function(dateStr) {
                
            },
            onClose: function() {
                var iMonth = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                var iYear = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                $(this).datepicker('setDate', new Date(iYear, iMonth, 1));
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


    function showPeriode() {
        var ikode = document.getElementById('e_periode').value;
        var ibulan = document.getElementById('e_bulan').value;
        $.ajax({
            type:"post",
            url:"module/mod_br_entrybrcash/viewdatams.php?module=getperiode",
            data:"ubulan="+ibulan+"&ukode="+ikode,
            success:function(data){
                var arr_date = data.split(",");
                document.getElementById('e_periode01').value=arr_date[0];
                document.getElementById('e_periode02').value=arr_date[1];
            }
        });
    }
    
    
    function hit_total(pNilai_,pQty_,pTotal_) {
        
        nilai = document.getElementById(pNilai_).value;  
        qty = document.getElementById(pQty_).value;

        var newchar = '';
        var mynilai = nilai;  
        mynilai = mynilai.split(',').join(newchar);
        var myqty = qty;  
        myqty = myqty.split(',').join(newchar);
        
        total_ = mynilai * myqty;
        document.getElementById(pTotal_).value = total_;
        findTotal();
        
        
    }
    
    function findTotal(){
        var newchar = '';
        var a1 = document.getElementById('e_total1').value;
        var a2 = document.getElementById('e_total2').value;
        var a3 = document.getElementById('e_total3').value;
        var a4 = document.getElementById('e_total4').value;
        var a5 = document.getElementById('e_total5').value;
        var a6 = document.getElementById('e_total6').value;
        var a7 = document.getElementById('e_total7').value;
        var a8 = document.getElementById('e_total8').value;
        var a9 = document.getElementById('e_total9').value;
        
        a1 = a1.split(',').join(newchar);
        a2 = a2.split(',').join(newchar);
        a3 = a3.split(',').join(newchar);
        a4 = a4.split(',').join(newchar);
        a5 = a5.split(',').join(newchar);
        a6 = a6.split(',').join(newchar);
        a7 = a7.split(',').join(newchar);
        a8 = a8.split(',').join(newchar);
        a9 = a9.split(',').join(newchar);
        if (a1 === "") a1=0; if (a2 === "") a2=0; if (a3 === "") a3=0; if (a4 === "") a4=0;
        if (a5 === "") a5=0; if (a6 === "") a6=0; if (a7 === "") a7=0; if (a8 === "") a8=0;
        if (a9 === "") a9=0;
        
        tot =parseInt(a1)+parseInt(a2)+parseInt(a3)+parseInt(a4)+parseInt(a5)+parseInt(a6)
            +parseInt(a7)+parseInt(a8)+parseInt(a9);
        document.getElementById('e_totalsemua').value = tot;
    }
    
    
    function disp_confirm(pText_, ket)  {
        var ekar =document.getElementById('e_idkaryawan').value;
        var eperi =document.getElementById('e_periode').value;
        var etotsem =document.getElementById('e_totalsemua').value;
        
        if (etotsem === "") etotsem=0;

        if (ekar==""){
            alert("yang membuat masih kosong....");
            return 0;
        }

        if (eperi==""){
            alert("periode harus diisi....");
            return 0;
        }
            

        if (parseInt(etotsem)==0) {
            alert("Total Rupiah Masih Kosong....");
            return 0;
        }
            
        ok_ = 1;
        if (ok_) {
            var r=confirm(pText_)
            if (r==true) {
                //document.write("You pressed OK!")
                var myurl = window.location;
                var urlku = new URL(myurl);
                var module = urlku.searchParams.get("module");
                var idmenu = urlku.searchParams.get("idmenu");

                document.getElementById("demo-form2").action = "module/mod_br_entrybrluarkotaotc/aksi_entrybrluarkotaotc.php?module="+module+"&act="+ket+"&idmenu="+idmenu;
                document.getElementById("demo-form2").submit();
                return 1;
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }
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
                            