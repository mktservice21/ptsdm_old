<?php
    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","10G");
    ini_set('max_execution_time', 0);
    
    session_start();
    if (!isset($_SESSION['USERID'])) {
        echo "ANDA HARUS LOGIN ULANG....";
        exit;
    }
    
    $ppilihrpt=$_GET['ket'];
    if ($ppilihrpt=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=Laporan_Expenses_CHC_PM.xls");
    }
    
    include("config/koneksimysqli.php");
    include "config/fungsi_combo.php";
    
    $printdate= date("d/m/Y");
    
    $pmodule=$_GET['module'];
    $pidmenu=$_GET['idmenu'];
?>


<?PHP
    $tgl01 = $_POST['e_periode01'];
    $tgl02 = $_POST['e_periode02'];
    
    $pperiode1 = date("Y-m-01", strtotime($tgl01));
    $pperiode2 = date("Y-m-t", strtotime($tgl02));
    
    
    $picardid=$_SESSION['IDCARD'];
    $puserid=$_SESSION['USERID'];

    $now=date("mdYhis");
    $tmp00 =" dbtemp.tmpbrpmexpschc00_".$puserid."_$now ";
    $tmp01 =" dbtemp.tmpbrpmexpschc01_".$puserid."_$now ";
    $tmp02 =" dbtemp.tmpbrpmexpschc02_".$puserid."_$now ";
    $tmp03 =" dbtemp.tmpbrpmexpschc03_".$puserid."_$now ";
    $tmp04 =" dbtemp.tmpbrpmexpschc04_".$puserid."_$now ";
    $tmp05 =" dbtemp.tmpbrpmexpschc05_".$puserid."_$now ";
    $tmp11 =" dbtemp.tmpbrpmexpschc11_".$puserid."_$now ";
    
    
    
    $query = "select distinct b.idinput, b.divisi, b.nodivisi, a.kodeinput, a.bridinput, b.pilih, b.kodeid, b.subkode from dbmaster.t_suratdana_br1 a "
            . " JOIN dbmaster.t_suratdana_br b on a.idinput=b.idinput WHERE "
            . " IFNULL(b.stsnonaktif,'')<>'Y' AND IFNULL(b.nodivisi,'')<>'' AND a.kodeinput IN ('D') "
            . " AND b.divisi='OTC'";
    $query = "create TEMPORARY table $tmp00 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
            $query = "ALTER table $tmp00 ADD COLUMN noidauto BIGINT(20) NOT NULL AUTO_INCREMENT PRIMARY KEY";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            $query = "CREATE UNIQUE INDEX `unx1` ON $tmp00 (noidauto)";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
    $query = "select distinct tanggal, nobukti, idinput, nodivisi from dbmaster.t_suratdana_bank "
            . " WHERE IFNULL(stsnonaktif,'')<>'Y' and stsinput='K' and subkode not in ('29') "
            . " AND idinput IN (select distinct IFNULL(idinput,'') from $tmp00)";
    $query = "create TEMPORARY table $tmp11 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
            $query = "ALTER table $tmp11 ADD COLUMN noidauto BIGINT(20) NOT NULL AUTO_INCREMENT PRIMARY KEY";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            $query = "CREATE UNIQUE INDEX `unx1` ON $tmp11 (noidauto)";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            
            
    $query = "select brOtcId, noslip, icabangid_o, tglbr, tgltrans, tglrpsby, tglreal, COA4, kodeid, subpost, real1, "
            . " jumlah, realisasi, jumlah jumlah_asli, realisasi as realisasi_asli, "
            . " keterangan1, keterangan2, lampiran, ca, jenis, dpp, ppn_rp, pph_rp, tgl_fp, batal "
            . " from hrd.br_otc WHERE IFNULL(batal,'')<>'Y' AND "
            . " brOtcId NOT IN (select DISTINCT IFNULL(brOtcId,'') FROM hrd.br_otc_reject) ";
    $query .= " AND tgltrans BETWEEN '$pperiode1' AND '$pperiode2' ";
    $query .= " AND icabangid_o IN (select DISTINCT IFNULL(cabangid_ho,'') from dbmaster.cabang_otc WHERE pm='Y') ";
    $query = "create TEMPORARY table $tmp01 ($query)";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    
    $query = "ALTER table $tmp01 ADD COLUMN nobuktibbk VARCHAR(20), ADD COLUMN nmkodeid VARCHAR(100), ADD COLUMN nmsubpost VARCHAR(100)";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp01 a JOIN hrd.brkd_otc b on a.subpost=b.subpost SET a.nmkodeid=b.nmsubpost";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp01 a JOIN hrd.brkd_otc b on a.kodeid=b.kodeid AND a.subpost=b.subpost SET a.nmsubpost=b.nama";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "select a.*, b.nama nama_cabang, c.NAMA4 "
            . " from $tmp01 a LEFT JOIN mkt.icabang_o b on a.icabangid_o=b.icabangid_o "
            . " LEFT JOIN dbmaster.coa_level4 c on a.COA4=c.COA4";
    $query = "create TEMPORARY table $tmp02 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "ALTER table $tmp02 ADD COLUMN idinput BIGINT(20), ADD COLUMN nodivisi VARCHAR(20), ADD COLUMN idinput1 BIGINT(20), ADD COLUMN nodivisi1 VARCHAR(20), ADD COLUMN idinput2 BIGINT(20), ADD COLUMN nodivisi2 VARCHAR(20), ADD COLUMN pcm VARCHAR(1), ADD COLUMN kodeid_pd INT(4), ADD COLUMN subkode_pd VARCHAR(5), ADD COLUMN kasbonsby VARCHAR(1)";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
    
    $query = "UPDATE $tmp02 SET nama_cabang=icabangid_o where IFNULL(nama_cabang,'')=''";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "UPDATE $tmp02 a JOIN (select distinct pilih, nodivisi, idinput, bridinput, kodeid, subkode FROM $tmp00 WHERE IFNULL(pilih,'')<>'Y') b on a.brOtcId=b.bridinput "
            . " SET a.nodivisi1=b.nodivisi, a.idinput1=b.idinput, a.kodeid_pd=b.kodeid, a.subkode_pd=b.subkode";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp02 a JOIN (select distinct pilih, nodivisi, idinput, bridinput, kodeid, subkode FROM $tmp00 WHERE IFNULL(pilih,'')='Y') b on a.brOtcId=b.bridinput "
            . " SET a.nodivisi2=b.nodivisi, a.idinput2=b.idinput, a.kodeid_pd=b.kodeid, a.subkode_pd=b.subkode";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp02 SET nodivisi=nodivisi2, idinput=idinput2";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    $query = "UPDATE $tmp02 SET nodivisi=nodivisi1, idinput=idinput1 WHERE IFNULL(nodivisi,'')=''";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
        
    //isi tanggal transfer no bukti bbk bobukti
    $query = "UPDATE $tmp02 a JOIN $tmp11 b on a.idinput=b.idinput SET a.nobuktibbk=b.nobukti";//a.nobukti=b.nobukti, a.tgltrans=b.tanggal
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    
    
    $query = "UPDATE $tmp02 SET pcm='Y' WHERE IFNULL(nodivisi1,'')<>'' AND IFNULL(nodivisi2,'')=''"; 
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    $query = "UPDATE $tmp02 SET kasbonsby='Y' WHERE CONCAT(kodeid_pd,subkode_pd) IN ('680')"; 
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


    $query = "UPDATE $tmp02 SET pcm='' WHERE "
            . " IFNULL(nodivisi2,'')='' AND IFNULL(lampiran,'')='Y' AND IFNULL(ca,'')='N'"
            . " AND CONCAT(kodeid_pd,subkode_pd) NOT IN ('680')";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
    
    $query = "UPDATE $tmp02 SET jumlah=realisasi WHERE IFNULL(realisasi,0)<>0";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
    //SALES 
    $query = "select icabangid, areaid, icustid, iprodid, 
		ecustid, distid, ecabangid, grp_sls, cabang, area, cabangytd, outlet, 
		sektor, alokasi, produk, divprodid, fakturid, tgljual, qty, hna, `value` as rpsales FROM mkt.otc_etl WHERE 
                 DATE_FORMAT(tgljual,'%Y%m') BETWEEN '$pperiode1' AND '$pperiode2' AND divprodid <>'OTHER' and icabangid <> 22";
    
    $query = "select a.iprodid, b.nama namaproduk, c.GRP_FKIDEN, d.GRP_NAMESS, sum(a.`value`) as rpsales 
            from MKT.otc_etl a 
            left JOIN MKT.iproduk b on a.iprodid=b.iprodid 
            left join MKT.T_OTC_GRPPRD_DETAIL c on b.iprodid=c.GRP_IDPROD
            left join MKT.T_OTC_GRPPRD d on c.GRP_FKIDEN = d.GRP_IDENTS WHERE 
            a.tgljual BETWEEN '$pperiode1' AND '$pperiode2' AND a.divprodid <>'OTHER' and a.icabangid <> 22";
    $query .=" AND c.GRP_FKIDEN IN ('6', '1', '4', '5', '2', '3', '7', '10')";
    $query .=" GROUP BY 1,2,3,4";
    $query = "create TEMPORARY table $tmp03 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    /*
 6 	 MELANOX DECORATIVE 	
 1 	 MELANOX PREMIUM 	
		
 4 	 PARASOL (FOCUS) 	
 5 	 PARASOL EXIST 	
		
 2 	 CARMED LOTION 	
		
 3 	 LANORE MAKE UP 	
 7 	 LANORE SKIN CARE 	
		
 10 	 ACNEMED 	
	
     * 
     */

    //END SALES
    
    $query = "SELECT distinct icabangid_o, nama_cabang FROM $tmp02";
    $query = "create TEMPORARY table $tmp04 ($query)";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "INSERT INTO $tmp04 (icabangid_o, nama_cabang) SELECT distinct icabangid, nama_cabang FROM $tmp03 WHERE "
            . " icabangid NOT IN (select distinct IFNULL(icabangid_o,'') FROM $tmp02)";
    //mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "CREATE TEMPORARY TABLE $tmp05 (idket VARCHAR(1), nama_ket VARCHAR(100))";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query ="INSERT INTO $tmp05 (idket, nama_ket)VALUES('1', 'EXPENSES'), ('2', 'SALES')";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $arridcab[]="";
    $arrnmcab[]="";
    $query = "select distinct icabangid_o, nama_cabang from $tmp04 order by nama_cabang, icabangid_o";
    $tampilk= mysqli_query($cnmy, $query);
    while ($zr= mysqli_fetch_array($tampilk)) {
        $zidcab=$zr['icabangid_o'];
        $znmcab=$zr['nama_cabang'];
        
        $arridcab[]=$zidcab;
        $arrnmcab[]=$znmcab;
    }
    
    $addcolumn="";
    for($ix=1;$ix<count($arridcab);$ix++) {
        $zidcab=$arridcab[$ix];
        $znmcab=$arrnmcab[$ix];
        
        $nmfield1="B".$zidcab;
        $nmfield2="S".$zidcab;
        
        $addcolumn .= " ADD COLUMN $nmfield1 DECIMAL(20,2),";//, ADD COLUMN $nmfield2 DECIMAL(20,2)
        
    }
    $addcolumn .= " ADD COLUMN BTOTAL DECIMAL(20,2)";//ADD COLUMN TOTAL DECIMAL(20,2), 
    
    $query = "ALTER TABLE $tmp05 $addcolumn";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    for($ix=1;$ix<count($arridcab);$ix++) {
        $zidcab=$arridcab[$ix];
        $znmcab=$arrnmcab[$ix];
        
        $filcabid=$zidcab;
        $nmfield1="a.B".$zidcab;
        
        
        $query = "UPDATE $tmp05 a JOIN (select '1' as idket, sum(jumlah) as jumlah from $tmp02 WHERE IFNULL(icabangid_o,'')='$filcabid' GROUP BY 1) b on "
                . " a.idket=b.idket SET $nmfield1=b.jumlah WHERE a.idket='1'";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $filcabid2=" AND IFNULL(GRP_FKIDEN,'')='$filcabid'" ;
        if ($filcabid=="PM_ACNEMED") $filcabid2= " AND IFNULL(GRP_FKIDEN,'') IN ('10')";
        if ($filcabid=="PM_CARMED") $filcabid2= " AND IFNULL(GRP_FKIDEN,'') IN ('2')";
        if ($filcabid=="PM_LANORE") $filcabid2= " AND IFNULL(GRP_FKIDEN,'') IN ('3', '7')";
        if ($filcabid=="PM_MELANOX") $filcabid2= " AND IFNULL(GRP_FKIDEN,'') IN ('6', '1')";
        if ($filcabid=="PM_PARASOL") $filcabid2= " AND IFNULL(GRP_FKIDEN,'') IN ('4', '5')";
        
        $query = "UPDATE $tmp05 a JOIN (select '2' as idket, sum(rpsales) rpsales from $tmp03 WHERE 1=1 $filcabid2 GROUP BY 1) b on "
                . " a.idket=b.idket SET $nmfield1=b.rpsales WHERE a.idket='2'";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    }
    
?>

<HTML>
<HEAD>
    <title>REPORT EXPENSES CHC BY PM</title>
    <?PHP if ($ppilihrpt!="excel") { ?>
        <meta http-equiv="Expires" content="Mon, 01 Mei 2050 1:00:00 GMT">
        <meta http-equiv="Pragma" content="no-cache">
        <link rel="shortcut icon" href="images/icon.ico" />
        <!--<link href="css/laporanbaru.css" rel="stylesheet">-->
        <?php header("Cache-Control: no-cache, must-revalidate"); ?>
        
        <!-- Bootstrap -->
        <link href="vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

    
        <link href="vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css" rel="stylesheet">


        <link href="vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css" rel="stylesheet">


        <script src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.flash.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
        
		
		
		
		<!-- jQuery -->
        <script src="vendors/jquery/dist/jquery.min.js"></script>
        
        
    <?PHP } ?>
    
</HEAD>
<BODY class="nav-md">
    
<div id='n_content'>
    
    <center><div class='h1judul'>REPORT EXPENSES CHC BY PM</div></center>
    
    <div id="divjudul">
        <table class="tbljudul">
            <tr><td>Periode</td><td>:</td><td><?PHP echo "<b>$tgl01 s/d. $tgl02</b>"; ?></td></tr>
            <tr class='miring text2'><td>view date</td><td>:</td><td><?PHP echo "$printdate"; ?></td></tr>
        </table>
    </div>
    <div class="clearfix"></div>
    <hr/>
    
    
        <table id='mydatatable1' class='table table-striped table-bordered' width="100%" border="1px solid black">
            <thead>
                <tr style='background-color:#cccccc; font-size: 13px;'>
                    <th align="center" nowrap>&nbsp;</th>

                    <?PHP
                    $jmlcolspan=0;
                    for($ix=1;$ix<count($arridcab);$ix++) {
                        $zidcab=$arridcab[$ix];
                        $znmcab=$arrnmcab[$ix];
                        
                        if ($znmcab=="zz") $znmcab="OTHERS";
                        $znmcab = str_replace("PM_", "", $znmcab);
                        echo "<th align='center' nowrap>$znmcab</th>";
                        
                        $jmlcolspan++; $jmlcolspan++;
                    }
                    $jmlcolspan=(double)$jmlcolspan+3;
                    
                    ?>
                    
                    <th align="center" nowrap>TOTAL</th>
                </tr>
            </thead>
            <tbody>
                <?PHP
                    for($ix=1;$ix<count($arridcab);$ix++) {
                        $psubtot[$ix]=0;
                        $psubtot_b[$ix]=0;
                        $psubtot_s[$ix]=0;
                    }
                    $query = "select * from $tmp05 order by idket";
                    $tampil2=mysqli_query($cnmy, $query);
                    while ($row2= mysqli_fetch_array($tampil2)) {
                        $pidket=$row2['idket'];
                        $pnamaket=$row2['nama_ket'];
                        
                            
                        echo "<tr>";
                        echo "<td nowrap>$pnamaket</td>";

                        $ptotaltahund=0;
                        for($ix=1;$ix<count($arridcab);$ix++) {
                            $zidcab=$arridcab[$ix];
                            $znmcab=$arrnmcab[$ix];

                            $nmcol="B".$zidcab;
                            $pjml=$row2[$nmcol];
                            if (empty($pjml)) $pjml=0;
                                
                            $ptotaltahund=(double)$ptotaltahund+(double)$pjml;
                            $psubtot[$ix]=(double)$psubtot[$ix]+(double)$pjml;
                            
                            if ($pidket=="1") {
                                $psubtot_b[$ix]=(double)$psubtot_b[$ix]+(double)$pjml;
                            }elseif ($pidket=="2") {
                                $psubtot_s[$ix]=(double)$psubtot_s[$ix]+(double)$pjml;
                            }
                            
                            $pjml=number_format($pjml,0,",",",");
                            
                            $pjmllink=$pjml;
                            if ($pidket=="1" AND $ppilihrpt!="excel") {
                                if ($pjml<>"0") {
                                    $pjmllink="<a href='?module=lapbudgetexpenseschcdet&act=input&idmenu=$pidmenu&icb=$zidcab&perd1=$tgl01&perd2=$tgl02&ket=bukan' target='_blank'>$pjml</a>";
                                }
                            }
                        
                            echo "<td align='right' nowrap>$pjmllink</td>";
                                
                        }
                        $ptotaltahund=number_format($ptotaltahund,0,",",",");
                        
                        $pjmllink_=$ptotaltahund;
                        if ($pidket=="1" AND $ppilihrpt!="excel") {
                            if ($ptotaltahund<>"0") {
                                $pjmllink_="<a href='?module=lapbudgetexpenseschcdet&act=input&idmenu=$pidmenu&icb=&perd1=$tgl01&perd2=$tgl02&ket=bukan' target='_blank'>$ptotaltahund</a>";
                            }
                        }
                            
                        echo "<td align='right' nowrap>$pjmllink_</td>";
                        echo "</tr>";
                    }
                    
                    //total
                    echo "<tr>";
                    echo "<td nowrap><b>CR (%)</b></td>";
                    
                    $ptotaltahund=0;
                    $ptotaltahund_b=0;
                    $ptotaltahund_s=0;
                    for($ix=1;$ix<count($arridcab);$ix++) {
                        $pjml=$psubtot[$ix];
                        $pjml_b=$psubtot_b[$ix];
                        $pjml_s=$psubtot_s[$ix];
                        if (empty($pjml)) $pjml=0;
                        if (empty($pjml_b)) $pjml_b=0;
                        if (empty($pjml_s)) $pjml_s=0;
                        
                        $ptotaltahund=(double)$ptotaltahund+(double)$pjml;
                        $ptotaltahund_b=(double)$ptotaltahund_b+(double)$pjml_b;
                        $ptotaltahund_s=(double)$ptotaltahund_s+(double)$pjml_s;
                        
                        $pperjml=0;
                        if ((DOUBLE)$pjml_s<>0) $pperjml=(DOUBLE)$pjml_b/(DOUBLE)$pjml_s*100;
                        
                        $pjml=number_format($pjml,0,",",",");
                        $pperjml=ROUND($pperjml,2);
                        
                        echo "<td align='right' nowrap><b>".$pperjml."</b></td>";
                    }
                    $pperjml=0;
                    if ((DOUBLE)$ptotaltahund_s<>0) $pperjml=(DOUBLE)$ptotaltahund_b/(DOUBLE)$ptotaltahund_s*100;
                        
                    $ptotaltahund=number_format($ptotaltahund,0,",",",");
                    $pperjml=ROUND($pperjml,2);
                    
                    echo "<td align='right' nowrap><b>$pperjml</b></td>";

                    echo "</tr>";
                ?>
            </tbody>
        </table>
        <br/>&nbsp;<br/>&nbsp;<br/>&nbsp;
    
</div>

    <?PHP if ($ppilihrpt!="excel") { ?>

        
        <!-- Bootstrap -->
        <script src="vendors/bootstrap/dist/js/bootstrap.min.js"></script>
    
        <!-- Datatables -->
		
        <script src="vendors/datatables.net/js/jquery.dataTables.min.js"></script>
        <script src="vendors/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
        <script src="vendors/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
        <script src="vendors/datatables.net-buttons-bs/js/buttons.bootstrap.min.js"></script>
        <script src="vendors/datatables.net-buttons/js/buttons.flash.min.js"></script>
        <script src="vendors/datatables.net-buttons/js/buttons.html5.min.js"></script>
        <script src="vendors/datatables.net-buttons/js/buttons.print.min.js"></script>
        <script src="vendors/datatables.net-fixedheader/js/dataTables.fixedHeader.min.js"></script>
        <script src="vendors/datatables.net-keytable/js/dataTables.keyTable.min.js"></script>
        <script src="vendors/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
        <script src="vendors/datatables.net-responsive-bs/js/responsive.bootstrap.js"></script>
        <script src="vendors/datatables.net-scroller/js/dataTables.scroller.min.js"></script>
        <script src="vendors/jszip/dist/jszip.min.js"></script>
        <script src="vendors/pdfmake/build/pdfmake.min.js"></script>
        <script src="vendors/pdfmake/build/vfs_fonts.js"></script>

        
        
        <style>
            #myBtn {
                display: none;
                position: fixed;
                bottom: 20px;
                right: 30px;
                z-index: 99;
                font-size: 18px;
                border: none;
                outline: none;
                background-color: red;
                color: white;
                cursor: pointer;
                padding: 15px;
                border-radius: 4px;
                opacity: 0.5;
            }

            #myBtn:hover {
                background-color: #555;
            }

        </style>

        <style>
            #n_content {
                color:#000;
                font-family: "Arial";
                margin: 5px 20px 20px 20px;
                /*overflow-x:auto;*/
            }
            
            .h1judul {
              color: blue;
              font-family: verdana;
              font-size: 140%;
              font-weight: bold;
            }
            table.tbljudul {
                font-size : 15px;
            }
            table.tbljudul tr td {
                padding: 1px;
                font-family : "Arial, Verdana, sans-serif";
            }
            .tebal {
                 font-weight: bold;
            }
            .miring {
                 font-style: italic;
            }
            table.tbljudul tr.text2 {
                font-size : 13px;
            }
            
            table {
                text-align: left;
                position: relative;
                border-collapse: collapse;
                background-color:#FFFFFF;
            }

            th {
                background: white;
                position: sticky;
                top: 0;
                box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
                z-index:1;
            }

            .th2 {
                background: white;
                position: sticky;
                top: 23;
                box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
                border-top: 1px solid #000;
            }
        </style>
    
        <style>
            
            .divnone {
                display: none;
            }
            #mydatatable1, #mydatatable2 {
                color:#000;
                font-family: "Arial";
            }
            #mydatatable1 th, #mydatatable2 th {
                font-size: 12px;
            }
            #mydatatable1 td, #mydatatable2 td { 
                font-size: 11px;
            }
        </style>
        
    <?PHP }else{ ?>
        <style>
            .h1judul {
              font-size: 140%;
              font-weight: bold;
            }
        </style>
    <?PHP } ?>
</BODY>

<?PHP
hapusdata:
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp00");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp01");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp02");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp03");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp04");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp05");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp11");
    mysqli_close($cnmy);
?>