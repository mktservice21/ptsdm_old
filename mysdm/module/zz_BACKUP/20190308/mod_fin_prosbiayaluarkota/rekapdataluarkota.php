<?PHP
    session_start();
    if (isset($_GET['iprint'])) {
        if ($_GET['iprint']=="editrutin"){
            include 'editdataluarkota.php';
            exit;
        }
    }
    if ($_GET['act']=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=REKAP BIAYA LUAR KOTA.xls");
    }
    
    include("config/koneksimysqli.php");
    include("config/common.php");
    include "config/fungsi_combo.php";
    
?>
<html>
<head>
    <title>REKAP BIAYA LUAR KOTA</title>
<?PHP if ($_GET['act']!="excel") { ?>
    <meta http-equiv="Expires" content="Mon, 01 Jan 2007 1:00:00 GMT">
    <meta http-equiv="Pragma" content="no-cache">
    <link rel="shortcut icon" href="images/icon.ico" />
    <link href="css/laporanbaru.css" rel="stylesheet">
    <?php header("Cache-Control: no-cache, must-revalidate"); ?>
<?PHP } ?>
</head>

<body>
<?php
    $tglnow = date("d/m/Y");
    
    $filterid=('');
    if (!empty($_POST['chkbox_br'])){
        $filterid=$_POST['chkbox_br'];
        $filterid=PilCekBox($filterid);
    }
    $noidbr=" $filterid ";

    if (empty($filterid)) {
        echo "Tidak ada data yang direkap"; exit;
    }

    
    $now=date("mdYhis");
    $tmp01 =" dbtemp.DTBRRFIPD01_".$_SESSION['IDCARD']."_$now ";
    $tmp02 =" dbtemp.DTBRRFIPD02_".$_SESSION['IDCARD']."_$now ";
    
    $query = "SELECT
	b1.nourut,
	br.idrutin,
	br.kode,
	br.karyawanid,
	br.bulan,
	br.kodeperiode,
	br.periode1,
	br.periode2,
	br.jumlah,
	br.keterangan,
	br.divisi,
	br.tgltrans,
	br.jmltrans,
	k.nama,
	a.nama nama_area_o,
	aa.Nama nama_area,
	b1.nobrid,
	i.nama nama_brid,
	b1.qty,
	b1.rp,
	b1.rptotal,
	b1.notes,
	b1.coa,
	c1.NAMA4 nama_coa,
        ifnull(br.tgl_atasan1,'0000-00-00') tgl_atasan1,
        br.gbr_atasan1,
        ifnull(br.tgl_atasan2,'0000-00-00') tgl_atasan2,
        br.gbr_atasan2,
        ifnull(br.tgl_atasan3,'0000-00-00') tgl_atasan3,
        br.gbr_atasan3,
        ifnull(tgl_atasan4,'0000-00-00') tgl_atasan4,
        br.gbr_atasan4
        FROM
                dbmaster.t_brrutin1 AS b1
        LEFT JOIN dbmaster.t_brrutin0 AS br ON b1.idrutin = br.idrutin
        LEFT JOIN hrd.karyawan AS k ON br.karyawanid = k.karyawanId
        LEFT JOIN MKT.iarea_o AS a ON br.areaid_o = a.areaid_o and br.icabangid_o=a.icabangid_o
        LEFT JOIN MKT.iarea AS aa ON br.areaid = aa.areaId and br.icabangid=aa.iCabangId
        LEFT JOIN dbmaster.t_brid AS i ON b1.nobrid = i.nobrid
        LEFT JOIN dbmaster.coa_level4 c1 ON c1.COA4 = b1.coa WHERE br.kode=2 AND br.stsnonaktif <> 'Y' AND br.idrutin in $noidbr ";
    
    $query = "create temporary table $tmp01 ($query)";
    mysqli_query($cnmy, $query);
    // update atasan NN
    mysqli_query($cnmy, "UPDATE $tmp01 set tgl_atasan1=null WHERE IFNULL(gbr_atasan1,'')=''");
    mysqli_query($cnmy, "UPDATE $tmp01 set tgl_atasan2=null WHERE IFNULL(gbr_atasan2,'')=''");
    mysqli_query($cnmy, "UPDATE $tmp01 set tgl_atasan3=null WHERE IFNULL(gbr_atasan3,'')=''");
    mysqli_query($cnmy, "UPDATE $tmp01 set tgl_atasan4=null WHERE IFNULL(gbr_atasan4,'')=''");
    
    $query = "select distinct idrutin, karyawanid, nama, divisi, keterangan, jumlah, tgl_atasan1,tgl_atasan2,tgl_atasan3,tgl_atasan4, jumlah as jmlhari, jumlah nilairp from $tmp01";
    $query = "create temporary table $tmp02 ($query)"; 
    mysqli_query($cnmy, $query);
    mysqli_query($cnmy, "UPDATE $tmp02 a set jmlhari=null, nilairp=null");
    
    $totalfield=1;
    $query = "select * from dbmaster.t_brid where kode=2 order by nobrid";
    $tampil = mysqli_query($cnmy, $query);
    while ($row=mysqli_fetch_array($tampil)) {
        $noid=$row['nobrid'];
        mysqli_query($cnmy, "ALTER TABLE $tmp02 add COLUMN F$noid DECIMAL(30,2)");
        $query = "UPDATE $tmp02 a set F$noid =(SELECT SUM(rptotal) FROM $tmp01 b WHERE a.idrutin=b.idrutin AND b.nobrid='$noid')";
        mysqli_query($cnmy, $query);
        $totalfield++;
    }
    
    $query = "UPDATE $tmp02 a set F4=10";
    //mysqli_query($cnmy, $query);
    
    $query = "UPDATE $tmp02 a set jmlhari =(SELECT qty FROM $tmp01 b WHERE a.idrutin=b.idrutin AND b.nobrid='04' LIMIT 1)";
    mysqli_query($cnmy, $query);
    $query = "UPDATE $tmp02 a set nilairp =(SELECT rp FROM $tmp01 b WHERE a.idrutin=b.idrutin AND b.nobrid='04' LIMIT 1)";
    mysqli_query($cnmy, $query);
    

?>

    <div id="kotakjudul">
        <div id="isikiri">
            <table class='tjudul' width='100%'>
                <tr><td width="150px"><b>PT SDM</b></td></tr>
                <tr><td width="150px"><b>Rekap Biaya Luar Kota</b></td></tr>
            </table>
        </div>
    </div>
    <div class="clearfix"></div>
    
        <table id='datatable2' class='table table-striped table-bordered example_2' border="1px solid black" width="100%">
            <thead>
                <tr style='background-color:#cccccc; font-size: 11px;'>
                <th align="center">No</th>
                <th align="center">ID</th>
                <th align="center" nowrap>Nama</th>
                <th align="center" nowrap>Divisi</th>
                <?PHP
                $query = "select * from dbmaster.t_brid where kode=2 order by nobrid";
                $result = mysqli_query($cnmy, $query);
                while ($row = mysqli_fetch_array($result)) {
                    $noid=$row["nobrid"];
                    $namabr=$row["nama"];
                    if ($noid=="04")
                        echo "<th align='center' colspan=2>$namabr</th>";
                    else
                        echo "<th align='center'>$namabr</th>";
                }
                ?>
                <th align="center" nowrap>Total Rp.</th>
                </tr>
            </thead>
            <tbody>
                <?PHP
                    $no=1;
                    for($x=1;$x<=$totalfield;$x++) {
                        $tot[$x]=0;
                    }
                    $totjumlh=0;
                    $query = "select * from $tmp02 order by divisi, nama, karyawanid";
                    $tampil= mysqli_query($cnmy, $query);
                    while ($row=mysqli_fetch_array($tampil)) {
                        $brnoid=$row['idrutin'];
                        $karyawanid=$row['karyawanid'];
                        $nama=$row['nama'];
                        $pdivisi=$row["divisi"];
                        if ($pdivisi=="CAN") $pdivisi="CANARY";
                        echo "<tr>";
                        echo "<td>$no</td>";
                        echo "<td nowrap>$brnoid</td>";
                        echo "<td nowrap>$nama</td>";
                        echo "<td nowrap>$pdivisi</td>";
                        
                        $zz=1;
                        $query2 = "select * from dbmaster.t_brid where kode=2 order by nobrid";
                        $tampil2= mysqli_query($cnmy, $query2);
                        while ($row2=mysqli_fetch_array($tampil2)) {
                            $namabr=$row2["nama"];
                            $noid=$row2["nobrid"];
                            
                            $jumlah=$row["F$noid"];
                            $jumlah=number_format($jumlah,0,",",",");
                            $jmlhari=""; $nilairp=""; $jmlrp="";
                            if ($noid=="04") {
                                $jmlhari=number_format($row["jmlhari"],0,",",",");
                                $nilairp=number_format($row["nilairp"],0,",",",");
                                $jmlrp="($jmlhari"."x"."$nilairp)<br/>";
                                echo "<td>$jmlrp</td>";
                                $zz++;
                                $tot[$zz]=$tot[$zz];
                            }
                            echo "<td align='right'>$jumlah</td>";
                            $tot[$zz]=$tot[$zz]+$row["F$noid"];
                            $zz++;   
                        }
                        //total rp
                        $jumlah=number_format($row['jumlah'],0,",",",");
                        $totjumlh=$totjumlh+$row['jumlah'];
                        echo "<td align='right'>$jumlah</td>";
                        echo "</tr>";

                        $no++;
                    }
                    echo "<tr>";
                    echo "<td colspan='4' align='right'><b>TOTAL : </b></td>";
                    for($x=1;$x<=$totalfield-1;$x++) {
                        $total=$tot[$x];
                        $total=number_format($total,0,",",",");
                        echo "<td align='right'><b>$total</b></td>";
                    }
                    $totjumlh=number_format($totjumlh,0,",",",");
                    echo "<td align='right'><b>$totjumlh</b></td>";
                    echo "</tr>";
                ?>
            </tbody>
        </table>
        <br/>&nbsp;<br/>&nbsp;
        <?PHP
            mysqli_query($cnmy, "drop temporary table $tmp01");
            mysqli_query($cnmy, "drop temporary table $tmp02");
        ?>
</body>
</html>
