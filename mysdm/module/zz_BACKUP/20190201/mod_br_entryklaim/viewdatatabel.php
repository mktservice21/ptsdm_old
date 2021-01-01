<?PHP
    session_start();
    
    $_SESSION['FINKLMTIPE']="";
    $_SESSION['FINKLMTGLTIPE']=$_POST['utgltipe'];
    $_SESSION['FINKLMPERENTY1']=$_POST['uperiode1'];
    $_SESSION['FINKLMPERENTY2']=$_POST['uperiode2'];
    
    
    $tgltipe=$_POST['utgltipe'];
    $date1=$_POST['uperiode1'];
    $date2=$_POST['uperiode2'];
    $tgl1= date("Y-m-d", strtotime($date1));
    $tgl2= date("Y-m-d", strtotime($date2));
    $divisi=$_POST['udivisi'];
    
    echo "<input type='hidden' name='cb_tgltipe' id='cb_tgltipe' value='$tgltipe'>";
    echo "<input type='hidden' name='xtgl1' id='xtgl1' value='$tgl1'>";
    echo "<input type='hidden' name='xtgl2' id='xtgl2' value='$tgl2'>";
    echo "<input type='hidden' name='cb_divisi' id='cb_divisi' value='$divisi'>";

?>
    
<script>
    $(document).ready(function() {
        var aksi = "module/mod_br_entryklaim/aksi_entrybrnon.php";
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var idmenu = urlku.searchParams.get("idmenu");
        var nmun = urlku.searchParams.get("nmun");
        var etgltipe=document.getElementById('cb_tgltipe').value;
        var etgl1 = document.getElementById("xtgl1").value;
        var etgl2 = document.getElementById("xtgl2").value;
        var edivisi=document.getElementById('cb_divisi').value;
        
        //alert(etgl2);
        var dataTable = $('#datatableklaim').DataTable( {
            "processing": true,
            "serverSide": true,
            "stateSave": true,
            "order": [[ 0, "desc" ]],
            "lengthMenu": [[10, 50, 100, 10000000], [10, 50, 100, "All"]],
            "displayLength": 10,
            "columnDefs": [
                { "visible": false },
                { className: "text-right", "targets": [6] },//right
                { className: "text-nowrap", "targets": [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10] }//nowrap

            ],
            "language": {
                "zeroRecords": "Lihat Page di bawah!!! Jika ada Page, Pilih Page 1...!!! Jika tidak ada Page, maka data KOSONG..."
            },

            "ajax":{
                url :"module/mod_br_entryklaim/mydata.php?module="+module+"&idmenu="+idmenu+"&nmun="+nmun+"&aksi="+aksi+"&utgltipe="+etgltipe+"&uperiode1="+etgl1+"&uperiode2="+etgl2+"&udivisi="+edivisi, // json datasource
                type: "post",  // method  , by default get
                data:"uperiode1="+etgl1+"&uperiode2="+etgl2,
                error: function(){  // error handling
                    $(".data-grid-error").html("");
                    $("#datatable").append('<tbody class="data-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                    $("#data-grid_processing").css("display","none");

                }
            }
        } );
    } );
    
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


                //document.write("You pressed OK!")
                document.getElementById("demo-form2").action = "module/mod_br_entryklaim/aksi_entryklaim.php?kethapus="+txt+"&ket="+ket+"&id="+noid;
                document.getElementById("demo-form2").submit();
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
    #datatableklaim th {
        font-size: 12px;
    }
    #datatableklaim td { 
        font-size: 11px;
    }
</style>

<form method='POST' action='<?PHP echo "?module='entrybrklaim'&act=input&idmenu=106"; ?>' id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left'>
    <input type='hidden' id='u_module' name='u_module' value='entrybrklaim' Readonly>
    <input type='hidden' id='u_idmenu' name='u_idmenu' value='106' Readonly>
    <input type='hidden' id='u_act' name='u_act' value='hapus' Readonly>
    
    <div class='x_content'>
        <table id='datatableklaim' class='table table-striped table-bordered' width='100%'>
            <thead>
                <tr>
                    <th width='7px'>No</th><th>Aksi</th>
                    <th width='60px'>Tanggal</th><th width='60px'>Tgl. Transfer</th>
                    <th>Yg Membuat</th>
                    <th width='80px'>Distributor</th><th width='50px'>Jumlah</th>
                    <th width='50px'>Realisasi</th><th width='50px'>No Slip</th>
                    <th>Lampiran</th><th width='50px'>Aktivitas</th>
                </tr>
            </thead>
        </table>

    </div>
</form>