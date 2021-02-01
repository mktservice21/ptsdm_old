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



<form method='POST' action='<?PHP echo "?module='$pmodule'&act=$pact&idmenu=$pidmenu"; ?>' id='d-form2' name='form2' data-parsley-validate class='form-horizontal form-label-left'>
    
    <div class='x_content'>
        <table id='datatable' class='datatable table nowrap table-striped table-bordered' width="100%">
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
                    
                    $padamaping=false;
                    $pcustmaping="";
                    $pdistmaping="";
                    $pcabmaping="";
                    $query = "select ecustid, nama_ecust, nama_dist, nama_ecabang FROM $tmp01 WHERE icabangid='$pidcabang' AND areaid='$pidarea' and icustid='$pidcust' order by nama_ecust, ecustid";
                    $tampil2=mysqli_query($cnms, $query);
                    $ketemu2=mysqli_num_rows($tampil2);
                    if ((int)$ketemu2>0) {
                        while ($row2=mysqli_fetch_array($tampil2)) {
                            if (!empty($row2['nama_ecust'])) {
                                $pcustmaping .="".$row2['nama_ecust']." (".$row2['ecustid'].")<br/>";

                                $pdistmaping .="".$row2['nama_dist']."<br/>";
                                $pcabmaping .="".$row2['nama_ecabang']."<br/>";
                                
                                $padamaping=true;
                            }
                        }
                    }
                    
                    $npidno=$pidcabang."".$pidarea."".$pidcust;
                    $pedit="<a class='btn btn-success btn-xs' href='?module=$pmodule&act=editdata&idmenu=$pidmenu&nmun=$pidmenu&id=$npidno'>Edit</a>";
                    
                    $phapus="<input type='button' value='Hapus Mapping' class='btn btn-danger btn-xs' onClick=\"ProsesDataMapping('hapus', '$npidno')\">";
                    if ($padamaping==false) {
                        $phapus="";
                    }
    
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

        th {
            background: white;
            position: sticky;
            top: 0;
            box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
            z-index:1;
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
    
    function ProsesDataMapping(ket, noid){

        ok_ = 1;
        if (ok_) {
            var r = confirm('Apakah akan melakukan proses hapus mapping customer ...?');
            if (r==true) {

                var myurl = window.location;
                var urlku = new URL(myurl);
                var module = urlku.searchParams.get("module");
                var idmenu = urlku.searchParams.get("idmenu");

                //document.write("You pressed OK!")
                document.getElementById("d-form2").action = "module/map_customersdm/aksi_customersdm.php?module="+module+"&idmenu="+idmenu+"&act=hapusmaping&kethapus="+"&ket="+ket+"&id="+noid;
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