<?PHP
    session_start();
    include "../../config/koneksimysqli.php";
?>


    <script >
        $(document).ready(function() {
            var dataTable = $('#mytable').dataTable({
                fixedHeader: false,
                "ordering": true,
                "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
                "displayLength": 10
            });
            //$('div.dataTables_filter input', dataTable.table().container()).focus();
        } );
    </script>


    <div class='modal-dialog modal-lg'>
        <!-- Modal content-->
        <div class='modal-content'>
            <div class='modal-header'>
                <button type='button' class='close' data-dismiss='modal'>&times;</button>
                <h4 class='modal-title'>Pilih Kelompok</h4>
            </div>

            <div class='modal-body'>
                <?PHP
                    $picardid=$_SESSION['IDCARD'];
                    $puserid=$_SESSION['USERID'];

                    $now=date("mdYhis");
                    $tmp00 =" dbtemp.tmpcrnodivanti00_".$puserid."_$now ";
                    $tmp01 =" dbtemp.tmpcrnodivanti01_".$puserid."_$now ";
    
                    $pidkry=$_POST['uidkry'];
                    $pbukaall=$_POST['ubuka'];

                    $ptgl = str_replace('/', '-', $_POST['utgl']);
                    
                    $tgl_pertama = date('Ym', strtotime('-2 month', strtotime($ptgl)));
                    $tgl_kedua= date("Ym", strtotime($ptgl));
                    
                    $query = "select idinput, DATE_FORMAT(tgl,'%d/%m/%Y') as tgl, nomor, nodivisi, jumlah, CAST(0 as DECIMAL(20,2)) as jmlrp from dbmaster.t_suratdana_br WHERE "
                            . " IFNULL(stsnonaktif,'')<>'Y' AND DATE_FORMAT(tgl,'%Y%m') >='$tgl_pertama' ";
                    if ($pbukaall=="1") {
                        
                    }else{
                        $query .=" AND userid='$pidkry' ";
                    }
                    
                    $query = "create TEMPORARY table $tmp00 ($query)";
                    mysqli_query($cnmy, $query);
                    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
                    
                    $query = "select idinput, sum(jumlah) as jumlahrp from dbmaster.t_br_antrian WHERE IFNULL(stsnonaktif,'')<>'Y' AND idinput IN "
                            . " (select IFNULL(idinput,'') from $tmp00) GROUP BY 1";
                    $query = "create TEMPORARY table $tmp01 ($query)";
                    mysqli_query($cnmy, $query);
                    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
                    
                    $query = "UPDATE $tmp00 a JOIN $tmp01 b on a.idinput=b.idinput SET a.jmlrp=b.jumlahrp";
                    mysqli_query($cnmy, $query);
                    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
                    
                    
                    
                    $query = "select * from $tmp00";
                    $query .=" ORDER BY idinput desc";
                    
                    
                    
                    echo "<table id='mytable' cellpadding='0' cellspacing='0' border='0' class='table table-striped table-bordered' width='100%'>";
                    echo "<thead><tr><th width='10px'>No</th><th width='80px'>ID</th><th>Tgl</th><th>Nomor</th><th>No Divisi</th>"
                            . "<th>Jumlah</th>"
                            . "<th>Transfer</th>"
                            . "<th>Sisa</th>"
                            . "</tr></thead>";
                    echo "<tbody class='gridview-error'>";
                    
                    $no=1;
                    $tampil = mysqli_query($cnmy, $query);
                    while ($r=mysqli_fetch_array($tampil)){
                        $pidinput=$r['idinput'];
                        $ptgl=$r['tgl'];
                        $pnomor=$r['nomor'];
                        $pnodivisi=$r['nodivisi'];
                        $pjumlah=$r['jumlah'];
                        $pjumlahrp=$r['jmlrp'];
                        
                        
                        if (empty($pjumlahrp)) $pjumlahrp=0;
                        $psisa=(double)$pjumlah-(double)$pjumlahrp;
                        
                        $pjumlah=number_format($pjumlah,0,",",",");
                        $pjumlahrp=number_format($pjumlahrp,0,",",",");
                        $psisa=number_format($psisa,0,",",",");
                        
                        echo "<tr scope='row'><td>$no</td>";
                        echo "<td><a class='btn btn-dark btn-xs' data-dismiss='modal' href='#' "
                        . "onClick=\"getDataModalNoDivisi('$_POST[udata1]', '$_POST[udata2]', '$_POST[udata3]', '$_POST[udata4]', '$_POST[udata5]', '$pidinput', '$pnodivisi', '$pjumlah', '$psisa', '$psisa')\">
                            $pidinput</a></td>";
                        echo "<td>$ptgl</td>";
                        echo "<td>$pnomor</td>";
                        echo "<td>$pnodivisi</td>";
                        echo "<td align='right'>$pjumlah</td>";
                        echo "<td align='right'>$pjumlahrp</td>";
                        echo "<td align='right'>$psisa</td>";
                        echo "</tr>";
                        $no++;
                        
                    }
                    
                    echo "</tbody>";
                    echo "</table>";
                    
                    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp00");
                    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp01");
                    mysqli_close($cnmy);
                
                ?>
            </div>

            <div class='modal-footer'>
                <button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
            </div>
        </div>
    </div>