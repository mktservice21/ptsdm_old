<script>

$(document).ready(function() {

                var groupColumn = 1;
                var groupColumn2 = 2;
                var table = $('#datatable').DataTable({
                    fixedHeader: true,
                    "ordering": false,
                    "lengthMenu": [[500, 1000, 1500, -1], [500, 1000, 1500, "All"]],
                    "displayLength": -1,
                    "columnDefs": [
                        { "contentPadding": "1" },
                        { "visible": false },
                        { className: "text-right", "targets": [2, 3, 4] },//right
                        { className: "text-nowrap", "targets": [0, 1] }//nowrap

                    ],
                    dom: 'Bfrtip',
                    buttons: [
                        'excel', 'print'
                    ],
                    bFilter: false, bInfo: false, "bLengthChange": false, "bLengthChange": false,
                    "bPaginate": false
                } );

                $('#enable').on( 'click', function () {
                    table.fixedHeader.enable();
                } );

                $('#disable').on( 'click', function () {
                    table.fixedHeader.disable();
                } );
 
                // Order by the grouping
                $('#datatable tbody').on( 'click', 'tr.group', function () {
                    var currentOrder = table.order()[0];
                    if ( currentOrder[0] === groupColumn && currentOrder[1] === 'asc' ) {
                        table.order( [ groupColumn, 'desc' ] ).draw();
                    }
                    else {
                        table.order( [ groupColumn, 'asc' ] ).draw();
                    }
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
</style>

<?php
    include "config/koneksimysqli_it.php";
    include "config/fungsi_combo.php";
    
    $rbtipe=$_POST['rb_rpttipe'];
    $karyawan=$_POST['e_idkaryawan'];

    if ($rbtipe=="P") {
        $karyawan=" and a.karyawanId='$_POST[e_idkaryawan]' ";
    }else $karyawan="";
    
    $lvlposisi="FF1";

    $now=date("mdYhis");
    $tmpbudgetreq1 =" dbtemp.DTBUDGETBRRELDAR01_$_SESSION[IDCARD]$now ";
    $tmpbudgetreq2 =" dbtemp.DTBUDGETBRRELDAR02_$_SESSION[IDCARD]$now ";


    $tgl01=$_POST['e_periode01'];
    $tgl02=$_POST['e_periode02'];

    $periode01= date("Y-m-d", strtotime($tgl01));
    $periode02= date("Y-m-d", strtotime($tgl02));


    $bln01= date("Ym", strtotime($tgl01));
    $bln02= date("Ym", strtotime($tgl02));


    $thnbln01= date("Y-m", strtotime($tgl01));
    $thnbln02= date("Y-m", strtotime($tgl02));


    $filterdivprod=('');
    if (!empty($_POST['chkbox_divisiprod'])){
        $filterdivprod=$_POST['chkbox_divisiprod'];
        $filterdivprod=PilCekBox($filterdivprod);
    }
    $filterdivprod=" and a.divprodid in $filterdivprod ";
    
    
    $filterkode=('');
    if (!empty($_POST['chkbox_kode'])){
        $filterkode=$_POST['chkbox_kode'];
        $filterkode=PilCekBox($filterkode);
    }
    $filterkode=" and a.kode in $filterkode ";
    
    
    
    $filtercab=('');
    if (!empty($_POST['chkbox_cabang'])){
        $filtercab=$_POST['chkbox_cabang'];
        $filtercab=PilCekBox($filtercab);
    }
    $pilcabkososng = (int)strpos($filtercab, "tanpa_cabang");
    if ( (int)$pilcabkososng>0 )
        $filtercab=" and (a.icabangid in $filtercab OR ifnull(a.icabangid,'')='')";
    else
        $filtercab=" and a.icabangid in $filtercab ";
    
    $filterdok="";
    if (!empty($_POST['e_iddokter'])) {
        $filterdok=" and a.dokterId='$_POST[e_iddokter]' ";
    }
    
    $fillamp="";
    if ($_POST['e_lampiran']=="Y")
        $fillamp=" and ifnull(a.lampiran,'')='Y' ";
    elseif ($_POST['e_lampiran']=="N")
        $fillamp=" and ifnull(a.lampiran,'') <> 'Y' ";
    
    $filca="";
    if ($_POST['e_ca']=="Y")
        $filca=" and ifnull(ca,'')='Y' ";
    elseif ($_POST['e_ca']=="N")
        $filca=" and ifnull(ca,'') <> 'Y' ";
    
    $filvia="";
    if ($_POST['e_via']=="Y")
        $filvia=" and ifnull(a.via,'')='Y' ";
    elseif ($_POST['e_via']=="N")
        $filvia=" and ifnull(a.via,'') <> 'Y' ";
   
    $kodeit = " and a.kode in (select kodeid from dbmaster.br_kode where (br <> '') and (br<>'N')) ";
    
    
    $query = "select a.brId, a.divprodid, a.kode, b.nama nama_kode, c.region, a.icabangid, c.nama nama_cabang, a.jumlah, a.jumlah1 from hrd.br0 a 
             JOIN hrd.br_kode b on a.kode=b.kodeid
             LEFT JOIN MKT.icabang c on a.icabangid=c.iCabangId
             WHERE a.retur <> 'Y' and a.batal <>'Y' $karyawan AND
             DATE_FORMAT(tgltrans,'%Y-%m-%d') between '$periode01' and '$periode02' $filterdivprod $filterkode $filtercab $filterdok $fillamp $filca $filvia";
    //echo $query; exit;
    $sql="create table $tmpbudgetreq1 ($query)";
    mysqli_query($cnit, $sql);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
   //FORMAT(realisasi1,2,'de_DE') as  
    $sql = "select a.brId, a.divprodid, a.region, a.icabangid, a.nama_cabang, a.kode, a.nama_kode, sum(a.jumlah) as jumlah, sum(a.jumlah1) as jumlah1 "
            . " from dbmaster.v_br0_all a where "
            . " DATE_FORMAT(a.tgltrans,'%Y-%m-%d') between '$periode01' and '$periode02' and a.retur<>'Y' "
            . " $karyawan $filterdivprod $filterkode $filtercab $filterdok"
            . " $fillamp $filca $filvia $kodeit";// and aktif_area='Y'
    $sql .= "group by a.brId, a.divprodid, a.region, a.icabangid, a.nama_cabang, a.kode, a.nama_kode";
    //$sql="create table $tmpbudgetreq1 ($sql)";
    //mysqli_query($cnit, $sql);
    //$erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    
    $sql="update $tmpbudgetreq1 set jumlah=jumlah1 where IFNULL(jumlah1,0)<>0";
    mysqli_query($cnit, $sql);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $sql="select distinct divprodid, region, icabangid, nama_cabang, "
            . "CAST(null AS DECIMAL(20,2)) as DCC, CAST(null AS DECIMAL(20,2)) as DSS, CAST(null AS DECIMAL(20,2)) as TOTAL "
            . " from $tmpbudgetreq1";
    $sql="create table $tmpbudgetreq2 ($sql)";
    mysqli_query($cnit, $sql);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $sql="update $tmpbudgetreq2 set DCC=(select sum(jumlah) from $tmpbudgetreq1 where "
            . " $tmpbudgetreq1.divprodid=$tmpbudgetreq2.divprodid and "
            . " $tmpbudgetreq1.icabangid=$tmpbudgetreq2.icabangid and "
            . " $tmpbudgetreq1.nama_kode like '%DCC%')";
    mysqli_query($cnit, $sql);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $sql="update $tmpbudgetreq2 set DSS=(select sum(jumlah) from $tmpbudgetreq1 where "
            . " $tmpbudgetreq1.divprodid=$tmpbudgetreq2.divprodid and "
            . " $tmpbudgetreq1.icabangid=$tmpbudgetreq2.icabangid and "
            . " $tmpbudgetreq1.nama_kode like '%DSS%')";
    mysqli_query($cnit, $sql);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    mysqli_query($cnit, "update $tmpbudgetreq2 set TOTAL=ifnull(DCC,0)+ifnull(DSS,0)");
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    mysqli_query($cnit, "update $tmpbudgetreq2 set region='N' WHERE IFNULL(region,'')=''");
    mysqli_query($cnit, "update $tmpbudgetreq2 set icabangid='N', nama_cabang='NONE' WHERE IFNULL(icabangid,'')=''");
    
    
    
    
    
    $printdate= date("d/m/Y");
    echo "<table width='100%' align='center' border='0' class='table table-striped table-bordered'>";
        echo "<tr>";
            echo "<td valign='top'>";
                echo "<table border='0' width='100%'>";
                echo "<tr><td><small style='color:blue;'>$_SESSION[NAMAPT]</small></td></tr>";
                if ($rbtipe=="P") { echo "<tr><td>Employee : <u>$_POST[e_karyawan]</u></td></tr>"; }
                echo "</table>";
            echo "</td>";
            echo "<td valign='top'>";
                echo "<table align='center' border='0' width='70%'>";
                echo "<tr><td align='left'><center><h1>Report Realisasi Budget Request Per Cabang</h1></center></td></tr>";
                echo "<tr><td align='left'><b>Periode $_POST[e_periode01] s/d. $_POST[e_periode02]<br/></td></tr>";
                echo "<tr><td align='right'><small><i>View Date : $printdate</i></small></td></tr>";
                echo "</table>";
            echo "</td>";
        echo "</tr>";
    echo "</table>";
    
    $mytabl=trim($tmpbudgetreq1);
    $sql="select TABLE_NAME from information_schema.TABLES where RTRIM(CONCAT('dbtemp.',TABLE_NAME))='$mytabl'";
    $adatable= mysqli_num_rows(mysqli_query($cnit, $sql));
    if ($adatable>0) {
        ?>
        <!--<table id='datatable' class='table nowrap table-striped table-bordered' width="100%">-->
        <table id='datatable' class='display  table table-striped table-bordered' style='width:100%'>
            <thead>
                <tr>
                    <th width='7px'>No</th>
                    <th>Nama Cabang</th>
                    <th>DCC</th>
                    <th>DSS</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?PHP
                $group0 = mysqli_query($cnit, "select distinct divprodid from $tmpbudgetreq2 order by divprodid");
                while ($g0=mysqli_fetch_array($group0)){
                    $divisi=$g0['divprodid'];
                    echo "<tr>";
                    echo "<td></td>";
                    echo "<td colspan='4'><b>$divisi</b></td>";
                    echo "<td class='divnone'></td>";
                    echo "<td class='divnone'></td>";
                    echo "<td class='divnone'></td>";
                    echo "</tr>";
                    
                    $group1 = mysqli_query($cnit, "select distinct divprodid, region from $tmpbudgetreq2 where divprodid='$g0[divprodid]' order by divprodid, region");
                    while ($g1=mysqli_fetch_array($group1)){
                        $region="Barat";
                        if ($g1['region']=="T") $region="Timur";
                        
                        echo "<tr>";
                        echo "<td></td>";
                        echo "<td colspan='4'><b>$region</b></td>";
                        echo "<td class='divnone'></td>";
                        echo "<td class='divnone'></td>";
                        echo "<td class='divnone'></td>";
                        echo "</tr>";
                        
                        $no=1;
                        $group2 = mysqli_query($cnit, "select * from $tmpbudgetreq2 where divprodid='$g0[divprodid]' and region='$g1[region]' order by divprodid, region, nama_cabang");
                        while ($g2=mysqli_fetch_array($group2)){
                            $jml1=number_format($g2['DCC'],0,",",",");
                            $jml2=number_format($g2['DSS'],0,",",",");
                            $total=number_format($g2['TOTAL'],0,",",",");
                            
                            echo "<tr>";
                            echo "<td>$no</td>";
                            echo "<td>$g2[nama_cabang]</td>";
                            echo "<td align='right'>$jml1</td>";
                            echo "<td align='right'>$jml2</td>";
                            echo "<td align='right'>$total</td>";
                            echo "</tr>";
                            $no++;
                        }
                        
                        //sub total region
                        $sgroup2 = mysqli_query($cnit, "select sum(DCC) as DCC, sum(DSS) as DSS, sum(TOTAL) as TOTAL from $tmpbudgetreq2 where divprodid='$g0[divprodid]' and region='$g1[region]'");
                        while ($s2=mysqli_fetch_array($sgroup2)){
                            $jml1=number_format($s2['DCC'],0,",",",");
                            $jml2=number_format($s2['DSS'],0,",",",");
                            $total=number_format($s2['TOTAL'],0,",",",");
                            
                            echo "<tr>";
                            echo "<td></td>";
                            echo "<td><b>Total $region $divisi : </b></td>";
                            echo "<td align='right'><b>$jml1</b></td>";
                            echo "<td align='right'><b>$jml2</b></td>";
                            echo "<td align='right'><b>$total</b></td>";
                            echo "</tr>";
                        }
                        
                    }
                    
                    //sub total divisi
                    $sgroup1 = mysqli_query($cnit, "select sum(DCC) as DCC, sum(DSS) as DSS, sum(TOTAL) as TOTAL from $tmpbudgetreq2 where divprodid='$g0[divprodid]'");
                    while ($s1=mysqli_fetch_array($sgroup1)){
                        $jml1=number_format($s1['DCC'],0,",",",");
                        $jml2=number_format($s1['DSS'],0,",",",");
                        $total=number_format($s1['TOTAL'],0,",",",");

                        echo "<tr>";
                        echo "<td></td>";
                        echo "<td><b>Total $divisi : </b></td>";
                        echo "<td align='right'><b>$jml1</b></td>";
                        echo "<td align='right'><b>$jml2</b></td>";
                        echo "<td align='right'><b>$total</b></td>";
                        echo "</tr>";
                    }
                    
                }
                
                //Grand TOTAL
                $sgroup0 = mysqli_query($cnit, "select sum(DCC) as DCC, sum(DSS) as DSS, sum(TOTAL) as TOTAL from $tmpbudgetreq2");
                while ($s0=mysqli_fetch_array($sgroup0)){
                    $jml1=number_format($s0['DCC'],0,",",",");
                    $jml2=number_format($s0['DSS'],0,",",",");
                    $total=number_format($s0['TOTAL'],0,",",",");

                    echo "<tr>";
                    echo "<td></td>";
                    echo "<td><b>Grand Total : </b></td>";
                    echo "<td align='right'><b>$jml1</b></td>";
                    echo "<td align='right'><b>$jml2</b></td>";
                    echo "<td align='right'><b>$total</b></td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
        <?php
    }
hapusdata:
    mysqli_query($cnit, "drop table $tmpbudgetreq1");
    mysqli_query($cnit, "drop table $tmpbudgetreq2");
?>
