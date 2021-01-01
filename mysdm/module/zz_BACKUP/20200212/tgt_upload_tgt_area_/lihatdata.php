<?php
    session_start();
    //ini_set('memory_limit', '-1');
    ini_set("memory_limit","10G");
    ini_set('max_execution_time', 0);
    
    include ("../../config/koneksimysqli_ms.php");
    
    $now=date("mdYhis");
    $tmp01 =" dbtemp.DTTMLGTLAUSR_01".$_SESSION['USERID']."_$now ";
    $tmp02 =" dbtemp.DTTMLGTLAUSR_02".$_SESSION['USERID']."_$now ";
    
    
    $ptglpil=$_POST['uperiode1'];
    $pidcabpil=$_POST['uidcabang'];
    $pidareapil=$_POST['uareaid'];
    
    $_SESSION['TGTUPDPERTPIL']=$ptglpil;
    $_SESSION['TGTUPDCABPIL']=$pidcabpil;
    $_SESSION['TGTUPDAREAPIL']=$pidareapil;
    
    $tgl_pertama=$ptglpil;
    $ptglpilihupload = date("Y-m-d", strtotime($ptglpil));
    $pperiode_ = date("Ym", strtotime($ptglpil));
    
    $query ="select * from tgt.targetarea WHERE DATE_FORMAT(bulan,'%Y%m')='$pperiode_' AND icabangid='$pidcabpil' AND areaid='$pidareapil'";
    $query = "CREATE TEMPORARY TABLE $tmp01($query)";
    mysqli_query($cnms, $query);
    $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { goto hapusdata; }
    
    $query ="select a.*, b.nama as nmproduk from $tmp01 a LEFT JOIN sls.iproduk b on CONVERT(a.iprodid, DECIMAL(20))=CONVERT(b.iprodid, DECIMAL(20))";
    $query = "CREATE TEMPORARY TABLE $tmp02($query)";
    mysqli_query($cnms, $query);
    $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { goto hapusdata; }
    
?>
<form method='POST' action='' id='d-form2' name='form2' data-parsley-validate class='form-horizontal form-label-left'>
    
    <div class='x_content' style="margin-left:-20px; margin-right:-20px;">
        
        <table id='datatabletpldttargetcab' class='table table-striped table-bordered' width='100%'>
            <thead>
                <tr>
                    <th width='10px'>No</th>
                    <th align="center" nowrap>DIVISI</th>
                    <th align="center" nowrap>KD PRODUK</th>
                    <th align="center" nowrap>HNA</th>
                    <th align="center" nowrap>NAMA PRODUK</th>
                    <th align="center" nowrap>QTY</th>
                    <th align="center" nowrap>VALUE</th>
                </tr>
            </thead>
            <tbody>
                <?PHP
                
                    $pgrdtotal=0;
                    $no=1;
                    $query = "select * from $tmp02 order by divprodid, nmproduk, iprodid";
                    $tampil= mysqli_query($cnms, $query);
                    $ketemu= mysqli_num_rows($tampil);
                    $jmlrec=$ketemu;
                    if ($ketemu>0) {
                        while ($row= mysqli_fetch_array($tampil)) {

                            $nfile0=$row['divprodid'];
                            $nfile1=$row['iprodid'];
                            $nfile2=$row['hna'];
                            $nfile3=$row['nmproduk'];
                            $nfile4=$row['qty'];
                            $nfile5=$row['value'];

                            $pgrdtotal=(double)$pgrdtotal+(double)$nfile5;

                            $nfile2=number_format($nfile2,0,",",",");
                            $nfile4=number_format($nfile4,0,",",",");
                            $nfile5=number_format($nfile5,0,",",",");

                            echo "<tr>";
                            echo "<td nowrap>$no</td>";
                            echo "<td nowrap>$nfile0</td>";
                            echo "<td nowrap>$nfile1</td>";
                            echo "<td nowrap align='right'>$nfile2</td>";
                            echo "<td nowrap>$nfile3</td>";
                            echo "<td nowrap align='right'>$nfile4</td>";
                            echo "<td nowrap align='right'>$nfile5</td>";
                            echo "</tr>";

                            $no++;
                        }

                        echo "<tr>";
                        echo "<td nowrap colspan='7' align='center'>&nbsp;</td>";
                        echo "<td class='divnone'></td>";
                        echo "<td class='divnone'></td>";
                        echo "<td class='divnone'></td>";
                        echo "<td class='divnone'></td>";
                        echo "<td class='divnone'></td>";
                        echo "<td class='divnone'></td>";
                        echo "</tr>";

                        $pgrdtotal=number_format($pgrdtotal,0,",",",");


                        $query = "select divprodid, SUM(value) as GVALUE from $tmp02 GROUP BY 1 ORDER BY 1";
                        $tampilg= mysqli_query($cnms, $query);
                        $ketemug= mysqli_num_rows($tampilg);
                        if ($ketemug>0) {
                            while ($rg= mysqli_fetch_array($tampilg)) {
                                $pndivi=$rg['divprodid'];
                                $ntotperdiv=$rg['GVALUE'];

                                $ntotperdiv=number_format($ntotperdiv,0,",",",");

                                echo "<tr>";
                                echo "<td nowrap colspan='6' align='right'><b>Total $pndivi : </b></td>";
                                echo "<td class='divnone'></td>";
                                echo "<td class='divnone'></td>";
                                echo "<td class='divnone'></td>";
                                echo "<td class='divnone'></td>";
                                echo "<td class='divnone'></td>";
                                echo "<td nowrap align='right'><b>$ntotperdiv</b></td>";
                                echo "</tr>";

                            }

                            echo "<tr>";
                            echo "<td nowrap colspan='7' align='center'>&nbsp;</td>";
                            echo "<td class='divnone'></td>";
                            echo "<td class='divnone'></td>";
                            echo "<td class='divnone'></td>";
                            echo "<td class='divnone'></td>";
                            echo "<td class='divnone'></td>";
                            echo "<td class='divnone'></td>";
                            echo "</tr>";

                        }

                        echo "<tr>";
                        echo "<td nowrap colspan='6' align='right'><b>Grand Total : </b></td>";
                        echo "<td class='divnone'></td>";
                        echo "<td class='divnone'></td>";
                        echo "<td class='divnone'></td>";
                        echo "<td class='divnone'></td>";
                        echo "<td class='divnone'></td>";
                        echo "<td nowrap align='right'><b>$pgrdtotal</b></td>";
                        echo "</tr>";


                    }
                 
                ?>
            </tbody>
        </table>
        
        
    </div>
</form>

<script>
            
    $(document).ready(function() {
        var dataTable = $('#datatabletpldttargetcab').DataTable( {
            fixedHeader: true,
            "bPaginate": false,
            "bLengthChange": false,
            "bFilter": true,
            "bInfo": false,
            "ordering": false,
            "order": [[ 0, "desc" ]],
            "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
            "displayLength": -1,
            "columnDefs": [
                { "visible": false },
                { "orderable": false, "targets": 0 },
                { "orderable": false, "targets": 1 },
                { className: "text-right", "targets": [3] },//right
                { className: "text-nowrap", "targets": [0, 1, 2] }//nowrap

            ],
            "language": {
                "zeroRecords": "Lihat Page di bawah!!! Jika ada Page, Pilih Page 1...!!! Jika tidak ada Page, maka data KOSONG..."
            }/*,
            "scrollY": 460,
            "scrollX": true*/
        } );
        $('div.dataTables_filter input', dataTable.table().container()).focus();
    } );
    
</script>


<style>
    .divnone {
        display: none;
    }
    #datatabletpldttargetcab th {
        font-size: 13px;
    }
    #datatabletpldttargetcab td { 
        font-size: 11px;
    }
    .imgzoom:hover {
        -ms-transform: scale(3.5); /* IE 9 */
        -webkit-transform: scale(3.5); /* Safari 3-8 */
        transform: scale(3.5);
        
    }
</style>


<?PHP
hapusdata:
    mysqli_query($cnms, "DROP TEMPORARY TABLE $tmp01");
    mysqli_query($cnms, "DROP TEMPORARY TABLE $tmp02");
    mysqli_close($cnms);
?>

