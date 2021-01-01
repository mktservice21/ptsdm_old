<?php

    date_default_timezone_set('Asia/Jakarta');
    ini_set('max_execution_time', 0);
    
    include "../../config/koneksimysqli_it.php";
    
?>


    <table id='mydatatable1' class='table table-striped table-bordered' width="100%" border="1px solid black">
        <thead>
            <tr>
                <th width='10px'>No</th>
                <th width='10px'><input type="checkbox" id="chkiddokt" value="select" onClick="SelAllCheckBox('chkiddokt', 'chkbox_iddok[]')" /></th>
                <th align="center" nowrap>ID</th>
                <th align="center" nowrap>Nama Lengkap</th>
                <th align="center" nowrap>Kota</th>
                <th align="center" nowrap>Alamat</th>
            </tr>
        </thead>
        <tbody>
            <?PHP
            $no=1;
            $query = "select distinct k.dokterid as dokterid, d.nama as nama, d.alamat1 as alamat1, d.alamat2 as alamat2, d.kota from hrd.ks1 k "
                    . " join hrd.dokter d on k.dokterid=d.dokterId "
                    . " where left(k.bulan,4) in ('2019', '2020') order by 2";
            $tampil = mysqli_query($cnit, $query);
            while ($z= mysqli_fetch_array($tampil)) {
                $pidoktid=$z['dokterid'];
                $pnmdoktb=$z['nama'];
                $palamtdkt1=$z['alamat1'];
                $palamtdkt2=$z['alamat2'];
                $pkotadkt1=$z['kota'];
                
                
                $pchkdokt="<input type=checkbox value='$pidoktid' name='chkbox_iddok[]' >";
                
                echo "<tr>";
                echo "<td nowrap>$no</td>";
                echo "<td nowrap>$pchkdokt</td>";
                echo "<td nowrap>$pidoktid</td>";
                echo "<td nowrap>$pnmdoktb</td>";
                echo "<td nowrap>$pkotadkt1</td>";
                echo "<td nowrap>$palamtdkt1</td>";
                echo "</tr>";
                
                $no++;
            }
            ?>
        </tbody>
    </table>


        <style>
            #n_content {
                color:#000;
                font-family: "Arial";
                margin: 5px 20px 20px 20px;
                /*overflow-x:auto;*/
            }
            
            .h1judul {
              color: blue;
              font-family: verdana;
              font-size: 140%;
              font-weight: bold;
            }
            table.tbljudul {
                font-size : 15px;
            }
            table.tbljudul tr td {
                padding: 1px;
                font-family : "Arial, Verdana, sans-serif";
            }
            .tebal {
                 font-weight: bold;
            }
            .miring {
                 font-style: italic;
            }
            table.tbljudul tr.text2 {
                font-size : 13px;
            }
            
            table {
                text-align: left;
                position: relative;
                border-collapse: collapse;
                background-color:#FFFFFF;
            }

            th {
                background: white;
                position: sticky;
                top: 0;
                box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
                z-index:1;
            }

            .th2 {
                background: white;
                position: sticky;
                top: 23;
                box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
                border-top: 1px solid #000;
            }
        </style>
    
        <style>
            
            .divnone {
                display: none;
            }
            #mydatatable1, #mydatatable2 {
                color:#000;
                font-family: "Arial";
            }
            #mydatatable1 th, #mydatatable2 th {
                font-size: 12px;
            }
            #mydatatable1 td, #mydatatable2 td { 
                font-size: 11px;
            }
        </style>
        
    <script>

        $(document).ready(function() {
            var dataTable = $('#mydatatable1').DataTable( {
                //"bPaginate": false,
                "bLengthChange": false,
                "bFilter": true,
                //"bInfo": false,
                //"ordering": false,
                "order": [[ 0, "asc" ]],
                "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
                "displayLength": -1,
                "columnDefs": [
                    { "visible": false },
                    //{ "orderable": false, "targets": 0 },
                    //{ "orderable": false, "targets": 1 },
                    //{ className: "text-right", "targets": [3, 6] },//right
                    { className: "text-nowrap", "targets": [0, 1, 2, 3] }//nowrap

                ],
                "language": {
                    "zeroRecords": "Lihat Page di bawah!!! Jika ada Page, Pilih Page 1...!!! Jika tidak ada Page, maka data KOSONG..."
                },
                //"scrollY": 460,
                "scrollX": true
            } );
            $('div.dataTables_filter input', dataTable.table().container()).focus();
        } );

    </script>
    
    
<?PHP
mysqli_close($cnit);
?>
