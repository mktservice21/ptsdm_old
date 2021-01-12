<?PHP
    session_start();
    //$pjabatan=$_POST['ujabatan'];
   
    $pmodule=$_GET['module'];
    $pact=$_GET['act'];
    $pidmenu=$_GET['idmenu'];

    //echo "<input type='hidden' name='cb_idjabatan' id='cb_idjabatan' value='$pjabatan'>";
    
?>


<script>
    $(document).ready(function() {
        var aksi = "module/ks_isiestimasiki/aksi_isiestimasiki.php";
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var idmenu = urlku.searchParams.get("idmenu");
        var nmun = urlku.searchParams.get("nmun");
        //var eidjabatan=document.getElementById('cb_idjabatan').value;
        
        
        var dataTable = $('#datatableotldpl').DataTable( {
            "processing": true,
            "serverSide": true,
            "stateSave": true,
            "order": [[ 0, "desc" ]],
            "lengthMenu": [[10, 50, 100, 10000000], [10, 50, 100, "All"]],
            "displayLength": 10,
            "columnDefs": [
                { "visible": false },
                { "orderable": false, "targets": 0 },
                { "orderable": false, "targets": 1 },
                { className: "text-right", "targets": [0,5,6,7,8,9] },//right
                { className: "text-nowrap", "targets": [0, 1, 2, 3, 4, 5, 6, 7, 8,9] }//nowrap

            ],
            "language": {
                "zeroRecords": "Lihat Page di bawah!!! Jika ada Page, Pilih Page 1...!!! Jika tidak ada Page, maka data KOSONG..."
            },
            "scrollX": true,

            "ajax":{
                url :"module/ks_isiestimasiki/mydataestki.php?module="+module+"&idmenu="+idmenu+"&nmun="+nmun+"&aksi="+aksi, //+"&uidjabatan="+eidjabatan // json datasource
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



<form method='POST' action='<?PHP echo "?module='$pmodule'&act=$pact&idmenu=$pidmenu"; ?>' 
      id='d-form2' name='form2' data-parsley-validate class='form-horizontal form-label-left'>
    
    <div class='x_content'>
        <table id='datatableotldpl' class='table table-striped table-bordered' width='100%'>
            <thead>
                <tr>
                    <th width='10px'>No</th>
                    <th width='10px'></th>
                    <th width='30px'>Bulan</th>
                    <th width='10px'>MR</th>
                    <th width='50px'>Dokter</th>
                    <th width='60px'>Jumlah KI Rp.</th>
                    <th width='40px'>Est. Sales Per Bulan Rp.</th>
                    <th width='30px'>Est. ROI</th>
                    <th width='20px'>Perkiraan Sls 6 Bulan Rp.</th>
                    <th width='20px'>ROI</th>
                </tr> 
            </thead>
        </table>
    </div>
    
</form>


<style>
    .divnone {
        display: none;
    }
    #datatableotldpl th {
        font-size: 13px;
    }
    #datatableotldpl td { 
        font-size: 11px;
    }
</style>