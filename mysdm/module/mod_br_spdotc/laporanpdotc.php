<?PHP
    session_start();
    $mact=$_GET['act'];
    $ppilihrpt="";
    if (isset($_GET['ket'])) $ppilihrpt=$_GET['ket'];
    
    if ($ppilihrpt=="excel") {
        $now_=date("mdYhis");
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=Rekap_Permohonan_Dana_BR_OTC_$now_.xls");
    }
    
    include("config/koneksimysqli.php");
    include("config/common.php");
    
    
    $idinputspd=$_GET['ispd'];
    
    
    $puserid=$_SESSION['USERID'];
    $now=date("mdYhis");
    $tmp01 =" dbtemp.tmprptlappdotcnn01_".$puserid."_$now ";
    $tmp02 =" dbtemp.tmprptlappdotcnn02_".$puserid."_$now ";
    $tmp03 =" dbtemp.tmprptlappdotcnn03_".$puserid."_$now ";
    $tmp04 =" dbtemp.tmprptlappdotcnn04_".$puserid."_$now ";
    
    
    $query = "select a.idinput, a.tgl, a.tglspd, a.divisi, a.nodivisi, a.nomor, a.kodeid, a.subkode, a.jenis_rpt, 
        a.lampiran, a.tglf, a.dir, 
        a.gbr_apv1, a.gbr_apv2, a.gbr_apv3, a.gbr_dir, a.gbr_dir2,
        b.tanggal as tglkeluar, b.nobukti,
        c.bridinput, c.amount, c.jml_adj, c.ketadj1, c.ketadj2, a.keterangan, c.urutan, 
        c.trans_ke, a.tgl_apv1, a.tgl_apv2, a.tgl_dir, a.tgl_dir2  
        from dbmaster.t_suratdana_br a 
            LEFT JOIN 
        (select DISTINCT idinput, IFNULL(bridinput,'') as bridinput, amount, jml_adj, aktivitas1 as ketadj1, aktivitas2 as ketadj2, urutan, trans_ke from dbmaster.t_suratdana_br1 WHERE idinput='$idinputspd') as c 
            on a.idinput=c.idinput
            LEFT JOIN
        (select idinput, tanggal, nobukti from dbmaster.t_suratdana_bank WHERE idinput='$idinputspd' AND stsinput='K' AND IFNULL(stsnonaktif,'')<>'Y' LIMIT 1) as b 
        on a.idinput=b.idinput
        WHERE a.idinput='$idinputspd'";
    
    $query = "create TEMPORARY table $tmp01 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp01 SET urutan=0 WHERE tgl<'2020-08-21' AND idinput<>'1932'"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "select a.brotcid, a.icabangid_o, b.nama nama_cabang, a.real1, a.bankreal1, a.norekreal1, "
            . " a.jumlah, a.realisasi, "
            . " a.keterangan1, a.keterangan2, a.batal "
            . " from hrd.br_otc a "
            . " LEFT JOIN MKT.icabang_o b on a.icabangid_o=b.icabangid_o WHERE "
            . " a.brotcid IN (select DISTINCT IFNULL(bridinput,'') FROM $tmp01)";
    $query = "create TEMPORARY table $tmp02 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "select a.brotcid, a.icabangid_o, b.nama nama_cabang, a.real1, a.bankreal1, a.norekreal1, "
            . " a.jumlah, a.realisasi, "
            . " a.keterangan1, a.keterangan2, 'Y' as batal "
            . " from dbmaster.backup_br_otc a "
            . " LEFT JOIN MKT.icabang_o b on a.icabangid_o=b.icabangid_o WHERE "
            . " a.brotcid IN (select DISTINCT IFNULL(bridinput,'') FROM $tmp01)";
    $query = "create TEMPORARY table $tmp03 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp02 a JOIN $tmp03 b on a.brotcid=b.brotcid SET a.batal=b.batal"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "DELETE FROM $tmp03 WHERE brotcid IN (select DISTINCT IFNULL(brotcid,'') FROM $tmp02)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "INSERT INTO $tmp02 SELECT * FROM $tmp03";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    mysqli_query($cnmy, "drop temporary table $tmp03");
    
    $query = "select brotcid, 'Y' batal from hrd.br_otc_reject WHERE brotcid IN (select DISTINCT IFNULL(bridinput,'') FROM $tmp01)";
    $query = "create TEMPORARY table $tmp03 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp02 a JOIN $tmp03 b on a.brotcid=b.brotcid SET a.batal=b.batal"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    mysqli_query($cnmy, "drop temporary table $tmp03");
    
    
    $query = "UPDATE $tmp02 a JOIN dbmaster.cabang_otc b on a.icabangid_o=b.cabangid_ho SET a.nama_cabang=b.initial WHERE IFNULL(nama_cabang,'')=''"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "ALTER TABLE $tmp02 ADD COLUMN urutan INT(4), ADD COLUMN grp1 VARCHAR(10)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp02 a JOIN $tmp01 b on a.brotcid=b.bridinput SET a.urutan=b.urutan, a.jumlah=b.amount";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp02 SET urutan=0 WHERE IFNULL(urutan,'')=''"; 
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }    
    
    
    $query = "UPDATE $tmp02 SET grp1=CONCAT('GP',IFNULL(urutan,'')) WHERE IFNULL(urutan,'')<>'0'"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp02 a JOIN dbmaster.cabang_otc b on a.icabangid_o=b.cabangid_ho SET a.grp1=b.group1 WHERE IFNULL(grp1,'')=''"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    
    $query = "select * from $tmp02";
    $query = "create TEMPORARY table $tmp03 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    
    
    $gmrheight = "100px";
    $ngbr_idinput="";
    $gbrttd_fin1="";
    $gbrttd_fin2="";
    $gbrttd_dir1="";
    $gbrttd_dir2="";
    
    $namapengaju_ttd_fin1="";
    $namapengaju_ttd_fin2="";
    $namapengaju_ttd_fin3="";
    
    $namapengaju_ttd1="";
    $namapengaju_ttd2="";
    
    $nnama_ss_mktdir1="FARIDA SOEWANTO";
    $nnama_ss_mktdir2="EVI KOSINA SANTOSO";
    
    $nnama_ss_mktdir=$nnama_ss_mktdir1;
    
    $ntgl_apv1="";
    $ntgl_apv2="";
    $ntgl_apv_dir1="";
    $ntgl_apv_dir2="";
    
    
    
    $query = "select DISTINCT tgl, idinput, nodivisi, jenis_rpt, lampiran, tglf, "
            . " dir, gbr_apv1, gbr_apv2, gbr_apv3, gbr_dir, gbr_dir2 from $tmp01";
    $tampilkan=mysqli_query($cnmy, $query);
    $ra= mysqli_fetch_array($tampilkan);
    
    $noslipurut=$ra['nodivisi'];
    $pjenis_rpt=$ra['jenis_rpt'];
    $jenis=$ra['lampiran'];
    
    
    $tgl01=date("Y-m-d");
    if (!empty($ra['tglf'])) $tgl01=$ra['tglf'];
    
    
    
    $periode= date("d-M-Y", strtotime($tgl01));
    
    $iapprovedirut=true;
    
    $tgljakukannya=$ra['tgl'];
    if ($tgljakukannya=="0000-00-00") $tgljakukannya="";
    if (!empty($tgljakukannya)) $tgljakukannya = date("Ymd", strtotime($tgljakukannya));
                        
    $passdirid=$ra['dir'];
    if ($passdirid=="0000002403") $nnama_ss_mktdir=$nnama_ss_mktdir2;
    else{
        if (!empty($tgljakukannya)) {
            if ((double)$tgljakukannya>='20200629') {
                $nnama_ss_mktdir=$nnama_ss_mktdir2;
            }
        }
    }
            
    $ngbr_idinput=$ra['idinput'];
    
    $gbrttd_fin1=$ra['gbr_apv1'];
    $gbrttd_fin2=$ra['gbr_apv2'];
    $gbrttd_fin3=$ra['gbr_apv3'];

    $gbrttd_dir1=$ra['gbr_dir'];
    $gbrttd_dir2=$ra['gbr_dir2'];
            
            
    
    if (!empty($gbrttd_fin1)) {
        $data="data:".$gbrttd_fin1;
        $data=str_replace(' ','+',$data);
        list($type, $data) = explode(';', $data);
        list(, $data)      = explode(',', $data);
        $data = base64_decode($data);
        $namapengaju_ttd_fin1="imgfin1_".$ngbr_idinput."TTDSPD_.png";
        file_put_contents('images/tanda_tangan_base64/'.$namapengaju_ttd_fin1, $data);

        if (!empty($ra['tgl_apv1']) AND $ra['tgl_apv1']<>"0000-00-00") $ntgl_apv1="Approved<br/>".date("d-m-Y", strtotime($ra['tgl_apv1']));

    }

    if (!empty($gbrttd_fin2)) {
        $data="data:".$gbrttd_fin2;
        $data=str_replace(' ','+',$data);
        list($type, $data) = explode(';', $data);
        list(, $data)      = explode(',', $data);
        $data = base64_decode($data);
        $namapengaju_ttd_fin2="imgfin2_".$ngbr_idinput."TTDSPD_.png";
        file_put_contents('images/tanda_tangan_base64/'.$namapengaju_ttd_fin2, $data);

        if (!empty($ra['tgl_apv2']) AND $ra['tgl_apv2']<>"0000-00-00") $ntgl_apv2="Approved<br/>".date("d-m-Y", strtotime($ra['tgl_apv2']));

    }

    if (!empty($gbrttd_fin3)) {
        $data="data:".$gbrttd_fin3;
        $data=str_replace(' ','+',$data);
        list($type, $data) = explode(';', $data);
        list(, $data)      = explode(',', $data);
        $data = base64_decode($data);
        $namapengaju_ttd_fin3="imgfin3_".$ngbr_idinput."TTDSPD_.png";
        file_put_contents('images/tanda_tangan_base64/'.$namapengaju_ttd_fin3, $data);

        if (!empty($ra['tgl_apv2']) AND $ra['tgl_apv2']<>"0000-00-00") $ntgl_apv2="Approved<br/>".date("d-m-Y", strtotime($ra['tgl_apv2']));

    }

    if (!empty($gbrttd_dir1)) {
        $data="data:".$gbrttd_dir1;
        $data=str_replace(' ','+',$data);
        list($type, $data) = explode(';', $data);
        list(, $data)      = explode(',', $data);
        $data = base64_decode($data);
        $namapengaju_ttd1="imgdr1_".$ngbr_idinput."TTDSPD_.png";
        file_put_contents('images/tanda_tangan_base64/'.$namapengaju_ttd1, $data);

        if (!empty($ra['tgl_dir']) AND $ra['tgl_dir']<>"0000-00-00") $ntgl_apv_dir1="Approved<br/>".date("d-m-Y", strtotime($ra['tgl_dir']));

    }

    if (!empty($gbrttd_dir2)) {
        $data="data:".$gbrttd_dir2;
        $data=str_replace(' ','+',$data);
        list($type, $data) = explode(';', $data);
        list(, $data)      = explode(',', $data);
        $data = base64_decode($data);
        $namapengaju_ttd2="imgdr2_".$ngbr_idinput."TTDSPD_.png";
        file_put_contents('images/tanda_tangan_base64/'.$namapengaju_ttd2, $data);

        if (!empty($ra['tgl_dir2']) AND $ra['tgl_dir2']<>"0000-00-00") $ntgl_apv_dir2="Approved<br/>".date("d-m-Y", strtotime($ra['tgl_dir2']));

    }
            
    
    
    $snamarpt_judul="Sudah Ada Bukti";
    if ($pjenis_rpt=="S") {
        $snamarpt_judul="Kasbon Surabaya";
    }elseif ($pjenis_rpt=="B") {
        $snamarpt_judul="Petty Cash Marketing";
        $iapprovedirut=false;
    }elseif ($pjenis_rpt=="K") {
        $snamarpt_judul="Klaim PC-M";
    }else{
        $snamarpt_judul="Sudah Ada Bukti";
    }
?>


<HTML>
<HEAD>
    <?PHP 
        echo "<title>Rekap Permohonana Dana BR</title>";
        if ($ppilihrpt!="excel") {
            echo "<meta http-equiv='Expires' content='Mon, 01 Jan 2050 1:00:00 GMT'>";
            echo "<meta http-equiv='Pragma' content='no-cache'>";
            echo "<link rel='shortcut icon' href='images/icon.ico' />";
            echo "<link href='css/laporanbaru.css' rel='stylesheet'>";
            
            header("Cache-Control: no-cache, must-revalidate");
            ?>

            <script>
                var EventUtil = new Object;
                EventUtil.formatEvent = function (oEvent) {
                        return oEvent;
                }


                function goto2(pForm_,pPage_) {
                   document.getElementById(pForm_).action = pPage_;
                   document.getElementById(pForm_).submit();

                }
            </script>
            <style>
            @page 
            {
                /*size: auto;   /* auto is the current printer page size */
                /*margin: 0mm;  /* this affects the margin in the printer settings */
                /*margin-left: 15mm;  /* this affects the margin in the printer settings */
                /*margin-right: 7mm;  /* this affects the margin in the printer settings */
                /*margin-top: 10mm;  /* this affects the margin in the printer settings */
                /*margin-bottom: 10mm;  /* this affects the margin in the printer settings */
                /*size: landscape; /*   */
            }
            </style>
            
            <?PHP
        }
        
    ?>
</HEAD>

<BODY>
    
    <center><h2><u>REKAP DATA PERMOHONAN DANA BR</u></h2></center>
    
    
    <div id="kotakjudul">
        <div id="isikiri">
            <table class='tjudul' width='100%'>
                <tr><td><b>To : Sdri. Lina (Finance)</b></td></tr>
                <tr><td><b>Rekap Budget Request (BR) Team OTC</b></td></tr>

                <tr><td><b><?PHP echo "$snamarpt_judul"; ?></b></td></tr>
                <!--
                <?PHP if ($jenis=="Y") { ?>
                <tr><td><b>Permintaan Uang Muka</b></td></tr>
                <?PHP }else{ ?>
                <tr><td><b>Petty Cash Marketing</b></td></tr>
                <?PHP } ?>
                -->
            </table>
        </div>
        <div id="isikanan">
            <table class='tjudul' width='100%'>
                <tr><td>&nbsp;</td></tr>
                <tr>
                    <td align='right'>
                        <?PHP
                        echo "$noslipurut";
                        ?>
                    </td>
                </tr>
                <tr><td align='right'><?PHP echo $periode; ?></td></tr>
            </table>
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="clearfix"></div>
        
        <table id='datatable2' class='table table-striped table-bordered example_2'>
            <thead>
                <tr style='background-color:#cccccc; font-size: 13px;'>
                <th align="center">Daerah</th>
                <th align="center">Keterangan</th>
                <th align="center">Realisasi</th>
                <th align="center">No.Rek</th>
                <th align="center">Kredit</th>
                <th align="center">No</th>
            </thead>
            <tbody>
            <?PHP
                
                $adadata=false;
                $gtotal=0;
                $no=1;
                
                
                // ke 2
                $sql = "select distinct grp1 from $tmp03 WHERE IFNULL(urutan,0)<>'0' order by grp1";
                $tampilk = mysqli_query($cnmy, $sql);
                while ($rk = mysqli_fetch_array($tampilk)) {
                   $pugroup=$rk['grp1'];
                   
                    $sql2 = "select * from $tmp03 where IFNULL(urutan,0)<>'0' AND grp1='$pugroup' order by keterangan1";
                    $tampilk2 = mysqli_query($cnmy, $sql2);
                    $sudah="FALSE";
                    $jumlahsub=0;
                    while ($rk2 = mysqli_fetch_array($tampilk2)) {
                        
                        $cabang=$rk2['icabangid_o'];
                        $nmcabang=$rk2['nama_cabang'];
                        $keterangan=$rk2['keterangan1'];
                        $realisasi=$rk2['real1'];
                        $norek=$rk2['norekreal1'];
                        $bankrek=$rk2['bankreal1'];

                        $ketbanknya="";
                        if (empty($bankrek) AND empty($norek))
                            $ketbanknya="";
                        else
                            $ketbanknya=$bankrek." : ".$norek;

                        
                        $prpjumlah=$rk2['jumlah'];
                        
                        $jumlah=0;
                        if (!empty($prpjumlah)) {
                            $jumlah=number_format($prpjumlah,0,",",",");
                            $jumlahsub = (double)$jumlahsub+(DOUBLE)$prpjumlah;
                        }

                        
                        echo "<tr>";
                        echo "<td style='padding-left:5px;' nowrap>$nmcabang</td>";
                        echo "<td>$keterangan</td>";
                        echo "<td>$realisasi</td>";
                        if ($sudah=="FALSE")
                            echo "<td><b>$ketbanknya</b></td>";
                        else
                            echo "<td></td>";

                        echo "<td align='right'>$jumlah</td>";
                        if ($sudah=="FALSE") {
                            echo "<td align='center'>$no</td>";
                            $no++;
                        }else
                            echo "<td></td>";
                        echo "</tr>";

                        $sudah="TRUE";
                        
                        $adadata=true;
                        
                    }
                    
                    $gtotal=(double)$gtotal+(double)$jumlahsub;
                    //subtotal
                    $jumlahnya=number_format($jumlahsub,0,",",",");
                    echo "<tr>";
                    echo "<td colspan=4></td>";
                    echo "<td align='right'><b>$jumlahnya</b></td>";
                    echo "<td>&nbsp;</td>";
                    echo "</tr>";
                    echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
                    
                }
                
                // end ke 2
                
                
                $sql = "select distinct icabangid_o, real1, norekreal1, bankreal1 from $tmp03 WHERE IFNULL(urutan,0)='0' order by nama_cabang, real1";
                $tampil = mysqli_query($cnmy, $sql);
                while ($r = mysqli_fetch_array($tampil)) {
                    $cabang1=$r['icabangid_o'];
                    $real1=$r['real1'];
                    $norek1=$r['norekreal1'];
                    $bankrek1=$r['bankreal1'];

                    $sql2 = "select * from $tmp03 where IFNULL(urutan,0)='0' AND icabangid_o='$cabang1' AND real1='$real1' AND norekreal1='$norek1' AND bankreal1='$bankrek1' order by keterangan1";
                    $tampil2 = mysqli_query($cnmy, $sql2);
                    $sudah="FALSE";
                    $jumlahsub=0;
                    while ($r2 = mysqli_fetch_array($tampil2)) {
                        $cabang=$r2['icabangid_o'];
                        $nmcabang=$r2['nama_cabang'];
                        $keterangan=$r2['keterangan1'];
                        $realisasi=$r2['real1'];
                        $norek=$r2['norekreal1'];
                        $bankrek=$r2['bankreal1'];

                        $ketbanknya="";
                        if (empty($bankrek) AND empty($norek))
                            $ketbanknya="";
                        else
                            $ketbanknya=$bankrek." : ".$norek;

                        
                        $prpjumlah=$r2['jumlah'];
                        
                        $jumlah=0;
                        if (!empty($prpjumlah)) {
                            $jumlah=number_format($prpjumlah,0,",",",");
                            $jumlahsub = (double)$jumlahsub+(DOUBLE)$prpjumlah;
                        }

                        
                        echo "<tr>";
                        echo "<td style='padding-left:5px;' nowrap>$nmcabang</td>";
                        echo "<td>$keterangan</td>";
                        echo "<td>$realisasi</td>";
                        if ($sudah=="FALSE")
                            echo "<td><b>$ketbanknya</b></td>";
                        else
                            echo "<td></td>";

                        echo "<td align='right'>$jumlah</td>";
                        if ($sudah=="FALSE") {
                            echo "<td align='center'>$no</td>";
                            $no++;
                        }else
                            echo "<td></td>";
                        echo "</tr>";

                        $sudah="TRUE";
                        
                        $adadata=true;
                        
                    }
                    
                    $gtotal=(double)$gtotal+(double)$jumlahsub;
                    //subtotal
                    $jumlahnya=number_format($jumlahsub,0,",",",");
                    echo "<tr>";
                    echo "<td colspan=4></td>";
                    echo "<td align='right'><b>$jumlahnya</b></td>";
                    echo "<td>&nbsp;</td>";
                    echo "</tr>";
                    echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
                    
                    
                }
                
                
                $gtotalnya=number_format($gtotal,0,",",",");
                echo "<tr style='background-color:#ffcc99;'>";
                echo "<td colspan=2></td>";
                echo "<td colspan=2 align='center'><b>GRAND TOTAL</b></td>";
                echo "<td align='right'><b>$gtotalnya</b></td>";
                echo "<td>&nbsp;</td>";
                echo "</tr>";
                
                
            ?>
            </tbody>
        </table>
        
        <?PHP
            if ($noslipurut=="024/BROTC-GAJI/IX/19") {
                echo "Tanda Tangan Pada Lampiran Manual, karena disistem belum ada ttd pak Asykur. "
                . "<a href='eksekusi3.php?module=bukafilenya&act=files&id=BR 024 - Gaji SPG Sep 2019.pdf'>Klik di sini</a><br/>";
            }elseif ($noslipurut=="026/BROTC-GAJI/XI/19") {
                echo "Gaji SPG tambahan periode Oktober, Approve manual. "
                . "<a href='eksekusi3.php?module=bukafilenya&act=files&id=BR 026 - tambahan gaji okt.pdf'>Klik di sini</a><br/>";
            }
        ?>
        <br/>&nbsp;
        
        
        <?PHP
            
            if ($iapprovedirut==true) {

                echo "<table class='tjudul' width='100%'>";
                    echo "<tr>";

                        echo "<td align='center'>";
                        echo "Dibuat oleh,";
                        if (!empty($namapengaju_ttd_fin1))
                            echo "<br/><img src='images/tanda_tangan_base64/$namapengaju_ttd_fin1' height='$gmrheight'><br/>";
                        else
                            echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                        echo "<b>DESI RATNA DEWI</b></td>";

                        if ($noslipurut!="172/BR-OTC/IX/19") {

                            if ($pjenis_rpt=="G") {//$noslipurut=="026/BROTC-GAJI/XI/19"

                                echo "<td align='center'>";
                                echo "Checker,";
                                if (!empty($namapengaju_ttd_fin2))
                                    echo "<br/><img src='images/tanda_tangan_base64/$namapengaju_ttd_fin2' height='$gmrheight'><br/>";
                                else
                                    echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                                echo "<b>SAIFUL RAHMAT</b></td>";

                                echo "<td align='center'>";
                                echo "Menyetujui,";
                                if (!empty($namapengaju_ttd_fin3))
                                    echo "<br/><img src='images/tanda_tangan_base64/$namapengaju_ttd_fin3' height='$gmrheight'><br/>";
                                else
                                    echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                                echo "<b>M. ASYKUR</b></td>";


                                echo "<td align='center'>";
                                echo "";
                                if (!empty($namapengaju_ttd1))
                                    echo "<br/><img src='images/tanda_tangan_base64/$namapengaju_ttd1' height='$gmrheight'><br/>";
                                else
                                    echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                                echo "<b>$nnama_ss_mktdir</b></td>";

                            }else{

                                echo "<td align='center'>";
                                echo "Checker,";
                                if (!empty($namapengaju_ttd_fin2))
                                    echo "<br/><img src='images/tanda_tangan_base64/$namapengaju_ttd_fin2' height='$gmrheight'><br/>";
                                else
                                    echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                                echo "<b>SAIFUL RAHMAT</b></td>";


                                echo "<td align='center'>";
                                echo "";
                                    echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                                echo "</td>";


                                echo "<td align='center'>";
                                echo "Menyetujui,";
                                if (!empty($namapengaju_ttd1))
                                    echo "<br/><img src='images/tanda_tangan_base64/$namapengaju_ttd1' height='$gmrheight'><br/>";
                                else
                                    echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                                echo "<b>$nnama_ss_mktdir</b></td>";

                            }


                        }else{

                            echo "<td align='center'>";
                            echo "Checker,";
                            if (!empty($namapengaju_ttd_fin2))
                                echo "<br/><img src='images/tanda_tangan_base64/$namapengaju_ttd_fin2' height='$gmrheight'><br/>";
                            else
                                echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                            echo "<b>SAIFUL RAHMAT</b></td>";


                        }

                        echo "<td align='center'>";
                        echo "Mengetahui,";
                        if (!empty($namapengaju_ttd2))
                            echo "<br/><img src='images/tanda_tangan_base64/$namapengaju_ttd2' height='$gmrheight'><br/>";
                        else
                            echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                        echo "<b>IRA BUDISUSETYO</b></td>";

                    echo "</tr>";

                echo "</table>";

                echo "<br/>&nbsp;<br/>&nbsp;";

            }else{



                echo "<table class='tjudul' width='100%'>";
                    echo "<tr>";

                        echo "<td align='center'>";
                        echo "Dibuat oleh,";
                        if (!empty($namapengaju_ttd_fin1))
                            echo "<br/><img src='images/tanda_tangan_base64/$namapengaju_ttd_fin1' height='$gmrheight'><br/>";
                        else
                            echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                        echo "<b>DESI RATNA DEWI</b></td>";


                            echo "<td align='center'>";
                            echo "Checker,";
                            if (!empty($namapengaju_ttd_fin2))
                                echo "<br/><img src='images/tanda_tangan_base64/$namapengaju_ttd_fin2' height='$gmrheight'><br/>";
                            else
                                echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                            echo "<b>SAIFUL RAHMAT</b></td>";

                            echo "<td align='center'>";
                            echo "";
                                echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                            echo "</td>";


                            echo "<td align='center'>";
                            echo "Menyetujui,";
                            if (!empty($namapengaju_ttd1))
                                echo "<br/><img src='images/tanda_tangan_base64/$namapengaju_ttd1' height='$gmrheight'><br/>";
                            else
                                echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                            echo "<b>$nnama_ss_mktdir</b></td>";

                    echo "</tr>";

                echo "</table>";

                echo "<br/>&nbsp;<br/>&nbsp;";



                                    /*
                if ($jenis=="Y") {
                    echo "<table width='100%' border='0px' style='border : 0px solid #fff; font-size: 11px; font-weight: bold;'>";
                    echo "<tr align='center'>";
                    echo "<td>Dibuat oleh,</td><td colspan=2>Mengetahui,</td><td>Disetujui,</td>";
                    echo "</tr>";

                    echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
                    echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
                    echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
                    echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";

                    echo "<tr align='center'>";
                    echo "<td>DESI RATNA DEWI</td><td>SAIFUL RAHMAT</td><td>$nnama_ss_mktdir</td><td>IRA BUDISUSETYO</td>";
                    echo "</tr>";
                }else{
                    echo "<table width='100%' border='0px' style='border : 0px solid #fff; font-size: 11px; font-weight: bold;'>";
                    echo "<tr align='center'>";
                    echo "<td>Dibuat oleh,</td><td>Mengetahui,</td><td>Disetujui,</td>";
                    echo "</tr>";

                    echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
                    echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
                    echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
                    echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";

                    echo "<tr align='center'>";
                    echo "<td>DESI RATNA DEWI</td><td>SAIFUL RAHMAT</td><td>$nnama_ss_mktdir</td>";
                    echo "</tr>";
                }
                                    */


            }
                
        ?>
        
</BODY>
<?PHP if ($ppilihrpt!="excel") { ?>
    <style>
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 13px;
            border: 0px solid #000;
        }
        table.example_2 {
            color: #000;
            font-family: Helvetica, Arial, sans-serif;
            width: 98%;
            border-collapse:
            collapse; border-spacing: 0;
            font-size: 11px;
            border: 1px solid #000;
        }

        table.example_2 td, table.example_2 th {
            border: 1px solid #000; /* No more visible border */
            height: 25px;
            transition: all 0.3s;  /* Simple transition for hover effect */
        }

        table.example_2 th {
            background: #DFDFDF;  /* Darken header a bit */
            font-weight: bold;
        }

        table.example_2 td {
            background: #FAFAFA;
        }

        /* Cells in even rows (2,4,6...) are one color */
        /*tr:nth-child(even) td { background: #F1F1F1; }*/

        /* Cells in odd rows (1,3,5...) are another (excludes header cells)  */
        tr:nth-child(odd) td { background: #FEFEFE; }

        tr td:hover.biasa { background: #666; color: #FFF; }
        tr td:hover.left { background: #ccccff; color: #000; }

        tr td.center1, td.center2 { text-align: center; }

        tr td:hover.center1 { background: #666; color: #FFF; text-align: center; }
        tr td:hover.center2 { background: #ccccff; color: #000; text-align: center; }
        /* Hover cell effect! */
        
        table {
            font-family: "Times New Roman", Times, serif;
            font-size: 11px;
        }
        table.tjudul {
            font-size: 13px;
            width: 97%;
        }
        
        
        input.e_id, input.e_i {
            font-family: "Times New Roman", Times, serif;
            font-size: 13px;
            background-color: transparent;
            border: 0px solid #000;
            height: 20px;
        }
        input.e_id {
            width: 25px;
            color: #000;
            text-align: right;
        }
        input.e_i {
            width: 40px;
            color: #000;
        }
        
        #kotakjudul {
            border: 0px solid #000;
            width:100%;
            height: 2.3cm;
        }
        #isikiri {
            float   : left;
            width   : 49%;
            border-left: 0px solid #000;
        }
        #isikanan {
            text-align: right;
            float   : right;
            width   : 49%;
        }
    </style>
<?PHP } ?>
</HTML>

<?PHP
    hapusdata:
        mysqli_query($cnmy, "drop temporary table $tmp01");
        mysqli_query($cnmy, "drop temporary table $tmp02");
        mysqli_query($cnmy, "drop temporary table $tmp03");
        mysqli_query($cnmy, "drop temporary table $tmp04");
        mysqli_close($cnmy);
?>