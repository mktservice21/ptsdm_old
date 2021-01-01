<?php

    session_start();
    $pdate1=$_POST['uperiode1'];
    $pdate2=$_POST['uperiode2'];
    
    $_SESSION['FDTBRCABTGL1']=$pdate1;
    $_SESSION['FDTBRCABTGL2']=$pdate2;
    
    $pmodule=$_GET['module'];
    $pact=$_GET['act'];
    $pidmenu=$_GET['idmenu'];
    
    $ptgl1= date("Y-m-d", strtotime($pdate1));
    $ptgl2= date("Y-m-d", strtotime($pdate2));
    
    echo "<input type='hidden' name='e_tgl1' id='e_tgl1' value='$ptgl1'>";
    echo "<input type='hidden' name='e_tgl2' id='e_tgl2' value='$ptgl2'>";
    
?>


<script>
    $(document).ready(function() {
        var aksi = "module/mod_br_entrybrdcccab/aksi_entrybrdcccab.php";
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var idmenu = urlku.searchParams.get("idmenu");
        var nmun = urlku.searchParams.get("nmun");
        var etgl1 = document.getElementById("e_tgl1").value;
        var etgl2 = document.getElementById("e_tgl2").value;
        
        var dataTable = $('#datatablebrcabdssdcc').DataTable( {
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
                { className: "text-right", "targets": [6] },//right
                { className: "text-nowrap", "targets": [0, 1, 2, 3, 4, 5, 6, 7, 8,9,10] }//nowrap

            ],
            "language": {
                "zeroRecords": "Lihat Page di bawah!!! Jika ada Page, Pilih Page 1...!!! Jika tidak ada Page, maka data KOSONG..."
            },
            "scrollY": 460,
            "scrollX": true,

            "ajax":{
                url :"module/mod_br_entrybrdcccab/mydata.php?module="+module+"&idmenu="+idmenu+"&nmun="+nmun+"&aksi="+aksi+"&uperiode1="+etgl1+"&uperiode2="+etgl2, // json datasource
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

<style>
    .divnone {
        display: none;
    }
    #datatablebrcabdssdcc th {
        font-size: 13px;
    }
    #datatablebrcabdssdcc td { 
        font-size: 11px;
    }
    .imgzoom:hover {
        -ms-transform: scale(3.5); /* IE 9 */
        -webkit-transform: scale(3.5); /* Safari 3-8 */
        transform: scale(3.5);
        
    }
</style>

<form method='POST' action='<?PHP echo "?module='$pmodule'&act=$pact&idmenu=$pidmenu"; ?>' id='d-form2' name='form2' data-parsley-validate class='form-horizontal form-label-left'>
    <div class='x_content'>
        <table id='datatablebrcabdssdcc' class='table table-striped table-bordered' width='100%'>
            <thead>
                <tr>
                    <th width='7px'>No</th>
                    <th width='70px'></th>
                    <th width='40px'>ID</th>
                    <th width='80px'>Tanggal</th>
                    <th width='40px'>Yang Membuat</th>
                    <th width='40px'>Dokter</th>
                    <th width='50px'>Jumlah</th>
                    <th width='40px'>Cabang</th>
                    <th width='40px'>Tgl. Issued</th>
                    <th width='40px'>Tgl. Booking</th>
                    <th width='50px'>Keterangan</th>
                </tr>
            </thead>
        </table>

    </div>    
</form>

<script>
    function ProsesData(ket, noid){

        ok_ = 1;
        if (ok_) {
            var r = confirm('Apakah akan melakukan proses '+ket+' ...?');
            if (r==true) {

                var txt;
                if (ket=="reject" || ket=="hapus" || ket=="pending") {
                    var textket = prompt("Masukan alasan "+ket+" : ", "");
                    if (textket == null || textket == "") {
                        txt = textket;
                    } else {
                        txt = textket;
                    }
                }
                
                var myurl = window.location;
                var urlku = new URL(myurl);
                var module = urlku.searchParams.get("module");
                var idmenu = urlku.searchParams.get("idmenu");

                //document.write("You pressed OK!")
                document.getElementById("d-form2").action = "module/mod_br_entrybrdcccab/aksi_entrybrdcccab.php?module="+module+"&idmenu="+idmenu+"&act="+ket+"&kethapus="+txt+"&ket="+ket+"&id="+noid+"&utxt="+txt;
                document.getElementById("d-form2").submit();
                return 1;
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }
    }
</script>