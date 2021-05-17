<?PHP
    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","512M");
    ini_set('max_execution_time', 0);

    session_start();
    include("../../../config/koneksimysqli.php");

    $pidcard="";
    if (isset($_SESSION['IDCARD'])) {
        $pidcard=$_SESSION['IDCARD'];
    }

    if (empty($pidcard)) {
        echo "Anda Harus Login Ulang...";
        exit;
    }

    $hari_ini = date("Y-m-d");
    $tgl1 = date('d/m/Y', strtotime($hari_ini));

    $pidinput_spd=$_POST['uidinput'];
    $pnospd=$_POST['unospd'];
    $pnodiv=$_POST['unodiv'];
    $pjmlminta=str_replace(",","", $_POST['ujumlah']);
    if (empty($pjmlminta)) $pjmlminta=0;

    $pjumlah=0;
    $pjmlsudah=0;
    $pketerangan="";
    $pnobukti="";
    $pidinputbank="";
    $psudahclsbank="";

    $sql = "SELECT sum(jumlah) as jumlah FROM dbmaster.t_suratdana_bank WHERE ";
    $sql.=" IFNULL(stsnonaktif,'')<>'Y' AND ( IFNULL(stsinput,'')='K' ) "
            . " AND idinput='$pidinput_spd' AND subkode NOT IN ('29')";
    $tampil= mysqli_query($cnmy, $sql);
    $nt= mysqli_fetch_array($tampil);
    $pjmlsudah=$nt['jumlah'];
    if (empty($pjmlsudah)) $pjmlsudah=0;

    $pjumlah=(double)$pjmlminta-(double)$pjmlsudah;
    $pjumlah_sisa2=$pjumlah;



    $pact = "simpandatabankkeluar";
?>
<!--input mask -->
<script src="js/inputmask.js"></script>    
<script> window.onload = function() { document.getElementById("e_id").focus(); } </script>

<div class='modal-dialog modal-lg'>
    <!-- Modal content-->
    <div class='modal-content'>


        <div class='modal-header'>
            <button type='button' class='close' data-dismiss='modal'>&times;</button>
            <h4 class='modal-title'>Isi Bank Keluar dari SPD</h4>
        </div>

        <div class="">

            <!--row-->
            <div class="row">

                <form method='POST' action='<?PHP echo "?module=brdanabankbyfin&act=inputbankkeluar&idmenu=505"; ?>' 
                    id='form_pdkeluar' name='form_pdkeluar1' data-parsley-validate class='form-horizontal form-label-left'>

                    <div class='col-md-12 col-sm-12 col-xs-12'>
                        <div class='x_panel'>

                            <div class='x_panel'>
                                <div class='x_content'>
                                    <div class='col-md-12 col-sm-12 col-xs-12'>

                                        <div hidden class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>ID <span class='required'></span></label>
                                            <div class='col-md-4'>
                                                <input type='text' id='e_id' name='e_id' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidinput_spd; ?>' Readonly>
                                                <input type='text' id='e_cardid' name='e_cardid' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidcard; ?>' Readonly>
                                                <input type='text' id='e_stssimpan' name='e_stssimpan' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pact; ?>' Readonly>
                                            </div>
                                        </div>
                                        
                                        <div hidden>
                                            <div class='form-group'>
                                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Id Bank <span class='required'></span></label>
                                                <div class='col-md-4'>
                                                    <input type='text' id='e_bankid' name='e_bankid' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidinputbank; ?>' Readonly>
                                                </div>
                                            </div>
                                        </div>

                                        <div hidden>
                                            <div class='form-group'>
                                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Closing Bank <span class='required'></span></label>
                                                <div class='col-md-4'>
                                                    <input type='text' id='e_stsclsbank' name='e_stsclsbank' class='form-control col-md-7 col-xs-12' value='<?PHP echo $psudahclsbank; ?>' Readonly>
                                                </div>
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
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Tgl. Keluar </label>
                                            <div class='col-md-3'>
                                                <div class='input-group date' id='mytgl01'>
                                                    <input type="text" class="form-control" id='e_tglkeluar' name='e_tglkeluar' autocomplete='off' required='required' placeholder='dd/MM/yyyy' data-inputmask="'mask': '99/99/9999'" value='<?PHP echo $tgl1; ?>'>
                                                    <span class='input-group-addon'>
                                                        <span class='glyphicon glyphicon-calendar'></span>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div hidden id="div_nobukti">
                                            <div class='form-group'>
                                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>No Bukti <span class='required'></span></label>
                                                <div class='col-md-4'>
                                                    <input type='text' id='e_nobukti' name='e_nobukti' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnobukti; ?>' Readonly>
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
                                                <input type='text' id='e_jml' name='e_jml' onblur="hitung_sisa_jumlah('e_mintadana', 'e_jmlsisa', 'e_jml', 'e_jmlsisa_2')" autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjumlah; ?>'>
                                            </div>
                                        </div>
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Jml. Keluar (Rp.) <span class='required'></span></label>
                                            <div class='col-md-4'>
                                                <input type='text' id='e_jmlsisa' name='e_jmlsisa' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjmlsudah; ?>' Readonly>
                                            </div>
                                        </div>

                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Sisa (Rp.) <span class='required'></span></label>
                                            <div class='col-md-4'>
                                                <input type='text' id='e_jmlsisa_2' name='e_jmlsisa_2' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjumlah_sisa2; ?>' Readonly>
                                            </div>
                                        </div>
                                        

                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Keterangan <span class='required'></span></label>
                                            <div class='col-md-4'>
                                                <input type='text' id='e_ket' name='e_ket' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pketerangan; ?>'>
                                            </div>
                                        </div>


                                        <div id="div_nodivisi1">
                                                
                                                <div class='form-group'>
                                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''><span style="color:red;">Dari No BR/Divisi 
                                                            <input type="checkbox" id="chk_drnod" name="chk_drnod" onclick="BukaDivNoBRDari()">
                                                        </span><span class='required'></span></label>
                                                    <div class='col-md-4'>
                                                        <div id="div_brdari" style="display: none;">
                                                            <select class='form-control input-sm' id='cb_nodivisi_dr' name='cb_nodivisi_dr'>
                                                                <option value='' selected>-- Pilihan --</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            
                                            </div>
                                            
                                            
                                            <div class='form-group'>
                                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''> <span class='required'></span></label>
                                                <div class='col-xs-9'>
                                                    <div class="checkbox">
                                                        <button type='button' class='btn btn-success' id='btnsimpan' onclick='disp_confirm_keluar()'>Save</button>
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
                                        <th width='30px' nowrap>TGL. KELUAR</th>
                                        <th width='20px'>JENIS</th>
                                        <th width='50px'>PENGAJUAN</th>
                                        <th width='50px'>NO BBK</th>
                                        <th width='20px'>JUMLAH</th>
                                        <th width='200px'>KETERANGAN</th>
                                    </tr>
                                </thead>
                                <body>
                                    <?PHP
                                        $sql = "SELECT a.nobukti, a.idinputbank, DATE_FORMAT(a.tanggal,'%d/%m/%Y') as tgl, 
                                                DATE_FORMAT(a.tanggal,'%d %M %Y') as tanggal, a.kodeid, a.subkode, "
                                                . " a.divisi, FORMAT(a.jumlah,0,'de_DE') as jumlah, a.jumlah as jml, "
                                                . " a.keterangan, b.bulan ";
                                        $sql.=" FROM dbmaster.t_suratdana_bank as a ";
                                        $sql .=" LEFT JOIN (select distinct DATE_FORMAT(bulan,'%Y%m') as bulan from dbmaster.t_bank_saldo) ";
                                        $sql.=" as b on DATE_FORMAT(a.tanggal,'%Y%m')=b.bulan ";
                                        $sql.=" WHERE IFNULL(a.stsnonaktif,'')<>'Y' AND ( IFNULL(a.stsinput,'')='K' ) "
                                                . " AND a.idinput='$pidinput_spd' order by a.idinputbank";
                                        $query=mysqli_query($cnmy, $sql) or die("mydata.php: get data");
                                        $no=1;
                                        while( $row=mysqli_fetch_array($query) ) {  // preparing an array
                                            
                                            $ni_idno=$row['idinputbank'];
                                            $ni_kodeid=$row['kodeid'];
                                            $ni_subkode=$row['subkode'];
                                            $ni_blncls=$row['bulan'];

                                            $ni_kodeid="Advance";
                                            if ($ni_kodeid=="2") $ni_kodeid="Klaim";
                                            if ($ni_kodeid=="3") $ni_kodeid="Bank";

                                            $ni_tglmasuk = $row["tanggal"];
                                            $ni_tgl = $row["tgl"];
                                            $ni_divisi = $row["divisi"];
                                            $ni_jumlah = $row["jumlah"];
                                            $ni_jml = $row["jml"];
                                            $ni_ket = $row["keterangan"];
                                            $ni_nobukti = $row["nobukti"];

                                            $ni_stscls="";
                                            if (!empty($ni_blncls)) $ni_stscls="sudahclose";
                                            
                                            $ni_hapus="";
                                            $ni_edit="";
                                            //$ni_hapus="<input type='button' value='Hapus' class='btn btn-danger btn-xs' onClick=\"disp_hapusdata_keluar('Hapus Data..?', '$ni_idno')\">";
                                            $ni_edit="<input type='button' value='Edit' class='btn btn-warning btn-xs' onClick=\"disp_edit_data('$ni_idno', '$ni_stscls', '$ni_tgl', '$ni_nobukti', '$ni_jml', '$ni_ket', '$pjmlsudah')\">";

                                            if ($ni_subkode=="29") {
                                                $ni_kodeid="Biaya Trf.";
                                                $ni_hapus="";
                                                $ni_edit="";
                                            }


                                            if ($ni_stscls=="sudahclose") {
                                                $ni_hapus="";
                                                $ni_edit="";
                                            }

                                            echo "<tr>";
                                            echo "<td nowrap>$no<t/d>";
                                            echo "<td nowrap>$ni_edit<t/d>";
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
    $('#mytgl01').datetimepicker({
        ignoreReadonly: true,
        allowInputToggle: true,
        format: 'DD/MM/YYYY'
    });


    function hitung_sisa_jumlah(pMintaDana,pKeluar,pJumlah, pSisa) {
        var nMintaDana = document.getElementById(pMintaDana).value;  
        var nKeluar = document.getElementById(pKeluar).value;
        var nJumlah = document.getElementById(pJumlah).value;
        var total_=0;
        
        var newchar = '';
        var myMintaDana = nMintaDana;  
        myMintaDana = myMintaDana.split(',').join(newchar);
        
        var myKeluar = nKeluar;  
        myKeluar = myKeluar.split(',').join(newchar);
        
        var myJumlah = nJumlah;  
        myJumlah = myJumlah.split(',').join(newchar);
        
        total_ =parseInt(myMintaDana)-(parseInt(myKeluar)+parseInt(myJumlah));
        document.getElementById(pSisa).value = total_;
        
        
    }

    function BukaDivNoBRDari () {
        var epilchk = document.getElementById('chk_drnod').checked;
        if (epilchk==true) {
            div_brdari.style.display = 'block';
            TampilkanDataBR("N");
        }else{
            div_brdari.style.display = 'none';
        }
    }

    function TampilkanDataBR(npilihan) {
        $.ajax({
            type:"post",
            url:"module/budget/viewdatabgt.php?module=caridatanodivdari",
            data:"upilihan="+npilihan,
            success:function(data){
                $("#cb_nodivisi_dr").html(data);
            }
        });
    }
    
    function disp_edit_data(sidbank, sclssts, stgl, snobukti, sjml, sket, sjmlsdh) {
        document.getElementById('e_stssimpan').value = 'updatedibank';
        document.getElementById('e_bankid').value = sidbank;
        document.getElementById('e_stsclsbank').value = sclssts;
        document.getElementById('e_tglkeluar').value = stgl;
        document.getElementById('e_nobukti').value = snobukti;
        document.getElementById('e_jml').value = sjml;
        document.getElementById('e_ket').value = sket;
        div_nodivisi1.style.display = 'none';
        div_nobukti.style.display = 'block';
        btnsimpan.style.display = 'block';

        var newchar = '';
        var e_totkel = "0";
        var njml = sjml; 
        njml = njml.split(',').join(newchar);
        

        var njmlkl = sjmlsdh; 
        njmlkl = njmlkl.split(',').join(newchar);
        if (njml=="") njml="0";
        if (njmlkl=="") njmlkl="0";
        e_totkel=parseFloat(njmlkl)-parseFloat(njml);

        document.getElementById('e_jmlsisa').value=e_totkel;
        document.getElementById('btnsimpan').innerHTML  = "Update";

    }

    function disp_confirm_keluar() {
        btnsimpan.style.display = 'none';
        
        var iid=document.getElementById('e_id').value;
        var iidcard=document.getElementById('e_cardid').value;
        var ists=document.getElementById('e_stssimpan').value;
        var ibnkid=document.getElementById('e_bankid').value;
        var isdhcls=document.getElementById('e_stsclsbank').value;
        var inospd=document.getElementById('e_nospd').value;
        var inodivisi=document.getElementById('e_nodivsi').value;
        var itglkeluar=document.getElementById('e_tglkeluar').value;
        var inobukti=document.getElementById('e_nobukti').value;
        var ijumlah=document.getElementById('e_jml').value;
        var iket=document.getElementById('e_ket').value;
        var inodiv_dari="";
        
        //e_id, e_cardid, e_stssimpan, e_bankid, e_stsclsbank, e_nospd, e_nodivsi, e_tglkeluar, 
        //e_nobukti, e_jml, e_ket, cb_nodivisi_dr

        if (ists=="") {
            alert("tidak ada data yang akan disave...");
            return false;
        }

        if (iid=="") {
            alert("tidak ada data yang akan disave...");
            return false;
        }

        if (iidcard=="") {
            alert("Anda Harus Login Ulang...");
            return false;
        }

        if (inodivisi=="") {
            alert("No Divisi Kosong...");
            return false;
        }

        if (ists=="updatedibank") {


            if (ibnkid=="") {
                alert("ID Bank Kosong...");
                return false;
            }
            if (inobukti=="") {
                alert("No Bukti Kosong...");
                return false;
            }

        }else{
            var epilchk = document.getElementById('chk_drnod').checked;
            if (epilchk==true){
                inodiv_dari = document.getElementById("cb_nodivisi_dr").value;
                if (inodiv_dari=="") {
                    alert("No BR / Divisi tidak ada yang dipilih"); return false;
                }
            }


        }

        if (isdhcls=="sudahclose") {
            alert("Bulan tersebut sudah closing...");
            return false;
        }

        var pText_="pastikan tanggal dan jumlah sudah sesuai...!!!\n\
Tanggal Keluar (Transfer) : "+itglkeluar+"\n\
Jumlah : "+ijumlah+"\n\
No Divisi / BR : "+inodivisi+"\n\
Apakah akan simpan data...?";
        
        if (ists=="updatedibank") {
            pText_="Pastikan kembali data yang akan anda ubah sudah sesuai...!!!\n\
Tanggal Keluar (Transfer) : "+itglkeluar+"\n\
Jumlah : "+ijumlah+"\n\
No Divisi / BR : "+inodivisi+"\n\
No Bukti / No BBK : "+inobukti+"\n\
Apakah akan melakukan update data...?";
        }

        if (ijumlah=="" || ijumlah=="0") {
            alert("Jumlah masih 0 atau kosong...");
        }

        
        ok_ = 1;
        if (ok_) {
            var r=confirm(pText_)
            if (r==true) {
                var myurl = window.location;
                var urlku = new URL(myurl);
                var module = urlku.searchParams.get("module");
                var idmenu = urlku.searchParams.get("idmenu");

                //iid, iidcard, ists, ibnkid, isdhcls, inodivisi, 
                //itglkeluar, inobukti, ijumlah, iket, inodiv_dari

                if (epilchk==true){//JIKA Dari No BR/Divisi

                }else{
                    $.ajax({
                        type:"post",
                        url:"module/budget/bgt_danabank/simpan_bank_spd_keluar_new.php?module="+module+"&act="+ists+"&idmenu="+idmenu,
                        data:"uid="+iid+"&uidcard="+iidcard+"&usts="+ists+
                            "&ubnkid="+ibnkid+"&usdhcls="+isdhcls+"&unodivisi="+inodivisi+
                            "&utglkeluar="+itglkeluar+"&unobukti="+inobukti+
                            "&ujumlah="+ijumlah+"&uket="+iket+
                            "&unodiv_dari="+inodiv_dari,
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


    }

</script>

<?PHP
hapusdata:
    mysqli_close($cnmy);
?>