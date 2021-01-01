<?PHP
    session_start();
?>

<script>
    $(document).ready(function() {
        var aksi = "module/sls_uploadpabriksls/aksi_uploadpabriksls.php";
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var idmenu = urlku.searchParams.get("idmenu");
        var nmun = urlku.searchParams.get("nmun");

        //alert(etgl1);
        var dataTable = $('#dtablepilupslspab').DataTable( {
            "processing": true,
            "serverSide": true,
            //"stateSave": true,
            "fixedHeader": true,
            "order": [[ 0, "desc" ]],
            "lengthMenu": [[10, 50, 100, 10000000], [10, 50, 100, "All"]],
            "displayLength": 10,
            "columnDefs": [
                { "visible": false },
                { "orderable": false, "targets": 0 },
                //{ "orderable": false, "targets": 1 },
                { className: "text-right", "targets": [10,11,12,13,14,15,16,17,18,19] },//right
                { className: "text-nowrap", "targets": [0, 1, 2, 3, 4, 5,6,7,8,9,10,11,12,13,14,15,16,17,18,19] }//nowrap

            ],
            "language": {
                "zeroRecords": "Lihat Page di bawah!!! Jika ada Page, Pilih Page 1...!!! Jika tidak ada Page, maka data KOSONG..."
            },
            "scrollY": 440,
            "scrollX": true,

            "ajax":{
                url :"module/sls_uploadpabriksls/mydatasales.php?module="+module+"&idmenu="+idmenu+"&nmun="+nmun+"&aksi="+aksi, // json datasource
                type: "post",  // method  , by default get
                data:"module="+module,
                error: function(){  // error handling
                    $(".data-grid-error").html("");
                    $("#datatable").append('<tbody class="data-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                    $("#data-grid_processing").css("display","none");

                }
            }
        } );
        $('div.dataTables_filter input', dataTable.table().container()).focus();
    } );
</script>

<style>
    .divnone {
        display: none;
    }
    #dtablepilupslspab th {
        font-size: 13px;
    }
    #dtablepilupslspab td { 
        font-size: 11px;
    }
    .imgzoom:hover {
        -ms-transform: scale(3.5); /* IE 9 */
        -webkit-transform: scale(3.5); /* Safari 3-8 */
        transform: scale(3.5);
        
    }
</style>

    <div class='col-md-12 col-sm-12 col-xs-12'>
        <div class='x_panel'>

                <div class='col-sm-4'>
                    <small>&nbsp;</small>
                   <div class="form-group">
                       <!--<button type='button' class='btn btn-success btn-xs' onclick='self.history.back()'>Back</button>-->
                       <?PHP
                       echo "<a class='btn btn-success' id='butbacnk' href='?module=slsuploadsalespabrik&idmenu=354&act=354'>Back</a>";
                       ?>
                   </div>
               </div>

        </div>


    </div>

<form method='POST' action='<?PHP echo "?module='slsuploadsalespabrikpros'&act=input&idmenu=354"; ?>' id='d-form2' name='form2' data-parsley-validate class='form-horizontal form-label-left'>
    
    <div class='x_content'>
        <table id='dtablepilupslspab' class='table table-striped table-bordered' width="100%" border="1px solid black">
            <thead>
                <tr>
                    <th width='10px'>No</th>
                    <th align="center" nowrap>Bukti / No. Faktur</th>
                    <th align="center" nowrap>Tgl Faktur</th>
                    <th align="center" nowrap>Kode Customer</th>
                    <th align="center" nowrap>Nama Customer</th>
                    <th align="center" nowrap>Alamat Customer</th>
                    <th align="center" nowrap>Kota</th>
                    <th align="center" nowrap>Kode Barang</th>
                    <th align="center" nowrap>Nama Barang</th>
                    <th align="center" nowrap>No. Batch</th>
                    <th align="center" nowrap>Kuantitas</th>
                    <th align="center" nowrap>Kuantitas Bonus</th>
                    <th align="center" nowrap>Nilai Bonus</th>
                    <th align="center" nowrap>Harga</th>
                    <th align="center" nowrap>Disc %</th>
                    <th align="center" nowrap>Disc Rp.</th>
                    <th align="center" nowrap>Jumlah Rp.</th>
                    <th align="center" nowrap>Disc Tambah %</th>
                    <th align="center" nowrap>Disc Tambah Rp.</th>
                    <th align="center" nowrap>Jumlah Netto</th>
                </tr>
            </thead>
            <tbody>
                
            </tbody>
        </table>
    </div>
</form>