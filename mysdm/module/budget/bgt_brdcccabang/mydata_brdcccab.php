<?php
session_start();
include "../../../config/koneksimysqli_ms.php";
include "../../../config/fungsi_sql.php";
include "../../../config/fungsi_ubahget_id.php";

$cnmy=$cnms;

$pidgrpuser=$_SESSION['GROUP'];
$fkaryawan=$_SESSION['IDCARD'];
$fuserid=$_SESSION['USERID'];
$pidjabatan=$_SESSION['JABATANID'];
$pmodule=$_GET['module'];

/// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;

$columns = array( 
// datatable column index  => database column name
    0 =>'a.id',
    1 =>'a.id',
    2 => 'a.id',
    3 => 'a.id',
    4=> 'a.tanggal',
    5=> 'a.jenis_br',
    6=> 'd.namalengkap',
    7=> 'c.nama',
    8=> 'a.jumlah',
    9=> 'a.jumlah1',
    10=> 'a.keterangan'
);

$pcabangid="";
if (isset($_GET['ucabid'])) {
    $pcabangid=$_GET['ucabid'];
}

$ptxtcabangid="";
$pfiltercabang="";
if (isset($_GET['utxtcabid'])) {
    $ptxtcabangid=$_GET['utxtcabid'];
    if (empty($pcabangid) AND !empty($ptxtcabangid)) {
        
        $tags_cab = explode(',',$ptxtcabangid);

        foreach($tags_cab as $nidcab) {    
            $pfiltercabang .="'".$nidcab."',";
        }
        
        if (!empty($pfiltercabang)) $pfiltercabang="(".substr($pfiltercabang, 0, -1).")";
    }
}

//FORMAT(realisasi1,2,'de_DE') as 
// getting total number records without any search
$sql = "select a.id, a.brid_proses, a.tanggal, a.bulan1, a.bulan2, a.icabangid as icabangid, b.nama as nama_cabang, 
    a.areaid, a.iddokter, d.namalengkap as nama_dokter, a.idpraktek, a.divprodid, a.createdby as karyawanid, c.nama as nama_karyawan, 
    a.jenis_br, a.kode, a.jumlah, a.jumlah1, a.keterangan, a.approvedby_dm, a.approveddate_dm, a.rejecteddate_dm,
    a.approvedby_sm, a.approveddate_sm, a.rejecteddate_sm, 
    a.approvedby_gsm, a.approveddate_gsm, a.rejecteddate_gsm ";
$sql.=" FROM ms2.br as a LEFT JOIN mkt.icabang as b on a.icabangid=b.icabangId "
        . " LEFT JOIN hrd.karyawan as c on LPAD(ifnull(a.createdby,0), 10, '0')=c.karyawanId "
        . " JOIN ms2.masterdokter as d on a.iddokter=d.id ";
$sql.=" WHERE 1=1 ";
//$sql.=" AND a.`kode` IN ('700-02-03', '700-04-03', '700-01-03') ";
$sql.=" AND ( a.`kode` IN ('3', '4') OR a.createdby IN ('$fkaryawan', '$fuserid') )";
$sql.=" AND  a.`kode` NOT IN ('1', '2') ";

if (!empty($pcabangid)) $sql.=" AND a.icabangId='$pcabangid' ";
else{
    if (!empty($pfiltercabang) AND ($pidjabatan=="10" OR $pidjabatan=="18" OR $pidjabatan=="08" OR $pidjabatan=="20" OR $pidjabatan=="05") ) {
        $sql.=" AND a.icabangId IN $pfiltercabang ";
    }
}

$query=mysqli_query($cnmy, $sql) or die("mydata.php: get data");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

//$sql.=" WHERE 1=1 "; // ada

if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
    $sql.=" AND ( a.id LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR a.icabangid LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR  b.nama LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR a.idpraktek LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR a.divprodid LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR c.nama LIKE '%".$requestData['search']['value']."%' )";
}
$query=mysqli_query($cnmy, $sql) or die("mydata.php: get data");
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($cnmy, $sql) or die("mydata.php: get data");

$data = array();
$no=1;
while( $row=mysqli_fetch_array($query) ) {  // preparing an array
    $nestedData=array();
    $idno=$row['id'];
    $pidbrpros=$row['brid_proses'];
    
    $ptgl=$row['tanggal'];
    $picabid=$row['icabangid'];
    $pnmcabang=$row['nama_cabang'];
    $pareaid=$row['icabangid'];
    $pnamaarea=$row['icabangid'];
    $pnmkaryawan=$row['nama_karyawan'];
    $pidkaryawan=$row['karyawanid'];
    $pjenisbr=$row['jenis_br'];
    $pkodeid=$row['kode'];
    $pjumlah=$row['jumlah'];
    $pjumlahreal=$row['jumlah1'];
    $pket=$row['keterangan'];
    $pnmdokter=$row['nama_dokter'];
    
    
    $preject2=$row['rejecteddate_dm'];
    $patasan2=$row['approvedby_dm'];
    $ptglatasan2=$row['approveddate_dm'];
    $preject3=$row['rejecteddate_sm'];
    $patasan3=$row['approvedby_sm'];
    $ptglatasan3=$row['approveddate_sm'];
    $preject4=$row['rejecteddate_gsm'];
    $patasan4=$row['approvedby_gsm'];
    $ptglatasan4=$row['approveddate_gsm'];
    
    if (empty($patasan2)) $ptglatasan2="";
    if ($ptglatasan2=="0000-00-00 00:00:00" OR $ptglatasan2=="0000-00-00") $ptglatasan2="";
    if ($ptglatasan3=="0000-00-00 00:00:00" OR $ptglatasan3=="0000-00-00") $ptglatasan3="";
    if ($ptglatasan4=="0000-00-00 00:00:00" OR $ptglatasan4=="0000-00-00") $ptglatasan4="";
    
    if ($preject2=="0000-00-00 00:00:00" OR $preject2=="0000-00-00") $preject2="";
    if ($preject3=="0000-00-00 00:00:00" OR $preject3=="0000-00-00") $preject3="";
    if ($preject4=="0000-00-00 00:00:00" OR $preject4=="0000-00-00") $preject4="";
    
    
    $ptanggal = date('d/m/Y', strtotime($ptgl));
    $pjumlah=number_format($pjumlah,0,",",",");
    $pjumlahreal=number_format($pjumlahreal,0,",",",");
    
    $pnamajenis="";
    if ($pjenisbr=="ADVANCE") {
        $pnamajenis="Sudah Ada Kuitansi";
    }elseif ($pjenisbr=="PCM") {
        $pnamajenis="Belum Ada Kuitansi";
    }
    
    $pidget=encodeString($idno);
    
    $prealjumlah="<a class='btn btn-warning btn-xs' href='?module=$_GET[module]&act=jmlrealisasi&idmenu=$_GET[idmenu]&nmun=$_GET[nmun]&id=$pidget'>Realisasi</a>";
    $pedit="<a class='btn btn-success btn-xs' href='?module=$_GET[module]&act=editdata&idmenu=$_GET[idmenu]&nmun=$_GET[nmun]&id=$pidget'>Edit</a>";
    $phapus="<input type='button' value='Hapus' class='btn btn-danger btn-xs' onClick=\"ProsesData('hapus', '$idno')\">";
    
    $print="<a style='font-size:11px;' title='Print / Cetak' href='#' class='btn btn-info btn-xs' data-toggle='modal' "
        . "onClick=\"window.open('eksekusi3.php?module=$pmodule&brid=$pidget&iprint=print',"
        . "'Ratting','width=700,height=500,left=500,top=100,scrollbars=yes,toolbar=yes,status=1,pagescrool=yes')\"> "
        . "Print</a>";
                    
    $phapus = "";
    
    if ($pidgrpuser=="1" OR $pidgrpuser=="24") {
        
    }else{
        //$pedit="";
    }
    
    if (empty($pidbrpros)) {
        $prealjumlah = "";
    }else{
        $phapus = "";
    }
    
    if ($pjenisbr=="PCM") {
    }else{
        $prealjumlah = "";
    }
    
    if (!empty($ptglatasan2)) {
        $pedit="Approved DM";
        $phapus = "";
    }
    
    if (!empty($ptglatasan3)) {
        $pedit="Approved SM";
        $phapus = "";
    }
    
    if (!empty($ptglatasan4)) {
        $pedit="Approved GSM";
        $phapus = "";
    }
    
    if (!empty($preject2)) {
        $pedit="Reject DM";
        $phapus = "";
        $print = "";
        $prealjumlah = "";
    }
    
    if (!empty($preject3)) {
        $pedit="Reject SM";
        $phapus = "";
        $print = "";
        $prealjumlah = "";
    }
    
    if (!empty($preject4)) {
        $pedit="Reject GSM";
        $phapus = "";
        $print = "";
        $prealjumlah = "";
    }
    
    
    $ppilihan="$pedit $phapus $print";
    
    
    $nestedData[] = $no;
    $nestedData[] = $ppilihan;
    $nestedData[] = $prealjumlah;
    $nestedData[] = $idno;
    $nestedData[] = $ptanggal;
    $nestedData[] = $pnamajenis;
    $nestedData[] = $pnmdokter;
    $nestedData[] = $pnmkaryawan;
    $nestedData[] = $pjumlah;
    $nestedData[] = $pjumlahreal;
    $nestedData[] = $pket;

    $data[] = $nestedData;
    $no=$no+1;
}



$json_data = array(
    "draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
    "recordsTotal"    => intval( $totalData ),  // total number of records
    "recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
    "data"            => $data   // total data array
);

echo json_encode($json_data);  // send data as json format

?>
