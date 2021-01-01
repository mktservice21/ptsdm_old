<link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />
<link href="css/stylenew.css" rel="stylesheet" type="text/css" />
<script src="js/inputmask.js"></script>
<?PHP
    ini_set("memory_limit","5000M");
    ini_set('max_execution_time', 0);
    
    session_start();
    include "../../config/koneksimysqli.php";
    include "../../config/fungsi_sql.php";
    include "../../config/library.php";
    
    $ptahun=$_POST['utahun'];
    
    $pmodule=$_GET['module'];
    $pidmenu=$_GET['idmenu'];
?>

<form method='POST' action='<?PHP echo "?module=$pmodule&act=input&idmenu=$pidmenu"; ?>' id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left'>
    
    <div class='x_content'>
        
        <table id='datatable' class='datatable table nowrap table-striped table-bordered' width="100%">
            <thead>
                <tr>
                    <th width='10px'>No</th>
                    <th width='300px' align="center">Bulan</th>
                    <th width='200px' align="center">Jml Hari Kerja SPG</th>
                    <th width='200px' align="center">Jml Hari Kerja ASPR</th>
                    <th width='100px' align="center" nowrap></th>
                </tr>
            </thead>
            <tbody>
            <?PHP
                $pkalender=CAL_GREGORIAN;
                $no=1;
                for($x=1;$x<=12;$x++) {
                    $nmbulan="Januari";
                    if ($x==1) $nmbulan="Januari";
                    if ($x==2) $nmbulan="Februari";
                    if ($x==3) $nmbulan="Maret";
                    if ($x==4) $nmbulan="April";
                    if ($x==5) $nmbulan="Mei";
                    if ($x==6) $nmbulan="Juni";
                    if ($x==7) $nmbulan="Juli";
                    if ($x==8) $nmbulan="Agustus";
                    if ($x==9) $nmbulan="September";
                    if ($x==10) $nmbulan="Oktober";
                    if ($x==11) $nmbulan="November";
                    if ($x==12) $nmbulan="Desember";
                    
                    
                    //$jmlkerja= cal_days_in_month($pkalender, $x, $ptahun);
                    $jmlkerja="";
                    $jmlkerja_aspr="";
                    $nthnblnhari="$ptahun-$x-01";
                    $pthnblnhari= date("Y-m-d", strtotime($nthnblnhari));
                    $ptglnya= date("Y-m", strtotime($nthnblnhari));
                    
                    
                    $query = "select * from dbmaster.t_spg_jmlharikerja WHERE DATE_FORMAT(periode,'%Y-%m')='$ptglnya'";
                    $tampil = mysqli_query($cnmy, $query);
                    while ($sp= mysqli_fetch_array($tampil)) {
                        if (!empty($sp['jumlah']))$jmlkerja=$sp['jumlah'];
                        if (!empty($sp['jml_aspr']))$jmlkerja_aspr=$sp['jml_aspr'];
                    }
                    
                    $finthnbln="<input type='hidden' size='10px' id='txtthnbln$no' name='txtthnbln$no' class='input' autocomplete='off' value='$pthnblnhari'>";
                    
                    $finharikerja="<input type='text' size='10px' id='txtjmlhk$no' name='txtjmlhk$no' class='input-sm inputmaskrp2' autocomplete='off' value='$jmlkerja'>";
                    
                    $finharikerja_aspr="<input type='text' size='10px' id='txtjmlhk_aspr$no' name='txtjmlhk_aspr$no' class='input-sm inputmaskrp2' autocomplete='off' value='$jmlkerja_aspr'>";
                    
                    $fsimpan="'txtthnbln$no', 'txtjmlhk$no', 'txtjmlhk_aspr$no'";
                    $simpandata= "<input type='button' class='btn btn-info btn-xs' id='s-submit' value='Save' onclick=\"SimpanData('input', $fsimpan)\">";
                        
                    echo "<tr>";
                    echo "<td nowrap>$no</td>";
                    echo "<td nowrap>$nmbulan $finthnbln</td>";
                    echo "<td nowrap>$finharikerja</td>";
                    echo "<td nowrap>$finharikerja_aspr</td>";
                    echo "<td nowrap>$simpandata</td>";
                    echo "</tr>";
                    
                    $no++;
                }
            ?>
            </tbody>
        </table>
        
    </div>
    
</form>

<style>
    .divnone {
        display: none;
    }
    #datatablespggj th {
        font-size: 12px;
    }
    #datatablespggj td { 
        font-size: 11px;
    }
    .imgzoom:hover {
        -ms-transform: scale(3.5); /* IE 9 */
        -webkit-transform: scale(3.5); /* Safari 3-8 */
        transform: scale(3.5);
        
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

<script>
    
    $(document).ready(function() {
        var dataTable = $('#datatable').DataTable( {
            "ordering": false,
            bFilter: false, bInfo: false, "bLengthChange": false, "bLengthChange": false,
            "bPaginate": false,
            "scrollX": true
        } );
    });
    
    function SimpanData(eact, aperiode, ajumlah, ajmlaspr)  {
        var eperiode =document.getElementById(aperiode).value;
        var ejumlah =document.getElementById(ajumlah).value;
        var ejmlaspr =document.getElementById(ajmlaspr).value;
        
        if (eperiode==""){
            alert("periode kosong....");
            return 0;
        }
        
        var pText_="Simpan";
        if (eact=="hapus") var pText_="Hapus";
        
        ok_ = 1;
        if (ok_) {
            var r=confirm(pText_)
            if (r==true) {
                var myurl = window.location;
                var urlku = new URL(myurl);
                var module = urlku.searchParams.get("module");
                var idmenu = urlku.searchParams.get("idmenu");

                $.ajax({
                    type:"post",
                    url:"module/md_m_spg_jmlharikerja/aksi_jmlharikerja.php?module="+module+"&act="+eact+"&idmenu="+idmenu,
                    data:"uperiode="+eperiode+"&uharikerja="+ejumlah+"&uhkaspr="+ejmlaspr,
                    success:function(data){
                        if (data.length > 2) {
                            alert(data);
                        }
                    }
                });
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }
        
        
    }
    
</script>