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
/// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;
    
$columns = array( 
    // datatable column index  => database column name
    0 =>'a.idinput',
    1 =>'a.idinput',
    2 => 'a.divisi',
    3=> 'c.nama_pengajuan',
    4=> 'b.nomor',
    5=> 'a.tgl',
    6=> 'a.nodivisi',
    7=> 'a.jumlah',
    8=> 'a.jumlah2',
    9=> 'IFNULL(jumlah,0)+IFNULL(jumlah2,0)'
);

$ptgl1=$_GET['uperiode1'];
$ptgl2=$_GET['uperiode2'];

$ptgl1= date("Y-m-01", strtotime($ptgl1));
$ptgl2= date("Y-m-t", strtotime($ptgl2));


$sql = "select a.idinput, a.tgl, a.divisi, "
        . " a.kodeid, b.nama as namakode, a.subkode, b.subnama, "
        . " a.jumlah, a.jumlah2, IFNULL(a.jumlah,0)+IFNULL(a.jumlah2,0) as jumlah_trans, "
        . " a.nomor, a.nodivisi, a.pilih, a.karyawanid, a.jenis_rpt, c.nama_pengajuan, "
        . " a.userproses, a.tgl_proses, a.tgl_dir, a.tgl_apv2 "
        . " from dbmaster.t_suratdana_br as a "
        . " LEFT JOIN dbmaster.t_kode_spd as b on "
        . " a.kodeid=b.kodeid AND a.subkode=b.subkode "
        . " LEFT JOIN dbmaster.t_kode_spd_pengajuan as c on a.jenis_rpt=c.jenis_rpt ";
$sql .=" WHERE IFNULL(a.stsnonaktif,'')<>'Y' ";
$sql.=" AND ( (Date_format(a.tglinput, '%Y-%m') between '$ptgl1' and '$ptgl2') OR (Date_format(a.tgl, '%Y-%m') between '$ptgl1' and '$ptgl2') ) ";
$sql.=" AND a.subkode IN ('01') ";
$sql.=" AND IFNULL(a.jenis_rpt,'') IN ('C', 'D') ";
if ($pgroupid=="1" OR $pgroupid=="24") {
    
}else{
    $sql.=" AND IFNULL(a.karyawanid,'') IN ('$usrkaryawanid', '0000001043') ";
}


$query=mysqli_query($cnmy, $sql) or die("mydata.php: get data");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
    $sql.=" AND ( a.idinput LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR b.nama LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR b.subnama LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR a.nodivisi LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR a.divisi LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR c.nama_pengajuan LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR DATE_FORMAT(a.tgl,'%d %M %Y') LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR DATE_FORMAT(a.tglinput,'%d %M %Y') LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR a.nomor LIKE '%".$requestData['search']['value']."%' )";
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
    
    $pidno=$row['idinput'];
    $pkryid=$row['karyawanid'];
    $pdivisi=$row['divisi'];
    $pkodeid=$row['kodeid'];
    $pkodenm=$row['namakode'];
    $psubkode=$row['subkode'];
    $psubnama=$row['subnama'];
    $pntgl=$row['tgl'];
    $pnodivis=$row['nodivisi'];
    $pnomor=$row['nomor'];
    $pjumlah1=$row['jumlah'];
    $pjumlah2=$row['jumlah2'];
    $pjumlahtrans=$row['jumlah_trans'];
    $ppilih=$row['pilih'];
    
    $puserproses=$row["userproses"];
    $ptglproses=$row["tgl_proses"];
    $pjenisrpt=$row["jenis_rpt"];
    $pnmpengajuan_jenis=$row["nama_pengajuan"];
    $ptglfin2=$row["tgl_apv2"];
    $ptgldir1=$row["tgl_dir"];
    
    $pidget=$row['idinput'];
    
    $pnamajenis="";
    
    if (empty($pnmpengajuan_jenis)) $pnmpengajuan_jenis="Advance BR";
        
        
    
    $pnmdivisi="";
    if ($pdivisi=="EAGLE") $pdivisi="EAGLE";
    if ($pdivisi=="PEACO") $pdivisi="PEACOCK";
    if ($pdivisi=="PIGEO") $pdivisi="PIGEON";
    if ($pdivisi=="CAN") $pdivisi="CANARY";
    if ($pdivisi=="ETH") $pdivisi="ETHICAL/CAN";
    
    $ndivipilih=$pnodivis;
    if ($ppilih=="N") $ndivipilih="<div style='color:red;'>$pnodivis</div>";
    
    $pntgl = date('d/m/Y', strtotime($pntgl));
    $pjumlah1=number_format($pjumlah1,0,",",",");
    $pjumlah2=number_format($pjumlah2,0,",",",");
    $pjumlahtrans=number_format($pjumlahtrans,0,",",",");
    
    $pmystsyginput="";
    if ($pjenisrpt=="D" OR $pjenisrpt=="C") {
        $plihatview = "<a class='btn btn-info btn-xs' href='eksekusi3.php?module=$pmodule&act=viewbrklaim&idmenu=$pidmenu&ket=bukan&ispd=$pidget&iid=$pmystsyginput' target='_blank'>View</a>";
        $plihatexcel = "<a class='btn btn-info btn-xs' href='eksekusi3.php?module=$pmodule&act=viewbrklaim&idmenu=$pidmenu&ket=excel&ispd=$pidget&iid=$pmystsyginput' target='_blank'>Excel</a>";
    }else{
        $plihatview = "<a class='btn btn-info btn-xs' href='eksekusi3.php?module=$pmodule&act=viewbr&idmenu=$pidmenu&ket=bukan&ispd=$pidget&iid=$pmystsyginput' target='_blank'>View</a>";
        $plihatexcel = "<a class='btn btn-info btn-xs' href='eksekusi3.php?module=$pmodule&act=viewbr&idmenu=$pidmenu&ket=excel&ispd=$pidget&iid=$pmystsyginput' target='_blank'>Excel</a>";
    }
    
    $plihat="$plihatview &nbsp; &nbsp; $plihatexcel";
    
    $pedit="<a class='btn btn-success btn-xs' href='?module=$pmodule&act=editdata&idmenu=$pidmenu&nmun=$pidmenu&id=$pidget'>Edit</a>";
    $phapus="<input type='button' value='Hapus' class='btn btn-danger btn-xs' onClick=\"ProsesDataHapus('hapus', '$pidget')\">";
    
    $pbutton="";
    
    
    
    
    
    if ($pjenisrpt=="J" OR $pjenisrpt=="W") $plihat="";
    if (!empty($puserproses)) {
        $pedit=""; $phapus="";
    }
    
    if (!empty($ptglfin2) AND $ptglfin2<>"0000-00-00") {
        $pedit=""; $phapus="";
    }

    //khusus input hapus di bank
    if ($pjenisrpt=="W") {
        $pedit=""; $phapus="";
    }
    if ($pkryid<>$usrkaryawanid) {
        $pedit=""; $phapus="";
    }
    
    
        
    
    $pbutton="$pedit &nbsp; $phapus &nbsp; ".$plihat;
    
    
    $nestedData[] = $no;
    $nestedData[] = $pbutton;
    $nestedData[] = $pdivisi;
    $nestedData[] = $pnmpengajuan_jenis;
    $nestedData[] = $pnomor;
    $nestedData[] = $pntgl;
    $nestedData[] = $ndivipilih;
    $nestedData[] = $pjumlah1;
    $nestedData[] = $pjumlah2;
    $nestedData[] = $pjumlahtrans;
    
    
    
    
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
