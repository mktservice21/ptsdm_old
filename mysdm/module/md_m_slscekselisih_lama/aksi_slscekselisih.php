<?php
    session_start();
    ini_set("memory_limit","5000M");
    ini_set('max_execution_time', 0);
    include "../../config/koneksimysqli_it.php";
    include "../../config/fungsi_sql.php";
    
?>
<script>
$(document).ready(function() {

    var table = $('#datatable').DataTable({
        fixedHeader: true,
        //"ordering": false,
        "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
        "displayLength": 10,
        "columnDefs": [
            { "orderable": false, "targets": 1 },
            { "orderable": false, "targets": 2 },
            { "orderable": false, "targets": 14 },
            { "contentPadding": "1" },
            { "visible": false },
            { className: "text-right", "targets": [3, 4, 5] }//,//right
            //{ className: "text-nowrap", "targets": [0, 1, 2, 3, 4, 5] }//nowrap

        ]
    } );

} );
</script>
<style>
    .divnone {
        display: none;
    }
    #datatable th {
        font-size: 12px;
    }
    #datatable td { 
        font-size: 11px;
    }
    textarea {
        resize: none;
        overflow:hidden;
        width: 0.1px;
        height: 0.0001px;
        font-size: 0.1px;
    }
</style>
<?PHP
    $tgl01=$_POST["bulan"];
    $bulan= date("Y-m", strtotime($tgl01));
    $tgldanbulan= date("Y-m-01", strtotime($tgl01));
    $region=$_POST["region"];
    if ($region=="B"){ $namaregion="Barat";} else if ($region=="T"){$namaregion="Timur";}
    $distibutor=$_POST["distibutor"];
    $fildist=(int)$_POST["distibutor"];
    $ketnya=$_POST["eket"];
    $selisih=$_POST["selisih"];
    $karyawanid=$_SESSION["IDCARD"];
    
    $milliseconds = round(microtime(true) * 1000);
    $now=date("mdYhis");
    $tmp0 ="dbtemp.DTSALESDISTSELISIHXX00".$karyawanid."_$now$milliseconds";
    $tmp1 ="dbtemp.DTSALESDISTSELISIHXX01".$karyawanid."_$now$milliseconds";
    
    $fperiode="";
    $fdist="";
    $fselisih="";
    $initial = getfieldcnit("select initial as lcfields from MKT.distrib0 where distid='$distibutor'");
    
    
    if ((int)$selisih==1) $fselisih=" AND ifnull(selisih,0) <>0 ";

    if ((int)$distibutor==2) {
        $nmtabel="MKT.salesspp";
        $subdis="s.subdist";
    }else{
        $subdis = "CAST('' AS CHAR(10)) AS subdist";
        
        if ((int)$distibutor==3) $nmtabel="MKT.salesams";
        if ((int)$distibutor==5) $nmtabel="MKT.salespv";
        if ((int)$distibutor==6) $nmtabel="MKT.salescp1";
        if ((int)$distibutor==10) $nmtabel="MKT.salessaptabaru";
        if ((int)$distibutor==11) $nmtabel="MKT.salescombibaru";
        if ((int)$distibutor==16) $nmtabel="MKT.salesmas";
        if ((int)$distibutor==23) $nmtabel="MKT.salesdum";
        if ((int)$distibutor==30) $nmtabel="MKT.salescpp";
        if ((int)$distibutor==31) $nmtabel="MKT.salessks";
    }
    
if ((int)$ketnya==0) {

    
    $query = "SELECT
        CAST($distibutor as char(10)) distid,
	$subdis,
        CAST('$tgldanbulan' AS date) tgljual, 
	s.fakturId fakturid,
	s.cabangid,
	s.custid ecustid,
	e.iCustId icustid,
	e.nama nama_cust,
	e.alamat1,
	e.alamat2,
	e.iCabangId icabangid,
	c.nama nama_cabang,
        e.areaId areaid,
	ip.DivProdId divprodid,
	s.brgid,
	p.iProdId iprodid,
	ip.nama nama_produk,
	ip.hna harga,
	SUM( s.qbeli ) qty,
	CAST(0 as DECIMAL(32,2)) as qtysdm,
	CAST(0 as DECIMAL(32,2)) as selisih 	 
        FROM
	$nmtabel s
	LEFT JOIN MKT.ecust e ON s.cabangId = e.CabangId 
	AND s.custid = e.ecustid 
	AND '$distibutor' = e.DistId
	LEFT JOIN MKT.eproduk AS p ON s.brgid = p.eProdId 
	AND '$distibutor' = p.DistId
	LEFT JOIN MKT.ecabang AS c ON s.cabangId = c.ecabangId 
	AND '$distibutor' = c.distid
	LEFT JOIN MKT.iproduk ip ON p.iprodid = ip.iprodid 
        WHERE
	LEFT ( s.tgljual, 7 ) = '$bulan' 
	AND p.DistId = '$distibutor' 
	AND e.DistId = '$distibutor' 
	AND e.nama NOT IN ( SELECT ee.nama FROM dbmaster.ecustnot AS ee ) 
	AND ip.DivProdId IN ( 'EAGLE', 'PIGEO', 'PEACO' )
	GROUP BY 1,2,3,	4,5,6,7,8,9,10,11,12,13,14,15";
    
    mysqli_query($cnit, "create table $tmp0($query)"); 
	mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    //create index temp dist
    $results = mysqli_query($cnit, "CREATE INDEX inx on $tmp0 (fakturid, cabangid, ecustid, icustid, divprodid, iprodid)");
    
	
	
	//baru
	
    
    mysqli_query($cnit, "ALTER TABLE $tmp0 ADD ikode Char(200), ADD INDEX a0 (ikode)");
	$query = "ALTER TABLE $tmp0 ADD INDEX a0 (ikode), ADD INDEX a1 (distid), ADD INDEX a2 (cabangid), ADD INDEX a3 (ecustid), ADD INDEX a4 (fakturid), ADD INDEX a5 (iprodid)";
	mysqli_query($cnit, $query); mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
	
	mysqli_query($cnit, "OPTIMIZE TABLE $tmp0");
	mysqli_query($cnit, "REPAIR TABLE $tmp0");
	
	$query = "update $tmp0 set ikode=IFNULL(CONCAT(IFNULL(cabangid,''), IFNULL(ecustid,''), IFNULL(fakturid,''), IFNULL(iprodid,'')),'')";
	mysqli_query($cnit, $query); mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
	
	mysqli_query($cnit, "OPTIMIZE TABLE $tmp0");
	mysqli_query($cnit, "REPAIR TABLE $tmp0");
	
	
	//end baru
	
	
    $query = "SELECT
	CAST($fildist as char(10))  distid,
        CAST('0000-00-00' AS date) tgljual, 
	s.initialecabang cabangid,
	s.fakturid,
	s.iprodid,
	s.ecustid,
	s.hna,
	sum( s.qty ) qty 
        FROM
	MKT.mr_sales2 AS s 
        WHERE
	LEFT ( s.tgljual, 7 ) = '$bulan' 
	AND s.DistId = '$distibutor'
	AND CONCAT(s.initialecabang, s.ecustid, s.fakturid, s.iprodid) in 
        (select ikode from $tmp0 a) 
        AND s.DivProdId IN ( 'EAGLE', 'PIGEO', 'PEACO' )
        GROUP BY 1, 2, 3, 4, 5, 6";//CONCAT(a.cabangid, a.ecustid, a.fakturid, a.iprodid) menggantikan a.ikode
    mysqli_query($cnit, "create table $tmp1($query)"); 
	mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
	
	//baru
	mysqli_query($cnit, "ALTER TABLE $tmp0 DROP COLUMN ikode"); mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
	//end baru
	
	
	
    //create index tempmr_sales2
    $results = mysqli_query($cnit, "CREATE INDEX inx on $tmp1 (distid, fakturid, cabangid, ecustid, iprodid)");
    
    
    $query = "UPDATE $tmp0 s
        JOIN $tmp1 s2 ON 
        s.distid=s2.distid 
        AND s.cabangid=s2.cabangid  
        AND s.ecustid=s2.ecustid 
        AND s.fakturid=s2.fakturid 
        AND s.iprodid=s2.iprodid 
        SET s.qtysdm=s2.qty WHERE s.distid=$distibutor AND s2.distid=$distibutor";
    $results = mysqli_query($cnit, $query);//AND s.qty=s2.qty
    
    $results = mysqli_query($cnit, "update $tmp0 set selisih=abs(qty)-abs(qtysdm)");
    
    
    
    //delete temp dist
    $query = "delete from dbmaster.tmp_sales_dist where distid=$fildist";
    mysqli_query($cnit, $query); mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    //insert temp dist
    $query = "insert into dbmaster.tmp_sales_dist select * from $tmp0";
    mysqli_query($cnit, $query); mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    

    
    //delete tempmr_sales2
    $query = "delete from dbmaster.tmp_sales_mr2 where distid=$fildist";
    mysqli_query($cnit, $query); mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    //insert tempmr_sales2
    $query = "insert into dbmaster.tmp_sales_mr2 select * from $tmp1";
    mysqli_query($cnit, $query); mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
} else {
    
    $fperiode = " AND LEFT(tgljual, 7) = '$bulan'";
    $fdist = " AND distid=$distibutor ";
}
$fdist = " AND distid=$fildist ";
?>


<table id='datatable' class='table nowrap table-striped table-bordered' width='100%'>
    <thead>
        <tr>
            <th>No</th>
            <th></th>
           <th></th><!--<input type=checkbox value='cekall' name=cekall class='cekall'>-->
            <th>Selisih</th>
            <th>Qty</th>
            <th>Qty SDM</th>
            <th>Kode Produk</th>
            <th>Produk</th>
            <th>Tgl Jual</th>
            <th>FakturId</th>
            <th>Cabang</th>
            <th>Subdist</th>
            <th>Ecust</th>
            <th>Alamat</th>
            <th></th>
        </tr>
    </thead>
    </tbody>
    <?PHP
        $no=1;
        $query = "select * from dbmaster.tmp_sales_dist WHERE 1=1 $fselisih $fdist order by nama_cabang, nama_cust, fakturid, nama_produk";
        $group1 = mysqli_query($cnit, $query);
        $ketemu=  mysqli_num_rows($group1);
        while ($g1=mysqli_fetch_array($group1)){
            echo "<tr scope='row'>";
            $selisih=number_format($g1['selisih'],0,",",",");
            $qty=number_format($g1['qty'],0,",",",");
            $qtysdm=number_format($g1['qtysdm'],0,",",",");
            $divisi=$g1['divprodid'];
            $kdprod=$g1['iprodid'];
            $prod=$g1['nama_produk'];
            //$tgljual=$g1['tgljual'];
            //$blnjual= date("Y-m", strtotime($g1['tgljual']));
            
            $faktur=$g1['fakturid'];
            $cabang=$g1['nama_cabang'];
            $subdis=$g1['subdist'];
            $ecust=$g1['nama_cust'];
            $alamat=$g1['alamat1'];
            
            $icabanid=$g1['icabangid'];
            $iareaid=$g1['areaid'];
            $ecustid=$g1['ecustid'];
            
            $link2 = ""
                . "<a title='Print / Cetak' href='#' class='btn btn-info btn-xs' data-toggle='modal' "
                . "onClick=\"window.open('module/md_m_slscekselisih_lama/lihatdata.php?module=$_GET[module]&idprod=$kdprod"
                . "&fakturid=$faktur',"
                . "'Ratting','width=700,height=500,left=300,top=100,scrollbars=yes,toolbar=yes,status=1,pagescrool=yes')\"> "
                . "Lihat Data</a> "
                ."";
            
            $linksel = "<button title='$cabang' class='btn btn-default btn-xs' onclick=\"myCopyClip('sno$no')\">Select</button>";
            $linkins = "<button title='$cabang' class='btn btn-danger btn-xs' onclick=\"myCopyClip('ino$no')\">Insert Barat</button>";
            $linkinsTimur = "<button title='$cabang' class='btn btn-success btn-xs' onclick=\"myCopyClip('inotim$no')\">Insert Timur</button>";
            $linkinsJoin = "<button title='$cabang' class='btn btn-dark btn-xs' onclick=\"myCopyClip('inojoin$no')\">Insert Join</button>";
            $linkup = "<button title='$cabang' class='btn btn-info btn-xs' onclick=\"myCopyClip('uno$no')\">Update</button>";
            
            //$icabid="'0000000031' icabangid";
            //$iareaid="'0000000001' areaid";
            
            $querydistri = "select s.* from $nmtabel s 
                LEFT JOIN MKT.eproduk p on s.brgid=p.eProdId and p.DistId=$fildist WHERE 
                 s.fakturid='$faktur' AND p.iProdId ='$kdprod';";
            
            $querysel = "$querydistri select * from MKT.mr_sales2 WHERE fakturid='$faktur' AND iprodid='$kdprod';";
            
            $updatetmpsudah ="update dbmaster.tmp_sales_dist set qtysdm=qty, selisih=0 where fakturid='$faktur' AND iprodid='$kdprod';";
            
            //barat 31
            $queryins = "insert into MKT.mr_sales2 (icabangid, areaid, icustid, ecustid, iprodid, distId, initial, fakturid, tgljual, ecabangid, divprodid, qty, hna, initialecabang) "
                    . " SELECT '0000000031' icabangid, '0000000001' areaid, '' as icustid, ecustid, iprodid, '$distibutor' distId, '$initial' initial, fakturid, tgljual, cabangid ecabangid,"
                    . " divprodid, qty, harga AS hna, cabangid initialecabang"
                    . " FROM dbmaster.tmp_sales_dist WHERE fakturid='$faktur' AND iprodid='$kdprod' "
                    . " AND CONCAT(fakturid,iprodid) not in (select CONCAT(fakturid,iprodid) from MKT.mr_sales2 where "
                    . " fakturid='$faktur' AND iprodid='$kdprod');";
            
            //timur 30
            $queryinstim = "insert into MKT.mr_sales2 (icabangid, areaid, icustid, ecustid, iprodid, distId, initial, fakturid, tgljual, ecabangid, divprodid, qty, hna, initialecabang) "
                    . " SELECT '0000000030' icabangid, '0000000001' areaid, '' as icustid, ecustid, iprodid, '$distibutor' distId, '$initial' initial, fakturid, tgljual, cabangid ecabangid,"
                    . " divprodid, qty, harga AS hna, cabangid initialecabang"
                    . " FROM dbmaster.tmp_sales_dist WHERE fakturid='$faktur' AND iprodid='$kdprod' "
                    . " AND CONCAT(fakturid,iprodid) not in (select CONCAT(fakturid,iprodid) from MKT.mr_sales2 where "
                    . " fakturid='$faktur' AND iprodid='$kdprod');";
            
            //join
            $queryinsjoin = "insert into MKT.mr_sales2 (icabangid, areaid, icustid, ecustid, iprodid, distId, initial, fakturid, tgljual, ecabangid, divprodid, qty, hna, initialecabang) "
                    . " SELECT e.icabangid,  e.areaid, e.icustid, s.ecustid, s.iprodid, '$distibutor' distId, '$initial' initial, s.fakturid, s.tgljual, s.cabangid ecabangid,"
                    . " s.divprodid, s.qty, s.harga AS hna, s.cabangid initialecabang "
                    . " FROM dbmaster.tmp_sales_dist s "
                    . " LEFT JOIN (select icabangid,  areaid, icustid, cabangid, ecustid from MKT.ecust where DistId='$distibutor') "
                    . " as e on  s.cabangid=e.cabangid and s.ecustid=e.ecustid "
                    . " WHERE s.fakturid='$faktur' AND s.iprodid='$kdprod' "
                    . " AND CONCAT(s.fakturid,s.iprodid) not in (select CONCAT(fakturid,iprodid) from MKT.mr_sales2 where "
                    . " fakturid='$faktur' AND iprodid='$kdprod');";
            
            $queryup = "update MKT.mr_sales2 set qty=$qty WHERE fakturid='$faktur' AND iprodid='$kdprod' AND "
                    . " icabangid='$icabanid' and ecustid='$ecustid' and distId='$distibutor'; $updatetmpsudah";
            $chkbox = "<input type='checkbox' id='chk$no' name='chk$no'>";
            echo "<td>$no</td>";
            echo "<td>$chkbox</td>";
            echo "<td>$linksel $linkins $linkinsTimur $linkinsJoin $linkup</td>";
            echo "<td>$selisih</td>";
            echo "<td>$qty</td>";
            echo "<td>$qtysdm</td>";
            echo "<td><a href='#' title='$divisi'>$kdprod</a></td>";
            echo "<td>$prod</td>";
            echo "<td></td>";
            echo "<td>$faktur</td>";
            echo "<td>$cabang</td>";
            echo "<td>$subdis</td>";
            echo "<td>$ecust</td>";
            echo "<td>$alamat</td>";
            echo "<td>";
            echo "<textarea id='sno$no' name='sno$no' rows='0.1px' cols='0.1px'>".$querysel."</textarea>"
                 . "<textarea id='ino$no' name='ino$no' rows='0.1px' cols='0.1px'>".$queryins."</textarea>"
                 . "<textarea id='uno$no' name='uno$no' rows='0.1px' cols='0.1px'>".$queryup."</textarea>"
                 . "<textarea id='inotim$no' name='inotim$no' rows='0.1px' cols='0.1px'>".$queryinstim."</textarea>"
                 . "<textarea id='inojoin$no' name='inojoin$no' rows='0.1px' cols='0.1px'>".$queryinsjoin."</textarea>";
            echo "</td>";
            echo "</tr>";
            $no++;
        }
    ?>
    </tbody>
</table>

<?PHP
    
hapusdata:
    $results1 = mysqli_query($cnit, "drop table $tmp0");
    $results1 = mysqli_query($cnit, "drop table $tmp1");
?>
<script>
function myCopyClip(text) {
    /* Get the text field */
    var copyText = document.getElementById(text);
    /* Select the text field */
    copyText.select();

    /* Copy the text inside the text field */
    document.execCommand("copy");

    /* Alert the copied text */
    alert("Copied the text: " + copyText.value);
}


</script>