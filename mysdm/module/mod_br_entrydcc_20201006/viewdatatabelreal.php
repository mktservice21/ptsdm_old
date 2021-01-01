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
<script src="js/inputmask.js"></script
    
<form method='POST' action='<?PHP echo "?module='$pmodule'&act=input&idmenu=$pidmenu"; ?>' id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left'>
    
    <div class='x_content'>
        
        <table id='datatabledccds2' class='table table-striped table-bordered' width='100%'>
            
            <thead>
                <tr>
                    <th width='7px'>No</th>
                    <th>Jumlah</th>
                    <th width='60px'>Realisasi</th><th width='60px'>Tgl. Terima</th><th>Lain-Lain</th><th>Batal</th>
                    <th></th>
                    <th width='50px'>Noslip</th>
                    <th width='100px'>Dokter / Realisasi</th>
                    <th width='80px'>Yg Membuat</th>
                    <th width='50px'>Realisasi</th>
                    <th nowrap>Tgl. Transfer</th>
                    <th nowrap>Tgl. Rpt. SBY</th>
                    <th nowrap>Tgl. Input</th>
                    <th nowrap>Keterangan</th>
                    <th>Kode</th>
                    <th>No Divisi/BR</th>
                    <th>ID</th>

                </tr>
            </thead>
            
            <tbody>
                <?PHP
                $no=1;
                $query = "select * from $tmp05 order by brid DESC";
                $tampil= mysqli_query($cnmy, $query);
                while ($row= mysqli_fetch_array($tampil)) {
                    
                    $pbrid=$row['brid'];
                    $ptglbr = $row["tgl"];
                    $ptgltrans = $row["tgltrans"];
                    $ptgltrm = $row["tgltrm"];
                    $ptglrpsby = $row["tglrpsby"];
                    $paktivitas1 = $row["aktivitas1"];
                    $pnama_kry = $row["nama_karyawan"];
                    $pnm_cab = $row["nama_cabang"];
                    $pjumlah = $row["jumlah"];
                    $pjmlreal = $row["jumlah1"];
                    $pnmreal = $row["realisasi1"];
                    $pnoslip = $row["noslip"];
                    $pnm_kode = $row["nama_kode"];
                    $piduser = $row["user1"];
                    $pnmuser = $row["nama_user"];
                    $pnodivisi = $row["nodivisi"];
                    $plain = $row["lain2"];
                    $pbatal = $row["batal"];
                    $pnmdokter = $row["nama_dokter"];
                    
                    $chkbatal="";
                    if ($pbatal=="Y") $chkbatal="checked";
                    
                    if ($ptgltrans=="0000-00-00") $ptgltrans="";
                    if ($ptgltrm=="0000-00-00") $ptgltrm="";
                    if ($ptglrpsby=="0000-00-00") $ptglrpsby="";
                    
                    $ntglbrpilih=$ptglbr;
                    $ntgltrsfpilih=$ptgltrans;
                    
                    if (!empty($ptglbr)) {
                        $ptglbr =date("d-M-Y", strtotime($ptglbr));
                        $ntglbrpilih= "<a href='#' data-toggle=\"tooltip\" data-placement=\"top\" title=".$pbrid.">".$ptglbr."</a>";
                    }
                    if (!empty($ptgltrans)) {
                        $ptgltrans =date("d-M-Y", strtotime($ptgltrans));
                        $ntgltrsfpilih = "<a href='#' title=".$pnm_kode.">".$ptgltrans."</a>";
                    }
                    if (!empty($ptgltrm)) $ptgltrm =date("Y-m-d", strtotime($ptgltrm));
                    if (!empty($ptglrpsby)) $ptglrpsby =date("d-M-Y", strtotime($ptglrpsby));
                    
                    
                    
                    $ptxtnobrid="<input type='hidden' size='10px' id='e_nobrid[$pbrid]' name='e_nobrid[$pbrid]' class='input-sm' autocomplete='off' value='$pbrid'>";
                    $ptxtjmlreal="<input type='text' size='10px' id='e_jmlreal[$pbrid]' name='e_jmlreal[$pbrid]' class='input-sm inputmaskrp2' autocomplete='off' value='$pjmlreal'>";
                    $ptxttglterima="<input type='date' size='5px' id='e_tglterima[$pbrid]' name='e_tglterima[$pbrid]' class='input' autocomplete='off' value='$ptgltrm'>";
                    $ptxtlain="<input type='text' size='10px' id='e_lain[$pbrid]' name='e_lain[$pbrid]' class='input' autocomplete='off' value='$plain'>";
                    $ptxtbatal="<input type='checkbox' id='chk_batal[$pbrid]' name='chk_batal[$pbrid]' class='input' value='$pbatal' $chkbatal>";
                    
                    $fsimpan="'e_nobrid[$pbrid]', 'e_jmlreal[$pbrid]', 'e_tglterima[$pbrid]', 'e_lain[$pbrid]', 'chk_batal[$pbrid]'";
                    $simpandata= "<input type='button' class='btn btn-info btn-xs' id='s-submit' value='Save Real' onclick=\"SimpanDataRealSatu('realsimpansatu', $fsimpan)\">";
                    
                    
                    $pjumlah=number_format($pjumlah,0,",",",");
                    $pjmlreal=number_format($pjmlreal,0,",",",");
                    
                    
                    $pterima = "<a class='btn btn-info btn-xs' href='?module=$pmodule&act=editterima&idmenu=$pidmenu&nmun=$pidmenu&id=$pbrid'>Terima</a>";
                    $prealis = "<a class='btn btn-default btn-xs' href='?module=$pmodule&act=edittransfer&idmenu=$pidmenu&nmun=$pidmenu&id=$pbrid'>Realisasi</a>";
                    $peditdata = "<a class='btn btn-success btn-xs' href='?module=$pmodule&act=editdata&idmenu=$pidmenu&nmun=$pidmenu&id=$pbrid'>Edit</a>";
                    $ptpajak = "<button type='button' class='btn btn-primary btn-xs' data-toggle='modal' data-target='#myModal' onClick=\"TambahDataInputPajak('$pbrid')\">Pajak</button>";
                    $phapus = "<input type='button' class='btn btn-danger btn-xs' value='Hapus' onClick=\"ProsesDataHapus('hapus', '$pbrid', '$pnodivisi')\">";
                   
                    if ($pgroupid=="1") {
                        
                    }else{
                        if ($piduser<>$puserid) {
                            $peditdata="";
                            $phapus="";
                            $simpandata="";
                        }
                    }
                    
                    echo "<tr>";
                    echo "<td nowrap>$no $ptxtnobrid</td>";
                    echo "<td nowrap align='right'>$pjumlah</td>";
                    echo "<td nowrap align='right'>$ptxtjmlreal</td>";
                    echo "<td nowrap>$ptxttglterima</td>";
                    echo "<td nowrap>$ptxtlain</td>";
                    echo "<td nowrap>$ptxtbatal</td>";
                    echo "<td nowrap>$simpandata $peditdata $ptpajak $phapus</td>";
                    
                    echo "<td nowrap>$pnoslip</td>";
                    echo "<td nowrap>$pnmdokter</td>";
                    echo "<td nowrap>$pnama_kry</td>";
                    echo "<td nowrap>$pnmreal</td>";
                    echo "<td nowrap>$ptgltrans</td>";
                    echo "<td nowrap>$ptglrpsby</td>";
                    echo "<td nowrap>$ptglbr</td>";
                    echo "<td nowrap>$paktivitas1</td>";
                    echo "<td nowrap>$pnm_kode</td>";
                    echo "<td nowrap>$pnodivisi</td>";
                    echo "<td nowrap>$pbrid</td>";
                    echo "</tr>";
                    
                    $no++;
                    
                }
                ?>
            </tbody>
            
            
        </table>  
            
        </table>
        
        
    </div>

</form>


<script>
    $(document).ready(function() {
        var dataTable = $('#datatabledccds2').DataTable( {
            fixedHeader: false,
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
            "bPaginate": false,
            rowReorder: {
                selector: 'td:nth-child(7)'
            },
            responsive: true
        } );
    } );
    
    
    
    function SimpanDataRealSatu(eact, idbr, ajmlreal,  atglterima, alain, abatal)  {
        var eidbr =document.getElementById(idbr).value;
        var ejmlreal =document.getElementById(ajmlreal).value;
        var etglterima =document.getElementById(atglterima).value;
        var elain =document.getElementById(alain).value;
        var ebatal =document.getElementById(abatal).checked;

        if (eidbr==""){
            alert("id kosong....");
            return 0;
        }
        
        
        //alert(eidbr+", "+ejmlreal+", "+etglterima+", "+elain+", "+ebatal); return 0;
        var pText_="Simpan";
        if (eact=="hapus") var pText_="Hapus";

        ok_ = 1;
        if (ok_) {
            var r=confirm(pText_)
            if (r==true) {
                
                
                if (ebatal==true){
                    var txtbatal="";
                    var textket = prompt("Masukan alasan Batal : ", "");
                    if (textket == null || textket == "") {
                        txtbatal = textket;
                    } else {
                        txtbatal = textket;
                    }

                }
                
                var myurl = window.location;
                var urlku = new URL(myurl);
                var module = urlku.searchParams.get("module");
                var idmenu = urlku.searchParams.get("idmenu");

                $.ajax({
                    type:"post",
                    url:"module/mod_br_entrydcc/aksi_simpanreal.php?module="+module+"&act="+eact+"&idmenu="+idmenu,
                    data:"uidbr="+eidbr+"&ujmlreal="+ejmlreal+"&ulain="+elain+"&utglterima="+etglterima+"&ubatal="+ebatal+"&utxtbatal="+txtbatal,
                    success:function(data){
                        if (data.length > 2) {
                            alert(data);
                        }
                        if (eact=="hapus" && data.length <= 1) {
                            //document.getElementById(enoslip).value="";
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
    #datatabledccds2 th {
        font-size: 12px;
    }
    #datatabledccds2 td { 
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