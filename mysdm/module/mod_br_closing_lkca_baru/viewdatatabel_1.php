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
    
    session_start();
    include "../../config/koneksimysqli.php";
    include "../../config/fungsi_sql.php";
    include "../../config/library.php";
    $cnit=$cnmy;
    $tglnow = date("d/m/Y");
    
    //harus ada diseleksi
        $pilih_koneksi="../../config/koneksimysqli.php";
        $ptgl_pillih = $_POST['utgl'];
        $stsreport = $_POST['usts'];
        $scaperiode1 = $_POST['ucaperiode1'];
        $scaperiode2 = $_POST['ucaperiode2'];
        $iproses_simpandata=false;
    //END harus ada diseleksi
    //seleksi data
    include ("seleksi_data_lk_ca.php");
    
    /*
    $now=date("mdYhis");
    $puserid=$_SESSION['USERID'];
    $tmp01 =" dbtemp.DTBRRETRLCLS01_".$puserid."_$now ";
    $tmp02 =" dbtemp.DTBRRETRLCLS02_".$puserid."_$now ";
    $tmp03 =" dbtemp.DTBRRETRLCLS03_".$puserid."_$now ";
    $tmp04 =" dbtemp.DTBRRETRLCLS04_".$puserid."_$now ";
    $tmp01="dbtemp.dtbrretrlcls01_1854_10092019085028";
    */
    //mysqli_query($cnit, "DELETE FROM $tmp01 where karyawanid NOT IN ('0000001437', '0000001927', '0000001263')");//, '0000002240'
    
    $ptgl_pil01= date("Y-m-01", strtotime($ptgl_pillih));
    $ptgl_pil02= date('Y-m-01', strtotime('+1 month', strtotime($ptgl_pillih)));
    $ptgl_pil_sbl= date('Y-m-01', strtotime('-1 month', strtotime($ptgl_pillih)));
    
    $m_periode1 = date("Y-m", strtotime($ptgl_pil01));
    $m_periode2 = date("Y-m", strtotime($ptgl_pil02));
    $m_periode_sbl = date("Y-m", strtotime($ptgl_pil_sbl));
    
    $perBlnThn1 = date("F Y", strtotime($ptgl_pil01));
    $perBlnThn2 = date("F Y", strtotime($ptgl_pil02));
    
    $_SESSION['CLSLKCA']=date("F Y", strtotime($ptgl_pil01));
    
    $ptgltrans="";
    $pnobukti="";
    
    $gtotjumlah=0; $gtotca1=0; $gtotca2=0; $gtotadj=0; $gtotselisih=0; $gtottrans=0; $gtotkurlebihca1=0;
?>


<form method='POST' action='<?PHP echo "?module=$_POST[module]&act=input&idmenu=$_POST[idmenu]"; ?>' id='demo-form5' name='form5' data-parsley-validate class='form-horizontal form-label-left'>
    
    
    <input type='hidden' id='u_module' name='u_module' value='<?PHP echo $_POST['module']; ?>' Readonly>
    <input type='hidden' id='u_idmenu' name='u_idmenu' value='<?PHP echo $_POST['idmenu']; ?>' Readonly>
    <input type='hidden' id='u_tgl1' name='u_tgl1' value='<?PHP echo $ptgl_pil01; ?>' Readonly>
    <input type='hidden' id='u_act' name='u_act' value='input' Readonly>
    
    <input type='hidden' id='e_per1' name='e_per1' class='form-control col-md-7 col-xs-12' value='<?PHP echo $ptgl_pil01; ?>' Readonly>
    <input type='hidden' id='e_per2' name='e_per2' class='form-control col-md-7 col-xs-12' value='<?PHP echo $ptgl_pil02; ?>' Readonly>
    <input type='hidden' id='e_sts' name='e_sts' class='form-control col-md-7 col-xs-12' value='<?PHP echo $stsreport; ?>' Readonly>
    <input type='hidden' id='e_periode' name='e_periode' class='form-control col-md-7 col-xs-12' value='<?PHP echo $ptgl_pil01; ?>' Readonly>
    <input type='hidden' id='e_periodeca1' name='e_periodeca1' class='form-control col-md-7 col-xs-12' value='<?PHP echo $scaperiode1; ?>' Readonly>
    <input type='hidden' id='e_periodeca2' name='e_periodeca2' class='form-control col-md-7 col-xs-12' value='<?PHP echo $scaperiode2; ?>' Readonly>
    

    
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

                            <div class='form-group'>
                                <label class='control-label col-md-5 col-sm-5 col-xs-12' for=''> <span class='required'></span></label>
                                <div class='col-xs-5'>
                                    <div class="checkbox">
                                        <button type='button' class='btn btn-warning' id="btnsave" name="btnsave" onclick='disp_confirm_simpanclose("Simpan ?", "<?PHP echo "simpanclose"; ?>")'>Save</button>
                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>
                </div>



            </div>
        </div>
    </div>
    
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
                    
                    $no=1;
                    $query = "select distinct divisi, karyawanid, nama_karyawan, saldo, ca1, ca2, jml_adj, selisih, jmltrans from $tmp01 order by divisi, nama_karyawan, karyawanid";
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
                        
                        $chkck="";
                        
                        $ceklisnya = "<input type='checkbox' value='$pkaryawanid' onclick=\"HitungJumlahTotalCexBox()\" name='chkbox_br[]' id='chkbox_br[$pkaryawanid]' class='cekbr' $chkck>";
                        
                        $txt_karyawanid="<input type='hidden' value='$pkaryawanid' id='txtkryid[$pkaryawanid]' name='txtkryid[$pkaryawanid]' class='' autocomplete='off' size='8px' Readonly>";
                        $txt_saldoreal="<input type='hidden' value='$prprutin' id='txtsaldo[$pkaryawanid]' name='txtsaldo[$pkaryawanid]' class='' autocomplete='off' size='8px' Readonly>";
                        $ptxt_1_untukca="<input type='text' value='$pca1' id='txt_1_ca[$pkaryawanid]' name='txt_1_ca[$pkaryawanid]' class='' autocomplete='off' size='8px' Readonly>";
                        $ptxt_2_untukca="<input type='hidden' value='$pca2' id='txt_2_ca[$pkaryawanid]' name='txt_2_ca[$pkaryawanid]' class='' autocomplete='off' size='8px' Readonly>";
                        $txt_jmladj="<input type='hidden' value='$pjumlahadj' id='txtjmladj[$pkaryawanid]' name='txtjmladj[$pkaryawanid]' class='' autocomplete='off' size='8px' Readonly>";
                        $txt_selisih="<input type='text' value='$pselisih' id='txtselisih[$pkaryawanid]' name='txtselisih[$pkaryawanid]' class='inputmaskrp2' autocomplete='off' size='8px' Readonly style='text-align:right; background-color: transparent; border: 0px solid;'>";
                        $ptxt_transjml="<input type='text' value='$pjmltrans' id='txt_ntrans[$pkaryawanid]' name='txt_ntrans[$pkaryawanid]' class='inputmaskrp2' autocomplete='off' size='8px' Readonly style='text-align:right; background-color: transparent; border: 0px solid;'>";
                        
                        
                        $txt_ca1="<input type='hidden' id='txtca1[$pkaryawanid]' name='txtca1[$pkaryawanid]' >";
                        $txt_jmltrans="<input type='hidden' id='txtjmltrans[$pkaryawanid]' name='txtjmltrans[$pkaryawanid]' >";
                        
                        
                        
                        $pjmlkuranglebih_ca1="";
                        $txt_kurlebca1_lama="<input type='hidden' value='$pjmlkuranglebih_ca1' id='txtkurleb_lamaca1[$pkaryawanid]' name='txtkurleb_lamaca1[$pkaryawanid]' class='inputmaskrp2' autocomplete='off' size='8px' Readonly>";
                        $txtPilihBluer="'txtsaldo[$pkaryawanid]', 'txt_1_ca[$pkaryawanid]', 'txt_2_ca[$pkaryawanid]', "
                                . " 'txtselisih[$pkaryawanid]', 'txtjmladj[$pkaryawanid]', "
                                . " 'txt_ntrans[$pkaryawanid]', 'txtkuranglebihca1[$pkaryawanid]', 'txtkurleb_lamaca1[$pkaryawanid]'";
                        
                        $txt_kuranglebih="<input type='text' value='$pjmlkuranglebih_ca1' id='txtkuranglebihca1[$pkaryawanid]' "
                                . " name='txtkuranglebihca1[$pkaryawanid]' class='inputmaskrp2' "
                                . " onblur=\"HitungKurangLebihCa1($txtPilihBluer)\""
                                . " autocomplete='off' size='8px'>";

                        $f_textinput="$txt_karyawanid $txt_saldoreal $txt_jmladj $txt_kurlebca1_lama $ptxt_1_untukca $ptxt_2_untukca ";
                        
                        
                        
                        $gtotca1=(double)$gtotca1+(double)$pca1;
                        $gtotca2=(double)$gtotca2+(double)$pca2;
                        $gtotadj=(double)$gtotadj+(double)$pjumlahadj;
                        $gtotselisih=(double)$gtotselisih+(double)$pselisih;
                        $gtottrans=(double)$gtottrans+(double)$pjmltrans;
                        
                        $prprutin=number_format($prprutin,0,",",",");
                        $pca1=number_format($pca1,0,",",",");
                        $pca2=number_format($pca2,0,",",",");
                        $pjumlahadj=number_format($pjumlahadj,0,",",",");
                        $pselisih=number_format($pselisih,0,",",",");
                        $pjmltrans=number_format($pjmltrans,0,",",",");
                        
                        
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
                                echo "<td>$ceklisnya $f_textinput</td>";
                                echo "<td>$pnmkaryawan</td>";
                                echo "<td>$pdivisi</td>";
                                echo "<td nowrap>$pnolk</td>";
                                echo "<td nowrap align='right'>$pjumlah</td>";
                                echo "<td nowrap align='right'>$prprutin</td>";
                                echo "<td nowrap align='right'>$ptxt_1_untukca</td>";//$pca1
                                echo "<td nowrap align='right'>$txt_kuranglebih</td>";
                                echo "<td nowrap align='right'>$txt_selisih</td>";//$pselisih
                                echo "<td nowrap align='right'>$pca2</td>";
                                echo "<td nowrap align='right'>$pjumlahadj</td>";
                                echo "<td nowrap align='right'>$ptxt_transjml $txt_jmltrans</td>";//$pjmltrans
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

                    echo "<tr>";
                    echo "<td></td>";
                    echo "<td></td>";
                    echo "<td></td>";

                    echo "<td nowrap></td>";
                    echo "<td nowrap align='right'><b>$gtotjumlah</b></td>";
                    echo "<td nowrap align='right'><b>$gtotjumlah</b></td>";
                    echo "<td nowrap align='right'><b>$gtotca1</b></td>";
                    echo "<td nowrap align='right'><b></b></td>";
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
                
                apilih_text="txtkuranglebihca1["+fields[0]+"]";
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
</style>



<?PHP
hapusdata:
    mysqli_query($cnit, "drop TEMPORARY table $tmp01");
    mysqli_query($cnit, "drop TEMPORARY table $tmp02");
    mysqli_query($cnit, "drop TEMPORARY table $tmp03");
    mysqli_query($cnit, "drop TEMPORARY table $tmp04");
?>