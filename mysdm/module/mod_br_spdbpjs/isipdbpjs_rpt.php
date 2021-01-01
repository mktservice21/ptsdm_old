<?PHP
    session_start();
    $mact=$_GET['act'];
    if ($_GET['ket']=="excel") {
        $now_=date("mdYhis");
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=Permintaan_Dana_BPJS_$now_.xls");
    }
    
    include("config/koneksimysqli.php");
    include("config/common.php");
    
    $picardid=$_SESSION['IDCARD'];
    $puserid=$_SESSION['USERID'];
    
    $now=date("mdYhis");
    $tmp00 =" dbtemp.tmprptpdmktdirl00_".$puserid."_$now ";
    $tmp01 =" dbtemp.tmprptpdmktdirl01_".$puserid."_$now ";
    $tmp02 =" dbtemp.tmprptpdmktdirl02_".$puserid."_$now ";
    
    $pmyidspd=$_GET['ispd'];
    //$parryidspd = explode("_", $pmyidspd);
    //$pidspd = $parryidspd[0];
    $pidspd = $pmyidspd;
    
    $query = "select * from dbmaster.t_spd_bpjs0 where idinput='$pidspd' AND IFNULL(stsnonaktif,'')<>'Y'";
    $tampil= mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($tampil);
    if ($ketemu==0) goto hapusdata;
    
    $row= mysqli_fetch_array($tampil);
    
    $pperiode=$row['periode'];
    $pbulan=$row['bulan'];
    $ptglaju=$row['tanggal'];
    $pmybln = date("F Y", strtotime($pbulan));
    $ptglpengajuan = date("d/m/Y", strtotime($ptglaju));
    
    $nodivisi="";
    
    $gmrheight = "100px";
    $ngbr_idinput="";
    $namapengaju_ttd_fin1="";
    $gbrttd_fin1="";
    $gbrttd_fin2="";
    $gbrttd_dir1="";
    $gbrttd_dir2="";
    $namapengaju_ttd1="";
    $namapengaju_ttd2="";
    
    $nmdir1="EVI KOSINA SANTOSO";
    $nmdir2="IRA BUDISUSETYO";
    
    
    $ntgl_apv1="";
    $ntgl_apv2="";
    $ntgl_apv_dir1="";
    $ntgl_apv_dir2="";
    
    
    $query = "select idinput, nodivisi, tgl_apv1, tgl_apv2, gbr_apv1, gbr_apv2, dir, tgl_dir, gbr_dir, dir2, tgl_dir2, gbr_dir2 from dbmaster.t_suratdana_br WHERE idinput='$pidspd'";
    $showkan= mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($showkan);
    if ($ketemu>0){
        $sh= mysqli_fetch_array($showkan);
        $nodivisi=$sh['nodivisi'];
        
        $gbrttd_fin1=$sh['gbr_apv1'];
        $gbrttd_fin2=$sh['gbr_apv2'];
        
        
        
        $ngbr_idinput=$sh['idinput'];
        
        if (!empty($gbrttd_fin1)) {
            $data="data:".$gbrttd_fin1;
            $data=str_replace(' ','+',$data);
            list($type, $data) = explode(';', $data);
            list(, $data)      = explode(',', $data);
            $data = base64_decode($data);
            $namapengaju_ttd_fin1="imgfin1_".$ngbr_idinput."TTDSPD_.png";
            file_put_contents('images/tanda_tangan_base64/'.$namapengaju_ttd_fin1, $data);
                
            if (!empty($sh['tgl_apv1']) AND $sh['tgl_apv1']<>"0000-00-00") $ntgl_apv1="Approved<br/>".date("d-m-Y", strtotime($sh['tgl_apv1']));
            
        }
        
        
        $piddir=$sh['dir'];
        if ($piddir!="0000001854" AND !empty($piddir)) {
            $query = "select nama from hrd.karyawan where karyawanid='$piddir'";
            $tampild= mysqli_query($cnmy, $query);
            $nd= mysqli_fetch_array($tampild);
            $nmdir1=$nd['nama'];
        }
        
        $gbrttd_dir1=$sh['gbr_dir'];
        $gbrttd_dir2=$sh['gbr_dir2'];
        
        if (!empty($gbrttd_dir1)) {
            $data="data:".$gbrttd_dir1;
            $data=str_replace(' ','+',$data);
            list($type, $data) = explode(';', $data);
            list(, $data)      = explode(',', $data);
            $data = base64_decode($data);
            $namapengaju_ttd1="imgdr1_".$ngbr_idinput."TTDSPD_.png";
            file_put_contents('images/tanda_tangan_base64/'.$namapengaju_ttd1, $data);
            
            if (!empty($sh['tgl_dir']) AND $sh['tgl_dir']<>"0000-00-00") $ntgl_apv_dir1="Approved<br/>".date("d-m-Y", strtotime($sh['tgl_dir']));
            
        }

        if (!empty($gbrttd_dir2)) {
            $data="data:".$gbrttd_dir2;
            $data=str_replace(' ','+',$data);
            list($type, $data) = explode(';', $data);
            list(, $data)      = explode(',', $data);
            $data = base64_decode($data);
            $namapengaju_ttd2="imgdr2_".$ngbr_idinput."TTDSPD_.png";
            file_put_contents('images/tanda_tangan_base64/'.$namapengaju_ttd2, $data);
            
            if (!empty($sh['tgl_dir2']) AND $sh['tgl_dir2']<>"0000-00-00") $ntgl_apv_dir2="Approved<br/>".date("d-m-Y", strtotime($sh['tgl_dir2']));
            
        }
        
    }
        
        
    
    $query = "select * from dbmaster.t_spd_bpjs where periode='$pperiode' AND IFNULL(bayar,0)<>0";
    $query = "create TEMPORARY table $tmp00 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "select a.*, b.nama nama_karyawan from $tmp00 a JOIN hrd.karyawan b on a.karyawanid=b.karyawanid";
    $query = "create TEMPORARY table $tmp01 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    
?>

<HTML>
<HEAD>
    <?PHP 
        echo "<title>Permintaan Dana BPJS</title>";
     
        if ($_GET['ket']!="excel") {
            echo "<meta http-equiv='Expires' content='Mon, 01 Jan 2050 1:00:00 GMT'>";
            echo "<meta http-equiv='Pragma' content='no-cache'>";
            echo "<link rel='shortcut icon' href='images/icon.ico' />";
            echo "<link href='css/laporanbaru.css' rel='stylesheet'>";
            
            header("Cache-Control: no-cache, must-revalidate");
        }
        
    ?>
    <style> .str{ mso-number-format:\@; } </style>
</HEAD>

<BODY>
    
    <div id="kotakjudul" style="margin-bottom: -30px;">
        <div id="isikiri">
            <table class='tjudul' width='100%'>
                <?PHP
                    echo "<tr><td width='250px' nowrap><b>Permintaan Dana BPJS </b></td><td>$nodivisi</td></tr>";
                    $nket_status="**Petty Cash";
                    echo "<tr><td width='150px'><b>Bulan : </b></td><td align='left'><b>$pmybln</b></td></tr>";
                    echo "<tr><td width='150px'><b>Tgl. : </b></td><td align='left'><b>$ptglpengajuan</b></td></tr>";
                    
                ?>  
            </table>
        </div>
        <div id="isikanan">
            
        </div>
        <div class="clearfix"></div>
    </div>
    
    
    <div class="clearfix"></div>
    <br/>&nbsp;
    <table id='datatable2' class='table table-striped table-bordered example_2' border="1px solid black">
        <thead>
            <tr style='background-color:#cccccc; font-size: 13px;'>
                <th width='10px'>No</th>
                <th align="center" nowrap>ID</th>
                <th align="center" nowrap>NAMA</th>
                <th align="center">KELAS</th>
                <th align="center" nowrap>BAYAR</th>
                <th align="center">KETERANGAN</th>
            </tr>
        </thead>
        <tbody>
            <?PHP
            $no=1;
            $ptotal=0;
            $query = "select * from $tmp01 order by nama_karyawan";
            $tampil= mysqli_query($cnmy, $query);
            $ketemu= mysqli_num_rows($tampil);
            if ($ketemu>0) {
                while ($row= mysqli_fetch_array($tampil)) {
                    $nkaryawanid=$row['karyawanid'];
                    $nkaryawannm=$row['nama_karyawan'];
                    $nkelas=$row['kelas'];
                    $nbayar=$row['bayar'];
                    $nketerangan=$row['keterangan'];

                    if ($nkelas=="0") $nkelas="";

                    $ptotal=(double)$ptotal+(double)$nbayar;
                    $nbayar=number_format($nbayar,0,",",",");


                    echo "<tr>";
                    echo "<td nowrap>$no</td>";
                    echo "<td nowrap class='str'>$nkaryawanid</td>";
                    echo "<td nowrap>$nkaryawannm</td>";
                    echo "<td nowrap>$nkelas</td>";
                    echo "<td nowrap align='right'>$nbayar</td>";
                    echo "<td >$nketerangan</td>";
                    echo "</tr>";

                    $no++;
                }

                $ptotal=number_format($ptotal,0,",",",");

                echo "<tr>";
                echo "<td nowrap colspan='4' align='right'><b>TOTAL : </b></td>";
                echo "<td nowrap align='right'><b>$ptotal</b></td>";
                echo "<td class='divnone'></td>";
                echo "</tr>";

            }

            ?>
        </tbody>

    </table>
    
    <br/>&nbsp;
    <?PHP
    echo "<table class='tjudul' width='100%'>";
        echo "<tr>";
            echo "<td align='center'>";
            echo "Yg. Membuat,";
            if (!empty($namapengaju_ttd_fin1))
                echo "<br/><img src='images/tanda_tangan_base64/$namapengaju_ttd_fin1' height='$gmrheight'><br/>";
            else
                echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
            echo "<b>WIDYA HASTUTI</b></td>";
            
            echo "<td align='center'>";
            echo "Menyetujui,";
            if (!empty($namapengaju_ttd1))
                echo "<br/><img src='images/tanda_tangan_base64/$namapengaju_ttd1' height='$gmrheight'><br/>";
            else
                echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
            echo "<b>$nmdir1</b></td>";
            
            
            echo "<td align='center'>";
            echo "Mengetahui,";
            if (!empty($namapengaju_ttd2))
                echo "<br/><img src='images/tanda_tangan_base64/$namapengaju_ttd2' height='$gmrheight'><br/>";
            else
                echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
            echo "<b>$nmdir2</b></td>";
        echo "</tr>";
    echo "</table>";
    ?>
    <br/>&nbsp;
</BODY>


</HTML>

<?PHP
hapusdata:
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp00");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp01");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp02");
    mysqli_close($cnmy);
?>