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
                        { className: "text-right", "targets": [2, 3, 4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28] },//right
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

    $periode01= date("Y-m", strtotime($tgl01));
    $periode02= date("Y-m", strtotime($tgl02));


    $bln01= date("Ym", strtotime($tgl01));
    $bln02= date("Ym", strtotime($tgl02));
    
    $ptahun= date("Y", strtotime($tgl01));

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
    if (!empty($_POST['chkbox_cabangdaerah'])){
        $filtercab=$_POST['chkbox_cabangdaerah'];
        $filtercab=PilCekBox($filtercab);
    }
    $pilcabkososng = (int)strpos($filtercab, "tanpa_cabang");
    if ( (int)$pilcabkososng>0 )
        $filtercab=" and (a.idcabang in $filtercab OR ifnull(a.idcabang,'')='')";
    else
        $filtercab=" and a.idcabang in $filtercab ";
    
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
        $filca=" and ifnull(a.ca,'')='Y' ";
    elseif ($_POST['e_ca']=="N")
        $filca=" and ifnull(a.ca,'') <> 'Y' ";
    
    $filvia="";
    if ($_POST['e_via']=="Y")
        $filvia=" and ifnull(a.via,'')='Y' ";
    elseif ($_POST['e_via']=="N")
        $filvia=" and ifnull(a.via,'') <> 'Y' ";
   
    $kodeit = " and a.kode in (select kodeid from dbmaster.br_kode where (br <> '') and (br<>'N')) ";
    
    $query = "select DATE_FORMAT(a.tgltrans,'%Y-%m') as tgltrans, a.brId, a.divprodid, a.kode, 
            b.nama nama_kode, c.region, a.idcabang icabangid, c.nama nama_cabang, a.jumlah, a.jumlah1 from hrd.br0 a 
             JOIN hrd.br_kode b on a.kode=b.kodeid
             LEFT JOIN MKT.cbgytd c on a.idcabang=c.idcabang
             WHERE a.retur <> 'Y' and a.batal <>'Y' $karyawan AND
             DATE_FORMAT(a.tgltrans,'%Y-%m') between '$periode01' and '$periode02' $filterdivprod $filterkode $filtercab $filterdok $fillamp $filca $filvia";
    //echo $query; exit;
    $sql="create table $tmpbudgetreq1 ($query)";
    mysqli_query($cnit, $sql);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $sql="update $tmpbudgetreq1 set jumlah=jumlah1 where IFNULL(jumlah1,0)<>0";
    mysqli_query($cnit, $sql);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $sql="select distinct divprodid, region, icabangid, nama_cabang, "
            . "CAST(null AS DECIMAL(20,2)) as DCC, CAST(null AS DECIMAL(20,2)) as DSS, CAST(null AS DECIMAL(20,2)) as TOTAL "
            . " from $tmpbudgetreq1";
    $sql="create table $tmpbudgetreq2 ($sql)";
    mysqli_query($cnit, $sql);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $n_filed_add="";
    for($xi=1;$xi<=12;$xi++) {
        $n_filed_add .=" ADD COLUMN dcc_".$xi." DECIMAL(20,2),ADD COLUMN dss_".$xi." DECIMAL(20,2),";
    }
    $n_filed_add .=" ADD COLUMN vtotal_dcc DECIMAL(20,2), ADD COLUMN vtotal_dss DECIMAL(20,2)";
    
    $query = "ALTER TABLE $tmpbudgetreq2 $n_filed_add";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    for($xi=1;$xi<=12;$xi++) {
        $fbulan=$ptahun."-0".$xi;
        if ((double)$xi >=10) $fbulan=$ptahun."-".$xi;
        $n_filed_add = "dcc_".$xi;
        
        $sql="update $tmpbudgetreq2 set $n_filed_add=(select sum(jumlah) from $tmpbudgetreq1 where "
                . " $tmpbudgetreq1.divprodid=$tmpbudgetreq2.divprodid and "
                . " $tmpbudgetreq1.icabangid=$tmpbudgetreq2.icabangid and "
                . " $tmpbudgetreq1.nama_kode like '%DCC%' AND $tmpbudgetreq1.tgltrans='$fbulan')";
        mysqli_query($cnit, $sql);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $n_filed_add2 = "dss_".$xi;
        
        $sql="update $tmpbudgetreq2 set $n_filed_add2=(select sum(jumlah) from $tmpbudgetreq1 where "
                . " $tmpbudgetreq1.divprodid=$tmpbudgetreq2.divprodid and "
                . " $tmpbudgetreq1.icabangid=$tmpbudgetreq2.icabangid and "
                . " $tmpbudgetreq1.nama_kode like '%DSS%' AND $tmpbudgetreq1.tgltrans='$fbulan')";
        mysqli_query($cnit, $sql);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query="DELETE FROM $tmpbudgetreq1 WHERE tgltrans='$fbulan'";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
    
                    $query ="update DTBUDGETBRRELDAR02_000000056610282019043735 a JOIN (
                            select tgltrans, divprodid, icabangid, sum(jumlah) as jumlah from DTBUDGETBRRELDAR01_000000056610282019043735 WHERE 
                            nama_kode like '%DCC%' GROUP BY 1,2,3) b on a.tgltrans=b.tgltrans AND a.divprodid=b.divprodid AND a.icabangid=b.icabangid 
                            SET a.DCC=b.jumlah";
        
    }
    
    $query = "UPDATE $tmpbudgetreq2 SET vtotal_dcc=IFNULL(dcc_1,0)+IFNULL(dcc_2,0)+IFNULL(dcc_3,0)+IFNULL(dcc_4,0)+IFNULL(dcc_5,0)+IFNULL(dcc_6,0)+IFNULL(dcc_7,0)+IFNULL(dcc_8,0)+IFNULL(dcc_9,0)+IFNULL(dcc_10,0)+IFNULL(dcc_11,0)+IFNULL(dcc_12,0),"
            . " vtotal_dss=IFNULL(dss_1,0)+IFNULL(dss_2,0)+IFNULL(dss_3,0)+IFNULL(dss_4,0)+IFNULL(dss_5,0)+IFNULL(dss_6,0)+IFNULL(dss_7,0)+IFNULL(dss_8,0)+IFNULL(dss_9,0)+IFNULL(dss_10,0)+IFNULL(dss_11,0)+IFNULL(dss_12,0)";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query="UPDATE $tmpbudgetreq2 SET TOTAL=IFNULL(vtotal_dcc,0)+IFNULL(vtotal_dss,0), DCC=vtotal_dcc, DSS=vtotal_dss";
    mysqli_query($cnit, $query);
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
                echo "<tr><td align='left'><center><h1>Report Realisasi Budget Request Per Daerah Per Bulan</h1></center></td></tr>";
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
                    <th>Nama Daerah</th>
                    <?PHP
                    for($xi=1;$xi<=12;$xi++) {
                        $fbulan=$ptahun."-0".$xi;
                        if ((double)$xi >=10) $fbulan=$ptahun."-".$xi;
                        $fbulan .="-01";
                        $nmbulan= date("F", strtotime($fbulan));
                        echo "<th>DCC $nmbulan</th>";
                        echo "<th>DSS $nmbulan</th>";
                    }
                    ?>
                    <th>Total DCC</th>
                    <th>Total DSS</th>
                    <th>Total</th>
                </tr>
                
                
            </thead>
            <tbody>
                <?PHP
                
                $pgrand_dcc[1]=0;$pgrand_dcc[2]=0;$pgrand_dcc[3]=0;$pgrand_dcc[4]=0;$pgrand_dcc[5]=0;$pgrand_dcc[6]=0;
                $pgrand_dcc[7]=0;$pgrand_dcc[8]=0;$pgrand_dcc[9]=0;$pgrand_dcc[10]=0;$pgrand_dcc[11]=0;$pgrand_dcc[12]=0;

                $pgrand_dss[1]=0;$pgrand_dss[2]=0;$pgrand_dss[3]=0;$pgrand_dss[4]=0;$pgrand_dss[5]=0;$pgrand_dss[6]=0;
                $pgrand_dss[7]=0;$pgrand_dss[8]=0;$pgrand_dss[9]=0;$pgrand_dss[10]=0;$pgrand_dss[11]=0;$pgrand_dss[12]=0;
                
                $pgrand_totdcc=0;
                $pgrand_totdss=0;
                $pgrand_total=0;
                
                $group0 = mysqli_query($cnit, "select distinct divprodid from $tmpbudgetreq2 order by divprodid");
                while ($g0=mysqli_fetch_array($group0)){
                    $divisi=$g0['divprodid'];
                    echo "<tr>";
                    echo "<td></td>";
                    echo "<td colspan='28'><b>$divisi</b></td>";
                    for($xi=1;$xi<=12;$xi++) {
                        echo "<td class='divnone'></td>";
                        echo "<td class='divnone'></td>";
                    }
                    echo "<td class='divnone'></td>";
                    echo "<td class='divnone'></td>";
                    echo "<td class='divnone'></td>";
                    echo "</tr>";
                    
                    $psubdiv_dcc[1]=0;$psubdiv_dcc[2]=0;$psubdiv_dcc[3]=0;$psubdiv_dcc[4]=0;$psubdiv_dcc[5]=0;$psubdiv_dcc[6]=0;
                    $psubdiv_dcc[7]=0;$psubdiv_dcc[8]=0;$psubdiv_dcc[9]=0;$psubdiv_dcc[10]=0;$psubdiv_dcc[11]=0;$psubdiv_dcc[12]=0;

                    $psubdiv_dss[1]=0;$psubdiv_dss[2]=0;$psubdiv_dss[3]=0;$psubdiv_dss[4]=0;$psubdiv_dss[5]=0;$psubdiv_dss[6]=0;
                    $psubdiv_dss[7]=0;$psubdiv_dss[8]=0;$psubdiv_dss[9]=0;$psubdiv_dss[10]=0;$psubdiv_dss[11]=0;$psubdiv_dss[12]=0;
                
                    
                    $psubdiv_totdcc=0;
                    $psubdiv_totdss=0;
                    $psubdiv_total=0;
                        
                    $group1 = mysqli_query($cnit, "select distinct divprodid, region from $tmpbudgetreq2 where divprodid='$g0[divprodid]' order by divprodid, region");
                    while ($g1=mysqli_fetch_array($group1)){
                        $region="Barat";
                        if ($g1['region']=="T") $region="Timur";
                        if ($g1['region']=="N") $region="None";
                        
                        echo "<tr>";
                        echo "<td></td>";
                        echo "<td colspan='28'><b>$region</b></td>";
                        for($xi=1;$xi<=12;$xi++) {
                            echo "<td class='divnone'></td>";
                            echo "<td class='divnone'></td>";    
                        }
                        echo "<td class='divnone'></td>";
                        echo "<td class='divnone'></td>";
                        echo "<td class='divnone'></td>";
                        echo "</tr>";
                        
                        
                        $psub_dcc[1]=0;$psub_dcc[2]=0;$psub_dcc[3]=0;$psub_dcc[4]=0;$psub_dcc[5]=0;$psub_dcc[6]=0;
                        $psub_dcc[7]=0;$psub_dcc[8]=0;$psub_dcc[9]=0;$psub_dcc[10]=0;$psub_dcc[11]=0;$psub_dcc[12]=0;

                        $psub_dss[1]=0;$psub_dss[2]=0;$psub_dss[3]=0;$psub_dss[4]=0;$psub_dss[5]=0;$psub_dss[6]=0;
                        $psub_dss[7]=0;$psub_dss[8]=0;$psub_dss[9]=0;$psub_dss[10]=0;$psub_dss[11]=0;$psub_dss[12]=0;
                                                
                        $psub_totdcc=0;
                        $psub_totdss=0;
                        $psub_total=0;
                        
                        $no=1;
                        $group2 = mysqli_query($cnit, "select * from $tmpbudgetreq2 where divprodid='$g0[divprodid]' and region='$g1[region]' order by divprodid, region, nama_cabang");
                        while ($g2=mysqli_fetch_array($group2)){
                            $jml1=$g2['DCC'];
                            $jml2=$g2['DSS'];
                            $total=$g2['TOTAL'];
                            
                            $psub_totdcc=(double)$psub_totdcc+(double)$jml1;
                            $psub_totdss=(double)$psub_totdss+(double)$jml2;
                            $psub_total=(double)$psub_total+(double)$total;
                            
                            $jml1=number_format($jml1,0,",",",");
                            $jml2=number_format($jml2,0,",",",");
                            $total=number_format($total,0,",",",");
                            
                            if ($jml1=="0") $jml1="";
                            if ($jml2=="0") $jml2="";
                            if ($total=="0") $total="";
                            
                            echo "<tr>";
                            echo "<td>$no</td>";
                            echo "<td nowrap>$g2[nama_cabang]</td>";
                            $nno_xi=1;
                            for($xi=1;$xi<=12;$xi++) {
                                $fbulan=$ptahun."-0".$xi;
                                if ((double)$xi >=10) $fbulan=$ptahun."-".$xi;
                                $pblndcc = "dcc_".$xi;
                                $pblndss = "dss_".$xi;
                                
                                $pjmldcc=$g2[$pblndcc];
                                $pjmldss=$g2[$pblndss];
                                if (empty($pjmldcc)) $pjmldcc=0;
                                if (empty($pjmldss)) $pjmldss=0;
                                
                                $psub_dcc[$nno_xi]=(double)$psub_dcc[$nno_xi]+(double)$pjmldcc;
                                $psub_dss[$nno_xi]=(double)$psub_dss[$nno_xi]+(double)$pjmldss;
                                $nno_xi++;
                                
                                $pjmldcc=number_format($pjmldcc,0,",",",");
                                $pjmldss=number_format($pjmldss,0,",",",");
                                
                                if ($pjmldcc=="0") $pjmldcc="";
                                if ($pjmldss=="0") $pjmldss="";
                                
                                echo "<td align='right'>$pjmldcc</td>";
                                echo "<td align='right'>$pjmldss</td>";    
                            }
                            echo "<td align='right'>$jml1</td>";
                            echo "<td align='right'>$jml2</td>";
                            echo "<td align='right'>$total</td>";
                            echo "</tr>";
                            $no++;
                        }
                        
                        echo "<tr>";
                        echo "<td></td>";
                        echo "<td nowrap><b>Total $region $divisi : </b></td>";
                        for($xi=1;$xi<=12;$xi++) {
                            if (empty($psub_dcc[$xi])) $psub_dcc[$xi]=0;
                            if (empty($psub_dss[$xi])) $psub_dss[$xi]=0;
                            
                            $psubdiv_dcc[$xi]=(double)$psubdiv_dcc[$xi]+(double)$psub_dcc[$xi];
                            $psubdiv_dss[$xi]=(double)$psubdiv_dss[$xi]+(double)$psub_dss[$xi];
                            
                            $psub_dcc[$xi]=number_format($psub_dcc[$xi],0,",",",");
                            $psub_dss[$xi]=number_format($psub_dss[$xi],0,",",",");
                            
                            if ($psub_dcc[$xi]=="0") $psub_dcc[$xi]="";
                            if ($psub_dss[$xi]=="0") $psub_dss[$xi]="";
                            
                            echo "<td align='right'><b>$psub_dcc[$xi]</b></td>";
                            echo "<td align='right'><b>$psub_dss[$xi]</b></td>";
                        }
                        
                        $psubdiv_totdcc=(double)$psubdiv_totdcc+(double)$psub_totdcc;
                        $psubdiv_totdss=(double)$psubdiv_totdss+(double)$psub_totdss;
                        $psubdiv_total=(double)$psubdiv_total+(double)$psub_total;
                        
                        $psub_totdcc=number_format($psub_totdcc,0,",",",");
                        $psub_totdss=number_format($psub_totdss,0,",",",");
                        $psub_total=number_format($psub_total,0,",",",");
                        
                        if ($psub_totdcc=="0") $psub_totdcc="";
                        if ($psub_totdss=="0") $psub_totdss="";
                        if ($psub_total=="0") $psub_total="";
                        
                        echo "<td align='right'><b>$psub_totdcc</b></td>";
                        echo "<td align='right'><b>$psub_totdss</b></td>";
                        echo "<td align='right'><b>$psub_total</b></td>";
                        echo "</tr>";
                        
                    }
                    
                    echo "<tr>";
                    echo "<td></td>";
                    echo "<td nowrap><b>Total $divisi : </b></td>";
                    for($xi=1;$xi<=12;$xi++) {
                        if (empty($psubdiv_dcc[$xi])) $psubdiv_dcc[$xi]=0;
                        if (empty($psubdiv_dss[$xi])) $psubdiv_dss[$xi]=0;

                        $pgrand_dcc[$xi]=(double)$pgrand_dcc[$xi]+(double)$psubdiv_dcc[$xi];
                        $pgrand_dss[$xi]=(double)$pgrand_dss[$xi]+(double)$psubdiv_dss[$xi];
                        
                        $psubdiv_dcc[$xi]=number_format($psubdiv_dcc[$xi],0,",",",");
                        $psubdiv_dss[$xi]=number_format($psubdiv_dss[$xi],0,",",",");

                        if ($psubdiv_dcc[$xi]=="0") $psubdiv_dcc[$xi]="";
                        if ($psubdiv_dss[$xi]=="0") $psubdiv_dss[$xi]="";
                        
                        echo "<td align='right'><b>$psubdiv_dcc[$xi]</b></td>";
                        echo "<td align='right'><b>$psubdiv_dss[$xi]</b></td>";
                    }
                        
                    $pgrand_totdcc=(double)$pgrand_totdcc+(double)$psubdiv_totdcc;
                    $pgrand_totdss=(double)$pgrand_totdss+(double)$psubdiv_totdss;
                    $pgrand_total=(double)$pgrand_total+(double)$psubdiv_total;
                    
                    $psubdiv_totdcc=number_format($psubdiv_totdcc,0,",",",");
                    $psubdiv_totdss=number_format($psubdiv_totdss,0,",",",");
                    $psubdiv_total=number_format($psubdiv_total,0,",",",");
                    
                    if ($psubdiv_totdcc=="0") $psubdiv_totdcc="";
                    if ($psubdiv_totdss=="0") $psubdiv_totdss="";
                    if ($psubdiv_total=="0") $psubdiv_total="";
                    
                    echo "<td align='right'><b>$psubdiv_totdcc</b></td>";
                    echo "<td align='right'><b>$psubdiv_totdss</b></td>";
                    echo "<td align='right'><b>$psubdiv_total</b></td>";
                    echo "</tr>";
                }
                
                echo "<tr>";
                echo "<td colspan='29'>&nbsp;</td>";
                for($xi=1;$xi<=29;$xi++) {
                    echo "<td class='divnone'></td>";
                }
                echo "</tr>";
                
                echo "<tr>";
                echo "<td></td>";
                echo "<td><b>Grand Total : </b></td>";
                
                for($xi=1;$xi<=12;$xi++) {
                    if (empty($pgrand_dcc[$xi])) $pgrand_dcc[$xi]=0;
                    if (empty($pgrand_dss[$xi])) $pgrand_dss[$xi]=0;

                    $pgrand_dcc[$xi]=number_format($pgrand_dcc[$xi],0,",",",");
                    $pgrand_dss[$xi]=number_format($pgrand_dss[$xi],0,",",",");

                    if ($pgrand_dcc[$xi]=="0") $pgrand_dcc[$xi]="";
                    if ($pgrand_dss[$xi]=="0") $pgrand_dss[$xi]="";
                    
                    echo "<td align='right'><b>$pgrand_dcc[$xi]</b></td>";
                    echo "<td align='right'><b>$pgrand_dss[$xi]</b></td>";
                }
                    
                $pgrand_totdcc=number_format($pgrand_totdcc,0,",",",");
                $pgrand_totdss=number_format($pgrand_totdss,0,",",",");
                $pgrand_total=number_format($pgrand_total,0,",",",");
                    
                if ($pgrand_totdcc=="0") $pgrand_totdcc="";
                if ($pgrand_totdss=="0") $pgrand_totdss="";
                if ($pgrand_total=="0") $pgrand_total="";
                
                echo "<td align='right'><b>$pgrand_totdcc</b></td>";
                echo "<td align='right'><b>$pgrand_totdss</b></td>";
                echo "<td align='right'><b>$pgrand_total</b></td>";
                echo "</tr>";
                ?>
            </tbody>
        </table>
        <?php
    }
hapusdata:
    mysqli_query($cnit, "drop table $tmpbudgetreq1");
    mysqli_query($cnit, "drop table $tmpbudgetreq2");
?>
