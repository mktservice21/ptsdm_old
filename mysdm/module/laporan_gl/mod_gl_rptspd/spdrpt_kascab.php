<?php
    if ($_GET['ket']=="bukan") {
        
        if (!empty($spdidinput)) {
            
            echo "<table>";
            echo "<tr>";
                echo "<td>";
                    echo "<a class='btn button1' href='eksekusi3.php?module=glreportspddetail&act=input&idmenu=$_GET[idmenu]&ket=excel&divisi=$_GET[divisi]&nodivisi=$_GET[nodivisi]&idinspd=$spdidinput' target='_blank'>EXCEL</a>";
                echo "</td>";
                echo "<td>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</td>";
                echo "<td>";
                if ($psts_posting==true) {
                    
                    if ($psudahpost==true) {
                        echo "<input type='button' class='btn button3' value='HAPUS POST' onClick=\"ProsesDataPosting('hapuspost', '$spdidinput', '$spdnodivisi')\">";
                    }else{
                        echo "<input type='button' class='btn button2' value='POST' onClick=\"ProsesDataPosting('posting', '$spdidinput', '$spdnodivisi')\">";
                    }
                    
                }
                echo "</td>";
            echo "</tr>";
            echo "</table>";
            echo "<br/>&nbsp;<br/>&nbsp;";
            
        }
        
    }
?>



<?PHP
    include("config/fungsi_sql.php");
    
        $pstsspd="2";
        $pnodiv=$spdidinput;
        
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
        
        
        $pyangmembuat="ERVIYANTI";
        
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
        
        $query = "select a.nmrealisasi, a.norekening, a.pengajuan, a.idkascab, a.tanggal, a.COA4, c.NAMA4, a.karyawanid, d.nama nama_karyawan,
            a.icabangid, b.nama as nama_cabang, a.icabangid_o, e.nama as namacab_o, 
            a.areaid, f.nama as nama_area, a.areaid_o, g.nama as namaarea_o,
            a.keterangan, a.jumlah, a.stsnonaktif, h.saldoawal   
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
        
        
            $query = "SELECT a.*, b.tglf, b.tglt, b.divisipd, b.kodenama, b.tglpd, b.nomor, b.idinput, b.nodivisi, 
                b.nobbm, b.nobbk, b.urutan, b.amount, b.coa, b.coa_nama, b.jumlahpd,
                b.jenis_rpt, b.kodeid, b.subkode, b.jml_kasbon, CAST(0 as DECIMAL(20,2)) as kuranglebihrp, 
                CAST('' as CHAR(100)) as ketkurleb, CAST('' as CHAR(1)) as npilih, CAST(0 as DECIMAL(20,2)) as jmlperycash 
                FROM $tmp02 a JOIN $tmp01 b on a.idkascab=b.bridinput";
            
        $query = "create TEMPORARY table $tmp03 ($query)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
        //bank
        $query = "SELECT a.idinputbank, a.stsinput, a.nobukti, a.idinput, a.nodivisi, a.tanggal as tgltrans FROM dbmaster.t_suratdana_bank as a "
                . " WHERE IFNULL(a.stsinput,'')='K' AND idinput='$pnodiv' AND IFNULL(a.nobukti,'')<>'' LIMIT 1";
        $query = "create TEMPORARY table $tmp06 ($query)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        

        $query = "UPDATE $tmp03 a join dbmaster.t_uangmuka_kascabang b on a.icabangid=b.icabangid SET a.jmlperycash=b.jumlah WHERE"
                . " pengajuan='ETH'";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

        $query = "UPDATE $tmp03 a join dbmaster.t_uangmuka_kascabang b on a.icabangid=b.icabangid SET a.jmlperycash=b.jumlah WHERE"
                . " pengajuan IN ('OTC', 'OT', 'CHC')";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        
        $query = "select a.idkascab, a.kode, b.nama nama_kode, a.jumlahrp, a.notes, a.coa4, c.NAMA4, b.coa_kode, d.NAMA4 nama_coa 
                from dbmaster.t_kaskecilcabang_d a LEFT JOIN dbmaster.t_kode_kascab b on a.kode=b.kode 
                left JOIN dbmaster.coa_level4 c on a.coa4=c.COA4 
                left JOIN dbmaster.coa_level4 d on b.coa_kode=d.COA4 
                JOIN $tmp03 e on a.idkascab=e.idkascab 
                where a.idkascab=e.idkascab";
        $query = "create TEMPORARY table $tmp04 ($query)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
		//khusus bogor
        $query = "INSERT INTO $tmp04 (idkascab, kode, nama_kode, jumlahrp, notes, coa4, NAMA4, coa_kode, nama_coa)"
                . " select idkascab, 'A0000' as kode, 'saldo awal' as nama_kode, saldoawal, '' as notes, coa, coa_nama, "
                . " coa as coa_kode, coa_nama as coa_nm_kode "
                . " from $tmp03 WHERE IFNULL(saldoawal,0)<>0 AND idkascab='C200900002'";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query = "UPDATE $tmp04 SET coa4=coa_kode, NAMA4=nama_coa WHERE IFNULL(coa4,'')=''"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query = "UPDATE $tmp04 SET jumlahrp=-1*IFNULL(jumlahrp,0) WHERE IFNULL(kode,'')='A0000'"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        $query = "select a.idkascab, a.kode, a.nama_kode, a.jumlahrp, a.notes, a.coa4, a.NAMA4, a.coa_kode, a.nama_coa, "
                . " e.nmrealisasi, e.norekening, e.pengajuan, e.tanggal, e.karyawanid, e.nama_karyawan,
                e.icabangid, e.nama_cabang, e.icabangid_o, e.namacab_o, 
                e.areaid, e.nama_area, e.areaid_o, e.namaarea_o,
                e.keterangan, e.jumlah, e.stsnonaktif, e.saldoawal, e.jmlperycash, e.nodivisi, b.nobukti, b.tgltrans  
                FROM $tmp04 as a JOIN $tmp03 as e on a.idkascab=e.idkascab "
                . " LEFT JOIN $tmp06 as b on b.nodivisi=e.nodivisi AND e.idinput=b.idinput";
        $query = "create TEMPORARY table $tmp05 ($query)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        $query = "DELETE FROM $tmp05 WHERE IFNULL(jumlahrp,0)=0"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        
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

                $ppengajuanpd=$pdivisipd;
                $ppengajuanpd2="BR $pdivisipd";
                
                $pjenisrpt=$r["jenis_rpt"];
                $nket="Laporan Kas Kecil periode ";
                
                
                if (!empty($r['tglpd']) AND $r['tglpd']<>"0000-00-00")
                    $ptglpd =date("d F Y", strtotime($r['tglpd']));
                
                $ptglpd_f = "";
                if (!empty($r['tglf']) AND $r['tglf']<>"0000-00-00")
                    $ptglpd_f =date("d M Y", strtotime($r['tglf']));
                
                $ptglpd_t = "";
                if (!empty($r['tglt']) AND $r['tglt']<>"0000-00-00")
                    $ptglpd_t =date("d M Y", strtotime($r['tglt']));
                    
                echo "<table class='tjudul' width='100%'>";
                
                if ($ppilihrpt=="excel") {
                    echo "<tr> <td>No. </td> <td width='300px' colspan='2'>$pnodivisi</td> </tr>";
                    echo "<tr> <td>Kas Kecil Cabang </td> <td width='300px' colspan='2'></td> </tr>";
                }else{
                    echo "<tr> <td width='300px' colspan='3'>No. $pnodivisi</td></tr>";
                    echo "<tr> <td width='300px' colspan='3'>Kas Kecil Cabang</td></tr>";
                }
                
                echo "</table>";
                echo "<br/>&nbsp;";
                
                
                $query = "select * FROM $tmp05";
                $jmlrec=mysqli_num_rows(mysqli_query($cnit, $query));
                $plimit=30;
                $pjmlfor=ceil((double)$jmlrec / (double)$plimit);
                            
                $nnomorjml=1;
                $pjmlsudah=0;
                $pgrdntot=0;
                for($ijml=1;$ijml<=$pjmlfor;$ijml++) {
            
                ?>
                    <table id='datatable2' class='table table-striped table-bordered example_2' border="1px solid black">
                        <thead>
                            <tr style='background-color:#cccccc; font-size: 13px;'>
                            <th align="center">Date</th>
                            <th align="center">Bukti</th>
                            <th align="center">Kode</th>
                            <th align="center">Perkiraan</th>
                            <th align="center">Nama</th>
                            <th align="center">Cabang</th>
                            <th align="center">Pengajuan</th>
                            <th align="center">Realisasi</th>
                            <th align="center">Keterangan</th>
                            <th align="center">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?PHP
                            $no=1;
                            $ptotalpengajuan=0;
                            $ptotalpc=0;
                            $query = "select * from $tmp05 order by nama_cabang LIMIT $pjmlsudah, $plimit";
                            $tampil=mysqli_query($cnmy, $query);
                            while ($row= mysqli_fetch_array($tampil)) {
                                $ntgl=$row['tgltrans'];
                                $nidkascab=$row['idkascab'];
                                $nnmcabang=$row['nama_cabang'];
                                $nnmkry=$row['nama_karyawan'];
                                $nrpsldawal=$row['saldoawal'];
                                $nrpjml=$row['jumlah'];
                                $nrppc=$row['jmlperycash'];
                                $nrpjmlamount=$row['jumlahrp'];
                                $nketerangan=$row['keterangan'];
                                $nnmreal=$row['nmrealisasi'];
                                $nnorek=$row['norekening'];
                                $nnobukti=$row['nobukti'];
                                $npcoa4=$row['coa4'];
                                $npnmcoa4=$row['NAMA4'];
                                $npnmkode=$row['nama_kode'];
                                $npnotes=$row['notes'];

                                if (empty($npnotes)) $npnotes=$nketerangan;

                                $ptotalpengajuan=(double)$ptotalpengajuan+(double)$nrpjmlamount;
                                $pgrdntot=(double)$pgrdntot+(double)$nrpjmlamount;
                                $ntgl = date('d/m/Y', strtotime($ntgl));
                                $nrpjmlamount=number_format($nrpjmlamount,0,",",",");



                                echo "<tr>";
                                echo "<td nowrap>$ntgl</td>";
                                echo "<td nowrap>$nnobukti</td>";
                                echo "<td nowrap>$npcoa4</td>";
                                echo "<td nowrap>$npnmcoa4</td>";
                                echo "<td nowrap>$npnmkode</td>";
                                echo "<td nowrap>$nnmcabang</td>";
                                echo "<td nowrap>$nnmkry</td>";
                                echo "<td nowrap>$nnmreal</td>";
                                echo "<td nowrap>$npnotes</td>";
                                echo "<td nowrap align='right'>$nrpjmlamount</td>";
                                echo "</tr>";

                                $no++;
                                
                                $pjmlsudah++;
                            }

                            $ptotalpengajuan=number_format($ptotalpengajuan,0,",",",");

                            echo "<tr style='font-weight:bold;'>";
                            echo "<td nowrap></td>";
                            echo "<td nowrap></td>";
                            echo "<td nowrap></td>";
                            echo "<td nowrap></td>";
                            echo "<td nowrap></td>";
                            echo "<td nowrap></td>";
                            echo "<td nowrap></td>";
                            echo "<td nowrap></td>";
                            echo "<td nowrap>Total : </td>";
                            echo "<td nowrap align='right'>$ptotalpengajuan</td>";
                            echo "</tr>";
                            
                            if ($pjmlfor==$nnomorjml) {
                                if ((double)$pjmlfor>1) {
                                    
                                    $pgrdntot=number_format($pgrdntot,0,",",",");
                                    
                                    echo "<tr style='font-weight:bold;'>";
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
                                    echo "</tr>";

                                    echo "<tr style='font-weight:bold;'>";
                                    echo "<td nowrap></td>";
                                    echo "<td nowrap></td>";
                                    echo "<td nowrap></td>";
                                    echo "<td nowrap></td>";
                                    echo "<td nowrap></td>";
                                    echo "<td nowrap></td>";
                                    echo "<td nowrap></td>";
                                    echo "<td nowrap></td>";
                                    echo "<td nowrap>Grand Total :</td>";
                                    echo "<td nowrap align='right'>$pgrdntot</td>";
                                    echo "</tr>";
                                    
                                }
                            }
                            
                            
                            $nnomorjml++
                            ?>
                        </tbody>
                    </table>

                <?PHP
                    echo "<br/>&nbsp;<br/>&nbsp;";
                }
                
                
        

            
               
                
                
            }
        }
         echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
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