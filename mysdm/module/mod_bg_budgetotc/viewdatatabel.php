<?PHP
    session_start();
    $_SESSION['DBPERENTY1']=$_POST['uperiode1'];
    $_SESSION['DBPERENTY2']=$_POST['uperiode2'];
    
    
    $date1=$_POST['uperiode1'];
    $date2=$_POST['uperiode2'];
    $tgl1= $date1;//date("Y-m-d", strtotime($date1));
    $tgl2= date("Y-m-d", strtotime($date2));
    
    echo "<input type='hidden' name='xtgl1' id='xtgl1' value='$tgl1'>";
    echo "<input type='hidden' name='xtgl2' id='xtgl2' value='$tgl2'>";
    

?>
    
<script>
    $(document).ready(function() {
        var aksi = "module/mod_bg_budgetotc/aksi_budgetteamotc.php";
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var idmenu = urlku.searchParams.get("idmenu");
        var nmun = urlku.searchParams.get("nmun");
        var etgl1 = document.getElementById("xtgl1").value;
        var etgl2 = document.getElementById("xtgl2").value;
        
        //alert(etgl1);
        var dataTable = $('#datatabledg').DataTable( {
            "processing": true,
            "serverSide": true,
            //"stateSave": true,
            "order": [[ 1, "asc" ]],
            "lengthMenu": [[10, 50, 100, 10000000], [10, 50, 100, "All"]],
            "displayLength": 10,
            "columnDefs": [
                { "visible": false },
                { "orderable": false, "targets": 0 },
                { "orderable": false, "targets": 1 },
                { className: "text-right", "targets": [5] },//right
                { className: "text-nowrap", "targets": [0, 1, 2, 3, 4, 5] }//nowrap

            ],
            "language": {
                "zeroRecords": "Lihat Page di bawah!!! Jika ada Page, Pilih Page 1...!!! Jika tidak ada Page, maka data KOSONG..."
            },
            "scrollY": 460,
            "scrollX": true,

            "ajax":{
                url :"module/mod_bg_budgetotc/mydata.php?module="+module+"&idmenu="+idmenu+"&nmun="+nmun+"&aksi="+aksi+"&uperiode1="+etgl1+"&uperiode2="+etgl2, // json datasource
                type: "post",  // method  , by default get
                data:"uperiode1="+etgl1+"&uperiode2="+etgl2,
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

<script>
    function ProsesData(ket, noid){

        ok_ = 1;
        if (ok_) {
            var r = confirm('Apakah akan melakukan proses '+ket+' ...?');
            if (r==true) {

                var myurl = window.location;
                var urlku = new URL(myurl);
                var module = urlku.searchParams.get("module");
                var idmenu = urlku.searchParams.get("idmenu");

                //document.write("You pressed OK!")
                document.getElementById("d-form2").action = "module/mod_bg_budgetotc/aksi_budgetteamotc.php?module="+module+"&idmenu="+idmenu+"&act=hapus&kethapus="+"&ket="+ket+"&id="+noid;
                document.getElementById("d-form2").submit();
                return 1;
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }



    }
</script>

<style>
    .divnone {
        display: none;
    }
    #datatabledg th {
        font-size: 13px;
    }
    #datatabledg td { 
        font-size: 11px;
    }
</style>

<form method='POST' action='<?PHP echo "?module='realisasibudgetmarketing'&act=input&idmenu=218"; ?>' id='d-form2' name='form2' data-parsley-validate class='form-horizontal form-label-left'>
    <input type='hidden' id='u_module' name='u_module' value='realisasibudgetmarketing' Readonly>
    <input type='hidden' id='u_idmenu' name='u_idmenu' value='218' Readonly>
    <input type='hidden' id='u_act' name='u_act' value='hapus' Readonly>
    
    <div class='x_content'>
        <table id='datatabledg' class='table table-striped table-bordered' width='100%'>
            <thead>
                <tr>
                    <th width='7px'>NO</th>
                    <th width='50px'>AKSI</th>
                    <th width='40px'>TAHUN</th>
                    <th width='80px'>DIVISI</th>
                    <th width='80px'>NAMA</th>
                    <th width='40px'>JUMLAH</th>
                </tr>
            </thead>
        </table>

    </div>
    
</form>