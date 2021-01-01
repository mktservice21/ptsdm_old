<?php
    session_start();
    include "../../config/koneksimysqli_it.php";
    
    $pacabangid=$_POST['ucabbaru'];
    $pareaid=$_POST['uareabaru'];
    
    $milliseconds = round(microtime(true) * 1000);
    $now=date("mdYhis");
    $tmp01 =" dbtemp.TPINCUS01_".$_SESSION['USERID']."_$now$milliseconds ";
    
    
?>
<u><b>BARU</b></u><br/>&nbsp;
<table id='datatablepindahcus2' class='table table-striped table-bordered' width='100%'>
    <thead>
        <tr>
            <th width='7px'>No</th>
            <th nowrap>Customer ID</th>
            <th nowrap>Nama</th>
            <th nowrap>Alamat</th>
            <!--<th nowrap>Customer ID Baru</th>-->
        </tr>
    </thead>
    <tbody>
    <?PHP
        $no=1;
        
        $query = "select MAX(iCustId) as pnomor from MKT.icust WHERE iCabangId='$pacabangid'";
        $tampil = mysqli_query($cnit, $query);
        $rw= mysqli_fetch_array($tampil);
        $pnomor=(DOUBLE)$rw['pnomor'];
        if (empty($pnomor)) $pnomor =0;
    
        $query = "select LPAD(@no:=@no+1, 10, '0') iCustIdNew, iCabangId, areaId, iCustId, nama, alamat1 
                from MKT.icust, (SELECT @no:=$pnomor) AS no WHERE iCabangId='$pacabangid' AND areaId='$pareaid' order by nama, iCustId";
        
        $tampil = mysqli_query($cnit, $query);
        while ($row= mysqli_fetch_array($tampil)) {
            $pcustidbaru=$row['iCustIdNew'];
            $pcustid=$row['iCustId'];
            $pcustnm=$row['nama'];
            $palamat=$row['alamat1'];
            echo "<tr>";
            echo "<td nowrap>$no</td>";
            echo "<td nowrap>$pcustid</td>";
            echo "<td nowrap>$pcustnm</td>";
            echo "<td nowrap>$palamat</td>";
            //echo "<td nowrap>$pcustidbaru</td>";
            echo "</tr>";
            
            $no++;
        }
    ?>
    </tbody>
</table>


<script type="text/javascript">
    $(document).ready(function() {
        var dataTable = $('#datatablepindahcus2').DataTable( {
            "stateSave": true,
            "lengthMenu": [[10, 50, 100, 10000000], [10, 50, 100, "All"]],
            "displayLength": 10,
            "columnDefs": [
                { className: "text-right", "targets": [0] },//right
                { className: "text-nowrap", "targets": [0, 1, 2, 3] }//nowrap

            ],
            "language": {
                "zeroRecords": "Lihat Page di bawah!!! Jika ada Page, Pilih Page 1...!!! Jika tidak ada Page, maka data KOSONG..."
            },
            "scrollY": 460,
            "scrollX": true
        } );
        $('div.dataTables_filter input', dataTable.table().container()).focus();
    } );
</script>

<style>
    .divnone {
        display: none;
    }
    #datatablepindahcus2 th {
        font-size: 13px;
    }
    #datatablepindahcus2 td { 
        font-size: 11px;
    }
</style>