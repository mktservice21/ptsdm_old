<?php
    session_start();
    include "../../config/koneksimysqli_ms.php";
    $cnmy=$cnms;
  
    $pbln=$_POST['ubln'];
    $pidkaryawan=$_POST['uidkaryawan'];
    $pcabang=$_POST['ucabang'];
    
    
    $_SESSION['MKSTRGDPERIODE']=$pbln;
    $_SESSION['MKSTRGDKRY']=$pidkaryawan;
    $_SESSION['MKSTRGDCAB']=$pcabang;
    
    
    $pidmenu=$_GET['idmenu'];
    $pmodule=$_GET['module'];
    
    $pperiode= date("Y", strtotime($pbln));
    
    $filtercab="";
    if (!empty($pcabang)) $filtercab=" AND icabangid='$pcabang' ";
    
    
    $userid=$_SESSION['USERID'];
    $now=date("mdYhis");
    $tmp01 =" dbtemp.VIEWTGLDAERAH01_".$userid."_$now ";
    $tmp02 =" dbtemp.VIEWTGLDAERAH02_".$userid."_$now ";
    $tmp03 =" dbtemp.VIEWTGLDAERAH03_".$userid."_$now ";
    
    $query ="select icabangid, bulan, divprodid, iprodid, hna, qty, value from tgt.targettahun WHERE YEAR(bulan)='$pperiode' $filtercab";
    $query = "create temporary table $tmp01 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query ="select a.iprodid, b.nama, a.divprodid, a.kategori, b.hna, CAST(0 as DECIMAL(20,2)) as qty, CAST(0 as DECIMAL(20,2)) as value "
            . " from sls.ytdprod a LEFT JOIN sls.iproduk b on a.iprodid=b.iprodid"
            . " WHERE DATE_FORMAT(a.bulan,'%Y%m')=(select DATE_FORMAT(MAX(bulan),'%Y%m') FROM sls.ytdprod)";
    $query = "create temporary table $tmp02 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "INSERT INTO $tmp02 (iprodid, nama, divprodid, hna)"
            . " SELECT a.iprodid, b.nama, a.divprodid, a.hna FROM $tmp01 a "
            . " LEFT JOIN sls.iproduk b on a.iprodid=b.iprodid ";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "UPDATE $tmp02 a JOIN $tmp01 b on a.iprodid=b.iprodid SET a.qty=b.qty, a.hna=b.hna, a.value=b.value";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "select distinct a.icabangid, b.iprodid, b.nama, b.divprodid, b.kategori, b.hna FROM $tmp01 a, $tmp02 b";
    $query = "create temporary table $tmp03 ($query)"; 
    //mysqli_query($cnmy, $query);
    //$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "DELETE FROM $tmp03 WHERE CONCAT(icabangid, iprodid) IN (select distinct CONCAT(icabangid, iprodid) FROM $tmp01)"; 
    //mysqli_query($cnmy, $query);
    //$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
?>

<script src="js/inputmask.js"></script>
<div class='x_content'>
    
    <table id='datatablepmkt' class='table table-striped table-bordered' width="100%">
        <thead>
            <tr>
                <th width='4px'></th>
                <th width='4px'>No</th>
                <th width='100px'>Divisi</th>
                <th width='80px'>Kategori</th>
                <th width='80px'>Produk</th>
                <th width='10px'>HNA</th>
                <th width='2px'>QTY</th>
                <th width='5px'>Value</th>
            </tr>
        </thead>
        
        <tbody>
            <?PHP
            $pstyle_txt =" style='text-align:right; background-color: transparent; border: 0px solid;' ";
            $pstyle_txt2 =" style='text-align:right;' ";
            $no=1;
            $query = "select * from $tmp02 order by divprodid, kategori, nama";
            $tampil = mysqli_query($cnmy, $query);
            while( $row=mysqli_fetch_array($tampil) ) {
                $picabangid=$pcabang;
                $pdivisi=$row['divprodid'];
                $pkategori=$row['kategori'];
                $pidprod=$row['iprodid'];
                $pnmprod=$row['nama'];
                $phna=$row['hna'];
                $pqty=$row['qty'];
                $pvalue=$row['value'];
                
                $cekbox = "<span hidden><input type=checkbox value='$pidprod' id='chkbox_br[$pidprod]' name='chkbox_br[]' onclick=\"HitungJumlahTotalCexBox()\" checked></span>";
                $txt_bridproduk="<input type='hidden' value='$pidprod' id='txtbridspg[$pidprod]' name='txtbridspg[$pidprod]' class='' size='8px' Readonly>";
                $txt_cabangid="<input type='hidden' value='$picabangid' id='txtidcabang[$pidprod]' name='txtidcabang[$pidprod]' class='' size='8px' Readonly>";
                $txt_hna="<input type='text' size='8px' id='txthnaprod[$pidprod]' name='txthnaprod[$pidprod]' class='inputmaskrp2' autocomplete='off' "
                        . " value='$phna' Readonly $pstyle_txt>";
                
                $txt_qty="<input type='text' size='8px' id='txtqtyprod[$pidprod]' name='txtqtyprod[$pidprod]' class='inputmaskrp2' autocomplete='off' "
                        . " value='$pqty' onblur=\"HitungJumlahTotalCexBox()\" $pstyle_txt2>";
                
                $txt_value="<input type='text' size='8px' id='txtvalueprod[$pidprod]' name='txtvalueprod[$pidprod]' class='inputmaskrp2' autocomplete='off' "
                        . " value='$pvalue' Readonly $pstyle_txt>";
                    
                
                $filed_ada="$txt_bridproduk $txt_cabangid";
                
                echo "<tr>";
                echo "<td nowrap>$cekbox $filed_ada</td>";
                echo "<td nowrap>$no</td>";
                echo "<td nowrap>$pdivisi</td>";
                echo "<td nowrap>$pkategori</td>";
                echo "<td nowrap>$pnmprod</td>";
                echo "<td nowrap align='right'>$txt_hna</td>";
                echo "<td nowrap align='right'>$txt_qty</td>";
                echo "<td nowrap align='right'>$txt_value</td>";
                echo "</tr>";
                
                $no++;
            }
            ?>
        </tbody>
        
    </table>
    
</div>



<?PHP
hapusdata:
    mysqli_query($cnmy, "drop TEMPORARY table $tmp01");
    mysqli_query($cnmy, "drop TEMPORARY table $tmp02");
    mysqli_query($cnmy, "drop TEMPORARY table $tmp03");
?>

<script>

$(document).ready(function() {
    var table = $('#datatablepmkt').DataTable({
        fixedHeader: true,
        "ordering": true,
        "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
        "displayLength": -1,
        "order": [[ 0, "asc" ]],
        bFilter: true, bInfo: true, "bLengthChange": true, "bLengthChange": true,
        "bPaginate": false
    } );

} );

</script>

<style>
    .divnone {
        display: none;
    }
    #datatablepmkt th {
        font-size: 12px;
    }
    #datatablepmkt td { 
        font-size: 11px;
    }
</style>