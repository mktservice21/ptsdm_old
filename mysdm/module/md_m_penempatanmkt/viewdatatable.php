<?PHP
    session_start();
    include "../../config/koneksimysqli_ms.php";
    $cnmy=$cnms;
    
    $pbln=$_POST['ubln'];
    $pregion=$_POST['uregion'];
    $pcanang=$_POST['ucabang'];
    $parea=$_POST['uarea'];
    
    $_SESSION['MKTTMPPERIODE']=$pbln;
    $_SESSION['MKTTMPREG']=$pregion;
    $_SESSION['MKTTMPCAB']=$pcanang;
    $_SESSION['MKTTMPARE']=$parea;
    
    $pidmenu=$_GET['idmenu'];
    $pmodule=$_GET['module'];
    
    $pperiode= date("Y-m", strtotime($pbln));
    
    $filtercab="";
    if (!empty($pcanang)) $filtercab=" AND icabangid='$pcanang' ";
    $filterarea="";
    if (!empty($parea)) $filterarea=" AND areaid='$parea' ";
    
    $userid=$_SESSION['USERID'];
    $now=date("mdYhis");
    $tmp01 =" dbtemp.VIEWPENMKT01_".$userid."_$now ";
    $tmp02 =" dbtemp.VIEWPENMKT02_".$userid."_$now ";
    $tmp03 =" dbtemp.VIEWPENMKT03_".$userid."_$now ";
    $tmp04 =" dbtemp.VIEWPENMKT04_".$userid."_$now ";
    $tmp05 =" dbtemp.VIEWPENMKT05_".$userid."_$now ";
    
    $query = "select * from ms.penempatan_marketing WHERE DATE_FORMAT(bulan,'%Y-%m') = '$pperiode' AND region='$pregion' $filtercab $filterarea";
    $query = "create temporary table $tmp01 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "select * from sls.icabang";
    $query = "create temporary table $tmp02 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "select * from sls.iarea";
    $query = "create temporary table $tmp03 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "select karyawanid, nama from hrd.karyawan";
    $query = "create  table $tmp04 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "SELECT a.id, a.bulan, a.region, a.icabangid, b.nama nama_cabang, a.areaid, c.nama nama_area, a.divprodid, 
        a.gsm, d.nama nama_gsm, a.sm, a.dm, h.nama nama_dm, e.nama nama_sm, a.am, f.nama nama_am, a.mr, g.nama nama_mr 
        from $tmp01 a 
        LEFT JOIN $tmp02 b on a.icabangid=b.iCabangId 
        LEFT JOIN $tmp03 c on a.icabangid=c.iCabangId AND a.areaid=c.areaId 
        LEFT JOIN $tmp04 d on a.gsm=d.karyawanId 
        LEFT JOIN $tmp04 e on a.sm=e.karyawanId 
        LEFT JOIN $tmp04 f on a.am=f.karyawanId 
        LEFT JOIN $tmp04 g on a.mr=g.karyawanId 
        LEFT JOIN $tmp04 h on a.dm=h.karyawanId ";
    $query = "create temporary table $tmp05 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
?>


<div class='x_content'>


    <table id='datatablepmkt' class='table table-striped table-bordered' width="100%">
        <thead>
            <tr>
                <th width='4px'>No</th>
                <th width='100px'>SM</th>
                <th width='80px'>DM</th><th width='80px'>AM</th><th width='10px'>Cabang</th><th width='2px'>Area</th>
                <th width='5px'>Divisi</th><th width='80px'>MR</th><th width='5px'></th>
            </tr>
        </thead>
        <tbody>
            <?PHP
            $no=1;
            $query = "select * from $tmp05 ORDER BY nama_sm, nama_dm, nama_am, nama_cabang, nama_area, divprodid, nama_mr";
            $tampil = mysqli_query($cnmy, $query);
            while( $row=mysqli_fetch_array($tampil) ) {
                $pidno=$row['id'];
                $pregion=$row['region'];
                $psm=$row['nama_sm'];
                
                $piddm=$row['dm'];
                $pdm=$row['nama_dm'];
                if ($row['dm']=="000")$pdm="VACANT";
                
                $pidam=$row['am'];
                $pam=$row['nama_am'];
                if ($row['am']=="000")$pam="VACANT";
                
                $pidmr=$row['mr'];
                $pmr=$row['nama_mr'];
                if ($row['mr']=="000")$pmr="VACANT";
                
                $pidcabang=$row['icabangid'];
                $pnmcabang=$row['nama_cabang'];
                $pidarea=$row['areaid'];
                $pnmarea=$row['nama_area'];
                $pdivisi=$row['divprodid'];
                
                
                $peditmr = "<a class='btn btn-info btn-xs' href='?module=$_GET[module]&act=editdatamr&idmenu=$_GET[idmenu]&nmun=$_GET[idmenu]&id=$pidno&idkry=$pidmr&idcab=$pidcabang&idarea=$pidarea'>Edit MR</a>";
                $peditam = "<a class='btn btn-warning btn-xs' href='?module=$_GET[module]&act=editdataam&idmenu=$_GET[idmenu]&nmun=$_GET[idmenu]&id=$pidno&idkry=$pidam&idcab=$pidcabang&idarea=$pidarea'>Edit AM (Per Area)</a>";
                $peditdm = "<a class='btn btn-success btn-xs' href='?module=$_GET[module]&act=editdatadm&idmenu=$_GET[idmenu]&nmun=$_GET[idmenu]&id=$pidno&idkry=$piddm&idcab=$pidcabang&idarea=$pidarea'>Edit DM (Per Area)</a>";
                
                echo "<tr>";
                echo "<td>$no</td>";
                //echo "<td>$pregion</td>";
                echo "<td>$psm</td>";
                echo "<td>$pdm</td>";
                echo "<td>$pam</td>";
                echo "<td>$pnmcabang</td>";
                echo "<td>$pnmarea</td>";
                echo "<td>$pdivisi</td>";
                echo "<td>$pmr</td>";
                echo "<td nowrap>$peditmr $peditam </td>";//$peditdm
                echo "</tr>";
                
                $no++;
            }
            ?>
        </tbody>
    </table>
</div>

<?PHP
hapusdata:
    mysqli_query($cnmy, "drop TEMPORARY table $tmp01");
    mysqli_query($cnmy, "drop TEMPORARY table $tmp02");
    mysqli_query($cnmy, "drop TEMPORARY table $tmp03");
    mysqli_query($cnmy, "drop table $tmp04");
    mysqli_query($cnmy, "drop TEMPORARY table $tmp05");
?>

<script>

$(document).ready(function() {
    var table = $('#datatablepmkt').DataTable({
        fixedHeader: true,
        "ordering": true,
        "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
        "displayLength": -1,
        "order": [[ 0, "asc" ]],
        bFilter: true, bInfo: true, "bLengthChange": true, "bLengthChange": true,
        "bPaginate": true
    } );

} );

</script>

<style>
    .divnone {
        display: none;
    }
    #datatablepmkt th {
        font-size: 12px;
    }
    #datatablepmkt td { 
        font-size: 11px;
    }
</style>