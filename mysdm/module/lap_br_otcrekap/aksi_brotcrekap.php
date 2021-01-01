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
                    { className: "text-right", "targets": [6, 9] },//right
                    { className: "text-nowrap", "targets": [0, 1, 2, 3, 4, 5, 6, 7, 8, 9] }//nowrap
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
    

    $now=date("mdYhis");
    $tmpbudgetreq =" dbtemp.DTBUDGETBROTCREKAP01_$_SESSION[IDCARD]$now ";


    $tgl01=$_POST['e_periode01'];
    $tgl02=$_POST['e_periode02'];

    $periode01= date("Y-m-d", strtotime($tgl01));
    $periode02= date("Y-m-d", strtotime($tgl02));


    $bln01= date("Ym", strtotime($tgl01));
    $bln02= date("Ym", strtotime($tgl02));


    $thnbln01= date("Y-m", strtotime($tgl01));
    $thnbln02= date("Y-m", strtotime($tgl02));

    
    
    $filterkode=('');
    if (!empty($_POST['chkbox_kodeotc'])){
        $filterkode=$_POST['chkbox_kodeotc'];
        $filterkode=PilCekBox($filterkode);
    }
    $filterkode=" and kodeid in $filterkode ";
    
    
    
    $filtercab=('');
    if (!empty($_POST['chkbox_cabango'])){
        $filtercab=$_POST['chkbox_cabango'];
        $filtercab=PilCekBox($filtercab);
    }
    $filtercab=" and icabangid_o in $filtercab ";
    

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
    
    $filbral="";
    if (!empty($_POST['cb_alokasi'])) {
        $filbral=" and bralid='$_POST[cb_alokasi]' ";
    }
    
    $filtypdate="tgltrans";
    if ($_POST['cb_tgltipe']=="1")
        $filtypdate="MODIFDATE";
    elseif ($_POST['cb_tgltipe']=="2")
        $filtypdate="tgltrans";
    elseif ($_POST['cb_tgltipe']=="3")
        $filtypdate="tglbr";
    
    
    $printdate= date("d/m/Y");
    echo "<table width='90%' align='center' border='0' class='table table-striped table-bordered'>";
        echo "<tr>";
            echo "<td valign='top'>";
                echo "<table border='0' width='100%'>";
                echo "<tr><td><small style='color:blue;'>$_SESSION[NAMAPT]</small></td></tr>";
                echo "</table>";
            echo "</td>";
            echo "<td valign='top'>";
                echo "<table align='center' border='0' width='100%'>";
                echo "<tr><td align='left'><h2>Rekap Transfer BR OTC</h2></td></tr>";
                echo "<tr><td align='left'><b>Periode $_POST[e_periode01] s/d. $_POST[e_periode02]<br/></td></tr>";
                echo "<tr><td align='right'><small><i>View Date : $printdate</i></small></td></tr>";
                echo "</table>";
            echo "</td>";
        echo "</tr>";
    echo "</table>";
    
   
    
        $sql = "select tgltrans as thnbln, DATE_FORMAT(tgltrans,'%d %M %Y') tgl, icabangid_o, nama_cabang, kodeid, nama_kode, noslip, "
                . " keterangan1, keterangan2, sum(jumlah) as jumlah, real1, DATE_FORMAT(tglreal,'%d %M %Y') tglreal, sum(realisasi) as realisasi "
                . " from dbmaster.v_br_otc_all WHERE DATE_FORMAT($filtypdate,'%Y-%m-%d') between '$periode01' and '$periode02'"
                . " $filterkode $filtercab"
                . " $fillamp $filca $filvia $filbral";
        $sql .= " group by tgltrans, DATE_FORMAT(tgltrans,'%d %M %Y'), icabangid_o, nama_cabang, kodeid, nama_kode, noslip, keterangan1, keterangan2, real1, tglreal";
        $sql="create table $tmpbudgetreq ($sql)";
        mysqli_query($cnit, $sql);
        
        ?>

            <table id='datatable' class='display  table table-striped table-bordered' style='width:100%'>
                <thead>
                    <tr>
                        <th width='7px'>No</th>
                        <th>Cabang</th><th width='80px'>Alokasi Budget</th><th width='50px'>Noslip</th>
                        <th>Keterangan Tempat</th><th>Keterangan</th>
                        <th width='80px'>Jumlah IDR</th><th width='80px'>Nama Realisasi</th>
                        <th>Tgl. Terima</th><th>Jumlah Realisasi</th>
                    </tr>
                </thead>
                <tbody>
                    <?PHP
                    $group1 = mysqli_query($cnit, "select distinct thnbln, tgl from $tmpbudgetreq order by thnbln");
                    $ketemu=  mysqli_num_rows($group1);
                    while ($g1=mysqli_fetch_array($group1)){
                        echo "<tr>";
                        echo "<td></td>";
                        echo "<td width='200px'><b>$g1[tgl]</b></td>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "</tr>";
                        $no=1;
                        $group2 = mysqli_query($cnit, "select * from $tmpbudgetreq where thnbln='$g1[thnbln]' order by nama_cabang");
                        $ketemu2=  mysqli_num_rows($group2);
                        while ($g2=mysqli_fetch_array($group2)){
                            $jumlah="";
                            $realisasi="";
                            if (!empty($g2['jumlah'])) $jumlah=number_format($g2['jumlah'],0,",",",");
                            if (!empty($g2['realisasi'])) $realisasi=number_format($g2['realisasi'],0,",",",");
                            
                            echo "<tr>";
                            echo "<td>$no</td>";
                            echo "<td>$g2[nama_cabang]</td>";
                            echo "<td>$g2[nama_kode]</td>";
                            echo "<td>$g2[noslip]</td>";
                            echo "<td>$g2[keterangan1]</td>";
                            echo "<td>$g2[keterangan2]</td>";
                            echo "<td align='right'>$jumlah</td>";
                            echo "<td>$g2[real1]</td>";
                            echo "<td width='100px'>$g2[tglreal]</td>";
                            echo "<td align='right'>$realisasi</td>";
                            echo "</tr>";
                            
                            $no++;
                        }
                        
                        $sum1 = mysqli_query($cnit, "select sum(jumlah) as jumlah, sum(realisasi) as realisasi from $tmpbudgetreq where thnbln='$g1[thnbln]'");
                        $ketemusum1=  mysqli_num_rows($sum1);
                        while ($s1=mysqli_fetch_array($sum1)){
                            $jumlah="";
                            $realisasi="";
                            if (!empty($s1['jumlah'])) $jumlah=number_format($s1['jumlah'],0,",",",");
                            if (!empty($s1['realisasi'])) $realisasi=number_format($s1['realisasi'],0,",",",");
                            
                            echo "<tr>";
                            echo "<td></td>";
                            echo "<td><b>Total $g1[tgl] : </b></td>";
                            echo "<td></td>";
                            echo "<td></td>";
                            echo "<td></td>";
                            echo "<td></td>";
                            echo "<td align='right'><b>$jumlah</b></td>";
                            echo "<td></td>";
                            echo "<td></td>";
                            echo "<td align='right'><b>$realisasi</b></td>";
                            echo "</tr>";
                            
                        }
                    }
                    $sum2 = mysqli_query($cnit, "select sum(jumlah) as jumlah, sum(realisasi) as realisasi from $tmpbudgetreq");
                    $ketemusum2=  mysqli_num_rows($sum2);
                    while ($s2=mysqli_fetch_array($sum2)){
                        $jumlah="";
                        $realisasi="";
                        if (!empty($s2['jumlah'])) $jumlah=number_format($s2['jumlah'],0,",",",");
                        if (!empty($s2['realisasi'])) $realisasi=number_format($s2['realisasi'],0,",",",");

                            echo "<tr>";
                            echo "<td></td>";
                            echo "<td><b>Grand Total : </b></td>";
                            echo "<td></td>";
                            echo "<td></td>";
                            echo "<td></td>";
                            echo "<td></td>";
                            echo "<td align='right'><b>$jumlah</b></td>";
                            echo "<td></td>";
                            echo "<td></td>";
                            echo "<td align='right'><b>$realisasi</b></td>";
                            echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        <?PHP
        
        mysqli_query($cnit, "drop table $tmpbudgetreq");
    
    
    
    
?>
