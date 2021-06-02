<?PHP
    session_start();
    
    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","512M");
    ini_set('max_execution_time', 0);
    
    $pdatainp1=$_POST['udata1'];
    $pdatainp2=$_POST['udata2'];
    $pdatainp3=$_POST['udata3'];
    
    $pidinput=$_POST['uidinput'];
    $paksi=$_POST['uaksi'];
    
    echo "<input type='hidden' name='e_data1' id='e_data1' value='$pdatainp1'>";
    echo "<input type='hidden' name='e_data2' id='e_data2' value='$pdatainp2'>";
    echo "<input type='hidden' name='e_data3' id='e_data3' value='$pdatainp3'>";
    echo "<input type='hidden' name='e_id' id='e_id' value='$pidinput'>";
    echo "<input type='hidden' name='e_aksi' id='e_aksi' value='$paksi'>";
    
    $pmodule=$_GET['module'];
    $pidmenu=$_GET['idmenu'];
    $pact="input";
    
?>


<script>
    $(document).ready(function() {
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var idmenu = urlku.searchParams.get("idmenu");
        var nmun = urlku.searchParams.get("nmun");
        var eidinput =document.getElementById('e_id').value;
        var edata1 =document.getElementById('e_data1').value;
        var edata2 =document.getElementById('e_data2').value;
        var edata3 =document.getElementById('e_data3').value;
        var eaksi = document.getElementById("e_aksi").value;

        
        var dataTable = $('#mytable').DataTable( {
            "processing": true,
            "serverSide": true,
            //"stateSave": true,
            "order": [[ 1, "desc" ]],
            "lengthMenu": [[10, 50, 100, 10000000], [10, 50, 100, "All"]],
            "displayLength": 10,
            "columnDefs": [
                { "visible": false },
                { "orderable": false, "targets": 0 },
                //{ "orderable": false, "targets": 1 },
                //{ "orderable": false, "targets": 2 },
                //{ "orderable": false, "targets": 3 },
                //{ className: "text-right", "targets": [6] },//right
                { className: "text-nowrap", "targets": [0, 1, 2, 3] }//nowrap

            ],
            "language": {
                "zeroRecords": "Lihat Page di bawah!!! Jika ada Page, Pilih Page 1...!!! Jika tidak ada Page, maka data KOSONG..."
            },
            "scrollY": 340,
            "scrollX": true,

            "ajax":{
                url :"module/purchasing/pch_terimabarangpo/viewdata_po3.php?module="+module+"&idmenu="+idmenu+"&nmun="+nmun+"&aksi="+eaksi+"&uidinput="+eidinput+"&udata1="+edata1+"&udata2="+edata2+"&udata3="+edata3, // json datasource
                type: "post",  // method  , by default get
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
    #mytable th {
        font-size: 13px;
    }
    #mytable td { 
        font-size: 11px;
    }
</style>

    
<form method='POST' action='<?PHP echo "?module=$pmodule&act=$pact&idmenu=$pidmenu"; ?>' 
      id='d-form2' name='form2' data-parsley-validate class='form-horizontal form-label-left'>
    <div class='x_content'>
        <table id='mytable' class='table table-striped table-bordered' width='100%'>
            <thead>
                <tr>
                    <th width='10px'>No</th>
                    <th width='40px'>ID PO</th>
                    <th width='80px'>Tgl. Input</th>
                    <th width='30px'>Vendor</th>
                </tr>
            </thead>
        </table>

    </div>
</form>
    
