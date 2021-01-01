<?PHP
    session_start();
    $udm=$_POST['udm'];
    $uspv=$_POST['uspv'];
    
    echo "<input type='hidden' name='cb_dm' id='cb_dm' value='$udm'>";
    echo "<input type='hidden' name='cb_spv' id='cb_spv' value='$uspv'>";

?>
    
<script>
    $(document).ready(function() {
        var aksi = "module/md_m_penempatanspv/aksi_penempatanspv.php";
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var idmenu = urlku.searchParams.get("idmenu");
        var nmun = urlku.searchParams.get("nmun");
        var edm=document.getElementById('cb_dm').value;
        var espv=document.getElementById('cb_spv').value;
        
        //alert(etgl2);
        var dataTable = $('#datatable').DataTable( {
            "processing": true,
            "serverSide": true,
			"stateSave": true,
            "order": [[ 0, "desc" ]],
            "lengthMenu": [[10, 50, 100, 10000000], [10, 50, 100, "All"]],
            "displayLength": 10,
            "columnDefs": [
                //{ "visible": false },
                //{ className: "text-right", "targets": [0] },//right
                { className: "text-nowrap", "targets": [0, 1, 2, 3, 4, 5, 6, 7] }//nowrap

            ],

            "ajax":{
                url :"module/md_m_penempatanspv/mydata.php?module="+module+"&idmenu="+idmenu+"&nmun="+nmun+"&aksi="+aksi+"&udm="+edm+"&uspv="+espv, // json datasource
                type: "post",  // method  , by default get
                data:"udm="+edm,
                error: function(){  // error handling
                    $(".data-grid-error").html("");
                    $("#datatable").append('<tbody class="data-grid-error"><tr><th colspan="7">No data found in the server</th></tr></tbody>');
                    $("#data-grid_processing").css("display","none");

                }
            }
        } );
    } );
</script>

<style>
    .divnone {
        display: none;
    }
    #datatable th {
        font-size: 12px;
    }
    #datatable td { 
        font-size: 11px;
    }
</style>

<div class='x_content'>

    <table id='datatable' class='table table-striped table-bordered' width="100%">
        <thead>
            <tr>
                <th width='7px'>No</th><th width='50px'>Aksi</th>
                <th width='60px'>Cabang</th><th width='50px'>Area</th><th>Divisi</th>
                <th>Karyawan</th><th width='50px'>Tanggal</th><th width='50px'>Aktif</th>

            </tr>
        </thead>
    </table>
</div>