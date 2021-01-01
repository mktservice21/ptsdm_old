<?PHP
    session_start();
    
    
    $pjabatan=$_POST['ujabatan'];
    
    
    $pmodule=$_GET['module'];
    $pact=$_GET['act'];
    $pidmenu=$_GET['idmenu'];

    echo "<input type='hidden' name='cb_idjabatan' id='cb_idjabatan' value='$pjabatan'>";
    
?>

<script>
    $(document).ready(function() {
        var aksi = "module/mst_isidatakaryawan/aksi_isidatakaryawan.php";
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var idmenu = urlku.searchParams.get("idmenu");
        var nmun = urlku.searchParams.get("nmun");
        var eidjabatan=document.getElementById('cb_idjabatan').value;
        
        
        var dataTable = $('#datatablerut2').DataTable( {
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
                { "orderable": false, "targets": 3 },
                { className: "text-right", "targets": [0] },//right
                { className: "text-nowrap", "targets": [0, 1, 2, 3, 4, 5, 6, 7, 8,9,10,11,12] }//nowrap

            ],
            "language": {
                "zeroRecords": "Lihat Page di bawah!!! Jika ada Page, Pilih Page 1...!!! Jika tidak ada Page, maka data KOSONG..."
            },
            "scrollY": 460,
            "scrollX": true,

            "ajax":{
                url :"module/mst_isidatakaryawan/mydata.php?module="+module+"&idmenu="+idmenu+"&nmun="+nmun+"&aksi="+aksi+"&uidjabatan="+eidjabatan, // json datasource
                type: "post",  // method  , by default get
                data:"uidjabatan="+eidjabatan,
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

<form method='POST' action='<?PHP echo "?module='$pmodule'&act=$pact&idmenu=$pidmenu"; ?>' id='d-form2' name='form2' data-parsley-validate class='form-horizontal form-label-left'>
    
    <div class='x_content'>
        <table id='datatablerut2' class='table table-striped table-bordered' width='100%'>
            <?PHP
            echo "<thead><tr><th width='10px'>No</th><th width='10px'>Edit</th><th width='10px'>ID</th><th width='10px'>PIN</th><th width='100px'>Karyawan</th>"
                    . "<th width='100px'>Tempat</th><th width='100px'>Tgl. Lahir</th>"
                    . "<th width='20px'>Jabatan</th><th width='100px'>Atasan</th>"
                    . "<th width='20px'>Tgl Masuk</th><th width='20px'>Tgl Keluar</th><th width='10px'>Divisi</th><th width='10px'>Status</th>"
                    . "</tr></thead>";
            ?>
        </table>

    </div>
    
</form>

<style>
    .divnone {
        display: none;
    }
    #datatablerut2 th {
        font-size: 13px;
    }
    #datatablerut2 td { 
        font-size: 11px;
    }
</style>