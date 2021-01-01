<?PHP
    include "config/koneksimysqli.php";
    include "config/fungsi_sql.php";
    $edit = mysqli_query($cnmy, "SELECT * FROM dbbudget.v_br WHERE NOID='$_GET[id]'");
    $r    = mysqli_fetch_array($edit);
    $noid=$r['NOID'];
    $rp=$r['JUMLAH'];
    $tglinput = date('d F Y', strtotime($r['TGL']));
    $tglperlu = date('d F Y', strtotime($r['TGL_PERLU']));
    $idajukan=$r['KARYAWANID']; 
    $nmajukan=$r['nama']; 
    $idcab=$r['ICABANGID']; 
    $nmcab=$r['nama_cabang'];
    $ccy=$r['ccyId'];
    $jumlah=$r['JUMLAH'];
    $aktivitas=$r['AKTIVITAS1'];
    $divprodid=$r['divprodid'];
?>
<style>
table {
    border-collapse: collapse;
    width: 100%;
}

th, td {
    text-align: left;
    padding: 8px;
}
td.angka {
    text-align: right;
    padding: 8px;
}

tr:nth-child(even){background-color: #f2f2f2}

th {
    background-color: #4CAF50;
    color: white;
}
</style>
<h1>Detail Budget Request</h1>
<table>
    <tr><td>No Input</td><td>:</td><td><?PHP echo $_GET['id']; ?></td></tr>
    <tr><td>Karyawan</td><td>:</td><td><?PHP echo $nmajukan; ?></td></tr>
    <tr><td>Cabang</td><td>:</td><td><?PHP echo $nmcab; ?></td></tr>
</table>

<table id='datatablea' class='table table-striped table-bordered'>
    <thead>
        <tr>
            <th width='10%'>No</th>
            <th width='15%'>Rp</th>
            <th width='30%'>Akun</th>
            <th>Catatan</th>
        </tr>
    </thead>
    <?PHP
        echo "<tbody>";
        $detail = mysqli_query($cnmy, "SELECT NOID, FORMAT(RP,2,'de_DE') as RP, kode, nama_kode, AKTIVITAS2 FROM dbbudget.v_br_d WHERE NOID='$_GET[id]'");
        $no=1;
        while ($d    = mysqli_fetch_array($detail)) {
            echo  "<tr>";
            echo "<td>$no</td>";
            echo "<td class='angka'>$d[RP]</td>";
            echo "<td>$d[kode]";
            echo " &nbsp;&nbsp;$d[nama_kode]</td>";
            echo "<td>$d[AKTIVITAS2]</td>";
            echo "</tr>";
            $no++;
        }
        echo "</tbody>";
    ?>
</table>

<table id='datatable' class='table table-striped table-bordered'>
    <thead>
        <tr>
            <th width='10%'>No</th>
            <th>Akun</th>
            <th width='10%'>Jumlah/Hari</th>
            <th width='10%'>Total</th>
            <th>Note</th>
        </tr>
    </thead>
        <?PHP
        echo "<tbody>";
        $no=1;
        $tampil = mysqli_query($cnmy, "SELECT NOBUD, NAMA_BUD, FORMAT(RP,2,'de_DE') as RP FROM dbbudget.br_uc_budget order by NOBUD");
        while ($uc=mysqli_fetch_array($tampil)){
            $jmhr=  getfield("select JML as lcfields from dbbudget.t_br_u where NOID='$_GET[id]' and NOBUD='$uc[NOBUD]'");
            $total=  getfield("select FORMAT(TOTAL,2,'de_DE') as lcfields from dbbudget.t_br_u where NOID='$_GET[id]' and NOBUD='$uc[NOBUD]'");
            $note=  getfield("select KET as lcfields from dbbudget.t_br_u where NOID='$_GET[id]' and NOBUD='$uc[NOBUD]'");
            
            echo "<tr scope='row'><td>$no</td>";
            echo "<td>$uc[NAMA_BUD]</td>";
            echo "<td class='angka'>$jmhr</td>";
            echo "<td class='angka'>$total</td>";
            echo "<td>$note</td>";
            $no++;
        }
        echo "</tbody>";
        ?>
</table>

