<?PHP
    session_start();
    
    $_SESSION['SWKRTIPE']="";
    $_SESSION['SWKRTGLTIPE']=$_POST['utgltipe'];
    $_SESSION['SWKRENTY1']=$_POST['uperiode1'];
    $_SESSION['SWKRENTY2']=$_POST['uperiode2'];
    $_SESSION['SWKRDIV']=$_POST['udivisi'];
    $_SESSION['SWKRCAB']=$_POST['uarea'];
    
    
    $tgltipe=$_POST['utgltipe'];
    $date1=$_POST['uperiode1'];
    $date2=$_POST['uperiode2'];
    $tgl1= date("Y-m-d", strtotime($date1));
    $tgl2= date("Y-m-d", strtotime($date2));
    $divisi=$_POST['udivisi'];
    $uidcard=$_POST['uidc'];
    $cabang=$_POST['uarea'];
    
    echo "<input type='hidden' name='cb_tgltipe' id='cb_tgltipe' value='$tgltipe'>";
    echo "<input type='hidden' name='xtgl1' id='xtgl1' value='$tgl1'>";
    echo "<input type='hidden' name='xtgl2' id='xtgl2' value='$tgl2'>";
    echo "<input type='hidden' name='cb_divisi' id='cb_divisi' value='$divisi'>";
    echo "<input type='hidden' name='e_cabang' id='e_cabang' value='$cabang'>";
    
    $filcoa="";
    echo "<input type='hidden' name='e_wewenang' id='e_wewenang' value='$filcoa'>";
    echo "<input type='hidden' name='e_idcardinput' id='e_idcardinput' value='$uidcard'>";
    

?>
    
<script>
    $(document).ready(function() {
        var aksi = "module/mod_br_entrysewa/aksi_entrysewa.php";
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var idmenu = urlku.searchParams.get("idmenu");
        var nmun = urlku.searchParams.get("nmun");
        var etgltipe=document.getElementById('cb_tgltipe').value;
        var etgl1 = document.getElementById("xtgl1").value;
        var etgl2 = document.getElementById("xtgl2").value;
        var edivisi=document.getElementById('cb_divisi').value;
        var efilcoa=document.getElementById('e_wewenang').value;
        var eidi=document.getElementById('e_idcardinput').value;
        var earea=document.getElementById('e_cabang').value;
        
        //alert(etgl1);
        var dataTable = $('#datatableswa').DataTable( {
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
                { className: "text-nowrap", "targets": [0, 1, 2, 3, 4, 5, 6, 7] }//nowrap

            ],
            "language": {
                "zeroRecords": "Lihat Page di bawah!!! Jika ada Page, Pilih Page 1...!!! Jika tidak ada Page, maka data KOSONG..."
            },
            "scrollY": 460,
            "scrollX": true,

            "ajax":{
                url :"module/mod_br_entrysewa/mydata.php?module="+module+"&idmenu="+idmenu+"&nmun="+nmun+"&aksi="+aksi+"&utgltipe="+etgltipe+"&uperiode1="+etgl1+"&uperiode2="+etgl2+"&udivisi="+edivisi+"&ufilcoa="+efilcoa+"&uidi="+eidi+"&uarea="+earea, // json datasource
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
    #datatableswa th {
        font-size: 13px;
    }
    #datatableswa td { 
        font-size: 11px;
    }
    .imgzoom:hover {
        -ms-transform: scale(3.5); /* IE 9 */
        -webkit-transform: scale(3.5); /* Safari 3-8 */
        transform: scale(3.5);
        
    }
</style>

<form method='POST' action='<?PHP echo "?module='entrybrsewa'&act=input&idmenu=122"; ?>' id='d-form2' name='form2' data-parsley-validate class='form-horizontal form-label-left'>
    
    
    <input type='hidden' id='u_module' name='u_module' value='' Readonly>
    <input type='hidden' id='u_idmenu' name='u_idmenu' value='' Readonly>
    <input type='hidden' id='u_act' name='u_act' value='input' Readonly>
    
    <div class='x_content'>
        <table id='datatableswa' class='table table-striped table-bordered' width='100%'>
            <thead>
                <tr>
                    <th width='7px'>No</th>
                    <th width='70px'>Aksi</th>
                    <th width='40px'>No ID</th>
                    <th width='80px'>Yang Membuat</th>
                    <!--<th width='50px'>Area</th>-->
                    <th width='40px'>Periode</th>
                    <th width='40px'>s/d.</th>
                    <th width='50px'>Jumlah</th>
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


                //document.write("You pressed OK!")
                var myurl = window.location;
                var urlku = new URL(myurl);
                var module = urlku.searchParams.get("module");
                var idmenu = urlku.searchParams.get("idmenu");
                
                document.getElementById("d-form2").action = "module/mod_br_entrysewa/aksi_entrysewa.php?module="+module+"&act=hapus&idmenu="+idmenu+"&kethapus="+txt+"&ket="+ket+"&id="+noid;
                document.getElementById("d-form2").submit();
                return 1;
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }
    }
</script>