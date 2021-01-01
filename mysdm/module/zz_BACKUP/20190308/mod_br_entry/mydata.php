<?php
include "../../config/koneksimysqli.php";

/// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;

$columns = array( 
// datatable column index  => database column name
    0 =>'NOID',
    1 =>'NOID',
    2 => 'TGL',
    3=> 'nama',
    4=> 'nama_cabang',
    5=> 'JUMLAH',
    6=> 'TGL_PERLU',
    7=> 'AKTIVITAS1'
);

// getting total number records without any search
$sql = "SELECT NOID, DATE_FORMAT(TGL,'%d %M %Y') as TGL, DATE_FORMAT(TGL_PERLU,'%d %M %Y') as TGL_PERLU, "
        . "divprodid, KARYAWANID, nama, ICABANGID, nama_cabang, FORMAT(JUMLAH,2,'de_DE') as JUMLAH, "
        . "AKTIVITAS1 ";
$sql.=" FROM dbbudget.v_br ";
$sql.=" WHERE 1=1 ";

if ($_GET['uloglvl']=="FF2" or $_GET['uloglvl']=="FF3" or $_GET['uloglvl']=="FF4") {
    $sql.=" and divprodid='$_GET[ulogdivisi]' ";
}
//$sql.=" WHERE kode in (select kodeid from dbbudget.br_kode where (br = '' and br<>'N')) ";// tidak ada
$query=mysqli_query($cnmy, $sql) or die("mydata.php: get data");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

//$sql.=" WHERE 1=1";

if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
    $sql.=" AND ( NOID LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR nama LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR nama_cabang LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR FORMAT(JUMLAH,2,'de_DE') LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR TGL(tgl,'%d %M %Y') LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR TGL_PERLU(tgltrans,'%d %M %Y') LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR nama LIKE '%".$requestData['search']['value']."%' )";
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
    $nestedData[] = ""
            . "<a href='#' class='btn btn-info btn-xs' data-toggle='modal' "
            . "onClick=\"window.open('eksekusi_printform.php?module=cetak&id=$row[NOID]',"
            . "'Ratting','width=700,height=500,left=500,top=100,scrollbars=yes,toolbar=yes,status=1,pagescrool=yes')\"> "
            . "Cetak</a> "
            . "<a class='btn btn-success btn-xs' href='?module=$_GET[module]&act=editdata&idmenu=$_GET[idmenu]&nmun=$_GET[nmun]&id=$row[NOID]'>Edit</a>
    ";
    $nestedData[] = "<a class='btn btn-info btn-xs' href='#' data-toggle=\"tooltip\" data-placement=\"top\" "
            . " onClick=\"window.open('eksekusi_printform.php?module=entrybrnoncabang&act=lihatdatauc&id=$row[NOID]','Ratting', "
            . "'width=700,height=600,left=400,top=50,scrollbars=yes,toolbar=yes,status=1,pagescrool=yes')\" title=".$row['NOID'].">".$row["TGL"]."</a>";
    $nestedData[] = $row["TGL_PERLU"];
    $nestedData[] = $row["nama"]." <small>(".$row["divprodid"].")<small>";
    $nestedData[] = $row["nama_cabang"];
    $nestedData[] = $row["JUMLAH"];
    $nestedData[] = $row["AKTIVITAS1"];

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
