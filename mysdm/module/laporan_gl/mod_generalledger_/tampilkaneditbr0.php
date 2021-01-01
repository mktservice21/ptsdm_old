<?PHP
    date_default_timezone_set('Asia/Jakarta');
    session_start();
    include "../../../config/koneksimysqli.php";
    $act="input";
    $aksi="";
    
    $papppil=$_POST['uapppil'];
    $pketpros=$_POST['uketpros'];
    $pidbr=$_POST['uidbr'];
    $pnoid=$_POST['unoid'];
    $pnorincibr=$_POST['unorincibr'];
    $pcoaasli=$_POST['ucoaasli'];
    $pcoanmasli=$_POST['ucoanmasli'];
    $pdivisi=$_POST['udivisi'];
    $pketerangan=$_POST['uketerangan'];
    
    $pidkddokter=$_POST['uiddokternm'];
    $pnamadokter=$_POST['udokternm'];
    $pkodeakunasli=$_POST['ukdakun'];
    $pnmkode=$_POST['unmakun'];
    $pnmasliakun=$pkodeakunasli." - ".$pnmkode;
    
    
            
    $psudahpost=false;
    if ($pketpros=="1" OR (INT)$pketpros==1) $psudahpost=true;
$psudahpost=false;  
    
    $query_pilih="";
    if ($psudahpost==true) {
        $query_pilih="select keterangan from dbmaster.t_proses_bm_act where noidauto='$pnoid' AND idkodeinput='$pidbr' AND kodeinput='$papppil'";
    }else{
        if ($papppil=="A") {
            $query_pilih="select a.aktivitas1 as keterangan, a.kode, b.nama nama_akun, dokterid from hrd.br0 a LEFT JOIN hrd.br_kode b on a.kode=b.kodeid where a.brid='$pidbr'";
        }
    }
    if (!empty($query_pilih)) {
        $tampil= mysqli_query($cnmy, $query_pilih);
        $row= mysqli_fetch_array($tampil);
        $pketerangan=$row['keterangan'];
        if ($papppil=="A") {
            if (empty($pkodeakunasli)) {
                $pkodeakunasli=$row['kode'];
                $pnmkode=$row['nama_akun'];
                $pnmasliakun=$pkodeakunasli." - ".$pnmkode;
            }
        }
    }
?>

    <!-- jQuery -->
    <script src="vendors/jquery/dist/jquery.min.js"></script>


    
<script> window.onload = function() { document.getElementById("e_idbr").focus(); } </script>

<div class='modal-dialog modal-lg'>
    <!-- Modal content-->
    <div class='modal-content'>
        <div class='modal-header'>
            <button type='button' class='close' data-dismiss='modal'>&times;</button>
            <h4 class='modal-title'>EDIT COA BR</h4>
        </div>

        
        
        <div class="">

            <!--row-->
            <div class="row">

                <form method='POST' action='<?PHP echo "$aksi?module=lapgeneralledger&act=input&idmenu=258"; ?>' id='demo-form4' name='form4' data-parsley-validate class='form-horizontal form-label-left'>

                    <div class='col-md-12 col-sm-12 col-xs-12'>
                        <div class='x_panel'>

                            <div class='x_panel'>
                                <div class='x_content'>
                                    <div class='col-md-12 col-sm-12 col-xs-12'>


                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>ID <span class='required'></span></label>
                                            <div class='col-md-4'>
                                                <input type='text' id='e_idbr' name='e_idbr' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidbr; ?>' Readonly>
                                                <input type='hidden' id='e_rincinoid' name='e_rincinoid' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnorincibr; ?>' Readonly>
                                                <input type='hidden' id='e_nomorid' name='e_nomorid' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnoid; ?>' Readonly>
                                                <input type='hidden' id='e_stspros' name='e_stspros' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pketpros; ?>' Readonly>
                                                <input type='hidden' id='e_apppil' name='e_apppil' class='form-control col-md-7 col-xs-12' value='<?PHP echo $papppil; ?>' Readonly>
                                                <input type='hidden' id='e_divid' name='e_divid' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pdivisi; ?>' Readonly>
                                            </div>
                                        </div>
                                        
                                        
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Akun <span class='required'></span></label>
                                            <div class='col-md-4'>
                                                <input type='text' id='e_asliakun' name='e_asliakun' autocomplete='off' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnmasliakun; ?>' Readonly>
                                                <input type='hidden' id='e_kodeasliakun' name='e_kodeasliakun' autocomplete='off' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pkodeakunasli; ?>' Readonly>
                                                <input type='hidden' id='e_namaasliakun' name='e_namaasliakun' autocomplete='off' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnmkode; ?>' Readonly>
                                                <input type='hidden' id='e_namaaslidokter' name='e_namaaslidokter' autocomplete='off' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnamadokter; ?>' Readonly>
                                                <input type='hidden' id='e_idaslidokter' name='e_idaslidokter' autocomplete='off' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidkddokter; ?>' Readonly>
                                            </div>
                                        </div>
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>COA <span class='required'></span></label>
                                            <div class='col-md-4'>
                                                <input type='text' id='e_aslicoa' name='e_aslicoa' autocomplete='off' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pcoaasli; ?>' Readonly>
                                            </div>
                                        </div>
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Nama COA <span class='required'></span></label>
                                            <div class='col-md-4'>
                                                <input type='text' id='e_aslinmcoa' name='e_aslinmcoa' autocomplete='off' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pcoanmasli; ?>' Readonly>
                                            </div>
                                        </div>
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''><span style="color:red;">DOKTER</span> <span class='required'></span></label>
                                            <div class='col-md-6'>
                                                <select class='form-control' id="cb_iddoker" name="cb_iddoker" onchange="">
                                                    <?PHP
                                                    if (!empty($pnamadokter)) {
                                                        echo "<option value='' >-- Kosong --</option>";
                                                        echo "<option value='$pidkddokter' selected>$pidkddokter - $pnamadokter</option>";
                                                    }else{
                                                        echo "<option value='' >-- Kosong --</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''><span style="color:red;">AKUN EDIT</span> <span class='required'></span></label>
                                            <div class='col-md-6'>
                                                <select class='form-control' id="cb_akunedit" name="cb_akunedit" onchange="">
                                                    <?PHP
                                                    $query ="select kodeid, nama from hrd.br_kode WHERE divprodid='$pdivisi' order by nama";
                                                    $tampil1 = mysqli_query($cnmy, $query);
                                                    while ($zs= mysqli_fetch_array($tampil1)) {
                                                        $npkodeidak=$zs['kodeid'];
                                                        $npkodenmak=$zs['nama'];
                                                        if ($npkodeidak==$pkodeakunasli)
                                                            echo "<option value='$npkodeidak' selected>$npkodeidak - $npkodenmak</option>";
                                                        else
                                                            echo "<option value='$npkodeidak' >$npkodeidak - $npkodenmak</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''><span style="color:red;">COA EDIT</span> <span class='required'></span></label>
                                            <div class='col-md-6'>
                                                <select class='form-control' id="cb_coapil" name="cb_coapil" onchange="">
                                                    <?PHP
                                                    $query = "select DISTINCT d.DIVISI2, d.COA1, e.NAMA1, c.COA2, d.NAMA2, b.COA3, c.NAMA3, b.COA4, b.NAMA4
                                                       from dbmaster.coa_level4 b 
                                                       LEFT JOIN dbmaster.coa_level3 c ON c.COA3=b.COA3
                                                       LEFT JOIN dbmaster.coa_level2 d ON c.COA2=d.COA2
                                                       LEFT JOIN dbmaster.coa_level1 e ON e.COA1=d.COA1 WHERE d.DIVISI2='$pdivisi' OR b.COA4='$pcoaasli' ";
                                                    $query .=" order by b.COA4";
                                                    $tampil = mysqli_query($cnmy, $query);
                                                    echo "<option value='' selected>--Pilih--</option>";
                                                    while ($z= mysqli_fetch_array($tampil)) {
                                                        $ncoa4=$z['COA4'];
                                                        $nnmcoa4=$z['NAMA4'];
                                                        
                                                        if ($pcoaasli==$ncoa4)
                                                            echo "<option value='$ncoa4' selected>$ncoa4 - $nnmcoa4</option>";
                                                        else
                                                            echo "<option value='$ncoa4'>$ncoa4 - $nnmcoa4</option>";
                                                    }
                                                    
                                                    $query = "select DISTINCT d.DIVISI2, d.COA1, e.NAMA1, c.COA2, d.NAMA2, b.COA3, c.NAMA3, b.COA4, b.NAMA4
                                                       from dbmaster.coa_level4 b 
                                                       LEFT JOIN dbmaster.coa_level3 c ON c.COA3=b.COA3
                                                       LEFT JOIN dbmaster.coa_level2 d ON c.COA2=d.COA2
                                                       LEFT JOIN dbmaster.coa_level1 e ON e.COA1=d.COA1 WHERE d.DIVISI2='OTHER' ";
                                                    $query .=" order by b.COA4";
                                                    $tampil = mysqli_query($cnmy, $query);
                                                    while ($z= mysqli_fetch_array($tampil)) {
                                                        $ncoa4=$z['COA4'];
                                                        $nnmcoa4=$z['NAMA4'];
                                                        
                                                        if ($pcoaasli==$ncoa4)
                                                            echo "<option value='$ncoa4' selected>$ncoa4 - $nnmcoa4 (OTHER)</option>";
                                                        else
                                                            echo "<option value='$ncoa4'>$ncoa4 - $nnmcoa4 (OTHER)</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Keterangan <span class='required'></span></label>
                                            <div class='col-md-6'>
                                                <textarea id='e_ket' name='e_ket' rows='4' cols='55' placeholder='Aktivitas' readonly><?PHP echo $pketerangan; ?></textarea>
                                            </div>
                                        </div>

                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''> <span class='required'></span></label>
                                            <div class='col-xs-9'>
                                                <div class="checkbox">
                                                    <button type='button' id='nm_btn_save' class='btn btn-success' onclick='disp_confirm_editbr0("Simpan ?", "<?PHP echo $act; ?>")'>Save</button>
                                                </div>
                                            </div>
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
        
        
        <div class='modal-footer'>
            <button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
        </div>
    </div>
</div>

<script type='text/javascript' src='datetime/js/jquery-ui.min.js'></script>

<script>
    function disp_confirm_editbr0(pText_,nid)  {
        //e_idbr, e_nomorid, e_stspros, e_apppil, cb_coapil
        var eact="simpaneditdatabr";
        var eidbr = document.getElementById("e_idbr").value;
        var enobridr = document.getElementById("e_rincinoid").value;
        var eidauto = document.getElementById("e_nomorid").value;
        var estspros = document.getElementById("e_stspros").value;
        var eapppil = document.getElementById("e_apppil").value;
        var ecoapil = document.getElementById("cb_coapil").value;
        var easlicoapil = document.getElementById("e_aslicoa").value;
        
        
        var eakuneditid = document.getElementById("cb_akunedit").value;
        var ekdakunasli = document.getElementById("e_kodeasliakun").value;
        var enmakunasli = document.getElementById("e_namaasliakun").value;
        
        var eiddokternmedit = document.getElementById("cb_iddoker").value;
        var easliiddokter = document.getElementById("e_idaslidokter").value;
        var easlinmdokter = document.getElementById("e_namaaslidokter").value;
        
        
        ok_ = 1;
        if (ok_) {
            var r=confirm(pText_)
            if (r==true) {
                var myurl = window.location;
                var urlku = new URL(myurl);
                var module = urlku.searchParams.get("module");
                var idmenu = urlku.searchParams.get("idmenu");
                
                $.ajax({
                    type:"post",
                    url:"module/laporan_gl/mod_generalledger/simpan_data_edit_br0.php?module="+module+"&act="+eact+"&idmenu="+idmenu,
                    data:"uidbr="+eidbr+"&uidauto="+eidauto+"&ustspros="+estspros+"&uapppil="+eapppil+"&ucoapil="+ecoapil+
                            "&uaslicoapil="+easlicoapil+"&unobridr="+enobridr+"&uakuneditid="+eakuneditid+
                            "&uiddokternmedit="+eiddokternmedit+"&uasliiddokter="+easliiddokter+"&uaslinmdokter="+easlinmdokter+
                            "&ukdakunasli="+ekdakunasli+"&unmakunasli="+enmakunasli,
                    success:function(data){
                        if (data.length > 2) {
                            alert(data);
                        }
                        //nm_btn_save.style.display='none';
                        //$('#myModal').modal('hide');
                    }
                });
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }
    }

</script>