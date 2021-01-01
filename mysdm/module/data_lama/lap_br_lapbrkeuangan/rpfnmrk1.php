<?PHP
    session_start();
    if ($_GET['ket']=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=Rekap Permohonan Dana Budged Request.xls");
    }
?>

<html>
<head>
    <title>Laporan Keuangan Marketing</title>
    <meta http-equiv="Expires" content="Mon, 01 Jan 2007 1:00:00 GMT">
    <meta http-equiv="Pragma" content="no-cache">
    <link rel="shortcut icon" href="images/icon.ico" />
    <?php header("Cache-Control: no-cache, must-revalidate"); ?>
</head>
<form action="rpfnmrk0.php" method=post>

<?php
	include_once("config/common.php");
        include "config/koneksimysqli_it.php";
	
	
	$tahun = $_POST['tahun']; //echo"tahun=$tahun";
	
        
                $eprodid="";
                for ($x=0;$x<300;$x++) {
                    $prodTQty_[$x]=0;
                    $produkTQty_[$x]=0;
                    
                    $zzzx = substr('00'.$x,-2);
                    $prodTQty_[$zzzx]=0;
                    $produkTQty_[$zzzx]=0;
                }
                
                $t_total=0;
                $ccyid="";
                $t1_total=0;
                $t_total1=0;
                $t_total2=0;
                $t_uibr="";
                $t_uitm="";
                $no=0;
                $ccyid_="";
                
                
	$query_dlds = "delete from hrd.brfnmrkt";
	$result_dlds = mysqli_query($cnit, $query_dlds);
        
        
            $now=date("mdYhis");
            $tmp01 =" dbtemp.RPTREKBMABG01_".$_SESSION['USERID']."_$now ";

            $query = "select * from hrd.br0
                     where left(tgltrans,4)='$tahun' and retur <> 'Y' and batal <>'Y'";
            $query = "create TEMPORARY table $tmp01 ($query)";
            mysqli_query($cnit, $query);
        
            $query = "UPDATE $tmp01 SET jumlah=jumlah1 WHERE IFNULL(jumlah1,0)<>0";
            mysqli_query($cnit, $query);
            
            
        
        
	// promosi
	$query_dlp = "delete from hrd.brfnmrkt where kodeid='01'";
	$result_dlp = mysqli_query($cnit, $query_dlp);
	$query_po = "select SUM(jumlah) as jumlah,ccyid, left(tgltrans,7) as bulan from $tmp01 br0  
			    where (br0.kode='700-01-01' or br0.kode='700-02-01' or br0.kode='700-04-01')  and 
			    ((left(tgltrans,4)='$tahun' and tglunrtr='0000-00-00') 
			    or LEFT(tglunrtr,4)='$tahun') and retur <> 'Y' and batal <>'Y' and left(tgltrans,4)='$tahun' 
			    group by left(tgltrans,7),ccyid
			    order by tgltrans"; //echo"$query_po";
	$result_po = mysqli_query($cnit, $query_po);
	$records_po = mysqli_num_rows($result_po);
	$row_po = mysqli_fetch_array($result_po);
	$i = 1;
	while ($i <= $records_po) {
		$ccyid_po = $row_po['ccyid'];
                if (empty($ccyid_po)) $ccyid_po="IDR";
		$jumlah_po = $row_po['jumlah'];  
		$bulan_po = $row_po['bulan'];
		$kodeid_po = "01"; //echo"$kodeid_po";
	
		$query = "insert into hrd.brfnmrkt (kodeid,bulan,jumlah,ccyid) 
				  values ('$kodeid_po','$bulan_po','$jumlah_po','$ccyid_po')"; //echo"$query";
		$result = mysqli_query($cnit, $query);			   
		$row_po = mysqli_fetch_array($result_po);
		$i++;
	}	

	// DCC
	$query_dldc = "delete from hrd.brfnmrkt where kodeid='02'";
	$result_dldc = mysqli_query($cnit, $query_dldc);
	$query_dc = "select SUM(jumlah) as jumlah,ccyid, left(tgltrans,7) as bulan from $tmp01 br0  
				 where (br0.kode='700-01-03' or br0.kode='700-02-03' or br0.kode='700-04-03')  and ((left(tgltrans,4)='$tahun' and tglunrtr='0000-00-00') 
				 or LEFT(tglunrtr,4)='$tahun') and retur <> 'Y' and batal <>'Y' and left(tgltrans,4)='$tahun' 
				 group by left(tgltrans,7),ccyid
				 order by tgltrans"; //echo"$query_dc";
	$result_dc = mysqli_query($cnit, $query_dc);
	$records_dc = mysqli_num_rows($result_dc);
	$row_dc = mysqli_fetch_array($result_dc);
	$i = 1;
	while ($i <= $records_dc) {
		$ccyid_dc = $row_dc['ccyid'];
                if (empty($ccyid_dc)) $ccyid_dc="IDR";
		$jumlah_dc = $row_dc['jumlah'];  
		$bulan_dc = $row_dc['bulan'];
		$kodeid_dc = "02"; //echo"$kodeid_po";
		$query = "insert into hrd.brfnmrkt (kodeid,bulan,jumlah,ccyid) 
				  values ('$kodeid_dc','$bulan_dc','$jumlah_dc','$ccyid_dc')"; //echo"$query";
		$result = mysqli_query($cnit, $query);		
		$row_dc = mysqli_fetch_array($result_dc);
		$i++;
	}	
	
	// DSS
	$query_dlds = "delete from hrd.brfnmrkt where kodeid='03'";
	$result_dlds = mysqli_query($cnit, $query_dlds);
	$query_ds = "select SUM(jumlah) as jumlah,ccyid, left(tgltrans,7) as bulan from $tmp01 br0  
				 where (br0.kode='700-01-04' or br0.kode='700-02-04' or br0.kode='700-04-04')  and ((left(tgltrans,4)='$tahun' and tglunrtr='0000-00-00') 
				 or LEFT(tglunrtr,4)='$tahun') and retur <> 'Y' and batal <>'Y' and left(tgltrans,4)='$tahun' 
				 group by left(tgltrans,7),ccyid
				 order by tgltrans"; //echo"$query_ds<br>";
	$result_ds = mysqli_query($cnit, $query_ds);
	$records_ds = mysqli_num_rows($result_ds);
	$row_ds = mysqli_fetch_array($result_ds);
	$i = 1;
	while ($i <= $records_ds) {
		$ccyid_ds = $row_ds['ccyid'];
                if (empty($ccyid_ds)) $ccyid_ds="IDR";
		$jumlah_ds = $row_ds['jumlah'];  
                //echo "$jumlah_ds<br/>";
		$bulan_ds = $row_ds['bulan']; //ECHO"$bulan_ds<br>";
		$kodeid_ds = "03"; //echo"$kodeid_po";
		$query = "insert into hrd.brfnmrkt (kodeid,bulan,jumlah,ccyid) 
				  values ('$kodeid_ds','$bulan_ds','$jumlah_ds','$ccyid_ds')"; //echo"$query";
		$result = mysqli_query($cnit, $query);		
		$row_ds = mysqli_fetch_array($result_ds);
		$i++;
	}	
	//goto hapusdata;
	//SIMPOSIUM
	$query_dls = "delete from hrd.brfnmrkt where kodeid='04'";
	$result_dls = mysqli_query($cnit, $query_dls);
	
	$query_s = " select SUM(jumlah) as jumlah,ccyid, left(tgltrans,7) as bulan from $tmp01 br0  
				 where (br0.kode='700-01-08' or br0.kode='700-02-05')  and ((left(tgltrans,4)='$tahun' and tglunrtr='0000-00-00') 
				 or LEFT(tglunrtr,4)='$tahun') and retur <> 'Y' and batal <> 'Y' and left(tgltrans,4)='$tahun' 
				 group by left(tgltrans,7),ccyid
				 order by tgltrans"; //echo"$query_po";
	$result_s = mysqli_query($cnit, $query_s);
	$records_s = mysqli_num_rows($result_s);
	$row_s = mysqli_fetch_array($result_s);
	$i = 1;
	while ($i <= $records_s) {
		$ccyid_s = $row_s['ccyid'];
                if (empty($ccyid_s)) $ccyid_s="IDR";
		$jumlah_s = $row_s['jumlah'];  
		$bulan_s = $row_s['bulan'];
		$kodeid_s = "04"; //echo"$kodeid_po";
		$query = "insert into hrd.brfnmrkt (kodeid,bulan,jumlah,ccyid) 
				  values ('$kodeid_s','$bulan_s','$jumlah_s','$ccyid_s')"; //echo"$query";
		$result = mysqli_query($cnit, $query);
		$row_s = mysqli_fetch_array($result_s);
		$i++;
	}	
	
	//EXHIBITION
	$query_dlm = "delete from hrd.brfnmrkt where kodeid='05'";
	$result_dlm = mysqli_query($cnit, $query_dlm);
	
	$query_m = " select SUM(jumlah) as jumlah,ccyid, left(tgltrans,7) as bulan from $tmp01 br0 
				 where (br0.kode='700-01-05' or br0.kode='700-04-05')  and ((left(tgltrans,4)='$tahun' and tglunrtr='0000-00-00') 
				 or LEFT(tglunrtr,4)='$tahun') and retur <> 'Y' and batal<> 'Y' and left(tgltrans,4)='$tahun' 
				 group by left(tgltrans,7),ccyid
				 order by tgltrans"; //echo"$query_po";
	$result_m = mysqli_query($cnit, $query_m);
	$records_m = mysqli_num_rows($result_m);
	$row_m = mysqli_fetch_array($result_m);
	$i = 1;
	while ($i <= $records_m) {
		$ccyid_m = $row_m['ccyid'];
                if (empty($ccyid_m)) $ccyid_m="IDR";
		$jumlah_m = $row_m['jumlah'];  
		$bulan_m = $row_m['bulan'];
		$kodeid_m = "05"; //echo"$kodeid_po";
		
		
		$query = "insert into hrd.brfnmrkt (kodeid,bulan,jumlah,ccyid) 
				  values ('$kodeid_m','$bulan_m','$jumlah_m','$ccyid_m')"; //echo"$query";
		$result = mysqli_query($cnit, $query);
		$row_m = mysqli_fetch_array($result_m);
		$i++;
	}
	
	//otc - br0
	$query_dlsm = "delete from hrd.brfnmrkt where kodeid='06'";
	$result_dlsm = mysqli_query($cnit, $query_dlsm);
	
	$query_sm = "select SUM(jumlah) as jumlah,ccyid, left(tgltrans,7) as bulan from $tmp01 br0 
				where (br0.kode='700-03-01' or br0.kode='700-03-05' or br0.kode='700-03-06')  and ((left(tgltrans,4)='$tahun' and tglunrtr='0000-00-00') 
				or LEFT(tglunrtr,4)='$tahun') and retur <> 'Y'  and batal <>'Y' and left(tgltrans,4)='$tahun' 
				group by left(tgltrans,7),ccyid
				order by tgltrans"; //echo"$query_po";
	$result_sm = mysqli_query($cnit, $query_sm);
	$records_sm = mysqli_num_rows($result_sm);
	$row_sm = mysqli_fetch_array($result_sm);
	$i = 1;
	while ($i <= $records_sm) {
		$ccyid_sm = $row_sm['ccyid'];
                if (empty($ccyid_sm)) $ccyid_sm="IDR";
		$jumlah_sm = $row_sm['jumlah'];  
		$bulan_sm = $row_sm['bulan'];
		$kodeid_sm = "06"; //echo"$kodeid_sm";
		
	
		$query = "insert into hrd.brfnmrkt (kodeid,bulan,jumlah,ccyid) 
				  values ('$kodeid_sm','$bulan_sm','$jumlah_sm','$ccyid_sm')"; //echo"$query";
		$result = mysqli_query($cnit, $query);
		$row_sm = mysqli_fetch_array($result_sm);
		$i++;
	}
	
	//otc - otc
	$query_dlsm1 = "delete from hrd.brfnmrkt where kodeid='061'";
	$result_dlsm1 = mysqli_query($cnit, $query_dlsm1);
	
	$query_sm1 = "select sum(jumlah) as jumlah,ccyid, left(tgltrans,7) as bulan
				  from hrd.br_otc 
				  where left(tgltrans,4)='$tahun' and kodeid in (select kodeid from hrd.brkd_otc where fnmrkt='06') 
				  group BY left(tgltrans,7),ccyid order by tgltrans"; //echo"$query_sm1";
	$result_sm1 = mysqli_query($cnit, $query_sm1);
	$records_sm1 = mysqli_num_rows($result_sm1);
	$row_sm1 = mysqli_fetch_array($result_sm1);
	$i = 1;
	while ($i <= $records_sm1) {
		$ccyid_sm1 = $row_sm1['ccyid'];
                $ccyid_sm1="IDR";
		$jumlah_sm1 = $row_sm1['jumlah'];  
		$bulan_sm1 = $row_sm1['bulan'];
		$kodeid_sm1 = "061"; //echo"$kodeid_sm";
		
	
		$query = "insert into hrd.brfnmrkt (kodeid,bulan,jumlah,ccyid) 
				  values ('$kodeid_sm1','$bulan_sm1','$jumlah_sm1','$ccyid_sm1')";//echo"$query";
		$result = mysqli_query($cnit, $query);
		$row_sm1 = mysqli_fetch_array($result_sm1);
		$i++;
	}
	
	$query_sm2 = "select SUM(jumlah) as jumlah,bulan,ccyid,kodeid from hrd.brfnmrkt where kodeid='06' or kodeid='061'
				  group by bulan order by bulan"; //echo"$query_sm2";
	$result_sm2 = mysqli_query($cnit, $query_sm2);
	$records_sm2 = mysqli_num_rows($result_sm2);
	$row_sm2 = mysqli_fetch_array($result_sm2);
	$i = 1;
	$query_dlsm2 = "delete from hrd.brfnmrkt where kodeid='061' or kodeid='06'";
	$result_dlsm2 = mysqli_query($cnit, $query_dlsm2);
	while ($i <= $records_sm2) {
		$ccyid_sm2 = $row_sm2['ccyid'];
                if (empty($ccyid_sm2)) $ccyid_sm2="IDR";
		$jumlah_sm2 = $row_sm2['jumlah'];  
		$bulan_sm2 = $row_sm2['bulan'];
		$kodeid_sm2 = "06"; //echo"$kodeid_sm";
		
		$query = "insert into hrd.brfnmrkt (kodeid,bulan,jumlah,ccyid) 
				  values ('$kodeid_sm2','$bulan_sm2','$jumlah_sm2','$ccyid_sm2')";//echo"$query";
		$result = mysqli_query($cnit, $query);
		$row_sm2 = mysqli_fetch_array($result_sm2);
		$i++;
	}
	

	//IKLAN ETHICAL
	$query_dlie = "delete from hrd.brfnmrkt where kodeid='07'";
	$result_dlie = mysqli_query($cnit, $query_dlie);
	
	$query_ie = "select SUM(jumlah) as jumlah,ccyid, left(tgltrans,7) as bulan from $tmp01 br0 
				 where (br0.kode='700-01-06')  and ((left(tgltrans,4)='$tahun' and tglunrtr='0000-00-00') 
				 or LEFT(tglunrtr,4)='$tahun') and retur <> 'Y' and batal <>'Y' and left(tgltrans,4)='$tahun' 
				 group by left(tgltrans,7),ccyid
				 order by tgltrans"; //echo"$query_po";
	$result_ie = mysqli_query($cnit, $query_ie);
	$records_ie = mysqli_num_rows($result_ie);
	$row_ie = mysqli_fetch_array($result_ie);
	$i = 1;
	while ($i <= $records_ie) {
		$ccyid_ie = $row_ie['ccyid'];
                if (empty($ccyid_ie)) $ccyid_ie="IDR";
		$jumlah_ie = $row_ie['jumlah'];  
		$bulan_ie = $row_ie['bulan'];
		$kodeid_ie = "07"; //echo"$kodeid_po";
		$query = "insert into hrd.brfnmrkt (kodeid,bulan,jumlah,ccyid) 
				  values ('$kodeid_ie','$bulan_ie','$jumlah_ie','$ccyid_ie')"; //echo"$query";
		$result = mysqli_query($cnit, $query);
		$row_ie = mysqli_fetch_array($result_ie);
		$i++;
	}
	
	//claim disc
	$query_dlcd = "delete from hrd.brfnmrkt where kodeid='08'";
	$result_dlcd = mysqli_query($cnit, $query_dlcd);
	
	$query_cd = "select sum(jumlah) as jumlah, LEFT(tgltrans,7) as bulan
				 from hrd.klaim where left(tgltrans,4)='$tahun'
				 group by left(tgltrans,7)"; //echo"$query_po";
	$result_cd = mysqli_query($cnit, $query_cd);
	$records_cd = mysqli_num_rows($result_cd);
	$row_cd = mysqli_fetch_array($result_cd);
	$i = 1;
	while ($i <= $records_cd) {
		$jumlah_cd = $row_cd['jumlah'];  
		$bulan_cd = $row_cd['bulan'];
		$kodeid_cd = "08"; //echo"$kodeid_po";
		$query = "insert into hrd.brfnmrkt (kodeid,bulan,jumlah,ccyid) 
				  values ('$kodeid_cd','$bulan_cd','$jumlah_cd','IDR')"; //echo"$query";
		$result = mysqli_query($cnit, $query);
		$row_cd = mysqli_fetch_array($result_cd);
		$i++;
	}
	
	//HO
	$query_dlho = "delete from hrd.brfnmrkt where kodeid='09'";
	$result_dlho = mysqli_query($cnit, $query_dlho);
	
	$query_ho = "select SUM(jumlah) as jumlah,ccyid, left(tgltrans,7) as bulan from $tmp01 br0  
				 where (br0.kode='700-01-099' or br0.kode='700-02-099' or br0.kode='700-04-099')  and ((left(tgltrans,4)='$tahun' and tglunrtr='0000-00-00') 
				 or LEFT(tglunrtr,4)='$tahun') and retur <> 'Y' and batal <>'Y' and left(tgltrans,4)='$tahun'
				 group by left(tgltrans,7),ccyid
				 order by tgltrans"; //echo"$query_ho";
	$result_ho = mysqli_query($cnit, $query_ho);
	$records_ho = mysqli_num_rows($result_ho);
	$row_ho = mysqli_fetch_array($result_ho);
	$i = 1;
	while ($i <= $records_ho) {
		$ccyid_ho = $row_ho['ccyid'];
		$jumlah_ho = $row_ho['jumlah'];  
		$bulan_ho = $row_ho['bulan'];
		$kodeid_ho = "09"; //echo"$kodeid_po";
		$query = "insert into hrd.brfnmrkt (kodeid,bulan,jumlah,ccyid) 
				  values ('$kodeid_ho','$bulan_ho','$jumlah_ho','$ccyid_ho')"; //echo"$query";
		$result = mysqli_query($cnit, $query);
		$row_ho = mysqli_fetch_array($result_ho);
		$i++;
	}
	
	//INCTV
	$query_dlin = "delete from hrd.brfnmrkt where kodeid='10'";
	$result_dlin = mysqli_query($cnit, $query_dlin);
	
	$query_in = "select sum(jumlah) as jumlah, left(periode,7) as bulan
				 from hrd.inctv where LEFT(periode,4)='$tahun'
				 group by left(periode,7) "; //echo"$query_in";
	$result_in = mysqli_query($cnit, $query_in);
	$records_in = mysqli_num_rows($result_in);
	$row_in = mysqli_fetch_array($result_in);
	$i = 1;
	while ($i <= $records_in) {
		$jumlah_in = $row_in['jumlah'];  
		$bulan_in = $row_in['bulan'];
		$kodeid_in = "10"; //echo"$kodeid_po";
		$query = "insert into hrd.brfnmrkt (kodeid,bulan,jumlah,ccyid) 
				  values ('$kodeid_in','$bulan_in','$jumlah_in','IDR')"; //echo"$query";
		$result = mysqli_query($cnit, $query);
		$row_in = mysqli_fetch_array($result_in);
		$i++;
	}
	
	//RUTIN
	$query_dlrt = "delete from hrd.brfnmrkt where kodeid='11'";
	$result_dlrt = mysqli_query($cnit, $query_dlrt);
	
	$query_rt = "select sum(jmltr) as jumlah, left(periode,7) as bulan
				 from hrd.brutin0 where left(periode,4)='$tahun'
				 group by left(periode,7)"; //echo"$query_po";
	$result_rt = mysqli_query($cnit, $query_rt);
	$records_rt = mysqli_num_rows($result_rt);
	$row_rt = mysqli_fetch_array($result_rt);
	$i = 1;
	while ($i <= $records_rt) {
		$jumlah_rt = $row_rt['jumlah'];  
		$bulan_rt = $row_rt['bulan'];
		$kodeid_rt = "11"; //echo"$kodeid_po";
		$query = "insert into hrd.brfnmrkt (kodeid,bulan,jumlah,ccyid) 
				  values ('$kodeid_rt','$bulan_rt','$jumlah_rt','IDR')"; //echo"$query";
		$result = mysqli_query($cnit, $query);
		$row_rt = mysqli_fetch_array($result_rt);
		$i++;
	}
	
	//blk
	$query_dllk = "delete from hrd.brfnmrkt where kodeid='12'";
	$result_dllk = mysqli_query($cnit, $query_dllk);
	
	$query_lk = "select sum(qty*nilai) as jumlah,left(blkota0.tgl1,7) as bulan 
				 from hrd.blkota0 blkota0 
			     join hrd.blkota1 blkota1 on blkota0.noBlkota = blkota1.noblkota
			     where (left(blkota0.tgl1,4) = '$tahun') and blkotaid<14 
			     group by left(blkota0.tgl1,7) order by left(blkota0.tgl1,7)"; //echo"$query_lk";
	$result_lk = mysqli_query($cnit, $query_lk);
	$records_lk = mysqli_num_rows($result_lk);
	$row_lk = mysqli_fetch_array($result_lk);
	$i = 1;
	while ($i <= $records_lk) {
		$jumlah_lk = $row_lk['jumlah'];  
		$bulan_lk = $row_lk['bulan'];
		$kodeid_lk = "12"; //echo"$kodeid_po";
		$query = "insert into hrd.brfnmrkt (kodeid,bulan,jumlah,ccyid) 
				  values ('$kodeid_lk','$bulan_lk','$jumlah_lk','IDR')"; //echo"$query";
		$result = mysqli_query($cnit, $query);
		$row_lk = mysqli_fetch_array($result_lk);
		$i++;
	}
	
	//KAS KECIL
	$query_dlkk = "delete from hrd.brfnmrkt where kodeid='13'";
	$result_dlkk = mysqli_query($cnit, $query_dlkk);
	
	$query_kk = "select sum(jumlah) as jumlah, left(periode1,7) as bulan 
				 from hrd.kas 
				 where left(periode1,4)='$tahun'
				 group by left(periode1,7)"; //echo"$query_kk";
	$result_kk = mysqli_query($cnit, $query_kk);
	$records_kk = mysqli_num_rows($result_kk);
	$row_kk = mysqli_fetch_array($result_kk);
	$i = 1;
	while ($i <= $records_kk) {
		$jumlah_kk = $row_kk['jumlah'];  
		$bulan_kk = $row_kk['bulan'];
		$kodeid_kk = "13"; //echo"$kodeid_po";
		$query = "insert into hrd.brfnmrkt (kodeid,bulan,jumlah,ccyid) 
				  values ('$kodeid_kk','$bulan_kk','$jumlah_kk','IDR')"; //echo"$query";
		$result = mysqli_query($cnit, $query);
		$row_kk = mysqli_fetch_array($result_kk);
		$i++;
	}
	
	//corporate
	$query_dlcr = "delete from hrd.brfnmrkt where kodeid='14'";
	$result_dlcr = mysqli_query($cnit, $query_dlcr);
	
	$query_cr = "select SUM(jumlah) as jumlah,ccyid, left(tgltrans,7) as bulan from $tmp01 br0 
				 where (br0.kode='700-03-099')  and ((left(tgltrans,4)='$tahun' and tglunrtr='0000-00-00') 
				 or LEFT(tglunrtr,4)='$tahun') and retur <> 'Y' and batal <>'Y' and left(tgltrans,4)='$tahun'
				 group by left(tgltrans,7),ccyid
				 order by tgltrans"; //echo"$query_ho";
	$result_cr = mysqli_query($cnit, $query_cr);
	$records_cr = mysqli_num_rows($result_cr);
	$row_cr = mysqli_fetch_array($result_cr);
	$i = 1;
	while ($i <= $records_cr) {
		$ccyid_cr = $row_cr['ccyid'];
		$jumlah_cr = $row_cr['jumlah'];  
		$bulan_cr = $row_cr['bulan'];
		$kodeid_cr = "14"; //echo"$kodeid_po";
		$query = "insert into hrd.brfnmrkt (kodeid,bulan,jumlah,ccyid) 
				  values ('$kodeid_cr','$bulan_cr','$jumlah_cr','$ccyid_cr')"; //echo"$query";
		$result = mysqli_query($cnit, $query);
		$row_cr = mysqli_fetch_array($result_cr);
		$i++;
	}
	
	$query_i = "select * from hrd.brfnmrkt where ccyid='IDR'";
	$result_i = mysqli_query($cnit, $query_i);
	$num_results_i = mysqli_num_rows($result_i); 
	if ($num_results_i) {
		$query = "select brfnmrkt.*, brfn_kd.nama as nmkode
				  from hrd.brfnmrkt brfnmrkt 
				  join hrd.brfn_kd brfn_kd on brfnmrkt.kodeid=brfn_kd.kodeid
				  where ccyid='IDR'
				  order by brfn_kd.kodeid,bulan,ccyid";//echo"$query";

		$result = mysqli_query($cnit, $query);
		$num_results = mysqli_num_rows($result); 
		for ($i=0; $i < $num_results; $i++) {
			$row = mysqli_fetch_array($result);
			if ($i==0) {
			   $bulan_ = $row['bulan']; //echo"bulan=$bulan_";
			   $bln_ = substr($bulan_,-2,2); //echo"bln=$bln_";
			}	
		} 
		
		mysqli_data_seek($result,0);
		//ambil shorts (bulan)
		echo '<table border="1" cellspacing="0" cellpadding="1">';
		echo"<b><center>LAPORAN KEUANGAN MARKETING $tahun</b></center><br>";
		echo '<tr>';
		$header_ = add_space('Rincian Alokasi',30);
		echo "<b>** Dalam IDR</b>";
		echo "<th rowspan=3 align='center'><small>$header_</small></th>"; 
		echo "<th colspan=12 align=center><small><b>Bulan</b></small></th>";
		echo '<td rowspan=3 align="center"><small><b>Total IDR</b></small></td>';
		$query2 = "select number from hrd.bulan order by number";
		$result2 = mysqli_query($cnit, $query2);
		$num_results2 = mysqli_num_rows($result2);
		$j = 0;
		echo "<tr>";
		unset($Bln_);
		for ($i=0; $i < $num_results2; $i++) {
			$row2 = mysqli_fetch_array($result2);
			$j++ ;
			$Bln_[$j] = $row2['number']; 
			$nm_bln= nama_bulan($Bln_[$j]); 
			echo "<th><small>".$nm_bln."</small></th>"; 
		} 
			
		echo '</tr>';
		//array for total qty 
		$dummy_ = $num_results2+1; // echo"$dummy_";
		for ($k=1; $k < $dummy_ ; $k++) {
			$produkQty_[$k]="";   // qty
			$produkTQty_[$k]="";   // total qty
		} 
		echo "<tr>";
		$tot_sales = 0;
		$row = mysqli_fetch_array($result);
		$i = 1;
		while ($i <= $num_results) { 	
			$nmkode_ = $row['nmkode']; 
			$kodeid_ = $row['kodeid']; 
			echo "<tr>";
			echo '<td align="left"><small>'.$row['nmkode']."</small></td>";
			$tot_prod = 0;
			while ($nmkode_ == $row['nmkode'] && $kodeid_ == $row['kodeid'] ) {
				$bulan_ = $row['bulan'];// echo"$bulan_";
				$bln = substr($bulan_,-2,2); //echo"<b>$bln</b>";
				$jumlah = 0;
				$ccyid = $row['ccyid']; //echo"$ccyid";
				$jumlah = $row['jumlah'];// echo"$jumlah";
				
				//echo"$kodeid_/$bln/$jumlah/$jmlh<br>";
				$row = mysqli_fetch_array($result);
				$i++;
				 
			   $index_ = array_search($bln,$Bln_);
			   $produkQty_[$index_] = $jumlah;  
			   $produkTQty_[$index_] = (double)$produkTQty_[$index_] + (double)$jumlah;

				//jumlah qty dan sales
				$index_ = ($kodeid_);
				$prodTQty_[$index_] = $prodTQty_[$index_] + $jumlah; 

			}  // break per outlet
			//cetak
			$total = $prodTQty_[$index_]; 
			$t_total = $t_total + $total;

			for ($k=1; $k < $dummy_; $k++) {
				if ($produkQty_[$k]=="") {
				    echo '<td align="right"><small>&nbsp;</small></td>';
				} else {
					echo '<td align="right"><small>'.number_format($produkQty_[$k],0)."</small></td>";
				} 
				$produkQty_[$k]="";   
			} 
			echo '<td align="right"><small><b>'.number_format($total,0)."</b></small></td>";
			echo "</tr>";
		} // end while

		//cetak total
		echo "<tr>";
		echo '<td><small><b>Total IDR:</b></small></td>';
		for ($k=1; $k < $dummy_; $k++) {
			if ($produkTQty_[$k]==0) {
				echo '<td align="right"><small>&nbsp;</small></td>';
			} else {
				echo '<td align="right"><small><b>'.number_format($produkTQty_[$k],0)."</b></small></td>";
			} 
		} 
		echo '<td align="right"><small><b>'.number_format($t_total,0)."</b></small></td>";
		echo "</table>\n";
	} else {
	}
	
	$query_u = "select * from hrd.brfnmrkt where ccyid='USD'";
	$result_u = mysqli_query($cnit, $query_u);
	$num_results_u = mysqli_num_rows($result_u); 
	if ($num_results_u) {
		$query = "select brfnmrkt.*, brfn_kd.nama as nmkode
				  from hrd.brfnmrkt brfnmrkt 
				  join hrd.brfn_kd brfn_kd on brfnmrkt.kodeid=brfn_kd.kodeid
				  where ccyid='USD'
				  order by brfn_kd.kodeid,bulan,ccyid";

		$result = mysqli_query($cnit, $query);
		$num_results = mysqli_num_rows($result); 
		for ($i=0; $i < $num_results; $i++) {
			$row = mysqli_fetch_array($result);
			if ($i==0) {
			   $bulan_ = $row['bulan']; 
			   $bln_ = substr($bulan_,-2,2); 
			}	
		} 
		
		mysqli_data_seek($result,0);
		echo '<br><br><table border="1" cellspacing="0" cellpadding="1">';
		echo '<tr>';
		$header_ = add_space('Rincian Alokasi',30);
		echo "<b>** Dalam USD</b>";
		echo "<th rowspan=3 align='center'><small>$header_</small></th>"; 
		echo "<th colspan=12 align=center><small><b>Bulan</b></small></th>";
		echo '<td rowspan=3 align="center"><small><b>Total USD</b></small></td>';
		$query2 = "select number from hrd.bulan order by number";
		$result2 = mysqli_query($cnit, $query2);
		$num_results2 = mysqli_num_rows($result2);
		$j = 0;
		echo "<tr>";
		unset($Bln_);
		for ($i=0; $i < $num_results2; $i++) {
			$row2 = mysqli_fetch_array($result2);
			$j++ ;
			$Bln_[$j] = $row2['number']; 
			$nm_bln= nama_bulan($Bln_[$j]); 
			echo "<th><small>".$nm_bln."</small></th>"; 
		} 
			
		echo '</tr>';
		$dummy_ = $num_results2+1;
		for ($k=1; $k < $dummy_ ; $k++) {
			$produkQty1_[$k]="";   
			$produkTQty1_[$k]=""; 
		} 
		echo "<tr>";
		$tot_sales = 0;
		$row = mysqli_fetch_array($result);
		$i = 1;
		while ($i <= $num_results) { 	
			$nmkode_ = $row['nmkode']; 
			$kodeid_ = $row['kodeid']; 
			echo "<tr>";
			echo '<td align="left"><small>'.$row['nmkode']."</small></td>";
			$tot_prod = 0;
			while ($nmkode_ == $row['nmkode'] && $kodeid_ == $row['kodeid'] ) {
				$bulan_ = $row['bulan'];
				$bln = substr($bulan_,-2,2); 
				$jumlah = 0;
				$ccyid = $row['ccyid']; 
				$jumlah1 = $row['jumlah'];
				
				$row = mysqli_fetch_array($result);
				$i++;
		
			   $index_ = array_search($bln,$Bln_);
			   $produkQty1_[$index_] = $jumlah1;  
			   $produkTQty1_[$index_] = $produkTQty1_[$index_] + $jumlah1; 

				$index_ = ($kodeid_);
				$prodTQty1_[$index_] = $prodTQty1_[$index_] + $jumlah1; 

			}  // break per outlet
			$total1 = $prodTQty1_[$index_]; 
			$t_total1 = $t_total1 + $total1;

			for ($k=1; $k < $dummy_; $k++) {
				if ($produkQty1_[$k]=="") {
				    echo '<td align="right"><small>&nbsp;</small></td>';
				} else {
					echo '<td align="right"><small>'.number_format($produkQty1_[$k],0)."</small></td>";
				} 
				$produkQty1_[$k]="";   
			} 
			echo '<td align="right"><small><b>'.number_format($total1,0)."</b></small></td>";
			echo "</tr>";
		} // end while

		//cetak total
		echo "<tr>";
		echo '<td><small><b>Total USD:</b></small></td>';
		for ($k=1; $k < $dummy_; $k++) {
			if ($produkTQty1_[$k]==0) {
				echo '<td align="right"><small>&nbsp;</small></td>';
			} else {
				echo '<td align="right"><small><b>'.number_format($produkTQty1_[$k],0)."</b></small></td>";
			} 
		} 
		echo '<td align="right"><small><b>'.number_format($t_total1,0)."</b></small></td>";
		echo "</table>\n";
	} else {
	}
	
	$query_s = "select * from hrd.brfnmrkt where ccyid='SGD'";
	$result_s = mysqli_query($cnit, $query_s);
	$num_results_s = mysqli_num_rows($result_s); 
	if ($num_results_s) {
		$query = "select brfnmrkt.*, brfn_kd.nama as nmkode
			  from hrd.brfnmrkt brfnmrkt 
			  join hrd.brfn_kd brfn_kd on brfnmrkt.kodeid=brfn_kd.kodeid
			  where ccyid='SGD'
			  order by brfn_kd.kodeid,bulan,ccyid";

		$result = mysqli_query($cnit, $query);
		$num_results = mysqli_num_rows($result); 
		for ($i=0; $i < $num_results; $i++) {
			$row = mysqli_fetch_array($result);
			if ($i==0) {
			   $bulan_ = $row['bulan']; 
			   $bln_ = substr($bulan_,-2,2); 
			}	
		} 
		
		mysqli_data_seek($result,0);
		echo '<br><br><table border="1" cellspacing="0" cellpadding="1">';
		echo '<tr>';
		$header_ = add_space('Rincian Alokasi',30);
		echo "<b>** Dalam SGD</b>";
		echo "<th rowspan=3 align='center'><small>$header_</small></th>"; 
		echo "<th colspan=12 align=center><small><b>Bulan</b></small></th>";
		echo '<td rowspan=3 align="center"><small><b>Total SGD</b></small></td>';
		$query2 = "select number from hrd.bulan order by number";
		$result2 = mysqli_query($cnit, $query2);
		$num_results2 = mysqli_num_rows($result2);
		$j = 0;
		echo "<tr>";
		unset($Bln_);
		for ($i=0; $i < $num_results2; $i++) {
			$row2 = mysqli_fetch_array($result2);
			$j++ ;
			$Bln_[$j] = $row2['number']; 
			$nm_bln= nama_bulan($Bln_[$j]); 
			echo "<th><small>".$nm_bln."</small></th>"; 
		} 
			
		echo '</tr>';
		$dummy_ = $num_results2+1;
		for ($k=1; $k < $dummy_ ; $k++) {
			$produkQty2_[$k]="";   
			$produkTQty2_[$k]=""; 
		} 
		echo "<tr>";
		$tot_sales = 0;
		$row = mysqli_fetch_array($result);
		$i = 1;
		while ($i <= $num_results) { 	
			$nmkode_ = $row['nmkode']; 
			$kodeid_ = $row['kodeid']; 
			echo "<tr>";
			echo '<td align="left"><small>'.$row['nmkode']."</small></td>";
			$tot_prod = 0;
			while ($nmkode_ == $row['nmkode'] && $kodeid_ == $row['kodeid'] ) {
				$bulan_ = $row['bulan'];
				$bln = substr($bulan_,-2,2); 
				$jumlah = 0;
				$ccyid = $row['ccyid']; 
				$jumlah2 = $row['jumlah'];
				
				$row = mysqli_fetch_array($result);
				$i++;
		
			   $index_ = array_search($bln,$Bln_);
			   $produkQty2_[$index_] = $jumlah2;  
			   $produkTQty2_[$index_] = $produkTQty2_[$index_] + $jumlah2; 

				$index_ = ($kodeid_);
				$prodTQty2_[$index_] = $prodTQty2_[$index_] + $jumlah2; 

			}  // break per outlet
			$total2 = $prodTQty2_[$index_]; 
			$t_total2 = $t_total2 + $total2;

			for ($k=1; $k < $dummy_; $k++) {
				if ($produkQty2_[$k]=="") {
				    echo '<td align="right"><small>&nbsp;</small></td>';
				} else {
					echo '<td align="right"><small>'.number_format($produkQty2_[$k],0)."</small></td>";
				} 
				$produkQty2_[$k]="";   
			} 
			echo '<td align="right"><small><b>'.number_format($total2,0)."</b></small></td>";
			echo "</tr>";
		} // end while

		//cetak total
		echo "<tr>";
		echo '<td><small><b>Total SGD:</b></small></td>';
		for ($k=1; $k < $dummy_; $k++) {
			if ($produkTQty2_[$k]==0) {
				echo '<td align="right"><small>&nbsp;</small></td>';
			} else {
				echo '<td align="right"><small><b>'.number_format($produkTQty2_[$k],0)."</b></small></td>";
			} 
		} 
		echo '<td align="right"><small><b>'.number_format($t_total2,0)."</b></small></td>";
		echo "</table>\n";
	} else {
	}
	
    hapusdata:
        mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp01");
?>
</body>
</form>
</html>
