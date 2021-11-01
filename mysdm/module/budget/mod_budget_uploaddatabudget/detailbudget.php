<?PHP
    function BuatFormatNum($prp, $ppilih) {
        if (empty($prp)) $prp=0;
        
        $numrp=$prp;
        if ($ppilih=="1") $numrp=number_format($prp,0,",",",");
        elseif ($ppilih=="2") $numrp=number_format($prp,0,".",".");
            
        return $numrp;
    }
    
    ini_set("memory_limit","500M");
    ini_set('max_execution_time', 0);
    
    session_start();
    
    $puserid="";
    if (isset($_SESSION['USERID'])) $puserid=$_SESSION['USERID'];

    if (empty($puserid)) {
        echo "ANDA HARUS LOGIN ULANG...";
        exit;
    }
    
    
    $pmodule=$_GET['module'];
    $pact=$_GET['act'];
    $pidmenu=437;//$_GET['idmenu'];
    
    $fkaryawan=$_SESSION['IDCARD'];
    $fdivisi=$_SESSION['DIVISI'];
    $fgroupidcard=$_SESSION['GROUP'];
    $fjbtid=$_SESSION['JABATANID'];
    
    $ptahunpilih=$_POST['utahun'];
    $pdivpilih=$_POST['udivpl'];
    $pkryawanid=$_POST['ukryid'];
    $pdeptid=$_POST['udptid'];
    $pcabid=$_POST['ucabid'];
    
    
    $ppilformat="1";
    if (($fkaryawan=="0000000143" OR $fkaryawan=="0000000329") AND $ppilihrpt=="excel") {
        $ppilformat="2";
    }
    
    $ppilihrpt="";
    if (isset($_GET['ket'])) $ppilihrpt=$_GET['ket'];
    if ($ppilihrpt=="excel") {
        $ppilformat="3";
    }
    
    
    $_SESSION['BGTUPDTHN']=$ptahunpilih;
    $_SESSION['BGTUPDDVL']=$pdivpilih;
    $_SESSION['BGTUPDKRY']=$pkryawanid;
    $_SESSION['BGTUPDDPT']=$pdeptid;
    $_SESSION['BGTUPDCAB']=$pcabid;
    
    include "../../../config/koneksimysqli.php";
    
    $now=date("mdYhis");
    $tmp01 =" dbtemp.tmppdetinpbgtup01_".$puserid."_$now ";
    $tmp02 =" dbtemp.tmppdetinpbgtup02_".$puserid."_$now ";
    
    
    
    $query = "select bulan, divisi_pengajuan, iddep, karyawanid, divisi, icabangid, icabangid_o, "
            . " postingid, kodeid, coa4, SUM(jumlah) as jumlah "
            . " from dbmaster.t_budget_divisi WHERE year(bulan)='$ptahunpilih' AND divisi_pengajuan='$pdivpilih' AND "
            . " karyawanid='$pkryawanid' AND IFNULL(iddep,'')='$pdeptid'";
    //if (!empty($pcabid)) {
        if ($pdivpilih=="OTC" OR $pdivpilih=="OT" OR $pdivpilih=="CHC") {
            $query .=" AND icabangid_o='$pcabid'";
        }else{
            $query .=" AND icabangid='$pcabid'";
        }
    //}
    $query .=" GROUP BY bulan, divisi_pengajuan, iddep, karyawanid, divisi, icabangid, icabangid_o, postingid, kodeid, coa4";
    $query = "create TEMPORARY table $tmp01 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "UPDATE $tmp01 SET icabangid=icabangid_o WHERE ( divisi_pengajuan in ('OT', 'OTC', 'CHC') OR divisi in ('OT', 'OTC', 'CHC') )"; 
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query ="SELECT DISTINCT divisi_pengajuan, iddep, karyawanid, divisi, icabangid, postingid, kodeid, coa4 FROM $tmp01";
    $query = "create TEMPORARY table $tmp02 ($query)"; 
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $addcolumn="";
    for ($x=1;$x<=12;$x++) {
        $addcolumn .= " ADD B$x DECIMAL(20,2),";
    }
    $addcolumn .= " ADD BTOTAL DECIMAL(20,2)";

    $query = "ALTER TABLE $tmp02 $addcolumn";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $urut=2;
    for ($x=1;$x<=12;$x++) {
        $jml=  strlen($x);
        $awal=$urut-$jml;
        $nbulan=$ptahunpilih."-".str_repeat("0", $awal).$x;
        $nfield="B".$x;

        $query = "UPDATE $tmp02 as a JOIN (SELECT divisi_pengajuan, iddep, karyawanid, divisi, icabangid, postingid, kodeid, coa4, SUM(jumlah) as jumlah "
                . " FROM $tmp01 WHERE DATE_FORMAT(bulan, '%Y-%m')='$nbulan' GROUP BY "
                . " divisi_pengajuan, iddep, karyawanid, divisi, icabangid, postingid, kodeid, coa4) as b "
                . " on IFNULL(a.divisi_pengajuan,'')=IFNULL(b.divisi_pengajuan,'') AND IFNULL(a.iddep,'')=IFNULL(b.iddep,'') AND "
                . " IFNULL(a.karyawanid,'')=IFNULL(b.karyawanid,'') AND IFNULL(a.divisi,'')=IFNULL(b.divisi,'') AND "
                . " IFNULL(a.icabangid,'')=IFNULL(b.icabangid,'') AND IFNULL(a.postingid,'')=IFNULL(b.postingid,'') AND "
                . " IFNULL(a.postingid,'')=IFNULL(b.postingid,'') AND IFNULL(a.kodeid,'')=IFNULL(b.kodeid,'') AND IFNULL(a.coa4,'')=IFNULL(b.coa4,'') SET "
                . " a.$nfield=b.jumlah"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        

    }
            
    $query = "UPDATE $tmp02 as a JOIN (SELECT divisi_pengajuan, iddep, karyawanid, divisi, icabangid, postingid, kodeid, coa4, SUM(jumlah) as jumlah "
            . " FROM $tmp01 WHERE 1=1 GROUP BY "
            . " divisi_pengajuan, iddep, karyawanid, divisi, icabangid, postingid, kodeid, coa4) as b "
            . " on IFNULL(a.divisi_pengajuan,'')=IFNULL(b.divisi_pengajuan,'') AND IFNULL(a.iddep,'')=IFNULL(b.iddep,'') AND "
            . " IFNULL(a.karyawanid,'')=IFNULL(b.karyawanid,'') AND IFNULL(a.divisi,'')=IFNULL(b.divisi,'') AND "
            . " IFNULL(a.icabangid,'')=IFNULL(b.icabangid,'') AND IFNULL(a.postingid,'')=IFNULL(b.postingid,'') AND "
            . " IFNULL(a.postingid,'')=IFNULL(b.postingid,'') AND IFNULL(a.kodeid,'')=IFNULL(b.kodeid,'') AND IFNULL(a.coa4,'')=IFNULL(b.coa4,'') SET "
            . " a.BTOTAL=b.jumlah"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "ALTER TABLE $tmp02 ADD COLUMN transaksi_nama VARCHAR(200), ADD COLUMN nama_dep VARCHAR(200), ADD COLUMN nama_karyawan VARCHAR(200), ADD COLUMN nama_cabang VARCHAR(200), "
            . " ADD COLUMN posting_nama VARCHAR(200), ADD COLUMN kode_nama VARCHAR(200), ADD COLUMN nama_coa4 VARCHAR(200), ADD COLUMN kode_rutin VARCHAR(5)";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "UPDATE $tmp02 as a JOIN hrd.karyawan as b on a.karyawanid=b.karyawanId SET a.nama_karyawan=b.nama";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp02 as a JOIN mkt.icabang as b on a.icabangid=b.icabangid SET a.nama_cabang=b.nama WHERE ( divisi_pengajuan not in ('OT', 'OTC', 'CHC') AND divisi NOT in ('OT', 'OTC', 'CHC') )";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp02 as a JOIN mkt.icabang_o as b on a.icabangid=b.icabangid_o SET a.nama_cabang=b.nama WHERE ( divisi_pengajuan in ('OT', 'OTC', 'CHC') OR divisi in ('OT', 'OTC', 'CHC') )";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp02 as a JOIN hrd.br_kode as b on a.postingid=b.postingid AND a.kodeid=b.kodeid SET a.kode_nama=b.nama WHERE LEFT(a.postingid,3)='01-'";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp02 as a JOIN hrd.brkd_otc as b on a.postingid=b.postingid AND a.kodeid=b.kodeid SET a.kode_nama=b.nama WHERE LEFT(a.postingid,3)='02-'";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp02 as a JOIN dbmaster.t_spg_kode as b on a.postingid=b.postingid AND a.kodeid=b.kodeid SET a.kode_nama=b.nama WHERE LEFT(a.postingid,3)='03-'";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp02 as a JOIN dbmaster.t_brid as b on a.postingid=b.postingid AND a.kodeid=b.nobrid SET a.kode_nama=b.nama, a.kode_rutin=b.kode WHERE LEFT(a.postingid,3)='04-'";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp02 as a JOIN dbmaster.t_kode_posting as b on a.postingid=b.postingid AND a.kodeid=b.kodeid SET a.kode_nama=b.nama WHERE LEFT(a.postingid,3) IN ('06-', '11-')";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp02 as a JOIN hrd.bp_kode as b on a.postingid=b.postingid AND a.kodeid=b.kodeid SET a.kode_nama=b.nama WHERE LEFT(a.postingid,3)='07-'";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp02 as a JOIN dbmaster.t_kode_kascab as b on a.postingid=b.postingid AND a.kodeid=b.kode SET a.kode_nama=b.nama WHERE LEFT(a.postingid,3)='14-'";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp02 as a JOIN dbmaster.t_kode_spd as b on a.postingid=b.postingid AND a.kodeid=b.subkode SET a.kode_nama=b.subnama WHERE LEFT(a.postingid,3)='51-'";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp02 as a JOIN dbmaster.coa_level4 as b on a.coa4=b.COA4 SET a.nama_coa4=b.NAMA4";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "ALTER TABLE $tmp02 ADD COLUMN igroup VARCHAR(10), ADD COLUMN nama_group VARCHAR(200), "
            . " ADD COLUMN all_dep VARCHAR(1) DEFAULT 'N', ADD COLUMN notes_tabel VARCHAR(300), "
            . " ADD COLUMN field_id VARCHAR(50), ADD COLUMN field_nm VARCHAR(200)";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp02 as a JOIN dbmaster.posting_akun as b on a.postingid=b.postingid SET a.posting_nama=b.posting_nama, "
            . " a.igroup=b.igroup, a.notes_tabel=b.notes_tabel, a.field_id=b.field_id, a.field_nm=b.field_nm, "
            . " a.transaksi_nama=b.deskripsi";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp02 as a JOIN (select distinct igroup, posting_nama from dbmaster.posting_akun) as b on a.igroup=b.igroup "
            . " SET a.nama_group=b.posting_nama";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "select distinct notes_tabel, field_id, field_nm FROM $tmp02 WHERE IFNULL(notes_tabel,'')<>'' AND IFNULL(field_id,'')<>'' AND IFNULL(field_nm,'')<>'' ORDER BY notes_tabel";
    $tampil=mysqli_query($cnmy, $query);
    while ($row= mysqli_fetch_array($tampil)) {
        $pnmtabel=$row['notes_tabel'];
        $pfieldid=$row['field_id'];
        $pfieldnm=$row['field_nm'];
        
        $query_upt="UPDATE $tmp02 as a JOIN $pnmtabel as b on a.kodeid=b.$pfieldid SET a.kode_nama=b.$pfieldnm WHERE notes_tabel='$pnmtabel'";
        mysqli_query($cnmy, $query_upt); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
    }
    
    
    $query = "UPDATE $tmp02 SET transaksi_nama=CASE WHEN kode_rutin='1' THEN 'BIAYA - RUTIN' 
                    ELSE CASE WHEN kode_rutin='2' THEN 'BIAYA - LUAR KOTA' 
                    ELSE CASE WHEN kode_rutin='3' THEN 'BIAYA - CASH ADVANCE' 
                    ELSE CASE WHEN kode_rutin='4' THEN 'BIAYA - MUTASI' 
                    ELSE CASE WHEN kode_rutin='5' THEN 'BIAYA - SERVICE KENDARAAN'
                    ELSE 'RUTIN dan LK' 
                    END END END END END "
            . " WHERE LEFT(postingid,3)='04-'";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
?>
<button onclick="topFunction()" id="myBtn" title="Go to top">Top</button>

<div class='col-md-12 col-sm-12 col-xs-12'>
    <div class='x_panel'>
        <form method='POST' action='<?PHP echo "?module='$pmodule'&act=input&idmenu=$pidmenu"; ?>' 
              id='demo_data2' name='form2' data-parsley-validate class='form-horizontal form-label-left'>
            <div class='x_content'>

                <!--<table id='dtablepiluptgt' class='table table-striped table-bordered' width='100%'>-->
                <table id='dtablepiluptgt' class='table table-striped table-bordered' width="100%" border="1px solid black">
                    <thead>
                        <tr>
                            <th width='10px'>No</th>
                            <th align="center" nowrap>Transasksi</th>
                            <th align="center" nowrap>Posting Id</th>
                            <th align="center" nowrap>Posting Nama</th>
                            <th align="center" nowrap>Kode Id</th>
                            <th align="center" nowrap>Kode Nama</th>
                            <th align="center" nowrap>Divisi</th>
                            <th align="center" nowrap>COA</th>
                            <th align="center" nowrap>Nama COA</th>
                            <th align="center" nowrap>JANUARI</th>
                            <th align="center" nowrap>FEBRUARI</th>
                            <th align="center" nowrap>MARET</th>
                            <th align="center" nowrap>APRIL</th>
                            <th align="center" nowrap>MEI</th>
                            <th align="center" nowrap>JUNI</th>
                            <th align="center" nowrap>JULI</th>
                            <th align="center" nowrap>AGUSTUS</th>
                            <th align="center" nowrap>SEPTEMBER</th>
                            <th align="center" nowrap>OKTOBER</th>
                            <th align="center" nowrap>NOVEMBER</th>
                            <th align="center" nowrap>DESEMBER</th>
                            <th align="center" nowrap>TOTAL</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?PHP
                            for ($x=1;$x<=12;$x++) {
                                $pgrandtotal[$x]=0;
                                $pgrandtotalsls[$x]=0;
                            }
                            $no=1;
                            $ptotal=0;
                            $ptotalcoa=0;
                            $query = "select DISTINCT postingid, posting_nama from $tmp02 order by postingid, posting_nama";
                            $tampil1= mysqli_query($cnmy, $query);
                            $ketemu1= mysqli_num_rows($tampil1);
                            if ($ketemu1>0) {
                                while ($row1= mysqli_fetch_array($tampil1)) {
                                    $npostingid=$row1['postingid'];
                                    $nnamaposting=$row1['posting_nama'];

                                    $ptotalcoa=0;
                                    $query = "select * from $tmp02 WHERE postingid='$npostingid' order by transaksi_nama, kodeid, kode_nama, coa4, nama_coa4";
                                    $tampil= mysqli_query($cnmy, $query);
                                    $ketemu= mysqli_num_rows($tampil);
                                    while ($row= mysqli_fetch_array($tampil)) {

                                        $nnmtransasksi=$row['transaksi_nama'];
                                        $nkodeid=$row['kodeid'];
                                        $nkodenm=$row['kode_nama'];
                                        $ndivisi=$row['divisi'];
                                        $ncoa=$row['coa4'];
                                        $nnmcoa=$row['nama_coa4'];


                                        echo "<tr>";
                                        echo "<td nowrap>$no</td>";
                                        echo "<td nowrap>$nnmtransasksi</td>";
                                        echo "<td nowrap>$npostingid</td>";
                                        echo "<td nowrap>$nnamaposting</td>";
                                        echo "<td nowrap>$nkodeid</td>";
                                        echo "<td nowrap>$nkodenm</td>";
                                        echo "<td nowrap>$ndivisi</td>";
                                        echo "<td nowrap>$ncoa</td>";
                                        echo "<td nowrap>$nnmcoa</td>";
                                        
                                        
                                        $ptotaltahund=0;
                                        for ($x=1;$x<=12;$x++) {
                                            $nmcol="B".$x;
                                            $pjml=$row[$nmcol];
                                            if (empty($pjml)) $pjml=0;

                                            $ptotaltahund=(double)$ptotaltahund+(double)$pjml;
                                            $pgrandtotal[$x]=(double)$pgrandtotal[$x]+(double)$pjml;

                                            $pjml=BuatFormatNum($pjml, $ppilformat);
                                            
                                            echo "<td nowrap align='right'>$pjml</td>";
                                        }
                                        
                                        $ptotal=(DOUBLE)$ptotal+(DOUBLE)$ptotaltahund;
                                        
                                        $ptotaltahund=BuatFormatNum($ptotaltahund, $ppilformat);
                                        echo "<td nowrap align='right'>$ptotaltahund</td>";
                                        echo "</tr>";

                                        $no++;
                                    }
                                    
                                    /*
                                    $ptotalcoa=number_format($ptotalcoa,0,",",",");

                                    echo "<tr style='font-weight:bold;'>";
                                    echo "<td nowrap></td>";
                                    echo "<td nowrap></td>";
                                    echo "<td nowrap></td>";
                                    echo "<td nowrap></td>";
                                    echo "<td nowrap></td>";
                                    echo "<td nowrap></td>";
                                    echo "<td nowrap></td>";
                                    echo "<td nowrap></td>";
                                    echo "<td nowrap>TOTAL $npostingid - $nnamaposting : </td>";
                                    echo "<td nowrap></td>";
                                    echo "<td nowrap align='right'>$ptotalcoa</td>";
                                    echo "</tr>";
                                     * 
                                     */

                                }
                                
                                
                                $ptotal=number_format($ptotal,0,",",",");

                                echo "<tr style='font-weight:bold;'>";
                                echo "<td nowrap></td>";
                                echo "<td nowrap></td>";
                                echo "<td nowrap></td>";
                                echo "<td nowrap></td>";
                                echo "<td nowrap></td>";
                                echo "<td nowrap></td>";
                                echo "<td nowrap></td>";
                                echo "<td nowrap></td>";
                                echo "<td nowrap>GRAND TOTAL : </td>";
                                for ($x=1;$x<=12;$x++) {
                                    $ptotalbln=$pgrandtotal[$x];
                                    
                                    $ptotalbln=BuatFormatNum($ptotalbln, $ppilformat);
                                    
                                    echo "<td nowrap align='right'><b>$ptotalbln</b></td>";
                                }
                                echo "<td nowrap align='right'>$ptotal</td>";
                                echo "</tr>";
                                

                            }

                        ?>
                    </tbody>

                </table>


                <script>

                    $(document).ready(function() {
                        var dataTable = $('#dtablepiluptgt').DataTable( {
                            "bPaginate": false,
                            "bLengthChange": false,
                            //"bFilter": true,
                            "bInfo": false,
                            "ordering": false,
                            "order": [[ 0, "desc" ]],
                            "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
                            "displayLength": -1,
                            "columnDefs": [
                                { "visible": false },
                                { "orderable": false, "targets": 0 },
                                { "orderable": false, "targets": 1 },
                                { className: "text-right", "targets": [4] },//right
                                { className: "text-nowrap", "targets": [0, 1,2,3,4] }//nowrap

                            ],
                            "language": {
                                "zeroRecords": "Lihat Page di bawah!!! Jika ada Page, Pilih Page 1...!!! Jika tidak ada Page, maka data KOSONG..."
                            },
                            //"scrollY": 460,
                            "scrollX": true
                        } );
                        $('div.dataTables_filter input', dataTable.table().container()).focus();
                    } );

                </script>


                <style>
                    .divnone {
                        display: none;
                    }
                    #dtablepiluptgt th {
                        font-size: 13px;
                    }
                    #dtablepiluptgt td { 
                        font-size: 11px;
                    }
                    .imgzoom:hover {
                        -ms-transform: scale(3.5); /* IE 9 */
                        -webkit-transform: scale(3.5); /* Safari 3-8 */
                        transform: scale(3.5);

                    }
                </style>

            </div>
        </form>
    </div>
</div>
<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;
<?PHP
hapusdata:
    mysqli_query($cnmy, "drop TEMPORARY table $tmp01");
    mysqli_query($cnmy, "drop TEMPORARY table $tmp02");
    
    mysqli_close($cnmy);
?>


<style>
    #myBtn {
        display: none;
        position: fixed;
        bottom: 20px;
        right: 30px;
        z-index: 99;
        font-size: 18px;
        border: none;
        outline: none;
        background-color: red;
        color: white;
        cursor: pointer;
        padding: 15px;
        border-radius: 4px;
        opacity: 0.5;
    }

    #myBtn:hover {
        background-color: #555;
    }
</style>


<script>
    // When the user scrolls down 20px from the top of the document, show the button
    window.onscroll = function() {scrollFunction()};
    function scrollFunction() {
      if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
        document.getElementById("myBtn").style.display = "block";
      } else {
        document.getElementById("myBtn").style.display = "none";
      }
    }

    // When the user clicks on the button, scroll to the top of the document
    function topFunction() {
      document.body.scrollTop = 0;
      document.documentElement.scrollTop = 0;
    }
</script>