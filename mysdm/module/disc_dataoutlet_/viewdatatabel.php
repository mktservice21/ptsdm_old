<?PHP
    ini_set("memory_limit","500M");
    ini_set('max_execution_time', 0);
    
    session_start();
    
    $puserid="";
    if (isset($_SESSION['USERID'])) $puserid=$_SESSION['USERID'];

    if (empty($puserid)) {
        echo "ANDA HARUS LOGIN ULANG...";
        exit;
    }
    
    include "../../config/koneksimysqli.php";
    
    $now=date("mdYhis");
    $tmp01 =" dbtemp.tmpotldpldisc01_".$puserid."_$now ";
    $tmp02 =" dbtemp.tmpotldpldisc02_".$puserid."_$now ";
    
    
    $pidcab=$_POST['ucabid'];
    
    $_SESSION['DISCDPLCBOTL']=$pidcab;
    
    $pmodule=$_GET['module'];
    $pact=$_GET['act'];
    $pidmenu=$_GET['idmenu'];
    
    $fkaryawan=$_SESSION['IDCARD'];
    $fdivisi=$_SESSION['DIVISI'];
    $fgroupidcard=$_SESSION['GROUP'];
    $fjbtid=$_SESSION['JABATANID'];
    
    $filtercabang="";
    if ($fjbtid=="38") {
        $query = "select distinct icabangid as icabangid from hrd.rsm_auth WHERE karyawanid='$fkaryawan'";
        $tampil=mysqli_query($cnmy, $query);
        while ($ro=mysqli_fetch_array($tampil)) {
            $cabidp=$ro['icabangid'];

            $filtercabang .="'".$cabidp."',";
        }

        if (!empty($filtercabang)) {
            $filtercabang="(".substr($filtercabang, 0, -1).")";    
        }else{
            $filtercabang="('icabangidx')";
        }
    }
    
    $sql = "SELECT a.idoutlet_dpl, a.nodpl, a.isektorid, b.nama as nama_sektor, a.nama_outlet, 
        a.alamat, a.provinsi, a.kota, a.kodepos, a.telp, a.kontakperson,
        a.notes, a.userid, a.aktif, a.discount, a.bonus ";
    $sql.=" FROM dbdiscount.t_outlet_dpl as a ";
    $sql.=" LEFT JOIN MKT.isektor as b on a.isektorid=b.iSektorId ";
    $sql.=" WHERE 1=1 ";

    if (!empty($filtercabang)) {
        $sql.=" AND a.icabangid IN $filtercabang ";
    }

    if (!empty($pidcab)) {
        $sql.=" AND a.icabangid='$pidcab' ";
    }
    
    $query = "create TEMPORARY table $tmp01 ($sql)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "select a.idoutlet_dpl, a.distid, a.disc, a.keterangan "
            . " from dbdiscount.t_outlet_dpl_d as a "
            . " JOIN $tmp01 as b on a.idoutlet_dpl=b.idoutlet_dpl";
    $query = "create TEMPORARY table $tmp02 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "alter table $tmp02 ADD COLUMN nama_dist Varchar(100), ADD COLUMN initial Varchar(20), ADD COLUMN urutan INT(4)";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp02 as a JOIN dbdiscount.t_dist_pilih as b on a.distid=b.distid SET a.urutan=b.urutan";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp02 as a JOIN MKT.distrib0 as b on a.distid=b.distid SET a.nama_dist=b.nama, a.initial=b.initial";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp02 SET nama_dist='SDM', initial='SDM', urutan='99' WHERE distid='0000000000'";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
?>

<form method='POST' action='<?PHP echo "?module='$pmodule'&act=input&idmenu=$pidmenu"; ?>' 
      id='demo_data2' name='form2' data-parsley-validate class='form-horizontal form-label-left'>
    <div class='x_content'>
        <table id='datatabledcds' class='table table-striped table-bordered' width='100%'>
            <thead>
                <tr>
                    <th width='10px'>No</th>
                    <th width='10px'></th>
                    <th width='10px'>No. DPL</th>
                    <th width='20px'>Sektor</th>
                    <th width='50px'>Nama</th>
                    <th width='60px'>Alamat</th>
                    <th width='40px'>Kota</th>
                    <th width='20px'>Telp.</th>
                    <th width='10px'>Discount</th>
                    <th width='10px'>Bonus</th>
                    <th width='30px'>Keterangan</th>
                    <th width='5px' class="divnone"></th>
                </tr> 
            </thead>
            <tbody>
                <?PHP
                $no=1;
                $query = "select * from $tmp01 order by nama_outlet";
                $tampil= mysqli_query($cnmy, $query);
                while ($row= mysqli_fetch_array($tampil)) {
                    $pidoutelt = $row["idoutlet_dpl"];
                    $pnmoutelt = $row["nama_outlet"];
                    $pidsektor = $row["isektorid"];
                    $pnmsektor = $row["nama_sektor"];
                    $palamat = $row["alamat"];
                    $pprovinsi = $row["provinsi"];
                    $pkota = $row["kota"];
                    $pkodepos = $row["kodepos"];
                    $ptelp = $row["telp"];
                    $ppersonkontak = $row["kontakperson"];
                    $pnotes = $row["notes"];
                    $paktif = $row["aktif"];

                    $pnodpl= $row["nodpl"];
                    $pdiscount= $row["discount"];
                    $pbonus= $row["bonus"];

                    $pedit = "<a class='btn btn-success btn-xs' href='?module=$pmodule&act=editdata&idmenu=$pidmenu&nmun=$pidmenu&id=$pidoutelt'>Edit</a>";
                    
                    echo "<tr>";
                    echo "<td nowrap>$no</td>";
                    echo "<td nowrap>$pedit</td>";
                    echo "<td nowrap>$pnodpl</td>";
                    echo "<td nowrap>$pnmsektor</td>";
                    echo "<td nowrap>$pnmoutelt</td>";
                    echo "<td nowrap>$palamat</td>";
                    echo "<td nowrap>$pkota</td>";
                    echo "<td nowrap>$ptelp</td>";
                    echo "<td nowrap align='right'></td>";
                    echo "<td nowrap align='right'>$pbonus</td>";
                    echo "<td >$pnotes</td>";
                    echo "<td class='divnone'></td>";
                    echo "</tr>";
                    
                    $no++;
                    
                    $query = "select * from $tmp02 WHERE idoutlet_dpl='$pidoutelt' order by urutan";
                    $tampil2= mysqli_query($cnmy, $query);
                    while ($row2= mysqli_fetch_array($tampil2)) {
                        $pnmdist = $row2["nama_dist"];
                        $pnminitialdist = $row2["initial"];
                        $pdisc = $row2["disc"];
                        
                        echo "<tr>";
                        echo "<td nowrap></td>";
                        echo "<td nowrap></td>";
                        echo "<td nowrap></td>";
                        echo "<td nowrap></td>";
                        echo "<td nowrap>$pnminitialdist</td>";
                        echo "<td nowrap></td>";
                        echo "<td nowrap></td>";
                        echo "<td nowrap></td>";
                        echo "<td nowrap align='right'>$pdisc</td>";
                        echo "<td nowrap align='right'></td>";
                        echo "<td ></td>";
                        echo "<td class='divnone'>$pnmsektor $pnodpl $pnmoutelt $palamat $pkota $ptelp $pnotes</td>";
                        echo "</tr>";
                        
                    }
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
    #datatabledcds th {
        font-size: 12px;
    }
    #datatabledcds td { 
        font-size: 11px;
    }
</style>

<script>
    $(document).ready(function() {
        var dataTable = $('#datatabledcds').DataTable( {
            //"stateSave": true,
            fixedHeader: true,
            "ordering": false,
            "processing": true,
            "order": [[ 0, "asc" ], [ 1, "desc" ], [ 2, "desc" ], [ 3, "desc" ]],
            "lengthMenu": [[14, 70, 140, -1], [14, 70, 140, "All"]],
            "displayLength": 14,
            "columnDefs": [
                { "visible": false },
                { "orderable": false, "targets": 0 },
                { "orderable": false, "targets": 1 },
                { "orderable": true, "targets": 2 },
                { "orderable": true, "targets": 4 },
                { className: "text-right", "targets": [8, 9] },//right
                { className: "text-nowrap", "targets": [0, 1, 2, 3, 4, 5, 6, 7,8,9] }//nowrap

            ],
            "language": {
                "zeroRecords": "Lihat Page di bawah!!! Jika ada Page, Pilih Page 1...!!! Jika tidak ada Page, maka data KOSONG..."
            }
        } );
        $('div.dataTables_filter input', dataTable.table().container()).focus();
    } );
    
</script>

<?PHP
hapusdata:
    mysqli_query($cnmy, "drop TEMPORARY table $tmp01");
    mysqli_query($cnmy, "drop TEMPORARY table $tmp02");
    
    mysqli_close($cnmy);
?>