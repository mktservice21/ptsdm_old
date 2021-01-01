<?PHP
    $aksi="module/md_m_kendaraan_isirangka/aksi_isikendaraan.php";
    
    $query = "select * from dbmaster.t_kendaraan where nopol='$pnopol_pilkpkhs'";
    $tampil_kp= mysqli_query($cnmy, $query);
    $rkp_= mysqli_fetch_array($tampil_kp);
    $pnorangka_kp=$rkp_['norangka'];
    $pnomesin_kp=$rkp_['nomesin'];
    $ptglst_kp=$rkp_['tgltempostnk'];
    
    $ptglst_kpnk="";
    if ($ptglst_kp=="0000-00-00") $ptglst_kp="";
    if (!empty($ptglst_kp)) $ptglst_kpnk = date('d/m/Y', strtotime($ptglst_kp));
    
?>

<!-- bootstrap-datetimepicker -->
<link href="vendors/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css" rel="stylesheet">
        
        
<script>
$(document).ready(function() {
    $('#mytgl03kp').datetimepicker({
        ignoreReadonly: true,
        allowInputToggle: true,
        format: 'DD/MM/YYYY'
    });
} );


function disp_confirm_kp_mkt(pText_, ket)  {
    var eid =document.getElementById('e_id').value;
    var enorangka =document.getElementById('e_norangka').value;
    var enomesin =document.getElementById('e_nomesin').value;
    var etglstnk =document.getElementById('e_tglstnk').value;
    
    if (eid==""){
        alert("PLAT NOMOR TIDAK BOLEH KOSONG....");
        document.getElementById('e_id').focus();
        return 0;
    }
    
    if (enorangka=="") {
        alert("No Rangka Masih Kosong...");
        return false;
    }
    
    if (enomesin=="") {
        alert("No Mesin Masih Kosong...");
        return false;
    }
    
    if (etglstnk=="") {
        alert("Tanggal Jatuh Tempo STNK Masih Kosong...");
        return false;
    }

    
    pText_= "Pastikan data yang diisi sudah sesuai!!!\n\
Setelah data terisi dan disimpan, maka tampilan untuk isi no rangka atau no mesin tidak akan ditampilkan lagi.\n\
Apakah akan melanjutkan simpan...???";
    ok_ = 1;
    if (ok_) {
        var r=confirm(pText_)
        if (r==true) {
            //document.write("You pressed OK!")
            var myurl = window.location;
            var urlku = new URL(myurl);
            var module = urlku.searchParams.get("module");
            var idmenu = urlku.searchParams.get("idmenu");
            
            document.getElementById("demo-form2").action = "module/md_m_kendaraan_isirangka/aksi_kendaraankpmkt.php?module=isidatakendaraanmkt"+"&act="+ket+"&idmenu=0";
            document.getElementById("demo-form2").submit();
            return 1;
        }
    } else {
        //document.write("You pressed Cancel!")
        return 0;
    }
}


</script>
<div class="">
    <div class="page-title"><div class="title_left">
            <h3>
                <?PHP
                $judul="Isi Data Kendaraan";
                echo "$judul";
                ?>
            </h3>
    </div></div><div class="clearfix"></div>

            
        <!--row-->
        <div class="row">    

            <form method='POST' action='<?PHP echo "$aksi?module=isidatakendaraanmkt&act=input&idmenu=0"; ?>' 
                  id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left' 
                  enctype='multipart/form-data'>

                <div class='col-md-12 col-sm-12 col-xs-12'>

                    <div class='x_panel'>

                        <div class='x_panel'>
                            <div class='x_content'>
                                <div class='col-md-12 col-sm-12 col-xs-12'>

                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>PLAT NOMOR <span class='required'></span></label>
                                        <div class='col-md-4'>
                                            <input type='hidden' id='e_idlama' name='e_idlama' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnopol_pilkpkhs; ?>' Readonly>
                                            <input type='text' id='e_id' name='e_id' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnopol_pilkpkhs; ?>' Readonly>
                                        </div>
                                    </div>



                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>NO. RANGKA <span class='required'></span></label>
                                        <div class='col-md-4'>
                                            <input type='text' id='e_norangka' name='e_norangka' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnorangka_kp; ?>'>
                                        </div>
                                    </div>

                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>NO. MESIN <span class='required'></span></label>
                                        <div class='col-md-4'>
                                            <input type='text' id='e_nomesin' name='e_nomesin' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnomesin_kp; ?>'>
                                        </div>
                                    </div>

                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>TGL. JATUH TEMPO STNK <span class='required'></span></label>
                                        <div class='col-md-4'>
                                            <div class='input-group date' id='mytgl03kp'>
                                                <input type="text" class="form-control" id='e_tglstnk' name='e_tglstnk' autocomplete='off' required='required' placeholder='dd/MM/yyyy' data-inputmask="'mask': '99/99/9999'" value='<?PHP echo $ptglst_kpnk; ?>'>
                                                <span class='input-group-addon'>
                                                    <span class='glyphicon glyphicon-calendar'></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''> <span class='required'></span></label>
                                        <div class='col-md-4'>
                                            <br/>
                                            <button type='button' class='btn btn-success' onclick='disp_confirm_kp_mkt("Simpan ?", "input")'>Simpan</button>
                                        </div>
                                    </div>




                                </div>
                            </div>
                        </div>


                    </div>
                </div>

            </form>

        </div>
            
        
    
</div>