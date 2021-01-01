<?PHP
    session_start();
    
    $_SESSION['STPDTIPEINC']="B";
    
    $_SESSION['STPDPERENTYINC1']=$_POST['uperiode1'];
    $_SESSION['STPDPERENTYINC2']=$_POST['uperiode2'];
    
    $date1=$_POST['uperiode1'];
    $date2=$_POST['uperiode2'];
    $tgl1= date("Y-m-d", strtotime($date1));
    $tgl2= date("Y-m-d", strtotime($date2));
    
    $paksi=$_POST['uaksi'];
    
    echo "<input type='hidden' name='e_tgl1' id='e_tgl1' value='$tgl1'>";
    echo "<input type='hidden' name='e_tgl2' id='e_tgl2' value='$tgl2'>";
    echo "<input type='hidden' name='e_aksi' id='e_aksi' value='$paksi'>";
    
?>

<script>
    $(document).ready(function() {
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var idmenu = urlku.searchParams.get("idmenu");
        var nmun = urlku.searchParams.get("nmun");
        var etgl1 = document.getElementById("e_tgl1").value;
        var etgl2 = document.getElementById("e_tgl2").value;
        var eaksi = document.getElementById("e_aksi").value;
        
        //alert(etgl1);
        var dataTable = $('#datatablespd').DataTable( {
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
                { className: "text-right", "targets": [9] },//right
                { className: "text-nowrap", "targets": [0, 1, 2, 3, 4, 5, 6, 7, 8,9] }//nowrap

            ],
            "language": {
                "zeroRecords": "Lihat Page di bawah!!! Jika ada Page, Pilih Page 1...!!! Jika tidak ada Page, maka data KOSONG..."
            },
            "scrollY": 460,
            "scrollX": true,

            "ajax":{
                url :"module/mod_br_spdincentive/mydata.php?module="+module+"&idmenu="+idmenu+"&nmun="+nmun+"&aksi="+eaksi+"&uperiode1="+etgl1+"&uperiode2="+etgl2, // json datasource
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
                document.getElementById("d-form2").action = "module/mod_br_spdincentive/aksi_spdincentive.php?module="+module+"&idmenu="+idmenu+"&act=hapus&kethapus="+"&ket="+ket+"&id="+noid;
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
    #datatablespd th {
        font-size: 13px;
    }
    #datatablespd td { 
        font-size: 11px;
    }
</style>

<form method='POST' action='<?PHP echo "?module='saldosuratdana'&act=input&idmenu=149"; ?>' id='d-form2' name='form2' data-parsley-validate class='form-horizontal form-label-left'>
    <div class='x_content'>
        <table id='datatablespd' class='table table-striped table-bordered' width='100%'>
            <thead>
                <tr>
                    <th width='7px'>No</th>
                    <th width='50px'></th>
                    <th width='40px'>Divisi</th>
                    <th width='80px'>Kode</th>
                    <th width='50px'>Sub</th>
                    <th width='80px'>No. SPD</th>
                    <th width='80px'>Tgl. Pengajuan</th>
                    <th width='40px'>Bulan</th>
                    <th width='50px'>No. BR</th>
                    <th width='50px'>Jumlah</th>
                </tr>
            </thead>
        </table>

    </div>
</form>