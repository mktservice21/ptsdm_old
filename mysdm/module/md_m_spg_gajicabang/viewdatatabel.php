<link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />
<link href="css/stylenew.css" rel="stylesheet" type="text/css" />
<script src="js/inputmask.js"></script>
<?PHP
    ini_set("memory_limit","5000M");
    ini_set('max_execution_time', 0);
    
    session_start();
    include "../../config/koneksimysqli_it.php";
    include "../../config/fungsi_sql.php";
    include "../../config/library.php";
    $date1=$_POST['utgl'];
    $tgl1= date("Y-m-01", strtotime($date1));
    $bulan= date("Ym", strtotime($date1));
    
    $_SESSION['SPGMSTGJTGLCAB']=date("F Y", strtotime($date1));
    
?>


<form method='POST' action='<?PHP echo "?module=$_POST[module]&act=input&idmenu=$_POST[idmenu]"; ?>' id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left'>
    
    
    <input type='hidden' id='u_module' name='u_module' value='<?PHP echo $_POST['module']; ?>' Readonly>
    <input type='hidden' id='u_idmenu' name='u_idmenu' value='<?PHP echo $_POST['idmenu']; ?>' Readonly>
    <input type='hidden' id='u_tgl1' name='u_tgl1' value='<?PHP echo $tgl1; ?>' Readonly>
    <input type='hidden' id='u_act' name='u_act' value='input' Readonly>
    
    
    
    <div class='x_content'>
        <table id='datatable' class='datatable table nowrap table-striped table-bordered' width="100%">
            <thead>
                <tr>
                    <th width='10px'>No</th>
                    <th width='300px' align="center">Cabang</th>
                    <th align="center" nowrap>Gaji Pokok</th>
                    <th align="center" nowrap>U. Makan</th>
                    <th align="center" nowrap>Sewa Kendaraan</th>
                    <th align="center" nowrap>Pulsa</th>
                    <th align="center" nowrap>Parkir</th>
                    <th align="center" nowrap>BBM</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?PHP
                    $no=1;
                    $query = "select icabangid_o, nama from dbmaster.v_icabang_o WHERE aktif='Y' AND i_spg='Y' AND nama NOT IN ('OTHER1', 'OTHER2')";
                    $query .=" ORDER BY nama";
                    $tampil = mysqli_query($cnit, $query);
                    while ($sp= mysqli_fetch_array($tampil)) {
                        $pidcabang=$sp['icabangid_o'];
                        
                        $fcabang = "  icabangid = '$pidcabang' ";
                        if (empty($pidcabang)) $fcabang = "  IFNULL(icabangid,'') = '' ";
                        
                        if ($pidcabang=="JKT_MT") {
                            $fcabang = "  IFNULL(icabangid,'') = '0000000007' AND alokid='001' ";
                        }elseif ($pidcabang=="JKT_RETAIL") {
                            $fcabang = "  IFNULL(icabangid,'') = '0000000007' AND alokid='002' ";
                        }
                        
                        $pnmcabang=$sp['nama'];
                        
                        $queryg="select * from dbmaster.t_spg_gaji_cabang WHERE DATE_FORMAT(periode,'%Y%m')='$bulan' and $fcabang";
                        $tampilg= mysqli_query($cnmy, $queryg);
                        $gj= mysqli_fetch_array($tampilg);
                        $pgaji=$gj['gaji'];
                        $pumakan=$gj['umakan'];
                        $psewakendaraan=$gj['sewakendaraan'];
                        $ppulsa=$gj['pulsa'];
                        $pparkir=$gj['parkir'];
                        $pbbm=$gj['bbm'];
                        
                        $finbulan="<input type='hidden' id='txtbulan$no' name='txtbulan$no' class='input-sm' value='$tgl1'>";
                        $fincabang="<input type='hidden' id='txtcabang$no' name='txtcabang$no' class='input-sm' value='$pidcabang'>";
                        
                        $fingaji="<input type='text' size='10px' id='txtgaji$no' name='txtgaji$no' class='input-sm inputmaskrp2' autocomplete='off' value='$pgaji'>";
                        $finumakan="<input type='text' size='10px' id='txtumkn$no' name='txtumkn$no' class='input-sm inputmaskrp2' autocomplete='off' value='$pumakan'>";
                        $finsewa="<input type='text' size='10px' id='txtsewa$no' name='txtsewa$no' class='input-sm inputmaskrp2' autocomplete='off' value='$psewakendaraan'>";
                        $finpulsa="<input type='text' size='10px' id='txtpulsa$no' name='txtpulsa$no' class='input-sm inputmaskrp2' autocomplete='off' value='$ppulsa'>";
                        $finparkir="<input type='text' size='10px' id='txtparkir$no' name='txtparkir$no' class='input-sm inputmaskrp2' autocomplete='off' value='$pparkir'>";
                        $finbbm="<input type='text' size='10px' id='txtbbm$no' name='txtbbm$no' class='input-sm inputmaskrp2' autocomplete='off' value='$pbbm'>";
                        
                        $fsimpan="'txtbulan$no', 'txtcabang$no', 'txtgaji$no', 'txtumkn$no', 'txtsewa$no', 'txtpulsa$no', 'txtparkir$no', 'txtbbm$no'";
                        
                        $simpandata= "<input type='button' class='btn btn-info btn-xs' id='s-submit' value='Save' onclick=\"SimpanData('input', $fsimpan)\">";
                        $hapusdata= "<input type='button' class='btn btn-danger btn-xs' id='s-hapus' value='Hapus' onclick=\"SimpanData('hapus', $fsimpan)\">";
                        
                        echo "<tr>";
                        echo "<td>$no</td>";
                        echo "<td>$pnmcabang $finbulan $fincabang</td>";
                        echo "<td>$fingaji</td>";
                        echo "<td>$finumakan</td>";
                        echo "<td>$finsewa</td>";
                        echo "<td>$finpulsa</td>";
                        echo "<td>$finparkir</td>";
                        echo "<td>$finbbm</td>";
                        echo "<td align='right' nowrap>$simpandata $hapusdata</td>";
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
<?PHP //$fsimpan="txtidspg$no, txtbulan$no, txtcabang$no, txtgaji$no, txtumkn$no, txtsewa$no, txtpulsa$no, txtparkir$no"; ?>                            
<script>
    
    function SimpanData(eact, abulan, acabang, agaji, amakan, asewa, apulsa, aparkir, abbm)  {
        
        var ebulan =document.getElementById(abulan).value;
        var ecabang =document.getElementById(acabang).value;
        var egaji =document.getElementById(agaji).value;
        var emakan =document.getElementById(amakan).value;
        var esewa =document.getElementById(asewa).value;
        var epulsa =document.getElementById(apulsa).value;
        var eparkir =document.getElementById(aparkir).value;
        var ebbm =document.getElementById(abbm).value;
        
        //alert(espg+", "+ebulan+", "+ecabang+", "+egaji+", "+emakan+", "+esewa+", "+epulsa+", "+eparkir+", "+ebbm); return 0;
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
                    url:"module/md_m_spg_gajicabang/aksi_spggajicabang.php?module="+module+"&act="+eact+"&idmenu="+idmenu,
                    data:"ubulan="+ebulan+"&ucabang="+ecabang+"&ugaji="+egaji+"&umakan="+emakan+"&usewa="+esewa+"&upulsa="+epulsa+"&uparkir="+eparkir+"&ubbm="+ebbm,
                    success:function(data){
                        alert(data);
                        if (eact=="hapus") {
                            document.getElementById(agaji).value="";
                            document.getElementById(amakan).value="";
                            document.getElementById(asewa).value="";
                            document.getElementById(apulsa).value="";
                            document.getElementById(aparkir).value="";
                            document.getElementById(abbm).value="";
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