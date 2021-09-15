<?PHP
    session_start();
    
    
    $pjabatanid=$_POST['ujabatanid'];
    
    
    $pmodule=$_GET['module'];
    $pact=$_GET['act'];
    $pidmenu=$_GET['idmenu'];

    echo "<input type='hidden' name='cb_jabatan' id='cb_jabatan' value='$pjabatanid'>";
    
?>

<script>
    $(document).ready(function() {
        var aksi = "module/purchasing/pch_barang/aksi_pchbarang.php";
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var idmenu = urlku.searchParams.get("idmenu");
        var nmun = urlku.searchParams.get("nmun");
        var ejabatanid=document.getElementById('cb_jabatan').value;
        
        var dataTable = $('#datatablerstpas').DataTable( {
            "processing": true,
            "serverSide": true,
            //"stateSave": true,
            "order": [[ 1, "asc" ]],
            "lengthMenu": [[10, 50, 100, 10000000], [10, 50, 100, "All"]],
            "displayLength": 10,
            "columnDefs": [
                { "visible": false },
                { "orderable": false, "targets": 0 },
                { "orderable": true, "targets": 1 },
                { "orderable": true, "targets": 2 },
                { "orderable": true, "targets": 4 },
                //{ className: "text-right", "targets": [4] },//right
                { className: "text-nowrap", "targets": [0, 1, 2, 3, 4, 5, 6, 7,8,9] }//nowrap

            ],
            "language": {
                "zeroRecords": "Lihat Page di bawah!!! Jika ada Page, Pilih Page 1...!!! Jika tidak ada Page, maka data KOSONG..."
            },
            "scrollY": 460,
            "scrollX": true,

            "ajax":{
                url :"module/tools/mod_tools_resetpass/mydata_rstpas.php?module="+module+"&idmenu="+idmenu+"&nmun="+nmun+"&aksi="+aksi+"&ujabatanid="+ejabatanid, // json datasource
                type: "post",  // method  , by default get
                data:"ujabatanid="+ejabatanid,
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

<form method='POST' action='<?PHP echo "?module='$pmodule'&act=$pact&idmenu=$pidmenu"; ?>' id='form_data2' name='form2' data-parsley-validate class='form-horizontal form-label-left'>
    
    <div class='x_content'>
        <table id='datatablerstpas' class='table table-striped table-bordered' width='100%'>
            <thead>
                <tr>
                    <th width='50px'></th>
                    <th width='10px'>Karyawan ID</th>
                    <th width='200px'>PIN/Password</th>
                    <th width='10px'>Nama</th>
                    <th width='40px'>Tgl. Ubah Pass</th>
                    <th width='10px'>Username</th>
                    <th width='40px'>Jabatan</th>
                    <th width='40px'>Cabang</th>
                    <th width='40px'>Tgl. Masuk</th>
                    <th width='40px'>Tgl. Keluar</th>
                </tr>
            </thead>
        </table>

    </div>
    
</form>

<style>
    .divnone {
        display: none;
    }
    #datatablerstpas th {
        font-size: 13px;
    }
    #datatablerstpas td { 
        font-size: 11px;
    }
</style>