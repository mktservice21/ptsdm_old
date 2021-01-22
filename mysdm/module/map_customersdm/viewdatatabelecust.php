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
    
    $pidcabang=$_POST['ucabang'];
    $pidarea=$_POST['uarea'];
    $pnmfilter=$_POST['unamafilter'];
    
    
    $_SESSION['MAPCUSTIDCAB']=$pidcabang;
    $_SESSION['MAPCUSTIDARE']=$pidarea;
    $_SESSION['MAPCUSTFILTE']=$pnmfilter;
    
    $pmodule=$_GET['module'];
    $pact=$_GET['act'];
    $pidmenu=$_GET['idmenu'];

    echo "<input type='hidden' name='cb_cabang' id='cb_cabang' value='$pidcabang'>";
    echo "<input type='hidden' name='cb_area' id='cb_area' value='$pidarea'>";
    
    include "../../config/koneksimysqli_ms.php";
    
    $now=date("mdYhis");
    $tmp01 =" dbtemp.tmpcustsdm01_".$puserid."_$now ";
    $tmp02 =" dbtemp.tmpcustsdm02_".$puserid."_$now ";
    
    $sql = "select a.icabangid, a.icustid, a.areaid, a.nama, a.alamat1, a.alamat2, a.kota, a.kodepos, a.telp, a.isektorid, a.aktif, a.dispen, a.user1, a.grp,
        b.nama as nama_sektor, c.nama as nama_cabang, d.nama as nama_area, e.nama as nama_ecust, e.ecustid,
        f.nama as nama_dist, e.DistId, e.CabangId, g.nama as nama_ecabang 
        from MKT.icust as a LEFT JOIN MKT.isektor as b on a.iSektorId=b.iSektorId
        JOIN MKT.icabang as c on a.iCabangId=c.iCabangId
        JOIN MKT.iarea as d on a.iCabangId=d.iCabangId and a.areaId=d.areaId
        LEFT JOIN MKT.ecust as e on a.iCabangId=e.iCabangId and a.areaId=e.areaId and a.iCustId=e.iCustId 
        LEFT JOIN MKT.distrib0 as f on e.DistId=f.distid
        LEFT JOIN MKT.ecabang as g on e.DistId=g.distId and e.cabangid=g.ecabangid ";
    $sql.=" WHERE a.icabangid='$pidcabang' AND ifnull(a.aktif,'')<>'N' ";
    if (!empty($pidarea)) $sql.=" AND a.areaId='$pidarea' ";
    if (!empty($pnmfilter)) $sql.=" AND a.nama like '%$pnmfilter%' ";
    
    $query = "create TEMPORARY table $tmp01 ($sql)"; 
    mysqli_query($cnms, $query);
    $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

?>

<script>
    $(document).ready(function() {
        var aksi = "module/map_customersdm/aksi_datacusstomer.php";
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var idmenu = urlku.searchParams.get("idmenu");
        var nmun = urlku.searchParams.get("nmun");
        var ecabang=document.getElementById('cb_cabang').value;
        var earea=document.getElementById('cb_area').value;
        var idisply="10";
        if (earea!="") {
            idisply="10";
        }
        var dataTable = $('#datatablecust').DataTable( {
            "processing": true,
            //"serverSide": true,
            //"stateSave": true,
            //"order": [[ 2, "asc" ], [ 3, "asc" ], [ 4, "asc" ]],
            "lengthMenu": [[10, 50, 100, 10000000], [10, 50, 100, "All"]],
            "displayLength": idisply,
            "columnDefs": [
                { "visible": false },
                { "orderable": true, "targets": 0 },
                { "orderable": true, "targets": 1 },
                { "orderable": true, "targets": 2 },
                { "orderable": true, "targets": 3 },
                { "orderable": true, "targets": 4 },
                //{ className: "text-right", "targets": [6] },//right
                { className: "text-nowrap", "targets": [0, 1, 2, 3, 4,5,6,7,8,9] }//nowrap

            ],
            "language": {
                "zeroRecords": "Lihat Page di bawah!!! Jika ada Page, Pilih Page 1...!!! Jika tidak ada Page, maka data KOSONG..."
            },
            "scrollY": 490,
            "scrollX": true/*,

            "ajax":{
                url :"module/map_customersdm/mydatacust.php?module="+module+"&idmenu="+idmenu+"&nmun="+nmun+"&aksi="+aksi+"&ucabang="+ecabang+"&uarea="+earea, // json datasource
                type: "post",  // method  , by default get
                data:"ucabang="+ecabang+"&uarea="+earea,
                error: function(){  // error handling
                    $(".data-grid-error").html("");
                    $("#datatable").append('<tbody class="data-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                    $("#data-grid_processing").css("display","none");

                }
            }*/
        } );
        $('div.dataTables_filter input', dataTable.table().container()).focus();
    } );
</script>



<form method='POST' action='<?PHP echo "?module='$pmodule'&act=$pact&idmenu=$pidmenu"; ?>' id='d-form2' name='form2' data-parsley-validate class='form-horizontal form-label-left'>
    
    <div class='x_content'>
        <table id='datatablecust' class='table table-striped table-bordered' width='100%'>
            <thead>
                <tr>
                    <th width='50px'>No</th>
                    <th width='50px'>&nbsp;</th>
                    <th width='100px'>Nama Customer</th>
                    <th width='100px'>Alamat 1</th>
                    <th width='100px'>Alamat 2</th>
                    <th width='50px'>Kota</th>
                    <th width='80px'>Nama Sektor</th>
                    <th width='100px'>Sudah di-map ke :</th>
                    <th width='100px'>&nbsp;</th>
                    <th width='100px'>&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                <?PHP
                $no=1;
                $query = "select distinct icabangid, nama_cabang, areaid, nama_area, icustid, nama, "
                        . " isektorid, nama_sektor, alamat1, alamat2, kodepos, telp, kota from $tmp01 order by nama, icustid";
                $tampil=mysqli_query($cnms, $query);
                while ($row=mysqli_fetch_array($tampil)) {
                    $pidcabang=$row['icabangid'];
                    $pnmcabang=$row['nama_cabang'];
                    $pidarea=$row['areaid'];
                    $pnmarea=$row['nama_area'];
                    $pidcust=$row['icustid'];
                    $pnmcust=$row['nama'];
                    $pisektorid=$row['isektorid'];
                    $pnmsektor=$row['nama_sektor'];
                    $palamat1=$row['alamat1'];
                    $palamat2=$row['alamat2'];
                    $pkdpost=$row['kodepos'];
                    $ptelp=$row['telp'];
                    $pkota=$row['kota'];
                    
                    $pidcusttomer=(INT)$pidcust;
                    
                    $pcustmaping="";
                    $pdistmaping="";
                    $pcabmaping="";
                    $query = "select ecustid, nama_ecust, nama_dist, nama_ecabang FROM $tmp01 WHERE icabangid='$pidcabang' AND areaid='$pidarea' and icustid='$pidcust' order by nama_ecust, ecustid";
                    $tampil2=mysqli_query($cnms, $query);
                    $ketemu2=mysqli_num_rows($tampil2);
                    if ((int)$ketemu2>0) {
                        while ($row2=mysqli_fetch_array($tampil2)) {
                            if (!empty($row2['nama_ecust'])) {
                                $pcustmaping .="".$row2['nama_ecust']." (".(INT)$row2['ecustid'].")<br/>";

                                $pdistmaping .="".$row2['nama_dist']."<br/>";
                                $pcabmaping .="".$row2['nama_ecabang']."<br/>";
                            }
                        }
                    }
                    
                    $npidno=$pidcabang."".$pidarea."".$pidcust;
                    $pedit="<a class='btn btn-success btn-xs' href='?module=$pmodule&act=editdata&idmenu=$pidmenu&nmun=$pidmenu&id=$npidno'>Edit</a>";
                    $phapus="<input type='button' value='Hapus' class='btn btn-danger btn-xs' onClick=\"ProsesData('hapus', '$npidno')\">";
    
    
                    echo "<tr>";
                    echo "<td nowrap>$no</td>";
                    echo "<td nowrap>$pedit &nbsp; &nbsp; $phapus</td>";
                    echo "<td nowrap>$pnmcust ($pidcusttomer)</td>";
                    echo "<td nowrap>$palamat1</td>";
                    echo "<td nowrap>$palamat2</td>";
                    echo "<td nowrap>$pkota</td>";
                    echo "<td nowrap>$pnmsektor</td>";
                    echo "<td nowrap>$pcustmaping</td>";
                    echo "<td nowrap>$pdistmaping</td>";
                    echo "<td nowrap>$pcabmaping</td>";
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
    #datatablecust th {
        font-size: 13px;
    }
    #datatablecust td { 
        font-size: 11px;
    }
</style>

<script>
    function ProsesData(ket, noid){

        ok_ = 1;
        if (ok_) {
            var r = confirm('Apakah akan melakukan proses '+ket+' ...?');
            if (r==true) {

                var myurl = window.location;
                var urlku = new URL(myurl);
                var module = urlku.searchParams.get("module");
                var idmenu = urlku.searchParams.get("idmenu");

                //document.write("You pressed OK!")
                document.getElementById("d-form2").action = "module/map_customersdm/aksi_customersdm.php?module="+module+"&idmenu="+idmenu+"&act=hapus&kethapus="+"&ket="+ket+"&id="+noid;
                document.getElementById("d-form2").submit();
                return 1;
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }



    }
</script>

<?PHP
hapusdata:
    mysqli_query($cnms, "drop TEMPORARY table $tmp01");
    mysqli_query($cnms, "drop TEMPORARY table $tmp02");
    
    mysqli_close($cnms);
?>