<?php

date_default_timezone_set('Asia/Jakarta');
ini_set("memory_limit","512M");
ini_set('max_execution_time', 0);
    
session_start();

$pmodule=$_GET['module'];
$pidmenu=$_GET['idmenu'];
$pgroupid=$_SESSION['GROUP'];
$usrkaryawanid=$_SESSION['IDCARD'];

include "../../../config/koneksimysqli.php";
include "../../../config/fungsi_ubahget_id.php";
/// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;
    
$columns = array( 
    // datatable column index  => database column name
    0 =>'a.idterima',
    1 =>'a.idterima',
    2 => 'RIGHT(a.idterima,10)',
    3=> 'b.idpo',
    4=> 'e.NAMA_SUP',
    5=> 'a.tgl_terima'
);

$nkaryawanid=$_GET['ukryid'];
$ptgl1=$_GET['uperiode1'];
$ptgl2=$_GET['uperiode2'];

$ptgl1= date("Y-m-01", strtotime($ptgl1));
$ptgl2= date("Y-m-t", strtotime($ptgl2));

//a.id, a.jml_terima, a.ket_terima, b.idpr_po, a.idpo_d, 
$sql = "select DISTINCT a.igroup, a.tglinput, a.tgl_terima, 
    a.keterangan, a.`status`, a.stsnonaktif, a.userid, 
    a.idterima,
    b.idpo, c.kdsupp, e.NAMA_SUP as nama_sup 
    from dbpurchasing.t_po_transaksi_terima as a 
    JOIN dbpurchasing.t_po_transaksi_d as b on a.idpo_d=b.idpo_d 
    JOIN dbpurchasing.t_po_transaksi as c on b.idpo=c.idpo 
    JOIN dbpurchasing.t_pr_transaksi_po as d on b.idpr_po=d.idpr_po AND c.kdsupp=d.kdsupp 
    JOIN dbmaster.t_supplier as e on c.kdsupp=e.KDSUPP ";
$sql .=" WHERE IFNULL(a.stsnonaktif,'')<>'Y' ";
$sql.=" AND ( (a.tglinput between '$ptgl1' and '$ptgl2') OR (a.tgl_terima between '$ptgl1' and '$ptgl2') ) ";


$query=mysqli_query($cnmy, $sql) or die("mydata.php: get data");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
    $sql.=" AND ( a.id LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR b.idpo LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR c.kdsupp LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR e.NAMA_SUP LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR a.ket_terima LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR DATE_FORMAT(a.tgl_terima,'%d %M %Y') LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR DATE_FORMAT(a.tglinput,'%d %M %Y') LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR a.idpo_d LIKE '%".$requestData['search']['value']."%' )";
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
    
    $pidno=$row['idterima'];
    $pidgroup=$row['igroup'];
    $pidpo=$row['idpo'];
    $pnamasup=$row['nama_sup'];
    $ptglterima=$row['tgl_terima'];
    //$pjmlterima=$row['jml_terima'];
    //$pketterima=$row['ket_terima'];
    
    
    $ptglterima = date('d/m/Y', strtotime($ptglterima));
    //$pjmlterima=number_format($pjmlterima,0,",",",");
    
    $pidnoget=encodeString($pidno);
    
    $pedit="<a class='btn btn-success btn-xs' href='?module=$pmodule&act=editdata&idmenu=$pidmenu&nmun=$pidmenu&id=$pidnoget'>Edit</a>";
    $phapus="<input type='button' value='Hapus' class='btn btn-danger btn-xs' onClick=\"ProsesDataHapus('hapus', '$pidno')\">";
    
    $print="<a title='Detail Barang / Print' href='#' class='btn btn-dark btn-xs' data-toggle='modal' "
        . "onClick=\"window.open('eksekusi3.php?module=gimicterimabarang&nid=$pidno&iprint=print',"
        . "'Ratting','width=700,height=500,left=500,top=100,scrollbars=yes,toolbar=yes,status=1,pagescrool=yes')\"> "
        . "Detail</a>";
    
    
    $pbutton="$pedit &nbsp; $phapus &nbsp; $print";
    
    
    
    $nestedData[] = $no;
    $nestedData[] = $pbutton;
    $nestedData[] = $pidno;
    $nestedData[] = $pidpo;
    $nestedData[] = $pnamasup;
    $nestedData[] = $ptglterima;
    //$nestedData[] = $pjmlterima;
    //$nestedData[] = $pketterima;
    
    
    
    
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
