<!--<script src="module/mod_br_entrynon/mytransaksi.js"></script>-->
<script>
function getDataKaryawanDiv(data1, data2, logstsadmin, loglvlposisi, logdivisi, idivprod){
    var edivprod =document.getElementById(idivprod).value;
    
    $.ajax({
        type:"post",
        url:"config/viewdata.php?module=viewkaryawandiv&data1="+data1+"&data2="+data2,
        data:"udata1="+data1+"&udata2="+data2+"&ulogstatus="+logstsadmin+"&uloglvl="+loglvlposisi+"&ulogdivisi="+logdivisi+"&uedivprod="+edivprod,
        success:function(data){
            $("#myModal").html(data);
        }
    });
}

function getDataModalKaryawan(fildnya1, fildnya2, d1, d2, icabang){
    document.getElementById(fildnya1).value=d1;
    document.getElementById(fildnya2).value=d2;
    document.getElementById('e_idcabang').value="";
    document.getElementById('e_cabang').value="";
}

function getDataCabangFmr(data1, data2, imr){
    var emr=document.getElementById(imr).value;
    if (emr=="") return 0;
    $.ajax({
        type:"post",
        url:"config/viewdata.php?module=viewdatacabangfmr&data1="+data1+"&data2="+data2,
        data:"udata1="+data1+"&udata2="+data2+"&umr="+emr,
        success:function(data){
            $("#myModal").html(data);
        }
    });
}

function getDataModalCabang(fildnya1, fildnya2, d1, d2){
    document.getElementById(fildnya1).value=d1;
    document.getElementById(fildnya2).value=d2;
}

function HapusDataKaryawan(i_idkaryawan, i_karyawan, i_idcabang, i_cabang, i_akun, i_namaakun){
    document.getElementById(i_idkaryawan).value="";
    document.getElementById(i_karyawan).value="";
    document.getElementById(i_idcabang).value="";
    document.getElementById(i_cabang).value="";
    document.getElementById(i_akun).value="";
    document.getElementById(i_namaakun).value="";

}

function disp_confirm(pText_)  {
    var ecab =document.getElementById('e_idcabang').value;
    var ebuat =document.getElementById('e_idkaryawan').value;
    var edivi =document.getElementById('cb_divisi').value;
    var eakun =document.getElementById('e_akun').value;
    var enominal =document.getElementById('e_nominal').value;
    
    if (edivi==""){
        alert("divisi masih kosong....");
        return 0;
    }
    if (ebuat==""){
        alert("yang membuat masih kosong....");
        return 0;
    }
    if (ecab==""){
        alert("cabang masih kosong....");
        return 0;
    }


    if (eakun==""){
        alert("akun masih kosong....");
        return 0;
    }
    if (enominal=="" || enominal=="0"){
        alert("nominal masih kosong....");
        return 0;
    }
    
    
    
    ok_ = 1;
    if (ok_) {
        var r=confirm(pText_)
        if (r==true) {
            //document.write("You pressed OK!")
            document.getElementById("demo-form2").action = "module/mod_br_entry/aksi_entrybr.php";
            document.getElementById("demo-form2").submit();
            return 1;
        }
    } else {
        //document.write("You pressed Cancel!")
        return 0;
    }
}


$(document).ready(function(){
    $('#wizard').smartWizard({transitionEffect:'slide',onFinish:onFinishCallback});
    function onFinishCallback(){
        alert('Klik Save');
    }     
});
            
</script>

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


<?PHP
$noid="";
$hari_ini = date("Y-m-d");
$tglinput = date('d F Y', strtotime($hari_ini));
$tglperlu=$tglinput;
$idajukan=$_SESSION['IDCARD']; 
$nmajukan=$_SESSION['NAMALENGKAP']; 
$idcab=$_SESSION['IDCABANG']; 
$nmcab=$_SESSION['NMCABANG'];
$jumlah="";
$aktivitas="";
$ccy="";
$divprodid="";
$act="input";
$akidakun="";
$aknmakun="";
$akrp="";
$akcatat="";
$divreadonly="";
if ($_GET['act']=="editdata"){
    $act="update";
    
    $edit = mysqli_query($cnmy, "SELECT * FROM dbbudget.v_br WHERE NOID='$_GET[id]'");
    $r    = mysqli_fetch_array($edit);
    $noid=$r['NOID'];
    $rp=$r['JUMLAH'];
    $tglinput = date('d F Y', strtotime($r['TGL']));
    $tglperlu = date('d F Y', strtotime($r['TGL_PERLU']));
    $idajukan=$r['KARYAWANID']; 
    $nmajukan=$r['nama']; 
    $idcab=$r['ICABANGID']; 
    $nmcab=$r['nama_cabang'];
    $ccy=$r['ccyId'];
    $jumlah=$r['JUMLAH'];
    $aktivitas=$r['AKTIVITAS1'];
    $divprodid=$r['divprodid'];
    
    $detail = mysqli_query($cnmy, "SELECT * FROM dbbudget.v_br_d WHERE NOID='$_GET[id]' limit 1");
    $d    = mysqli_fetch_array($detail);
    $akidakun=$d['kode'];
    $aknmakun=$d['nama_kode'];
    $akrp=$d['RP'];
    $akcatat=$d['AKTIVITAS2'];
    $divreadonly="Readonly";
}
    
?>

<!-- Modal -->
<div class='modal fade' id='myModal' role='dialog'></div>

<div class="">

    <!--row-->
    <div class="row">
        
        <form method='POST' action='<?PHP echo "$aksi?module=$_GET[module]&act=$act&idmenu=$_GET[idmenu]"; ?>' id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left'>
            
            <input type='hidden' id='u_module' name='u_module' value='<?PHP echo $_GET['module']; ?>' Readonly>
            <input type='hidden' id='u_idmenu' name='u_idmenu' value='<?PHP echo $_GET['idmenu']; ?>' Readonly>
            <input type='hidden' id='u_act' name='u_act' value='<?PHP echo $act; ?>' Readonly>
            
            <div class='col-md-12 col-sm-12 col-xs-12'>
                <div class='x_panel'>
                    <div class='col-md-12 col-sm-12 col-xs-12'>
                        <h2>
                            <input type='button' value='Back' onclick='self.history.back()' class='btn btn-default'>
                            <button type='button' class='btn btn-success' onclick='disp_confirm("Simpan ?")'>Save</button>
                            <?PHP if ($_GET['act']=="tambah"){ ?>
                                <small>tambah data</small>
                            <?PHP } else { ?>
                                <small>edit data</small>
                            <?PHP } ?>
                        </h2>
                        <div class='clearfix'></div>
                    </div>
                    


                    <!--kiri-->
                    <div class='col-md-6 col-xs-12'>
                        <div class='x_panel'>
                            <div class='x_content form-horizontal form-label-left'>

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>ID <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <input type='text' id='e_nobr' name='e_nobr' class='form-control col-md-7 col-xs-12' value='<?PHP echo $noid; ?>' Readonly>
                                    </div>
                                </div>

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='tgl01'>Tanggal </label>
                                    <div class='col-md-9'>
                                        <div class='input-group date' id='tgl01'>
                                            <input type='text' id='e_tglinput' name='e_tglinput' required='required' class='form-control col-md-7 col-xs-12' placeholder='tanggal input' value='<?PHP echo $tglinput; ?>' placeholder='dd mmm yyyy' Readonly>
                                            <span class='input-group-addon'>
                                                <span class='glyphicon glyphicon-calendar'></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_divisi'>Divisi <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='form-control' id='cb_divisi' name='cb_divisi' onchange="HapusDataKaryawan('e_idkaryawan', 'e_karyawan', 'e_idcabang', 'e_cabang', 'e_akun', 'e_namaakun')">
                                            <?PHP
                                            $fildiv="";
                                            if ($_GET['act']=="editdata"){
                                                $fildiv=" and DivProdId='$divprodid' ";
                                            }
                                            $tampil=mysqli_query($cnmy, "SELECT DivProdId, nama FROM 1it.divprod where br='Y' $fildiv order by nama");
                                            //echo "<option value='' selected>-- Pilihan --</option>";
                                            while($a=mysqli_fetch_array($tampil)){ 
                                                if ($a['DivProdId']==$divprodid)
                                                    echo "<option value='$a[DivProdId]' selected>$a[nama]</option>";
                                                else{
                                                    if ($a['DivProdId']==$_SESSION['DIVISI'])
                                                        echo "<option value='$a[DivProdId]' selected>$a[nama]</option>";
                                                    else
                                                        echo "<option value='$a[DivProdId]'>$a[nama]</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_karyawan'>Yang Membuat <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <div class='input-group '>
                                            <span class='input-group-btn'>
                                                <button type='button' class='btn btn-primary' data-toggle='modal' data-target='#myModal' 
                                                        onClick="getDataKaryawanDiv('e_idkaryawan', 'e_karyawan', 
                                                '<?PHP echo $_SESSION['STSADMIN']; ?>',
                                                '<?PHP echo $_SESSION['LVLPOSISI']; ?>',
                                                '<?PHP echo $_SESSION['DIVISI']; ?>',
                                                'cb_divisi'
                                                )">Go!</button>
                                            </span>
                                            <input type='hidden' class='form-control' id='e_idkaryawan' name='e_idkaryawan' value='<?PHP echo $idajukan; ?>' Readonly>
                                            <input type='text' class='form-control' id='e_karyawan' name='e_karyawan' value='<?PHP echo $nmajukan; ?>' Readonly>
                                        </div>
                                    </div>
                                </div>

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_cabang'>Cabang SDM <span class='required'></span></label>
                                    <div class='col-sm-9'>
                                        <div class='input-group '>
                                        <span class='input-group-btn'>
                                            <button type='button' class='btn btn-primary' data-toggle='modal' data-target='#myModal' onClick="getDataCabangFmr('e_idcabang', 'e_cabang', 'e_idkaryawan')">Go!</button>
                                        </span>
                                        <input type='hidden' class='form-control' id='e_idcabang' name='e_idcabang' value='<?PHP echo $idcab; ?>' Readonly>
                                        <input type='text' class='form-control' id='e_cabang' name='e_cabang' value='<?PHP echo $nmcab; ?>' Readonly>
                                        </div>

                                    </div>
                                </div>

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='tgl02'>Tanggal Perlu </label>
                                    <div class='col-md-9'>
                                        <div class='input-group date' id='tgl01'>
                                            <input type='text' id='e_tglperlu' name='e_tglperlu' required='required' class='form-control col-md-7 col-xs-12' placeholder='tgl perlu' value='<?PHP echo $tglperlu; ?>' placeholder='dd mmm yyyy' Readonly>
                                            <span class='input-group-addon'>
                                                <span class='glyphicon glyphicon-calendar'></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                

                            </div>
                        </div>
                    </div>

                    <!--kanan-->
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
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_jenis'>Mata Uang <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='form-control' name='cb_jenis'>
                                            <?php
                                            $tampil=mysqli_query($cnmy, "SELECT ccyId, nama FROM 1it.ccy");
                                            while($c=mysqli_fetch_array($tampil)){
                                                if ($c['ccyId']==$ccy)
                                                    echo "<option value='$c[ccyId]' selected>$c[ccyId] - $c[nama]</option>";
                                                else{
                                                    if ($c['ccyId']=="IDR")
                                                        echo "<option value='$c[ccyId]' selected>$c[ccyId] - $c[nama]</option>";
                                                    else    
                                                        echo "<option value='$c[ccyId]'>$c[ccyId] - $c[nama]</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>


                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Nominal <span class='required'>*</span></label>
                                    <div class='col-xs-9'>
                                        <input type='text' id='e_nominal' name='e_nominal' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $akrp; ?>'>
                                    </div><!--disabled='disabled'-->
                                </div>



                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_aktivitas'>Aktivitas <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <textarea class='form-control' id='e_aktivitas' name='e_aktivitas' rows='3' placeholder='Aktivitas'><?PHP echo $aktivitas; ?></textarea>
                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>
                    
                    
                    
<div class='col-md-12 col-xs-12'>
    <div class='x_panel'>
        <div class='x_content form-horizontal form-label-left'>
            <div class='tbldata'>
                <table id='datatableuc' class='table table-striped table-bordered' width="50%">
                    <thead>
                        <tr><th width='5%px'>No</th>
                        <th>Akun</th>
                        <th width='17%' align="right">Rp. (Limit)</th>
                        <th width='10%' align="right">Jumlah/Hari</th>
                        <th width='15%' align="right">Total</th>
                        <th width='40%' align="right">Note</th>
                        </tr>
                    </thead>
                    <tbody class='inputdatauc'>
                    <?PHP
                    $no=1;
                    $tampil = mysqli_query($cnmy, "SELECT NOBUD, NAMA_BUD, FORMAT(RP,2,'de_DE') as RP FROM dbbudget.br_uc_budget order by NOBUD");
                    while ($uc=mysqli_fetch_array($tampil)){
                        $jmhr="";
                        $total="";
                        $note="";
                        if ($_GET['act']=="editdata"){
                            $jmhr=  getfield("select JML as lcfields from dbbudget.t_br_u where NOID='$_GET[id]' and NOBUD='$uc[NOBUD]'");
                            $total=  getfield("select TOTAL as lcfields from dbbudget.t_br_u where NOID='$_GET[id]' and NOBUD='$uc[NOBUD]'");
                            $note=  getfield("select KET as lcfields from dbbudget.t_br_u where NOID='$_GET[id]' and NOBUD='$uc[NOBUD]'");
                        }
                        echo "<tr scope='row'><td>$no</td>";
                        echo "<td>$uc[NAMA_BUD]</td>";
                        echo "<td align='right'>$uc[RP]</td>";
                        echo "<td><input type='text' id='e_hr$uc[NOBUD]' name='e_hr$uc[NOBUD]' class='form-control input-sm inputmaskrp2' autocomplete='off' value='$jmhr'></td>";
                        echo "<td><input type='text' id='e_rphr$uc[NOBUD]' name='e_rphr$uc[NOBUD]' class='form-control input-sm inputmaskrp2' autocomplete='off' value='$total'></td>";
                        echo "<td><input type='text' id='e_note$uc[NOBUD]' name='e_note$uc[NOBUD]' class='form-control input-sm' autocomplete='off' value='$note'></td>";
                        $no++;
                    }
                    ?>
                    </tbody>
                    </table>
                
            </div>


        </div>
    </div>
</div>
                    

                   
                </div>
            </div>

        </form>
    </div>
    <!--end row-->
</div>
