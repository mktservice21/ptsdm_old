<?php
    session_start();
    //ini_set('memory_limit', '-1');
    ini_set("memory_limit","10G");
    ini_set('max_execution_time', 0);
    
    include ("../../config/koneksimysqli_ms.php");
    
    $arridprod[]="";
    $arrnmprod[]="";
    $arrdivprodid[]="";
    $arrprodaktif[]="";
    $query = "select iprodid, nama, divprodid, aktif from sls.iproduk order by divprodid, nama";
    $tampilk= mysqli_query($cnms, $query);
    while ($zr= mysqli_fetch_array($tampilk)) {
        $zidprod=$zr['iprodid'];
        $znmprod=$zr['nama'];
        $zdivprodid=$zr['divprodid'];
        $zprodaktif=$zr['aktif'];
        
        $arridprod[]=$zidprod;
        $arrnmprod[]=$znmprod;
        $arrdivprodid[]=$zdivprodid;
        $arrprodaktif[]=$zprodaktif;
    }
    
    
    $now=date("mdYhis");
    $tmp01 =" dbtemp.tmpstcuploaddata_01".$_SESSION['USERID']."_$now ";
    $tmp02 =" dbtemp.tmpstcuploaddata_02".$_SESSION['USERID']."_$now ";
    
    
    $ptglpil=$_POST['uperiode1'];
    
    $_SESSION['STCUPDPERTPIL']=$ptglpil;
    
    
    $tgl_pertama=$ptglpil;
    $ptglpilihupload = date("Y-m-d", strtotime($ptglpil));
    $pperiode_ = date("Ym", strtotime($ptglpil));
    
    
    $query ="select * from sls.istock";// WHERE DATE_FORMAT(bulan,'%Y%m')='$pperiode_'
    $query = "CREATE TEMPORARY TABLE $tmp01($query)";
    mysqli_query($cnms, $query);
    $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { goto hapusdata; }
    
    $query = "select a.*, b.nmproduk, b.iprodid, c.nama nama_produk, c.divprodid from $tmp01 a LEFT JOIN sls.imaping_produk b on a.kdproduk=b.kdproduk "
            . " LEFT JOIN sls.iproduk c on b.iprodid=c.iprodid";
    $query = "CREATE TEMPORARY TABLE $tmp02($query)";
    mysqli_query($cnms, $query);
    $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { goto hapusdata; }
    
?>

<form method='POST' action='' id='d-form2' name='form2' data-parsley-validate class='form-horizontal form-label-left'>
    
    <div class='x_content' style="margin-left:-20px; margin-right:-20px;">
    
        <table id='dtablepiluptgt' class='table table-striped table-bordered' width="100%" border="1px solid black">

            <thead>
                <tr>
                    <th width='10px'>No</th>
                    <th align="center" nowrap>Kode</th>
                    <th align="center" nowrap>Nama</th>
                    <th align="center" nowrap>Qty</th>
                    <th align="center" nowrap>Batch</th>
                    <th align="center" nowrap>Expired Date</th>
                    <th align="center" nowrap>IProduk</th>
                    <th align="center" nowrap></th>
                </tr>
            </thead>
            <tbody>
                <?PHP
                //yang belum mapping
                $totalqty=0;
                $no=1;
                $query = "select * from $tmp02 WHERE IFNULL(iprodid,'')='' order by nmproduk, kdproduk";
                $tampil1= mysqli_query($cnms, $query);
                $ketemu1= mysqli_num_rows($tampil1);
                $jmlrec1=$ketemu1;
                if ($ketemu1>0) {
                    while ($row1= mysqli_fetch_array($tampil1)) {
                        $nfile0=$row1['divprodid'];
                        $nfile1=$row1['kdproduk'];
                        $nfile2="";
                        $nfile3=$row1['nmproduk'];
                        $nfile4=$row1['qty'];
                        $nfile5="";
                        $nfile6=$row1['nobatch'];
                        $nfile7=$row1['expdate'];
                        $nfile8=$row1['iprodid'];
                        $nfile9=$row1['nama_produk'];


                        if ($nfile7=="0000-00-00") $nfile7="";

                        if (!empty($nfile7)) $nfile7 = date("F Y", strtotime($nfile7));

                        if (empty($nfile4)) $nfile4=0;

                        $totalqty=(double)$totalqty+(double)$nfile4;
                        $nfile4=number_format($nfile4,0,",",",");


                        $pwarna="";
                        if (empty($nfile8)) $pwarna=" style='color:red;' ";

                        echo "<tr $pwarna>";
                        echo "<td nowrap>$no</td>";
                        echo "<td nowrap>$nfile1</td>";
                        echo "<td nowrap>$nfile3</td>";
                        echo "<td nowrap align='right'>$nfile4</td>";
                        echo "<td nowrap>$nfile6</td>";
                        echo "<td nowrap>$nfile7</td>";
                        echo "<td nowrap>";
                        echo "<select class='soflow' id='cb_iprodid$no' name='cb_iprodid$no'>";
                        echo "<option value=''>--Pilih--</option>";
                        for($ix=1;$ix<count($arridprod);$ix++) {

                            $zidprod=$arridprod[$ix];
                            $znmprod=$arrnmprod[$ix];
                            $zdivprodid=$arrdivprodid[$ix];
                            $zprodaktif=$arrprodaktif[$ix];
                            $paktifnm="Aktif";
                            if ($zprodaktif=="N") $paktifnm="Non Aktif";

                            echo "<option value='$zidprod'>$znmprod ($paktifnm) - $zdivprodid</option>";

                        }
                        echo "</select>";

                        echo "</td>";
                        echo "<td nowrap>";
                        echo "<input type='hidden' id='txt_kdprod$no' name='txt_kdprod$no' value='$nfile1'>";
                        echo "<input type='button' class='btn btn-dark btn-xs' id='btnsave' name='btnsave' onclick=\"SimpanDataMapping('txt_kdprod$no', 'cb_iprodid$no')\" value='Save'>";
                        echo "</td>";
                        echo "</tr>";

                        $no++;

                    }
                }

                //yang sudah mapping
                $query = "select * from $tmp02 WHERE IFNULL(iprodid,'')<>'' order by nmproduk, kdproduk";
                $tampil= mysqli_query($cnms, $query);
                $ketemu= mysqli_num_rows($tampil);
                $jmlrec=$ketemu;
                if ($ketemu>0) {
                    while ($row= mysqli_fetch_array($tampil)) {
                        $nfile0=$row['divprodid'];
                        $nfile1=$row['kdproduk'];
                        $nfile2="";
                        $nfile3=$row['nmproduk'];
                        $nfile4=$row['qty'];
                        $nfile5="";
                        $nfile6=$row['nobatch'];
                        $nfile7=$row['expdate'];
                        $nfile8=$row['iprodid'];
                        $nfile9=$row['nama_produk'];


                        if ($nfile7=="0000-00-00") $nfile7="";

                        if (!empty($nfile7)) $nfile7 = date("F Y", strtotime($nfile7));

                        if (empty($nfile4)) $nfile4=0;

                        $totalqty=(double)$totalqty+(double)$nfile4;
                        $nfile4=number_format($nfile4,0,",",",");

                        $pwarna="";
                        if (empty($nfile8)) $pwarna=" style='color:red;' ";

                        echo "<tr $pwarna>";
                        echo "<td nowrap>$no</td>";
                        echo "<td nowrap>$nfile1</td>";
                        echo "<td nowrap>$nfile3</td>";
                        echo "<td nowrap align='right'>$nfile4</td>";
                        echo "<td nowrap>$nfile6</td>";
                        echo "<td nowrap>$nfile7</td>";
                        echo "<td nowrap>$nfile9</td>";
                        echo "<td nowrap></td>";
                        echo "</tr>";

                        $no++;

                    }
                }
                $totalqty=number_format($totalqty,0,",",",");
                echo "<tr>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap align='right'>Total Qty : </td>";
                echo "<td nowrap align='right'>$totalqty</td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "</tr>";
                ?>
            </tbody>
        </table>
        
    </div>
    
</form>

<script>

    $(document).ready(function() {
        var dataTable = $('#dtablepiluptgt').DataTable( {
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
            },
            "scrollY": 460,
            "scrollX": true
        } );
        $('div.dataTables_filter input', dataTable.table().container()).focus();
    } );


    function SimpanDataMapping(ikdprodsby, iprodid) {
        var eprodsby =document.getElementById(ikdprodsby).value;
        var eprodid =document.getElementById(iprodid).value;

        //alert(eprodsby+", "+eprodid);
        ok_ = 1;
        if (ok_) {
            var r = confirm('Apakah akan simpan data...?');
            if (r==true) {

                var txt="";

                $.ajax({
                    type:"post",
                    url:"module/stc_uploaddatastock/simpandatamaping.php?module=simpandatamapingstc&act=input",
                    data:"uprodsby="+eprodsby+"&uprodid="+eprodid,
                    success:function(data){
                        alert(data);
                    }
                });


            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }

    }
</script>


<style>
    .divnone {
        display: none;
    }
    #dtablepiluptgt th {
        font-size: 13px;
    }
    #dtablepiluptgt td { 
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
