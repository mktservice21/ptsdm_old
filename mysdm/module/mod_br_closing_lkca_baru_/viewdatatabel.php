<?PHP date_default_timezone_set('Asia/Jakarta'); session_start(); ?>

<link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />
<link href="css/stylenew.css" rel="stylesheet" type="text/css" />
<script src="js/inputmask.js"></script>

<style>
    #myBtn {
        display: none;
        position: fixed;
        bottom: 20px;
        right: 30px;
        z-index: 99;
        font-size: 18px;
        border: none;
        outline: none;
        background-color: red;
        color: white;
        cursor: pointer;
        padding: 15px;
        border-radius: 4px;
        opacity: 0.5;
    }

    #myBtn:hover {
        background-color: #555;
    }
</style>


<button onclick="topFunction()" id="myBtn" title="Go to top">Top</button>


<?PHP
    ini_set("memory_limit","5000M");
    ini_set('max_execution_time', 0);
    
    
    $_SESSION['CLSETHPERIODE01']=$_POST['utgl'];
    $_SESSION['CLSETHSTS']=$_POST['usts'];
    $_SESSION['CLSETHPILIHPROS']=$_POST['uprosid_sts'];
    $_SESSION['CLSETHBTNPILIH']=$_POST['upilihjenis'];
    $_SESSION['CLSETHPILCA1']=$_POST['ucaperiode1'];
    $_SESSION['CLSETHPILCA2']=$_POST['ucaperiode2'];
    
    
    
    include "../../config/koneksimysqli.php";
    include "../../config/fungsi_sql.php";
    include "../../config/library.php";
    $cnit=$cnmy;
    $tglnow = date("d/m/Y");
    
    //harus ada diseleksi
        $pilih_koneksi="../../config/koneksimysqli.php";
        $ptgl_pillih = $_POST['utgl'];
        $stsreport = $_POST['usts'];
        $pprosid_sts = $_POST['uprosid_sts'];
        $scaperiode1 = $_POST['ucaperiode1'];
        $scaperiode2 = $_POST['ucaperiode2'];
        $iproses_simpandata=false;
        $u_filterkaryawan="";
    //END harus ada diseleksi
    //seleksi data
    include ("seleksi_data_lk_ca.php");
    
    $pjenispilih = $_POST['upilihjenis'];
    
    $pigroupid="";
    $ptgl_pil01= date("Y-m-01", strtotime($ptgl_pillih));
    $ptgl_pil02= date('Y-m-01', strtotime('+1 month', strtotime($ptgl_pillih)));
    $ptgl_pil_sbl= date('Y-m-01', strtotime('-1 month', strtotime($ptgl_pillih)));
    
    $m_periode1 = date("Y-m", strtotime($ptgl_pil01));
    $m_periode2 = date("Y-m", strtotime($ptgl_pil02));
    $m_periode_sbl = date("Y-m", strtotime($ptgl_pil_sbl));
    
    $perBlnThn1 = date("F Y", strtotime($ptgl_pil01));
    $perBlnThn2 = date("F Y", strtotime($ptgl_pil02));
    
    $_SESSION['CLSLKCA']=date("F Y", strtotime($ptgl_pil01));
    
    $nstyle_text=" style='text-align:right; background-color: transparent; border: 0px solid;' ";
    $gtotjumlah=0; $gtotca1=0; $gtotca2=0; $gtotadj=0; $gtotselisih=0; $gtottrans=0; $gtotkurlebihca1=0;
    
    $pidinputpd=""; $pidinputbank="";
    
    $query = "select * from $tmp00";
    $tampil= mysqli_query($cnit, $query);
    $ketemu= mysqli_num_rows($tampil);
    if ($ketemu>0) {
        $nr= mysqli_fetch_array($tampil);
        $pidinputpd = $nr['idinput'];
        $pidinputbank = $nr['idinputbank'];
        if ($pidinputpd=="0") $pidinputpd="";
        if ($pidinputbank=="0") $pidinputbank="";
        
        $pigroupid = $nr['igroup'];
        $gtotjumlah = $nr['saldo'];
        $gtotca1 = $nr['ca1'];
        $gtotca2 = $nr['ca2'];
        $gtotadj = $nr['jml_adj'];
        $gtotselisih = $nr['selisih'];
        $gtottrans = $nr['jmltrans'];
        $gtotkurlebihca1 = $nr['kuranglebihca1'];
    }
    
    
    $pdivnomor="";
    $ptgltrans= date("d F Y");
    $pnobukti="";

    $ppilih_nobukti="";
    $pblnini="";
    $mbulan="";
    $ppilih_blnthn="";
    $pbukti_periode="";
    
    $ptutup_nodivisi="hidden";
    $ptutup_bank="hidden";
    
    
                //untuk permintaan dana
                if ($pjenispilih=="2" AND $stsreport=="C") {
                    $ptutup_nodivisi="";
                            
                    
                    if (!empty($pidinputpd)) {
                        $query = "select nodivisi from dbmaster.t_suratdana_br WHERE idinput='$pidinputpd'";
                        $tampil= mysqli_query($cnit, $query);
                        $ketemu= mysqli_num_rows($tampil);
                        if ($ketemu>0){
                            $sc= mysqli_fetch_array($tampil);
                            $pdivnomor=$sc['nodivisi'];
                        }
                        
                    }
                    
                    if (empty($pdivnomor)) {
                    
                        $bl= date("m", strtotime($ptgl_pil01));
                        $byear= date("y", strtotime($ptgl_pil01));
                        $bl=(int)$bl;
                        $blromawi="I";
                        if ($bl==1) $blromawi="I";
                        if ($bl==2) $blromawi="II";
                        if ($bl==3) $blromawi="III";
                        if ($bl==4) $blromawi="IV";
                        if ($bl==5) $blromawi="V";
                        if ($bl==6) $blromawi="VI";
                        if ($bl==7) $blromawi="VII";
                        if ($bl==8) $blromawi="VIII";
                        if ($bl==9) $blromawi="IX";
                        if ($bl==10) $blromawi="X";
                        if ($bl==11) $blromawi="XI";
                        if ($bl==12) $blromawi="XII";


                        $pkode="2";
                        $psubkode="21";

                        $nobuktinya="";
                        $tno=1;
                        $awal=3;

                        $query = "SELECT MAX(SUBSTRING_INDEX(nodivisi, '/', 1)) as pnomor "
                                . " FROM dbmaster.t_suratdana_br WHERE stsnonaktif<>'Y' AND divisi<>'OTC' AND kodeid='$pkode' AND subkode='$psubkode'";
                        $showkan= mysqli_query($cnit, $query);
                        $ketemu= mysqli_num_rows($showkan);
                        if ($ketemu>0){
                            $sh= mysqli_fetch_array($showkan);
                            if (!empty($sh['pnomor'])) { $tno=(INT)$sh['pnomor']; $tno++; }
                        }

                        $jml=  strlen($tno);
                        $awal=$awal-$jml;
                        if ($awal>=0) $tno=str_repeat("0", $awal).$tno;
                        else $tno=$tno;
                        $noslipurut=$tno."/LK/".$blromawi."/".$byear;
                        $pdivnomor=$noslipurut;
                    
                    }
                    
                }
                //END untuk permintaan dana

                
                //untuk BANK BBK
                if ($pjenispilih=="3" AND $stsreport=="C") {
                    $ptutup_bank="";
                    
                    if (!empty($pidinputbank)) {
                        $query = "select nobukti, tanggal from dbmaster.t_suratdana_bank WHERE idinputbank='$pidinputbank'";
                        $tampil= mysqli_query($cnit, $query);
                        $ketemu= mysqli_num_rows($tampil);
                        if ($ketemu>0){
                            $sc= mysqli_fetch_array($tampil);
                            $pnobukti=$sc['nobukti'];
                            $ptgltrans = date('d F Y', strtotime($sc['tanggal']));
                        }
                        
                    }
                    
                    
                    if (empty($pnobukti)) {
                        
                        include "../../config/fungsi_combo.php";
                        $hari_ini = date("Y-m-d");

                        include "../../module/mod_br_danabank/cari_nomorbukti.php";
                        $ppilih_nobukti=caribuktinomor('2', $hari_ini);// 1=bbm, 2=bbk

                        $pbukti_periode=date('Ym', strtotime($hari_ini));;
                        $pblnini = date('m', strtotime($hari_ini));
                        $pthnini = date('Y', strtotime($hari_ini));
                        $mbulan=CariBulanHuruf($pblnini);
                        $ppilih_blnthn="/".$mbulan."/".$pthnini;
                        $pnobukti = "BBK".$ppilih_nobukti."/".$mbulan."/".$pthnini;
                    
                    }
                }
                //END untuk BANK BBK
    
    
?>


<form method='POST' action='<?PHP echo "?module=$_POST[module]&act=input&idmenu=$_POST[idmenu]"; ?>' id='demo-form5' name='form5' data-parsley-validate class='form-horizontal form-label-left'>
    
    
    <input type='hidden' id='u_module' name='u_module' value='<?PHP echo $_POST['module']; ?>' Readonly>
    <input type='hidden' id='u_idmenu' name='u_idmenu' value='<?PHP echo $_POST['idmenu']; ?>' Readonly>
    <input type='hidden' id='u_tgl1' name='u_tgl1' value='<?PHP echo $ptgl_pil01; ?>' Readonly>
    <input type='hidden' id='u_act' name='u_act' value='input' Readonly>
    
    
    <div class='x_panel'>
        <div class='x_content'>
            <div class='col-md-12 col-sm-12 col-xs-12'>



                <div id='loading2'></div>
                <div class='col-md-6 col-xs-12'>
                    <div class='x_panel'>
                        <div class='x_content form-horizontal form-label-left'>

                            <div hidden class='form-group'>
                                <label class='control-label col-md-5 col-sm-5 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
                                <div class='col-xs-5'>
                                    <button type='button' id="btnhitung" name="btnhitung" class='btn btn-danger btn-xs' onclick='HitungTotalJumlah()'>Hitung Jumlah</button> <span class='required'></span>
                                    
                                    <input type='hidden' id='e_idgroup' name='e_idgroup' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pigroupid; ?>' Readonly>
                                    <input type='hidden' id='e_idinputpd' name='e_idinputpd' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidinputpd; ?>' Readonly>
                                    <input type='hidden' id='e_idinputbank' name='e_idinputbank' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidinputbank; ?>' Readonly>
                                    <input type='hidden' id='e_per1' name='e_per1' class='form-control col-md-7 col-xs-12' value='<?PHP echo $ptgl_pil01; ?>' Readonly>
                                    <input type='hidden' id='e_per2' name='e_per2' class='form-control col-md-7 col-xs-12' value='<?PHP echo $ptgl_pil02; ?>' Readonly>
                                    <input type='hidden' id='e_sts' name='e_sts' class='form-control col-md-7 col-xs-12' value='<?PHP echo $stsreport; ?>' Readonly>
                                    <input type='hidden' id='e_periode' name='e_periode' class='form-control col-md-7 col-xs-12' value='<?PHP echo $ptgl_pil01; ?>' Readonly>
                                    <input type='hidden' id='e_periodeca1' name='e_periodeca1' class='form-control col-md-7 col-xs-12' value='<?PHP echo $scaperiode1; ?>' Readonly>
                                    <input type='hidden' id='e_periodeca2' name='e_periodeca2' class='form-control col-md-7 col-xs-12' value='<?PHP echo $scaperiode2; ?>' Readonly>
                                    
                                </div>
                            </div>

                            <div class='form-group'>
                                <label class='control-label col-md-5 col-sm-5 col-xs-12' for=''>Saldo Real <span class='required'></span></label>
                                <div class='col-xs-5'>
                                    <input type='text' id='e_saldo' name='e_saldo' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $gtotjumlah; ?>' Readonly>
                                </div>
                            </div>

                            <div class='form-group'>
                                <label class='control-label col-md-5 col-sm-5 col-xs-12' for=''>CA <?PHP echo $perBlnThn1; ?><span class='required'></span></label>
                                <div class='col-xs-5'>
                                    <input type='text' id='e_ca1' name='e_ca1' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $gtotca1; ?>' Readonly>
                                </div>
                            </div>

                            <div class='form-group'>
                                <label class='control-label col-md-5 col-sm-5 col-xs-12' for=''>Kurang / Lebih CA <?PHP echo $perBlnThn1; ?><span class='required'></span></label>
                                <div class='col-xs-5'>
                                    <input type='text' id='e_kuranglebihca1' name='e_kuranglebihca1' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $gtotkurlebihca1; ?>' Readonly>
                                </div>
                            </div>

                            <div class='form-group'>
                                <label class='control-label col-md-5 col-sm-5 col-xs-12' for=''>Selisih <span class='required'></span></label>
                                <div class='col-xs-5'>
                                    <input type='text' id='e_selisih' name='e_selisih' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $gtotselisih; ?>' Readonly>
                                </div>
                            </div>


                        </div>
                    </div>
                </div>



                <div class='col-md-6 col-xs-12'>
                    <div class='x_panel'>
                        <div class='x_content form-horizontal form-label-left'>


                            <div class='form-group'>
                                <label class='control-label col-md-5 col-sm-5 col-xs-12' for=''>CA <?PHP echo $perBlnThn1; ?><span class='required'></span></label>
                                <div class='col-xs-5'>
                                    <input type='text' id='e_ca2' name='e_ca2' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $gtotca2; ?>' Readonly>
                                </div>
                            </div>

                            <div class='form-group'>
                                <label class='control-label col-md-5 col-sm-5 col-xs-12' for=''>Utang Piutang <?PHP echo $m_periode_sbl; ?><span class='required'></span></label>
                                <div class='col-xs-5'>
                                    <input type='text' id='e_jmladj' name='e_jmladj' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $gtotadj; ?>' Readonly>
                                </div>
                            </div>

                            <div class='form-group'>
                                <label class='control-label col-md-5 col-sm-5 col-xs-12' for=''>Jumlah Trsf. <span class='required'></span></label>
                                <div class='col-xs-5'>
                                    <input type='text' id='e_jmltrsf' name='e_jmltrsf' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $gtottrans; ?>' Readonly>
                                </div>
                            </div>

                            <div <?PHP echo $ptutup_nodivisi; ?> class='form-group'>
                                <label class='control-label col-md-5 col-sm-5 col-xs-12' for=''>No BR/Divisi <span class='required'></span></label>
                                <div class='col-xs-5'>
                                    <input type='text' id='e_nomordiv' name='e_nomordiv' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pdivnomor; ?>'>
                                </div>
                            </div>

                            <div <?PHP echo $ptutup_bank; ?> class='form-group'>
                                <label class='control-label col-md-5 col-sm-5 col-xs-12' for=''>Tgl. Trsf. <span class='required'></span></label>
                                <div class='col-xs-5'>
                                    <div class='input-group date' id='tgl1'>
                                        <input type='text' id='e_periode01' name='e_periode01' autocomplete='off' required='required' class='form-control' placeholder='dd/MM/yyyy' value='<?PHP echo $ptgltrans; ?>' data-inputmask="'mask': '99/99/9999'">
                                        <span class="input-group-addon">
                                           <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            
                            <div <?PHP echo $ptutup_bank; ?> id="div_nobukti">
                                <div class='form-group'>
                                    <label class='control-label col-md-5 col-sm-5 col-xs-12' for=''>No Bukti <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <input type='hidden' id='e_bukti_periode' name='e_bukti_periode' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pbukti_periode; ?>' Readonly>
                                        <input type='hidden' id='e_bukti_blnthn' name='e_bukti_blnthn' class='form-control col-md-7 col-xs-12' value='<?PHP echo $ppilih_blnthn; ?>' Readonly>
                                        <input type='hidden' id='e_bukti2' name='e_bukti2' class='form-control col-md-7 col-xs-12 inputmaskrp3' value='<?PHP echo $ppilih_nobukti; ?>' Readonly>
                                        <input type='text' onblur="gantiNoBuktiLabel('e_bukti', 'e_bukti_blnthn')" id='e_bukti' name='e_bukti' class='form-control col-md-7 col-xs-12 inputmaskrp3' value='<?PHP echo $ppilih_nobukti; ?>'>
                                        <label id="lbl_nobukti" style="font-size: 12px; color: blue;"><?PHP echo $pnobukti; ?></label>
                                    </div>
                                </div>
                            </div>
                            

                            <div class='form-group'>
                                <label class='control-label col-md-5 col-sm-5 col-xs-12' for=''> <span class='required'></span></label>
                                <div class='col-xs-5'>
                                    <div class="checkbox">
                                        <?PHP
                                        if ($stsreport=="C") {
                                            if ($pjenispilih=="2") {
                                                if (!empty($pidinputpd)) {
                                                    if (!empty($pidinputbank)) 
                                                        echo "";//sudah ada inputan bank
                                                    else
                                                        echo "<button type='button' class='btn btn-danger' id='btnsave' name='btnsave' onclick=\"disp_confirm_simpan_pd_bukti('Hapus Permintaan Dana ?', 'hapuspd')\">Hapus Permintaan Dana</button>";
                                                }else{
                                                    echo "<button type='button' class='btn btn-dark' id='btnsave' name='btnsave' onclick=\"disp_confirm_simpan_pd_bukti('Simpan Permintaan Dana ?', 'simpandana')\">Simpan Permintaan Dana</button>";
                                                }
                                            }elseif ($pjenispilih=="3") {
                                                if (!empty($pidinputbank)) {
                                                    echo "<button type='button' class='btn btn-danger' id='btnsave' name='btnsave' onclick=\"disp_confirm_simpan_pd_bukti('Hapus Bank, isi no bbk ?', 'hapubankkeluar')\">Hapus Bank</button>";
                                                }else{
                                                    if (empty($pidinputpd))
                                                        echo "belum ada permintaan dana";
                                                    else
                                                        echo "<button type='button' class='btn btn-warning' id='btnsave' name='btnsave' onclick=\"disp_confirm_simpan_pd_bukti('Simpan Bank, isi no bbk ?', 'simpanbankkeluar')\">Simpan Bank</button>";
                                                }
                                            }else{
                                                //tidak bisa dihapus jika masih ada permintaan dana
                                                if (empty($pidinputpd))
                                                    echo "<button type='button' class='btn btn-danger' id='btnsave' name='btnsave' onclick=\"disp_confirm_simpanclose('Hapus ?', 'hapus')\">Hapus</button>";
                                                
                                            }
                                        }else{
                                            echo "<button type='button' class='btn btn-success' id='btnsave' name='btnsave' onclick=\"disp_confirm_simpanclose('Simpan ?', 'simpanclose')\">Simpan Closing</button>";
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>
                </div>



            </div>
        </div>
    </div>
    
    <?PHP
    //untuk PD dan BANK
    if ( ($pjenispilih=="2" OR $pjenispilih=="3") AND $stsreport=="C") {
        mysqli_query($cnit, "DELETE FROM $tmp01");
        //goto hapusdata;
    }
    ?>
    
    
    <div class='x_content'>
        
        <?PHP
            $chkall = "<input type='checkbox' id='chkbtnbr' name='chkbtnbr' value='select' onClick=\"SelAllCheckBox('chkbtnbr', 'chkbox_br[]')\"/>";
            if ($stsreport=="C" OR $stsreport=="S") $chkall="";
        ?>
        <!-- <table id='datatablelkcacls' class='table table-striped table-bordered' width='100%'> -->
        <table id='datatablelkcacls' class='datatable table nowrap table-striped table-bordered' width="100%">
        
            <thead>
                <tr>
                <th width="30px" align="center" nowrap><?PHP echo $chkall; ?></th>
                <th align="center" nowrap>NAMA</th>
                <th align="center" nowrap>DIVISI</th>
                <th align="center" nowrap>No LK</th>
                <th align="center" nowrap>Credit</th>
                <th align="center" nowrap>Saldo REAL</th>
                <th align="center" nowrap>CA<br/><?PHP echo $perBlnThn1; ?></th>
                <th align="center" nowrap>Kurang/<br/>Lebih CA</th>
                <th align="center" nowrap>Selisih</th>
                <th align="center" >CA<br/><?PHP echo $perBlnThn2; ?></th>
                <th align="center" >AR / AP<br/><?PHP echo $m_periode_sbl; ?></th>
                <th align="center" >JUML TRSF</th>
                </tr>
            </thead>
            <tbody>
                <?PHP
                    $gtotjumlah=0; $gtotca1=0; $gtotca2=0; $gtotadj=0; $gtotselisih=0; $gtottrans=0; $gtotkurlebihca1=0;
                    $no=1;
                    $query = "select distinct "
                            . " IFNULL(divisi,'') as divisi, karyawanid, nama_karyawan, 
                                IFNULL(saldo,0) saldo, IFNULL(ca1,0) ca1, IFNULL(ca2,0) ca2, 
                                IFNULL(jml_adj,0) jml_adj, IFNULL(selisih,0) selisih, 
                                IFNULL(jmltrans,0) jmltrans, IFNULL(kuranglebihca1,0) kuranglebihca1  "
                            . " from $tmp01 order by divisi, nama_karyawan, karyawanid";
                    $tampil= mysqli_query($cnit, $query);
                    while ($row= mysqli_fetch_array($tampil)) {
                        $pdivisi=$row['divisi'];
                        $pkaryawanid=$row['karyawanid'];
                        $pnmkaryawan=$row['nama_karyawan'];
                        
                        $prprutin=$row['saldo'];
                        $pca1=$row['ca1'];
                        $pca2=$row['ca2'];
                        $pjumlahadj=$row['jml_adj'];
                        $pselisih=$row['selisih'];
                        $pjmltrans=$row['jmltrans'];
                        $pjmlkuranglebih_ca1=$row['kuranglebihca1'];
                        
                        $ptxt_readonly="";
                        if ($stsreport=="C" OR $stsreport=="S") {
                            $ptxt_readonly="Readonly";
                        }
                        
                        $chkck="";
                        $ceklisnya = "<input type='checkbox' value='$pkaryawanid' onclick=\"HitungJumlahTotalCexBox()\" name='chkbox_br[]' id='chkbox_br[$pkaryawanid]' class='cekbr' $chkck>";
                        
                        $ptxt_saldoreal="<input type='text' value='$prprutin' id='txtsaldo[$pkaryawanid]' name='txtsaldo[$pkaryawanid]' class='' autocomplete='off' size='8px' Readonly $nstyle_text>";
                        $ptxt_1_untukca="<input type='text' value='$pca1' id='txt_1_ca[$pkaryawanid]' name='txt_1_ca[$pkaryawanid]' class='inputmaskrp2 inputbaya' size='8px' Readonly $nstyle_text>";
                        $ptxt_2_untukca="<input type='text' value='$pca2' id='txt_2_ca[$pkaryawanid]' name='txt_2_ca[$pkaryawanid]' class='inputmaskrp2 inputbaya' size='8px' Readonly $nstyle_text>";
                        
                        $ptxt_selisih="<input type='text' value='$pselisih' id='txtselisih[$pkaryawanid]' name='txtselisih[$pkaryawanid]' class='inputmaskrp2 inputbaya' size='8px' Readonly $nstyle_text>";
                        
                        $ptxt_jmladj="<input type='text' value='$pjumlahadj' id='txtjmladj[$pkaryawanid]' name='txtjmladj[$pkaryawanid]' class='' autocomplete='off' size='8px' Readonly $nstyle_text>";
                        
                        $ptxt_transjml="<input type='text' value='$pjmltrans' id='txt_ntrans[$pkaryawanid]' name='txt_ntrans[$pkaryawanid]' class='inputmaskrp2 inputbaya' size='8px' Readonly $nstyle_text>";
                        
                        
                        $txtPilihBluer="'txtsaldo[$pkaryawanid]', 'txt_1_ca[$pkaryawanid]', 'txt_2_ca[$pkaryawanid]', 'txtselisih[$pkaryawanid]', 'txtjmladj[$pkaryawanid]', 'txt_ntrans[$pkaryawanid]', 'txtkuranglebih_1ca[$pkaryawanid]', 'txtsbl[$pkaryawanid]'";
                        
                        $ptxt_kuranglebih_sebelum="<input type='hidden' value='$pjmlkuranglebih_ca1' id='txtsbl[$pkaryawanid]' class='inputmaskrp2' size='8px' Readonly>";
                        $ptxt_kuranglebih="<input type='text' onblur=\"HitungKurangLebihCa1($txtPilihBluer)\" value='$pjmlkuranglebih_ca1' id='txtkuranglebih_1ca[$pkaryawanid]' name='txtkuranglebih_1ca[$pkaryawanid]' class='inputmaskrp2' size='8px' $ptxt_readonly>";
                        
                        //text name tidak bisa lebih banyak, aneh ya
                        
                        $gtotca1=(double)$gtotca1+(double)$pca1;
                        $gtotca2=(double)$gtotca2+(double)$pca2;
                        $gtotadj=(double)$gtotadj+(double)$pjumlahadj;
                        $gtotselisih=(double)$gtotselisih+(double)$pselisih;
                        $gtottrans=(double)$gtottrans+(double)$pjmltrans;
                        $gtotkurlebihca1=(double)$gtotkurlebihca1+(double)$pjmlkuranglebih_ca1;
                        
                        $prprutin=number_format($prprutin,0,",",",");
                        $pca1=number_format($pca1,0,",",",");
                        $pca2=number_format($pca2,0,",",",");
                        $pjumlahadj=number_format($pjumlahadj,0,",",",");
                        $pselisih=number_format($pselisih,0,",",",");
                        $pjmltrans=number_format($pjmltrans,0,",",",");
                        $pjmlkuranglebih_ca1=number_format($pjmlkuranglebih_ca1,0,",",",");
                        
                        
                        if ($stsreport=="C" OR $stsreport=="S") $ceklisnya="";
                            
                        $belum=false;
                        $query = "select * from $tmp01 where karyawanid='$pkaryawanid' order by idrutin";
                        $result2 = mysqli_query($cnit, $query);
                        while ($row2= mysqli_fetch_array($result2)) {
                            
                            $pnolk=$row2['idrutin'];
                            $pidca1=$row2['idca1'];
                            $pidca2=$row2['idca2'];
                            $pketerangan=$row2['keterangan'];
                            $pjumlah=number_format($row2['credit'],0,",",",");
                            $gtotjumlah=$gtotjumlah+$row2['credit'];
                            
                            if ($belum==true) {
                                echo "<tr>";
                                echo "<td></td>";
                                echo "<td></td>";
                                echo "<td></td>";

                                echo "<td nowrap>$pnolk</td>";
                                echo "<td nowrap align='right'>$pjumlah</td>";
                                echo "<td nowrap align='right'></td>";
                                echo "<td nowrap align='right'></td>";
                                echo "<td nowrap align='right'></td>";
                                echo "<td nowrap align='right'></td>";
                                echo "<td nowrap align='right'></td>";
                                echo "<td nowrap align='right'></td>";
                                echo "<td nowrap align='right'></td>";
                                echo "</tr>";

                            }else{
                                if ((double)$pjmltrans < 0) $pjmltrans=0;
                                
                                echo "<tr>";
                                echo "<td>$ceklisnya</td>";
                                echo "<td>$pnmkaryawan</td>";
                                echo "<td>$pdivisi</td>";
                                
                                echo "<td nowrap>$pnolk</td>";//no id blk
                                echo "<td nowrap align='right'>$pjumlah</td>";//credit
                                echo "<td nowrap align='right'>$ptxt_saldoreal</td>";//$ptxt_saldoreal
                                echo "<td nowrap align='right'>$ptxt_1_untukca</td>";//$ptxt_1_untukca
                                echo "<td nowrap align='right'>$ptxt_kuranglebih $ptxt_kuranglebih_sebelum</td>";//$txt_kuranglebih $ptxt_kuranglebih_sebelum
                                echo "<td nowrap align='right'>$ptxt_selisih</td>";//$txt_selisih
                                echo "<td nowrap align='right'>$ptxt_2_untukca</td>";//$pca2
                                echo "<td nowrap align='right'>$ptxt_jmladj</td>";//$ptxt_jmladj
                                echo "<td nowrap align='right'>$ptxt_transjml</td>";//$ptxt_transjml
                                echo "</tr>";

                            }

                            $belum=true;
                            $no++;
                            
                        }
                            
                    }
                    $gtotjumlah=number_format($gtotjumlah,0,",",",");
                    $gtotca1=number_format($gtotca1,0,",",",");
                    $gtotselisih=number_format($gtotselisih,0,",",",");
                    $gtotca2=number_format($gtotca2,0,",",",");
                    $gtotadj=number_format($gtotadj,0,",",",");
                    if ((double)$gtottrans < 0) $gtottrans=0;
                    $gtottrans=number_format($gtottrans,0,",",",");
                    $gtotkurlebihca1=number_format($gtotkurlebihca1,0,",",",");

                    echo "<tr>";
                    echo "<td></td>";
                    echo "<td></td>";
                    echo "<td></td>";

                    echo "<td nowrap></td>";
                    echo "<td nowrap align='right'><b>$gtotjumlah</b></td>";
                    echo "<td nowrap align='right'><b>$gtotjumlah</b></td>";
                    echo "<td nowrap align='right'><b>$gtotca1</b></td>";
                    echo "<td nowrap align='right'><b>$gtotkurlebihca1</b></td>";
                    echo "<td nowrap align='right'><b>$gtotselisih</b></td>";
                    echo "<td nowrap align='right'><b>$gtotca2</b></td>";
                    echo "<td nowrap align='right'><b>$gtotadj</b></td>";
                    echo "<td nowrap align='right'><b>$gtottrans</b></td>";
                    echo "</tr>";
                ?>
            </tbody>
        </table>

    </div>
    
    
    <?PHP
        $gtotjumlah=0; $gtotca1=0; $gtotca2=0; $gtotadj=0; $gtotselisih=0; $gtottrans=0; $gtotkurlebihca1=0;
    ?>

    
    
</form>


<script>
    $(document).ready(function() {
        var dataTable = $('#datatablelkcacls').DataTable( {
            "bPaginate": false,
            "bLengthChange": false,
            "bFilter": true,
            "bInfo": false,
            "ordering": false,
            "order": [[ 0, "desc" ]],
            "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
            "displayLength": -1,
            "columnDefs": [
                { "visible": false },
                { "orderable": false, "targets": 0 },
                { "orderable": false, "targets": 1 },
                { className: "text-right", "targets": [3,4,5,6,7,8,9,10,11] },//right
                { className: "text-nowrap", "targets": [0, 1, 2, 3, 4, 5,6,7,8,9,10,11] }//nowrap

            ],
            "language": {
                "zeroRecords": "Lihat Page di bawah!!! Jika ada Page, Pilih Page 1...!!! Jika tidak ada Page, maka data KOSONG..."
            },
            "scrollY": 440/*,
            "scrollX": true ,
            rowReorder: {
                selector: 'td:nth-child(3)'
            },
            responsive: true*/
        } );
        $('div.dataTables_filter input', dataTable.table().container()).focus();
    } );
    
    
    function SelAllCheckBox(nmbuton, data){
        var checkboxes = document.getElementsByName(data);
        var button = document.getElementById(nmbuton);
        if(button.value == 'select'){
            for (var i in checkboxes){
                checkboxes[i].checked = 'FALSE';
            }
            button.value = 'deselect'
        }else{
            for (var i in checkboxes){
                checkboxes[i].checked = '';
            }
            button.value = 'select';
        }
        HitungJumlahTotalCexBox();
    }
    
    function hilangkanTanda(sText){
        var newchar = '';
        var aText=document.getElementById(sText).value;
        aText = aText.split(',').join(newchar);
        
        if (aText=="") { aText="0"; }
        
        return aText;
    }
    
    function HitungKurangLebihCa1(nSaldo, nCa1, nCa2, nSelisih, nAdj, nTransf, nKLca1, nKLca1_lama) {
        var iSaldo=hilangkanTanda(nSaldo);
        var iCa1=hilangkanTanda(nCa1);
        var iCa2=hilangkanTanda(nCa2);
        var iAdj=hilangkanTanda(nAdj);
        var iKLca1=hilangkanTanda(nKLca1);
        var iKLca1_lama=hilangkanTanda(nKLca1_lama);
        
        var nTotalSel="0";
        var nTotalTrans="0";
        var nTotalCA1="0";
        
        nTotalCA1=(parseInt(iCa1)+parseInt(iKLca1_lama))-parseInt(iKLca1);
        nTotalSel=parseInt(nTotalCA1)-parseInt(iSaldo);//-parseInt(iKLca1)
        
        nTotalTrans=parseInt(iCa2)-parseInt(nTotalSel)+parseInt(iAdj);
        if (parseInt(nTotalSel)>0 && parseInt(iCa2)==0) nTotalTrans="0";
        else if (parseInt(nTotalSel)>0 && parseInt(iCa2)>0) nTotalTrans=parseInt(iCa2) + parseInt(iAdj);
        else if (parseInt(nTotalSel)==0 && parseInt(iCa2)>0) nTotalTrans=parseInt(iCa2) + parseInt(iAdj);
        
        document.getElementById(nCa1).value=nTotalCA1;
        document.getElementById(nSelisih).value=nTotalSel;
        document.getElementById(nTransf).value=nTotalTrans;
        
        document.getElementById(nKLca1_lama).value=iKLca1;
        
        HitungJumlahTotalCexBox();
    }
    
    function HitungJumlahTotalCexBox() {
        var chk_arr1 =  document.getElementsByName('chkbox_br[]');
        var chklength1 = chk_arr1.length;

        var apilih_text="";
        var ajml_rpnya="";
        
        var nRp_saldo="0";
        var nRp_ca1="0";
        var nRp_ca2="0";
        var nRp_selisih="0";
        var nRp_adj="0";
        var nRp_trf="0";
        var nRp_kuranglebihca1="0";
        
        
        for(k=0;k< chklength1;k++)
        {
            if (chk_arr1[k].checked == true) {
                var kata = chk_arr1[k].value;
                var fields = kata.split('-');
                
                apilih_text="txtsaldo["+fields[0]+"]";
                ajml_rpnya=hilangkanTanda(apilih_text);
                nRp_saldo =parseInt(nRp_saldo)+parseInt(ajml_rpnya);
                
                apilih_text="txt_1_ca["+fields[0]+"]";
                ajml_rpnya=hilangkanTanda(apilih_text);
                nRp_ca1 =parseInt(nRp_ca1)+parseInt(ajml_rpnya);
                
                apilih_text="txtselisih["+fields[0]+"]";
                ajml_rpnya=hilangkanTanda(apilih_text);
                nRp_selisih =parseInt(nRp_selisih)+parseInt(ajml_rpnya);
                
                apilih_text="txt_2_ca["+fields[0]+"]";
                ajml_rpnya=hilangkanTanda(apilih_text);
                nRp_ca2 =parseInt(nRp_ca2)+parseInt(ajml_rpnya);
                
                apilih_text="txtjmladj["+fields[0]+"]";
                ajml_rpnya=hilangkanTanda(apilih_text);
                nRp_adj =parseInt(nRp_adj)+parseInt(ajml_rpnya);
                
                apilih_text="txt_ntrans["+fields[0]+"]";
                ajml_rpnya=hilangkanTanda(apilih_text);
                nRp_trf =parseInt(nRp_trf)+parseInt(ajml_rpnya);
                
                apilih_text="txtkuranglebih_1ca["+fields[0]+"]";
                ajml_rpnya=hilangkanTanda(apilih_text);
                nRp_kuranglebihca1 =parseInt(nRp_kuranglebihca1)+parseInt(ajml_rpnya);
                
                
                
            }
        }
        
        document.getElementById('e_saldo').value=nRp_saldo;
        document.getElementById('e_ca1').value=nRp_ca1;
        document.getElementById('e_selisih').value=nRp_selisih;
        document.getElementById('e_ca2').value=nRp_ca2;
        document.getElementById('e_jmladj').value=nRp_adj;
        document.getElementById('e_jmltrsf').value=nRp_trf;
        document.getElementById('e_kuranglebihca1').value=nRp_kuranglebihca1;

    }
    
    function disp_confirm_simpanclose(pText_,ket)  {
        
        var ijml =document.getElementById('e_saldo').value;
        if(ijml==""){
            ijml="0";
        }
        if (ijml=="0") {
            alert("jumlah masih kosong...");
            return false;
        }
        
        
        ok_ = 1;
        if (ok_) {
            var r=confirm(pText_)
            if (r==true) {
                var myurl = window.location;
                var urlku = new URL(myurl);
                var module = urlku.searchParams.get("module");
                var idmenu = urlku.searchParams.get("idmenu");
                //document.write("You pressed OK!")
                document.getElementById("demo-form5").action = "module/mod_br_closing_lkca_baru/aksi_closing_lkca.php?module="+module+"&act="+ket+"&idmenu="+idmenu;
                document.getElementById("demo-form5").submit();
                return 1;
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }
    }
    
    
    function disp_confirm_simpan_pd_bukti(pText_,ket)  {
        var ijml =document.getElementById('e_jmltrsf').value;
        if(ijml==""){
            ijml="0";
        }
        if (ijml=="0") {
            alert("jumlah masih kosong...");
            return false;
        }
        
        ok_ = 1;
        if (ok_) {
            var r=confirm(pText_)
            if (r==true) {
                var myurl = window.location;
                var urlku = new URL(myurl);
                var module = urlku.searchParams.get("module");
                var idmenu = urlku.searchParams.get("idmenu");
                //document.write("You pressed OK!")
                document.getElementById("demo-form5").action = "module/mod_br_closing_lkca_baru/simpan_pd_bukti.php?module="+module+"&act="+ket+"&idmenu="+idmenu;
                document.getElementById("demo-form5").submit();
                return 1;
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
        document.getElementById('lbl_nobukti').innerHTML = "BBK"+noGanti+""+blnThnGanti;
    }
</script>

<script type="text/javascript">
    $(function() {
        $('#e_periode01').datepicker({
            changeMonth: true,
            changeYear: true,
            numberOfMonths: 1,
            firstDay: 1,
            dateFormat: 'dd MM yy',
            /*
            minDate: '0',
            maxDate: '+2Y',
            */
            onSelect: function(dateStr) {
                ShowNoBuktiBBK();
            } 
        });
    });
    
    function ShowNoBuktiBBK() {
        var inospd = "";//document.getElementById('e_nospd').value;
        var inodivisi = "";//document.getElementById('e_nodivsi').value;
        var itgl = document.getElementById('e_periode01').value;

        $.ajax({
            type:"post",
            url:"module/mod_br_danabank/viewdata.php?module=carikontennobuktibbk",
            data:"utgl="+itgl+"&unospd="+inospd+"&unodivisi="+inodivisi,
            success:function(data){
                $("#div_nobukti").html(data);
            }
        });
    }
    
</script>



<script>
    // When the user scrolls down 20px from the top of the document, show the button
    window.onscroll = function() {scrollFunction()};
    function scrollFunction() {
      if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
        document.getElementById("myBtn").style.display = "block";
      } else {
        document.getElementById("myBtn").style.display = "none";
      }
    }

    // When the user clicks on the button, scroll to the top of the document
    function topFunction() {
      document.body.scrollTop = 0;
      document.documentElement.scrollTop = 0;
    }
</script>

<style>
    .divnone {
        display: none;
    }
    #datatablelkcacls th {
        font-size: 13px;
    }
    #datatablelkcacls td { 
        font-size: 11px;
    }
    .imgzoom:hover {
        -ms-transform: scale(3.5); /* IE 9 */
        -webkit-transform: scale(3.5); /* Safari 3-8 */
        transform: scale(3.5);
        
    }
    .inputbaya {
        text-align:right;
        background-color:
        transparent;
        border: 0px solid;
    }
</style>



<?PHP
hapusdata:
    mysqli_query($cnit, "drop TEMPORARY table $tmp00");
    mysqli_query($cnit, "drop TEMPORARY table $tmp01");
    mysqli_query($cnit, "drop TEMPORARY table $tmp02");
    mysqli_query($cnit, "drop TEMPORARY table $tmp03");
    mysqli_query($cnit, "drop TEMPORARY table $tmp04");
    mysqli_query($cnit, "drop TEMPORARY table $tmp05");
?>