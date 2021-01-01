<?php
    ini_set("memory_limit","5000M");
    ini_set('max_execution_time', 0);
?>
    <link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />
    <link href="css/stylenew.css" rel="stylesheet" type="text/css" />
    <script src="js/inputmask.js"></script>
    
<?php
    ini_set("memory_limit","5000M");
    ini_set('max_execution_time', 0);
    
    session_start();
    include "../../config/koneksimysqli.php";
    include "../../config/fungsi_sql.php";
    include "../../config/library.php";
    $cnit=$cnmy;
    
    $pidgroup=$_POST['uidinput'];//di simpan di idinput
    $didkrymaster=$_POST['ukrymaster'];
    
    $date1=$_POST['utgl'];
    $tgl1= date("Y-m-01", strtotime($date1));
    $bulan= date("Ym", strtotime($date1));
    
    $_SESSION['CLSLKCA']=date("F Y", strtotime($date1));
    
    
    $tglnow = date("Y-m-d");
    $tgl01 = $_POST['utgl'];
    $periode1 = date("Y-m", strtotime($tgl01));
    $per1 = date("F Y", strtotime($tgl01));
    
    $stsreport = "";
    
    $now=date("mdYhis");
    $tmp01 =" dbtemp.DTBRRETRLCLS01_".$_SESSION['IDCARD']."_$now ";
    $tmp02 =" dbtemp.DTBRRETRLCLS02_".$_SESSION['IDCARD']."_$now ";
    $tmp03 =" dbtemp.DTBRRETRLCLS03_".$_SESSION['IDCARD']."_$now ";
    $tmp04 =" dbtemp.DTBRRETRLCLS04_".$_SESSION['IDCARD']."_$now ";
    
    $query = "select DISTINCT a.bulan, a.karyawanid, b.nama, a.divisi, a.saldo, a.ca1, IFNULL(a.ca1,0)-IFNULL(a.saldo,0) as selisih 
        from dbmaster.t_brrutin_ca_close a JOIN hrd.karyawan b on a.karyawanid=b.karyawanId 
        WHERE DATE_FORMAT(a.bulan,'%Y-%m')='$periode1' AND IFNULL(IFNULL(a.ca1,0)-IFNULL(a.saldo,0),0) > 0
        ORDER BY b.nama";
    $query = "create TEMPORARY table $tmp01 ($query)"; 
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    //cari pengembalian hanya status ots=1
    $query = "select bulan, karyawanid, tgl_kembali, keterangan, igroup, ikaryawanid, inama_karyawan, sum(kembali_rp) kembali_rp from dbmaster.t_brrutin_outstanding WHERE 
        DATE_FORMAT(bulan,'%Y-%m')='$periode1' AND IFNULL(ots_status,'')='1' AND divisi <> 'OTC' group by 1,2,3,4,5,6,7";
    $query = "create TEMPORARY table $tmp02 ($query)";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    //cari kelebihan status ots <> 1
    $query = "select ots_status, karyawanid, igroup, ikaryawanid, inama_karyawan, sum(kembali_rp) kembali_rp from dbmaster.t_brrutin_outstanding WHERE 
        DATE_FORMAT(bulan,'%Y-%m')='$periode1' AND IFNULL(ots_status,'')<>'1' AND divisi <> 'OTC' group by 1,2,3,4,5";
    $query = "create TEMPORARY table $tmp04 ($query)";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    if (empty($pidgroup)) {
        $query = "DELETE FROM $tmp01 WHERE karyawanid IN (select distinct karyawanid from $tmp02)";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

        $query = "DELETE FROM $tmp01 WHERE karyawanid IN (select distinct karyawanid from $tmp04)";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    }
    
    
    $query = "SELECT a.*, c.ots_status, b.tgl_kembali, b.keterangan, b.kembali_rp, CAST(NULL as CHAR(10)) as igroup, "
            . " CAST(NULL as CHAR(10)) as ikaryawanid, CAST(NULL as CHAR(10)) as nama_transfer, CAST(NULL as CHAR(10)) as inama_karyawan from $tmp01 a LEFT JOIN $tmp02 b on a.karyawanid=b.karyawanid "
            . " LEFT JOIN $tmp04 c on a.karyawanid=c.karyawanid";
    $query = "create TEMPORARY table $tmp03 ($query)";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    if (!empty($pidgroup)) {
        $query = "UPDATE $tmp03 a JOIN $tmp02 b on a.karyawanid=b.karyawanid SET a.igroup=b.igroup, a.ikaryawanid=b.ikaryawanid, a.inama_karyawan=b.inama_karyawan";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }

        $query = "UPDATE $tmp03 a JOIN $tmp04 b on a.karyawanid=b.karyawanid SET a.igroup=b.igroup, a.ikaryawanid=b.ikaryawanid, a.inama_karyawan=b.inama_karyawan";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
    
        $query = "DELETE FROM $tmp03 WHERE IFNULL(kembali_rp,0)<>0 AND IFNULL(igroup,'') <>'$pidgroup' AND IFNULL(ikaryawanid,'')<>'$didkrymaster'";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    }
    
?>

<div class='x_content'>

    <input type="hidden" class="form-control" id='e_pilperiodeots' name='e_pilperiodeots' autocomplete='off' 
           required='required' value='<?PHP echo $date1; ?>'>
    
    <table id='datatablepostdeth' class='table table-striped table-bordered' width="100%">
        <thead>
            <tr>
                <th width='4px'>No</th>
                <th width='200px'>Karyawan</th>
                <th width='100px'>LK</th>
                <th width='100px'>CA</th>
                <th width='100px'>Selisih</th>
                <th width='100px'>Jumlah Kembali</th>
                <th width='100px'>Keterangan</th>
            </tr>
        </thead>
        <tbody>
        <?PHP
            $no=1;
            $query = "select * from $tmp03 order by nama, karyawanid";
            $tampil = mysqli_query($cnmy, $query);
            while( $row=mysqli_fetch_array($tampil) ) {
                $pidkry=$row['karyawanid'];
                $pnmkry=$row['nama'];
                $pdivisi=$row['divisi'];
                $psaldo=$row['saldo'];
                $pca=$row['ca1'];
                $pselisih=$row['selisih'];
                
                $ptglkembali=$row['tgl_kembali'];
                if ($ptglkembali=="0000-00-00") $ptglkembali="";
                $pketkembali=$row['keterangan'];
                $potsstatus=$row['ots_status'];
                $pkembalirp=$row['kembali_rp'];
                
                $in_chk_val="";
                $cn_isi_kembali="";
                $cn_div_hidden="";
                if (!empty($row['kembali_rp']) AND !empty($ptglkembali)) {
                    //$cn_div_hidden="hidden";
                    //$cn_isi_kembali=number_format($pkembalirp,0,",",",");
                    $in_chk_val="checked";
                }
                
                $finchksama="<span $cn_div_hidden><input type='checkbox' id='txtchksama[$pidkry]' name='txtchksama[]' onClick=\"SamakanSelisih('txtchksama[$pidkry]', 'txtjmlselisih[$pidkry]', 'txtjmlkembali[$pidkry]')\" value='$pidkry' $in_chk_val></span>";
                
                $finnmkry="<input type='hidden' id='txtnmkry[$pidkry]' name='txtnmkry[$pidkry]' class='input-sm' value='$pnmkry'>";
                $finidkry="<input type='hidden' id='txtidkry[$pidkry]' name='txtidkry[$pidkry]' class='input-sm' value='$pidkry'>";
                $finiddivisi="<input type='hidden' id='txtiddiv[$pidkry]' name='txtiddiv[$pidkry]' class='input-sm' value='$pdivisi'>";
                $finjmlselisih="<input type='hidden' size='10px' id='txtjmlselisih[$pidkry]' name='txtjmlselisih[$pidkry]' class='input-sm inputmaskrp2' autocomplete='off' value='$pselisih'>";
                $finjmlca="<input type='hidden' size='10px' id='txtjmlca[$pidkry]' name='txtjmlca[$pidkry]' class='input-sm inputmaskrp2' autocomplete='off' value='$pca'>";
                $finjmlsaldo="<input type='hidden' size='10px' id='txtjmlsaldo[$pidkry]' name='txtjmlsaldo[$pidkry]' class='input-sm inputmaskrp2' autocomplete='off' value='$psaldo'>";
                
                
                $finjmlkembali="<span $cn_div_hidden><input type='text'  onblur=\"HitungJumlahTotalCexBox()\" size='10px' id='txtjmlkembali[$pidkry]' name='txtjmlkembali[$pidkry]' class='input-sm inputmaskrp2' autocomplete='off' value='$pkembalirp'></span>";
                $sel3="selected";
                $sel4="";
                if ($potsstatus=="4"){
                    $sel3="";
                    $sel4="selected";    
                }
                $finsts="<div><select id='cbsts[$pidkry]' name='cbsts[$pidkry]' class='input-sm' >"
                        . "<option value='3' $sel3>Pembulatan</option>"
                        . "<option value='4' $sel4>Hutang Piutang</option>"
                        . "</select></div>";
                        
                
                $psaldo=number_format($psaldo,0,",",",");
                $pca=number_format($pca,0,",",",");
                $pselisih=number_format($pselisih,0,",",",");
                
                
                echo "<tr>";
                echo "<td>$no $finidkry $finnmkry $finiddivisi $finjmlsaldo $finjmlca $finjmlselisih</td>";
                echo "<td nowrap>$pnmkry</td>";
                echo "<td align='right'>$psaldo</td>";
                echo "<td align='right'>$pca</td>";
                echo "<td align='right'>$pselisih</td>";
                echo "<td align='right'><table><tr><td>$finchksama</td><td>$finjmlkembali $cn_isi_kembali</td></tr></table></td>";
                echo "<td><table><tr><td>$finsts</td><td>&nbsp;</td></tr></table></td>";
                echo "</tr>";
                
                $no++;
            }
        ?>
        </tbody>
    </table>
    
</div>
  
    
<script type="text/javascript">
    $(document).ready(function() {
        var table = $('#datatablepostdeth').DataTable({
            fixedHeader: true,
            "ordering": true,
            "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
            "displayLength": -1,
            "order": [[ 0, "asc" ]],
            bFilter: false, bInfo: false, "bLengthChange": true, "bLengthChange": true,
            "bPaginate": true,
            "scrollY": 440
        } );

    } );

    function SamakanSelisih(nmbuton, eselisih, utextkembali){
        var jmlselisih =document.getElementById(eselisih).value;
        var nn_chk=document.getElementById(nmbuton).checked;
        if (nn_chk==true) {
            document.getElementById(utextkembali).value=jmlselisih;
        }else{
            document.getElementById(utextkembali).value=0;
        }
        HitungJumlahTotalCexBox();
    }
    
    function hilangkanTanda(sText){
        var newchar = '';
        var aText=document.getElementById(sText).value;
        aText = aText.split(',').join(newchar);
        
        if (aText=="") { aText="0"; }
        
        return aText;
    }
    
    function HitungJumlahTotalCexBox() {
        
        var chk_arr1 =  document.getElementsByName('txtchksama[]');
        var chklength1 = chk_arr1.length;

        var apilih_text="";
        var ajml_rpnya="";
        
        var nRp_kembali="0";
        
        
        for(k=0;k< chklength1;k++)
        {
            if (chk_arr1[k].checked == true) {
                var kata = chk_arr1[k].value;
                var fields = kata.split('-');
                
                apilih_text="txtjmlkembali["+fields[0]+"]";
                ajml_rpnya=hilangkanTanda(apilih_text);
                nRp_kembali =parseInt(nRp_kembali)+parseInt(ajml_rpnya);
                
            }
        }
        document.getElementById('e_jmlusulan').value=nRp_kembali;
        document.getElementById('e_jmlusulan2').value=nRp_kembali;

    }
</script>
    
    
    
    <style>
        .divnone {
            display: none;
        }
        #datatablepostdeth th {
            font-size: 12px;
        }
        #datatablepostdeth td { 
            font-size: 11px;
        }
        .imgzoom:hover {
            -ms-transform: scale(3.5); /* IE 9 */
            -webkit-transform: scale(3.5); /* Safari 3-8 */
            transform: scale(3.5);

        }


        .form-group, .input-group, .control-label {
            margin-bottom:2px;
        }
        .control-label {
            font-size:11px;
        }
        #datatable input[type=text] {
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

        table.datatable {
            color: #000;
            font-family: Helvetica, Arial, sans-serif;
            width: 100%;
            border-collapse:
            collapse; border-spacing: 0;
            font-size: 11px;
            border: 0px solid #000;
        }

        table.datatable td {
            border: 1px solid #000; /* No more visible border */
            height: 10px;
            transition: all 0.1s;  /* Simple transition for hover effect */
        }

        table.datatable th {
            background: #DFDFDF;  /* Darken header a bit */
            font-weight: bold;
        }

        table.datatable td {
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
    
    
    
<?PHP
hapusdata:
    mysqli_query($cnmy, "drop TEMPORARY table $tmp01");
    mysqli_query($cnmy, "drop TEMPORARY table $tmp02");
    mysqli_query($cnmy, "drop TEMPORARY table $tmp03");
    mysqli_query($cnmy, "drop TEMPORARY table $tmp04");
?>
