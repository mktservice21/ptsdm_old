<?PHP
    session_start();
    include "../../config/koneksimysqli.php";
    include "../../config/fungsi_combo.php";
    
    $pnospd=$_POST['unospd'];
    $kodestsinput="M";
    
    
    $act="input";
    $pjmlminta=0;
    $edit = mysqli_query($cnmy, "SELECT sum(jumlah) jumlah FROM dbmaster.t_suratdana_br WHERE IFNULL(stsnonaktif,'')<>'Y' AND nomor='$pnospd'");
    $ketemu= mysqli_num_rows($edit);
    if ($ketemu>0) {
        $r    = mysqli_fetch_array($edit);
        $pjmlminta=$r['jumlah'];
        
    }
    
    
    
    $sql = "SELECT nomor, sum(jumlah) as jumlah FROM dbmaster.t_suratdana_bank WHERE ";
    $sql.=" IFNULL(stsnonaktif,'')<>'Y' AND IFNULL(stsinput,'')='$kodestsinput' AND nomor='$pnospd' GROUP BY 1";
    $tampil= mysqli_query($cnmy, $sql);
    $adajml= mysqli_num_rows($tampil);
    
    $sudah_input="";
    
    $psudahada_bank="hidden";//asalnya = ""
    $pjmlsudah=0;
    if ($adajml>0) {
        $nt= mysqli_fetch_array($tampil);
        $pjmlsudah=$nt['jumlah'];
        if (empty($pjmlsudah)) $pjmlsudah=0;
        
        $psudahada_bank="hidden";
        $sudah_input="hidden";
    }
    $pjumlah=(double)$pjmlminta-(double)$pjmlsudah;
    
    
    $hari_ini = date("Y-m-d");
    $tgl1 = date('d/m/Y', strtotime($hari_ini));
    
    $idbr="";
    $pketerangan="";
    $pnobukti="";
            
    
    
    
        $edit = mysqli_query($cnmy, "SELECT tglmasuk, nobbm FROM dbmaster.t_suratdana_br WHERE "
                . " IFNULL(stsnonaktif,'')<>'Y' AND IFNULL(nobbm,'')<>'' AND nomor='$pnospd'");
        $ketemu= mysqli_num_rows($edit);
        if ($ketemu>0) {
            $r    = mysqli_fetch_array($edit);
            $pnobukti=$r['nobbm'];
            
            if (!empty($r['tglmasuk'])) $hari_ini=$r['tglmasuk'];
            $tgl1 = date('d/m/Y', strtotime($hari_ini));

        }
        
        
        
        if (empty($pnobukti)) {
            $pblnini = date('m', strtotime($hari_ini));
            $pthnini = date('Y', strtotime($hari_ini));
            $pthnini_bln = date('Ym', strtotime($hari_ini));
            $tno="1501";
            $query = "SELECT LTRIM(REPLACE(MAX(SUBSTRING_INDEX(nobbm, '/', 1)),'BBM','')) as nobbm FROM dbmaster.t_suratdana_br 
                WHERE IFNULL(stsnonaktif,'') <> 'Y' AND DATE_FORMAT(tglmasuk,'%Y%m')='$pthnini_bln'";
            $showkan= mysqli_query($cnmy, $query);
            $ketemu= mysqli_num_rows($showkan);
            if ($ketemu>0){
                $sh= mysqli_fetch_array($showkan);
                if (!empty($sh['nobbm'])) { $tno=(INT)$sh['nobbm']+1; }
                if ((double)$tno==1) $tno="1501";
            }
            $mbulan=CariBulanHuruf($pblnini);
            $pnobukti = "BBM".$tno."/".$mbulan."/".$pthnini;
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
            <h4 class='modal-title'>Isi Bukti (No BBM) dan Bank dari SPD</h4>
        </div>

        
        
        <div class="">

            <!--row-->
            <div class="row">

                <form method='POST' action='<?PHP echo "$aksi?module=brdanabank&act=input&idmenu=258"; ?>' id='demo-form4' name='form4' data-parsley-validate class='form-horizontal form-label-left'>

                    <div class='col-md-12 col-sm-12 col-xs-12'>
                        <div class='x_panel'>

                            <div class='x_panel'>
                                <div class='x_content'>
                                    <div class='col-md-12 col-sm-12 col-xs-12'>


                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>No. SPD <span class='required'></span></label>
                                            <div class='col-md-4'>
                                                <input type='text' id='e_nospd' name='e_nospd' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnospd; ?>' Readonly>
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
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>No Bukti <span class='required'></span></label>
                                            <div class='col-md-4'>
                                                <input type='text' id='e_bukti' name='e_bukti' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnobukti; ?>'>
                                            </div>
                                        </div>
                                        
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Minta Dana (Rp.) <span class='required'></span></label>
                                            <div class='col-md-4'>
                                                <input type='text' id='e_mintadana' name='e_mintadana' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjmlminta; ?>' Readonly>
                                            </div>
                                        </div>
                                        
                                        
                                        <div <?PHP echo $psudahada_bank; ?> class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Simpan Semua No Divisi<span class='required'></span></label>
                                            <div class='col-xs-5'>
                                                <select class='form-control input-sm' id='cb_banksimpan' name='cb_banksimpan' onchange="ShowSimpanSPDBank()">
                                                    <?PHP
                                                    $pdari_sel1="";
                                                    $pdari_sel2="selected";
                                                    if ($pd_spd=="Y") $pdari_sel2="selected";

                                                    echo "<option value='N' $pdari_sel1>N</option>";
                                                    echo "<option value='Y' $pdari_sel2>Y</option>";
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div id="d_isibank" style="display: block;">
                                        
                                            <div hidden class='form-group'>
                                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Jumlah (Rp.) <span class='required'></span></label>
                                                <div class='col-md-4'>
                                                    <input type='text' id='e_jml' name='e_jml' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjumlah; ?>'>
                                                </div>
                                            </div>

                                            <div hidden class='form-group'>
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

                                        </div>
                                        

                                        <div <?PHP echo $sudah_input; ?> class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''> <span class='required'></span></label>
                                            <div class='col-xs-9'>
                                                <div class="checkbox">
                                                    <button type='button' class='btn btn-success' onclick='disp_confirm_spd_all("Simpan ?", "<?PHP echo $act; ?>")'>Save</button>
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
                                        $sql = "SELECT nomor, nobukti, idinputbank, DATE_FORMAT(tanggal,'%d %M %Y') as tanggal, kodeid, "
                                                . " divisi, FORMAT(jumlah,0,'de_DE') as jumlah, "
                                                . " keterangan ";
                                        $sql.=" FROM dbmaster.t_suratdana_bank ";
                                        $sql.=" WHERE IFNULL(stsnonaktif,'')<>'Y' AND IFNULL(stsinput,'')='$kodestsinput' AND nomor='$pnospd' order by idinputbank";
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
                                            $ni_nomorspd = $row["nomor"];

                                            $ni_hapus="<input type='button' value='Hapus' class='btn btn-danger btn-xs' onClick=\"disp_hapusdata_spd_all('Hapus Data..?', '$ni_nomorspd')\">";
                                            
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
        ShowNoBuktiBBM();
    });
    
    
    function ShowSimpanSPDBank(){
        var e_bank = document.getElementById('cb_banksimpan').value;
        if (e_bank=="" || e_bank=="N"){
            d_isibank.style.display = 'none';
        }else{
            d_isibank.style.display = 'block';
        }
    }
    
    function ShowNoBuktiBBM() { 
        var inospd = document.getElementById('e_nospd').value;
        var inodivisi = "";
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
    
    function disp_hapusdata_spd_all(pText_,nid)  {
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
                    url:"module/mod_br_danabank/simpan_bank_spd.php?module="+module+"&act=hapus&idmenu="+idmenu,
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
    function disp_confirm_spd_all(pText_,ket)  {
        // e_id, e_inputid, e_nospd, e_nodivsi, e_tglmasuk, e_jml, e_ket
        var eact=ket;
        var esimpanbank = document.getElementById("cb_banksimpan").value;
        var enospd = document.getElementById("e_nospd").value;
        var etglmasuk = document.getElementById("e_tglmasuk").value;
        var ebukti = document.getElementById("e_bukti").value;
        var eketerangan = document.getElementById("e_ket").value;
        var emintadana = document.getElementById("e_mintadana").value;
        
        //alert(eact+" : "+enospd+", "+ebukti+", "+etglmasuk+", "+eketerangan+" -- "+esimpanbank); return false;
        
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
                    url:"module/mod_br_danabank/simpan_bank_spd_all.php?module="+module+"&act="+eact+"&idmenu="+idmenu,
                    data:"unospd="+enospd+"&ubukti="+ebukti+"&utglmasuk="+etglmasuk+"&usimpanspd="+esimpanbank+"&uketerangan="+eketerangan+"&umintadana="+emintadana,
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

