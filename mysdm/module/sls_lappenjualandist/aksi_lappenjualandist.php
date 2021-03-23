<?php
    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","512M");
    ini_set('max_execution_time', 0);
    
session_start();
$puser=$_SESSION['USERID'];

if (empty($puser)) {
    echo "ANDA HARUS LOGIN ULANG....!!!";
    exit;
}

$userid=$_SESSION['USERID'];

$ppilihrpt=$_GET['ket'];
if ($ppilihrpt=="excel") {
    // Fungsi header dengan mengirimkan raw data excel
    header("Content-type: application/vnd-ms-excel");
    // Mendefinisikan nama file ekspor "hasil-export.xls"
    header("Content-Disposition: attachment; filename=Laporan Penjualan Distributor Ethical CHC.xls");
}
    

$module=$_GET['module'];
$act=$_GET['act'];
$idmenu=$_GET['idmenu'];

$tgl01=$_POST['e_periode01'];
$tanggal = '01';
$bulan = date("m", strtotime($tgl01));
$tahun = date("Y", strtotime($tgl01));
$periode=date("Y-m-01", strtotime($tgl01));
$periode2=date("Y-m-t", strtotime($tgl01));

$bln_nm=date("F", strtotime($tgl01));
         
$distid = $_POST['distid']; 
$ecabangid = $_POST['ecabangid'];
$divprodid = $_POST['divprodid'];

$pilihan="";
$chkBlank="";
$chkZero="";
$chkBonus="";
$chkDataBonus="";
$chkAll="";

if (isset($_POST['pilihan'])) $pilihan = $_POST['pilihan']; 
if (isset($_POST['chkBlank'])) $chkBlank = $_POST['chkBlank'];
if (isset($_POST['chkZero'])) $chkZero = $_POST['chkZero']; 
if (isset($_POST['chkBonus'])) $chkBonus = $_POST['chkBonus'];
if (isset($_POST['chkDataBonus'])) $chkDataBonus = $_POST['chkDataBonus'];
if (isset($_POST['chkAll'])) $chkAll = $_POST['chkAll'];
                
$nama_div="";
if ($divprodid == 'A') {
    $nama_div = 'ETHICAL & OTC';
} else {
    if ($divprodid == 'E') {
        $nama_div = 'ETHICAL';
    } else {
        if ($divprodid == 'N') {
            $nama_div = 'N/A';
        } elseif ($divprodid == 'O') {
            $nama_div = 'OTC';
        // tambahan 20170124
        }else{
            $nama_div = 'PEACOCK';
        }
    }
}

$nama_plh="";
if ($pilihan == 'A') {
    $nama_plh = 'REGULAR, DISPENSING & RETUR';
} else {
    if ($pilihan == 'D') {
        $nama_plh = 'DISPENSING';
    } else {
        if ($pilihan == 'RE') {
            $nama_plh = 'REGULAR';
        } else {
            $nama_plh = 'RETUR';
        }
    }
}
                


$pviewdate=date("d/m/Y H:i:s");

$milliseconds = round(microtime(true) * 1000);
$now=date("mdYhis");
$tmp01 ="dbtemp.tempslsbucusteth01_".$puser."_$now$milliseconds";
$tmp02 ="dbtemp.tempslsbucusteth02_".$puser."_$now$milliseconds";
$tmp03 ="dbtemp.tempslsbucusteth03_".$puser."_$now$milliseconds";
    

include("config/koneksimysqli_ms.php");

$join="";
$where_="";

$query = "select sls_data, nama from MKT.distrib0 where distid='$distid'";
$result = mysqli_query($cnms, $query);
$row = mysqli_fetch_array($result);
$num_results = mysqli_num_rows($result);
$sls_data = $row['sls_data'];
$nama_dist = $row['nama'];
$periode_ = substr($periode,0,7);
$periode2_ = substr($periode2,0,7);
if($userid=='266'){
    $periode_ = "$periode_ s/d $periode2_";
}
$join .= "";
                
//echo "$periode_ & $periode2_";         
    if ($ecabangid == 'A') {
        //echo $chkZero.'~'.$chkBonus;
        if($chkZero!=''){
            $where_ .= " and qbeli <> 0 ";
        }
        if($chkBonus!=''){
            $where_ .= " and qbonus <> 0 ";
        }

    } else {
        if($chkZero!=''){
            $where_ .= " and qbeli <> 0 ";
        }

        if($chkBonus!=''){
            $where_ .= " and qbonus <> 0 ";
        }

        if ($ecabangid == 'B' || $ecabangid == 'T') {
            $join .= " LEFT JOIN MKT.ecust as ecust ON $sls_data.cabangid=ecust.cabangid AND $sls_data.custid=ecust.ecustid AND ecust.distid = '$distid' "
                    . " LEFT JOIN MKT.icabang as icabang ON ecust.icabangid=icabang.icabangid ";
            $where_ .= "AND (icabang.region='$ecabangid' || ecust.icabangid = '')";
        }else{
            $join .= "";
            $where_ .= "AND $sls_data.cabangid='$ecabangid'";
        }
    }

    
    
if ($pilihan == 'A') {//Jenis ALL, Dispensing, reguler, retur
    
    if ($divprodid == 'A' OR $divprodid == '') {//ALL
        $query = "select distinct $sls_data.*, eproduk.nama as nm_prod, iproduk.divprodid
            from MKT.eproduk as eproduk
            LEFT JOIN MKT.$sls_data as $sls_data on eproduk.eprodid=$sls_data.brgid
            ".$join."
            join MKT.iproduk as iproduk on eproduk.iprodid=iproduk.iprodid
            where ('".$periode."' <= tgljual and tgljual <= '".$periode2."') and eproduk.distid='$distid' 
            ".$where_." 
            
        ";
    }else{//divisi selain ALL
        
        if ($divprodid == 'E') {
            
            $query = "select $sls_data.*, eproduk.nama as nm_prod, iproduk.divprodid
                from MKT.eproduk as eproduk 
                LEFT JOIN MKT.$sls_data as $sls_data on eproduk.eprodid=$sls_data.brgid
                ".$join."
                join MKT.iproduk as iproduk on eproduk.iprodid=iproduk.iprodid
                where ('".$periode."' <= tgljual and tgljual <= '".$periode2."') and eproduk.distid='$distid' 
                ".$where_."  and (iproduk.divprodid='EAGLE' or iproduk.divprodid='PIGEO') 
                
            ";
        } else {
            if ($divprodid == 'O') {
                $query = "select $sls_data.*, eproduk.nama as nm_prod, iproduk.divprodid
                      from MKT.eproduk as eproduk 
                      LEFT JOIN MKT.$sls_data as $sls_data on eproduk.eprodid=$sls_data.brgid
                    ".$join."
                      join MKT.iproduk as iproduk on eproduk.iprodid=iproduk.iprodid
                      where ('".$periode."' <= tgljual and tgljual <= '".$periode2."') and eproduk.distid='$distid' 
                     ".$where_."  and (iproduk.divprodid='OTC') 
                     
                ";
            } elseif ($divprodid == 'NA') {
                $query = "select $sls_data.*, eproduk.nama as nm_prod, iproduk.divprodid
                      from MKT.eproduk as eproduk 
                      LEFT JOIN MKT.$sls_data as $sls_data on eproduk.eprodid=$sls_data.brgid
                    ".$join."
                      join MKT.iproduk as iproduk on eproduk.iprodid=iproduk.iprodid
                      where ('".$periode."' <= tgljual and tgljual <= '".$periode2."') and eproduk.distid='$distid' 
                      ".$where_."  and iproduk.divprodid='NA' 
                      
                ";

            // tambahan 20170124
            }else{
                $query = "select $sls_data.*, eproduk.nama as nm_prod, iproduk.divprodid
                      from MKT.eproduk as eproduk 
                      LEFT JOIN MKT.$sls_data as $sls_data on eproduk.eprodid=$sls_data.brgid
                    ".$join."
                      join MKT.iproduk as iproduk on eproduk.iprodid=iproduk.iprodid
                      where ('".$periode."' <= tgljual and tgljual <= '".$periode2."') and eproduk.distid='$distid' 
                      ".$where_."  and iproduk.divprodid='PEACO' 
                      
                ";
            }
        }
        
    }
    
}else{//Jenis selain ALL
    if ($pilihan == 'D') { // dispensing
        if ($divprodid == 'A') {
            $query = "SELECT $sls_data.*, eproduk.nama as nm_prod, iproduk.divprodid
              from MKT.eproduk as eproduk 
              LEFT JOIN MKT.$sls_data as $sls_data on eproduk.eprodid=$sls_data.brgid
                    ".$join."
              join MKT.iproduk as iproduk on eproduk.iprodid=iproduk.iprodid
              where ('".$periode."' <= tgljual and tgljual <= '".$periode2."') and eproduk.distid='$distid' 
              ".$where_." 
              and fakturid like '%P' 
              
                ";
        } else {
            if ($divprodid == 'E') {
                $query = "SELECT $sls_data.*, eproduk.nama as nm_prod, iproduk.divprodid
                      from MKT.eproduk as eproduk
                      LEFT JOIN MKT.$sls_data as $sls_data on eproduk.eprodid=$sls_data.brgid
                    ".$join."
                      join MKT.iproduk as iproduk on eproduk.iprodid=iproduk.iprodid
                      where ('".$periode."' <= tgljual and tgljual <= '".$periode2."') and eproduk.distid='$distid' 
                      ".$where_."  and (iproduk.divprodid='EAGLE' or iproduk.divprodid='PIGEO')
                      and fakturid like '%P' 
                      
                ";
            } else {
                if ($divprodid == 'O') {
                    $query = "SELECT $sls_data.*, eproduk.nama as nm_prod, iproduk.divprodid
                          from MKT.eproduk as eproduk
                          LEFT JOIN MKT.$sls_data as $sls_data on eproduk.eprodid=$sls_data.brgid
                        ".$join."
                          join MKT.iproduk as iproduk on eproduk.iprodid=iproduk.iprodid
                          where ('".$periode."' <= tgljual and tgljual <= '".$periode2."') and eproduk.distid='$distid' 
                          ".$where_."  and (iproduk.divprodid='OTC') 
                          and fakturid like '%P' 
                          
                    ";
                } elseif ($divprodid == 'NA') {
                    $query = "SELECT $sls_data.*, eproduk.nama as nm_prod, iproduk.divprodid
                          from MKT.eproduk as eproduk
                          LEFT JOIN MKT.$sls_data as $sls_data on eproduk.eprodid=$sls_data.brgid
                            ".$join."
                          join MKT.iproduk as iproduk on eproduk.iprodid=iproduk.iprodid
                          where ('".$periode."' <= tgljual and tgljual <= '".$periode2."') and eproduk.distid='$distid' 
                          ".$where_."  and iproduk.divprodid='NA'  
                          and fakturid like '%P' 
                          
                    ";
                // tambahan 20170124
                }else{
                    $query = "SELECT $sls_data.*, eproduk.nama as nm_prod, iproduk.divprodid
                      from MKT.eproduk as eproduk
                      LEFT JOIN MKT.$sls_data as $sls_data on eproduk.eprodid=$sls_data.brgid
                    ".$join."
                      join MKT.iproduk as iproduk on eproduk.iprodid=iproduk.iprodid
                      where ('".$periode."' <= tgljual and tgljual <= '".$periode2."') and eproduk.distid='$distid' 
                      ".$where_."  and iproduk.divprodid='PEACO'  
                      and fakturid like '%P' 
                      
                    ";
                }
            }
        }
    }else{//REGULER
        
        if ($pilihan == 'RE') { //regular
            if ($divprodid == 'A') {
                $query = "SELECT $sls_data.*, eproduk.nama as nm_prod, iproduk.divprodid
                      from MKT.eproduk as eproduk
                      LEFT JOIN MKT.$sls_data as $sls_data on eproduk.eprodid=$sls_data.brgid
                    ".$join."
                      join MKT.iproduk as iproduk on eproduk.iprodid=iproduk.iprodid
                      where ('".$periode."' <= tgljual and tgljual <= '".$periode2."') and eproduk.distid='$distid' 
                      ".$where_." 
                      and fakturid NOT like '%R' AND fakturid NOT like '%P' 
                      ";//echo"$query";
            } else {
                if ($divprodid == 'E') {
                    $query = "SELECT $sls_data.*, eproduk.nama as nm_prod, iproduk.divprodid
                              from MKT.eproduk as eproduk
                              LEFT JOIN MKT.$sls_data as $sls_data on eproduk.eprodid=$sls_data.brgid
                            ".$join."
                              join MKT.iproduk as iproduk on eproduk.iprodid=iproduk.iprodid
                              where ('".$periode."' <= tgljual and tgljual <= '".$periode2."') and eproduk.distid='$distid' 
                              ".$where_."  and (iproduk.divprodid='EAGLE' or iproduk.divprodid='PIGEO')
                              and fakturid NOT like '%R' AND fakturid NOT like '%P' ";//echo"$query";
                } else {
                    if ($divprodid == 'O') {
                        $query = "SELECT $sls_data.*, eproduk.nama as nm_prod, iproduk.divprodid
                            from MKT.eproduk as eproduk
                            LEFT JOIN MKT.$sls_data as $sls_data on eproduk.eprodid=$sls_data.brgid
                            ".$join."
                              join MKT.iproduk as iproduk on eproduk.iprodid=iproduk.iprodid
                              where ('".$periode."' <= tgljual and tgljual <= '".$periode2."') and eproduk.distid='$distid' 
                              ".$where_." and (iproduk.divprodid='OTC' ) 
                              and fakturid NOT like '%R' AND fakturid NOT like '%P' ";//echo"$query";
                    } elseif($divprodid == 'NA') {
                        $query = "SELECT $sls_data.*, eproduk.nama as nm_prod, iproduk.divprodid
                              from MKT.eproduk as eproduk
                              LEFT JOIN MKT.$sls_data as $sls_data on eproduk.eprodid=$sls_data.brgid
                                ".$join."
                              join MKT.iproduk as iproduk on eproduk.iprodid=iproduk.iprodid
                              where ('".$periode."' <= tgljual and tgljual <= '".$periode2."') and eproduk.distid='$distid' 
                              ".$where_." and iproduk.divprodid='NA'
                              and fakturid NOT like '%R' AND fakturid NOT like '%P' ";//echo"$query";
                    // tambahan 20170124
                    } else {
                        $query = "SELECT $sls_data.*, eproduk.nama as nm_prod, iproduk.divprodid
                              from MKT.eproduk as eproduk
                              LEFT JOIN MKT.$sls_data as $sls_data on eproduk.eprodid=$sls_data.brgid
                                ".$join."
                              join MKT.iproduk as iproduk on eproduk.iprodid=iproduk.iprodid
                              where ('".$periode."' <= tgljual and tgljual <= '".$periode2."') and eproduk.distid='$distid' 
                              ".$where_." and iproduk.divprodid='PEACO'
                              and fakturid NOT like '%R' AND fakturid NOT like '%P' ";//echo"$query";
                    }
                }
            }
        } else { // retur
            
            if ($divprodid == 'A') {
                $query = "SELECT $sls_data.*, eproduk.nama as nm_prod, iproduk.divprodid
                      from MKT.eproduk as eproduk
                      LEFT JOIN MKT.$sls_data as $sls_data on eproduk.eprodid=$sls_data.brgid
                    ".$join."
                      join MKT.iproduk as iproduk on eproduk.iprodid=iproduk.iprodid
                      where ('".$periode."' <= tgljual and tgljual <= '".$periode2."') and eproduk.distid='$distid' 
                     ".$where_." 
                      and fakturid like '%R' ";//echo"$query";
            } else {
                if ($divprodid == 'E') {
                    $query = "SELECT $sls_data.*, eproduk.nama as nm_prod, iproduk.divprodid
                          from MKT.eproduk as eproduk
                          LEFT JOIN MKT.$sls_data as $sls_data on eproduk.eprodid=$sls_data.brgid
                            ".$join."
                              join MKT.iproduk as iproduk on eproduk.iprodid=iproduk.iprodid
                              where ('".$periode."' <= tgljual and tgljual <= '".$periode2."') and eproduk.distid='$distid' 
                             ".$where_."  and (iproduk.divprodid='EAGLE' or iproduk.divprodid='PIGEO')
                              and fakturid like '%R' ";//echo"$query";
                } else {
                    if ($divprodid == 'O') {

                        $query = "SELECT $sls_data.*, eproduk.nama as nm_prod, iproduk.divprodid
                              from MKT.eproduk as eproduk
                              LEFT JOIN MKT.$sls_data as $sls_data on eproduk.eprodid=$sls_data.brgid
                            ".$join."
                              join MKT.iproduk as iproduk on eproduk.iprodid=iproduk.iprodid
                              where ('".$periode."' <= tgljual and tgljual <= '".$periode2."') and eproduk.distid='$distid' 
                              ".$where_."  and (iproduk.divprodid='OTC')
                              and fakturid like '%R' 
                            ";//echo"$query";

                    } elseif($divprodid == 'NA') {

                        $query = "SELECT $sls_data.*, eproduk.nama as nm_prod, iproduk.divprodid
                              from MKT.eproduk as eproduk
                              LEFT JOIN MKT.$sls_data as $sls_data on eproduk.eprodid=$sls_data.brgid
                            ".$join."
                              join MKT.iproduk as iproduk on eproduk.iprodid=iproduk.iprodid
                              where ('".$periode."' <= tgljual and tgljual <= '".$periode2."') and eproduk.distid='$distid' 
                              ".$where_." and iproduk.divprodid='NA'
                              and fakturid like '%R' 
                        ";//echo"$query";
// tambahan 20170124
                    } else {

                        $query = "SELECT $sls_data.*, eproduk.nama as nm_prod, iproduk.divprodid
                              from MKT.eproduk as eproduk
                              LEFT JOIN MKT.$sls_data as $sls_data on eproduk.eprodid=$sls_data.brgid
                            ".$join."
                              join MKT.iproduk as iproduk on eproduk.iprodid=iproduk.iprodid
                              where ('".$periode."' <= tgljual and tgljual <= '".$periode2."') and eproduk.distid='$distid' 
                              ".$where_." and iproduk.divprodid='PEACO'
                              and fakturid like '%R' 
                        ";//echo"$query";
                    }
                }
            }
            
            
        }
        
        
    }
                        
                        
                        
}

if (empty($query)) {
    goto hapusdata;
}



//$query .= " order by iproduk.divprodid,eproduk.nama,tgljual";
$query = "CREATE TEMPORARY TABLE $tmp01 ($query)";
mysqli_query($cnms, $query);
$erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }

//echo "Jenis : $pilihan, Divisi : $divprodid<br/>";
?>

<HTML>
<HEAD>
    <title>Laporan Penjualan Distributor Ethical & CHC</title>
    <?PHP if ($ppilihrpt!="excel") { ?>
        <meta http-equiv="Expires" content="Mon, 01 Mei 2050 1:00:00 GMT">
        <meta http-equiv="Pragma" content="no-cache">
        <link rel="shortcut icon" href="images/icon.ico" />
        <link href="css/laporanbaru.css" rel="stylesheet">
        <?php header("Cache-Control: no-cache, must-revalidate"); ?>
        
        <!-- Bootstrap -->
        <link href="vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

    
        <!-- Datatables -->
        <link href="vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css" rel="stylesheet">
    
        <!-- Datatables -->
        <link href="vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css" rel="stylesheet">

        <!-- Datatables -->
        <script src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.flash.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
        <!-- jQuery -->
        <script src="vendors/jquery/dist/jquery.min.js"></script>
        
        
    <?PHP } ?>
    
</HEAD>

<BODY>
    
<?PHP if ($ppilihrpt!="excel") { ?>
    <button onclick="topFunction()" id="myBtn" title="Go to top">Top</button>
<?PHP } ?>
    
    
<div id='n_content'>

    <div id="kotakjudul">
        <div id="isikiri">
            <table class='tjudul' width='100%'>
                <?PHP if ($ppilihrpt=="excel") {
                    echo "<tr><td colspan=5 width='150px'><b>Laporan Penjualan Distributor Ethical & CHC</b></td></tr>";
                    echo "<tr><td colspan=5 width='150px'>view date : $pviewdate</td></tr>";
                }else{
                    echo "<tr><td width='150px'><b><h3>Laporan Penjualan Distributor Ethical & CHC</h3></b></td></tr>";
                    echo "<tr><td width='150px'><i>view date : $pviewdate</i></td></tr>";
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
               $query = "select * from $tmp01 order by divprodid, nm_prod, tgljual";
               //echo $query;
		$result = mysqli_query($cnms, $query);
		$records = mysqli_num_rows($result);	
		//echo"record=$records";
		$row = mysqli_fetch_array($result);
		
		if ($records) {
			$i = 1;
			$g_qbonus = 0;
			$g_qtotal = 0;
			$g_qbeli = 0;
			$g_values = 0;

			// echo "chkZero = $chkZero <br> chkBonus = $chkBonus <br> chkDataBonus = $chkDataBonus <br> chkAll = $chkAll";

			// ------------------------------------- pilihan jika ingin lihat view bonus saja
			if($chkDataBonus==''){
				// echo 1;

				while ($i <= $records) {
					$brgid = $row['brgid'];
					$nm_prod = $row['nm_prod'];
					// echo"$nm_prod";

					// ------------------------------------- pilihan tampilan bukan lihat view all data
					if($chkAll==''){
						// echo 1;

						echo '
							<table id="datatable2" class="table table-striped table-bordered" width="100%" border="1px solid black"><br>
							<b>Nama Produk : '.$nm_prod.'</b>
							<tr>
								<th align="left"><small>No</small></th>
								<th align="center"><small>No Faktur</small></th>
								<th align="center"><small>Tgl. Jual</small></th>
								<th align="center"><small>Nama Outlet</small></th>
								<th align="center"><small>Harga</small></th>
								<th align="center"><small>Jumlah</small></th>
								<th align="center"><small>Bonus</small></th>
								<th align="center"><small>Total</small></th>
								<th align="center"><small>Value</small></th>
								<th align="center"><small>Divisi Produk</small></th>
							</tr>
						';

						$qtotal = 0;
						$t_qbeli = 0;
						$t_qbonus = 0;
						$t_qtotal = 0;
						$t_values = 0;
						$no = 0;
						while ( ($i<=$records) and ($brgid == $row['brgid']) ) {
							echo "<tr>";
							$no = $no + 1;			
							$custid = $row['custid'];
							$cabangid = $row['cabangId'];
							$tgljual = $row['tgljual'];
							$nama = $row['nm_prod'];	
							$qbeli = $row['qbeli']; 
							$qbonus = $row['qbonus'];
							$fakturid = $row['fakturId'];
							$hna = $row['harga'];
							$divprodid = $row['divprodid'];

							/*
							$qtotal = $qbeli + $qbonus;
							$t_qbeli = $t_qbeli + $qbeli;
							$t_qbonus = $t_qbonus + $qbonus;
							$t_qtotal = $t_qtotal + $qtotal;
							$g_qbeli = $g_qbeli + $qbeli;
							$g_qbonus = $g_qbonus + $qbonus;
							$g_qtotal = $g_qtotal + $qtotal;
							*/

							$nama_cst = '';
							$query_cst = "SELECT nama FROM MKT.ecust WHERE distid='$distid' AND cabangid='$cabangid' AND ecustid='$custid'";
							// echo"$query_cst<br>";

							$result_cst = mysqli_query($cnms, $query_cst);
							$num_results_cst = mysqli_num_rows($result_cst);
							if ($num_results_cst) {
								 $row_cst = mysqli_fetch_array($result_cst);
								 $nama_cst = $row_cst['nama'];
							}
							$ok2_ = 1;
							if ($chkBlank) {
								$ok2_ = 0;
								if ($nama_cst=="") {
									$ok2_ = 1;
								}
							}

							//if ($chkZero) {
							//	$ok2_ = 0;
							//	if (($qbeli + $qbonus) <> 0 and ($nama_cst=="")) {
							//		$ok2_ = 1;
							//	}
							//}
							//if ($chkBonus) {
							//	$ok2_ = 0;
							//	if (($qbonus) <> 0 and ($nama_cst=="")) {
							//		$ok2_ = 1;
							//	}
							//}
							
							if ($ok2_) {
								$qtotal = $qbeli + $qbonus;
								$values = $qbonus*$hna;
								$t_qbeli = $t_qbeli + $qbeli;
								$t_qbonus = $t_qbonus + $qbonus;
								$t_qtotal = $t_qtotal + $qtotal;
								$g_qbeli = $g_qbeli + $qbeli;
								$g_qbonus = $g_qbonus + $qbonus;
								$g_qtotal = $g_qtotal + $qtotal;

								$t_values += $values;
								$g_values += $t_values;

								echo "
									<td><small>$no</small></td>
									<td><small>$fakturid</small></td>
									<td><small>$tgljual</small></td>
								";
								if ($nama_cst=='') {
									echo "<td><small>$custid</small></td>";
								} else {
									echo "<td><small>$nama_cst</small></td>";
								}
								echo "
										<td><small>".number_format($hna,0)."</small></td>
										<td align=right><small>".number_format($qbeli,0)."</small></td>
										<td align=right><small>".number_format($qbonus,0)."</small></td>
										<td align=right><small>".number_format($qtotal,0)."</small></td>
										<td align=right><small>".number_format($values,0)."</small></td>
										<td align=left><small>".$divprodid."</small></td>
									</tr>
								";
							}

							$row = mysqli_fetch_array($result);
							$i++;
							// $nm_prod = $row['nm_prod'];
						}// break per bulan

						echo "
							<tr>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td align=right><b>Total :</td>
								<td align=right><b>".number_format($t_qbeli,0)."</b></td>
								<td align=right><b>".number_format($t_qbonus,0)."</b></td>
								<td align=right><b>".number_format($t_qtotal,0)."</b></td>
								<td align=left colspan=2><b>".number_format($t_values,0)."</b></td>
							</tr>
						";

					}else{
						// echo 2;
						// ---------------------------------------------- tampilan All data
						echo '
                        <table id="datatable2" class="table table-striped table-bordered" width="100%" border="1px solid black"><br>
								<tr>
									<th align="left"><small>No</small></th>
									<th align="center"><small>No Faktur</small></th>
									<th align="center"><small>Produk</small></th>
									<th align="center"><small>Tgl. Jual</small></th>
									<th align="center"><small>Nama Outlet</small></th>
									<th align="center"><small>Harga</small></th>
									<th align="center"><small>Jumlah</small></th>
									<th align="center"><small>Bonus</small></th>
									<th align="center"><small>Total</small></th>
									<th align="center"><small>Value</small></th>
									<th align="center"><small>Divisi Produk</small></th>
								</tr>
						';

						$qtotal = 0;
						$t_qbeli = 0;
						$t_qbonus = 0;
						$t_qtotal = 0;
						$t_values = 0;
						$no = 0;
						while ( ($i<=$records)) {
							$no = $no + 1;			
							$custid = $row['custid'];
							$cabangid = $row['cabangId'];
							$tgljual = $row['tgljual'];
							$nama = $row['nm_prod'];	
							$qbeli = $row['qbeli']; 
							$qbonus = $row['qbonus'];
							$fakturid = $row['fakturId'];
							$hna = $row['harga'];
							$divprodid = $row['divprodid'];

							$nama_cst = '';
							$query_cst = "SELECT nama FROM MKT.ecust WHERE distid='$distid' AND cabangid='$cabangid' AND ecustid='$custid'";
							// echo"$query_cst";

							$result_cst = mysqli_query($cnms, $query_cst);
							$num_results_cst = mysqli_num_rows($result_cst);
							if ($num_results_cst) {
								 $row_cst = mysqli_fetch_array($result_cst);
								 $nama_cst = $row_cst['nama'];
							}

							$ok2_ = 1;

							if ($chkBlank) {
								$ok2_ = 0;
								if ($nama_cst=="") {
									$ok2_ = 1;
								}
							}

							if ($ok2_) {
								$qtotal = $qbeli + $qbonus;
								$values = $qbonus*$hna;
								$t_qbeli = $t_qbeli + $qbeli;
								$t_qbonus = $t_qbonus + $qbonus;
								$t_qtotal = $t_qtotal + $qtotal;
								$g_qbeli = $g_qbeli + $qbeli;
								$g_qbonus = $g_qbonus + $qbonus;
								$g_qtotal = $g_qtotal + $qtotal;

								$t_values += $values;
								$g_values += $t_values;

								echo "
									<tr>
										<td nowrap><small>$no</small></td>
										<td nowrap><small>$fakturid</small></td>
										<td><small>$nm_prod</small></td>
										<td nowrap><small>$tgljual</small></td>
								";

								if ($nama_cst == '') {
									echo "<td><small>$custid</small></td>";
								} else {
									echo "<td><small>$nama_cst</small></td>";
								}

								echo "
										<td><small>".number_format($hna,0)."</small></td>
										<td align=right><small>".number_format($qbeli,0)."</small></td>
										<td align=right><small>".number_format($qbonus,0)."</small></td>
										<td align=right><small>".number_format($qtotal,0)."</small></td>
										<td align=right><small>".number_format($values,0)."</small></td>
										<td align=left><small>".$divprodid."</small></td>
									</tr>
								";
							}

							$row = mysqli_fetch_array($result);
							$i++;
							$nm_prod = $row['nm_prod'];
						}// break per bulan

						echo "
							<tr>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td align=right><b>Total :</td>
								<td align=right><b>".number_format($t_qbeli,0)."</b></td>
								<td align=right><b>".number_format($t_qbonus,0)."</b></td>
								<td align=right><b>".number_format($t_qtotal,0)."</b></td>
								<td align=right colspan=2><b>".number_format($t_values,0)."</b></td>
							</tr>
						";
					}
				}
				// eof  i<= num_results

				echo "
						<tr>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td align=right nowrap><b>Grand Total :</td>
							<td align=right><b>".number_format($g_qbeli,0)."</b></td>
							<td align=right><b>".number_format($g_qbonus,0)."</b></td>
							<td align=right><b>".number_format($g_qtotal,0)."</b></td>
							<td align=right colsapn=2><b>".number_format($g_values,0)."</b></td>
						</tr>
					</table>
				";

			}else{
				// echo 2;
				
				echo "
					<br>
					<b>Periode : $periode s/d $periode2</b><hr>
					<table border=\"1\" cellspacing=\"0\" cellpadding=\"1\">
					<tr>
						<th align=\"left\"><small>No</small></th>
						<th align=\"center\"><small>Tgl Jual</small></th>
						<th align=\"center\"><small>Nama Produk</small></th>
						<th align=\"center\"><small>HNA</small></th>
						<th align=\"center\"><small>Bonus</small></th>
						<th align=\"center\"><small>Value</small></th>
						<th align=\"center\"><small>Divisi Produk</small></th>
					</tr>
				";
				
				$no = 1;
				while ($i <= $records) {
					$brgid = $row['brgid'];
					$nm_prod = '';
					$t_qbonus = 0;
					$t_values = 0;
					
					while ( ($i<=$records) and ($brgid != $row['brgid']) ) {
						$custid = $row['custid'];
						$cabangid = $row['cabangId'];
						$tgljual = $row['tgljual'];
						$nama = $row['nm_prod'];	
						$qbeli = $row['qbeli']; 
						$qbonus = $row['qbonus'];
						$fakturid = $row['fakturId'];
						$values = $row['harga']*$qbonus;
						$divprodid = $row['divprodid'];
						
						//if($nm_prod != $row['nm_prod']){
							$t_qbonus += $qbonus;
							$t_values += $values;
						//}
							
						echo "
							<tr>
								<td><small>$no</small></td>
								<td><small>".$tgljual."</small></td>
								<td><small>".$row['nm_prod']."</small></td>
								<td align=right><small>".number_format($row['harga'],0)."</small></td>
								<td align=right><small>".number_format($qbonus,0)."</small></td>
								<td align=right><small>".number_format($values,0)."</small></td>
								<td align=left><small>".$divprodid."</small></td>
						";
						//echo "<td align=right><small>".number_format($t_qbonus,0)."</small></td>";
						echo "</tr>";
						
						$row = mysqli_fetch_array($result);
						$g_qbonus += $qbonus;
						$g_values += $values;
						$i++;
						$no++;
						$nm_prod = $row['nm_prod'];
					}
					//style=background-color:grey
					echo "
						<tr style=background-color:grey>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td align=right><b> Total :</td>
							<td align=right><b>".number_format($t_qbonus,0)."</b></td>
							<td align=right colspan=2><b>".number_format($t_values,0)."</b></td>
						</tr>
					";
				}
				
				echo "
						<tr style=background-color:grey>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td align=right><b>Grand Total :</td>
							<td align=right><b>".number_format($g_qbonus,0)."</b></td>
							<td align=right colspan=2><b>".number_format($g_values,0)."</b></td>
						</tr>
					</table>
				";
			}
		} else {
			echo "<br /><b>Data tidak ditemukan!!!</b><br/><br/>";		
	    }
    
            ?>
    <br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;
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

            #n_content {
                color:#000;
                font-family: "Arial";
                margin: 20px;
                /*overflow-x:auto;*/
            }
        </style>

        <style>
            .divnone {
                display: none;
            }
            #datatable2, #datatable3 {
                color:#000;
                font-family: "Arial";
            }
            #datatable2 th, #datatable3 th {
                font-size: 16px;
            }
            #datatable2 td, #datatable3 td { 
                font-size: 15px;
            }
        </style>
        
    <?PHP }else{ ?>
        <style>
            .tjudul {
                font-family: Georgia, serif;
                font-size: 15px;
                margin-left:10px;
                margin-right:10px;
            }
            .tjudul td {
                padding: 4px;
            }
            #datatable2, #datatable3 {
                font-family: Georgia, serif;
                margin-left:10px;
                margin-right:10px;
            }
            #datatable2 th, #datatable2 td, #datatable3 th, #datatable3 td {
                padding: 4px;
            }
            #datatable2 thead, #datatable3 thead{
                background-color:#cccccc; 
                font-size: 16px;
            }
            #datatable2 tbody, #datatable3 tbody{
                font-size: 15px;
            }
        </style>
    <?PHP } ?>
        
        
</BODY>

    <script>
        // SCROLL
        // When the user scrolls down 20px from the top of the document, show the button
        window.onscroll = function() {scrollFunction()};
        function scrollFunction() {
            if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
                document.getElementById("myBtn").style.display = "block";
            } else {
                document.getElementById("myBtn").style.display = "none";
            }
        }

        // When the user clicks on the button, scroll to the top of the document
        function topFunction() {
            document.body.scrollTop = 0;
            document.documentElement.scrollTop = 0;
        }
        // END SCROLL
    
    
        $(document).ready(function() {
            var table = $('#datatable2, #datatable3').DataTable({
                fixedHeader: true,
                "ordering": false,
                "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
                "displayLength": -1,
                "order": [[ 0, "asc" ]],
                "columnDefs": [
                    { "visible": false },
                    { className: "text-right", "targets": [4,5,6,7,8] },//right
                    { className: "text-nowrap", "targets": [0, 1, 2, 3, 4, 5,6,8,9] }//nowrap

                ],
                bFilter: true, bInfo: true, "bLengthChange": true, "bLengthChange": true,
                "bPaginate": true
            } );

        } );
    
    </script>
</HTML>


<?PHP
hapusdata:
    mysqli_query($cnms, "DROP TEMPORARY TABLE $tmp01");
    mysqli_query($cnms, "DROP TEMPORARY TABLE $tmp02");
    mysqli_query($cnms, "DROP TEMPORARY TABLE $tmp03");
    mysqli_close($cnms);
?>

