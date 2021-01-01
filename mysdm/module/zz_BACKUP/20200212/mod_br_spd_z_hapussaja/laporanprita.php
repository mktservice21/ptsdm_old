<?PHP
    session_start();
    $mact=$_GET['act'];
    if ($_GET['ket']=="excel") {
        $now_=date("mdYhis");
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        if ($mact=="viewbrklaim")
            header("Content-Disposition: attachment; filename=KLAIM_DISCOUNT_BR_EAGLE_$now_.xls");
        else
            header("Content-Disposition: attachment; filename=CASH ADVANCE_BR_EAGLE_$now_.xls");
    }
    
    include("config/koneksimysqli.php");
    include("config/common.php");
    $cnit=$cnmy;
?>
<html>
<head>
    <?PHP 
        if ($mact=="viewbrklaim") echo "<title>KLAIM DISCOUNT - BR</title>";
        else echo "<title>CASH ADVANCE - BR</title>";
     
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
<?php

    if (!isset($_GET['ispd'])) {
        goto hapusdata;
    }
    
    $tglnow = date("d/m/Y");
    $periode1 = date("d F Y");
    
    
    $gmrheight = "100px";
    $ngbr_idinput="";
    $gbrttd_fin1="";
    $gbrttd_fin2="";
    $gbrttd_dir1="";
    $gbrttd_dir2="";
    
    $ntgl_apv1="";
    $ntgl_apv2="";
    $ntgl_apv_dir1="";
    $ntgl_apv_dir2="";
    

    $idinputspd=$_GET['ispd'];
    
    
    $userid=$_SESSION['IDCARD'];
    $now=date("mdYhis");
    $tmp01 =" dbtemp.DSETHZ01_".$userid."_$now ";
    $tmp02 =" dbtemp.DSETHZ02_".$userid."_$now ";
    $tmp03 =" dbtemp.DSETHZ03_".$userid."_$now ";
    $tmp04 =" dbtemp.DSETHZ04_".$userid."_$now ";
    $tmp05 =" dbtemp.DSETHZ05_".$userid."_$now ";
    $tmp06 =" dbtemp.DSETHZ06_".$userid."_$now ";
    $tmp07 =" dbtemp.DSETHZ07_".$userid."_$now ";
    
    
    $query = "select a.idinput, a.tgl, a.tglspd, a.divisi, a.nodivisi, a.nomor, a.kodeid, a.subkode, a.jenis_rpt, 
        a.gbr_apv1, a.gbr_apv2, a.gbr_dir, a.gbr_dir2,
        b.tanggal as tglkeluar, b.nobukti,
        c.bridinput, c.amount, c.jml_adj, c.ketadj1, c.ketadj2, a.keterangan, c.urutan, c.trans_ke, a.tgl_apv1, a.tgl_apv2, a.tgl_dir, a.tgl_dir2  
        from dbmaster.t_suratdana_br a 
            LEFT JOIN 
        (select DISTINCT idinput, IFNULL(bridinput,'') as bridinput, amount, jml_adj, aktivitas1 as ketadj1, aktivitas2 as ketadj2, urutan, trans_ke from dbmaster.t_suratdana_br1 WHERE idinput='$idinputspd') as c 
            on a.idinput=c.idinput
            LEFT JOIN
        (select idinput, tanggal, nobukti from dbmaster.t_suratdana_bank WHERE idinput='$idinputspd' AND stsinput='K' LIMIT 1) as b 
        on a.idinput=b.idinput
        WHERE a.idinput='$idinputspd'";
    
    $query = "create TEMPORARY table $tmp01 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    if ($mact=="viewbrklaim") {
        $query = "select a.DIVISI divprodid, a.klaimId brId, a.karyawanid karyawanId, c.nama nama_karyawan,
            a.distid dokterId, b.nama nama_dokter, a.aktivitas1, '' aktivitas2, a.jumlah, a.tgl, a.tgltrans, a.tglrpsby,  
            a.realisasi1, a.noslip, a.COA4, d.NAMA4, 0 jumlah1, 0 realisasi2, '' iCabangId, '' nama_cabang, CAST('' as CHAR(1)) batal, CAST('' AS char(10)) ccyId  
            from hrd.klaim a LEFT JOIN MKT.distrib0 b on a.distid=b.distid 
            LEFT JOIN hrd.karyawan c on a.karyawanid=c.karyawanId 
            LEFT JOIN dbmaster.coa_level4 d on a.COA4=d.COA4
            WHERE a.klaimId IN (select DISTINCT IFNULL(bridinput,'') bridinput FROM $tmp01) ";
        
        $query = "create TEMPORARY table $tmp02 ($query)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
    }else{
        
        $query = "select a.divprodid, a.brId, a.tgl, a.noslip, a.tgltrans, a.tglrpsby, a.karyawanId, a.dokterId, a.dokter, 
            a.aktivitas1, a.aktivitas2, a.realisasi1, a.jumlah, a.jumlah1, a.realisasi2, a.iCabangId, a.COA4, a.batal, a.ccyId from hrd.br0 a 
            WHERE a.brId IN (select DISTINCT IFNULL(bridinput,'') bridinput FROM $tmp01)";
        $query = "create TEMPORARY table $tmp04 ($query)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        $query = "INSERT INTO $tmp04 (divprodid, brId, tgl, noslip, tgltrans, tglrpsby, karyawanId, dokterId, dokter, 
            aktivitas1, aktivitas2, realisasi1, jumlah, jumlah1, realisasi2, iCabangId, COA4, batal, ccyId)
            select a.divprodid, a.brId, a.tgl, a.noslip, a.tgltrans, a.tglrpsby, a.karyawanId, a.dokterId, a.dokter, 
            a.aktivitas1, a.aktivitas2, a.realisasi1, a.jumlah, a.jumlah1, a.realisasi2, a.iCabangId, a.COA4, 'Y' as batal, a.ccyId from dbmaster.backup_br0 a 
            WHERE a.brId IN (select DISTINCT IFNULL(bridinput,'') bridinput FROM $tmp01)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        
        $query = "select a.divprodid, a.brId, a.tgl, a.noslip, a.tgltrans, a.tglrpsby, a.karyawanId, c.nama nama_karyawan, a.dokterId, a.dokter, b.nama nama_dokter,
            a.aktivitas1, a.aktivitas2, a.realisasi1, a.jumlah, a.jumlah1, a.realisasi2, a.iCabangId, d.nama nama_cabang, a.COA4, e.NAMA4, a.batal, a.ccyId from $tmp04 a 
            LEFT JOIN hrd.dokter b on a.dokterId=b.dokterId
            LEFT JOIN hrd.karyawan c on a.karyawanId=c.karyawanId 
            LEFT JOIN MKT.icabang d on a.iCabangId=d.iCabangId
            LEFT JOIN dbmaster.coa_level4 e on a.COA4=e.COA4
            WHERE a.brId IN (select DISTINCT IFNULL(bridinput,'') bridinput FROM $tmp01)";
        
        $query = "create TEMPORARY table $tmp02 ($query)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
    }
    
    
    $query = "UPDATE $tmp02 SET ccyId='IDR' WHERE IFNULL(ccyId,'')=''"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "select a.*, b.amount, b.jml_adj, b.ketadj1, b.ketadj2, b.urutan, b.trans_ke, b.tgl_apv1, b.tgl_apv2, b.tgl_dir, b.tgl_dir2 from $tmp02 a JOIN $tmp01 b on a.brId=b.bridinput";
    $query = "create TEMPORARY table $tmp03 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "Alter table $tmp03 MODIFY urutan CHAR(150)";
    //mysqli_query($cnmy, $query);
    //$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    mysqli_query($cnmy, "UPDATE $tmp03 SET urutan=noslip where IFNULL(urutan,'0')='0'");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    mysqli_query($cnmy, "UPDATE $tmp03 SET urutan=realisasi1 where IFNULL(urutan,'')=''");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    mysqli_query($cnmy, "UPDATE $tmp03 SET jumlah=amount");
    
    
    $query = "select * from $tmp03 WHERE IFNULL(jml_adj,0)<>0";
    $query = "create TEMPORARY table $tmp07 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    //mysqli_query($cnmy, "UPDATE $tmp07 SET jml_adj=0-jml_adj WHERE IFNULL(jml_adj,0)>0");
    mysqli_query($cnmy, "UPDATE $tmp07 SET amount=jml_adj, jumlah=jml_adj, jumlah1=jml_adj, aktivitas1=ketadj1, aktivitas2=ketadj2");
    
    
    $query = "INSERT INTO $tmp03 SELECT * FROM $tmp07";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    
    $pnobukti="";
    $ptglkeluar="";
    $nodivisi="";
    $padvance="A";
    $pjnsrpt="";
    $pdivisi="";
    $tgl="";
    
    $query = "select a.idinput, a.tgl, a.tglspd, a.divisi, a.nodivisi, a.nomor, "
            . " a.kodeid, a.subkode, a.jenis_rpt, a.gbr_apv1, a.gbr_apv2, a.gbr_dir, a.gbr_dir2, a.tglkeluar, a.nobukti, "
            . " a.tgl_apv1, a.tgl_apv2, a.tgl_dir, a.tgl_dir2 FROM $tmp01 a LIMIT 1";
    $tampil= mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($tampil);
    if ($ketemu>0) {
        $ra= mysqli_fetch_array($tampil);

        $pnobukti=$ra['nobukti'];

        if (!empty($ra['tglkeluar']) AND $ra['tglkeluar']<>"0000-00-00") $ptglkeluar = date("d-M-y", strtotime($ra['tglkeluar']));

        $nodivisi=$ra['nodivisi'];
        $padvance=$ra['jenis_rpt'];
        $pjnsrpt=$ra['kodeid'];
        $pdivisi=$ra['divisi'];
        $tgl=$ra['tgl'];
        if ($mact=="rekapbr") {
            $periode1 = date("d-M-y", strtotime($tgl));
        }else{
            $periode1 = date("d F Y", strtotime($tgl));
        }

        $ngbr_idinput=$ra['idinput'];

        $gbrttd_fin1=$ra['gbr_apv1'];
        $gbrttd_fin2=$ra['gbr_apv2'];

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
    }
    
    
    $ntgl_rptsby="";
    //$query="select tglrpsby FROM $tmp02 WHERE IFNULL(tglrpsby,'0000-00-00') <>'0000-00-00' AND IFNULL(tglrpsby,'') <>'' LIMIT 1";
    $query="select tanggal as tglrpsby from dbmaster.t_suratdana_bank WHERE nodivisi='$nodivisi' and stsinput='N' and stsnonaktif<>'Y'";
    $tampil_s= mysqli_query($cnmy, $query);
    $ketemu_s= mysqli_num_rows($tampil_s);
    if ($ketemu_s>0) {
        $rs= mysqli_fetch_array($tampil_s);
        
        if (!empty($rs['tglrpsby']) AND $rs['tglrpsby']<>"0000-00-00") {
            $ntgl_rptsby=$rs['tglrpsby'];
            $ntgl_rptsby = date("d F Y", strtotime($ntgl_rptsby));
        }
        
    }
    
    $nmadvance="**Cash Advance";
    if ($padvance=="K") $nmadvance="* Klaim";
    if ($padvance=="B") $nmadvance="* BELUM ADA KUITANSI";
    if ($padvance=="D") $nmadvance="* Klaim Discount";
    if ($padvance=="S") $nmadvance="* Kasbon Surabaya";
    if ($padvance=="V") $nmadvance="* Via Surabaya (BR)";
    if ($padvance=="C") $nmadvance="* Via Surabaya (Klaim Disc.)";
    if ($padvance=="W") $nmadvance="";
    
    
    $imatauangbanyak=false;
    $m_uang1=false;
    $m_uang2=false;
    $m_uang3=false;
    $m_uang4=false;
    
    $query = "select distinct ccyId from $tmp03";
    $tampil= mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($tampil);
    if ($ketemu>1) $imatauangbanyak=true;
    
?>    
    
    <div id="kotakjudul" style="margin-bottom: -30px;">
        <div id="isikiri">
            <table class='tjudul' width='100%'>
                <?PHP
                    if ($padvance=="W") {
                    }else{
                        echo "<tr><td width='200px'>To : </td><td>Sdr. Lina (Finance)</td></tr>";
                        echo "<tr><td width='150px'><b>&nbsp;</b></td><td></td></tr>";
                    }
                    echo "<tr><td width='250px' nowrap><b>Budget Request Team $pdivisi : </b></td><td>$nodivisi</td></tr>";
                    
                    $nket_status="**Cash Advance";
                    if ($padvance=="B") $nket_status="**Mau Minta Uang";
                    if ($padvance=="K") $nket_status="**Klaim";
                    if ($padvance=="S") $nket_status="**Kasbon Surabaya";
                    if ($padvance=="W") $nket_status="Tanggal";
                    //if ($padvance=="V") $nket_status="**Via Surabaya (BR)";
                    //if ($padvance=="C") $nket_status="**Via Surabaya (Klaim Disc.)";
                    
                    echo "<tr><td width='150px'><b>$nket_status : </b></td><td align='left'><b>$periode1</b></td></tr>";
                    
                    if (!empty($ntgl_rptsby)) {
                        echo "<tr><td width='150px'><b>Tgl. Transfer Surabaya : </b></td><td align='left'><b>$ntgl_rptsby</b></td></tr>";
                    }else{
                        echo "<tr><td width='150px'>&nbsp;</td><td></td></tr>";
                    }
                    
                ?>  
            </table>
        </div>
        <div id="isikanan">
            
        </div>
        <div class="clearfix"></div>
    </div>
    
    
    <div class="clearfix"></div>
    <?PHP
        if ($_GET['ket']=="excel") {
            echo "<div><table style='color:blue; border:0px' width='90%'>"
                . "<tr>"
                . "<td></td>"
                . "<td></td>"
                . "<td></td>"
                . "<td></td>"
                . "<td></td>"
                . "<td></td>"
                . "<td colspan='3'>$nmadvance</td>"
                . "</tr>"
                . "</table></div>";
        }else{
            echo "<div><table style='color:blue; border:0px' width='90%'><tr><td align='right'>$nmadvance</td></tr></table></div>";
        }
    ?>
    
    <table id='datatable2' class='table table-striped table-bordered example_2' border="1px solid black">
        <thead>
            <tr style='background-color:#cccccc; font-size: 13px;'>
                <th align="center">Date</th>
                <th align="center">DOKTER/SUPPLIER/CUSTOMER</th>
                <th align="center">NO. SLIP</th>
                <th align="center">PENGAJUAN</th>
                <th align="center">DAERAH</th>
                <th align="center">KETERANGAN</th>
                <th align="center">REALISASI</th>
                <th align="center">Credit</th>
                <th align="center">No.</th>
            </tr>
        </thead>
        <tbody>
            <?PHP
                $nmyno="";
                $njml="";
                
                $gtotal=0;
                $gtotal_real=0;
                $tot_perslip_c=0;
                $tot_perslip_c1=0;
                
                $pjumlah=0;
                $pjumlah_1=0;
                
                $pjumlah_2=0; $pjumlah_1_2=0;
                $pjumlah_3=0; $pjumlah_1_3=0;
                $pjumlah_4=0; $pjumlah_1_4=0;
                
                $gtotal_2=0; $gtotal_real_2=0; $tot_perslip_c_2=0; $tot_perslip_c1_2=0;
                $gtotal_3=0; $gtotal_real_3=0; $tot_perslip_c_3=0; $tot_perslip_c1_3=0;
                $gtotal_4=0; $gtotal_real_4=0; $tot_perslip_c_4=0; $tot_perslip_c1_4=0;
                
                $no=1;
                $query = "select distinct urutan from $tmp03 order by urutan";
                $tampil1=mysqli_query($cnmy, $query);
                while ($row1= mysqli_fetch_array($tampil1)) {
                    $pnourut_ = $row1['urutan'];
                    
                    $njml=0;
                    $nmyno=$no;
                    
                    $tot_perslip_c=0;
                    $tot_perslip_c1=0;
                    
                    $query = "select * from $tmp03 WHERE urutan='$pnourut_' order by urutan, tgl, brId, noslip, realisasi1, nama_karyawan, brId";
                    $tampil=mysqli_query($cnmy, $query);
                    while ($row= mysqli_fetch_array($tampil)) {
                        $pbrid = $row['brId'];
                        $pstsbatal = $row['batal'];
						
						$ptgl_pengajuan_br = $row['tgl'];
                        
                        $ptgltrans = "";
                        if (!empty($row['tgltrans']) AND $row['tgltrans']<> "0000-00-00")
                            $ptgltrans =date("d-M-Y", strtotime($row['tgltrans']));
                        
                        if (empty($ptglkeluar)) {
                            $ptglkeluar =$ptgltrans;
                        }
						
                        //$ptglkeluar=$ptgl_pengajuan_br;
						
                        $piddokter = $row['dokterId'];
                        $pnmdokter = $row['nama_dokter'];
                        if (empty($pnmdokter)) $pnmdokter = $row['dokter'];
                        
                        $pnoslip = $row['noslip'];
                        
                        $pnamakaryawan = $row['nama_karyawan'];
                        $pdaerah = $row['nama_cabang'];
                        if ($pdaerah=="ETH - HO") $pdaerah= "HO";
                        $paktivitas1 = $row['aktivitas1'];
                        $paktivitas2 = $row['aktivitas2'];
                        $prealisasi1 = $row['realisasi1'];
                        
                        $pcoa = $row['COA4'];
                        $pnmcoa = $row['NAMA4'];
                        
                        $pmatauang = $row['ccyId'];
                            
                        $pjumlah=$row['jumlah'];
                        $pjumlah_1=$row['jumlah1'];
                                        
                        $pjumlah_2=$row['jumlah'];
                        $pjumlah_1_2=$row['jumlah1'];
                            
                        $pjumlah_3=$row['jumlah'];
                        $pjumlah_1_3=$row['jumlah1'];
                            
                        $pjumlah_4=$row['jumlah'];
                        $pjumlah_1_4=$row['jumlah1'];
                        
                        
                        if ($pmatauang=="EUR") {
                            $m_uang2=true;
                            $gtotal_2=(double)$gtotal_2+(double)$pjumlah_2; //total minta matauang lain
                        }elseif ($pmatauang=="USD") {
                            $m_uang3=true;
                            $gtotal_3=(double)$gtotal_3+(double)$pjumlah_3; //total minta matauang lain
                        }elseif ($pmatauang=="SGD") {
                            $m_uang4=true;
                            $gtotal_4=(double)$gtotal_4+(double)$pjumlah_4; //total minta matauang lain
                        }else{
                            $m_uang1=true;
                            $gtotal=(double)$gtotal+(double)$pjumlah; //total minta
                        }
                        
                        
                        
                        
                        $stl_batal="";
                        if ($pstsbatal=="Y") {
                            $stl_batal="style='color:red;'";
                            
                            if (!empty($paktivitas1)) $paktivitas1 = "BATAL - ".$paktivitas1;
                            elseif (empty($paktivitas1)) $paktivitas1 = "BATAL";
                        }else{

                            if ($pmatauang=="EUR") {
                                $gtotal_real_2=(double)$gtotal_real_2+(double)$pjumlah_2;//realisasi matauang bera

                                $tot_perslip_c_2=(double)$tot_perslip_c_2+(double)$pjumlah_2;
                                $tot_perslip_c1_2=(double)$tot_perslip_c1_2+(double)$pjumlah_1_2;   
                            }elseif ($pmatauang=="USD") {
                                $gtotal_real_3=(double)$gtotal_real_3+(double)$pjumlah_3;//realisasi matauang bera

                                $tot_perslip_c_3=(double)$tot_perslip_c_3+(double)$pjumlah_3;
                                $tot_perslip_c1_3=(double)$tot_perslip_c1_3+(double)$pjumlah_1_3;
                            }elseif ($pmatauang=="SGD") {
                                $gtotal_real_4=(double)$gtotal_real_4+(double)$pjumlah_4;//realisasi matauang bera

                                $tot_perslip_c_4=(double)$tot_perslip_c_4+(double)$pjumlah_4;
                                $tot_perslip_c1_4=(double)$tot_perslip_c1_4+(double)$pjumlah_1_4;
                            }else{//IDR
                                $gtotal_real=(double)$gtotal_real+(double)$pjumlah;//realisasi

                                $tot_perslip_c=(double)$tot_perslip_c+(double)$pjumlah;
                                $tot_perslip_c1=(double)$tot_perslip_c1+(double)$pjumlah_1;
                            }
                            
                            
                        }
                        
                        $pjumlah=number_format($pjumlah,0,",",",");        
                        
                        $ptranske = $row['trans_ke'];
                        
                        if ($padvance=="K" AND !empty($ptgltrans)) $ptglkeluar=$ptgltrans;
                        
                        
                        echo "<tr $stl_batal>";
                        
                        echo "<td nowrap>$ptglkeluar</td>";//$ptgltrans
                        echo "<td>$pnmdokter</td>";
                        echo "<td nowrap>$pnoslip</td>";
                        echo "<td>$pnamakaryawan</td>";

                        echo "<td nowrap>$pdaerah</td>";
                        echo "<td>$paktivitas1</td>";
                        echo "<td>$prealisasi1</td>";
                        echo "<td nowrap align='right'>$pjumlah</td>";
                        
                        //jenis transfer (BCA / NON)
                        if ($ptranske=="NB")
                            echo "<td nowrap align='center'><b>$nmyno</b></td>";
                        else
                            echo "<td nowrap align='center'>$nmyno</td>";
                        
                        
                        echo "</tr>";
                        
                        if ($padvance=="K") {
                            $no++;
                            $nmyno=$no;
                        }else{
                            $nmyno="";
                        }
                        
                        $njml++;
                    }
                    
                    if ($padvance=="K") {
                    }else{
                        $no++;
                    }
                    
                    if ((double)$njml>1 AND $padvance!="K") {
                        $tot_perslip_s=(double)$tot_perslip_c-(double)$tot_perslip_c1;
                        
                        $tot_perslip_c=number_format($tot_perslip_c,0,",",",");
                        $tot_perslip_c1=number_format($tot_perslip_c1,0,",",",");
                        $tot_perslip_s=number_format($tot_perslip_s,0,",",",");
                        
                        echo "<tr>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "<td nowrap align='right'><b>$tot_perslip_c</b></td>";
                        echo "<td></td>";
                        echo "</tr>";
                        
                        echo "<tr>";
                        echo "<td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>";
                        echo "</tr>";
                        
                    }
                    
                    
                    
                }
                
                echo "<tr><td colspan='9'></td></tr>";
                
                if ($imatauangbanyak==false){
                    //potongan (kelebihan)
                    $gtotal_potongan=(double)$gtotal-(double)$gtotal_real;

                    $gtotal=number_format($gtotal,0,",",",");


                    echo "<tr style='font-size:15px;'>";
                    echo "<td></td>";
                    echo "<td></td>";
                    echo "<td></td>";
                    echo "<td></td>";
                    echo "<td></td>";
                    echo "<td nowrap><b>TOTAL</b></td>";
                    echo "<td></td>";
                    echo "<td nowrap align='right'><b>$gtotal</b></td>";
                    echo "<td></td>";
                    echo "</tr>";

                    if ((double)$gtotal_potongan<>0) {

                        $gtotal_real=number_format($gtotal_real,0,",",",");
                        $gtotal_potongan=number_format($gtotal_potongan,0,",",",");

                        echo "<tr style='font-size:15px;'>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "<td nowrap><b>REALISASI</b></td>";
                        echo "<td></td>";
                        echo "<td nowrap align='right'><b>$gtotal_real</b></td>";
                        echo "<td></td>";
                        echo "</tr>";

                        //adjustment
                        echo "<tr style='font-size:15px;'>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "<td nowrap><b>KELEBIHAN</b></td>";
                        echo "<td></td>";
                        echo "<td nowrap align='right'><b>$gtotal_potongan</b></td>";
                        echo "<td></td>";
                        echo "</tr>";

                    }
                    
                }else{
                    
                    $gtotal_potongan=(double)$gtotal-(double)$gtotal_real;
                    $gtotal=number_format($gtotal,0,",",",");

                    echo "<tr style='font-size:15px;'>";
                    echo "<td></td>";
                    echo "<td></td>";
                    echo "<td></td>";
                    echo "<td></td>";
                    echo "<td></td>";
                    echo "<td nowrap><b>TOTAL IDR</b></td>";
                    echo "<td></td>";
                    echo "<td nowrap align='right'><b>$gtotal</b></td>";
                    echo "<td></td>";
                    echo "</tr>";
                    
                    if ($m_uang2==true) {
                        $gtotal_potongan_2=(double)$gtotal_2-(double)$gtotal_real_2;
                        $gtotal_2=number_format($gtotal_2,0,",",",");

                        echo "<tr style='font-size:15px;'>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "<td nowrap><b>TOTAL EUR</b></td>";
                        echo "<td></td>";
                        echo "<td nowrap align='right'><b>$gtotal_2</b></td>";
                        echo "<td></td>";
                        echo "</tr>";
                    }
                    
                    if ($m_uang3==true) {
                        $gtotal_potongan_3=(double)$gtotal_3-(double)$gtotal_real_3;
                        $gtotal_3=number_format($gtotal_3,0,",",",");

                        echo "<tr style='font-size:15px;'>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "<td nowrap><b>TOTAL USD</b></td>";
                        echo "<td></td>";
                        echo "<td nowrap align='right'><b>$gtotal_3</b></td>";
                        echo "<td></td>";
                        echo "</tr>";
                    }
                    
                    if ($m_uang4==true) {
                        $gtotal_potongan_4=(double)$gtotal_4-(double)$gtotal_real_4;
                        $gtotal_4=number_format($gtotal_4,0,",",",");

                        echo "<tr style='font-size:15px;'>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "<td nowrap><b>TOTAL USD</b></td>";
                        echo "<td></td>";
                        echo "<td nowrap align='right'><b>$gtotal_4</b></td>";
                        echo "<td></td>";
                        echo "</tr>";
                    }
                    
                }
            ?>
        </tbody>
    </table>
    
    <br/>&nbsp;<br/>&nbsp;
    
        <?PHP
        
        
            $nposisi="left";
            if ($padvance!="B" AND $padvance!="W") $nposisi="center";
        
        if ($_GET['ket']=="excel") {
            
            echo "<table class='tjudul' width='100%'>";
                echo "<tr>";

                    echo "<td align='$nposisi'>";
                    echo "Yang Membuat,";
                    echo "<br/>&nbsp;<br/>&nbsp;<br/>$ntgl_apv1<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                    echo "<b>PRITA M SINA</b></td>";

                if ($padvance!="B" AND $padvance!="W") {
                    
                    echo "<td align='center'>";
                    echo "Checker,";
                    echo "<br/>&nbsp;<br/>&nbsp;<br/>$ntgl_apv2<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                    echo "<b>MARIANNE PRASANTI</b></td>";
                    

                    echo "<td align='center'>";
                    echo "Mengetahui,";
                    echo "<br/>&nbsp;<br/>&nbsp;<br/>$ntgl_apv_dir1<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                    echo "<b>FARIDA SOEWANTO</b></td>";


                    echo "<td align='center'>";
                    echo "Disetujui,";
                    echo "<br/>&nbsp;<br/>&nbsp;<br/>$ntgl_apv_dir2<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                    echo "<b>IRA BUDISUSETYO</b></td>";
                    
                }
                
                
                echo "</tr>";

            echo "</table>";
            
        }else{
            
            echo "<table class='tjudul' width='100%'>";
                echo "<tr>";

                    echo "<td align='$nposisi'>";
                    echo "Yang Membuat,";
                    if (!empty($namapengaju_ttd_fin1))
                        echo "<br/><img src='images/tanda_tangan_base64/$namapengaju_ttd_fin1' height='$gmrheight'><br/>";
                    else
                        echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                    echo "<b>PRITA M SINA</b></td>";

                if ($padvance!="B" AND $padvance!="W") {
                    
                    echo "<td align='center'>";
                    echo "Checker,";
                    if (!empty($namapengaju_ttd_fin2))
                        echo "<br/><img src='images/tanda_tangan_base64/$namapengaju_ttd_fin2' height='$gmrheight'><br/>";
                    else
                        echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                    echo "<b>MARIANNE PRASANTI</b></td>";
                    

                    echo "<td align='center'>";
                    echo "Mengetahui,";
                    if (!empty($namapengaju_ttd1))
                        echo "<br/><img src='images/tanda_tangan_base64/$namapengaju_ttd1' height='$gmrheight'><br/>";
                    else
                        echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                    echo "<b>FARIDA SOEWANTO</b></td>";


                    echo "<td align='center'>";
                    echo "Disetujui,";
                    if (!empty($namapengaju_ttd2))
                        echo "<br/><img src='images/tanda_tangan_base64/$namapengaju_ttd2' height='$gmrheight'><br/>";
                    else
                        echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                    echo "<b>IRA BUDISUSETYO</b></td>";
                    
                }
                
                
                echo "</tr>";

            echo "</table>";
            
        }
            
            
        /*
        echo "<table width='100%' style='border:0px;' >";
        echo "<tr align='center'>";
        //echo "<td>Yang membuat,</td> <td></td> <td>Checker</td> <td></td> <td>Menyetujui,</td>";
        echo "<td>YANG MEMBUAT,</td> <td></td> <td>CHECKER,</td> <td></td> <td>MENYETUJUI,</td>";
        echo "</tr>";

        echo "<tr><td>&nbsp;</td> <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td> <td>&nbsp;</td></tr>";
        echo "<tr><td>&nbsp;</td> <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td> <td>&nbsp;</td></tr>";
        echo "<tr><td>&nbsp;</td> <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td> <td>&nbsp;</td></tr>";
        echo "<tr><td>&nbsp;</td> <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td> <td>&nbsp;</td></tr>";
        echo "<tr><td>&nbsp;</td> <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td> <td>&nbsp;</td></tr>";
        echo "<tr><td>&nbsp;</td> <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td> <td>&nbsp;</td></tr>";

        echo "<tr align='center'>";
        //echo "<td>Ernilya</td> <td></td> <td>(Marianne Prasanti)</td> <td></td> <td>(dr. Farida Soewanto)</td>";
        echo "<td>PRITA M SINA</td> <td></td> <td>MARIANNE PRASANTI</td> <td></td> <td>IRA BUDI SUSETYO</td>";
        echo "</tr>";
        
        echo "</table>";
         * 
         */
        ?>
        <br/>&nbsp;<br/>&nbsp;
    
    
<?PHP
    hapusdata:
        mysqli_query($cnmy, "drop temporary table $tmp01");
        mysqli_query($cnmy, "drop temporary table $tmp02");
        mysqli_query($cnmy, "drop temporary table $tmp03");
        mysqli_query($cnmy, "drop temporary table $tmp04");
        mysqli_query($cnmy, "drop temporary table $tmp05");
        mysqli_query($cnmy, "drop temporary table $tmp06");
        mysqli_query($cnmy, "drop temporary table $tmp07");
?>
    
</body>


</html>
