<?PHP
    date_default_timezone_set('Asia/Jakarta');
    session_start();
    $puserid=$_SESSION['IDCARD'];
    if (empty($puserid)) {
        echo "ANDA HARUS LOGIN ULANG....!!!";
        exit;
    }
    ini_set("memory_limit","5000M");
    ini_set('max_execution_time', 0);
    $module=$_GET['module'];
    $idmenu=$_GET['idmenu'];
    $act=$_GET['act'];
?>
<link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />
<link href="css/stylenew.css" rel="stylesheet" type="text/css" />
<script src="js/inputmask.js"></script>
<?php
    include "../../config/koneksimysqli.php";
    $date1=$_POST['utgl'];
    $ptgl= date("Y-m-01", strtotime($date1));
    $pbulan= date("Ym", strtotime($date1));
    $pidcabang=$_POST['ucabang'];
    $pstspilih=$_POST['ustspilih'];
    $ppilihdataproses=$_POST['uketpilih'];
    
    $_SESSION['SPGMSTIMPCAB']=$pidcabang;
    $_SESSION['SPGMSTIMPTGL']=date("F Y", strtotime($date1));
    $_SESSION['SPGMSTIMPSTS']=$pstspilih;
    $_SESSION['SPGMSTIMPPILIH']=$ppilihdataproses;
    
    
    $filter_cabang="";
    if (!empty($pidcabang)) {
        if ($pidcabang=="JKT_MT") $filter_cabang =" AND icabangid='0000000007' AND alokid='001' ";
        elseif ($pidcabang=="JKT_RETAIL") $filter_cabang =" AND icabangid='0000000007' AND alokid='002' ";
        else $filter_cabang =" AND icabangid='$pidcabang' ";
    }
    
    $filter_stsaktif="";
    if ($pstspilih=="A") {
        $filter_stsaktif =" AND (IFNULL(tglkeluar,'')='' OR IFNULL(tglkeluar,'0000-00-00')='0000-00-00') ";
    }elseif ($pstspilih=="T") {
        $filter_stsaktif =" AND (IFNULL(tglkeluar,'')<>'' AND IFNULL(tglkeluar,'0000-00-00')<>'0000-00-00') ";
    }
    
    
    
    $now=date("mdYhis");
    $tmp01 =" dbtemp.DSPGIMPITMS01_".$puserid."_$now ";
    $tmp02 =" dbtemp.DSPGIMPITMS02_".$puserid."_$now ";
    $tmp03 =" dbtemp.DSPGIMPITMS03_".$puserid."_$now ";
    $tmp04 =" dbtemp.DSPGIMPITMS04_".$puserid."_$now ";
    
    $query = "SELECT * FROM dbmaster.t_spg_data WHERE DATE_FORMAT(periode,'%Y%m')='$pbulan' $filter_cabang";//$filter_stsaktif
    $query = "create TEMPORARY table $tmp03 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


    if ($ppilihdataproses=="1") {
        $query = "SELECT * FROM MKT.spg WHERE 1=1 $filter_cabang $filter_stsaktif AND "
                . " id_spg NOT IN (select distinct IFNULL(id_spg,'') FROM $tmp03)";
        //$query = "SELECT * FROM MKT.spg WHERE id_spg in (select distinct id_spg from dbmaster.t_spg_gaji_br0 WHERE DATE_FORMAT(periode,'%Y%m')='$pbulan' and stsnonaktif<>'Y')";
    }else{
        $query = "SELECT * FROM $tmp03";
    }
    $query = "create TEMPORARY table $tmp01 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "SELECT a.*, CAST('' as CHAR(1)) as stransaksi, b.nama nama_cabang, c.nama nama_area, d.id_zona, e.nama_zona, f.jabatanid, f.nama_jabatan FROM $tmp01 a LEFT JOIN MKT.icabang_o b on a.icabangid=b.icabangid_o "
            . " LEFT JOIN MKT.iarea_o c on a.icabangid=c.icabangid_o AND a.areaid=c.areaid_o "
            . " LEFT JOIN dbmaster.t_spg_gaji_area_zona d on a.icabangid=d.icabangid AND a.areaid=d.areaid "
            . " LEFT JOIN dbmaster.t_zona e on d.id_zona=e.id_zona "
            . " LEFT JOIN dbmaster.t_spg_jabatan f on a.jabatid=f.jabatid";
    $query = "create TEMPORARY table $tmp02 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    if ($ppilihdataproses=="2") {
        $query = "SELECT * FROM dbmaster.t_spg_gaji_br0 WHERE IFNULL(stsnonaktif,'')<>'Y' AND DATE_FORMAT(periode,'%Y%m')='$pbulan' AND "
                . " id_spg IN (select distinct IFNULL(id_spg,'') FROM $tmp02)";
        $query = "create TEMPORARY table $tmp04 ($query)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        $query = "UPDATE $tmp02 a JOIN $tmp04 b on a.id_spg=b.id_spg SET a.stransaksi='Y'";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    }
    
    $query = "UPDATE $tmp02 SET nama_cabang='JAKARTA MT', icabangid='JKT_MT' WHERE icabangid='0000000007' AND alokid='001'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }

    $query = "UPDATE $tmp02 SET nama_cabang='JAKARTA RETAIL', icabangid='JKT_RETAIL' WHERE icabangid='0000000007' AND alokid='002'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }

    $query = "ALTER TABLE $tmp02 ADD COLUMN bulan_bpjs DATE,  ADD COLUMN nobpjs_kerja VARCHAR(150)";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }

    $query = "UPDATE $tmp02 as a JOIN dbmaster.t_spg_bpjs as b on a.id_spg=b.id_spg SET a.bulan_bpjs=b.bulan, a.nobpjs_kerja=b.nobpjs_kerja";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }

?>

<form method='POST' action='<?PHP echo "?module=$module&act=input&idmenu=$idmenu"; ?>' id='d-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left'>
    <input type='hidden' id='u_cabangid' name='u_cabangid' value='<?PHP echo $pidcabang; ?>' Readonly>
    <input type='hidden' id='u_tgl1' name='u_tgl1' value='<?PHP echo $ptgl; ?>' Readonly>
    
    <div class='x_content'>
        
        <table id='datatable' class='datatable table nowrap table-striped table-bordered' width="100%">
            <thead>
                <tr>
                    <th width='10px'>
                        <input type="checkbox" id="chkbtnbr" value="select" onClick="SelAllCheckBox('chkbtnbr', 'chkbox_br[]')" />
                    </th>
                    <th width='10px'>No</th>
                    <th width='10px'></th>
                    <th width='100px' align="center">ID SPG</th>
                    <th width='300px' align="center">Nama SPG</th>
                    <th width='90px' align="center">Jabatan</th>
                    <th width='120px' align="center" nowrap>Area</th>
                    <th width='90px' align="center" nowrap>Alokasi</th>
                    <th width='50px' align="center" nowrap>Zona</th>
                    <th width='50px' align="center" nowrap>Penempatan</th>
                    <th width='90px' align="center" nowrap>Tgl. Masuk</th>
                    <th width='90px' align="center" nowrap>Tgl. Keluar<br/>bln/tgl/thn</th>
                </tr>
            </thead>
            <tbody>
                <?PHP
                $query = "select distinct icabangid, nama_cabang from $tmp02 order by nama_cabang";
                $tampil1= mysqli_query($cnmy, $query);
                while ($row1= mysqli_fetch_array($tampil1)) {
                    $nidcabang=$row1['icabangid'];
                    $nnmcabang=$row1['nama_cabang'];
                    
                    if (empty($pidcabang)) {
                        echo "<tr>";
                        echo "<td nworap></td>";
                        echo "<td nworap></td>";
                        echo "<td nworap colspan='10'><b>$nnmcabang</b></td>";
                        echo "<td nworap class='divnone'></td>";
                        echo "<td nworap class='divnone'></td>";
                        echo "<td nworap class='divnone'></td>";
                        echo "<td nworap class='divnone'></td>";
                        echo "<td nworap class='divnone'></td>";
                        echo "<td nworap class='divnone'></td>";
                        echo "<td nworap class='divnone'></td>";
                        echo "<td nworap class='divnone'></td>";
                        echo "</tr>";
                    }
                    
                    $no=1;
                    $query = "select * from $tmp02 WHERE icabangid='$nidcabang' order by nama";
                    $tampil2= mysqli_query($cnmy, $query);
                    while ($row2= mysqli_fetch_array($tampil2)) {
                        $pidspg=$row2['id_spg'];
                        $pnmspg=$row2['nama'];
                        $pnmjabatan=$row2['nama_jabatan'];
                        $pnmarea=$row2['nama_area'];
                        $pidalokasi=$row2['alokid'];
                        $pidzona=$row2['id_zona'];
                        $ppenempatan=$row2['penempatan'];
                        $ptglmasuk=$row2['tglmasuk'];
                        $ptglkeluar=$row2['tglkeluar'];
                        $pststransaksi=$row2['stransaksi'];
                        
                        $pblnbpjs=$row2['bulan_bpjs'];
                        
                        $cekbox = "<input type=checkbox value='$pidspg' id='chkbox_br[]' name='chkbox_br[]' onclick=\"\">";
                        
                        $txt_tglkeluar="<input type='date' id='dtp_keluaar[$pidspg]' name='dtp_keluaar[$pidspg]' value='$ptglkeluar'>";
                        
                        if ($ppilihdataproses=="2") {
                            if ($ptglkeluar=="0000-00-00") $ptglkeluar="";
                            if (!empty($ptglkeluar)) $ptglkeluar = date('m/d/Y', strtotime($ptglkeluar));
                            $txt_tglkeluar=$ptglkeluar;
                        }
                        
                        if ($ptglmasuk=="0000-00-00") $ptglmasuk="";
                        if ($ptglkeluar=="0000-00-00") $ptglkeluar="";
                        
                        if (!empty($ptglmasuk)) $ptglmasuk = date('d/m/Y', strtotime($ptglmasuk));
                        if (!empty($ptglkeluar)) $ptglkeluar = date('d/m/Y', strtotime($ptglkeluar));
                        
                        if ($pidzona=="0") $pidzona="";
                        
                        if ($pststransaksi=="Y") {
                            $cekbox="";
                        }
                        
                        $pbtnbpjs="btn btn-warning btn-xs";
                        $pcolor="";
                        if (!empty($pblnbpjs)) {
                            //$pcolor=" style='color:blue;' ";
                            $pbtnbpjs="btn btn-dark btn-xs";
                        }
                        
                        $pnbpjs_kerja = "<button type='button' class='$pbtnbpjs' data-toggle='modal' data-target='#myModal' onClick=\"TambahDataBPJSKerja('$pidspg')\">BPJS Tenagakerja</button>";
                        
                        
                        echo "<tr $pcolor>";
                        echo "<td nworap>$cekbox</td>";
                        echo "<td nworap>$no</td>";
                        echo "<td nworap>$pnbpjs_kerja</td>";
                        echo "<td nworap>$pidspg</td>";
                        echo "<td nworap>$pnmspg</td>";
                        echo "<td nworap>$pnmjabatan</td>";
                        echo "<td nworap>$pnmarea</td>";
                        echo "<td nworap>$pidalokasi</td>";
                        echo "<td nworap>$pidzona</td>";
                        echo "<td>$ppenempatan</td>";
                        echo "<td nworap>$ptglmasuk</td>";
                        echo "<td nworap>$txt_tglkeluar</td>";//$ptglkeluar
                        echo "</tr>";
                        
                        $no++;
                    }
                    
                    echo "<tr>";
                    echo "<td nworap colspan='12'></td>";
                    echo "<td nworap class='divnone'></td>";
                    echo "<td nworap class='divnone'></td>";
                    echo "<td nworap class='divnone'></td>";
                    echo "<td nworap class='divnone'></td>";
                    echo "<td nworap class='divnone'></td>";
                    echo "<td nworap class='divnone'></td>";
                    echo "<td nworap class='divnone'></td>";
                    echo "<td nworap class='divnone'></td>";
                    echo "<td nworap class='divnone'></td>";
                    echo "<td nworap class='divnone'></td>";
                    echo "<td nworap class='divnone'></td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
            
        </table>
        
        
    </div>

    
    <div class='col-md-12 col-sm-12 col-xs-12'>

        <div id="div_jumlah">
            <div class='x_panel'>
                <div class='x_content'>
                    <div class='col-md-12 col-sm-12 col-xs-12'>


                        
                        <div class='form-group'>
                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''> <span class='required'></span></label>
                            <div class='col-xs-9'>
                                <div class="checkbox">
                                    <?PHP
                                    if ($ppilihdataproses=="1") {
                                        echo "<button type='button' class='btn btn-success' onclick=\"disp_confirm('Simpan...?', 'input')\">Simpan</button>";
                                    }else{
                                        if ($_SESSION['GROUP']=="37"){
                                            echo "<button type='button' class='btn btn-danger' onclick=\"disp_confirm('Hapus...?', 'hapus')\">Hapus</button>";
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                        
                        
                    </div>
                </div>
            </div>
            
        </div>

    </div>
    
    
</form>


<?php
hapusdata:
    
    mysqli_query($cnmy, "drop temporary table $tmp01");
    mysqli_query($cnmy, "drop temporary table $tmp02");
    mysqli_query($cnmy, "drop temporary table $tmp03");
    mysqli_query($cnmy, "drop temporary table $tmp04");
    mysqli_close($cnmy);
?>


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
            "scrollY": 440,
            "scrollX": true
        } );
    });
    
    
    function SelAllCheckBox(nmbuton, data){
        var checkboxes = document.getElementsByName(data);
        var button = document.getElementById(nmbuton);

        if(button.value == 'select'){
            for (var i in checkboxes){
                checkboxes[i].checked = 'FALSE';
            }
            button.value = 'deselect'
        }else{
            for (var i in checkboxes){
                checkboxes[i].checked = '';
            }
            button.value = 'select';
        }
    }
    
    function TambahDataBPJSKerja(eidspg){
        $.ajax({
            type:"post",
            url:"module/md_m_spg_importdata/tambah_bpjskerja.php?module=viewisibpjskerja",
            data:"uidspg="+eidspg,
            success:function(data){
                $("#myModal").html(data);
            }
        });
    }
                    
                    
    function disp_confirm(pText_,ket)  {
        ok_ = 1;
        if (ok_) {
            var r=confirm(pText_)
            if (r==true) {
                
                
                var iketalasan="";
                if (ket=="reject") {
                    var textket = prompt("Masukan alasan "+ket+" : ", "");
                    if (textket == null || textket == "") {
                        iketalasan = textket;
                    } else {
                        iketalasan = textket;
                    }
                }
                    
                var myurl = window.location;
                var urlku = new URL(myurl);
                var module = urlku.searchParams.get("module");
                var idmenu = urlku.searchParams.get("idmenu");
                //document.write("You pressed OK!")
                document.getElementById("d-form2").action = "module/md_m_spg_importdata/aksi_importdataspg.php?module="+module+"&act="+ket+"&idmenu="+idmenu+"&ukethapus="+iketalasan;
                document.getElementById("d-form2").submit();
                return 1;
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }
    }
</script>
