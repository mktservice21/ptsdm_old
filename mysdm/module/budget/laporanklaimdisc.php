<?PHP
    session_start();
    $pcardidlog="";
    if (isset($_SESSION['IDCARD'])) $pcardidlog=$_SESSION['IDCARD'];
    
    if (empty($pcardidlog)) {
        echo "ANDA HARUS LOGIN ULANG...";
        exit;
    }
        
    $mact=$_GET['act'];
    $ppilihrpt="";
    if (isset($_GET['ket'])) $ppilihrpt=$_GET['ket'];
    
    if ($ppilihrpt=="excel") {
        $now_=date("mdYhis");
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=KLAIM_DISCOUNT_BR_$now_.xls");
    }
    
    include("config/koneksimysqli.php");
    include("config/common.php");
    
?>

<HTML>
<HEAD>
    <?PHP 
        echo "<title>KLAIM DISCOUNT - BR</title>";
     
        if ($ppilihrpt!="excel") {
            echo "<meta http-equiv='Expires' content='Mon, 01 Jan 2030 1:00:00 GMT'>";
            echo "<meta http-equiv='Pragma' content='no-cache'>";
            echo "<link rel='shortcut icon' href='images/icon.ico' />";
            echo "<link href='css/laporanbaru.css' rel='stylesheet'>";
            
            header("Cache-Control: no-cache, must-revalidate");
        }
        
    ?>
</HEAD>

<BODY>
    <?PHP
    $idinputspd="";
    if (isset($_GET['ispd'])) $idinputspd=$_GET['ispd'];
    
    if (empty($idinputspd)) {
        goto hapusdata;
    }
    
    $pbolehformatnumber=true;
    if ($ppilihrpt=="excel") $pbolehformatnumber=false;
    
    $npilih_urutan='99999';
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
    
    $nnama_ss_mktdir1="FARIDA SOEWANTO";
    $nnama_ss_mktdir2="EVI KOSINA SANTOSO";
    
    $nnama_ss_mktdir=$nnama_ss_mktdir1;


    $idinputspd=$_GET['ispd'];
    
    
    $puserid=$_SESSION['USERID'];
    $now=date("mdYhis");
    $tmp01 =" dbtemp.tmptrkdanakdisc01_".$puserid."_$now ";
    $tmp02 =" dbtemp.tmptrkdanakdisc02_".$puserid."_$now ";
    $tmp03 =" dbtemp.tmptrkdanakdisc03_".$puserid."_$now ";
    $tmp04 =" dbtemp.tmptrkdanakdisc04_".$puserid."_$now ";
    $tmp05 =" dbtemp.tmptrkdanakdisc05_".$puserid."_$now ";
    $tmp06 =" dbtemp.tmptrkdanakdisc06_".$puserid."_$now ";
    $tmp07 =" dbtemp.tmptrkdanakdisc07_".$puserid."_$now ";
    
    $query = "select a.idinput, a.karyawanid, a.tgl, a.tglspd, a.divisi, a.nomor, a.nodivisi, a.kodeid, a.subkode, a.jenis_rpt, 
        a.apv1, a.tgl_apv1, a.gbr_apv1, a.apv2, a.tgl_apv2, a.gbr_apv2, a.tgl_dir, a.gbr_dir, a.tgl_dir2, a.gbr_dir2, a.keterangan, 
        ROUND(IFNULL(jumlah,0)+IFNULL(jumlah2,0),0) as jumlah_transfer 
        FROM dbmaster.t_suratdana_br as a 
        WHERE idinput='$idinputspd'";
    $query = "create TEMPORARY table $tmp01 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "select DISTINCT idinput, IFNULL(bridinput,'') as bridinput, amount, jml_adj, "
            . " aktivitas1 as ketadj1, aktivitas2 as ketadj2, "
            . " urutan, trans_ke from dbmaster.t_suratdana_br1 WHERE idinput='$idinputspd'";
    $query = "create TEMPORARY table $tmp02 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "ALTER table $tmp01 ADD COLUMN nama_karyawan varchar(150), ADD COLUMN nama_pengajuan varchar(150), ADD COLUMN nama_report varchar(150), "
            . " ADD COLUMN nama_ket varchar(150), "
            . " ADD COLUMN tgl_trans date, ADD COLUMN nobukti varchar(100), ADD COLUMN tgl_sby date";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp01 as a JOIN hrd.karyawan as b on a.karyawanid=b.karyawanId SET "
            . " a.nama_karyawan=b.nama";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp01 as a JOIN dbmaster.t_kode_spd_pengajuan as b on a.jenis_rpt=b.jenis_rpt SET "
            . " a.nama_pengajuan=b.nama_pengajuan, a.nama_report=b.nama_report";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp01 as a JOIN (select idinput, tanggal, nobukti "
            . " from dbmaster.t_suratdana_bank WHERE idinput='$idinputspd' AND stsinput='K' "
            . " AND IFNULL(stsnonaktif,'')<>'Y' and subkode NOT IN ('29') LIMIT 1) as b on a.idinput=b.idinput SET "
            . " a.tgl_trans=tanggal, a.nobukti=b.nobukti"; 
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp01 as a JOIN (select idinput, tanggal, nobukti "
            . " from dbmaster.t_suratdana_bank WHERE idinput='$idinputspd' AND stsinput='N' "
            . " AND IFNULL(stsnonaktif,'')<>'Y' and subkode NOT IN ('29') LIMIT 1) as b on a.idinput=b.idinput SET "
            . " a.tgl_sby=tanggal"; 
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    

    $query_data = "select a.divisi, a.pengajuan, a.COA4 as coa4, a.klaimId as brid, "
            . " a.karyawanid, c.nama as nama_karyawan, a.distid as dokterid, b.nama as nama_dokter, "
            . " a.jumlah, a.realisasi1, a.noslip, "
            . " a.tgl, a.tgltrans, a.tglrpsby, "
            . " a.aktivitas1, a.aktivitas2, a.batal as ibatal, a.alasan_batal ";
    
    $query = $query_data." FROM hrd.klaim as a LEFT JOIN MKT.distrib0 b on a.distid=b.distid "
            . " LEFT JOIN hrd.karyawan as c on a.karyawanid=c.karyawanId "
            . " WHERE klaimId IN (select DISTINCT IFNULL(bridinput,'') bridinput FROM $tmp02)";
    $query = "create TEMPORARY table $tmp03 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query_reject = $query_data." FROM dbmaster.backup_klaim as a LEFT JOIN MKT.distrib0 b on a.distid=b.distid "
            . " LEFT JOIN hrd.karyawan as c on a.karyawanid=c.karyawanId "
            . " WHERE klaimId IN (select DISTINCT IFNULL(bridinput,'') bridinput FROM $tmp02)";
    
    $query = "INSERT INTO $tmp03 ".$query_reject;
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    
    
        
    $query = "ALTER table $tmp03 ADD COLUMN batal varchar(1), ADD COLUMN nama_cabang varchar(150), ADD COLUMN nomorid varchar(20)";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
    $query = "UPDATE $tmp03 as a JOIN hrd.klaim_reject as b on a.brid=b.klaimId SET a.batal='Y'";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
    $query = "UPDATE $tmp03 SET batal='Y' WHERE IFNULL(ibatal,'')='Y'";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
    $query = "select a.*, b.amount, b.jml_adj, b.ketadj1, b.ketadj2, b.urutan, b.trans_ke "
            . " FROM $tmp03 as a JOIN $tmp02 as b on a.brid=b.bridinput";
    $query = "create TEMPORARY table $tmp04 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    mysqli_query($cnmy, "UPDATE $tmp04 SET nomorid=brid where ( IFNULL(urutan,'0')='0' OR IFNULL(urutan,'')='') "); 
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    mysqli_query($cnmy, "UPDATE $tmp04 SET urutan='$npilih_urutan' where ( IFNULL(urutan,'0')='0' OR IFNULL(urutan,'')='') "); 
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    mysqli_query($cnmy, "UPDATE $tmp04 SET jumlah=amount");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "select * from $tmp04 WHERE IFNULL(jml_adj,0)<>0";
    $query = "create TEMPORARY table $tmp05 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    mysqli_query($cnmy, "UPDATE $tmp05 SET amount=jml_adj, jumlah=jml_adj, aktivitas1=IFNULL(ketadj1,''), aktivitas2=IFNULL(ketadj2,'')");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "INSERT INTO $tmp04 SELECT * FROM $tmp05";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $pkaryawanidspd="";
    $pnamakaryawan="";
    $pdivisi="";
    $pnobukti="";
    $pnodivisi="";
    $pjenisrpt="";
    $pkodeid="";
    $psubkode="";
    $ntgl="";
    $ntglspd="";
    $pperiode_tgl="";
    $pperiode_spd="";
    $ntgl_keluar="";
    $ntgl_rptsby="";
    $nmapengajuan="";
    $nmadvance="";
    $nket_status="";
    $pjumlahtranferspd=0;
    $papv_kar1="";
    $papv_kar2="";
    
    $query = "select * from $tmp01";
    $tampil= mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($tampil);
    if ($ketemu>0) {
        
        $ra= mysqli_fetch_array($tampil);
        
        $pkaryawanidspd=$ra['karyawanid'];
        $pnamakaryawan=$ra['nama_karyawan'];
        $nket_status=$ra['nama_ket'];
        $nmapengajuan=$ra['nama_pengajuan'];
        $nmadvance=$ra['nama_report'];
        $pdivisi=$ra['divisi'];
        $pnobukti=$ra['nobukti'];
        $pnodivisi=$ra['nodivisi'];
        $pnomorspd=$ra['nomor'];
        $pjenisrpt=$ra['jenis_rpt'];
        $pkodeid=$ra['kodeid'];
        $psubkode=$ra['subkode'];
        $ntgl=$ra['tgl'];
        $ntglspd=$ra['tglspd'];
        $ntgl_rptsby=$ra['tgl_sby'];
        $ntgl_keluar=$ra['tgl_trans'];
        $pjumlahtranferspd=$ra['jumlah_transfer'];
        
        $ngbr_idinput=$ra['idinput'];

        $papv_kar1=$ra['apv1'];
        $papv_kar2=$ra['apv2'];
        $gbrttd_fin1=$ra['gbr_apv1'];
        $gbrttd_fin2=$ra['gbr_apv2'];

        $gbrttd_dir1=$ra['gbr_dir'];
        $gbrttd_dir2=$ra['gbr_dir2'];
        
        $pperiode_tgl = date("d F Y", strtotime($ntgl));
        $pperiode_spd = date("d F Y", strtotime($ntglspd));
        
        if ($ntgl_rptsby=="0000-00-00") $ntgl_rptsby="";
        if ($ntgl_keluar=="0000-00-00") $ntgl_keluar="";
        if (!empty($ntgl_rptsby)) $ntgl_rptsby = date("d F Y", strtotime($ntgl_rptsby));
        if (!empty($ntgl_keluar)) $ntgl_keluar = date("d-M-Y", strtotime($ntgl_keluar));
        
        
        $tgljakukannya=$ntgl;
        if ($tgljakukannya=="0000-00-00") $tgljakukannya="";
        if (!empty($tgljakukannya)) $tgljakukannya = date("Ymd", strtotime($tgljakukannya));

        if (!empty($tgljakukannya)) {
            if ((double)$tgljakukannya>='20200701') {
                $nnama_ss_mktdir=$nnama_ss_mktdir2;
            }
        }
        
        
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
    
    $pstatusapprovespv=false;
    if ($papv_kar1==$papv_kar2) $pstatusapprovespv=true;
    
    if (empty($nmadvance)) $nmadvance="** Sudah Ada Bukti";
    if (empty($nket_status)) $nket_status="**Cash Advance";
    if ($pjenisrpt=="W" OR $pjenisrpt=="J") {
        $nmadvance="";
        if ($pjenisrpt=="J") $nket_status="";
    }
    
    //if ($pjenisrpt=="C" OR $pjenisrpt=="D") $pdivisi="EAGLE";
    
    
    //cari apakah ada urutan yang sama lebih dari 1 record
    $purutanlebih=false;
    $query = "select urutan, count(*) as jmlurutan from $tmp02 where IFNULL(urutan,0) NOT IN ('$npilih_urutan', '0','') GROUP BY 1 HAVING count(*)>1";
    $tampilu= mysqli_query($cnmy, $query);
    $ketemuu= mysqli_num_rows($tampilu);
    if ($ketemuu>0) {
        $purutanlebih=true;
    }
    
    //cari adjustment
    $query = "DROP TEMPORARY table $tmp03";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; } 
    
    $query = "select idinput, jumlah, keterangan from dbmaster.t_suratdana_br where "
            . " IFNULL(stsnonaktif,'')<>'Y' and nodivisi2='$pnodivisi' and subkode=50";
    $query = "create TEMPORARY table $tmp03 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    //echo "$pdivisi : $pnobukti, $pnodivisi - $pnomorspd, $pperiode_tgl, $nnama_ss_mktdir, tgl keluar $ntgl_keluar, tgl sby $ntgl_rptsby ($nmadvance)";
    
    ?>
    
    <div id="kotakjudul" style="margin-bottom: -30px;">
        <div id="isikiri">
            <table class='tjudul' width='100%'>
                <?PHP
                    if ($pjenisrpt=="W") {
                    }else{
                        echo "<tr><td width='200px'>To : </td><td>Sdr. Lina (Finance)</td></tr>";
                        echo "<tr><td width='150px'><b>&nbsp;</b></td><td></td></tr>";
                    }
                    echo "<tr><td width='250px' nowrap><b>Budget Request : </b></td><td>$pnodivisi</td></tr>";
                                        
                    echo "<tr><td width='150px'><b>$nket_status : </b></td><td align='left'><b>$pperiode_tgl</b></td></tr>";
                    
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
        if ($ppilihrpt=="excel") {
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
                <th align="center">SUPPLIER/CUSTOMER</th>
                <th align="center">NO. SLIP</th>
                <th align="center">PENGAJUAN</th>
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
                $no=1;
                
                $ptotalrincian=0;
                $pgrandtotal=0;
                $pgrandtotalaktif=0;
                $pgrandtotalbatal=0;
                
                $query = "select distinct urutan, nomorid from $tmp04 order by urutan";
                $tampil1=mysqli_query($cnmy, $query);
                while ($row1= mysqli_fetch_array($tampil1)) {
                    $purutan=$row1['urutan'];
                    $pnomorid=$row1['nomorid'];
                    
                    $nmyno=$no;
                    $njml=0;
                    
                    $psubtotal=0;
                    $psubtotalaktif=0;
                    $psubtotalbatal=0;
                    
                    $filter_nomid="";
                    if ((INT)$npilih_urutan==(INT)$npilih_urutan OR (INT)$npilih_urutan==0) {
                        $filter_nomid=" AND IFNULL(nomorid,'')='$pnomorid' ";
                    }
                    
                    $query = "select * from $tmp04 WHERE IFNULL(urutan,'')='$purutan' $filter_nomid order by urutan";
                    $tampil2=mysqli_query($cnmy, $query);
                    while ($row2= mysqli_fetch_array($tampil2)) {
                        $pbrid = $row2['brid'];
                        $pstsbatal = $row2['batal'];
                        $ptranske = $row2['trans_ke'];
                        $ptgltrans = $row2['tgltrans'];
                        
                        $pcutid= $row2['dokterid'];
                        $pcutnama= $row2['nama_dokter'];
                        $pnoslip= $row2['noslip'];
                        $pnamapengaju= $row2['nama_karyawan'];
                        $paktivitas1= $row2['aktivitas1'];
                        $paktivitas2= $row2['aktivitas2'];
                        $pnamarealisasi= $row2['realisasi1'];
                        $pjumlah=$row2['jumlah'];
                        $pbatalalasan=$row2['alasan_batal'];
                        
                        $pstsrealisasi="";
                        $pnamacabang="";
                        
                        
                        if ($ptgltrans=="0000-00-00") $ptgltrans="";
                        if (!empty($ptgltrans)) $ptgltrans = date("d-M-Y", strtotime($ptgltrans));
                        
                        if (!empty($ntgl_keluar)) {
                            $ntgl_keluar=$ptgltrans;//tgl transfer dari bank
                        }
                        
                        $psubtotal=(DOUBLE)$psubtotal+(DOUBLE)$pjumlah;
                        $pgrandtotal=(DOUBLE)$pgrandtotal+(DOUBLE)$pjumlah;
                        
                        $stl_batal="";
                        if ($pstsbatal=="Y") {
                            $stl_batal="style='color:red;'";
                            
                            if (!empty($pbatalalasan)) {
                                if (!empty($paktivitas1)) $paktivitas1 = "BATAL ($pbatalalasan) - ".$paktivitas1;
                                elseif (empty($paktivitas1)) $paktivitas1 = "BATAL ($pbatalalasan)";
                            }else{
                                if (!empty($paktivitas1)) $paktivitas1 = "BATAL - ".$paktivitas1;
                                elseif (empty($paktivitas1)) $paktivitas1 = "BATAL";
                            }
                            
                            $psubtotalbatal=(DOUBLE)$psubtotalbatal+(DOUBLE)$pjumlah;
                            $pgrandtotalbatal=(DOUBLE)$pgrandtotalbatal+(DOUBLE)$pjumlah;
                        }else{
                            $psubtotalaktif=(DOUBLE)$psubtotalaktif+(DOUBLE)$pjumlah;
                            $pgrandtotalaktif=(DOUBLE)$pgrandtotalaktif+(DOUBLE)$pjumlah;
                            $pbatalalasan="";
                        }
                        
                        
                        
                        if ($pbolehformatnumber==true) {
                            $pjumlah=number_format($pjumlah,0,",",",");
                        }else{
                            $pjumlah=number_format($pjumlah,0,"","");
                        }
                        
                        
                        echo "<tr $stl_batal>";
                        echo "<td nowrap>$ntgl_keluar</td>";
                        echo "<td nowrap>$pcutnama</td>";
                        echo "<td nowrap>$pnoslip</td>";
                        echo "<td nowrap>$pnamapengaju</td>";
                        //echo "<td nowrap>$pnamacabang</td>";
                        echo "<td >$paktivitas1</td>";
                        //echo "<td >$pstsrealisasi</td>";
                        echo "<td >$pnamarealisasi</td>";
                        echo "<td nowrap align='right'>$pjumlah</td>";
                        
                        //jenis transfer (BCA / NON)
                        if ($ptranske=="NB")
                            echo "<td nowrap align='center'><b>$nmyno</b></td>";
                        else
                            echo "<td nowrap align='center'>$nmyno</td>";
                        
 
                        echo "</tr>";
                        
                        
                        //if (empty($purutan)) $purutan="0";
                        
                        if ($pjenisrpt=="K") {
                            $no++;
                            $nmyno=$no;
                        }else{
                            $nmyno="";
                        }
                        
                        $njml++;
                    }
                    
                    if ($pjenisrpt=="K") {
                    }else{
                        $no++;
                    }
                    
                    if ($pbolehformatnumber==true) {
                        $psubtotalaktif=number_format($psubtotalaktif,0,",",",");
                    }else{
                        $psubtotalaktif=number_format($psubtotalaktif,0,"","");
                    }
                    
                    $plewaturutan=false;
                    if ($purutanlebih == true) {
                        echo "<tr style='font-weight:bold; font-size:11.5px; font-family:arial;'>";
                        //echo "<td></td>";
                        //echo "<td></td>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "<td nowrap align='right'>$psubtotalaktif</td>";
                        echo "<td></td>";
                        echo "</tr>";
                        
                        echo "<tr>";//<td>&nbsp;</td><td></td>
                        echo "<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>";
                        echo "</tr>";
                        $plewaturutan=true;
                    }
                    
                    
                }
                
                
                if ($ppilihrpt=="excel") {
                }else{
                    if ($plewaturutan == false) {
                        echo "<tr>";
                        echo "<td colspan='8'>&nbsp;</td>";
                        echo "</tr>";
                    }
                }
                
                $ptotalrincian=$pgrandtotal;//untuk cek dengan jumalh minta dana
                
                $padaselisih=false;
                if ((DOUBLE)$pgrandtotal<>(DOUBLE)$pgrandtotalaktif) {
                    $padaselisih=true;
                }
                
                if ($pbolehformatnumber==true) {
                    $pgrandtotal=number_format($pgrandtotal,0,",",",");
                    $pgrandtotalaktif=number_format($pgrandtotalaktif,0,",",",");
                    $pgrandtotalbatal=number_format($pgrandtotalbatal,0,",",",");
                }else{
                    $pgrandtotal=number_format($pgrandtotal,0,"","");
                    $pgrandtotalaktif=number_format($pgrandtotalaktif,0,"","");
                    $pgrandtotalbatal=number_format($pgrandtotalbatal,0,"","");
                }
                
                //GRAND TOTAL
                echo "<tr style='font-weight:bold; font-size:15px; font-family:arial;'>";
                //echo "<td></td>";
                echo "<td></td>";
                echo "<td></td>";
                echo "<td></td>";
                echo "<td></td>";
                echo "<td></td>";
                echo "<td nowrap>TOTAL</td>";
                //echo "<td></td>";
                echo "<td nowrap align='right'><b>$pgrandtotal</b></td>";
                echo "<td></td>";
                echo "</tr>";
                
                if ($padaselisih==true) {
                    echo "<tr style='font-weight:bold; font-size:15px; font-family:arial;'>";
                    //echo "<td></td>";
                    echo "<td></td>";
                    echo "<td></td>";
                    echo "<td></td>";
                    echo "<td></td>";
                    echo "<td></td>";
                    echo "<td nowrap>REALISASI</td>";
                    //echo "<td></td>";
                    echo "<td nowrap align='right'><b>$pgrandtotalaktif</b></td>";
                    echo "<td></td>";
                    echo "</tr>";
                    
                    echo "<tr style='font-weight:bold; font-size:15px; font-family:arial;'>";
                    //echo "<td></td>";
                    echo "<td></td>";
                    echo "<td></td>";
                    echo "<td></td>";
                    echo "<td></td>";
                    echo "<td></td>";
                    echo "<td nowrap>KELEBIHAN</td>";
                    //echo "<td></td>";
                    echo "<td nowrap align='right'><b>$pgrandtotalbatal</b></td>";
                    echo "<td></td>";
                    echo "</tr>";
                }
                
                $psudahadaadj=false;
                $query = "select idinput, jumlah, keterangan from $tmp03";
                $tampiladj=mysqli_query($cnmy, $query);
                while ($rad=mysqli_fetch_array($tampiladj)) {
                    $pketadj=$rad['keterangan'];
                    $prpadj=$rad['jumlah'];
                    
                    //$prpadj_=-1*(DOUBLE)$prpadj;
                    //$ptotalrincian=(DOUBLE)$ptotalrincian+(DOUBLE)$prpadj_;
                    
                    $prpadj=number_format($prpadj,0,",",",");

                    echo "<tr style='color:red; font-weight:bold; font-size:15px; font-family:arial;'>";
                    //echo "<td></td>";
                    echo "<td></td>";
                    echo "<td></td>";
                    echo "<td></td>";
                    echo "<td>Adjustment</td>";
                    echo "<td>$pketadj</td>";
                    //echo "<td nowrap></td>";
                    echo "<td></td>";
                    echo "<td nowrap align='right'>$prpadj</td>";
                    echo "<td></td>";
                    echo "</tr>";
                    
                    $psudahadaadj=true;
                }
            ?>
        </tbody>
    </table>
    
    <br/><br/>
    <?PHP
    
    if ($psudahadaadj == true){
    }else{
        $pjumlahtranferspd=number_format($pjumlahtranferspd,0,",",",");
        $ptotalrincian=number_format($ptotalrincian,0,",",",");
        
        if ((DOUBLE)$pjumlahtranferspd==(DOUBLE)$ptotalrincian){
        }else{
            echo "<center><span style='font-size:16px; font-weight:bold; color:red;'>ADA SELISIH JUMLAH, MOHON CEK KEMBALI...</span></center>";
            echo "<br/><br/>";
        }
    }
    //echo "$pjumlahtranferspd dan $ptotalrincian";
    
    $nposisi="left";
    if ($pjenisrpt!="B" AND $pjenisrpt!="W") $nposisi="center";
    
    if ($ppilihrpt=="excel") {
        echo "<table class='tjudul' width='100%'>";
            echo "<tr>";

                echo "<td align='$nposisi'>";
                echo "Yang Membuat,";
                echo "<br/>&nbsp;<br/>&nbsp;<br/>$ntgl_apv1<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                echo "<b>$pnamakaryawan</b></td>";

                
                echo "<td align='center'>";
                echo "Checker,";
                echo "<br/>&nbsp;<br/>&nbsp;<br/>$ntgl_apv2<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                echo "<b>MARIANNE PRASANTI</b></td>";


                echo "<td align='center'>";
                echo "Menyetujui,";
                echo "<br/>&nbsp;<br/>&nbsp;<br/>$ntgl_apv_dir1<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                echo "<b>$nnama_ss_mktdir</b></td>";

                if ($pjenisrpt!="B" AND $pjenisrpt!="W") {
                    echo "<td align='center'>";
                    echo "Mengetahui,";
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
                echo "<b>$pnamakaryawan</b></td>";



                echo "<td align='center'>";
                echo "Checker,";
                if (!empty($namapengaju_ttd_fin2))
                    echo "<br/><img src='images/tanda_tangan_base64/$namapengaju_ttd_fin2' height='$gmrheight'><br/>";
                else
                    echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                echo "<b>MARIANNE PRASANTI</b></td>";


                echo "<td align='center'>";
                echo "Menyetujui,";
                if (!empty($namapengaju_ttd1))
                    echo "<br/><img src='images/tanda_tangan_base64/$namapengaju_ttd1' height='$gmrheight'><br/>";
                else
                    echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                echo "<b>$nnama_ss_mktdir</b></td>";

                if ($pjenisrpt!="B" AND $pjenisrpt!="W") {

                    echo "<td align='center'>";
                    echo "Mengetahui,";
                    if (!empty($namapengaju_ttd2))
                        echo "<br/><img src='images/tanda_tangan_base64/$namapengaju_ttd2' height='$gmrheight'><br/>";
                    else
                        echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                    echo "<b>IRA BUDISUSETYO</b></td>";

                }


            echo "</tr>";

        echo "</table>";
    }
    
    ?>
    <br/><br/><br/><br/><br/>
    
    
</BODY>

</HTML>


<?PHP
hapusdata:
    mysqli_query($cnmy, "drop TEMPORARY table if EXISTS $tmp01");
    mysqli_query($cnmy, "drop TEMPORARY table if EXISTS $tmp02");
    mysqli_query($cnmy, "drop TEMPORARY table if EXISTS $tmp03");
    mysqli_query($cnmy, "drop TEMPORARY table if EXISTS $tmp04");
    mysqli_query($cnmy, "drop TEMPORARY table if EXISTS $tmp05");
    mysqli_query($cnmy, "drop TEMPORARY table if EXISTS $tmp06");
    mysqli_query($cnmy, "drop TEMPORARY table if EXISTS $tmp07");
    mysqli_close($cnmy);
?>