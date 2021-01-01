<?PHP
    ini_set('memory_limit', '-1');
    ini_set('max_execution_time', 0);
    session_start();
    $ppilihrpt=$_GET['ket'];
    if ($ppilihrpt=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=LAPORAN KLAIM PENGOBATAN.xls");
    }
    
    include("config/koneksimysqli.php");
    include "config/fungsi_combo.php";
    include "config/fungsi_sql.php";
    $cnit=$cnmy;
?>


<html>
<head>
    <title>LAPORAN KLAIM PENGOBATAN</title>
    <?PHP if ($ppilihrpt!="excel") { ?>
        <meta http-equiv="Expires" content="Mon, 01 Mei 2050 1:00:00 GMT">
        <meta http-equiv="Pragma" content="no-cache">
        <link rel="shortcut icon" href="images/icon.ico" />
        <link href="css/laporanbaru.css" rel="stylesheet">
        <?php header("Cache-Control: no-cache, must-revalidate"); ?>
    <?PHP } ?>
</head>

<body>
    <?PHP
        
        $pidkaryawan=$_POST['cb_karyawan'];
        $nmkaryawan= getfield("select nama as lcfields from hrd.karyawan where karyawanId='$pidkaryawan'");
        
        $filterjenis=('');
        if (!empty($_POST['chkbox_jnsobat'])){
            $filterjenis=$_POST['chkbox_jnsobat'];
            $filterjenis=PilCekBoxAndEmpty($filterjenis);
        }
    
        $tgl01 = $_POST['e_tgl1'];
        $tgl02 = $_POST['e_tgl2'];
        
        $pperiode1 = date("Y-m", strtotime($tgl01));
        $pperiode2 = date("Y-m", strtotime($tgl02));
        
        $myperiode1 = date("F Y", strtotime($tgl01));
        $myperiode2 = date("F Y", strtotime($tgl02));
    
        
        
        //echo "$pidkaryawan, $nmkaryawan, $filterjenis, $pperiode1, $pperiode2"; exit;
        
        
        $now=date("mdYhis");
        $tmp01 =" dbtemp.RPTKPCC01_".$_SESSION['USERID']."_$now ";
        $tmp02 =" dbtemp.RPTKPCC02_".$_SESSION['USERID']."_$now ";
        
        $query = "SELECT b.karyawanid, d.nama nama_karyawan, a.idrutin, a.nobrid, c.nama nama_id, a.tgl1, a.obat_untuk, a.notes, a.alasanedit_fin, a.rptotal 
            FROM dbmaster.t_brrutin1 a 
            JOIN dbmaster.t_brrutin0 b on a.idrutin=b.idrutin
            JOIN dbmaster.t_brid c on a.nobrid=c.nobrid
            JOIN hrd.karyawan d on b.karyawanid=d.karyawanId
            WHERE IFNULL(b.stsnonaktif,'')<>'Y'
            AND a.nobrid in $filterjenis
            AND IFNULL(b.tgl_fin,'') <>'' AND DATE_FORMAT(b.bulan,'%Y-%m') BETWEEN '$pperiode1' AND '$pperiode2' AND 
            b.karyawanid='$pidkaryawan' ";
        //echo $query; goto hapusdata;
        $query = "create TEMPORARY table $tmp01 ($query)";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        echo "<table class='tjudul' width='100%'>";
        echo "<tr> <td width='400px'>LAPORAN KLAIM PENGOBATAN ($nmkaryawan)</td> </tr>";
        echo "<tr> <td width='200px'>$myperiode1 s/d. $myperiode2</td></tr>";
        echo "</table>";
        echo "<br/>&nbsp;";
        

    ?>
        <table id='datatable2' class='table table-striped table-bordered example_2' border="1px solid black">
            <thead>
                <tr>
                <th align="center" nowrap>No</th>
                <th align="center" nowrap>ID</th>
                <th align="center" nowrap>NAMA</th>
                <th align="center" nowrap>TANGGAL</th>
                <th align="center" nowrap>ATAS NAMA</th>
                <th align="center" nowrap>JUMLAH</th>
                <th align="center" nowrap>KETERANGAN</th>
                </tr>
            </thead>
            <tbody>
                <?PHP
                $ptotal=0;
                $no=1;
                $query = "select * from $tmp01 order by idrutin, nobrid";
                $tampil=mysqli_query($cnit, $query);
                while ($row= mysqli_fetch_array($tampil)) {
                    $pidrutin=$row['idrutin'];
                    $pnoid=$row['nobrid'];
                    $pnamaid=$row['nama_id'];
                    $puntuk=$row['obat_untuk'];
                    $pket=$row['notes'];
                    $palasandin=$row['alasanedit_fin'];
                    if (!empty($palasandin)) $pket=$palasandin;
                    $pobatuntuk="";
                    if ($puntuk=="1") $pobatuntuk="Istri";
                    if ($puntuk=="2") $pobatuntuk="Anak";
                    $prptotal=$row['rptotal'];
                    $ptotal=(double)$ptotal+(double)$prptotal;
                    $ptgl="";
                    if (!empty($row['tgl1']) AND $row['tgl1']<>"0000-00-00") $ptgl = date("d/m/Y", strtotime($row['tgl1']));
                    $prptotal=number_format($prptotal,0,",",",");
                    

                    echo "<tr>";
                    echo "<td nowrap>$no</td>";
                    echo "<td nowrap>$pidrutin</td>";
                    echo "<td nowrap>$pnamaid</td>";
                    echo "<td nowrap>$ptgl</td>";
                    echo "<td nowrap>$pobatuntuk</td>";
                    echo "<td nowrap align='right'>$prptotal</td>";
                    echo "<td>$pket</td>";
                    echo "</tr>";
                    
                    $no++;
                }
                $ptotal=number_format($ptotal,0,",",",");
                echo "<tr>";
                echo "<td colspan='5' align='center'><b>TOTAL</b></td>";
                echo "<td nowrap align='right'><b>$ptotal</b></td>";
                echo "<td></td>";
                echo "</tr>";
                ?>
            </tbody>
        </table>
    

    <br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;
    <?PHP
    hapusdata:
        mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp01");
        mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp02");
        
        mysqli_close($cnit);
    ?>
</body>

    <style>
        .tjudul {
            font-family: Georgia, serif;
            font-size: 15px;
            margin-left:10px;
            margin-right:10px;
        }
        .tjudul td {
            padding: 4px;
        }
        #datatable2 {
            font-family: Georgia, serif;
            margin-left:10px;
            margin-right:10px;
        }
        #datatable2 th, #datatable2 td {
            padding: 4px;
        }
        #datatable2 thead{
            background-color:#cccccc; 
            font-size: 14px;
        }
        #datatable2 tbody{
            font-size: 13px;
        }
    </style>
    
    
    
</html>
