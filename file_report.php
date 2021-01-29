<?php
include "mysdm/config/koneksimysqli_ms.php";

$query = "select a.icabangid, b.nama as nama_cabang, a.areaid, c.nama as nama_area, "
        . " a.iprodid, d.nama as nama_produk, left(a.tgljual,7) as bulan, "
        . " sum(a.qty) as qty, sum(a.hna*a.qty) as tvalue "
        . " from sls.mr_sales2 as a join mkt.icabang as b on a.icabangid=b.icabangid "
        . " join mkt.iarea as c on a.areaid=c.areaid and a.icabangid=c.icabangid "
        . " join mkt.iproduk as d on a.iprodid=d.iprodid "
        . " where a.tgljual between '2021-01-01' AND '2021-01-15' group by 1,2,3,4,5,6,7";
$tampil= mysqli_query($cnms, $query);

echo "<table border='1px'>";
echo "<tr>";
echo "<th nowrap>No</th>";
echo "<th nowrap>Nama Cabang</th>";
echo "<th nowrap>Nama Area</th>";
echo "<th nowrap>Nama Produk</th>";
echo "<th nowrap>Qty</th>";
echo "</tr>";

$no=1;
while ($row=mysqli_fetch_array($tampil)) {
    $picabangid=$row['icabangid'];
    $picabangnm=$row['nama_cabang'];
    $pareanm=$row['nama_area'];
    $pprodnm=$row['nama_produk'];
    $pqty=$row['qty'];
    
    
    echo "<tr>";
    echo "<td nowrap>$no</td>";
    echo "<td nowrap>$picabangnm</td>";
    echo "<td nowrap>$pareanm</td>";
    echo "<td nowrap>$pprodnm</td>";
    echo "<td nowrap align='right'>$pqty</td>";
    echo "</tr>";
    
    $no++;
}

echo "</table>";

mysqli_close($cnms);

?>