<?php
    session_start();
    include "../../config/koneksimysqli_it.php";
    
    $pacabangid=$_POST['ucab'];
    $pareaid=$_POST['uarea'];
    
?>
<u><b>LAMA</b></u><br/>&nbsp;
<table id='datatablepindahcus' class='table table-striped table-bordered' width='100%'>
    <thead>
        <tr>
            <th width='7px'>No</th>
            <th nowrap>Customer ID</th>
            <th nowrap>Nama</th>
            <th nowrap>Alamat</th>
        </tr>
    </thead>
    <tbody>
    <?PHP
        $no=1;
    
        $query = "select iCabangId, areaId, iCustId, nama, alamat1 
                from MKT.icust WHERE iCabangId='$pacabangid' AND areaId='$pareaid' order by nama, iCustId";
        
        $tampil = mysqli_query($cnit, $query);
        while ($row= mysqli_fetch_array($tampil)) {
            $pcustid=$row['iCustId'];
            $pcustnm=$row['nama'];
            $palamat=$row['alamat1'];
            echo "<tr>";
            echo "<td nowrap>$no</td>";
            echo "<td nowrap>$pcustid</td>";
            echo "<td nowrap>$pcustnm</td>";
            echo "<td nowrap>$palamat</td>";
            echo "</tr>";
            
            $no++;
        }
    ?>
    </tbody>
</table>
<br/>&nbsp;

<script type="text/javascript">
    $(document).ready(function() {
        var dataTable = $('#datatablepindahcus').DataTable( {
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
    #datatablepindahcus th {
        font-size: 13px;
    }
    #datatablepindahcus td { 
        font-size: 11px;
    }
</style>