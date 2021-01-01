<?PHP
    session_start();
    
    date_default_timezone_set('Asia/Jakarta');
    //ini_set("memory_limit","10G");
    ini_set('max_execution_time', 0);
    
    $pmodule="";
    if (isset($_GET['module'])) $pmodule=$_GET['module'];
if ($pmodule=="viewdatadoktcab") {
    include "../../config/koneksimysqli.php";
    
    $pidcabang=$_POST['uidcb'];
    
    $query = "select distinct a.dokterid as dokterid, b.nama as nama "
            . " from hrd.br0 as a JOIN hrd.dokter as b "
            . " on a.dokterid=b.dokterid where IFNULL(a.stsbr,'')='KI' AND a.icabangid='$pidcabang' ";
    
    $query .=" order by b.nama";
    $tampil = mysqli_query($cnmy, $query);
    echo "<option value='' selected>--Pilihan--</option>";
    while ($z= mysqli_fetch_array($tampil)) {
        $pniddokt=$z['dokterid'];
        $pdoktnm=$z['nama'];
        echo "<option value='$pniddokt'>$pdoktnm ($pniddokt)</option>";
    }
    mysqli_close($cnmy);
}
else
if ($pmodule=="viewdatakaryawanpilih") {
    include "../../config/koneksimysqli.php";
    
    $pidcabang=$_POST['uidcb'];
    $piddokt=$_POST['uiddokt'];
?>
<link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />
<link href="css/stylenew.css" rel="stylesheet" type="text/css" />

<div class='tbldata'>
    
    <table id='datatable' class='datatable table nowrap table-striped table-bordered' width="100%">
        <thead>
            <tr>
                <th width='2%px' class='divnone'></th>
                <th width='5%px'>No</th>
                <th width='20%'>Karyawan Id</th>
                <th width='50%'>Nama</th>
                <th width='10%px'>&nbsp;</th>
            </tr>
        </thead>
        <tbody class='inputdatauc'>
            <?PHP
            $no=1;
            $query = "select distinct a.mrid as mrid, b.nama as nama "
                    . " from hrd.br0 as a JOIN hrd.karyawan as b "
                    . " on a.mrid=b.karyawanid where IFNULL(a.stsbr,'')='KI' AND a.icabangid='$pidcabang' AND a.dokterid='$piddokt' ";

            $tampil=mysqli_query($cnmy, $query);
            while ($nrow= mysqli_fetch_array($tampil)){
                $pidkry=$nrow['mrid'];
                $pnmkry=$nrow['nama'];
                
                $chkbox = "<span hidden><input type='checkbox' id='chk_kodeid[$pidkry]' name='chk_kodeid[]' value='$pidkry' checked></span>";
                $plihatview = "<a class='btn btn-info btn-xs' href='eksekusi3.php?module=ksmonitoringkiks&act=viewdata&idmenu=396&ket=bukan&nid=$pidkry&did=$piddokt&ic=$pidcabang' target='_blank'>Preview</a>";
                
                
                echo "<tr>";
                echo "<td nowrap>$no $chkbox</td>";
                echo "<td nowrap>$pidkry</td>";
                echo "<td nowrap>$pnmkry</td>";
                echo "<td nowrap>$plihatview</td>";
                echo "</tr>";
                
                $no++;
                
            }
            ?>
        </tbody>
    </table>
    
</div>

<?PHP  
    mysqli_close($cnmy);
}
else
if ($pmodule=="xxxxx") {
    

    
}
?>