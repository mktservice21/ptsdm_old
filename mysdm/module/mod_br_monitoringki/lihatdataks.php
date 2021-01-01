<?PHP
    date_default_timezone_set('Asia/Jakarta');
    session_start();
    include "../../config/koneksimysqli.php";
    include "../../config/fungsi_combo.php";
    $act="input";
    $aksi="";
    
    $hari_ini = date("Y-m-d");
    $tgl_pertama = date('d/m/Y', strtotime($hari_ini));
    $pnoresi="";
    $ptgl=$_POST['utgltrs'];
    $piddokter=$_POST['uiddokter'];
    $pidkry=$_POST['uidkaryawan'];
    $pidmr=$_POST['umrid'];
    $prptrans=$_POST['urp'];
    
    $ptgltransf=date('d/m/Y', strtotime($ptgl));
    $ptgltrs = date('Y-m-01', strtotime($ptgl));
    $pblntrs = date('Ym', strtotime($ptgl));
    if (empty($pidmr)) $pidmr=$pidkry;
    
    $now=date("mdYhis");
    $puserid=$_SESSION['USERID'];
    $tmp00 =" dbtemp.tmpbrmntusrlks00_".$puserid."_$now ";
    $tmp01 =" dbtemp.tmpbrmntusrlks01_".$puserid."_$now ";
    
    $pilihquery=false;
    $ppilihkaryawan=$pidmr;
    
    //cari jabatan
    $query = "select jabatanId as jabatanid from hrd.karyawan WHERE karyawanid='$pidkry'";
    $tampil=mysqli_query($cnmy, $query);
    $row= mysqli_fetch_array($tampil);
    $pjbtid=$row['jabatanid'];
    if ($pjbtid=="18") $pjbtid="10";//supervisor disamakan dengan am
    
    //cari nama dokter
    $query = "select nama as nama from hrd.dokter WHERE dokterid='$piddokter'";
    $tampil2=mysqli_query($cnmy, $query);
    $row2= mysqli_fetch_array($tampil2);
    $pnmdokter=$row2['nama'];
    
    
    $pfiltercab="";
    $pfilterkry="";
    $query = "select * from hrd.dokter_ks1 WHERE srid='$pidmr' AND dokterid='$piddokter' AND bulan>='$ptgltrs'";
    $ketemu= mysqli_num_rows(mysqli_query($cnmy, $query));
    if ((DOUBLE)$ketemu>0) $pilihquery=true;
    else{
        $query2 = "select * from hrd.dokter_ks1 WHERE srid='$pidkry' AND dokterid='$piddokter' AND bulan>='$ptgltrs'";
        $ketemu2= mysqli_num_rows(mysqli_query($cnmy, $query2));
        if ((DOUBLE)$ketemu2>0) $ppilihkaryawan=$pidkry;
        else{
            
            
            $query = "select distinct icabangid as icabangid, areaid as areaid from ms.t_penempatan_marketing_auto where karyawanid='$pidkry' and bulan='$pblntrs'";
            $tampil=mysqli_query($cnmy, $query);
            while ($irow= mysqli_fetch_array($tampil)) {
                $ficabid=$irow['icabangid'];
                $fiareaid=$irow['areaid'];

                if ($pjbtid=="08") $pfiltercab .="'".$ficabid."',";
                elseif ($pjbtid=="10") $pfiltercab .="'".$ficabid."".$fiareaid."',";
            }
            
            
            
            if (!empty($pfiltercab)) {
                
                $pfiltercab="(".substr($pfiltercab, 0, -1).")";
                if ($pjbtid=="08") {
                    $query = "select distinct karyawanid from ms.t_penempatan_marketing_auto where iCabangId in $pfiltercab and bulan='$pblntrs' AND jabatanid in ('15')";
                }else{
                    $query = "select distinct karyawanid from ms.t_penempatan_marketing_auto where CONCAT(iCabangId,areaid) in $pfiltercab and bulan='$pblntrs' AND jabatanid in ('15')";
                }
                $tampil2=mysqli_query($cnmy, $query);
                while ($irow2= mysqli_fetch_array($tampil2)) {
                    $fikryid=$irow2['karyawanid'];
                    
                    $pfilterkry .="'".$fikryid."',";
                }
                
                if (!empty($pfilterkry)) {
                    $pfilterkry="(".substr($pfilterkry, 0, -1).")";
                }
                
            }
            
            
            
        }
        
    }
    
    
    $query = "select bulan, srid, dokterid, sum(tvalue) as tvalue "
            . " from hrd.dokter_ks1 WHERE "
            . " dokterid='$piddokter' AND bulan>='$ptgltrs' ";
    if ($pilihquery == true)
        $query .= " AND srid='$ppilihkaryawan' ";
    else
        $query .= " AND srid IN $pfilterkry ";
    
    $query .= " GROUP BY 1,2,3 ";
    $queryii=$query;//untuk test query
    $query = "create TEMPORARY table $tmp00 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "SELECT a.bulan, a.srid, b.nama as nama_karyawan, a.dokterid as dokterid, '$pnmdokter' as nama_dokter, a.tvalue "
            . " FROM $tmp00 as a LEFT JOIN "
            . " hrd.karyawan as b on a.srid=b.karyawanid";
    $query = "create TEMPORARY table $tmp01 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
?>


    <!-- bootstrap-datetimepicker -->
    <link href="vendors/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css" rel="stylesheet">

    <script src="js/hanyaangka.js"></script>
    <!-- jQuery -->
    <script src="vendors/jquery/dist/jquery.min.js"></script>
    <!--input mask -->
    <script src="js/inputmask.js"></script>


    
<script> window.onload = function() { document.getElementById("e_jumlah").focus(); } </script>

<div class='modal-dialog modal-lg'>
    <!-- Modal content-->
    <div class='modal-content'>
        <div class='modal-header'>
            <button type='button' class='close' data-dismiss='modal'>&times;</button>
            <h4 class='modal-title'><u>Data KS</u></h4>
            <h4 class='modal-title'>Tgl. Transfer : <?PHP echo " $ptgltransf"; ?></h4>
            <h4 class='modal-title'>User : <?PHP echo " ($piddokter) $pnmdokter"; ?></h4>
            <h4 class='modal-title'>Jumlah Rp. : <?PHP echo " $prptrans"; ?></h4>
        </div>

        <?PHP
        //echo $queryii;//untuk test query
        ?>
        
        <div class="">

            <!--row-->
            <div class="row">

                <form method='POST' action='<?PHP echo "$aksi?module=bgtmonitoringki&act=input&idmenu=379"; ?>' id='demo-form4' name='form4' data-parsley-validate class='form-horizontal form-label-left'>

                    <div class='col-md-12 col-sm-12 col-xs-12'>
                        <div class='x_panel'>
                            
                            
                            <table id='datatablekslihat' class='table table-striped table-bordered' width='100%'>
                                <thead>
                                    <tr>
                                        <th width='7px'>No</th>
                                        <th width='7px'>Bulan</th>
                                        <th width='7px'>Karyawanid</th>
                                        <th width='7px'>Nama Karyawan</th>
                                        <th width='7px'>Rp.</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?PHP
                                    $no=1;
                                    $ptotal=0;
                                    $query = "select * from $tmp01 order by bulan";
                                    $tampil= mysqli_query($cnmy, $query);
                                    while ($row= mysqli_fetch_array($tampil)) {
                                        $nbln=$row['bulan'];
                                        $nidkaryawan=$row['srid'];
                                        $nnmkaryawan=$row['nama_karyawan'];
                                        $niddokter=$row['dokterid'];
                                        $nnmdokter=$row['nama_dokter'];
                                        $nrp=$row['tvalue'];
                                        
                                        $ptotal=(DOUBLE)$ptotal+(DOUBLE)$nrp;
                                        $nbln = date('F Y', strtotime($nbln));
                                        $nrp=number_format($nrp,0,",",",");
                                        
                                        
                                        echo "<tr>";
                                        echo "<td nowrap>$no</td>";
                                        echo "<td nowrap>$nbln</td>";
                                        echo "<td nowrap>$nidkaryawan</td>";
                                        echo "<td nowrap>$nnmkaryawan</td>";
                                        echo "<td nowrap align='right'>$nrp</td>";
                                        echo "</tr>";
                                        
                                        
                                        $no++;
                                        
                                    }
                                    
                                    $ptotal=number_format($ptotal,0,",",",");
                                    
                                    echo "<tr style='font-weight:bold;'>";
                                    echo "<td nowrap></td>";
                                    echo "<td nowrap></td>";
                                    echo "<td nowrap></td>";
                                    echo "<td nowrap>Total : </td>";
                                    echo "<td nowrap align='right'>$ptotal</td>";
                                    echo "</tr>";
                                    ?>
                                </tbody>
                            </table>


                        </div>
                    </div>


                </form>
            </div>
            <!--end row-->
        </div>
        
        
        <div class='modal-footer'>
            <button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
        </div>
    </div>
</div>


    <script type='text/javascript' src='datetime/js/jquery-ui.min.js'></script>

    <!-- jquery.inputmask -->
    <script src="vendors/jquery.inputmask/dist/min/jquery.inputmask.bundle.min.js"></script>

    <!-- bootstrap-daterangepicker -->
    <script src="vendors/moment/min/moment.min.js"></script>
    <script src="vendors/bootstrap-daterangepicker/daterangepicker.js"></script>
    <script src="vendors/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>
    <!-- Custom Theme Scripts -->
        
        


<?PHP
hapusdata:
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp00");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp01");
    
    mysqli_close($cnmy);
?>