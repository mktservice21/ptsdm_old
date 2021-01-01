<?php
include "../../config/koneksimysqli.php";

/// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;

$columns = array( 
// datatable column index  => database column name
    0 =>'brId',
    1 =>'brId',
    2 => 'tgl',
    3=> 'nama_kode',
    4=> 'nama',
    5=> 'nama_cabang',
    6=> 'jumlah',
    7=> 'realisasi1',
    8=> 'cn',
    9=> 'noslip',
    10=> 'tgltrans',
    11=> 'aktivitas1',
    12=> 'aktivitas2'
);
//FORMAT(realisasi1,2,'de_DE') as 
// getting total number records without any search
$sql = "SELECT brId, DATE_FORMAT(tgl,'%d %M %Y') as tgl, DATE_FORMAT(tgltrans,'%d %M %Y') as tgltrans, "
        . "nama, nama_kode, nama_cabang, FORMAT(jumlah,2,'de_DE') as jumlah, realisasi1, "
        . "FORMAT(cn,2,'de_DE') as cn, "
        . "noslip, aktivitas1 ";
$sql.=" FROM dbbudget.v_br0_all ";//v_br0_non
$sql.=" WHERE Date_format(sys_now, '%Y%m')>=DATE_FORMAT((DATE_SUB(curdate(), INTERVAL 2 MONTH)),'%Y%m') and "
        . " (br = '' and br<>'N') ";// tidak ada
        //kode in (select kodeid from dbbudget.br_kode where (br = '' and br<>'N'))
$query=mysqli_query($cnmy, $sql) or die("mydata.php: get data");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

//$sql.=" WHERE 1=1 "; // ada

if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
    $sql.=" AND ( brId LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR nama_kode LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR nama_cabang LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR mrid LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR DATE_FORMAT(tgl,'%d %M %Y') LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR DATE_FORMAT(tgltrans,'%d %M %Y') LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR noslip LIKE '%".$requestData['search']['value']."%' )";
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

    $nestedData[] = $no;
    //$rpjumlah=number_format($row['brId'],0,",",".");
    /*
     * 
            
     */
    $nestedData[] = ""
            . "<a href='#' class='btn btn-info btn-xs' data-toggle='modal' "
            . "onClick=\"window.open('eksekusi_printform.php?module=$_GET[module]&brid=$row[brId]',"
            . "'Ratting','width=700,height=500,left=500,top=100,scrollbars=yes,toolbar=yes,status=1,pagescrool=yes')\"> "
            . "Cetak</a> "
            . "<a class='btn btn-success btn-xs' href='?module=$_GET[module]&act=editdata&idmenu=$_GET[idmenu]&nmun=$_GET[nmun]&id=$row[brId]'>Edit</a>
    ";
    
    $nestedData[] = "<a href='#' data-toggle=\"tooltip\" data-placement=\"top\" title=".$row['brId'].">".$row["tgl"]."</a>";
    $nestedData[] = $row["nama_kode"];
    $nestedData[] = $row["nama"];
    $nestedData[] = $row["nama_cabang"];
    $nestedData[] = $row["jumlah"];
    $nestedData[] = $row["realisasi1"];
    $nestedData[] = $row["cn"];
    $nestedData[] = $row["noslip"];
    $nestedData[] = $row["tgltrans"];
    //$nestedData[] = "<a class='btn btn-success btn-sm' href='?module=$_GET[module]&act=editdata&idmenu=$_GET[idmenu]&nmun=$_GET[nmun]&id=$row[brId]'>Edit</a>";
    //<a class='btn btn-danger btn-sm' href=\"$_GET[aksi]?module=$_GET[module]&act=hapus&id=$row[karyawanId]&idmenu=$_GET[idmenu]\" onClick=\"return confirm('Apakah Anda benar-benar akan menghapusnya?')\">Hapus</a>
/*
     0 =>'brId',
    1 => 'tgl',
    2=> 'nama_kode',
    3=> 'nama',
    4=> 'nama_cabang',
    5=> 'jumlah',
    6=> 'realisasi1',
    7=> 'cn',
    8=> 'noslip',
    9=> 'tgltrans',
    10=> 'aktivitas1',
    11=> 'aktivitas2'
 */

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
