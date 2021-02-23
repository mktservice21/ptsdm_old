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
    $tmp01 =" dbtemp.tmpkslstdoktmr01_".$puserid."_$now ";
    $tmp02 =" dbtemp.tmpkslstdoktmr02_".$puserid."_$now ";
    $tmp03 =" dbtemp.tmpkslstdoktmr03_".$puserid."_$now ";
    $tmp04 =" dbtemp.tmpkslstdoktmr04_".$puserid."_$now ";
    
    
    $pidkaryawan=$_POST['uidkry'];
    $pidcab=$_POST['uidcab'];
    $pstsdr=$_POST['ustsdr'];
    
    $_SESSION['LHTKSDAPT']="";

    $pmodule=$_GET['module'];
    $pact=$_GET['act'];
    $pidmenu=$_GET['idmenu'];
    
    $fkaryawan=$_SESSION['IDCARD'];
    $fdivisi=$_SESSION['DIVISI'];
    $fgroupidcard=$_SESSION['GROUP'];
    $fjbtid=$_SESSION['JABATANID'];
    
    $query = "select nama from hrd.karyawan where karyawanid='$pidkaryawan'";
    $tampilk=mysqli_query($cnmy, $query);
    $rowk=mysqli_fetch_array($tampilk);
    $pnamakarywanpl=$rowk['nama'];
    
    $pfilterkry="";
    if (!empty($pidkaryawan)) $pfilterkry=" AND karyawanid='$pidkaryawan' ";

    $query = "create TEMPORARY table $tmp04 (icabangid varchar(10), karyawanid varchar(10))";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    $query = "insert into $tmp04 (icabangid, karyawanid) select distinct icabangid, karyawanid from mkt.imr0 where 
        icabangid='$pidcab' AND IFNULL(karyawanid,'')<>'' $pfilterkry"; //AND karyawanid='0000000896'
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    $query = "insert into $tmp04 (icabangid, karyawanid) select distinct icabangid, karyawanid from mkt.ispv0 where 
        icabangid='$pidcab' AND IFNULL(karyawanid,'')<>'' $pfilterkry";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    $query = "insert into $tmp04 (icabangid, karyawanid) select distinct icabangid, karyawanid from mkt.idm0 where 
        icabangid='$pidcab' AND IFNULL(karyawanid,'')<>'' $pfilterkry";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    $query = "select distinct b.icabangid, a.srid as karyawanid, a.dokterid from hrd.ks1 as a JOIN
        (select distinct karyawanid, icabangid from $tmp04) as b on a.srid=b.karyawanid";
    $query = "create TEMPORARY table $tmp02 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    $query = "select DISTINCT a.dokterid as dokterid, a.nama as nama_dokter, 
        a.alamat1, a.alamat2, 
        b.karyawanid, c.nama as nama_karyawan, 
        b.icabangid, d.nama as nama_cabang 
        FROM hrd.dokter as a JOIN $tmp02 as b on a.dokterid=b.dokterid 
        JOIN hrd.karyawan as c on b.karyawanid=c.karyawanid 
        JOIN MKT.icabang as d on b.icabangid=d.icabangid";
    $query = "create TEMPORARY table $tmp01 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

/*
    goto hapusdata;


    if (!empty($pstsdr)) {
        $query = "select distinct a.dokterid from hrd.ks1 as a WHERE a.srid='$pidkaryawan'";
        $query = "create TEMPORARY table $tmp02 ($query)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    }
    
    $query = "select DISTINCT a.dokterid as dokterid, a.nama as nama_dokter, 
        a.alamat1, a.alamat2, 
        b.karyawanid as karyawanid, c.nama as nama_karyawan, 
        b.iCabangId as icabangid, d.nama as nama_cabang, b.areaId as areaid, e.nama as nama_area 
        from hrd.dokter as a LEFT JOIN hrd.mr_dokt as b on a.dokterid=b.dokterid 
        left join hrd.karyawan as c on b.karyawanId=c.karyawanId 
        left join MKT.icabang as d on b.iCabangId=d.iCabangId 
        LEFT JOIN MKT.iarea as e on b.iCabangId=e.iCabangId and b.areaId=e.areaId ";
    if (!empty($pstsdr)) {
        $query .=" JOIN $tmp02 as f on a.dokterid=f.dokterid";
    }
    $query .=" WHERE 1=1 ";
    $query .=" AND b.karyawanid='$pidkaryawan' ";
    $query = "create TEMPORARY table $tmp01 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

*/
    
    $aksi="eksekusi3.php";
?>


    <div class='x_content'>
        <b>List Data Dokter MR : <?PHP echo "$pnamakarywanpl"; ?></b>
        <hr/>
        <table id='datatabledrlstmr' class='table table-striped table-bordered' width='100%'>
            <thead>
                <tr>
                    <th width='10px'>No</th>
                    <th width='10px'>DokterId</th>
                    <th width='20px'>Nama Dokter</th>
                    <th width='50px'>Alamat</th>
                    <th width='50px'>&nbsp;</th>
                </tr> 
            </thead>
            <tbody>
                <?PHP
                $no=1;
                $query = "select * from $tmp01 order by nama_dokter";
                $tampil= mysqli_query($cnmy, $query);
                while ($row= mysqli_fetch_array($tampil)) {
                    $piddokt = $row["dokterid"];
                    $pnmdokt = $row["nama_dokter"];
                    $pidkry = $row["karyawanid"];
                    $pnmkry = $row["nama_karyawan"];
                    $pnmcab = $row["nama_cabang"];
                    //$pnmarea = $row["nama_area"];
                    $paalamat = $row["alamat1"];
                    
                    //$plihatks="<a class='btn btn-info btn-xs' href='eksekusi3.php?module=lihatdataksusr&ket=bukan&iid=$pidkry&ind=$piddokt' target='_blank'>Preview KS</a>";
                    
                    echo "<form method='POST' action='$aksi?module=$pmodule&act=input&idmenu=$pidmenu' "
                            . " id='form_data$no' name='form$no' data-parsley-validate "
                            . " target='_blank'>";
                    
                    $plihatks="<button type='button' class='btn btn-info btn-xs' onclick=\"disp_confirm_ks('', 'form_data$no', '$piddokt', '')\">Preview KS</button>";
                    
                    
                    echo "<tr>";
                    echo "<td nowrap>$no</td>";
                    echo "<td nowrap>$piddokt</td>";
                    echo "<td nowrap>$pnmdokt</td>";
                    echo "<td >$paalamat</td>";
                    echo "<td nowrap>$plihatks</td>";
                    echo "</tr>";
                    
                    echo "</form>";
                    
                    $no++;
                }
                ?>
            </tbody>
        </table>
    </div>


<style>
    .divnone {
        display: none;
    }
    #datatabledrlstmr th {
        font-size: 12px;
    }
    #datatabledrlstmr td { 
        font-size: 11px;
    }
</style>

<script>
    $(document).ready(function() {
        var dataTable = $('#datatabledrlstmr').DataTable( {
            //"stateSave": true,
            fixedHeader: true,
            "ordering": false,
            "processing": true,
            //"order": [[ 0, "asc" ]],
            "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
            "displayLength": 10,
            "columnDefs": [
                { "visible": false },
                { "orderable": false, "targets": 0 },
                { "orderable": true, "targets": 1 },
                { "orderable": true, "targets": 2 },
                { "orderable": false, "targets": 3 },
                //{ className: "text-right", "targets": [8, 9] },//right
                { className: "text-nowrap", "targets": [0, 1, 2,4] }//nowrap

            ],
            "language": {
                "zeroRecords": "Lihat Page di bawah!!! Jika ada Page, Pilih Page 1...!!! Jika tidak ada Page, maka data KOSONG..."
            }
        } );
        $('div.dataTables_filter input', dataTable.table().container()).focus();
    } );
    
    
    function disp_confirm_ks(pText, pnmform, eiddok, enmdokt)  {
        var eidkry =document.getElementById('cb_karyawan').value;
        
        if (eidkry=="") {
            alert("karyawan harus diisi...!!!");
            return false;
        }
        
        if (eiddok=="") {
            alert("dokter harus diisi...!!!");
            return false;
        }
        
        document.getElementById('e_iddokt').value=eiddok;
        document.getElementById('e_nmdokt').value=enmdokt;
        
        var eiddok2 =document.getElementById('e_iddokt').value;
        if (eiddok2=="") {
            alert("dokter harus diisi...!!!");
            return false;
        }
        
        //alert(eiddok); return false;
        
        if (pText == "excel") {
            document.getElementById('data_form01').action = "<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]&ket=excel"; ?>";
            document.getElementById('data_form01').submit();
            return 1;
        }else{
            document.getElementById('data_form01').action = "<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]&ket=bukan"; ?>";
            document.getElementById('data_form01').submit();
            return 1;
        }
    }
</script>

<?PHP
hapusdata:
    mysqli_query($cnmy, "drop TEMPORARY table $tmp01");
    mysqli_query($cnmy, "drop TEMPORARY table $tmp02");
    mysqli_query($cnmy, "drop TEMPORARY table $tmp03");
    
    mysqli_close($cnmy);
?>