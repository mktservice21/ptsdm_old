<?PHP
    session_start();
    
    include "../../config/fungsi_sql.php";
    
    $pmodule=$_GET['module'];
    $pact=$_GET['act'];
    $pidmenu=$_GET['idmenu'];
    $pidcabang=$_POST['ucab'];
    $pidarea=$_POST['uidarea'];
    
    $pnamacab=getfieldcnnew("select nama as lcfields from sls.icabang WHERE icabangid='$pidcabang'");
    $pnmarea=getfieldcnnew("select nama as lcfields from sls.iarea WHERE icabangid='$pidcabang' AND areaid='$pidarea'");
    
    echo "<input type='hidden' name='txt_idcab' id='txt_idcab' value='$pidcabang'>";
    echo "<input type='hidden' name='txt_idarea' id='txt_idarea' value='$pidarea'>";
    
    $pnamacabang="";
    $pnamaarea="";
    
    if (!empty($pidcabang)) {
        $pnamacabang=$pnamacab." (".(INT)$pidcabang.")";
    }
    
    if (!empty($pidarea)) {
        $pnamaarea=$pnmarea." (".(INT)$pidarea.")";
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
        var idarea=document.getElementById('txt_idarea').value;
        
        
        var dataTable = $('#datatblcust').DataTable( {
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
                { className: "text-nowrap", "targets": [0, 1, 2, 3,4,5,6,7] }//nowrap

            ],
            "language": {
                "zeroRecords": "Lihat Page di bawah!!! Jika ada Page, Pilih Page 1...!!! Jika tidak ada Page, maka data KOSONG..."
            },
            "scrollX": true,

            "ajax":{
                url :"module/mst_pindahcabareacust/mydatacust.php?module="+module+"&idmenu="+idmenu+"&nmun="+nmun+"&aksi="+aksi+"&ucab="+icab+"&uidarea="+idarea, //+"&uidjabatan="+eidjabatan // json datasource
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
        <?PHP echo "Data Customer (iCust) -> Cabang $pnamacabang"; ?>
    </h1>
</div>
<div class="clearfix"></div>

<form method='POST' action='<?PHP echo "?module='$pmodule'&act=$pact&idmenu=$pidmenu"; ?>' 
      id='d-form2' name='form2' data-parsley-validate class='form-horizontal form-label-left'>
    
    <div class='x_content'>
        <table id='datatblcust' class='table table-striped table-bordered' width='100%'>
            <thead>
                <tr>
                    <th width='10px'>No</th>
                    <th width='10px'>&nbsp;</th>
                    <th width='10px'>Id Area</th>
                    <th width='30px'>Nama Area</th>
                    <th width='10px'>ID Cust</th>
                    <th width='30px'>Nama Cust</th>
                    <th width='30px'>Alamat</th>
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
    #datatblcust th {
        font-size: 13px;
    }
    #datatblcust td { 
        font-size: 11px;
    }
</style>


<script>
    $(document).ready(function() {
        var icab=document.getElementById('txt_idcab').value;
        var iidarea=document.getElementById('txt_idarea').value;
        
        TampilkanDataEcust(icab, iidarea, '');
    } );
    
    function TampilkanDataEcust(icab, idarea, icust){
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var idmenu = urlku.searchParams.get("idmenu");
        var act = urlku.searchParams.get("act");
        
        $("#loading4").html("<center><img src='images/loading.gif' width='50px'/></center>");
        $.ajax({
            type:"post",
            url:"module/mst_pindahcabareacust/viewdatatabelecust_e.php?module="+module+"&idmenu="+idmenu+"&act="+act,
            data:"module="+module+"&ucab="+icab+"&uidarea="+idarea+"&uicust="+icust,
            success:function(data){
                $("#c-data4").html(data);
                $("#loading4").html("");
            }
        });
    }
</script>