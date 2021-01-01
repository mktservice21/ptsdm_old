<?php
session_start();
include "../../config/koneksimysqli.php";

/// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;



$pmodule=$_GET['module'];
$pidmenu=$_GET['idmenu'];

$ptipe=$_GET['utgltipe'];
$pbln=$_GET['ubulan'];
$ptgl=$_GET['utanggal'];
            
$pbulan= date("Ym", strtotime($pbln));
$ptanggal= date("Y-m-d", strtotime($ptgl));


$ptipe=$_GET['utgltipe'];
$columns = array( 
// datatable column index  => database column name
    0 =>'idantrian',
    1 =>'tanggal',
    2 => 'status_trf',
    3=> 'nourut',
    4=> 'nama',
    5=> 'nodivisi',
    6=> 'jumlah'
);


$sql = "SELECT idantrian, DATE_FORMAT(tanggal,'%d/%m/%Y') as tanggal, status_trf, "
        . " nourut, nama, nodivisi, FORMAT(jumlah,0,'de_DE') as jumlah, userid, selesai, keterangan, karyawanid ";
$sql.=" FROM dbmaster.v_br_antrian ";
$sql.=" WHERE stsnonaktif <> 'Y' ";
if ($ptipe=="B") {
    $sql.=" AND Date_format(tanggal, '%Y%m') = '$pbulan' ";
}else{
    $sql.=" AND tanggal = '$ptanggal' ";
}

$query=mysqli_query($cnmy, $sql) or die("mydata.php: get data");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
    $sql.=" AND ( idantrian LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR nama LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR nourut LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR nodivisi LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR DATE_FORMAT(tanggal,'%d %M %Y') LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR status_trf LIKE '%".$requestData['search']['value']."%' )";
}

$query=mysqli_query($cnmy, $sql) or die("mydata.php: get data");
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($cnmy, $sql) or die("mydata.php: get data");

$data = array();
$no=1;
$pudgroupuser=$_SESSION['GROUP'];
$pidcard=$_SESSION['IDCARD'];
while( $row=mysqli_fetch_array($query) ) {  // preparing an array
    $nestedData=array();
    
    $pidantrian=$row['idantrian'];
    $pntgl=$row['tanggal'];
    $pststrf=$row['status_trf'];
    $pnourut=$row['nourut'];
    $pnama=$row['nama'];
    $pnodivisi=$row['nodivisi'];
    $pjumlah=$row['jumlah'];
    $puserid=$row['userid'];
    $pselesai=$row['selesai'];
    $pketerangan=$row['keterangan'];
    $pkryid=$row['karyawanid'];
    
    $pnamaststrf="Payroll";
    if ($pststrf=="T") $pnamaststrf="Transfer";
    
    
    $pbtnedit = "<a class='btn btn-success btn-xs' href='?module=$pmodule&act=editdata&idmenu=$pidmenu&nmun=$pidmenu&id=$pidantrian'>Edit</a>";
    $pbtnhapus = "<input type='button' value='Hapus' class='btn btn-danger btn-xs' onClick=\"ProsesData('hapus', '$pidantrian')\">";
    $pbtnselesai= "<input type='button' value='Selesai' class='btn btn-warning btn-xs' onClick=\"ProsesData('selesai', '$pidantrian')\">";
    
    if ($pudgroupuser!="1") {
        if ($pidcard<>$puserid AND $pkryid<>$pidcard) {
            if ($pudgroupuser!="25") {
                $pjumlah = "";
            }
            $pbtnedit = "";
            $pbtnhapus = "";
            $pbtnselesai = "";
        }
    }
    
    if ($pselesai=="Y") { 
        $pbtnedit = "";
        $pbtnhapus = "";
        $pbtnselesai = "selesai";
    }
    
    $plink = "$pbtnedit $pbtnhapus $pbtnselesai";
    
    $nestedData[] = $plink;
    $nestedData[] = $pntgl;
    $nestedData[] = $pnamaststrf;
    $nestedData[] = $pnourut;
    $nestedData[] = $pnama;
    $nestedData[] = $pnodivisi;
    $nestedData[] = $pjumlah;
    $nestedData[] = $pketerangan;
    
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

mysqli_close($cnmy);
?>