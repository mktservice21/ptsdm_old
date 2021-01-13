<?PHP
    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","500M");
    ini_set('max_execution_time', 0);
    
    session_start();
    //$pjabatan=$_POST['ujabatan'];
   
    $pmodule=$_GET['module'];
    $pact=$_GET['act'];
    $pidmenu=$_GET['idmenu'];

    //echo "<input type='hidden' name='cb_idjabatan' id='cb_idjabatan' value='$pjabatan'>";
    
?>


<script>
    $(document).ready(function() {
        var aksi = "module/mst_pindahcabareacust/aksi_pindahcabareacust.php";
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var idmenu = urlku.searchParams.get("idmenu");
        var nmun = urlku.searchParams.get("nmun");
        //var eidjabatan=document.getElementById('cb_idjabatan').value;
        
        
        var dataTable = $('#datatblcab').DataTable( {
            "processing": true,
            "serverSide": true,
            //"stateSave": true,
            "order": [[ 0, "desc" ]],
            "lengthMenu": [[10, 50, 100, 10000000], [10, 50, 100, "All"]],
            "displayLength": 10,
            "columnDefs": [
                { "visible": false },
                //{ "orderable": false, "targets": 0 },
                //{ "orderable": false, "targets": 1 },
                //{ className: "text-right", "targets": [0,5,6,7,8,9] },//right
                { className: "text-nowrap", "targets": [0, 1, 2, 3,4] }//nowrap

            ],
            "language": {
                "zeroRecords": "Lihat Page di bawah!!! Jika ada Page, Pilih Page 1...!!! Jika tidak ada Page, maka data KOSONG..."
            },
            "scrollX": true,

            "ajax":{
                url :"module/mst_pindahcabareacust/mydatacab.php?module="+module+"&idmenu="+idmenu+"&nmun="+nmun+"&aksi="+aksi, //+"&uidjabatan="+eidjabatan // json datasource
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
        <table id='datatblcab' class='table table-striped table-bordered' width='100%'>
            <thead>
                <tr>
                    <th width='10px'>No</th>
                    <th width='10px'>&nbsp;</th>
                    <th width='30px'>Id Cabang</th>
                    <th width='10px'>Nama Cabang</th>
                    <th width='10px'>Aktif</th>
                </tr> 
            </thead>
        </table>
    </div>
    
</form>


<style>
    .divnone {
        display: none;
    }
    #datatblcab th {
        font-size: 13px;
    }
    #datatblcab td { 
        font-size: 11px;
    }
</style>


<script>
    $(document).ready(function() {
        TampilkanDataArea('');
    } );
    
    function TampilkanDataArea(icab){
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var idmenu = urlku.searchParams.get("idmenu");
        var act = urlku.searchParams.get("act");
        
        $("#loading2").html("<center><img src='images/loading.gif' width='50px'/></center>");
        $.ajax({
            type:"post",
            url:"module/mst_pindahcabareacust/viewdatatabelearea.php?module="+module+"&idmenu="+idmenu+"&act="+act,
            data:"module="+module+"&ucab="+icab,
            success:function(data){
                $("#c-data2").html(data);
                $("#loading2").html("");
            }
        });
    }
</script>