<?PHP
    date_default_timezone_set('Asia/Jakarta');
    session_start();
    include "../../config/koneksimysqli.php";
    include "../../config/fungsi_combo.php";
    
    $kodestsinput="N";
    
    $pidinput_spd=$_POST['uidinput'];
    $pnospd=$_POST['unospd'];
    $pnodiv=$_POST['unodiv'];
    $pjmlminta=str_replace(",","", $_POST['ujumlah']);
    if (empty($pjmlminta)) $pjmlminta=0;
    
    $sql = "SELECT sum(jumlah) as jumlah FROM dbmaster.t_suratdana_bank WHERE ";
    $sql.=" IFNULL(stsnonaktif,'')<>'Y' AND IFNULL(stsinput,'')='$kodestsinput' AND idinput='$pidinput_spd'";
    $tampil= mysqli_query($cnmy, $sql);
    $nt= mysqli_fetch_array($tampil);
    $pjmlsudah=$nt['jumlah'];
    if (empty($pjmlsudah)) $pjmlsudah=0;
    
    $pjumlah=(double)$pjmlminta-(double)$pjmlsudah;
    
    
    $hari_ini = date("Y-m-d");
    $tgl1 = date('d/m/Y', strtotime($hari_ini));
    
    $idbr="";
    $pketerangan="";
    $pnobukti="";
            
    $p_hidden_set_no="hidden";
    
    
    $ppilih_nobukti="";
    
    
        $edit = mysqli_query($cnmy, "SELECT tanggal, nobukti, LTRIM(REPLACE(MAX(SUBSTRING_INDEX(nobukti, '/', 1)),'BBM','')) as nobbm FROM dbmaster.t_suratdana_bank WHERE "
                . " IFNULL(stsnonaktif,'')<>'Y' AND nomor='$pnospd' AND stsinput='M'");//  AND stsinput='N' AND nodivisi='$pnodiv'
        $ketemu= mysqli_num_rows($edit);
        if ($ketemu>0) {
            $r    = mysqli_fetch_array($edit);
            $pnobukti=$r['nobukti'];
            
            $ppilih_nobukti=$r['nobbm'];
            
            if (!empty($r['tanggal'])) $hari_ini=$r['tanggal'];
            $tgl1 = date('d/m/Y', strtotime($hari_ini));
            
        }
        
        $pbukti_periode=date('Ym', strtotime($hari_ini));
        $pblnini = date('m', strtotime($hari_ini));
        $pthnini = date('Y', strtotime($hari_ini));
        $mbulan=CariBulanHuruf($pblnini);
        $ppilih_blnthn="/".$mbulan."/".$pthnini;
            
        if (empty($pnobukti)) {
        
            include "cari_nomorbukti.php";
            $ppilih_nobukti=caribuktinomor('1', $hari_ini);// 1=bbm, 2=bbm

            $pbukti_periode=date('Ym', strtotime($hari_ini));
            $pblnini = date('m', strtotime($hari_ini));
            $pthnini = date('Y', strtotime($hari_ini));
            $mbulan=CariBulanHuruf($pblnini);
            $ppilih_blnthn="/".$mbulan."/".$pthnini;
            $pnobukti = "BBM".$ppilih_nobukti."/".$mbulan."/".$pthnini;
            
            $p_hidden_set_no="hidden";
        }
            
    
    $act="input";

    $edit = mysqli_query($cnmy, "SELECT * FROM dbmaster.t_suratdana_bank WHERE idinput='$pidinput_spd'");
    $ketemu= mysqli_num_rows($edit);
    if ($ketemu>0) {
        $r    = mysqli_fetch_array($edit);
        //$idbr=$r['idinputbank'];
        if (!empty($idbr)) {
            //$act="update";
        }
        //$tgl1 = date('d/m/Y', strtotime($r['tanggal']));
        //$pketerangan=$r['keterangan'];
        //$pjumlah=$r['jumlah'];
    }
    
    
    
$aksi="";
?>

<!--input mask -->
<script src="js/inputmask.js"></script>

    
<script> window.onload = function() { document.getElementById("e_id").focus(); } </script>

<div class='modal-dialog modal-lg'>
    <!-- Modal content-->
    <div class='modal-content'>
        <div class='modal-header'>
            <button type='button' class='close' data-dismiss='modal'>&times;</button>
            <h4 class='modal-title'>Isi Bank Masuk dari SPD</h4>
        </div>

        
        
        <div class="">

            <!--row-->
            <div class="row">

                <form method='POST' action='<?PHP echo "$aksi?module=brdanabank&act=input&idmenu=258"; ?>' id='demo-form3' name='form3' data-parsley-validate class='form-horizontal form-label-left'>

                    <div class='col-md-12 col-sm-12 col-xs-12'>
                        <div class='x_panel'>

                            <div class='x_panel'>
                                <div class='x_content'>
                                    <div class='col-md-12 col-sm-12 col-xs-12'>

                                        <div hidden class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>ID <span class='required'></span></label>
                                            <div class='col-md-4'>
                                                <input type='text' id='e_id' name='e_id' class='form-control col-md-7 col-xs-12' value='<?PHP echo $idbr; ?>' Readonly>
                                            </div>
                                        </div>

                                        
                                        <div hidden class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>ID INPUT SPD <span class='required'></span></label>
                                            <div class='col-md-4'>
                                                <input type='text' id='e_inputid' name='e_inputid' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidinput_spd; ?>' Readonly>
                                            </div>
                                        </div>

                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>No. SPD <span class='required'></span></label>
                                            <div class='col-md-4'>
                                                <input type='text' id='e_nospd' name='e_nospd' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnospd; ?>' Readonly>
                                            </div>
                                        </div>

                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>No. Divisi / BR <span class='required'></span></label>
                                            <div class='col-md-4'>
                                                <input type='text' id='e_nodivsi' name='e_nodivsi' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnodiv; ?>' Readonly>
                                            </div>
                                        </div>
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Tgl. Masuk </label>
                                            <div class='col-md-3'>
                                                <div class='input-group date' id='mytgl01'>
                                                    <input type="text" class="form-control" id='e_tglmasuk' name='e_tglmasuk' autocomplete='off' required='required' placeholder='dd/MM/yyyy' data-inputmask="'mask': '99/99/9999'" value='<?PHP echo $tgl1; ?>'>
                                                    <span class='input-group-addon'>
                                                        <span class='glyphicon glyphicon-calendar'></span>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <div <?PHP echo $p_hidden_set_no; ?> class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp <span class='required'></span></label>
                                            <div class='col-md-4'>
                                                <input type="checkbox" id="chk_setbbm" name="chk_setbbm" onclick="ShowNoBuktiBBM()"> <b>Set No Terakhir</b>
                                            </div>
                                        </div>

                                        <div id="div_nobukti">
                                            <div class='form-group'>
                                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>No Bukti <span class='required'></span></label>
                                                <div class='col-md-4'>
                                                    <input type='hidden' id='e_bukti_periode' name='e_bukti_periode' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pbukti_periode; ?>' Readonly>
                                                    <input type='hidden' id='e_bukti_blnthn' name='e_bukti_blnthn' class='form-control col-md-7 col-xs-12' value='<?PHP echo $ppilih_blnthn; ?>' Readonly>
                                                    <input type='hidden' id='e_bukti2' name='e_bukti2' class='form-control col-md-7 col-xs-12 inputmaskrp3' value='<?PHP echo $ppilih_nobukti; ?>' Readonly>
                                                    <input type='text' onblur="gantiNoBuktiLabel('e_bukti', 'e_bukti_blnthn')" id='e_bukti' name='e_bukti' class='form-control col-md-7 col-xs-12 inputmaskrp3' value='<?PHP echo $ppilih_nobukti; ?>'>
                                                    <label id="lbl_nobukti" style="font-size: 12px; color: blue;"><?PHP echo $pnobukti; ?></label>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Minta Dana (Rp.) <span class='required'></span></label>
                                            <div class='col-md-4'>
                                                <input type='text' id='e_mintadana' name='e_mintadana' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjmlminta; ?>' Readonly>
                                            </div>
                                        </div>
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Jumlah (Rp.) <span class='required'></span></label>
                                            <div class='col-md-4'>
                                                <input type='text' id='e_jml' name='e_jml' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjumlah; ?>'>
                                            </div>
                                        </div>
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Jml. Masuk (Rp.) <span class='required'></span></label>
                                            <div class='col-md-4'>
                                                <input type='text' id='e_jmlsisa' name='e_jmlsisa' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjmlsudah; ?>' Readonly>
                                            </div>
                                        </div>


                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Keterangan <span class='required'></span></label>
                                            <div class='col-md-4'>
                                                <input type='text' id='e_ket' name='e_ket' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pketerangan; ?>'>
                                            </div>
                                        </div>


                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''> <span class='required'></span></label>
                                            <div class='col-xs-9'>
                                                <div class="checkbox">
                                                    <button type='button' class='btn btn-success' onclick='disp_confirm("Simpan ?", "<?PHP echo $act; ?>")'>Save</button>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                </div>
                            </div>


                        </div>
                    </div>


                </form>

                
                <div class='col-md-12 col-sm-12 col-xs-12'>
                    <div class='x_content'>
                        <div class='x_panel'>
                            <table id='datatableindb' class='table table-striped table-bordered' width='100%'>
                                <thead>
                                    <tr>
                                        <th width='5px'>NO</th>
                                        <th width='20px'></th>
                                        <th width='20px'>ID</th>
                                        <th width='30px' nowrap>TGL. MASUK</th>
                                        <th width='20px'>JENIS</th>
                                        <th width='50px'>PENGAJUAN</th>
                                        <th width='50px'>NO BBM</th>
                                        <th width='20px'>JUMLAH</th>
                                        <th width='200px'>KETERANGAN</th>
                                    </tr>
                                </thead>
                                <body>
                                    <?PHP
                                        $sql = "SELECT nobukti, idinputbank, DATE_FORMAT(tanggal,'%d %M %Y') as tanggal, kodeid, "
                                                . " divisi, FORMAT(jumlah,0,'de_DE') as jumlah, "
                                                . " keterangan ";
                                        $sql.=" FROM dbmaster.t_suratdana_bank ";
                                        $sql.=" WHERE IFNULL(stsnonaktif,'')<>'Y' AND IFNULL(stsinput,'')='$kodestsinput' AND idinput='$pidinput_spd' order by idinputbank";
                                        $query=mysqli_query($cnmy, $sql) or die("mydata.php: get data");
                                        $no=1;
                                        while( $row=mysqli_fetch_array($query) ) {  // preparing an array
                                            
                                            $ni_idno=$row['idinputbank'];
                                            $ni_kodeid="Advance";
                                            if ($row["kodeid"]=="2") $ni_kodeid="Klaim";
                                            if ($row["kodeid"]=="3") $ni_kodeid="Bank";

                                            $ni_tglmasuk = $row["tanggal"];
                                            $ni_divisi = $row["divisi"];
                                            $ni_jumlah = $row["jumlah"];
                                            $ni_ket = $row["keterangan"];
                                            $ni_nobukti = $row["nobukti"];

                                            $ni_hapus="<input type='button' value='Hapus' class='btn btn-danger btn-xs' onClick=\"disp_hapusdata('Hapus Data..?', '$ni_idno')\">";
                                            
                                            echo "<tr>";
                                            echo "<td nowrap>$no<t/d>";
                                            echo "<td nowrap>$ni_hapus<t/d>";
                                            echo "<td nowrap>$ni_idno<t/d>";
                                            echo "<td nowrap>$ni_tglmasuk<t/d>";
                                            echo "<td nowrap>$ni_kodeid<t/d>";
                                            echo "<td nowrap>$ni_divisi<t/d>";
                                            echo "<td nowrap>$ni_nobukti<t/d>";
                                            echo "<td nowrap align='right'>$ni_jumlah<t/d>";
                                            echo "<td>$ni_ket<t/d>";
                                            echo "</tr>";
                                            $no=$no+1;
                                        }
                                    ?>
                                </body>
                            </table>

                        </div>
                    </div>
                </div>
                
            </div>
            <!--end row-->
        </div>
        
        
        <div class='modal-footer'>
            <button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
        </div>
    </div>
</div>


<!-- jquery.inputmask -->
<script src="vendors/jquery.inputmask/dist/min/jquery.inputmask.bundle.min.js"></script>


<script>
    $('#mytgl01, #mytgl02').datetimepicker({
        ignoreReadonly: true,
        allowInputToggle: true,
        format: 'DD/MM/YYYY'
    });
    
    
    $('#mytgl01, #mytgl02').on('change dp.change', function(e){
        //ShowNoBuktiBBM();
        CariDivNoBukti();
    });
    
    
    function ShowNoBuktiBBM() {
        var chkinp=document.getElementById('chk_setbbm');
        if (chkinp.checked == true){
            var inospd = "";
        }else{
            var inospd = document.getElementById('e_nospd').value;
        }
        var inodivisi = document.getElementById('e_nodivsi').value;
        var itgl = document.getElementById('e_tglmasuk').value;
        $.ajax({
            type:"post",
            url:"module/mod_br_danabank/viewdata.php?module=viewnobuktispd",
            data:"utgl="+itgl+"&unospd="+inospd+"&unodivisi="+inodivisi,
            success:function(data){
                document.getElementById('e_bukti').value=data;
            }
        });
    }
    
    
    function disp_hapusdata(pText_,nid)  {
        //alert(nid); return false;
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
                    url:"module/mod_br_danabank/hapus_bank_spd.php?module="+module+"&act=hapus&idmenu="+idmenu,
                    data:"uid="+nid,
                    success:function(data){
                        if (data.length > 2) {
                            alert(data);
                        }
                        $('#myModal').modal('hide');
                    }
                });
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }
    }
    function disp_confirm(pText_,ket)  {
        // e_id, e_inputid, e_nospd, e_nodivsi, e_tglmasuk, e_jml, e_ket
        var eact=ket;
        var eid = document.getElementById('e_id').value;
        var eidinputspd = document.getElementById("e_inputid").value;
        var enospd = document.getElementById("e_nospd").value;
        var enodiv = document.getElementById("e_nodivsi").value;
        var etglmasuk = document.getElementById("e_tglmasuk").value;
        var ejml = document.getElementById("e_jml").value;
        var eketerangan = document.getElementById("e_ket").value;
        var enobukti = document.getElementById("e_bukti").value;
        
        
        var ebuktiperiode = document.getElementById("e_bukti_periode").value;
        var ebuktithnbln = document.getElementById("e_bukti_blnthn").value;
        var ebukti2 = document.getElementById("e_bukti2").value;
        var ebukti = document.getElementById("e_bukti").value;
        
        
        //alert(eact+" : "+eid+", "+eidinputspd+", "+enospd+", "+enodiv+", "+etglmasuk+", "+ejml+", "+eketerangan+", "+enobukti); return false;
        
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
                    url:"module/mod_br_danabank/simpan_bank_spd.php?module="+module+"&act="+eact+"&idmenu="+idmenu,
                    data:"uid="+eid+"&uidinputspd="+eidinputspd+"&unospd="+enospd+"&unodiv="+enodiv+
                            "&utglmasuk="+etglmasuk+"&ujml="+ejml+"&uketerangan="+eketerangan+"&unobukti="+enobukti+"&ubukti="+ebukti+
                            "&ubukti2="+ebukti2+"&ubuktiperiode="+ebuktiperiode+"&ubuktithnbln="+ebuktithnbln,
                    success:function(data){
                        if (data.length > 2) {
                            alert(data);
                        }
                        $('#myModal').modal('hide');
                    }
                });
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }
    }
</script>

<script>
    function gantiNoBuktiLabel(nNoGanti, nBlnThn) {
        var noGanti=document.getElementById(nNoGanti).value;
        var blnThnGanti=document.getElementById(nBlnThn).value;
        document.getElementById('lbl_nobukti').innerHTML = "BBM"+noGanti+""+blnThnGanti;
    }
    
    function CariDivNoBukti() {
        var inospd = document.getElementById('e_nospd').value;
        var inodivisi = "";
        var itgl = document.getElementById('e_tglmasuk').value;
        
        $.ajax({
            type:"post",
            url:"module/mod_br_danabank/viewdata.php?module=satucarikontennobukti",
            data:"utgl="+itgl+"&unospd="+inospd+"&unodivisi="+inodivisi,
            success:function(data){
                $("#div_nobukti").html(data);
            }
        });
    }
</script>