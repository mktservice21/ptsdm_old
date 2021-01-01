<script>
function getDataBgAkunDivProd(data1, data2, divprod){
    var edivprod=document.getElementById(divprod).value;
    $.ajax({
        type:"post",
        url:"config/viewdata.php?module=viewbgakundivprod&data1="+data1+"&data2="+data2,
        data:"udata1="+data1+"&udata2="+data2+"&udivprod="+edivprod,
        success:function(data){
            $("#myModal").html(data);
        }
    });
}

function getDataModalBgAkun(fildnya1, fildnya2, d1, d2){
    document.getElementById(fildnya1).value=d1;
    document.getElementById(fildnya2).value=d2;
}

$(document).ready(function(){
    $("#add_new").click(function(){
        $(".entry-form").fadeIn("fast");
    });

    $("#close").click(function(){
            $(".entry-form").fadeOut("fast");
    });

    $("#cancel").click(function(){
            $(".entry-form").fadeOut("fast");
    });

    $(".add-row").click(function(){
        
        var i_total = $("#e_jmlusulan").val();
        if (i_total=='') i_total=0;
        
        /*var a = "1.000.000";
        var b = "1.000.000";
        var ab = parseFloat(a.replace(".",""))+parseFloat(b.replace(".",""));
        alert (ab); return false; */
        if (i_total!==0) i_total = i_total.replace(",","");
        if (i_total!==0) i_total = i_total.replace(",","");


        var i_nominal = $("#e_nominal").val();
        if (i_nominal=='') i_nominal=0;
        i_total =parseFloat(i_total)+parseFloat(i_nominal.replace(",",""));
        
        
        /*document.form1.e_totdebit.value = convertToRP(i_totD);
        document.form1.e_totkredit.value = convertToRP(i_totK);*/
        document.form1.e_jmlusulan.value = i_total;


                    
        var i_nmakun = $("#e_namaakun").val();
        var i_akun = $("#e_akun").val();
        var i_catatan = $("#e_aktivitas2").val();
        var markup;
        markup = "<tr>";
        markup += "<td><input type='checkbox' name='record'></td>";
        markup += "<td>" + i_nominal + "<input type='hidden' id='m_nominal[]' name='m_nominal[]' value='"+i_nominal+"'></td>";
        markup += "<td colspan=2>" + i_akun + "<input type='hidden' id='m_akun[]' name='m_akun[]' value='"+i_akun+"'>";
        markup += " - " + i_nmakun + "<input type='hidden' id='m_nmakun[]' name='m_nmakun[]' value='"+i_nmakun+"'></td>";
        markup += "<td>" + i_catatan + "<input type='hidden' id='m_catatan[]' name='m_catatan[]' value='"+i_catatan+"'></td>";
        markup += "</tr>";
        $("table tbody.inputdata").append(markup);

    });

    // Find and remove selected table rows
    $(".delete-row").click(function(){
        var ilewat = false;
        $("table tbody.inputdata").find('input[name="record"]').each(function(){
            if($(this).is(":checked")){
                $(this).parents("tr").remove();
                ilewat = true;
            }
        });

        if (ilewat == true) {
            var tot = 0;
            var inpsD = document.getElementsByName('m_nominal[]');
            for (var i = 0; i < inpsD.length; i++) {
                var inpD = inpsD[i];
                var zD = inpD.value;
                tot =parseFloat(tot)+parseFloat(zD.replace(",",""));
            }

            document.form1.e_jmlusulan.value = tot;
        }

    });
});
</script>


<!--kiri-->
<div class='col-md-6 col-xs-12'>
    <div class='x_panel'>
        <div class='x_content form-horizontal form-label-left'>

            
            <div class='form-group'>
                <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_karyawan'>Akun <span class='required'>*</span></label>
                <div class='col-md-9 col-sm-9 col-xs-12'>
                    <div class='input-group '>
                        <span class='input-group-btn'>
                            <button type='button' class='btn btn-primary' data-toggle='modal' data-target='#myModal' onClick="getDataBgAkunDivProd('e_akun', 'e_namaakun', 'cb_divisi')">Go!</button>
                        </span>
                        <input type='text' class='form-control' id='e_akun' name='e_akun' value='<?PHP echo $akidakun; ?>' Readonly>
                    </div>
                    <input type='text' class='form-control' id='e_namaakun' name='e_namaakun' value='<?PHP echo $aknmakun; ?>' Readonly>
                </div>
            </div>

            <div class='form-group'>
                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Nominal <span class='required'>*</span></label>
                <div class='col-xs-9'>
                    <input type='text' id='e_nominal' name='e_nominal' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $akrp; ?>'>
                </div><!--disabled='disabled'-->
            </div>
            
            <div class='form-group'>
                <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_aktivitas2'>Catatan <span class='required'></span></label>
                <div class='col-xs-9'>
                    <textarea class='form-control' id='e_aktivitas2' name='e_aktivitas2' rows='3' placeholder='Aktivitas'><?PHP echo $akcatat; ?></textarea>
                </div>
            </div>
            
            <div class='form-group'>
                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''> <span class='required'></span></label>
                <div class='col-xs-9'>
                    <input type='button' value='Tambah' id='save' class='btn btn-danger add-row'>
                </div>
            </div>
            
            
            
            
        </div>
    </div>
</div>

<!--kanan-->
<div class='col-md-6 col-xs-12'>
    <div class='x_panel'>
        <div class='x_content form-horizontal form-label-left'>


            <div class='tbldata'>
                <table id='datatableadd' class='table table-striped table-bordered'>
                    <thead>
                        <tr><th width='5%px'>Pilih</th><th width='15%'>Rp</th>
                        <th colspan=2 width='30%'>Akun</th>
                        <th>Catatan</th>
                        </tr>
                    </thead>
                    <?PHP if ($_GET['act']=="tambahbaru"){ ?>
                        <tbody class='inputdata'></tbody>
                    <?PHP }else{
                            echo "<tbody class='inputdata'>";
                            $detail = mysqli_query($cnmy, "SELECT * FROM dbbudget.v_br_d WHERE NOID='$_GET[id]'");
                            while ($d    = mysqli_fetch_array($detail)) {
                                $jumlahd=number_format($d['RP'],0,",",".");
                                echo  "<tr>";
                                echo "<td><input type='checkbox' name='record'></td>";
                                echo "<td>$jumlahd<input type='hidden' id='m_nominal[]' name='m_nominal[]' value='$d[RP]'></td>";
                                echo "<td colspan=2>$d[kode]<input type='hidden' id='m_akun[]' name='m_akun[]' value='$d[kode]'>";
                                echo "<br/>$d[nama_kode]<input type='hidden' id='m_nmakun[]' name='m_nmakun[]' value='$d[nama_kode]'></td>";
                                echo "<td>$d[AKTIVITAS2]<input type='hidden' id='m_catatan[]' name='m_catatan[]' value='$d[AKTIVITAS2]'></td>";
                                echo "</tr>";
                            }
                            echo "</tbody>";
                          } ?>
                    </table>
                
                    <div class='form-group'>
                        <input type='button' class='delete-row' value='Hapus'>
                    </div>
                
            </div>


        </div>
    </div>
</div>