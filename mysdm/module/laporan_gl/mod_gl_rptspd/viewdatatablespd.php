<?php
    session_start();
    include "../../../config/koneksimysqli.php";
    
    $pses_grpuser=$_SESSION['GROUP'];
    $pses_divisi=$_SESSION['DIVISI'];
    $pses_idcard=$_SESSION['IDCARD'];
    
    
    $cket = $_POST['eket'];
    $mytgl1 = $_POST['uperiode1'];
    $mytgl2 = $_POST['uperiode2'];
    
    
    $tgl1= date("Y-m", strtotime($mytgl1));
    $tgl2= date("Y-m", strtotime($mytgl2));
    
    $now=date("mdYhis");
    $tmp01 =" dbtemp.RPTREKOTCFA01_".$_SESSION['USERID']."_$now ";
    $tmp02 =" dbtemp.RPTREKOTCFA02_".$_SESSION['USERID']."_$now ";
    $tmp03 =" dbtemp.RPTREKOTCFA03_".$_SESSION['USERID']."_$now ";
    
    $query = "select nomor, tglspd, sum(jumlah) as jumlah from dbmaster.t_suratdana_br WHERE "
            . " stsnonaktif<>'Y' AND DATE_FORMAT(tglspd,'%Y-%m') BETWEEN '$tgl1' AND '$tgl2' "
            . " GROUP BY 1,2";
    $query = "create TEMPORARY table $tmp01 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $query = "SELECT distinct nomor as nospdadj from dbmaster.t_suratdana_br WHERE stsnonaktif<>'Y' "
            . " AND nomor2 IN (select distinct IFNULL(nomor,'') from $tmp01) AND kodeid='3'";
    $query = "create TEMPORARY table $tmp02 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $query = "select a.*, b.nospdadj from $tmp01 a LEFT JOIN $tmp02 b on a.nomor=b.nospdadj";
    $query = "create TEMPORARY table $tmp03 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
?>

<form method='POST' action='' id='d-form4' name='form4' data-parsley-validate class='form-horizontal form-label-left'>
    
    
    <div class='x_content' style="margin-left:-20px; margin-right:-20px;">
        
            <div class="title_left">
                <h4 style="font-size : 12px;">
                    <?PHP
                        $noteket = strtoupper($cket);
                        $text="";
                        echo "<b>$text"
                                . "<p/>&nbsp;*) <span style='color:red;'>klik no divisi/nobr untuk melihat detail pengajuan</span></b>";
                    ?>
                </h4>
            </div>
        <div class="clearfix"></div>
        
        <table id='dtablecadir' class='table table-striped table-bordered' width='100%'>
            <thead>
                <tr>
                    <th width='5px'>No</th>
                    <th width='5px'></th>
                    <th width='50px'>No SPD</th>
                    <th width='50px'>Jumlah</th>
                    <th width='50px'>Tanggal</th>
                </tr>
            </thead>
            <tbody>
            <?PHP
                $sql = "select * from $tmp03 ";
                $sql.=" order by nomor desc";
                //echo $sql;
                $no=1;
                $tampil = mysqli_query($cnmy, $sql);
                while ($row= mysqli_fetch_array($tampil)) {
                    $idno=$row['nomor'];
                    $pnomorspdadj=$row['nospdadj'];
                    $tglbuat = $row["tglspd"];
                    $tglbuat =date("d M Y", strtotime($tglbuat));
                    $pnomorspd = $row["nomor"];
                    $pjumlah = $row["jumlah"];
                    $pjumlah=number_format($pjumlah,0,",",",");
                    
                    
                    $pmymodule="module=suratpd&brid=$pnomorspd&iprint=print";
                    $pmymodule2="module=suratpd&brid=$pnomorspd&iprint=print";
                    $print="<a style='font-size:11px;' title='Print / Cetak' href='#' class='btn btn-info btn-xs' data-toggle='modal' "
                        . "onClick=\"window.open('eksekusi3.php?$pmymodule',"
                        . "'Ratting','width=800,height=500,left=400,top=100,scrollbars=yes,toolbar=yes,status=1,pagescrool=yes')\"> "
                        . "Print</a>";
                    $btnexcel = "<a class='btn btn-success btn-xs' href='eksekusi3.php?$pmymodule2' target='_blank'>Excel</a>";
                        
                    $pbtn_warna="btn btn-warning btn-xs";
                    if (!empty($pnomorspdadj)) $pbtn_warna="btn btn-success btn-xs";
                    $nadd_adj="<button type='button' class='$pbtn_warna' data-toggle='modal' data-target='#myModal' onClick=\"TambahDataInputAdj('$pnomorspd')\">Adjustment</button>";
                    
                    echo "<tr>";
                    echo "<td>$no</td>";
                    echo "<td nowrap>$nadd_adj $print</td>";
                    echo "<td nowrap>$pnomorspd</td>";
                    echo "<td>$pjumlah</td>";
                    echo "<td>$tglbuat</td>";
                    
                    echo "</tr>";
                    $no++;
                    
                }
            ?>
            </tbody>
            
        </table>
        
        
    </div>
    
    
    <div class='clearfix'></div>
</form>

<script>
    
    $(document).ready(function() {
        //alert(etgl1);
        var dataTable = $('#dtablecadir').DataTable( {
            //"stateSave": true,
            //"order": [[ 7, "desc" ]],
            "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
            "displayLength": 10,
            "columnDefs": [
                { "visible": false },
                { "orderable": false, "targets": 0 },
                { "orderable": false, "targets": 1 },
                { className: "text-right", "targets": [3] },//right
                { className: "text-nowrap", "targets": [0, 1, 2, 3,4] }//nowrap

            ],
            "language": {
                "zeroRecords": "Lihat Page di bawah!!! Jika ada Page, Pilih Page 1...!!! Jika tidak ada Page, maka data KOSONG..."
            },
            rowReorder: {
                selector: 'td:nth-child(3)'
            },
            responsive: true,
            "sDom": "Rlfrtip"
        } );
        //$('div.dataTables_filter input', dataTable.table().container()).focus();
    } );
    
    function TambahDataInputAdj(enomorspd){
        $.ajax({
            type:"post",
            url:"module/laporan_gl/mod_gl_rptspd/tambah_adj.php?module=viewisibankspdall",
            data:"unomorspd="+enomorspd,
            success:function(data){
                $("#myModal").html(data);
            }
        });
    }
</script>


<style>
    .divnone {
        display: none;
    }
    #dtablecadir th {
        font-size: 13px;
    }
    #dtablecadir td { 
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
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp01");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp02");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp03");

    mysqli_close($cnmy);
?>