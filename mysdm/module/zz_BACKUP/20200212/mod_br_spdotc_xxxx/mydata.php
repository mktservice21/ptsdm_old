<?php

    session_start();
    include "../../config/koneksimysqli.php";
    
    /// storing  request (ie, get/post) global array to a variable  
    $requestData= $_REQUEST;

    
    $columns = array( 
        // datatable column index  => database column name
        0 =>'idinput',
        1 =>'idinput',
        2 => 'idinput',
        3=> 'nama',
        4=> 'subnama',
        5=> 'nomor',
        6=> 'tgl',
        7=> 'nodivisi',
        8=> 'jumlah'
    );
    
    $tgl1=$_GET['uperiode1'];
    $tgl2=$_GET['uperiode2'];

    $tgl1= date("Y-m", strtotime($tgl1));
    $tgl2= date("Y-m", strtotime($tgl2));
    
    
    //FORMAT(realisasi1,2,'de_DE') as 
    // getting total number records without any search
    $sql = "SELECT idinput, DATE_FORMAT(tgl,'%d/%m/%Y') as tgl, "
            . "divisi, kodeid, nama, subkode, subnama, FORMAT(jumlah,0,'de_DE') as jumlah, "
            . " nomor, nodivisi, pilih, karyawanid, jenis_rpt, userproses, DATE_FORMAT(tgl_proses,'%d/%m/%Y') tgl_proses, jenis_rpt, periodeby ";
    $sql.=" FROM dbmaster.v_suratdana_br ";
    $sql.=" WHERE stsnonaktif <> 'Y' AND CONCAT(kodeid,subkode,divisi) NOT IN ('103OTC', '221OTC') ";
    $sql.=" AND Date_format(tglinput, '%Y-%m') between '$tgl1' and '$tgl2' ";
    $sql .=" AND divisi='OTC'";
    
    
    $query=mysqli_query($cnmy, $sql) or die("mydata.php: get data");
    $totalData = mysqli_num_rows($query);
    $totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

    if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
        $sql.=" AND ( idinput LIKE '%".$requestData['search']['value']."%' ";
        $sql.=" OR nama LIKE '%".$requestData['search']['value']."%' ";
        $sql.=" OR subnama LIKE '%".$requestData['search']['value']."%' ";
        $sql.=" OR nodivisi LIKE '%".$requestData['search']['value']."%' ";
        $sql.=" OR DATE_FORMAT(tgl,'%d %M %Y') LIKE '%".$requestData['search']['value']."%' ";
        $sql.=" OR nomor LIKE '%".$requestData['search']['value']."%' )";
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
        $idno=$row['idinput'];
        $pkaryawanid=$row['karyawanid'];
        $pdivisi=$row['divisi'];
        $pnama=$row['nama'];
        $psubnama=$row['subnama'];
        $pnomor=$row['nomor'];
        $ptgl=$row['tgl'];
        $pjumlah=$row['jumlah'];
        $ndiviotc=$row["nodivisi"];
        $pkode=$row["kodeid"];
        $psubkode=$row["subkode"];
        $pjenisrpt=$row["jenis_rpt"];
        $puserproses=$row["userproses"];
        $ptglproses=$row["tgl_proses"];
        $pjenis_rpt=$row["jenis_rpt"];
        $pperiodeby=$row["periodeby"];
        $nourut = "";
        
        $pmystsyginput="";
        if ($pkode=="1" AND $psubkode=="03") {//ria
            $pmystsyginput=3;
        }elseif ($pkode=="2" AND $psubkode=="21") {//marsis
            $pmystsyginput=4;
        }
            
    
        $pedit="<a class='btn btn-success btn-xs' href='?module=$_GET[module]&act=editdata&idmenu=$_GET[idmenu]&nmun=$_GET[nmun]&id=$idno'>Edit</a>";
        $phapus="<input type='button' value='Hapus' class='btn btn-danger btn-xs' onClick=\"ProsesData('hapus', '$idno')\">";

        $plihat="<a class='btn btn-info btn-xs' href='?module=$_GET[module]&act=lihatdata&idmenu=$_GET[idmenu]&nmun=$_GET[nmun]&id=$idno'>Lihat</a>";
        

        
        $plihat="";
        $plihatview="";
        $plihatexcel="";
        
        if ($pdivisi=="OTC") {
            $plihat="<a class='btn btn-info btn-xs' href='eksekusi3.php?module=lapbrotcpermo&act=input&idmenu=134&ket=bukan&ispd=$idno' target='_blank'>Rekap</a>"
                    . " <a class='btn btn-info btn-xs' href='eksekusi3.php?module=lapbrotcpermo&act=input&idmenu=134&ket=excel&ispd=$idno' target='_blank'>Rekap Excel</a>";
            if ($pjenis_rpt=="L") {
                $plihat="";
                $pedit="";
            }
        }
        
        
        $prptsby="<a class='btn btn-warning btn-xs' href='eksekusi3.php?module=spdotc&act=spdotc&idmenu=226&ket=bukan&ispd=$idno&periodeby=$pperiodeby' target='_blank'>Rpt. SBY</a>"
                . " <a class='btn btn-warning btn-xs' href='eksekusi3.php?module=spdotc&act=spdotc&idmenu=226&ket=excel&ispd=$idno&periodeby=$pperiodeby' target='_blank'>Rpt. SBY Excel</a>";
        
        
        if ($pdivisi=="OTC") {
            if ($row["pilih"]=="N") $ndiviotc="<div style='color:red;'>$ndiviotc</div>";
        }
        
        if (!empty($puserproses)) {
            $pedit="";
            $phapus="";
        }
        
        
        $nestedData[] = $no;

        $nestedData[] = "$pedit $plihat $prptsby $phapus ";

        $nestedData[] = $pdivisi;
        $nestedData[] = $pnama;
        $nestedData[] = $psubnama;
        $nestedData[] = $pnomor;
        $nestedData[] = $ptgl;
        $nestedData[] = $ndiviotc;
        $nestedData[] = $pjumlah;
        //$nestedData[] = $nourut;

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

