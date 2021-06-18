<?PHP
    session_start();
    $ppilihrpt=$_GET['ket'];
    if ($ppilihrpt=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=REKAP BUDGET REQUEST KAS KECIL CABANG.xls");
    }
    
    
    $ppilihedit="";
    if (isset($_GET['pedit'])) {
        $ppilihedit=$_GET['pedit'];
    }
    
    include("config/koneksimysqli.php");
    include("config/fungsi_sql.php");
    include("config/common.php");
?>

<html>
<head>
    <title>Rekap Budget Request Kas Kecil Cabang</title>
    <?PHP if ($ppilihrpt!="excel") { ?>
        <meta http-equiv="Expires" content="Mon, 01 Apr 2050 1:00:00 GMT">
        <meta http-equiv="Pragma" content="no-cache">
        <link rel="shortcut icon" href="images/icon.ico" />
        <link href="css/laporanbaru.css" rel="stylesheet">
        <?php header("Cache-Control: no-cache, must-revalidate"); ?>
        
        <style>
            #datatable2 td {
                padding:5px;
            }
            .btn_link {
                color: blue;
            }
            .btn_link:hover {
                color: red;
            }
            
            @media print {
                .btn_link {
                    color: #000;
                    text-decoration: none;
                }
            }
        </style>
        
            
    <?PHP } ?>
</head>

<body>
    <?PHP
        include "config/koneksimysqli.php";
        include "config/fungsi_combo.php";
        $pstsspd="2";
        $pnodiv=$_GET['ispd'];
        
        $ptglpd=date("d F Y");
        
        
        
        $p_rp_pettycash_ho="0";
        
        $p_rp_pettycash=$p_rp_pettycash_ho;
        
        $now=date("mdYhis");
        
        $tmp01 =" dbtemp.TMPKCCBRP01_".$_SESSION['USERID']."_$now ";
        $tmp02 =" dbtemp.TMPKCCBRP02_".$_SESSION['USERID']."_$now ";
        $tmp03 =" dbtemp.TMPKCCBRP03_".$_SESSION['USERID']."_$now ";
        $tmp04 =" dbtemp.TMPKCCBRP04_".$_SESSION['USERID']."_$now ";
        $tmp05 =" dbtemp.TMPKCCBRP05_".$_SESSION['USERID']."_$now ";
        $tmp06 =" dbtemp.TMPKCCBRP06_".$_SESSION['USERID']."_$now ";
    

        
        $pyangmembuat="";
        
        $gmrheight = "100px";
        $ngbr_idinput="";
        $pnmkaryawan="";
        $gbrttd_fin1="";
        $gbrttd_fin2="";
        $gbrttd_dir1="";
        $gbrttd_dir2="";
        
        
    
        $ntgl_apv1="";
        $ntgl_apv2="";
        $ntgl_apv_dir1="";
        $ntgl_apv_dir2="";

        $namapengaju_ttd_fin1="";
        $namapengaju_ttd_fin2="";

        $namapengaju_ttd1="";
        $namapengaju_ttd2="";
        
        
		$nnama_ss_mktdir1="EVI KOSINA SANTOSO";
		$nnama_ss_mktdir2="EVI KOSINA SANTOSO";
		
		$nnama_ss_mktdir=$nnama_ss_mktdir1;
	
	
        $query = "select * from dbmaster.t_suratdana_br WHERE idinput='$pnodiv'";
        $tampil= mysqli_query($cnmy, $query);
        $ketemu= mysqli_num_rows($tampil);
        if ($ketemu>0) {
            $ra= mysqli_fetch_array($tampil);
            
            $ngbr_idinput=$ra['idinput'];
            $pkryid=$ra['karyawanid'];
            $pnmkaryawan=getfield("select nama as lcfields from hrd.karyawan where karyawanid='$pkryid'");
            
            
            
            $gbrttd_fin1=$ra['gbr_apv1'];
            $gbrttd_fin2=$ra['gbr_apv2'];
            
            $gbrttd_dir1=$ra['gbr_dir'];
            $gbrttd_dir2=$ra['gbr_dir2'];
            
			$tgljakukannya=$ra['tgl'];
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
        
        
        
        $query = "select a.*, b.tglf, b.tglt, b.divisi divisipd, b.nodivisi, b.nomor, b.tgl as tglpd, b.coa4 coa, c.NAMA4 coa_nama,
            b.jumlah jumlahpd, b.kodeid, d.nama kodenama, b.subkode, d.subnama, b.jenis_rpt, b.jumlah2 jml_kasbon  
            from dbmaster.t_suratdana_br1 a JOIN  dbmaster.t_suratdana_br b 
            ON a.idinput=b.idinput LEFT JOIN dbmaster.coa_level4 c on b.coa4=c.COA4 
            LEFT JOIN dbmaster.t_kode_spd d on b.kodeid=d.kodeid and b.subkode=d.subkode 
            WHERE a.idinput = '$pnodiv'";
        $query = "create TEMPORARY table $tmp01 ($query)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query = "select a.nmrealisasi, a.norekening, a.pengajuan, a.idkascab, a.tanggal, a.bulan, a.COA4, c.NAMA4, a.karyawanid, d.nama nama_karyawan,
            a.icabangid, b.nama as nama_cabang, a.icabangid_o, e.nama as namacab_o, 
            a.areaid, f.nama as nama_area, a.areaid_o, g.nama as namaarea_o,
            a.keterangan, a.jumlah, stsnonaktif, h.saldoawal, h.pc_bln_lalu, h.pcm, h.jmltambahan, h.jumlah as jmlpcm, h.oustanding     
            from dbmaster.t_kaskecilcabang as a  
            left join MKT.icabang as b on a.icabangid=b.icabangid 
            LEFT JOIN dbmaster.coa_level4 as c on a.COA4=c.COA4
            LEFT JOIN hrd.karyawan d on a.karyawanid=d.karyawanId 
            left join MKT.icabang_o as e on a.icabangid_o=e.icabangid_o 
            left join MKT.iarea as f on a.areaid=f.areaid AND a.icabangid=f.icabangid 
            left join MKT.iarea_o as g on a.areaid_o=g.areaid_o AND a.icabangid_o=g.icabangid_o 
            LEFT JOIN dbmaster.t_kaskecilcabang_rpdetail h on a.idkascab=h.idkascab  
            WHERE 1=1 ";
            $query .= " AND a.idkascab IN (select IFNULL(bridinput,'') from $tmp01)";
        $query = "create TEMPORARY table $tmp02 ($query)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query = "UPDATE $tmp02 SET icabangid=icabangid_o, nama_cabang=namacab_o, areaid=areaid_o, nama_area=namaarea_o WHERE PENGAJUAN IN ('OTC', 'OT', 'CHC')";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
            $query = "SELECT a.*, b.tglf, b.tglt, b.divisipd, b.kodenama, b.tglpd, b.nomor, b.nodivisi, 
                b.nobbm, b.nobbk, b.urutan, b.amount, b.coa, b.coa_nama, b.jumlahpd,
                b.jenis_rpt, b.kodeid, b.subkode, b.jml_kasbon, CAST(0 as DECIMAL(20,2)) as kuranglebihrp, 
                CAST('' as CHAR(100)) as ketkurleb, CAST('' as CHAR(1)) as npilih, CAST(0 as DECIMAL(20,2)) as jmlperycash 
                FROM $tmp02 a JOIN $tmp01 b on a.idkascab=b.bridinput";
            
        $query = "create TEMPORARY table $tmp03 ($query)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        

        $query = "UPDATE $tmp03 a join dbmaster.t_uangmuka_kascabang b on a.icabangid=b.icabangid SET a.jmlperycash=b.jumlah WHERE"
                . " pengajuan='ETH'";
        //mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

        $query = "UPDATE $tmp03 a join dbmaster.t_uangmuka_kascabang b on a.icabangid=b.icabangid SET a.jmlperycash=b.jumlah WHERE"
                . " pengajuan IN ('OTC', 'OT', 'CHC')";
        //mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

        $query = "UPDATE $tmp03 SET jumlah=IFNULL(jumlah,0)-IFNULL(saldoawal,0) WHERE idkascab='C200900002'";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        $ftgl=$_GET['bln'];
        
        $datetrm = str_replace('/', '-', $ftgl);
        $ptgl_bln= date("Y-m-d", strtotime($datetrm));
            
        $mbln= date("F Y", strtotime($ptgl_bln));
        $mthan= date("Y", strtotime($ptgl_bln));
        
        
        
        $query = "select * from dbmaster.t_kaskecilcabang_adj WHERE idinput='$pnodiv'";
        $query = "create TEMPORARY table $tmp04 ($query)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        $query = "ALTER TABLE $tmp03 ADD COLUMN jml_adj DECIMAL(20,2), ADD COLUMN ket_adj VARCHAR(200)"; 
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query = "UPDATE $tmp03 as a JOIN $tmp04 as b on a.idkascab=b.idkascab AND LEFT(a.bulan,7)=LEFT(b.bulan,7) SET "
                . " a.jml_adj=b.jumlah, a.ket_adj=b.keterangan"; 
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        $padaadjustment=false;
        $query = "select sum(jml_adj) as jml_adj FROM $tmp03";
        $tampiladj=mysqli_query($cnmy, $query);
        $ketemuadj=mysqli_num_rows($tampiladj);
        if ((INT)$ketemuadj>0) {
            $adj= mysqli_fetch_array($tampiladj);
            $pnjmladj=$adj['jml_adj'];
            if (empty($pnjmladj)) $pnjmladj=0;
            if ((DOUBLE)$pnjmladj<>0) $padaadjustment=true;
        }

        
        $query = "select distinct kodeid, subkode, tglf, tglt, tglpd, divisipd divisi, kodenama, nomor, nodivisi, coa, coa_nama, jumlahpd, jenis_rpt, jml_kasbon FROM $tmp03 order by tglpd, divisipd, nodivisi";
      
        $tampil=mysqli_query($cnmy, $query);
        $ketemu=mysqli_num_rows($tampil);
        if ($ketemu>0) {
            while ($r= mysqli_fetch_array($tampil)) {
                $pkode_id=$r['kodeid'];
                $psubkode_id=$r['subkode'];
                
                $pjmlkasbon=$r['jml_kasbon'];
                
                
                $pkodenm=$r['kodenama'];
                $pnospd=$r['nomor'];
                $pnodivisi=$r['nodivisi'];
                $pcoapd=$r['coa'];
                $pnmcoapd=$r['coa_nama'];
                $pjumlahpd=$r['jumlahpd'];
                
                $pdivisipd=$r['divisi'];
                
                if ($pdivisipd=="OTC" OR $pdivisipd=="CHC") {
                    $pyangmembuat="DESI RATNA DEWI";
                }else{
                    $pyangmembuat="TITIK ERVIYANTI";
                }
                
                
                $ppengajuanpd=$pdivisipd;
                $ppengajuanpd2="BR $pdivisipd";
                
                $pjenisrpt=$r["jenis_rpt"];
                $nket="Laporan Kas Kecil periode $mbln";
                
                
                if (!empty($r['tglpd']) AND $r['tglpd']<>"0000-00-00")
                    $ptglpd =date("d F Y", strtotime($r['tglpd']));
                
                $ptglpd_f = "";
                if (!empty($r['tglf']) AND $r['tglf']<>"0000-00-00")
                    $ptglpd_f =date("d M Y", strtotime($r['tglf']));
                
                $ptglpd_t = "";
                if (!empty($r['tglt']) AND $r['tglt']<>"0000-00-00")
                    $ptglpd_t =date("d M Y", strtotime($r['tglt']));
                
                
                $ptglsby="";
                $query = "select tanggal from dbmaster.t_suratdana_bank where IFNULL(stsnonaktif,'')<>'Y' "
                        . " and idinput='$pnodiv' and nodivisi='$pnodivisi' and stsinput IN ('N') and CONCAT(kodeid,subkode)='239'";
                $tampilb= mysqli_query($cnmy, $query);
                $ketemub= mysqli_num_rows($tampilb);
                if ($ketemub>0) {
                    $brow= mysqli_fetch_array($tampilb);
                    $ptglsy=$brow['tanggal'];
                    if ($ptglsy=="0000-00-00") $ptglsy="";
                    
                    if (!empty($ptglsy)) {
                        $ptglsby =date("d/m/Y", strtotime($ptglsy));
                    }
                }
                
                echo "<table class='tjudul' width='100%'>";
                echo "<tr> <td width='300px' colspan='3'>Kepada : </td></tr>";
                echo "<tr> <td width='300px' colspan='3'>Yth. Ibu Natalia S. / Ibu Vanda</td></tr>";
                echo "<tr> <td width='300px' colspan='3'>PT. SDM - Surabaya</td></tr>";
                
                if ($ppilihrpt=="excel") {
                    echo "<tr> <td>No. </td> <td width='300px' colspan='2'>$pnodivisi</td> </tr>";
                    //echo "<tr> <td>Dilaporkan oleh </td> <td width='300px' colspan='2'>$pnmkaryawan</td> </tr>";
                    
                    echo "<tr> <td>Hal. </td> <td width='300px' colspan='2'>Laporan Kas Kecil Cabang</td> </tr>";
                    if (!empty($ptglsby)) {
                        echo "<tr> <td>Tgl. Surabaya </td> <td width='300px' colspan='2'>$ptglsby</td> </tr>";
                    }
                }else{
                    echo "<tr> <td width='300px' colspan='3'>No. $pnodivisi</td></tr>";
                    //echo "<tr> <td width='300px' colspan='3'>Dilaporkan oleh $pnmkaryawan</td></tr>";
                    echo "<tr> <td width='300px' colspan='3'>Hal. Laporan Kas Kecil Cabang</td></tr>";
                    if (!empty($ptglsby)) {
                        echo "<tr> <td width='300px' colspan='3'>Tgl. Surabaya $ptglsby</td></tr>";
                    }
                }
                
                echo "</table>";
                echo "<br/>&nbsp;";
                
                

                                
                ?>
                <table id='datatable2' class='table table-striped table-bordered example_2' border="1px solid black">
                    <thead>
                        <tr style='background-color:#cccccc; font-size: 13px;'>
                        <th align="center">No.</th>
                        <th align="center">Bulan</th>
                        <th align="center">ID</th>
                        <th align="center">Cabang</th>
                        <th align="center">Yang Mengajukan</th>
                        <th align="center">Realisasi</th>
                        <th align="center">No Rekening</th>
                        <th align="center">Saldo Awal Rp.</th>
                        <th align="center">Isi PC Bln Lalu Rp.</th>
                        <th align="center">Jumlah Rp.</th>
                        <?PHP
                        if ($padaadjustment==true) {
                            echo "<th align='center'>Adjustment</th>";
                        }
                        ?>
                        <th align="center">PC-M Rp.</th>
                        <th align="center">Sisa Rp.</th>
                        <th align="center">Keterangan</th>
                        <?PHP
                        if ($padaadjustment==true) {
                            echo "<th align='center'>Ket. Adjustment</th>";
                        }
                        ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?PHP
                        $no=1;
                        $ptotawal=0;
                        $ptotpclalu=0;
                        $ptotalpengajuan=0;
                        $ptotalpc=0;
                        $ptotaladj=0;
                        $ptotaltransfer=0;
                        $query = "select * from $tmp03 order by bulan, nama_cabang";
                        $tampil=mysqli_query($cnmy, $query);
                        while ($row= mysqli_fetch_array($tampil)) {
                            $ntgl=$row['bulan'];
                            $nidkascab=$row['idkascab'];
                            $nnmcabang=$row['nama_cabang'];
                            $nnmkry=$row['nama_karyawan'];
                            $nrpsldawal=$row['saldoawal'];
                            $prpblnlalu=$row['pc_bln_lalu'];
                            $nrpjml=$row['jumlah'];
                            $nadjrp=$row['jml_adj'];
                            $nadjket=$row['ket_adj'];
                            $nrppc=$row['pcm'];
                            $nketerangan=$row['keterangan'];
                            $nnmreal=$row['nmrealisasi'];
                            $nnorek=$row['norekening'];
                            $pidpengajuan=$row['pengajuan'];
                            
                            $ptotalpengajuanbiaya=(DOUBLE)$ptotalpengajuan+(DOUBLE)$nrpjml;
                            $ptotalpengajuan=(DOUBLE)$ptotalpengajuan+(DOUBLE)$nrpjml;//-(DOUBLE)$nrpsldawal
                            $ptotalpc=(DOUBLE)$ptotalpc+(DOUBLE)$nrppc;
                            
                            $ptotawal=(DOUBLE)$ptotawal+(DOUBLE)$nrpsldawal;
                            $ptotpclalu=(DOUBLE)$ptotpclalu+(DOUBLE)$prpblnlalu;
                            
                            $ntgl = date('M Y', strtotime($ntgl));
                            
                            //$sisarp=(DOUBLE)$nrppc-(DOUBLE)$nrpjml;
                            $sisarp=(DOUBLE)$nrpsldawal+(DOUBLE)$prpblnlalu-(DOUBLE)$nrpjml;
                            
                            $nrpjml=number_format($nrpjml,0,",",",");
                            $nrppc=number_format($nrppc,0,",",",");
                            $sisarp=number_format($sisarp,0,",",",");
                            
                            $ptotaladj=(DOUBLE)$ptotaladj+(DOUBLE)$nadjrp;
                            $nadjrp=number_format($nadjrp,0,",",",");
                            
                            
                            $nrpsldawal=number_format($nrpsldawal,0,",",",");
                            $prpblnlalu=number_format($prpblnlalu,0,",",",");
                            $ptotalpengajuanbiaya=number_format($ptotalpengajuanbiaya,0,",",",");
                            
                            $pnmodulp="bgtkaskecilcabang";
                            if ($pidpengajuan=="OTC" OR $pidpengajuan=="OT" OR$pidpengajuan=="CHC") {
                                $pnmodulp="bgtkaskecilcabangotc";
                            }
                            
                            if ($ppilihrpt=="excel") {
                                $pprint=$nidkascab;
                            }else{
                                $pprint="<a title='Detail Barang / Print' href='#' class='btn_link' data-toggle='modal' "
                                    . "onClick=\"window.open('eksekusi3.php?module=$pnmodulp&brid=$nidkascab&iprint=print',"
                                    . "'Ratting','width=700,height=500,left=500,top=100,scrollbars=yes,toolbar=yes,status=1,pagescrool=yes')\"> "
                                    . "$nidkascab</a>";
                            }
                            
                            echo "<tr>";
                            echo "<td nowrap>$no</td>";
                            echo "<td nowrap>$ntgl</td>";
                            echo "<td nowrap>$pprint</td>";
                            echo "<td nowrap>$nnmcabang</td>";
                            echo "<td nowrap>$nnmkry</td>";
                            echo "<td >$nnmreal</td>";
                            echo "<td nowrap>$nnorek</td>";
                            echo "<td nowrap align='right'>$nrpsldawal</td>";
                            echo "<td nowrap align='right'>$prpblnlalu</td>";
                            echo "<td nowrap align='right' style='font-weight:bold; font-size:12px; color:#000060;'>$nrpjml</td>";
                            if ($padaadjustment==true) {
                                echo "<td nowrap align='right' style='font-weight:bold; font-size:12px; color:#660000;'>$nadjrp</td>";
                            }
                            echo "<td nowrap align='right'>$nrppc</td>";
                            echo "<td nowrap align='right'>$sisarp</td>";
                            echo "<td >$nketerangan</td>";
                            if ($padaadjustment==true) {
                                echo "<td >$nadjket</td>";
                            }
                            echo "</tr>";
                            
                            $no++;
                        }
                        
                        
                        $ptotaltransfer=(DOUBLE)$ptotalpengajuan+(DOUBLE)$ptotaladj;
                        
                        //$sisarp=(DOUBLE)$ptotalpc-(DOUBLE)$ptotalpengajuan;
                        $sisarp=(DOUBLE)$ptotawal+(DOUBLE)$ptotpclalu-(DOUBLE)$ptotalpengajuan;
                        
                        $ptotalpengajuan=number_format($ptotalpengajuan,0,",",",");
                        $ptotalpc=number_format($ptotalpc,0,",",",");
                        
                        
                        $ptotawal=number_format($ptotawal,0,",",",");
                        $ptotpclalu=number_format($ptotpclalu,0,",",",");
                        
                        $sisarp=number_format($sisarp,0,",",",");
                        
                        $ptotaladj=number_format($ptotaladj,0,",",",");
                        
                        
                        echo "<tr style='font-weight:bold;'>";
                        echo "<td nowrap></td>";
                        echo "<td nowrap></td>";
                        echo "<td nowrap></td>";
                        echo "<td nowrap></td>";
                        echo "<td nowrap></td>";
                        echo "<td nowrap></td>";
                        echo "<td nowrap>Total : </td>";
                        echo "<td nowrap align='right'>$ptotawal</td>";
                        echo "<td nowrap align='right'>$ptotpclalu</td>";
                        echo "<td nowrap align='right' style='font-size:12px; color:#000060;'>$ptotalpengajuan</td>";
                        if ($padaadjustment==true) {
                            echo "<td nowrap align='right' style='font-size:12px; color:#660000;'>$ptotaladj</td>";
                        }
                        echo "<td nowrap align='right'>$ptotalpc</td>";
                        echo "<td nowrap align='right'>$sisarp</td>";
                        echo "<td ></td>";
                        if ($padaadjustment==true) {
                            echo "<td ></td>";
                        }
                        echo "</tr>";
                        
                        if ($padaadjustment==true) {
                            $ptotaltransfer=number_format($ptotaltransfer,0,",",",");
                            
                            echo "<tr style='font-weight:bold;'>";
                            echo "<td nowrap></td>";
                            echo "<td nowrap></td>";
                            echo "<td nowrap></td>";
                            echo "<td nowrap></td>";
                            echo "<td nowrap></td>";
                            echo "<td nowrap></td>";
                            echo "<td nowrap>Total Transfer (Realisasi) : </td>";
                            echo "<td nowrap align='right'>&nbsp;</td>";
                            echo "<td nowrap align='right'>&nbsp;</td>";
                            echo "<td nowrap colspan='2' align='center' style='font-size:13px;'>$ptotaltransfer</td>";
                            echo "<td nowrap align='right'>&nbsp;</td>";
                            echo "<td nowrap align='right'>&nbsp;</td>";
                            echo "<td ></td>";
                            echo "<td ></td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
                <?PHP

                echo "<br/>&nbsp;";
                
                echo "<table width='100%' border='0px' style='border : 0px solid #fff; font-size: 11px;'>";
                echo "<tr>";
                    echo "<td colspan='3'>Jakarta, $ptglpd</td>";
                echo "</tr>";
                echo "</table>";
                
                echo "<br/>&nbsp;";
                
                
        
        if ($_GET['ket']=="excel") {
    
                echo "<table class='tjudul' width='100%'>";
                    echo "<tr>";

                        echo "<td align='center'>";
                        echo "Yang Membuat,";
                        echo "<br/>&nbsp;<br/>&nbsp;<br/>$ntgl_apv1<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                        echo "<b>$pyangmembuat</b></td>";


                        echo "<td align='center'>";
                        echo "Checker,";
                        echo "<br/>&nbsp;<br/>&nbsp;<br/>$ntgl_apv2<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                        
                        if ($pdivisipd=="OTC" OR $pdivisipd=="CHC") {
                            echo "<b>SAIFUL RAHMAT</b>";
                        }else{
                            echo "<b>MARIANNE PRASANTI</b>";
                        }
                        echo "</td>";


                        echo "<td align='center'>";
                        echo "Menyetujui,";
                        echo "<br/>&nbsp;<br/>&nbsp;<br/>$ntgl_apv_dir1<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                        echo "<b>$nnama_ss_mktdir</b></td>";
                        
                        
                        echo "<td align='center'>";
                        echo "Mengetahui,";
                        echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                        echo "<b>IRA BUDISUSETYO</b></td>";
                        
                    echo "</tr>";

                echo "</table>";
            
        }else{
            
                echo "<table class='tjudul' width='100%'>";
                    echo "<tr>";

                        echo "<td align='center'>";
                        echo "Yang Membuat,";
                        if (!empty($namapengaju_ttd_fin1))
                            echo "<br/><img src='images/tanda_tangan_base64/$namapengaju_ttd_fin1' height='$gmrheight'><br/>";
                        else
                            echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                        echo "<b>$pyangmembuat</b></td>";


                        echo "<td align='center'>";
                        echo "Checker,";
                        if (!empty($namapengaju_ttd_fin2))
                            echo "<br/><img src='images/tanda_tangan_base64/$namapengaju_ttd_fin2' height='$gmrheight'><br/>";
                        else
                            echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                        
                        if ($pdivisipd=="OTC" OR $pdivisipd=="CHC") {
                            echo "<b>SAIFUL RAHMAT</b>";
                        }else{
                            echo "<b>MARIANNE PRASANTI</b>";
                        }
                        echo "</td>";


                        echo "<td align='center'>";
                        echo "Menyetujui,";
                        if (!empty($namapengaju_ttd1))
                            echo "<br/><img src='images/tanda_tangan_base64/$namapengaju_ttd1' height='$gmrheight'><br/>";
                        else
                            echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                        echo "<b>$nnama_ss_mktdir</b></td>";

                        
                        echo "<td align='center'>";
                        echo "Mengetahui,";
                        if (!empty($namapengaju_ttd2))
                            echo "<br/><img src='images/tanda_tangan_base64/$namapengaju_ttd2' height='$gmrheight'><br/>";
                        else
                            echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                        echo "<b>IRA BUDISUSETYO</b></td>";
                        
                    echo "</tr>";

                echo "</table>";
                
        }
            
                echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                
                
            }
        }
    ?>
    
    
    <?PHP
    hapusdata:
        mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp01");
        mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp02");
        mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp03");
        mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp04");
        mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp05");
        mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp06");
        
        mysqli_close($cnmy);
    ?>
    
            
</body>
</html>