<?PHP
    session_start();
    
    include "../../config/fungsi_sql.php";
    
    $pmodule=$_GET['module'];
    $pact=$_GET['act'];
    $pidmenu=$_GET['idmenu'];
    $pidcabang=$_POST['ucab'];
    
    $pnamacab=getfieldcnit("select nama as lcfields from MKT.icabang WHERE icabangid='$pidcabang'");
    echo "<input type='hidden' name='txt_idcab' id='txt_idcab' value='$pidcabang'>";
    
    $pnamacabang="";
    if (!empty($pidcabang)) {
        $pnamacabang=$pnamacab." &nbsp; (".(INT)$pidcabang.")";
    }
    
?>


<script>
    $(document).ready(function() {
        var aksi = "module/mst_pindahcabareacust/aksi_pindahcabareacust.php";
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var idmenu = urlku.searchParams.get("idmenu");
        var nmun = urlku.searchParams.get("nmun");
        var icab=document.getElementById('txt_idcab').value;
        
        
        var dataTable = $('#datatblarea').DataTable( {
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
                url :"module/mst_pindahcabareacust/mydataarea.php?module="+module+"&idmenu="+idmenu+"&nmun="+nmun+"&aksi="+aksi+"&ucab="+icab, //+"&uidjabatan="+eidjabatan // json datasource
                type: "post",  // method  , by default get
                data:"module="+module,
                error: function(){  // error handling
                    $(".data-grid-error").html("");
                    $("#datatable").append('<tbody class="data-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                    $("#data-grid_processing").css("display","none");

                }
            }
        } );
    } );
</script>


<div class="page-title">
    <h1 style="font-size:15px; font-weight: bold;">
        <?PHP echo "Data Area -> Cabang&nbsp; $pnamacabang"; ?>
    </h1>
</div>
<div class="clearfix"></div>

<form method='POST' action='<?PHP echo "?module='$pmodule'&act=$pact&idmenu=$pidmenu"; ?>' 
      id='d-form2' name='form2' data-parsley-validate class='form-horizontal form-label-left'>
    
    <div class='x_content'>
        <table id='datatblarea' class='table table-striped table-bordered' width='100%'>
            <thead>
                <tr>
                    <th width='10px'>No</th>
                    <th width='10px'>&nbsp;</th>
                    <th width='30px'>Id Area</th>
                    <th width='10px'>Nama Area</th>
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
    #datatblarea th {
        font-size: 13px;
    }
    #datatblarea td { 
        font-size: 11px;
    }
</style>


<script>
    $(document).ready(function() {
        var icab=document.getElementById('txt_idcab').value;
        TampilkanDataCust(icab, '');
    } );
    
    function TampilkanDataCust(icab, idarea){
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var idmenu = urlku.searchParams.get("idmenu");
        var act = urlku.searchParams.get("act");
        
        $("#loading3").html("<center><img src='images/loading.gif' width='50px'/></center>");
        $.ajax({
            type:"post",
            url:"module/mst_pindahcabareacust/viewdatatabelecust.php?module="+module+"&idmenu="+idmenu+"&act="+act,
            data:"module="+module+"&ucab="+icab+"&uidarea="+idarea,
            success:function(data){
                $("#c-data3").html(data);
                $("#loading3").html("");
            }
        });
    }
</script>