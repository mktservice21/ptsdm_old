<?php
session_start();
include "../../config/koneksimysqli.php";
include "../../config/fungsi_sql.php";

$fkaryawan=$_SESSION['IDCARD'];
$fdivisi=$_SESSION['DIVISI'];
$fgroupidcard=$_SESSION['GROUP'];
$fjbtid=$_SESSION['JABATANID'];

$pmodule=$_GET['module'];
$pidmenu=$_GET['idmenu'];


/// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;

    
$columns = array( 
// datatable column index  => database column name
    0 =>'a.noid',
    1 => 'a.noid',
    2 => 'DATE_FORMAT(a.bulan,"%Y%m")',
    3 => 'c.nama',
    4 => 'b.nama',
    5=> 'a.jumlah',
    6=> 'a.est_perbln',
    7=> 'a.est_roi',
    8=> 'a.cn',
    9=> 'a.roi'
);


$sql = "select a.noid, a.tglinput, a.bulan, a.dokterid, a.srid, a.jumlah, a.est_perbln, a.est_roi, a.jml_bulan, a.periode1, a.periode2, 
    a.periode_ket, a.cn, a.roi, a.notes, b.nama as nama_dokter, c.nama as nama_karyawan  
    from hrd.t_estimasi_ki as a join hrd.dokter as b on a.dokterid=b.dokterId
    join hrd.karyawan as c on a.srid=c.karyawanId";
$sql .=" where IFNULL(stsnonaktif,'')<>'Y' ";

$query=mysqli_query($cnmy, $sql) or die("mydata.php: get data");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
    $sql.=" AND ( a.noid LIKE '%".$requestData['search']['value']."%' ";
    
    $sql.=" OR a.dokterid LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR b.nama LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR c.nama LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR DATE_FORMAT(a.bulan,'%d %M %Y') LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR DATE_FORMAT(a.bulan,'%Y%m') LIKE '%".$requestData['search']['value']."%' ";
    
    $sql.=" OR a.srid LIKE '%".$requestData['search']['value']."%' )";
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
    
    $pnomid = $row["noid"];
    $pbulan = $row["bulan"];
    $psrid = $row["srid"];
    $pnmkaryawan = $row["nama_karyawan"];
    $piddokt = $row["dokterid"];
    $pnmdokt = $row["nama_dokter"];
    $pjumlahki = $row["jumlah"];
    $pestjmlbln = $row["est_perbln"];
    $pestroi = $row["est_roi"];
    $pjmlbln = $row["jml_bulan"];
    $pper1 = $row["periode1"];
    $pper2 = $row["periode2"];
    $pperket = $row["periode_ket"];
    $pjmlcn = $row["cn"];
    $pjmlroi = $row["roi"];
    $pnotes = $row["notes"];
    
    
    $pbulan = date("F Y", strtotime($pbulan));
    $pjumlahki=number_format($pjumlahki,2,".",",");
    $pestjmlbln=number_format($pestjmlbln,2,".",",");
    $pjmlcn=number_format($pjmlcn,2,".",",");
    
    $pedit = "<a class='btn btn-success btn-xs' href='?module=$pmodule&act=editdata&idmenu=$pidmenu&nmun=$pidmenu&id=$pnomid'>Edit</a>";
    
    $print="<a title='Print / Cetak' href='#' class='btn btn-info btn-xs' data-toggle='modal' "
        . "onClick=\"window.open('eksekusi3.php?module=$pmodule&brid=$pnomid&iprint=print',"
        . "'Ratting','width=700,height=500,left=500,top=100,scrollbars=yes,toolbar=yes,status=1,pagescrool=yes')\"> "
        . "Print</a>";
    
    $nestedData[] = $no;
    $nestedData[] = "$pedit &nbsp; $print";
    $nestedData[] = $pbulan;
    $nestedData[] = $pnmkaryawan;
    $nestedData[] = $pnmdokt;
    $nestedData[] = $pjumlahki;
    $nestedData[] = $pestjmlbln;
    $nestedData[] = $pestroi;
    $nestedData[] = $pjmlcn;
    $nestedData[] = $pjmlroi;
    
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

