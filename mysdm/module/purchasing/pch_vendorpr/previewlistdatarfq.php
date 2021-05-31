<?php
    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","512M");
    ini_set('max_execution_time', 0);

    session_start();
    
    $puserlogin="";
    $puserid="";
    if (isset($_SESSION['IDCARD'])) $puserlogin=$_SESSION['IDCARD'];
    if (isset($_SESSION['USERID'])) $puserid=$_SESSION['USERID'];
    
    if (empty($puserlogin) AND empty($puserid)){
        echo "<center>Maaf, Anda Harus Login Ulang.<br>"; exit;
    }
    
    
    $ppilihrpt=$_GET['ket'];
    if ($ppilihrpt=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=List_Data_RFQ.xls");
    }
    
    
    $pnamakarywanpl=$_SESSION['NAMALENGKAP'];
    
    if (!isset($_POST['chkbox_br'])) {
        echo "<center>tidak ada data yang dipreview.<br>"; exit;
    }
    
    $filterbr="";
    foreach ($_POST['chkbox_br'] as $no_brid) {
        if (!empty($no_brid)) {
            $filterbr .="'".$no_brid."',";
        }
    }
    
    if (!empty($filterbr)) $filterbr="(".substr($filterbr, 0, -1).")";
    else $filterbr="('')";
    
    
    include("config/koneksimysqli.php");
    include("config/common.php");

    $now=date("mdYhis");
    $tmp01 =" dbtemp.tmpprvlistrfq01_".$puserid."_$now ";
    $tmp02 =" dbtemp.tmpprvlistrfq02_".$puserid."_$now ";
    $tmp03 =" dbtemp.tmpprvlistrfq03_".$puserid."_$now ";

    $query = "select a.idpr_po, a.tglinput as tglinputrfq, a.idpr_d, 
        a.idpr, b.karyawanid, c.nama as nama_karyawan_pr, b.tanggal as tglpr,
        CASE WHEN IFNULL(e.stsnonaktif,'')<>'Y' THEN d.idpo ELSE '' END as idpo, a.kdsupp, f.NAMA_SUP as nama_sup,
        a.idbarang, a.namabarang, a.spesifikasi1, a.keterangan, a.jumlah, a.satuan, a.harga, a.totalrp, a.aktif, a.userid
        from dbpurchasing.t_pr_transaksi_po as a 
        JOIN dbpurchasing.t_pr_transaksi as b on a.idpr=b.idpr 
        JOIN hrd.karyawan as c on b.karyawanid=c.karyawanId 
        LEFT join dbpurchasing.t_po_transaksi_d as d on a.idpr_po=d.idpr_po 
        LEFT JOIN dbpurchasing.t_po_transaksi as e on d.idpo=e.idpo and a.kdsupp=e.kdsupp 
        LEFT JOIN dbmaster.t_supplier as f on a.kdsupp=f.KDSUPP 
        WHERE a.idpr_po IN $filterbr";
    $query = "create TEMPORARY table $tmp01 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
?>


<HTML>
<HEAD>
  <TITLE>List Data RFQ</TITLE>
    <meta http-equiv="Expires" content="Mon, 01 Jan 2030 1:00:00 GMT">
    <meta http-equiv="Pragma" content="no-cache">
    <?php header("Cache-Control: no-cache, must-revalidate"); ?>
    <link rel="shortcut icon" href="images/icon.ico" />
    <style> .str{ mso-number-format:\@; } </style>
</HEAD>
<script>
</script>

<BODY onload="initVar()">

    <?PHP

    echo "<b>List Data RFQ</b><br/>";
    //echo "<b>Nama : $pnamakarywanpl - $puserlogin</b><br/>";
    echo "<hr/><br/>";
    
    
    echo "<table id='tbltable' border='1' cellspacing='0' cellpadding='1'>";
        echo "<tr>";
            echo "<th align='left'><small>No</small></th>";
            echo "<th align='left'><small>ID PR</small></th>";
            echo "<th align='left'><small>Yg. Mengajuakan PR</small></th>";
            echo "<th align='left'><small>Vendor</small></th>";
            echo "<th align='left'><small>Tgl. Input RFQ</small></th>";
            echo "<th align='left'><small>Nama Barang</small></th>";
            echo "<th align='left'><small>Spesifikasi</small></th>";
            echo "<th align='left'><small>Jumlah</small></th>";
            echo "<th align='left'><small>Harga</small></th>";
            echo "<th align='left'><small>Satuan</small></th>";
            echo "<th align='left'><small>Total</small></th>";
            echo "<th align='left'><small>ID PO</small></th>";
        echo "</tr>";

        $no=1;
        $query = "select * from $tmp01 order by idpr, nama_sup";
        $tampil0=mysqli_query($cnmy, $query);
        while ($row0=mysqli_fetch_array($tampil0)) {
            $cidinput=$row0['idpr_po'];
            $ntglinputrfq=$row0['tglinputrfq'];
            $nnmsupp=$row0['nama_sup'];
            $nidpr=$row0['idpr'];
            $nkrynmpr=$row0['nama_karyawan_pr'];
            $nnamabrg=$row0['namabarang'];
            $nspesifikasi1=$row0['spesifikasi1'];
            $njumlah=$row0['jumlah'];
            $nharga=$row0['harga'];
            $ntotalrp=$row0['totalrp'];
            $nsatuan=$row0['satuan'];
            $nidpo=$row0['idpo'];
            
            
            $ntglinputrfq= date("d/m/Y", strtotime($ntglinputrfq));
            $njumlah=number_format($njumlah,0,",",",");
            $nharga=number_format($nharga,0,",",",");
            $ntotalrp=number_format($ntotalrp,0,",",",");
            
            echo "<tr>";
            echo "<td nowrap>$no</td>";
            echo "<td nowrap>$nidpr</td>";
            echo "<td nowrap>$nkrynmpr</td>";
            echo "<td nowrap>$nnmsupp</td>";
            echo "<td nowrap>$ntglinputrfq</td>";
            echo "<td nowrap>$nnamabrg</td>";
            echo "<td nowrap>$nspesifikasi1</td>";
            echo "<td nowrap align='right'>$njumlah</td>";
            echo "<td nowrap align='right'>$nharga</td>";
            echo "<td nowrap>$nsatuan</td>";
            echo "<td nowrap align='right'>$ntotalrp</td>";
            echo "<td nowrap>$nidpo</td>";
            echo "</tr>";

            $no++;
        }

    echo "</table>";
    
    
    ?>
    
    
</BODY>


    <style>
        #tbltable {
            border-collapse: collapse;
        }
        th {
            font-size : 16px;
            padding:5px;
            background-color: #ccccff;
        }
        tr td {
            font-size : 12px;
        }
        tr td {
            padding : 3px;
        }
        tr:hover {background-color:#f5f5f5;}
        thead tr:hover {background-color:#cccccc;}
    </style>


</HTML>

<?PHP
hapusdata:
    mysqli_query($cnmy, "drop TEMPORARY table if EXISTS $tmp01");
    mysqli_query($cnmy, "drop TEMPORARY table if EXISTS $tmp02");
    mysqli_query($cnmy, "drop TEMPORARY table if EXISTS $tmp03");
    mysqli_close($cnmy);
?>