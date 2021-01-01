<?php
    date_default_timezone_set('Asia/Jakarta'); 
    session_start(); 
    
    //ini_set('display_errors', '0');
    ini_set("memory_limit","10G");
    ini_set('max_execution_time', 0);

    $puserid="";
    if (isset($_SESSION['USERID'])) $puserid=$_SESSION['USERID'];

    if (empty($puserid)) {
        echo "ANDA HARUS LOGIN ULANG...";
        exit;
    }

    $hari_ini = date("Y-m-d");
    $tgl_pertama = date('d/mm/Y', strtotime($hari_ini));
    
    
    $pmodule=$_GET['module'];
    $pact=$_GET['act'];
    $pidmenu=$_GET['idmenu'];
    
    $_SESSION['FINDDTIPE']=$_POST['utipeproses'];
    $_SESSION['FINDDTGLTIPE']=$_POST['utgltipe'];
    $_SESSION['FINDDPERENTY1']=$_POST['uperiode1'];
    $_SESSION['FINDDPERENTY2']=$_POST['uperiode2'];
    $_SESSION['FINDDDIV']=$_POST['udivisi'];
    $_SESSION['FINUSPL']=$_POST['uidkarpilih'];
    
    $psescardidid=$_SESSION['IDCARD'];
    $pgroupid=$_SESSION['GROUP'];
    
    $ptgltipe=$_POST['utgltipe'];
    $date1=$_POST['uperiode1'];
    $date2=$_POST['uperiode2'];
    $tgl1= date("Y-m-d", strtotime($date1));
    $tgl2= date("Y-m-d", strtotime($date2));
    $pdivisi=$_POST['udivisi'];
    $uidcard=$_POST['uidc'];
    $upilidcrd=$_POST['uidkarpilih'];
    $pnuseriid=(INT)$upilidcrd;
    
    if (empty($upilidcrd)) $upilidcrd=$psescardidid;
    if (empty($pnuseriid)) $pnuseriid=$_SESSION['USERID'];
    
    
    include "../../config/koneksimysqli.php";
    
    //untuk yang dss dcc
    $filternondssdccCOA=" and (bk.br <> '' and bk.br<>'N') ";
    $filternondssdcc=" AND ( (br <> '' and br<>'N') OR user1=$pnuseriid ) ";
    
    $sql = "SELECT w.id, w.karyawanId, k.nama, w.COA4, c4.NAMA4,
	bk.br, bk.divprodid FROM dbmaster.coa_wewenang AS w
	LEFT JOIN hrd.karyawan AS k ON w.karyawanId = k.karyawanId
	LEFT JOIN dbmaster.coa_level4 AS c4 ON w.COA4 = c4.COA4
	LEFT JOIN dbmaster.br_kode AS bk ON c4.kodeid = bk.kodeid WHERE 
        w.karyawanId='$upilidcrd' $filternondssdccCOA";

    $tampil=mysqli_query($cnmy, $sql);
    $ketemu=mysqli_num_rows($tampil);
    $filcoapilih="";
    if ($ketemu>0) {
        while ($r=  mysqli_fetch_array($tampil)) {
            $xccoaid=$r['COA4'];
            $filcoapilih .= "'".$xccoaid."',";
        }
        if (!empty($filcoapilih)) {
            $filcoapilih="(".substr($filcoapilih, 0, -1).")";
        }
    }
    
    $filtipetglpil=" AND Date_format(MODIFDATE, '%Y-%m-%d') ";
    if ($ptgltipe=="2") $filtipetglpil=" AND Date_format(tgltrans, '%Y-%m-%d') ";
    if ($ptgltipe=="3") $filtipetglpil=" AND Date_format(tgltrm, '%Y-%m-%d') ";
    if ($ptgltipe=="4") $filtipetglpil=" AND Date_format(tgl, '%Y-%m-%d') ";
    if ($ptgltipe=="5") $filtipetglpil=" AND Date_format(tglrpsby, '%Y-%m-%d') ";
    
    $filtipetglpil=$filtipetglpil." BETWEEN '$tgl1' AND '$tgl2' ";
    
    $filteruntukcoa="";
    if (!empty($filcoapilih)) {
        $filteruntukcoa = " AND COA4 IN $filcoapilih ";
    }
    
    $filterdivprod="";
    if (!empty($pdivisi)) {
        $filterdivprod = " AND divprodid = '$pdivisi' ";
    }
    
    
    
    //echo "$ptgltipe, $date1 - $date2, $tgl1 - $tgl2, $pdivisi, $upilidcrd, $filcoapilih";
    //echo "<br/>&nbsp;<br/>&nbsp;$filtipetglpil<br/>$filternondssdcc<br/>$filteruntukcoa";
    
    
    $now=date("mdYhis");
    $tmp01 =" dbtemp.TMPBRDCNNP01_".$puserid."_$now ";
    $tmp02 =" dbtemp.TMPBRDCNNP02_".$puserid."_$now ";
    $tmp03 =" dbtemp.TMPBRDCNNP03_".$puserid."_$now ";
    $tmp04 =" dbtemp.TMPBRDCNNP04_".$puserid."_$now ";
    $tmp05 =" dbtemp.TMPBRDCNNP05_".$puserid."_$now ";
    $tmp06 =" dbtemp.TMPBRDCNNP06_".$puserid."_$now ";
    
    
    $query = "SELECT TRIM(LEADING '0' FROM user1) as user1, brid, tgl, tgltrans, tgltrm, tglrpsby, tglunrtr, coa4, icabangid, idcabang, "
            . " ccyid, jumlah, jumlah1, realisasi1, cn, noslip, "
            . " aktivitas1, aktivitas2, "
            . " kode, dokterid, dokter, karyawanid, karyawani2, karyawani3, karyawani4, mrid, lampiran, via, ca, sby, "
            . " divprodid, pajak, nama_pengusaha, noseri, tgl_fp, dpp, ppn, ppn_rp, pph_jns, pph, pph_rp, pembulatan, jasa_rp, materai_rp, jenis_dpp,"
            . " noseri_pph, tgl_fp_pph, dpp_pph, batal, alasan_b, lain2 FROM hrd.br0 WHERE 1=1 AND (user1='$pnuseriid' OR user1='$upilidcrd') $filtipetglpil $filteruntukcoa $filterdivprod ";
    $query.=" and brId not in (select distinct ifnull(brId,'') from hrd.br0_reject) ";
    $query = "create TEMPORARY table $tmp01 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    //JOIN ke KODE untuk pemisah NON DAN DCC DSS
    $query = "select a.*, b.nama AS nama_kode, b.br from $tmp01 a "
            . " LEFT JOIN hrd.br_kode b ON a.kode = b.kodeid AND a.divprodid = b.divprodid";
    $query = "create TEMPORARY table $tmp02 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    //AMBIL NON DCC DSS
    $query = "SELECT * FROM $tmp02 WHERE 1=1 $filternondssdcc";
    $query = "create TEMPORARY table $tmp03 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "select dokterid, nama from hrd.dokter WHERE dokterid IN (select distinct IFNULL(dokterid,'') FROM $tmp03)";
    $query = "create TEMPORARY table $tmp04 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    //JOIN KARYAWAN CABANG dan DAERAH
    $query = "SELECT a.*, b.nama nama_karyawan, c.nama nama_cabang, d.nama nama_daerah, e.nama nama_dokter, "
            . " f.nama nama_user, CAST('' as CHAR(50)) as nodivisi FROM $tmp03 a "
            . " LEFT JOIN hrd.karyawan b on a.karyawanid=b.karyawanid "
            . " LEFT JOIN MKT.icabang c on a.icabangid=c.icabangid "
            . " LEFT JOIN MKT.cbgytd d on a.idcabang=d.idcabang "
            . " LEFT JOIN $tmp04 e on a.dokterid=e.dokterid "
            . " LEFT JOIN hrd.karyawan f on a.user1=TRIM(LEADING '0' FROM f.karyawanid)";
    $query = "create TEMPORARY table $tmp05 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    
    $query = "select distinct a.bridinput, b.nodivisi, b.pilih, a.amount, a.jml_adj, b.kodeid, b.subkode "
            . " from dbmaster.t_suratdana_br1 a JOIN dbmaster.t_suratdana_br b on a.idinput=b.idinput "
            . " WHERE b.stsnonaktif<>'Y' AND a.kodeinput IN ('A', 'B', 'C') AND b.divisi<>'OTC' AND a.bridinput IN (select distinct IFNULL(brid,'') FROM $tmp05)";
    $query = "create TEMPORARY table $tmp06 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "UPDATE $tmp05 a JOIN (select distinct bridinput, nodivisi FROM $tmp06 WHERE IFNULL(pilih,'')='Y') b "
            . " ON a.brid=b.bridinput SET a.nodivisi=b.nodivisi";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp05 a JOIN (select distinct bridinput, nodivisi FROM $tmp06) b "
            . " ON a.brid=b.bridinput SET a.nodivisi=b.nodivisi WHERE IFNULL(a.nodivisi,'')=''";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    
?>

<link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />
<script src="js/inputmask.js"></script>

<form method='POST' action='<?PHP echo "?module='$pmodule'&act=input&idmenu=$pidmenu"; ?>' id='demo-form4' name='form4' data-parsley-validate class='form-horizontal form-label-left'>
    
    
    <div class='x_content'>
        
        <table id='datatabledccds3' class='table table-striped table-bordered' width='100%'>
            
            <thead>
                <tr>
                    <th width='7px'>No</th>
                    <th>Jumlah</th>
                    <th>Rpt. SBY</th>
                    <th width='60px'>Tgl. Rpt. SBY</th>
                    <th width='50px'>Noslip</th>
                    <th width='100px'>Dokter / Realisasi</th>
                    <th width='80px'>Yg Membuat</th>
                    <th width='50px'>Realisasi</th>
                    <th nowrap>Keterangan</th>
                    <th></th>
                    <th>ID</th>

                </tr>
            </thead>
            <tbody>
                
                <?PHP
                $ntno=1;
                $gtotal=0;
                $query ="select distinct IFNULL(tgltrans,'0000-00-00') tgltrans from $tmp05 ORDER BY tgltrans";
                $tampil1=mysqli_query($cnmy, $query);
                while ($row1= mysqli_fetch_array($tampil1)) {
                    $ntgltrans=$row1['tgltrans'];
                    
                    $ptgltrans = "";
                    if (!empty($row1['tgltrans']) AND $row1['tgltrans']<> "0000-00-00")
                        $ptgltrans =date("d-M-Y", strtotime($row1['tgltrans']));
                    
                    $nnamaid="chk_sby".$ntno."[]";
                    $ptxtsbyall="<input type='checkbox' id='chk_ntsby$ntno' name='chk_ntsby$ntno' class='input' value='select' onclick=\"SelAllCheckBoxNoDivisi('chk_ntsby$ntno', '$nnamaid')\">";
                    
                    echo "<tr>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap align='right'><b>Tgl. Trans : </b></td>";
                    echo "<td nowrap><b>$ptgltrans $ptxtsbyall</b></td>";
                    echo "<td ></td>";
                    echo "<td ></td>";
                    echo "<td ></td>";
                    echo "<td ></td>";
                    echo "<td ></td>";
                    echo "<td ></td>";
                    echo "<td ></td>";
                    echo "<td ></td>";
                    echo "</tr>";
                    
                    $query ="select * from $tmp05 where IFNULL(tgltrans,'0000-00-00')='$ntgltrans' ORDER BY tgltrans";
                    $no=1;
                    $ptotjumlah=0;
                    $tampil=mysqli_query($cnmy, $query);
                    while ($row= mysqli_fetch_array($tampil)) {
                        $dok="";
                        if (!empty($row['dokterId'])) $dok=$row["nama_dokter"];

                        $ptglsby = "";
                        if (!empty($row['tglrpsby']) AND $row['tglrpsby']<> "0000-00-00")
                            $ptglsby =date("d-M-Y", strtotime($row['tglrpsby']));

                        $ptgltrans = "";
                        if (!empty($row['tgltrans']) AND $row['tgltrans']<> "0000-00-00")
                            $ptgltrans =date("d-M-Y", strtotime($row['tgltrans']));

                        $ptglinput =date("d-M-Y", strtotime($row['tgl']));

                        $ptgltrm = "";
                        if (!empty($row['tgltrm']) AND $row['tgltrm']<> "0000-00-00")
                            $ptgltrm =date("d-M-Y", strtotime($row['tgltrm']));
                        
                        $tojumlah = $row["jumlah"];
                        $toreal = $row["jumlah1"];
                        if (empty($tojumlah)) $tojumlah=0;
                        if (empty($toreal)) $toreal=0;
                        if ((DOUBLE)$toreal<>0) $tojumlah=$toreal;
                        $ptotjumlah=$ptotjumlah+$tojumlah;
                        
                        $pjumlah = $row["jumlah"];
                        $pjumlah=number_format($pjumlah,0,",",",");

                        $pjmlreal = $row["jumlah1"];
                        $pjmlreal=number_format($pjmlreal,0,",",",");
                        
                        
                        if ($pjmlreal<>0) $pjumlah=$pjmlreal;
                        
                        $paktivitas = $row["aktivitas1"];
                        $pnmrealisasi = $row["realisasi1"];
                        $pnoslip = $row["noslip"];
                        $pnmkode = $row["nama_kode"];
                        $pnmkaryawan = $row["nama_karyawan"];
                        $pnmcab = $row["nama_cabang"];
                        $plain = $row["lain2"];
                        $psby = $row["sby"];
                        $pnodivisi = $row["nodivisi"];
                        $chkrptsby="";
                        $ndisable="";
                        
                        $pbrid = $row["brid"];

                        $ptxtnobrid="<input type='hidden' size='10px' id='e_nobrid$no' name='e_nobrid$no' class='input-sm' autocomplete='off' value='$pbrid'>";
                        
                        if ($psby=="Y") {
                            $chkrptsby="checked";
                            $ndisable="disabled";
                            $nnamaid="";
                        }else{
                            $nnamaid="chk_sby".$ntno."[]";
                        }
                        $ptxtsby="<input type='checkbox' id='$nnamaid' name='$nnamaid' class='input' value='$pbrid' $chkrptsby $ndisable>";

                        $fsimpan="'e_nobrid$no', 'e_tglsby$no', 'chk_sby$no'";
                        $simpandata= "<input type='button' class='btn btn-info btn-xs' id='s-submit' value='Save Real' onclick=\"SimpanData('input', $fsimpan)\">";
                        $pedit = "<a class='btn btn-success btn-xs' href='?module=$_GET[module]&act=editdata&idmenu=$_GET[idmenu]&nmun=$_GET[nmun]&id=$pbrid'>Edit</a>";
                        $phapus = "<input type='button' class='btn btn-danger btn-xs' value='Hapus' onClick=\"ProsesDataHapus('hapus', '$pbrid', '$pnodivisi')\">";
                        
                        echo "<tr>";
                        echo "<td nowrap>$no $ptxtnobrid</td>";
                        echo "<td nowrap align='right'>$pjumlah</td>";
                        echo "<td nowrap>$ptxtsby</td>";
                        echo "<td nowrap>$ptglsby</td>";

                        echo "<td nowrap>$pnoslip</td>";
                        echo "<td>$dok</td>";
                        echo "<td nowrap>$pnmkaryawan</td>";
                        echo "<td nowrap>$pnmrealisasi</td>";
                        echo "<td >$paktivitas</td>";
                        echo "<td nowrap>$pedit $phapus</td>";
                        echo "<td nowrap>$pbrid</td>";
                        echo "</tr>";

                        $no++;
                    }
                    $gtotal=$gtotal+$ptotjumlah;
                    $ptotjumlah=number_format($ptotjumlah,0,",",",");
                    echo "<tr>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap align='right'><b>Total : </b></td>";
                    echo "<td nowrap><b>$ptotjumlah</b></td>";
                    echo "<td ></td>";
                    echo "<td ></td>";
                    echo "<td ></td>";
                    echo "<td ></td>";
                    echo "<td ></td>";
                    echo "<td ></td>";
                    echo "<td ></td>";
                    echo "<td ></td>";
                    echo "</tr>";
                    
                    $ntno++;
                }

                $gtotal=number_format($gtotal,0,",",",");
                echo "<tr>";
                echo "<td nowrap></td>";
                echo "<td nowrap align='right'><b>Grand Total :</b></td>";
                echo "<td nowrap><b>$gtotal</b></b></td>";
                echo "<td ></td>";
                echo "<td ></td>";
                echo "<td ></td>";
                echo "<td ></td>";
                echo "<td ></td>";
                echo "<td ></td>";
                echo "<td ></td>";
                echo "<td ></td>";
                echo "</tr>";
                
                ?>
                
            </tbody>
            
        </table>
        
        
        <?PHP
        $ntno=$ntno;
        $ptxttotrec="<input type='hidden' size='10px' id='e_totrec' name='e_totrec' class='input-sm' autocomplete='off' value='$ntno'>";
        echo $ptxttotrec;
        ?>
        <br/>&nbsp;<br/>&nbsp;
        <div class='col-md-12 col-sm-12 col-xs-12'>
            <div class='x_panel'>
                
                <div class='col-sm-3'>
                    Tgl. Report SBY.
                   <div class="form-group">
                        <div class='input-group date' for='mytgl02'>
                            <input type="text" class="form-control" id='mytgl02' name='e_tgltrans' autocomplete="off" required='required' placeholder='dd/MM/yyyy' data-inputmask="'mask': '99/99/9999'" value='<?PHP echo $tgl_pertama; ?>'>
                            <span class='input-group-addon'>
                                <span class='glyphicon glyphicon-calendar'></span>
                            </span>
                        </div>
                   </div>
               </div>

                <div class='col-sm-3'>
                    <small>&nbsp;</small>
                   <div class="form-group">
                       <input type='button' class='btn btn-danger btn-sm' id="s-submit" value="Save" onclick='disp_confirmrpsby("Simpan ?")'>
                   </div>
               </div>
                
            </div>
        </div>
        
        
    </div>


</form>


<script>
    $(document).ready(function() {
        var dataTable = $('#datatabledccds3').DataTable( {
            fixedHeader: true,
            "stateSave": true,
            "ordering": false,
            "order": [[ 1, "asc" ]],
            "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
            "displayLength": -1,
            "columnDefs": [
                { "visible": false },
                { "orderable": false, "targets": 0 },
                //{ className: "text-right", "targets": [6] },//right
                { className: "text-nowrap", "targets": [0, 1, 2, 3, 4] }//nowrap

            ],
            "language": {
                "zeroRecords": "Lihat Page di bawah!!! Jika ada Page, Pilih Page 1...!!! Jika tidak ada Page, maka data KOSONG..."
            },
            bInfo: false, "bLengthChange": false, "bLengthChange": false,
            "bPaginate": false/*,
            rowReorder: {
                selector: 'td:nth-child(5)'
            },
            responsive: true*/
        } );
    } );

    $('#mytgl01, #mytgl02').datetimepicker({
        ignoreReadonly: true,
        allowInputToggle: true,
        format: 'DD/MM/YYYY'
    });
    
    function SelAllCheckBoxNoDivisi(nmbuton, data){
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
    
    
    function disp_confirmrpsby(pText_){
        var pText_="Simpan";
        ok_ = 1;
        if (ok_) {
            var r=confirm(pText_)
            if (r==true) {
                
                var tglsby=document.getElementById('mytgl02').value;
                var jmlrec=document.getElementById('e_totrec').value;
                var chk_arr =  "";//document.getElementsByName('chk_sby1[]');
                var chklength = "";//chk_arr.length;
                var allnobr="";
                
                for(x=0;x< jmlrec;x++) {
                    var nm="chk_sby"+x+"[]";
                    chk_arr =  document.getElementsByName(nm);
                    chklength = chk_arr.length;
                    
                    for(k=0;k< chklength;k++)
                    {
                        if (chk_arr[k].checked == true) {
                            var kata = chk_arr[k].value;
                            var fields = kata.split('-');
                            allnobr =allnobr + "'"+fields[0]+"',";
                        }
                    }
                }
                
                var myurl = window.location;
                var urlku = new URL(myurl);
                var module = urlku.searchParams.get("module");
                var act = "inputsby";
                var idmenu = urlku.searchParams.get("idmenu");
                
                $.ajax({
                    type:"post",
                    url:"module/mod_br_entrydcc/aksi_simpansby.php?module="+module+"&act="+act+"&idmenu="+idmenu,
                    data:"uidbr="+allnobr+"&utglsby="+tglsby,
                    success:function(data){
                        if (data.length > 1) {
                            alert(data);
                        }else{
                            PilihData3();
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

<style>
    .divnone {
        display: none;
    }
    #datatabledccds3 th {
        font-size: 12px;
    }
    #datatabledccds3 td { 
        font-size: 11px;
    }
</style>

<?PHP
hapusdata:
    mysqli_query($cnmy, "drop TEMPORARY table $tmp01");
    mysqli_query($cnmy, "drop TEMPORARY table $tmp02");
    mysqli_query($cnmy, "drop TEMPORARY table $tmp03");
    mysqli_query($cnmy, "drop TEMPORARY table $tmp04");
    mysqli_query($cnmy, "drop TEMPORARY table $tmp05");
    mysqli_query($cnmy, "drop TEMPORARY table $tmp06");
    
    mysqli_close($cnmy);
?>