<?PHP
    session_start();
    
    $_SESSION['BMTIPE']="";
    $_SESSION['BMTGLTIPE']=$_POST['utgltipe'];
    $_SESSION['BMPERENTY1']=$_POST['uperiode1'];
    $_SESSION['BMPERENTY2']=$_POST['uperiode2'];
    
    
    $tgltipe=$_POST['utgltipe'];
    $date1=$_POST['uperiode1'];
    $date2=$_POST['uperiode2'];
    $tgl1= date("Y-m-d", strtotime($date1));
    $tgl2= date("Y-m-d", strtotime($date2));
    
    echo "<input type='hidden' name='cb_tgltipe' id='cb_tgltipe' value='$tgltipe'>";
    echo "<input type='hidden' name='xtgl1' id='xtgl1' value='$tgl1'>";
    echo "<input type='hidden' name='xtgl2' id='xtgl2' value='$tgl2'>";
    

?>
    
<script>
    $(document).ready(function() {
        var aksi = "module/md_m_tickets/aksi_tickets.php";
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var idmenu = urlku.searchParams.get("idmenu");
        var nmun = urlku.searchParams.get("nmun");
        var etgltipe=document.getElementById('cb_tgltipe').value;
        var etgl1 = document.getElementById("xtgl1").value;
        var etgl2 = document.getElementById("xtgl2").value;
        
        //alert(etgl1);
        var dataTable = $('#datatablekasbon').DataTable( {
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
                //{ className: "text-right", "targets": [5] },//right
                { className: "text-nowrap", "targets": [0, 1, 2, 3, 4, 5,6] }//nowrap

            ],
            "language": {
                "zeroRecords": "Lihat Page di bawah!!! Jika ada Page, Pilih Page 1...!!! Jika tidak ada Page, maka data KOSONG..."
            },
            "scrollY": 460,
            "scrollX": true,

            "ajax":{
                url :"module/md_m_tickets/mydata.php?module="+module+"&idmenu="+idmenu+"&nmun="+nmun+"&aksi="+aksi+"&utgltipe="+etgltipe+"&uperiode1="+etgl1+"&uperiode2="+etgl2, // json datasource
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
                document.getElementById("d-form2").action = "module/md_m_tickets/aksi_tickets.php?module="+module+"&idmenu="+idmenu+"&act=hapus&kethapus="+"&ket="+ket+"&id="+noid;
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
    #datatablekasbon th {
        font-size: 13px;
    }
    #datatablekasbon td { 
        font-size: 11px;
    }
</style>

<form method='POST' action='<?PHP echo "?module='saldosuratdana'&act=input&idmenu=149"; ?>' id='d-form2' name='form2' data-parsley-validate class='form-horizontal form-label-left'>
    <input type='hidden' id='u_module' name='u_module' value='saldosuratdana' Readonly>
    <input type='hidden' id='u_idmenu' name='u_idmenu' value='149' Readonly>
    <input type='hidden' id='u_act' name='u_act' value='hapus' Readonly>
    
    <div class='x_content'>
        <table id='datatablekasbon' class='table table-striped table-bordered' width='100%'>
            <thead>
                <tr>
                    <th width='5px'>NO</th>
                    <th width='50px'>AKSI</th>
                    <th width='20px'>TANGGAL</th>
                    <th width='30px'>MENU</th>
                    <th width='50px'>NAMA</th>
                    <th width='20px'>KETERANGAN</th>
                    <th width='200px'>TGL. SELESAI</th>
                </tr>
            </thead>
        </table>

    </div>
    
</form>