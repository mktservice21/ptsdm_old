<?PHP
    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","512M");
    ini_set('max_execution_time', 0);
    
    session_start();
    
    $puserid="";
    if (isset($_SESSION['USERID'])) $puserid=$_SESSION['USERID'];

    if (empty($puserid)) {
        echo "ANDA HARUS LOGIN ULANG...";
        exit;
    }

    $pkaryawanid=$_SESSION['IDCARD'];
    $pgroupid=$_SESSION['GROUP'];
    
    $ptgl1=$_POST['utgl1'];
    $ptgl2=$_POST['utgl2'];
    $ptgltipe=$_POST['utipeid'];
    
    $pnuseriid=$_SESSION['USERID'];
    $upilidcrd=$_SESSION['IDCARD'];
    
    $_SESSION['FINDDTGLTIPE']=$ptgltipe;
    $_SESSION['FINDDPERENTY1']=$ptgl1;
    $_SESSION['FINDDPERENTY2']=$ptgl2;
    
    
    $ptgl1 = date('Y-m-01', strtotime($ptgl1));
    $ptgl2 = date('Y-m-t', strtotime($ptgl2));

    $pmodule=$_GET['module'];
    $pact=$_GET['act'];
    $pidmenu=$_GET['idmenu'];

    include "../../../config/koneksimysqli.php";
    include "../../../config/fungsi_ubahget_id.php";
    
    
    $now=date("mdYhis");
    $tmp01 =" dbtemp.TMPBRDCNNP01_".$puserid."_$now ";
    $tmp02 =" dbtemp.TMPBRDCNNP02_".$puserid."_$now ";
    $tmp03 =" dbtemp.TMPBRDCNNP03_".$puserid."_$now ";
    $tmp04 =" dbtemp.TMPBRDCNNP04_".$puserid."_$now ";
    $tmp05 =" dbtemp.TMPBRDCNNP05_".$puserid."_$now ";
    $tmp06 =" dbtemp.TMPBRDCNNP06_".$puserid."_$now ";
    
    $query = "select kodeid from hrd.br_kode where br <> '' and br<>'N'";
    $tampil=mysqli_query($cnmy, $query);
    $ketemu=mysqli_num_rows($tampil);
    $filkode="";
    if ($ketemu>0) {
        while ($r=  mysqli_fetch_array($tampil)) {
            $xccoaid=$r['kodeid'];
            $filkode .= "'".$xccoaid."',";
        }
        if (!empty($filkode)) {
            $filkode="(".substr($filkode, 0, -1).")";
        }else{
            $filkode="('')";
        }
    }else{
        $filkode="('')";
    }
    $filterkodeid=" AND kode IN $filkode ";
    
    $filternondssdcc=" AND ( (br <> '' and br<>'N') OR user1=$pnuseriid ) ";
    
    
    $fiteruser=" AND (user1='$pnuseriid' OR user1='$upilidcrd') ";
    $filtipetglpil=" AND Date_format(MODIFDATE, '%Y-%m-%d') ";
    if ($ptgltipe=="2") $filtipetglpil=" AND Date_format(tgltrans, '%Y-%m-%d') ";
    if ($ptgltipe=="3") $filtipetglpil=" AND Date_format(tgltrm, '%Y-%m-%d') ";
    if ($ptgltipe=="4") $filtipetglpil=" AND Date_format(tgl, '%Y-%m-%d') ";
    if ($ptgltipe=="5") $filtipetglpil=" AND Date_format(tglrpsby, '%Y-%m-%d') ";
    
    $filtipetglpil=$filtipetglpil." BETWEEN '$ptgl1' AND '$ptgl2' ";
    
    $filterkodeid=" AND kode IN ('700-01-03','700-01-04','700-02-03','700-02-04','700-04-03','700-04-04')";
    $filterdivprod="";
    
    if ($pgroupid=="1" OR $pgroupid=="24") {
        $fiteruser="";
    }
    
    $query = "SELECT TRIM(LEADING '0' FROM user1) as user1, brid, tgl, tgltrans, tgltrm, tglrpsby, tglunrtr, coa4, icabangid, idcabang, "
            . " ccyid, jumlah, jumlah1, realisasi1, cn, noslip, "
            . " aktivitas1, aktivitas2, "
            . " kode, dokterid, dokter, karyawanid, karyawani2, karyawani3, karyawani4, mrid, lampiran, via, ca, sby, "
            . " divprodid, pajak, nama_pengusaha, noseri, tgl_fp, dpp, ppn, ppn_rp, pph_jns, pph, pph_rp, pembulatan, jasa_rp, materai_rp, jenis_dpp,"
            . " noseri_pph, tgl_fp_pph, dpp_pph, batal, alasan_b, lain2 FROM hrd.br0 WHERE 1=1 "
            . " $filtipetglpil $fiteruser $filterdivprod $filterkodeid ";
    $query.=" and brId not in (select distinct ifnull(brId,'') from hrd.br0_reject) ";
    $query = "create TEMPORARY table $tmp01 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    //JOIN ke KODE untuk pemisah DAN DCC DSS
    $query = "select a.*, b.nama AS nama_kode, b.br from $tmp01 a "
            . " LEFT JOIN hrd.br_kode b ON a.kode = b.kodeid AND a.divprodid = b.divprodid";
    $query = "create TEMPORARY table $tmp02 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    //AMBIL DCC DSS
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
            . " f.nama nama_user, g.NAMA4, CAST('' as CHAR(50)) as nodivisi FROM $tmp03 a "
            . " LEFT JOIN hrd.karyawan b on a.karyawanid=b.karyawanid "
            . " LEFT JOIN mkt.icabang c on a.icabangid=c.icabangid "
            . " LEFT JOIN ms.cbgytd d on a.idcabang=d.idcabang "
            . " LEFT JOIN $tmp04 e on a.dokterid=e.dokterid "
            . " LEFT JOIN hrd.karyawan f on a.user1=TRIM(LEADING '0' FROM f.karyawanid) "
            . " LEFT JOIN dbmaster.coa_level4 g on a.coa4=g.COA4";
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

<form method='POST' action='<?PHP echo "?module=$pmodule&act=input&idmenu=$pidmenu"; ?>' 
      id='d-form2' name='form2' data-parsley-validate class='form-horizontal form-label-left'>
    <div class='x_content'>
        <table id='datatabledcds' class='table table-striped table-bordered' width='100%'>
            <thead>
                <tr>
                    <th width='7px'>No</th>
                    <th></th>
                    <th width='50px'>ID</th>
                    <th width='60px'>Tanggal</th>
                    <th width='60px'>Tgl. Transfer</th>
                    <th width='60px'>Tgl. Terima</th>
                    <th>Keterangan</th><th>Yg Membuat</th>
                    <th width='80px'>Cabang</th>
                    <th width='60px'>Dokter</th>
                    <th width='50px'>Jumlah</th>
                    <th width='60px'>Realisasi</th>
                    <th width='50px'>Realisasi</th>
                    <th width='50px'>No Slip</th>
                    <th width='50px'>Kode</th>
                    <th width='50px'>No Div/BR</th>
                    <th width='50px'>User Input</th>
                    <th width='50px'>Divisi</th>
                    <th width='50px'>COA - Perkiraan</th>

                </tr>
            </thead>
            <tbody>
                <?PHP
                $no=1;
                $query = "select * from $tmp05 order by brid DESC";
                $tampil= mysqli_query($cnmy, $query);
                while ($row= mysqli_fetch_array($tampil)) {
                    
                    $pbrid=$row['brid'];
                    $pdividprod=$row['divprodid'];
                    $pcoaid=$row['coa4'];
                    $pcoanm=$row['NAMA4'];
                    $ptglbr = $row["tgl"];
                    $ptgltrans = $row["tgltrans"];
                    $ptgltrm = $row["tgltrm"];
                    $paktivitas1 = $row["aktivitas1"];
                    $pnama_kry = $row["nama_karyawan"];
                    $pnm_cab = $row["nama_cabang"];
                    $pnm_dokter = $row["nama_dokter"];
                    $pjumlah = $row["jumlah"];
                    $pjmlreal = $row["jumlah1"];
                    $pnmreal = $row["realisasi1"];
                    $pnoslip = $row["noslip"];
                    $pnm_kode = $row["nama_kode"];
                    $piduser = $row["user1"];
                    $pnmuser = $row["nama_user"];
                    $pnodivisi = $row["nodivisi"];
                    $ppilpajak = $row["pajak"];
                    $pbatal = $row["batal"];
                    
                    $pidget=encodeString($pbrid);
                    
                    if ($ptgltrans=="0000-00-00") $ptgltrans="";
                    if ($ptgltrm=="0000-00-00") $ptgltrm="";
                    
                    $ntglbrpilih=$ptglbr;
                    $ntgltrsfpilih=$ptgltrans;
                    
                    $pthnbr =date("Y", strtotime($ptglbr));
                    
                    if (!empty($ptglbr)) {
                        $ptglbr =date("d-M-Y", strtotime($ptglbr));
                        $ntglbrpilih= "<a href='#' data-toggle=\"tooltip\" data-placement=\"top\" title=".$pbrid.">".$ptglbr."</a>";
                    }
                    if (!empty($ptgltrans)) {
                        $ptgltrans =date("d-M-Y", strtotime($ptgltrans));
                        $ntgltrsfpilih = "<a href='#' title=".$pnm_kode.">".$ptgltrans."</a>";
                    }
                    if (!empty($ptgltrm)) $ptgltrm =date("d-M-Y", strtotime($ptgltrm));
                    
                    
                    
                    
                    $pjumlah=number_format($pjumlah,0,",",",");
                    $pjmlreal=number_format($pjmlreal,0,",",",");
                    
                    
                    $pterima = "<a class='btn btn-info btn-xs' href='?module=$pmodule&act=editterima&idmenu=$pidmenu&nmun=$pidmenu&id=$pbrid'>Terima</a>";
                    $prealis = "<a class='btn btn-default btn-xs' href='?module=$pmodule&act=edittransfer&idmenu=$pidmenu&nmun=$pidmenu&id=$pbrid'>Realisasi</a>";
                    $peditdata = "<a class='btn btn-success btn-xs' href='?module=$pmodule&act=editdata&idmenu=$pidmenu&nmun=$pidmenu&id=$pidget'>Edit</a>";
                    $ptpajak = "<button type='button' class='btn btn-primary btn-xs' data-toggle='modal' data-target='#myModal' onClick=\"TambahDataInputPajak('$pbrid')\">Pajak</button>";
                    $phapus = "<input type='button' class='btn btn-danger btn-xs' value='Hapus' onClick=\"ProsesDataHapus('hapus', '$pbrid', '$pnodivisi')\">";
                    
                    $pnpajakedit = "<a class='btn btn-warning btn-xs' href='?module=$pmodule&act=editdata&idmenu=$pidmenu&nmun=$pidmenu&id=$pbrid'>Pajak</a>";
                   
                    if ($pgroupid=="1") {
                        
                    }else{
                        if ($piduser<>$puserid) {
                            $peditdata="";
                            $phapus="";
                            $pnpajakedit="";
                        }
                    }
                    
                    if ($ppilpajak!="Y") $ptpajak="";
                    
                    if ($pthnbr<2021) {
                        $phapus="";
                    }

                    $pcolorbatal="";
                    if ($pbatal=="Y") {
                        $phapus="";
                        $peditdata="";
                        $pcolorbatal="style='color:red;'";
                    }
                    
                    echo "<tr $pcolorbatal>";
                    echo "<td nowrap>$no</td>";
                    echo "<td nowrap>$peditdata $ptpajak $phapus</td>";
                    echo "<td nowrap>$pbrid</td>";
                    echo "<td nowrap>$ntglbrpilih</td>";
                    echo "<td nowrap>$ntgltrsfpilih</td>";
                    echo "<td nowrap>$ptgltrm</td>";
                    echo "<td>$paktivitas1</td>";
                    echo "<td nowrap>$pnama_kry</td>";
                    echo "<td nowrap>$pnm_cab</td>";
                    echo "<td nowrap>$pnm_dokter</td>";
                    echo "<td nowrap align='right'>$pjumlah</td>";
                    echo "<td nowrap align='right'>$pjmlreal</td>";
                    echo "<td nowrap>$pnmreal</td>";
                    echo "<td nowrap>$pnoslip</td>";
                    echo "<td nowrap>$pnm_kode</td>";
                    echo "<td nowrap>$pnodivisi</td>";
                    echo "<td nowrap>$pnmuser</td>";
                    echo "<td nowrap>$pdividprod</td>";
                    echo "<td nowrap>$pcoaid - $pcoanm</td>";
                    echo "</tr>";
                    
                    $no++;
                    
                }
                ?>
            </tbody>
        </table>
    </div>
    
</form>

<script>
    $(document).ready(function() {
        var dataTable = $('#datatabledcds').DataTable( {
            "stateSave": true,
            fixedHeader: true,
            //"ordering": false,
            "processing": true,
            "order": [[ 0, "asc" ], [ 1, "desc" ], [ 2, "desc" ], [ 3, "desc" ]],
            "lengthMenu": [[10, 50, 100, 10000000], [10, 50, 100, "All"]],
            "displayLength": 10,
            "columnDefs": [
                { "visible": false },
                { "orderable": false, "targets": 0 },
                { "orderable": false, "targets": 1 },
                { "orderable": true, "targets": 2 },
                { "orderable": true, "targets": 4 },
                { className: "text-right", "targets": [10, 11] },//right
                { className: "text-nowrap", "targets": [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12,13,14,15,16,17] }//nowrap

            ],
            "language": {
                "zeroRecords": "Lihat Page di bawah!!! Jika ada Page, Pilih Page 1...!!! Jika tidak ada Page, maka data KOSONG..."
            }
        } );
        $('div.dataTables_filter input', dataTable.table().container()).focus();
    } );
    
    
    function ProsesDataHapus(ket, noid, snodivi){

        ok_ = 1;
        if (ok_) {
            if (snodivi=="") {
                var r = confirm('Apakah akan melakukan proses '+ket+' ...?');
            }else{
                var r = confirm('Sudah Ada Nodivisi /no BR ('+snodivi+')...!!!\n\
Apakah akan melakukan proses '+ket+' ...?\n\
Status pada SPD akan berubah menjadi BATAL (merah)...');
            }
            if (r==true) {

                var txt;
                if (ket=="reject" || ket=="hapus" || ket=="pending") {
                    var textket = prompt("Masukan alasan "+ket+" : ", "");
                    if (textket == null || textket == "") {
                        txt = textket;
                    } else {
                        txt = textket;
                    }
                }

                var myurl = window.location;
                var urlku = new URL(myurl);
                var module = urlku.searchParams.get("module");
                var idmenu = urlku.searchParams.get("idmenu");

                //document.write("You pressed OK!")
                document.getElementById("d-form2").action = "module/budget/bgt_brdccdss/aksi_brdccdss.php?module="+module+"&act=hapus&idmenu="+idmenu+"&kethapus="+txt+"&ket="+ket+"&id="+noid+"&unodivisi="+snodivi;
                document.getElementById("d-form2").submit();
                return 1;
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }



    }
    
    function TambahDataInputPajak(eidbr){
        $.ajax({
            type:"post",
            url:"module/budget/bgt_brdccdss/tambah_pajakdcc.php?module=viewisipajak",
            data:"uidbr="+eidbr,
            success:function(data){
                $("#myModal").html(data);
            }
        });
    }
    
</script>

<style>
    .divnone {
        display: none;
    }
    #datatabledcds th {
        font-size: 12px;
    }
    #datatabledcds td { 
        font-size: 11px;
    }
</style>

<?PHP
hapusdata:
    mysqli_query($cnmy, "drop TEMPORARY table IF EXISTS $tmp01");
    mysqli_query($cnmy, "drop TEMPORARY table IF EXISTS $tmp02");
    mysqli_query($cnmy, "drop TEMPORARY table IF EXISTS $tmp03");
    mysqli_close($cnmy)
?>