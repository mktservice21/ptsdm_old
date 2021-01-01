<?php
    session_start();
    include ("config/koneksimysqli_ms.php");
    
    $pperiode_=$_GET['bln'];
    $pidcabpil=$_GET['icab'];
    $prpttype=$_GET['rptby'];
    $pdivisipil=$_GET['idiv'];
    
    $now=date("mdYhis");
    $tmp01 =" dbtemp.DTTMLGTUSR_01".$_SESSION['USERID']."_$now ";
    $tmp02 =" dbtemp.DTTMLGTUSR_02".$_SESSION['USERID']."_$now ";
    $tmp03 =" dbtemp.DTTMLGTUSR_03".$_SESSION['USERID']."_$now ";

    
    $query ="select * from tgt.targetarea WHERE DATE_FORMAT(bulan,'%Y%m')='$pperiode_' AND icabangid='$pidcabpil' AND divprodid='$pdivisipil'";
    $query = "CREATE TEMPORARY TABLE $tmp01($query)";
    mysqli_query($cnms, $query);
    $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { goto hapusdata; }
    
    $query ="select divprodid, iprodid, sum(qty) cab_qty, sum(value) as cab_value, "
            . " CAST(0 as DECIMAL(20,2)) as area_qty, CAST(0 as DECIMAL(20,2)) as area_value, "
            . " CAST(0 as DECIMAL(20,2)) as sisa_qty, CAST(0 as DECIMAL(20,2)) as sisa_value "
            . " from tgt.targetcab WHERE DATE_FORMAT(bulan,'%Y%m')='$pperiode_' AND icabangid='$pidcabpil' AND divprodid='$pdivisipil' GROUP BY 1,2";
    $query = "CREATE TEMPORARY TABLE $tmp02($query)";
    mysqli_query($cnms, $query);
    $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp02 a JOIN (SELECT divprodid, iprodid, SUM(qty) tqty, SUM(value) tvalue FROM $tmp01 GROUP BY 1,2) b on "
            . " a.divprodid=b.divprodid AND a.iprodid=b.iprodid SET a.area_qty=b.tqty, a.area_value=b.tvalue";
    mysqli_query($cnms, $query);
    $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp02 SET sisa_qty=IFNULL(cab_qty,0)-IFNULL(area_qty,0), sisa_value=IFNULL(cab_value,0)-IFNULL(area_value,0)";
    mysqli_query($cnms, $query);
    $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query ="select a.*, b.nama nmproduk from $tmp02 a LEFT JOIN sls.iproduk b on CONVERT(a.iprodid, DECIMAL(20))=CONVERT(b.iprodid, DECIMAL(20))";
    $query = "CREATE TEMPORARY TABLE $tmp03 ($query)";
    mysqli_query($cnms, $query);
    $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query ="DELETE FROM $tmp03 WHERE (IFNULL(sisa_qty,0)=0 AND IFNULL(sisa_value,0)=0)";
    mysqli_query($cnms, $query);
    $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
?>
    
<HTML>
<HEAD>
    <title>Detail Produk Target Area</title>
    <meta http-equiv="Expires" content="Mon, 01 Apr 2019 1:00:00 GMT">
    <meta http-equiv="Pragma" content="no-cache">
    <link rel="shortcut icon" href="images/icon.ico" />
    <link href="css/laporanbaru.css" rel="stylesheet">
    <?php header("Cache-Control: no-cache, must-revalidate"); ?>
</HEAD>
<BODY>
    
<form method='POST' action='' id='d-form2' name='form2' data-parsley-validate class='form-horizontal form-label-left'>

    <div class='x_content'>
        
        <p/>&nbsp;
        <div class="title_left">
            <h4>
                <b>QUOTA PER CABANG DAN PRODUK</b>
            </h4>
        </div>
        <div class="clearfix"></div>
        
        <table id='datatable2' class='table table-striped table-bordered example_2' border="1px solid black">
            <thead>
                <tr style='background-color:#cccccc; font-size: 13px;'>
                    <th width="15px" nowrap align='center'>No</th>
                    <th nowrap align='center'>Divisi</th>
                    <th nowrap align='center'>Produk</th>
                    <th nowrap align='center'>Quota Qty</th>
                    <th nowrap align='center'>Quota Value</th>
                </tr>
            </thead>
            <tbody>
                <?PHP
                $ptotqty=0;
                $ptotval=0;
                $no=1;
                $query = "SELECT divprodid, iprodid, nmproduk, SUM(sisa_qty) sisa_qty, SUM(sisa_value) as sisa_value "
                        . " FROM $tmp03 WHERE (IFNULL(sisa_qty,0)<>0 OR IFNULL(sisa_value,0)<>0) GROUP BY 1,2,3 ORDER BY 1,3,2";
                $tampil= mysqli_query($cnms, $query);
                while ($row= mysqli_fetch_array($tampil)) {
                    $nnpdivisi=$row['divprodid'];
                    $nnpnmprod=$row['nmproduk'];
                    $nnpqtysisa=$row['sisa_qty'];
                    $nnpvalsisa=$row['sisa_value'];
                    
                    
                    $ptotqty=(double)$ptotqty+(double)$nnpqtysisa;
                    $ptotval=(double)$ptotval+(double)$nnpvalsisa;
                    
                    $nnpqtysisa=number_format($nnpqtysisa,0,",",",");
                    $nnpvalsisa=number_format($nnpvalsisa,0,",",",");
                    
                    
                    echo "<tr>";
                    echo "<td nowrap>$no</td>";
                    echo "<td nowrap>$nnpdivisi</td>";
                    echo "<td nowrap>$nnpnmprod</td>";
                    echo "<td nowrap align='right'>$nnpqtysisa</td>";
                    echo "<td nowrap align='right'>$nnpvalsisa</td>";
                    echo "</tr>";
                    
                    $no++;
                }
                $ptotqty=number_format($ptotqty,0,",",",");
                $ptotval=number_format($ptotval,0,",",",");
                
                echo "<tr>";
                echo "<td nowrap colspan='3' align='center'><b>T O T A L</b></td>";
                echo "<td nowrap align='right'><b>$ptotqty</b></td>";
                echo "<td nowrap align='right'><b>$ptotval</b></td>";
                echo "</tr>";
                
                ?>
            </tbody>
        </table>
        
    </div>

</form>
    
</BODY>

</HTML>


<?PHP  
hapusdata:
    mysqli_query($cnms, "DROP TEMPORARY TABLE $tmp01");
    mysqli_query($cnms, "DROP TEMPORARY TABLE $tmp02");
    mysqli_query($cnms, "DROP TEMPORARY TABLE $tmp03");
    mysqli_close($cnms);

?>
