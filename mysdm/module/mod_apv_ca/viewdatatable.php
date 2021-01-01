<?php
    session_start();
    include "../../config/koneksimysqli.php";
    include "../../config/fungsi_sql.php";
    
    $kodeinput = " AND kode=3 ";
    
    $cket = $_POST['eket'];
    $mytgl1 = $_POST['uperiode1'];
    $mytgl2 = $_POST['uperiode2'];
    $karyawan = $_POST['ukaryawan'];
    $lvlposisi = $_POST['ulevel'];
    $divisi = $_POST['udiv'];
    $stsapv = $_POST['uketapv'];
    
    
    
    $_SESSION['APVCAISI_KET'] = $cket;
    $_SESSION['APVCAISI_TGL1'] = $mytgl1;
    $_SESSION['APVCAISI_TGL2'] = $mytgl2;
    $_SESSION['APVCAISI_KRY'] = $karyawan;
    $_SESSION['APVCAISI_LVL'] = $lvlposisi;
    $_SESSION['APVCAISI_DIV'] = $divisi;
    $_SESSION['APVCAISI_STSAPV'] = $stsapv;
    
    $tgl1= date("Y-m", strtotime($mytgl1));
    $tgl2= date("Y-m", strtotime($mytgl2));
    
?>

<form method='POST' action='' id='d-form2' name='form2' data-parsley-validate class='form-horizontal form-label-left'>
    
    <div class='x_content' style="margin-left:-20px; margin-right:-20px;">
        
            <div class="title_left">
                <h4 style="font-size : 12px;">
                    <?PHP
                        $noteket = strtoupper($cket);
                        $apvby = "";
                        if ($lvlposisi=="FF2") $apvby = "SPV / AM";
                        if ($lvlposisi=="FF3") $apvby = "DM";
                        if ($lvlposisi=="FF4") $apvby = "SM";
                        if (!empty($apvby)) $apvby = ".&nbsp; &nbsp; Status Karyawan : $apvby";
                        $text="";
                        if ($noteket=="APPROVE") $text="Data Yang Belum DiApprove";
                        if ($noteket=="UNAPPROVE") $text="Data Yang Sudah DiApprove";
                        if ($noteket=="REJECT") $text="Data Yang DiReject";
                        if ($noteket=="PENDING") $text="Data Yang DiPending";
                        if ($noteket=="SEMUA") $text="Data Yang Belum dan Sudah Approve";
                        
                        echo "<b>$text $apvby"
                                . "<p/>&nbsp;*) <span style='color:red;'>klik nama untuk melihat detail pengajuan</span></b>";
                    ?>
                </h4>
            </div><div class="clearfix">
        </div>
        <table id='datatableapvcaisi' class='table table-striped table-bordered' width='100%'>
            <thead>
                <tr>
                    <th width='20px'>
                        <input type="checkbox" id="chkbtnbr" value="select" 
                        onClick="SelAllCheckBox('chkbtnbr', 'chkbox_br[]')" />
                    </th>
                    <th width='100px'>Yg Membuat</th>
                    <th width='50px'>Jumlah</th>
                    <th width='50px'>Tgl Input</th>
                    <th width='50px'>Periode</th>
                    <th width='30px'>Bukti</th>
					<th width='30px'></th>
                    <th width='250px'>Keterangan</th>
                    <th width='30px'>ID</th>
                    <th width='30px'>Approve SPV/AM</th>
                    <th width='30px'>Approve DM</th>
                    <th width='30px'>Approve SM</th>
                    <!--<th width='30px'>Approve GSM</th>-->
                    <th width='30px'>Proses Finance</th>
                </tr>
            </thead>
            <tbody>
                <?PHP
                $sql = "SELECT idca, DATE_FORMAT(tgl,'%d %M %Y') as tgl, DATE_FORMAT(periode,'%M %Y') as bulan, DATE_FORMAT(tgl,'%d/%m/%Y') as periode, "
                        . " divisi, karyawanid, nama, areaid, '' as nama_area, FORMAT(jumlah,0,'de_DE') as jumlah, keterangan 
                        , ifnull(tgl_atasan1,'0000-00-00') tgl_atasan1,
                        ifnull(tgl_atasan2,'0000-00-00') tgl_atasan2,
                        ifnull(tgl_atasan3,'0000-00-00') tgl_atasan3,
                        ifnull(tgl_atasan4,'0000-00-00') tgl_atasan4,
                        ifnull(tgl_fin,'0000-00-00') tgl_fin, jabatanid 
                        "
                        ;
                $sql.=" FROM dbmaster.v_ca0_mydata ";
                $sql.=" WHERE 1=1 AND divisi<>'OTC' ";
                $sql.=" AND Date_format(tgl, '%Y-%m') between '$tgl1' and '$tgl2' ";
                
                if (!empty($divisi)) $sql.=" and divisi in $divisi ";
                
                if (strtoupper($cket)!= "REJECT") $sql.=" AND stsnonaktif <> 'Y' ";
                
                
                
                
                $filterbawahan = "";
                $atasannya = "";
                $tglatasannya = "";
                $tglatasannya_atas = "";
                $tglatasannya_bawah = "";
                $tglatasannya1 = "";
                $filterapv = "";
                $filterregion = "";
                $filterjabatangsm = "";
                
                if ($lvlposisi=="FF2") {
                    $atasannya = "atasan1";
                    $tglatasannya = "tgl_atasan1";
                        $tglatasannya_atas = "tgl_atasan2";
                }elseif ($lvlposisi=="FF3") {
                    $atasannya = "atasan2";
                    $tglatasannya = "tgl_atasan2";
                        $tglatasannya_bawah = "tgl_atasan1";
                        $tglatasannya_atas = "tgl_atasan3";
                }elseif ($lvlposisi=="FF4") {
                    $atasannya = "atasan3";
                    $tglatasannya = "tgl_atasan3";
                        $tglatasannya_bawah = "tgl_atasan2";
                        $tglatasannya_atas = "tgl_atasan4";
                }elseif ($lvlposisi=="FF5" OR $lvlposisi=="FF7") {
                    $tglatasannya = "tgl_atasan4";
                        $tglatasannya_bawah = "tgl_atasan3";
                        
                        //khusus
                        if ($_SESSION['IDCARD']=="0000000159"){
                            $apvnyaats4="";
                            if (strtoupper($cket)=="APPROVE") {
                                $apvnyaats4=" AND ifnull(tgl_atasan4,'')='' ";
                            }elseif (strtoupper($cket)=="UNAPPROVE") {
                                $apvnyaats4=" AND ifnull(tgl_atasan4,'')<>'' ";
                            }
                            $filterjabatangsm = " AND jabatanid in ('20', '05') OR (jabatanid='38' $apvnyaats4 AND stsnonaktif <> 'Y' AND karyawanid in (select distinct karyawanid from dbmaster.t_karyawan_app_gsm where gsm='$_SESSION[IDCARD]'))";
                        }else{
                            //$filterjabatangsm = " AND jabatanid in ('20', '05') ";
							$filterjabatangsm = " AND ( jabatanid in ('20', '05') OR (jabatanid='39' AND karyawanid='0000002297') ) ";
                        }
						
                    if (!empty($_SESSION['REGION'])) {

						if ($_SESSION['IDCARD']=="0000000159"){
							$filterregion = " AND (icabangid in (select icabangid from dbmaster.v_penempatan_des WHERE region='$_SESSION[REGION]') OR karyawanid='$_SESSION[IDCARD]')";
						}else{
							$filterregion = " AND (icabangid in (select icabangid from dbmaster.v_penempatan_des WHERE region='$_SESSION[REGION]') OR karyawanid='$_SESSION[IDCARD]' OR (jabatanid='08' AND karyawanid='0000002297') )";
						}
						
					}
					
                }else{

                }
                
                if (!empty($atasannya)) $filterbawahan = " $atasannya='$karyawan'"; //bawahan
                if (!empty($tglatasannya)) $tglatasannya = "ifnull($tglatasannya,'')"; //tanggal approve
                if (!empty($tglatasannya_bawah)) $tglatasannya_bawah = " AND ifnull($tglatasannya_bawah,'')<>'' "; //tanggal approve palingatas
                if (!empty($tglatasannya_atas)) $tglatasannya_atas = " AND ifnull($tglatasannya_atas,'')='' "; //tanggal approve palingatas
                
                
                if (strtoupper($cket)=="APPROVE") {
                    if (!empty($tglatasannya)) $filterapv = " $tglatasannya=''"; 
                }elseif (strtoupper($cket)=="UNAPPROVE") {
                    if (!empty($tglatasannya)) $filterapv = " $tglatasannya<>''";
                }elseif (strtoupper($cket)=="REJECT") {
                    $sql.=" AND stsnonaktif = 'Y' ";
                    if ($lvlposisi=="FF5" OR $lvlposisi=="FF7") {
                        $sql.=" AND karyawanid = '$_SESSION[IDCARD]' ";
                    }
                }elseif (strtoupper($cket)=="PENDING") {
                    
                }
                
                
                if ( (strtoupper($cket)!="SEMUA") AND strtoupper($cket)!= "REJECT" AND strtoupper($cket)!= "SUDAHFIN") {
                    $sql .= " AND ifnull(validate,'')='' AND ifnull(tgl_fin,'')='' "; //sudah validate
                    
                    if (!empty($filterapv)) $sql .= " AND $filterapv "; //filter tanggal approve
                    if (!empty($tglatasannya_bawah)) $sql .= " $tglatasannya_bawah "; //filter tanggal approve paling atas
                    if (!empty($tglatasannya_atas)) $sql .= " $tglatasannya_atas "; //filter tanggal approve paling atas
                }
                if (!empty($filterbawahan)) $sql .= " AND $filterbawahan "; //filter bawahan
                
                if (strtoupper($cket)== "SUDAHFIN") $sql .= " AND ifnull(tgl_fin,'')<>'' "; //sudah fin
                
                
                $sql.=" $filterregion $filterjabatangsm order by idca";
                //echo $sql;
                $no=1;
                $tampil = mysqli_query($cnmy, $sql);
                while ($row= mysqli_fetch_array($tampil)) {
                    $idno=$row['idca'];
                    $tglbuat = $row["tgl"];
                    $nama = $row["nama"];
                    $nmarea = $row["nama_area"];
                    $bulan = date("F Y", strtotime($row["bulan"]));
                    $periode = $row["periode"];
                    $pbulan = $row["bulan"];
                    $jumlah = $row["jumlah"];
                    $keterangan = $row["keterangan"];
                    $cekbox = "<input type=checkbox value='$idno' name=chkbox_br[]>";
                    
                    $print="<a style='font-size:11px;' title='Print / Cetak' href='#' class='btn btn-info btn-xs' data-toggle='modal' "
                        . "onClick=\"window.open('eksekusi3.php?module=entrybrcash&brid=$idno&iprint=print',"
                        . "'Ratting','width=700,height=500,left=500,top=100,scrollbars=yes,toolbar=yes,status=1,pagescrool=yes')\"> "
                        . "$nama</a>";
                    $pbukti = getfieldcnmy("SELECT idca as lcfields from dbimages.img_ca0 where idca='$idno'");
                    $bukti="";
                    if (!empty($pbukti)) {
                        $bukti="<a title='lihat bukti' href='#' class='btn btn-danger btn-xs' data-toggle='modal' "
                            . "onClick=\"window.open('eksekusi3.php?module=entrybrcash&brid=$idno&iprint=bukti',"
                            . "'Ratting','width=700,height=500,left=500,top=100,scrollbars=yes,toolbar=yes,status=1,pagescrool=yes')\"> "
                            . "Lihat</a>";
                    }
    
                    $apv1="";
                    $apv2="";
                    $apv3="";
                    $apv4="";
                    $apvfin="";
                    
                    $pjabat = $row["jabatanid"];
                    if ((int)$pjabat==20 OR (int)$pjabat==5) {
                    }else{
                        if ($row["tgl_atasan1"] <> "0000-00-00") $apv1=date("d F Y, h:i:s", strtotime($row["tgl_atasan1"]));
                        if ($row["tgl_atasan2"] <> "0000-00-00") $apv2=date("d F Y, h:i:s", strtotime($row["tgl_atasan2"]));
                    }
                    if ($row["tgl_atasan3"] <> "0000-00-00") $apv3=date("d F Y, h:i:s", strtotime($row["tgl_atasan3"]));
                    if ($row["tgl_atasan4"] <> "0000-00-00") $apv4=date("d F Y, h:i:s", strtotime($row["tgl_atasan4"]));
                    if ($row["tgl_fin"] <> "0000-00-00") $apvfin=date("d F Y, h:i:s", strtotime($row["tgl_fin"]));
    
                    $peditketeaja="";
                    if (empty($apvfin)) {
                        $peditketeaja="<a title='lihat bukti' href='#' class='btn btn-dark btn-xs' data-toggle='modal' "
                            . "onClick=\"window.open('eksekusi3.php?module=editdataketerutincalk&brid=$idno&iprint=nca',"
                            . "'Ratting','width=600,height=350,left=500,top=100,scrollbars=yes,toolbar=yes,status=1,pagescrool=yes')\"> "
                            . "Edit Ket.</a>";
                    }
					
                    echo "<tr>";
                    echo "<td>$cekbox</td>";
                    echo "<td>$print</td>";
                    echo "<td>$jumlah</td>";
                    echo "<td>$periode</td>";
                    echo "<td>$pbulan</td>";
                    echo "<td>$bukti</td>";
					echo "<td>$peditketeaja</td>";
                    echo "<td>$keterangan</td>";
                    echo "<td>$idno</td>";
                    echo "<td>$apv1</td>";
                    echo "<td>$apv2</td>";
                    echo "<td>$apv3</td>";
                    //echo "<td>$apv4</td>";
                    echo "<td>$apvfin</td>";
                    echo "</tr>";
                    $no++;
                }
                ?>
            </tbody>
        </table>
    </div>
    
<?PHP
if (strtoupper($cket)=="UNAPPROVE") {
?>
    <div class='clearfix'></div>
    <div class="well" style="margin-top: -5px; margin-bottom: 5px; padding-top: 10px; padding-bottom: 6px;"><!--overflow: auto; -->
        <?PHP
        if (strtoupper($cket)=="APPROVE") {
            ?>
            <!--<input class='btn btn-default' type='Submit' name='buttonapv' value='Approve'>-->
            <!--<input class='btn btn-danger' type='button' name='buttonapv' value='Reject' 
                   onClick="ProsesData('reject', 'chkbox_br[]')"> dipindah ke ttd-->
            <input class='btn btn-default' type='hidden' name='buttonapv' value='Pending' 
                   onClick="ProsesData('pending', 'chkbox_br[]')">
            <?PHP
        }elseif (strtoupper($cket)=="UNAPPROVE") {
            ?>
            <input class='btn btn-success' type='button' name='buttonapv' value='UnApprove' 
                   onClick="ProsesData('unapprove', 'chkbox_br[]')">
            <?PHP
        }elseif (strtoupper($cket)=="REJECT") {
        }elseif (strtoupper($cket)=="PENDING") {
        }elseif (strtoupper($cket)=="SEMUA") {
        }
        ?>
    </div>
<?PHP
}
?>
    
    <div class='clearfix'></div>


    <!-- tanda tangan -->
    <?PHP
        if (strtoupper($cket)=="APPROVE") {
            echo "<div class='col-sm-5'>";
            include "ttd_appvca.php";
            echo "</div>";
        }
    ?>
</form>

<script>
    $(document).ready(function() {
        //alert(etgl1);
        var dataTable = $('#datatableapvcaisi').DataTable( {
            //"stateSave": true,
            "order": [[ 7, "desc" ]],
            "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
            "displayLength": 10,
            "columnDefs": [
                { "visible": false },
                { "orderable": false, "targets": 0 },
                { "orderable": false, "targets": 1 },
				{ "orderable": false, "targets": 6 },
                { className: "text-right", "targets": [2] },//right
                { className: "text-nowrap", "targets": [0, 1, 2, 3, 4, 5] }//nowrap

            ],
            "language": {
                "zeroRecords": "Lihat Page di bawah!!! Jika ada Page, Pilih Page 1...!!! Jika tidak ada Page, maka data KOSONG..."
            },
            rowReorder: {
                selector: 'td:nth-child(3)'
            },
            responsive: true
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
    }
    
    function ProsesData(ket, cekbr){
        var cmt = confirm('Apakah akan melakukan proses '+ket+' ...?');
        if (cmt == false) {
            return false;
        }
        var chk_arr =  document.getElementsByName(cekbr);
        var chklength = chk_arr.length;             
        var allnobr="";
        for(k=0;k< chklength;k++)
        {
            if (chk_arr[k].checked == true) {
                allnobr =allnobr + "'"+chk_arr[k].value+"',";
            }
        }
        if (allnobr.length > 0) {
            var lastIndex = allnobr.lastIndexOf(",");
            allnobr = "("+allnobr.substring(0, lastIndex)+")";
        }else{
            alert("Tidak ada data yang diproses...!!!");
            return false;
        }
        
        var txt;
        if (ket=="reject" || ket=="pending") {
            var textket = prompt("Masukan alasan "+ket+" : ", "");
            if (textket == null || textket == "") {
                txt = textket;
            } else {
                txt = textket;
            }
        }
        
        var ekaryawan=document.getElementById('e_idkaryawan').value;
        var elevel=document.getElementById('e_lvlposisi').value;
            
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var idmenu = urlku.searchParams.get("idmenu");
        
        $.ajax({
            type:"post",
            url:"module/mod_apv_ca/aksi_apvca.php?module="+module+"&idmenu="+idmenu+"&act="+ket,
            data:"ket=approve"+"&unobr="+allnobr+"&ukaryawan="+ekaryawan+"&ulevel="+elevel+"&ketrejpen="+txt,
            success:function(data){
                pilihData(ket);
                alert(data);
            }
        });
        
    }
</script>

<style>
    .divnone {
        display: none;
    }
    #datatableapvcaisi th {
        font-size: 13px;
    }
    #datatableapvcaisi td { 
        font-size: 11px;
    }
    .imgzoom:hover {
        -ms-transform: scale(3.5); /* IE 9 */
        -webkit-transform: scale(3.5); /* Safari 3-8 */
        transform: scale(3.5);
        
    }
</style>

