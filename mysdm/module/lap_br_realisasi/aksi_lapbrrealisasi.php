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
                        { className: "text-right", "targets": [4, 8, 9] },//right
                        { className: "text-nowrap", "targets": [0, 1, 2, 3] }//nowrap

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
        $karyawan=" and karyawanId='$_POST[e_idkaryawan]' ";
    }else $karyawan="";
    
    $lvlposisi="FF1";

    $now=date("mdYhis");
    $tmpbudgetreq =" dbtemp.DTBUDGETBR01_$_SESSION[IDCARD]$now ";


    $tgl01=$_POST['e_periode01'];
    $tgl02=$_POST['e_periode01'];

    $periode01= $tgl01;//date("Y-m-d", strtotime($tgl01));
    $periode02= $tgl02;//date("Y-m-d", strtotime($tgl02));


    $bln01= date("Ym", strtotime($tgl01));
    $bln02= date("Ym", strtotime($tgl02));


    $thnbln01= date("Y-m", strtotime($tgl01));
    $thnbln02= date("Y-m", strtotime($tgl02));


    $filterdivprod=('');
    if (!empty($_POST['chkbox_divisiprod'])){
        $filterdivprod=$_POST['chkbox_divisiprod'];
        $filterdivprod=PilCekBox($filterdivprod);
    }
    $filterdivprod=" and divprodid in $filterdivprod ";
    
    
    $filterkode=('');
    if (!empty($_POST['chkbox_kode'])){
        $filterkode=$_POST['chkbox_kode'];
        $filterkode=PilCekBox($filterkode);
    }
    $filterkode=" and kode in $filterkode ";
    
    
    
    $filtercab=('');
    if (!empty($_POST['chkbox_cabang'])){
        $filtercab=$_POST['chkbox_cabang'];
        $filtercab=PilCekBox($filtercab);
    }
    $pilcabkososng = (int)strpos($filtercab, "tanpa_cabang");
    if ( (int)$pilcabkososng>0 )
        $filtercab=" and (icabangid in $filtercab OR ifnull(icabangid,'')='')";
    else
        $filtercab=" and icabangid in $filtercab ";
    

    
    $filterdok="";
    if (!empty($_POST['e_iddokter'])) {
        $filterdok=" and dokterId='$_POST[e_iddokter]' ";
    }
    
    $fillamp="";
    if ($_POST['e_lampiran']=="Y")
        $fillamp=" and ifnull(lampiran,'')='Y' ";
    elseif ($_POST['e_lampiran']=="N")
        $fillamp=" and ifnull(lampiran,'') <> 'Y' ";
    
    $filca="";
    if ($_POST['e_ca']=="Y")
        $filca=" and ifnull(ca,'')='Y' ";
    elseif ($_POST['e_ca']=="N")
        $filca=" and ifnull(ca,'') <> 'Y' ";
    
    $filvia="";
    if ($_POST['e_via']=="Y")
        $filvia=" and ifnull(via,'')='Y' ";
    elseif ($_POST['e_via']=="N")
        $filvia=" and ifnull(via,'') <> 'Y' ";
   
    
   //FORMAT(realisasi1,2,'de_DE') as  
    $sql = "select brId, karyawanId, nama, tgltrans, "
            . " aktivitas1, dokterId, nama_dokter, jumlah, "
            . " realisasi1, noslip, tgltrm, jumlah1 as jmlrealisasi "
            . " from dbmaster.v_br0_all where "//aktif_area='Y' and 
            . " DATE_FORMAT(tgltrans,'%Y') between '$periode01' and '$periode02' $karyawan $filterdivprod $filterkode $filtercab $filterdok"
            . " $fillamp $filca $filvia";//
    //echo $sql; exit;
    $sql="create table $tmpbudgetreq ($sql)";
    
    mysqli_query($cnit, $sql);
    
    $printdate= date("d/m/Y");
    echo "<table width='90%' align='center' border='0' class='table table-striped table-bordered'>";
        echo "<tr>";
            echo "<td valign='top'>";
                echo "<table border='0' width='100%'>";
                echo "<tr><td><small style='color:blue;'>$_SESSION[NAMAPT]</small></td></tr>";
                if ($rbtipe=="P") { echo "<tr><td>Employee : <u>$_POST[e_karyawan]</u></td></tr>"; }
                echo "</table>";
            echo "</td>";
            echo "<td valign='top'>";
                echo "<table align='center' border='0' width='100%'>";
                echo "<tr><td align='left'><h2>Report Realisasi Budget Request</h2></td></tr>";
                echo "<tr><td align='left'><b>Periode $_POST[e_periode01]<br/></td></tr>";
                echo "<tr><td align='right'><small><i>View Date : $printdate</i></small></td></tr>";
                echo "</table>";
            echo "</td>";
        echo "</tr>";
    echo "</table>";
    
    $mytabl=trim($tmpbudgetreq);
    
    $sql="select TABLE_NAME from information_schema.TABLES where RTRIM(CONCAT('dbtemp.',TABLE_NAME))='$mytabl'";
    $adatable= mysqli_num_rows(mysqli_query($cnit, $sql));
    if ($adatable>0) {
        ?>
        <!--<table id='datatable' class='table nowrap table-striped table-bordered' width="100%">-->
        <table id='datatable' class='display  table table-striped table-bordered' style='width:100%'>
            <thead>
                <tr>
                    <th width='7px'>No</th>
                    <th>Nama Pembuat</th>
                    <th>Tgl. Transfer</th>
                    <th>Keterangan</th>
                    <th>Nama Dokter</th>
                    <th>Jumlah IDR</th>
                    <th>Jumlah USD</th>
                    <th>Nama Realisasi</th>
                    <th>No Slip</th>
                    <th>Tgl. Terima</th>
                    <th>Jumlah Realisasi IDR</th>
                    <th>Jumlah Realisasi USD</th>
                    <th>Lain-Lain</th>
                </tr>
            </thead>
            <tbody>
                <?PHP
                $totalreal=0;
                $group0 = mysqli_query($cnit, "select distinct DATE_FORMAT(tgltrans,'%Y%m') as tglinput1, "
                        . " DATE_FORMAT(tgltrans,'%M %Y') as tglinput2 from $tmpbudgetreq order by DATE_FORMAT(tgltrans,'%Y%m')");
                while ($g0=mysqli_fetch_array($group0)){
                    echo "<tr>";
                    echo "<td></td>";
                    echo "<td colspan='12'><b>$g0[tglinput2]</b></td>";
                    echo "<td class='divnone'></td>";
                    echo "<td class='divnone'></td>";
                    echo "<td class='divnone'></td>";
                    echo "<td class='divnone'></td>";
                    echo "<td class='divnone'></td>";
                    echo "<td class='divnone'></td>";
                    echo "<td class='divnone'></td>";
                    echo "<td class='divnone'></td>";
                    echo "<td class='divnone'></td>";
                    echo "<td class='divnone'></td>";
                    echo "<td class='divnone'></td>";
                    echo "</tr>";
                    
                    $subtotreal=0;
                    $no=1;
                    $group1 = mysqli_query($cnit, "select * from $tmpbudgetreq where DATE_FORMAT(tgltrans,'%Y%m')='$g0[tglinput1]' order by tgltrans, nama");
                    $ketemu=  mysqli_num_rows($group1);
                    while ($g1=mysqli_fetch_array($group1)){
                        $jml=number_format($g1['jumlah'],0,",",",");
                        $jmlrealisasi=number_format($g1['jmlrealisasi'],0,",",",");
                        
                        $tgltrans="";
                        if (!empty($g1['tgltrans']) AND $g1['tgltrans']<>"0000-00-00")
                            $tgltrans=date("d F Y", strtotime($g1['tgltrans']));
                        
                        $tgltrm="";
                        if (!empty($g1['tgltrm']) AND $g1['tgltrm']<>"0000-00-00")
                            $tgltrm=date("d F Y", strtotime($g1['tgltrm']));
                        
                        echo "<tr scope='row'><td>$no</td>";
                        echo "<td>$g1[nama]</td>";
                        echo "<td>$tgltrans</td>";
                        echo "<td>$g1[aktivitas1]</td>";

                        if (!empty($g1['dokterId']) AND (int)$g1['dokterId']<>0)
                            echo "<td><small>(".(int)$g1['dokterId'].") </small>$g1[nama_dokter]</td>";
                        else
                            echo "<td></td>";

                        echo "<td align='right'>$jml</td>";
                        echo "<td align='right'></td>";
                        echo "<td>$g1[realisasi1]</td>";
                        echo "<td>$g1[noslip]</td>";
                        echo "<td>$tgltrm</td>";
                        
                        if ($tgltrm=="")
                            echo "<td></td>";
                        else {
                            echo "<td align='right'>$jmlrealisasi</td>";
                            if (!empty($g1['jmlrealisasi']))
                                $subtotreal =floatval($subtotreal)+floatval($g1['jmlrealisasi']);
                        }
                        
                        
                        echo "<td align='right'></td>";
                        echo "<td></td>";


                        echo "</tr>";
                        $no++;
                    }
                    
                    //sub total
                    $sub0 = mysqli_query($cnit, "select DATE_FORMAT(tgltrans,'%Y%m') as tglinput1, DATE_FORMAT(tgltrans,'%M %Y') astglinput2, "
                            . " sum(jumlah) as jumlah, "
                            . " sum(jmlrealisasi) as jmlrealisasi "
                            . " from $tmpbudgetreq where "
                            . " DATE_FORMAT(tgltrans,'%Y%m')='$g0[tglinput1]' group by DATE_FORMAT(tgltrans,'%Y%m'), DATE_FORMAT(tgltrans,'%M %Y')");
                    while ($s0=mysqli_fetch_array($sub0)){
                        $jml=number_format($s0['jumlah'],0,",",",");
                        //$jmlrealisasi=number_format($s0['jmlrealisasi'],0,",",",");
                        $jmlrealisasi=number_format($subtotreal,0,",",",");
                        
                        echo "<tr>";
                        echo "<td></td>";
                        echo "<td colspan=4><b>Total $g0[tglinput2] : </b></td>";
                        echo "<td class='divnone'></td>";
                        echo "<td class='divnone'></td>";
                        echo "<td class='divnone'></td>";
                        echo "<td align='right'><b>$jml</b></td>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "<td align='right'><b>$jmlrealisasi</b></td>";
                        echo "<td></td>";
                        echo "<td></td>";
                        
                        echo "</tr>";
                    }
                    
                    $totalreal =floatval($totalreal)+floatval($subtotreal);
                    $subtotreal=0;
                    
                }
                // total
                $sub1 = mysqli_query($cnit, "select sum(jumlah) as jumlah, "
                        . " sum(jmlrealisasi) as jmlrealisasi "
                        . " from $tmpbudgetreq");
                while ($s1=mysqli_fetch_array($sub1)){
                    $jml=number_format($s1['jumlah'],0,",",",");
                    //$jmltotal=number_format($s1['jmlrealisasi'],0,",",",");
                    $jmltotal=number_format($totalreal,0,",",",");
                    
                    echo "<tr>";
                    echo "<td></td>";
                    echo "<td colspan=4><b>Grand Total : </b></td>";
                    echo "<td class='divnone'></td>";
                    echo "<td class='divnone'></td>";
                    echo "<td class='divnone'></td>";
                    echo "<td align='right'><b>$jml</b></td>";
                    echo "<td></td>";
                    echo "<td></td>";
                    echo "<td></td>";
                    echo "<td></td>";
                    echo "<td align='right'><b>$jmltotal</b></td>";
                    echo "<td></td>";
                    echo "<td></td>";

                    echo "</tr>";
                }
                $totalreal=0;
                ?>
            </tbody>
        </table>
        <?php
    }
    mysqli_query($cnit, "drop table $tmpbudgetreq");
?>
