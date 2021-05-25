<?php
    session_start();
    $_SESSION['BNKDANATIPE']="viewdatatransferul";
    
    $puserid="";
    if (isset($_SESSION['USERID'])) $puserid=$_SESSION['USERID'];
    if (empty($puserid)) {
        echo "Maaf harus login ulang...";
        exit;
    }
    
    $now=date("mdYhis");
    $tmp01 =" dbtemp.tmpbtulang01_".$puserid."_$now ";
    $tmp02 =" dbtemp.tmpbtulang02_".$puserid."_$now ";
    
    $_SESSION['BNKDANAKARY']=$_POST['ukryid'];
    $_SESSION['BNKDANATGL01']=$_POST['uperiode'];
    
    include("../../../config/koneksimysqli.php");
    $pkaryawanid=$_POST['ukryid'];
    $date1=$_POST['uperiode'];
    $periode1= date("Ym", strtotime($date1));
    
    $filter_d="";
    
    if ($_SESSION['GROUP']=="1" OR $_SESSION['GROUP']=="25") {
    }else{
        if ($_SESSION['DIVISI']=="OTC") $filter_d=" AND divisi='OTC' ";
        else $filter_d=" AND divisi<>'OTC' ";
    }
    
    $query = "select idinput, kodeid, subkode, userid as karyawanid, divisi, DATE_FORMAT(tanggal,'%d %M %Y') as tanggal, idinputbank, parentidbank, nodivisi, brid, noslip, realisasi, "
            . " customer, aktivitas1, keterangan, jumlah, userid, CAST('' as CHAR(1)) as stssudah, "
            . " CAST('' as CHAR(1)) as jenis_rpt, CAST(NULL as DECIMAL(20)) as idinput2 "
            . " from dbmaster.t_suratdana_bank where IFNULL(idinput,'')<>'' AND IFNULL(brid,'')<>'' AND stsnonaktif<>'Y' and stsinput='T' AND "
            . " DATE_FORMAT(tanggal, '%Y%m')='$periode1' $filter_d AND IFNULL(userid,'')='$pkaryawanid' ";
    $query = "create TEMPORARY table $tmp01 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "UPDATE $tmp01 a JOIN dbmaster.t_suratdana_bank b on a.parentidbank=b.idinputbank SET a.idinput2=b.idinput"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp01 a JOIN dbmaster.t_suratdana_br b on a.idinput2=b.idinput SET a.jenis_rpt=b.jenis_rpt"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "SELECT nodivisi, idinput FROM dbmaster.t_suratdana_br WHERE stsnonaktif<>'Y' AND pilih='N' AND jenis_rpt='W' AND "
            . " nodivisi IN "
            . " (select distinct IFNULL(nodivisi,'') FROM $tmp01)";
    $query = "create TEMPORARY table $tmp02 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp01 a JOIN $tmp02 b on a.nodivisi=b.nodivisi SET a.stssudah='Y'"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $pjmlusul=0;
    
?>

<script src="js/inputmask.js"></script>
    
    
<!-- Modal -->
<div class='modal fade' id='myModal' role='dialog'></div>
<form method='POST' action='<?PHP echo "?module='brdanabank'&act=input&idmenu=258"; ?>' id='d-form7' name='form7' data-parsley-validate class='form-horizontal form-label-left'>
    
    <table id='datatable' class='datatable table nowrap table-striped table-bordered' width='100%'>
        <thead>
            <tr style='background-color:#cccccc; font-size: 13px;'>
            <th align="center">No</th>
            <th align="center">
                <input type="checkbox" id="chkpilihall[]" name="chkpilihall[]" onclick="SelAllCheckBox('chkpilihall[]', 'chk_jmltu[]')" value='select'>
            </th>
            <th align="center">ID</th>
            <th align="center">Tanggal</th>
            <th align="center">BR ID</th>
            <th align="center">No BR/Divisi</th>
            <th align="center">Jumlah</th>
            <th align="center">Noslip</th>
            <th align="center">Realisasi</th>
            <th align="center">Dokter/Customer</th>
            <th align="center">Aktivitas</th>
            </tr>
        </thead>
        <tbody>
        <?PHP
            $no=1;
            $query = "select * from $tmp01 order by nodivisi, tanggal, idinputbank, customer";
            $tampil= mysqli_query($cnmy, $query);
            while ($row= mysqli_fetch_array($tampil)) {
                $pid=$row['idinputbank'];
                $ptanggal=$row['tanggal'];
                $pnodivisi=$row['nodivisi'];
                $pbrid=$row['brid'];
                $pnoslip=$row['noslip'];
                $prealisasi=$row['realisasi'];
                $pcustomer=$row['customer'];
                $paktivitas=$row['aktivitas1'];
                $psudahinput=$row['stssudah'];
                $pjumlah=$row['jumlah'];
                
                
                $idno=$row['idinput'];
                $pkaryawanid=$row['karyawanid'];
                $pkode = $row["kodeid"];
                $psubkode = $row["subkode"];
                $pdivisi = $row["divisi"];
                $pjenisrpt = $row["jenis_rpt"];
                    
                $pmystsyginput="";
                if ($pkaryawanid=="0000000566") {
                    $pmystsyginput=1;
                }elseif ($pkaryawanid=="0000001043") {
                    $pmystsyginput=2;
                }else{
                    if ( ($pkode=="1" AND $psubkode=="01") OR ($pkode=="2" AND $psubkode=="20") ) {//anne
                        $pmystsyginput=5;
                    }else{
                        if ($pkode=="1" AND $psubkode=="03") {//ria
                            $pmystsyginput=3;
                        }elseif ($pkode=="2" AND $psubkode=="21") {//marsis
                            $pmystsyginput=4;
                        }elseif ( ($pkode=="2" AND $psubkode=="22") OR ($pkode=="2" AND $psubkode=="23") ) {//marsis
                            $pmystsyginput=6;
                        }
                    }
                }
                
                $pmymodule="";
                $print=$pid;
                if ($pdivisi=="OTC") {
                    if ( ($pkode=="1" AND $psubkode=="03") ) {
                        $pmymodule="module=rekapbiayarutinotc&act=input&idmenu=171&ket=bukan&ispd=$idno";
                    }elseif ( ($pkode=="2" AND $psubkode=="21") ) {
                        $pmymodule="module=rekapbiayaluarotc&act=input&idmenu=245&ket=bukan&ispd=$idno";
                    }elseif ( ($pkode=="2" AND $psubkode=="36") ) {
                        $pmymodule="module=rekapbiayarutincaotc&act=input&idmenu=245&ket=bukan&ispd=$idno";
                    }else{
                        $pmymodule="module=lapbrotcpermo&act=input&idmenu=134&ket=bukan&ispd=$idno";
                    }
                }else{
                    if ($pmystsyginput==1) {
                        $pmymodule="module=saldosuratdana&act=rekapbr&idmenu=192&ket=bukan&ispd=$idno&iid=$pmystsyginput";
                    }elseif ($pmystsyginput==2) {
                        if ($pjenisrpt=="D") {
                            $pmymodule="module=saldosuratdana&act=viewbrklaim&idmenu=192&ket=bukan&ispd=$idno&iid=$pmystsyginput";
                        }else{
                            $pmymodule="module=saldosuratdana&act=viewbr&idmenu=192&ket=bukan&ispd=$idno&iid=$pmystsyginput";
                        }
                    }elseif ($pmystsyginput==3) {
                        $pmymodule="module=rekapbiayarutin&act=input&idmenu=190&ket=bukan&ispd=$idno&iid=$pmystsyginput";
                    }elseif ($pmystsyginput==4) {
                        $pmymodule="module=rekapbiayaluar&act=input&idmenu=187&ket=bukan&ispd=$idno&iid=$pmystsyginput";
                    }elseif ($pmystsyginput==5) {
                        $pmymodule="module=saldosuratdana&act=rekapbr&idmenu=204&ket=bukan&ispd=$idno&iid=$pmystsyginput";
                    }elseif ($pmystsyginput==6) {
                        $pmymodule="module=spdkas&act=viewbrho&idmenu=205&ket=bukan&ispd=$idno&bln=$tglbuat";
                    }
                }

                if (!empty($pmymodule)) {

                    $print="<a style='font-size:11px;' title='Print / Cetak' href='#' class='btn btn-info btn-xs' data-toggle='modal' "
                        . "onClick=\"window.open('eksekusi3.php?$pmymodule',"
                        . "'Ratting','width=800,height=500,left=400,top=100,scrollbars=yes,toolbar=yes,status=1,pagescrool=yes')\"> "
                        . "$pid</a>";

                }
                
                
                
                
                $pjumlah=number_format($pjumlah,0,",",",");
                
                $nval_chk = "";
                
                $nnjumlahpilih=$pjumlah;
                
                $chkbox = "<input type='checkbox' id='chk_jmltu[$pid]' name='chk_jmltu[]' value='$pid' onclick=\"HitungTotalDariCekBox()\" $nval_chk>";
                $pinput_jumlah="<input type='hidden' id='txt_jml[$pid]' name='txt_jml[$pid]' value='$nnjumlahpilih' Readonly>";
                
                
                $phapus="<input type='button' value='Hapus' class='btn btn-danger btn-xs' onClick=\"ProsesDataHapus('hapus', '$pnodivisi')\">";
                
                if ($psudahinput=="Y") {
                    $chkbox= "";
                }else{
                    $phapus="";
                    $print=$pid;
                }
                
                echo "<tr>";
                echo "<td nowrap>$no</td>";
                echo "<td nowrap>$chkbox $pinput_jumlah</td>";
                echo "<td nowrap>$print $phapus</td>";
                echo "<td nowrap>$ptanggal</td>";
                echo "<td nowrap>$pbrid</td>";
                echo "<td nowrap>$pnodivisi</td>";
                echo "<td nowrap align='right'>$pjumlah</td>";
                echo "<td nowrap>$pnoslip</td>";
                echo "<td nowrap>$prealisasi</td>";
                echo "<td nowrap>$pcustomer</td>";
                echo "<td nowrap>$paktivitas</td>";
                echo "</tr>";
                
                $no++;
            }
        ?>
        </tbody>
    </table>
    
    <div class='col-md-12 col-sm-12 col-xs-12'>

        <div id="div_jumlah">
            <div class='x_panel'>
                <div class='x_content'>
                    <div class='col-md-12 col-sm-12 col-xs-12'>

                        <div hidden class='form-group'>
                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Periode <span class='required'></span></label>
                            <div class='col-xs-3'>
                                <input type='text' id='e_periode_save' name='e_periode_save' class='form-control col-md-7 col-xs-12' value='<?PHP echo $date1; ?>' Readonly>
                            </div>
                        </div>
                        
                        
                        <div class='form-group'>
                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Jumlah <span class='required'></span></label>
                            <div class='col-xs-3'>
                                <input type='text' id='e_jmlusulan' name='e_jmlusulan' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjmlusul; ?>' Readonly>
                            </div>
                        </div>

                        
                        <div class='form-group'>
                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''> <span class='required'></span></label>
                            <div class='col-xs-9'>
                                <div class="checkbox">
                                    <button type='button' class='btn btn-success' onclick='disp_confirm_pros_tu("Simpan ?", "<?PHP echo "simpan"; ?>")'>Simpan</button>
                                </div>
                            </div>
                        </div>
                        
                        
                    </div>
                </div>
            </div>
            
        </div>

    </div>
    
    
</form>


<script>

    $(document).ready(function() {
        var dataTable = $('#datatable').DataTable( {
            "ordering": false,
            bFilter: false, bInfo: false, "bLengthChange": false, "bLengthChange": false,
            "bPaginate": false,
            "scrollY": 240,
            "scrollX": true /*,
            fixedColumns:   {
                leftColumns: 1
            }*/
        } );
    });
    
    function ProsesDataHapus(pText_, nid)  {
        pText_= "Yakin akan hapus data...?";
        ok_ = 1;
        if (ok_) {
            var r=confirm(pText_)
            if (r==true) {
                var myurl = window.location;
                var urlku = new URL(myurl);
                var module = urlku.searchParams.get("module");
                var idmenu = urlku.searchParams.get("idmenu");
                //document.write("You pressed OK!")
                document.getElementById("d-form7").action = "module/budget/bgt_danabank/simpanlap_tu.php?module="+module+"&act=hapus"+"&idmenu="+idmenu+"&id="+nid;
                document.getElementById("d-form7").submit();
                return 1;
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }
    }
    
    
    function disp_confirm_pros_tu(pText_, eact)  {
        
        var ejmlusul =document.getElementById('e_jmlusulan').value;
        var etgl =document.getElementById('e_periode_save').value;

        if (etgl=="") {
            alert("Tidak ada data yang dipilih...");
            return false;    
        }

        if (parseInt(ejmlusul)=="0") {
            alert("jumlah masih kosong");
            return false;    
        }
        
        
        ok_ = 1;
        if (ok_) {
            var r=confirm(pText_)
            if (r==true) {
                var myurl = window.location;
                var urlku = new URL(myurl);
                var module = urlku.searchParams.get("module");
                var idmenu = urlku.searchParams.get("idmenu");
                //document.write("You pressed OK!")
                document.getElementById("d-form7").action = "module/budget/bgt_danabank/simpanlap_tu.php?module="+module+"&act="+eact+"&idmenu="+idmenu;
                document.getElementById("d-form7").submit();
                return 1;
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }
    }
    
    
</script>


<script type="text/javascript">
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
        HitungTotalDariCekBox();
    }
        
    function HitungTotalDariCekBox() {
        var chk_arr1 =  document.getElementsByName('chk_jmltu[]');
        var chklength1 = chk_arr1.length;
        var newchar = '';
        
        var nTotal_="0";
        for(k=0;k< chklength1;k++)
        {
            if (chk_arr1[k].checked == true) {
                var kata = chk_arr1[k].value;
                var fields = kata.split('-');    
                var anm_jml="txt_jml["+fields[0]+"]";
                var ajml=document.getElementById(anm_jml).value;
                if (ajml=="") ajml="0";
                ajml = ajml.split(',').join(newchar);
                
                nTotal_ =parseInt(nTotal_)+parseInt(ajml);
            }
        }
        
        document.getElementById('e_jmlusulan').value=nTotal_;
    }
</script>


<style>
    .divnone {
        display: none;
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
        font-size:12px;
    }
    #datatable input[type=text], #tabelnobr input[type=text] {
        box-sizing: border-box;
        color:#000;
        font-size:10px;
        height: 25px;
    }
    select.soflow {
        font-size:11px;
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

<?PHP
hapusdata:
    mysqli_query($cnmy, "drop temporary table $tmp01");
    mysqli_query($cnmy, "drop temporary table $tmp02");
    
    mysqli_close($cnmy);
?>