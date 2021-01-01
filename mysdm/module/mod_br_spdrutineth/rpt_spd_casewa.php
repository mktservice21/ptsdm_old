<?php

    session_start();
    $ppilihrpt=$_GET['ket'];
    if ($ppilihrpt=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=REKAP CA SEWA RUMAH.xls");
    }
    
    $nmodule=$_GET['module'];
    include("config/koneksimysqli.php");
    include("config/common.php");
    $cnit=$cnmy;
    
    $figroupuser=$_SESSION['GROUP'];
    $pses_divisi=$_SESSION['DIVISI'];
    $pses_idcard=$_SESSION['IDCARD'];
    
    
    if (!isset($_GET['ispd'])) {
        goto hapusdata;
    }
    
    
    $pidspd=$_GET['ispd'];
    
    $now=date("mdYhis");
    $tmp01 =" dbtemp.RPTREKOTCAS01_".$_SESSION['USERID']."_$now ";
    $tmp02 =" dbtemp.RPTREKOTCAS02_".$_SESSION['USERID']."_$now ";
    $tmp03 =" dbtemp.RPTREKOTCAS03_".$_SESSION['USERID']."_$now ";
    
    $query = "select a.idinput, a.divisi, a.tgl, a.tglspd, a.coa4, a.nomor, a.nodivisi, a.jumlah, b.bridinput, b.amount 
        from dbmaster.t_suratdana_br a JOIN dbmaster.t_suratdana_br1 b on a.idinput=b.idinput
        WHERE a.idinput='$pidspd'";
    $query = "create TEMPORARY table $tmp01 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "select a.idsewa, a.tgl, a.COA4, b.NAMA4, a.divisi, a.icabangid, c.nama nama_cabang, a.areaid, e.nama nama_area, 
        a.karyawanid, d.nama nama_karyawan, a.periode, a.tglmulai, a.tglakhir, a.keterangan, a.jumlah  
        from dbmaster.t_sewa a LEFT JOIN dbmaster.coa_level4 b on a.COA4=b.COA4 
        LEFT JOIN MKT.icabang c on a.icabangid=c.icabangid 
        LEFT JOIN hrd.karyawan d on a.karyawanid=d.karyawanid 
        LEFT JOIN MKT.iarea e on a.icabangid=e.icabangid AND a.areaid=e.areaId 
        WHERE a.idsewa IN (select distinct IFNULL(bridinput,'') FROM $tmp01)";
    $query = "create TEMPORARY table $tmp02 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $nodivisi="";
    $ntgl_pd="";
    
    $query = "select distinct tgl, divisi, nomor, nodivisi, jumlah FROM $tmp01";
    $tampil_s= mysqli_query($cnmy, $query);
    $ketemu_s= mysqli_num_rows($tampil_s);
    if ($ketemu_s>0) {
        $rs= mysqli_fetch_array($tampil_s);
        
        $nodivisi=$rs['nodivisi'];
        $ntgl_pd=$rs['tgl'];
        $ntgl_pd = date("d F Y", strtotime($ntgl_pd));
        
    }
    
    $ntgl_trans="";
    $nnobukti="";
    $query = "select idinput, nobukti, nodivisi, jumlah, tanggal from dbmaster.t_suratdana_bank WHERE idinput='$pidspd' and stsinput='K' and stsnonaktif<>'Y'";
    $tampil_s= mysqli_query($cnmy, $query);
    $ketemu_s= mysqli_num_rows($tampil_s);
    if ($ketemu_s>0) {
        $rs= mysqli_fetch_array($tampil_s);
        $nnobukti=$rs['nobukti'];
        $ntgl_trans=$rs['tanggal'];
        $ntgl_trans = date("d-m-Y", strtotime($ntgl_trans));
        
    }
    
    
?>


<head>
    <?PHP 
        echo "<title>CASH ADVANCE BIAYA RUTIN SEWA</title>";
        if ($_GET['ket']!="excel") {
            echo "<meta http-equiv='Expires' content='Mon, 01 Jan 2019 1:00:00 GMT'>";
            echo "<meta http-equiv='Pragma' content='no-cache'>";
            echo "<link rel='shortcut icon' href='images/icon.ico' />";
            echo "<link href='css/laporanbaru.css' rel='stylesheet'>";
            
            header("Cache-Control: no-cache, must-revalidate");
        }
        
    ?>
</head>

<body>
    
    <div id="kotakjudul" style="margin-bottom: -30px;">
        <div id="isikiri">
            <table class='tjudul' width='100%'>
                <?PHP
                    echo "<tr><td width='200px'>To : </td><td>Sdr. Lina (Finance)</td></tr>";
                    echo "<tr><td width='150px'><b>&nbsp;</b></td><td></td></tr>";
                    echo "<tr><td width='150px'>Laporan Cash Advance : </td><td>$nodivisi</td></tr>";                    
                    echo "<tr><td width='150px'>** Klaim : </td><td><b>$ntgl_pd</b></td></tr>";
                    
                ?>  
            </table>
        </div>
        <div id="isikanan">
            
        </div>
        <div class="clearfix"></div>
    </div>
    
    
    <br/>&nbsp;<br/>&nbsp;
    <table id='datatable2' class='table table-striped table-bordered example_2' border="1px solid black">
        <thead>
            <tr style='background-color:#cccccc; font-size: 13px;'>
                <th align="center">Date</th>
                <th align="center">Bukti</th>
                <th align="center">Kode</th>
                <th align="center">Perkiraan</th>
                <th align="center">Cabang</th>
                <th align="center">NO</th>
                <th align="center">Nama</th>
                <th align="center">Jenis</th>
                <th align="center">Description</th>
                <th align="center">Lain2</th>
                <th align="center">Debit</th>
                <th align="center">Credit</th>
                <th align="center">Saldo</th>
            </tr>
        </thead>
        <tbody>
            <?PHP
                $ptotal=0;
                $no=1;
                $query = "select * from $tmp02 order by COA4, nama_cabang";
                $tampil=mysqli_query($cnmy, $query);
                while ($row= mysqli_fetch_array($tampil)) {
                    
                    $pbrid = $row['idsewa'];
                    
                    $pcoa = $row['COA4'];
                    $pnmcoa = $row['NAMA4'];
                    $pnmcabang = $row['nama_cabang'];
                    $pnmarea = $row['nama_area'];
                    $pnmkaryawan = $row['nama_karyawan'];
                    
                    $ptglmulai = $row['tglmulai'];
                    $ptglakhir = $row['tglakhir'];
                    
                    $ptglmulai =date("F Y", strtotime($ptglmulai));
                    $ptglakhir =date("F Y", strtotime($ptglakhir));
                    
                    $jenis="CA Sewa Rumah";
                    
                    $pjumlah=$row['jumlah'];
                    
                    $ptotal=(double)$ptotal+(double)$pjumlah;
                    
                    if ($figroupuser=="28")
                        $pjumlah=number_format($pjumlah,0,".",".");
                    else
                        $pjumlah=number_format($pjumlah,0,",",",");

                    
                    echo "<tr>";
                    echo "<td nowrap>$ntgl_trans</td>";
                    echo "<td nowrap>$nnobukti</td>";
                    echo "<td nowrap>$pcoa</td>";
                    echo "<td nowrap>$pnmcoa</td>";
                    echo "<td nowrap>$pnmcabang</td>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap>$pnmkaryawan</td>";
                    echo "<td nowrap>$jenis</td>";
                    echo "<td nowrap>Periode $ptglmulai s/d. $ptglakhir</td>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap align='right'>$pjumlah</td>";
                    echo "<td nowrap align='right'></td>";
                    echo "</tr>";

                        echo "<tr>";
                        echo "<td nowrap></td>";
                        echo "<td nowrap></td>";
                        echo "<td nowrap></td>";
                        echo "<td nowrap></td>";
                        echo "<td nowrap></td>";
                        echo "<td nowrap></td>";
                        echo "<td nowrap></td>";
                        echo "<td nowrap></td>";
                        echo "<td nowrap></td>";
                        echo "<td nowrap></td>";
                        echo "<td nowrap></td>";
                        echo "<td nowrap align='right'></td>";
                        echo "<td nowrap align='right'>$pjumlah</td>";
                        echo "</tr>";
                }
                
                echo "<tr>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap align='right'></td>";
                echo "<td nowrap align='right'><b></b></td>";
                echo "</tr>";
                if ($figroupuser=="28")
                    $ptotal=number_format($ptotal,0,".",".");
                else
                    $ptotal=number_format($ptotal,0,",",",");
                echo "<tr>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap align='right'></td>";
                echo "<td nowrap align='right'><b>$ptotal</b></td>";
                echo "</tr>";
                
                echo "<tr>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap align='right'></td>";
                echo "<td nowrap align='right'><b></b></td>";
                echo "</tr>";
            ?>
        </tbody>
    </table>
    
    <br/>&nbsp;<br/>&nbsp;
    
    
    
    
</body>
</html>

    
<?PHP
hapusdata:
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp01");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp02");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp03");

    mysqli_close($cnmy);
?>