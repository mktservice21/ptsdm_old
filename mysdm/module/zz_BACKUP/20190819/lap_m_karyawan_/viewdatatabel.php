<?PHP
    session_start();
    
    $_SESSION['FMSTJBT']=$_POST['ujabatan'];
    $_SESSION['FMSTDIV']=$_SESSION['DIVISI'];
    
    $jabatan=$_POST['ujabatan'];
    $divisi=$_SESSION['DIVISI'];
    
    echo "<input type='hidden' name='e_jabatan' id='e_jabatan' value='$jabatan'>";
    echo "<input type='hidden' name='e_divisi' id='e_divisi' value='$divisi'>";
    

?>
    
<script>
    $(document).ready(function() {
        var aksi = "module/mod_br_brrutin/aksi_brrutin.php";
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var idmenu = urlku.searchParams.get("idmenu");
        var nmun = urlku.searchParams.get("nmun");
        var ejabatan=document.getElementById('e_jabatan').value;
        var edivisi=document.getElementById('e_divisi').value;
        
        //alert(edivisi);
        var dataTable = $('#datatablerut').DataTable( {
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
                { className: "text-right", "targets": [0] },//right
                { className: "text-nowrap", "targets": [0, 1, 2, 3, 4, 5, 6, 7, 8,9,10] }//nowrap

            ],
            "language": {
                "zeroRecords": "Lihat Page di bawah!!! Jika ada Page, Pilih Page 1...!!! Jika tidak ada Page, maka data KOSONG..."
            },
            "scrollY": 350,
            "scrollX": true,

            "ajax":{
                url :"module/lap_m_karyawan/mydata.php?module="+module+"&idmenu="+idmenu+"&nmun="+nmun+"&aksi="+aksi+"&ujabatan="+ejabatan+"&udivisi="+edivisi, // json datasource
                type: "post",  // method  , by default get
                data:"ujabatan="+ejabatan+"&udivisi="+edivisi,
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
    #datatablerut th {
        font-size: 13px;
    }
    #datatablerut td { 
        font-size: 11px;
    }
    .imgzoom:hover {
        -ms-transform: scale(3.5); /* IE 9 */
        -webkit-transform: scale(3.5); /* Safari 3-8 */
        transform: scale(3.5);
        
    }
</style>

<form method='POST' action='<?PHP echo "?module='datakaryawan'&act=input&idmenu=94"; ?>' id='d-form2' name='form2' data-parsley-validate class='form-horizontal form-label-left'>
    
    
    <input type='hidden' id='u_module' name='u_module' value='' Readonly>
    <input type='hidden' id='u_idmenu' name='u_idmenu' value='' Readonly>
    <input type='hidden' id='u_act' name='u_act' value='input' Readonly>
    
    <div class='x_content'>
        <table id='datatablerut' class='table table-striped table-bordered' width='100%'>
            <?PHP
            echo "<thead><tr><th width='10px'>No</th><th width='10px'>ID</th><th width='10px'>PIN</th><th width='100px'>Karyawan</th>"
                    . "<th width='100px'>Tempat</th><th width='100px'>Tgl. Lahir</th>"
                    . "<th width='20px'>Jabatan</th><th width='100px'>Atasan</th>"
                    . "<th width='20px'>Tgl Masuk</th><th width='20px'>Tgl Keluar</th><th width='10px'>Divisi</th>"
                    . "</tr></thead>";
            ?>
        </table>

    </div>
</form>