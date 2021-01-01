<?php
if ($_GET['module']=="viewdatamr0") {
    include "../../config/koneksimysqli_it.php";
    $idkarawanspv=$_POST['uspv'];
    $no=1;
    $query = "select distinct karyawanid, nama from dbmaster.v_penempatanmr where ifnull(karyawanid,'')<>'' "
            . " and CONCAT(icabangid,areaid) in (select distinct CONCAT(icabangid,areaid) from dbmaster.v_penempatanspv where karyawanid='$idkarawanspv')"
            . " order by nama";
    $tampil=mysqli_query($cnit, $query);

    while ($r=  mysqli_fetch_array($tampil)) {
        echo "<option value='$r[karyawanid]'>$r[nama]</option>";
        if ($no==1) $idkarawalmr="$r[karyawanid]";
        $no++;
    }
}elseif ($_GET['module']=="viewdataspv0") {
    include "../../config/koneksimysqli_it.php";
    $idkarawandm=$_POST['udm'];
    $no=1;
    $query = "select distinct karyawanid, nama from dbmaster.v_penempatanspv where ifnull(karyawanid,'')<>'' "
            . " and icabangid in (select distinct icabangid from dbmaster.v_penempatandm where karyawanid='$idkarawandm')"
            . " order by nama";
    $tampil=mysqli_query($cnit, $query);

    while ($r=  mysqli_fetch_array($tampil)) {
        echo "<option value='$r[karyawanid]'>$r[nama]</option>";
        if ($no==1) $idkarawalspv="$r[karyawanid]";

        $no++;
    }
}elseif ($_GET['module']=="viewdatadm0") {
    include "../../config/koneksimysqli_it.php";
    $idkarawansm=$_POST['usm'];
    
    $no=1;
    $query = "select distinct karyawanid, nama from dbmaster.v_penempatandm where ifnull(karyawanid,'')<>'' "
            . " and icabangid in (select distinct icabangid from dbmaster.v_penempatansm where karyawanid='$idkarawansm')"
            . " order by nama";
    $tampil=mysqli_query($cnit, $query);

    while ($r=  mysqli_fetch_array($tampil)) {
        echo "<option value='$r[karyawanid]'>$r[nama]</option>";
        if ($no==1) $idkarawaldm="$r[karyawanid]";

        $no++;
    }
}elseif ($_GET['module']=="viewdatasm0") {
    include "../../config/koneksimysqli_it.php";
    
}else{
include "../../config/koneksimysqli_it.php";
$aksi="module/md_m_penempatan/aksi_penempatan.php";

?>
<style>
    .divnone {
        display: none;
    }
    #datatable th {
        font-size: 12px;
    }
    #datatable td { 
        font-size: 11px;
    }
</style>

<div class='x_content'>

    <div class='x_title'>
        <h2><input class='btn btn-default' type=button value='Tambah Baru MR'
            onclick="window.location.href='<?PHP echo "?module=$_GET[module]&idmenu=$_GET[idmenu]&act=tambahbarumr"; ?>';">
            <small></small>
        </h2>
        <div class='clearfix'></div>
    </div>

    <table id='datatable' class='table table-striped table-bordered' width="100%">
        <thead>
            <tr>
                <th width='7px'>No</th>
                <th width='5px'>ID SPV</th><th width='100px'>Nama SPV</th>
                <th width='80px'>Nama Area</th><th width='10px'>Divisi</th><th width='10px'>Tanggal</th><th width='2px'>Aktif</th>
                <th width='5px'>ID MR</th><th width='80px'>Nama MR</th>
                <th width='80px'>Nama Area</th><th width='10px'>Divisi</th><th width='10px'>Tanggal</th><th width='2px'>Aktif</th>
            </tr>
        </thead>
        <tbody>
            <?PHP
            $idkarawansm=$_POST['usm'];
            $idkarawandm=$_POST['udm'];
            $idkarawanspv=$_POST['uspv'];
            $idkarawanmr=$_POST['umr'];
            
            $no=1;
            $query = "select distinct karyawanid, nama, icabangid, nama_cabang, areaid, nama_area, divisiid, tgl1, aktif from dbmaster.v_penempatanspv where "
                    . " karyawanid='$idkarawanspv' and ifnull(karyawanid,'')<>'' "
                    . " and icabangid in (select distinct icabangid from dbmaster.v_penempatandm where karyawanid='$idkarawandm')"
                    . " order by nama, divisiid, nama_cabang, nama_area, tgl1";

            $tampil=mysqli_query($cnit, $query);

            while ($r=  mysqli_fetch_array($tampil)) {
                $tgl=$r['tgl1']; $periodespv="";
                if (!empty($tgl) AND $tgl<>"0000-00-00")
                    $periodespv= date("d-m-Y", strtotime($tgl));
                echo "<tr>";
                echo "<td>$no</td>";
                echo "<td><b><small><a href='?module=$_GET[module]&act=editdataspv&idmenu=$_GET[idmenu]"
                        . "&nmun=$_GET[idmenu]&id=$r[karyawanid]"
                        . "&idcab=$r[icabangid]&tgl=$r[tgl1]"
                        . "&divisi=$r[divisiid]' class='btn btn-success btn-xs'>"
                        . "$r[karyawanid]</a></small></b></td>";
                echo "<td><b>$r[nama]</b></td>";
                echo "<td><b>$r[nama_area]</b></td>";
                echo "<td><b>$r[divisiid]</b></td>";
                echo "<td><b><small>$periodespv</small></b></td>";
                echo "<td>"
                    . "<a class='btn btn-default btn-xs' "
                    . "href='$aksi?module=$_GET[module]&act=aktifkan&idmenu=$_GET[idmenu]&nmun=$_GET[idmenu]&id=$r[karyawanid]&idcab=$r[icabangid]&tgl=$r[tgl1]&divisi=$r[divisiid]'
                        onClick=\"return confirm('Apakah Anda melakukan proses?')\">$r[aktif]</a></td>";

                $j=0;
                $query2 = "select distinct karyawanid, nama, divisiid, icabangid, nama_cabang, areaid, nama_area, tgl1, aktif "
                        . " from dbmaster.v_penempatanmr where karyawanid ='$idkarawanmr' and divisiid='$r[divisiid]' "
                    . " and icabangid='$r[icabangid]' and areaid='$r[areaid]' order by nama, divisiid, nama_cabang, nama_area, tgl1";

                $query2x = "select distinct karyawanid, nama, divisiid, icabangid, nama_cabang, areaid, nama_area, tgl1, aktif "
                        . " from dbmaster.v_penempatanmr where divisiid='$r[divisiid]' "
                    . " and icabangid='$r[icabangid]' and areaid='$r[areaid]' order by nama, divisiid, nama_cabang, nama_area, tgl1";
                $tampil2=mysqli_query($cnit, $query2);
                $ketemu2=  mysqli_num_rows($tampil2);
                while ($r2=  mysqli_fetch_array($tampil2)) {
                    $tglmr=$r2['tgl1']; $periodemr="";
                    if (!empty($tglmr) AND $tglmr<>"0000-00-00")
                        $periodemr= date("d-m-Y", strtotime($tglmr));

                    if ($j==0) {
                    }else{
                        echo "<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td>";
                    }

                    echo "<td><small><a href='?module=$_GET[module]&act=editdatamr&idmenu=$_GET[idmenu]&nmun=$_GET[idmenu]"
                            . "&id=$r2[karyawanid]&idcab=$r2[icabangid]&idarea=$r2[areaid]&tgl=$r2[tgl1]"
                            . "&divisi=$r2[divisiid]' class='btn btn-danger btn-xs'>$r2[karyawanid]</a></small></td>";
                    echo "<td>$r2[nama]</td>";
                    echo "<td>$r2[nama_area]</td>";
                    echo "<td>$r2[divisiid]</td>";
                    echo "<td><small>$periodemr</small></td>";
                    echo "<td>"
                        . "<a class='btn btn-default btn-xs' "
                        . "href='$aksi?module=$_GET[module]&act=aktifkan&idmenu=$_GET[idmenu]&nmun=$_GET[idmenu]"
                            . "&id=$r2[karyawanid]&idcab=$r2[icabangid]&idarea=$r2[areaid]"
                            . "&tgl=$r2[tgl1]&divisi=$r2[divisiid]'
                            onClick=\"return confirm('Apakah Anda melakukan proses?')\">$r2[aktif]</a></td>";
                    echo "</tr>";
                    $j++;
                }
                if ($ketemu2==0) echo "<td></td><td></td><td></td><td></td><td></td><td></td></tr>";
                $no++;
            }
            ?>
        </tbody>
    </table>
</div>
<?PHP
}
?>