<?php
    $puserid=$_SESSION['IDCARD'];
    if (empty($puserid)) {
        echo "Anda harus login ulang..."; exit;
    }
    
    //ini_set('memory_limit', '-1');
    ini_set("memory_limit","10G");
    ini_set('max_execution_time', 0);
    
    $skey="1";
    if (isset($_GET['skey'])) {
        $skey=$_GET['skey'];
    }
    
    
    $ptxturl=$_POST['e_txturl'];
    $ptglpil=$_POST['e_periode01'];
    $tgl_pertama=$_POST['e_periode01'];
    $pbulanupload = date("Y-m-d", strtotime($ptglpil));
    
    $tgl_aju=$_POST['e_tglberlaku'];
    $pperiodepilih = str_replace('/', '-', $tgl_aju);
    $ptglpilihupload = date("Y-m-d", strtotime($pperiodepilih));
    $pperiode_ = date("Ym", strtotime($ptglpil));
    
    $_SESSION['TGTUPDPERTPILCB']=$ptglpil;
    
    include ("config/koneksimysqli.php");
    
    $idinputspd="";
    $pnodivisiid="";
    
    
    $now=date("mdYhis");
    $tmp01 =" dbtemp.TMPUPSBPJS_01".$_SESSION['USERID']."_$now ";
    $tmp02 =" dbtemp.TMPUPSBPJS_02".$_SESSION['USERID']."_$now ";
    $tmp03 =" dbtemp.TMPUPSBPJS_03".$_SESSION['USERID']."_$now ";
        
    $query = "CREATE TABLE $tmp01 (
                nourut MEDIUMINT NOT NULL AUTO_INCREMENT,
                periode varchar(6),
                bulan date,
                tanggal date, 
                karyawanid VARCHAR(10) NOT NULL, nama VARCHAR(200), tempat VARCHAR(100), jabatanid VARCHAR(5), nama_jabatan VARCHAR(200), kelas VARCHAR(1), bayar DECIMAL(20,2), 
                ppt DECIMAL(20,2), pkry DECIMAL(20,2), keterangan VARCHAR(250),
                PRIMARY KEY (nourut)
           )";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error CREATE TABLE : $erropesan"; exit; }
        
    $pbolehupload=false;
    $psudaapprove=false;
    $psudaada=false;
    $pnonaktif=false;
    $psudahapvdir=false;
    $psudahapvdir2=false;
    $btnhapus="isi";
    
    if ($skey=="1") {
        $pbolehupload=true;
        
        
        $query = "select periode, dir1_tgl, stsnonaktif, idinput FROM dbmaster.t_spd_bpjs0 WHERE periode='$pperiode_'";
        $tampil= mysqli_query($cnmy, $query);
        $ketemu= mysqli_num_rows($tampil);
        if ($ketemu>0) {
            $nrs= mysqli_fetch_array($tampil);
            $idinputspd=$nrs['idinput'];
            $nperiodep=$nrs['periode'];
            $nstsnon=$nrs['stsnonaktif'];
            $ntgldirapv=$nrs['dir1_tgl'];
            if ($ntgldirapv=="0000-00-00" OR $ntgldirapv=="0000-00-00 00:00:00") $ntgldirapv="";
            if ($nstsnon=="Y") {
                $ntgldirapv="";
                $pnonaktif=true;
            }
            
            if (!empty($ntgldirapv)) {
                
                $psudaapprove=true;
                $pbolehupload=false;
            }
            
            if (!empty($nperiodep)) {
                $psudaada=true;
                if (!empty($idinputspd)) {
                    $query = "select tgl_dir, tgl_dir2 from dbmaster.t_suratdana_br where idinput='$idinputspd'";
                    $tampild= mysqli_query($cnmy, $query);
                    $ketemud= mysqli_num_rows($tampild);
                    if ($ketemud>0) {
                        $ndir= mysqli_fetch_array($tampild);
                        $papprovdir=$ndir['tgl_dir'];
                        if ($papprovdir=="0000-00-00") $papprovdir="";
                        if ($papprovdir=="0000-00-00 00:00:00") $papprovdir="";
                        
                        $papprovdir2=$ndir['tgl_dir2'];
                        if ($papprovdir2=="0000-00-00") $papprovdir2="";
                        if ($papprovdir2=="0000-00-00 00:00:00") $papprovdir2="";
                        
                        
                        if (!empty($papprovdir)) {
                            $pbolehupload=false;
                            $psudahapvdir=true;
                            if (!empty($papprovdir2)) $psudahapvdir2=true;
                        }else{
                            $query = "UPDATE dbmaster.t_suratdana_br SET stsnonaktif='Y' WHERE idinput='$idinputspd' LIMIT 1";
                            mysqli_query($cnmy, $query);
                            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); goto hapusdata; }
                                  
                            $idinputspd="";
                            
                        }
                    }
                    
                }
                
            }
            
        }
    }
    
    
    
    if ($pbolehupload==true) {
        $pjudul="Upload Data SPD BPJS";
        
        
        include("PHPExcel-1.8/Classes/PHPExcel/IOFactory.php");

        $pfile = $_FILES['fileToUpload']['name'];
        $_SESSION['TGTUPDFOLDPILCB']=$pfile;
        
    }else{
        $pjudul="Data SPD BPJS";
        
        if ($psudaapprove==true) {
            $pjudul="<span style='color:red;'>Upload Data gagal (sudah approve)</span>";
            $btnhapus="";
        }else{
        
            $query = "select periode, dir1_tgl, idinput FROM dbmaster.t_spd_bpjs0 WHERE periode='$pperiode_' AND IFNULL(stsnonaktif,'')<>'Y'";
            $tampil= mysqli_query($cnmy, $query);
            $ketemu= mysqli_num_rows($tampil);
            if ($ketemu>0) {
                $nrs= mysqli_fetch_array($tampil);
                $idinputspd=$nrs['idinput'];
                $ntgldirapv=$nrs['dir1_tgl'];
                if ($ntgldirapv=="0000-00-00" OR $ntgldirapv=="0000-00-00 00:00:00") $ntgldirapv="";

                if (!empty($ntgldirapv)) {
                    $pjudul="Data SPD BPJS (sudah approve)";
                    $btnhapus="";
                }
                
                
                    if (!empty($idinputspd)) {
                        $query = "select tgl_dir, tgl_dir2 from dbmaster.t_suratdana_br where idinput='$idinputspd'";
                        $tampild= mysqli_query($cnmy, $query);
                        $ketemud= mysqli_num_rows($tampild);
                        if ($ketemud>0) {
                            $ndir= mysqli_fetch_array($tampild);
                            $papprovdir=$ndir['tgl_dir'];
                            if ($papprovdir=="0000-00-00") $papprovdir="";
                            if ($papprovdir=="0000-00-00 00:00:00") $papprovdir="";
                            
                            $papprovdir2=$ndir['tgl_dir2'];
                            if ($papprovdir2=="0000-00-00") $papprovdir2="";
                            if ($papprovdir2=="0000-00-00 00:00:00") $papprovdir2="";
                            
                            if (!empty($papprovdir)) {
                                $psudahapvdir=true;
                                if (!empty($papprovdir2)) $psudahapvdir2=true;
                            }
                        }

                    }
                
                
            }
            
            $query = "select a.* from dbmaster.t_spd_bpjs a JOIN dbmaster.t_spd_bpjs0 b on a.periode=b.periode WHERE a.periode='$pperiode_' AND IFNULL(b.stsnonaktif,'')<>'Y'";
            $query = "CREATE TEMPORARY TABLE $tmp02 ($query)";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "Error CREATE TABLE 2 : $erropesan"; 
                mysqli_query($cnmy, "DROP  TABLE $tmp01");            mysqli_close($cnmy);
                exit; 
            }

            $query = "select a.*, b.nama from $tmp02 a JOIN hrd.karyawan b on a.karyawanid=b.karyawanid ";
            $query = "CREATE TEMPORARY TABLE $tmp03 ($query)";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "Error CREATE TABLE 3 : $erropesan"; 
                mysqli_query($cnmy, "DROP  TABLE $tmp01"); mysqli_query($cnmy, "DROP  TEMPORARY TABLE $tmp02");           mysqli_close($cnmy);
                exit; 
            }

            $query = "INSERT INTO $tmp01 (periode, tanggal, karyawanid, nama, tempat, kelas, ppt, pkry, bayar, keterangan)"
                    . " select periode, tanggal, karyawanid, nama, tempat, kelas, potongan_pt, potongan_kry, bayar, keterangan from $tmp03";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "Error CREATE TABLE 3 : $erropesan"; 
                mysqli_query($cnmy, "DROP  TABLE $tmp01"); mysqli_query($cnmy, "DROP  TEMPORARY TABLE $tmp02");           mysqli_close($cnmy);
                exit; 
            }

            $query ="select tanggal from $tmp03 WHERE IFNULL(tanggal,'')<>'' AND IFNULL(tanggal,'0000-00-00')<>'0000-00-00' LIMIT 1";
            $tampil= mysqli_query($cnmy, $query);
            $ketemu= mysqli_num_rows($tampil);
            if ($ketemu>0) {
                $nr= mysqli_fetch_array($tampil);
                $ntglp=$nr['tanggal'];
                if (!empty($ntglp)) {
                    $pperiodepilih = date("d/m/Y", strtotime($ntglp));
                }
            }
        
        }
        
    }
    
    $prpjmldarispd=0;
    
    //cari nomor divisi / no br
    $tno=1;
    $awal=3;
    if (empty($idinputspd)) {
        if (empty($ntglp)) $ntglp=date("Y-m-d");
        $tahuninput= date("Y", strtotime($ntglp));

        $bl= date("m", strtotime($ntglp));
        $byear= date("y", strtotime($ntglp));
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

        $pdivsi="HO";
        $pkode = "2";
        $psubkode = "25";

        $query = "SELECT MAX(SUBSTRING_INDEX(nodivisi, '/', 1)) as pnomor FROM dbmaster.t_suratdana_br WHERE "
                . " stsnonaktif<>'Y' AND YEAR(tgl)='$tahuninput' AND DATE_FORMAT(tgl,'%Y%m')>='202005' AND "
                . " kodeid='$pkode' AND subkode='$psubkode' AND IFNULL(nodivisi,'')<>''";
        
        $showkan= mysqli_query($cnmy, $query);
        $ketemu= mysqli_num_rows($showkan);
        if ($ketemu>0){
            $sh= mysqli_fetch_array($showkan);
            if (!empty($sh['pnomor'])) { $tno=(INT)$sh['pnomor']; $tno++; }
        }

        $jml=  strlen($tno);
        $awal=$awal-$jml;

        if ($awal>=0)
            $tno=str_repeat("0", $awal).$tno;
        else
            $tno=$tno;

        $pnodivisiid=$tno."/BPJS/".$blromawi."/".$byear;

    }else{
        $query = "select nodivisi, jumlah from dbmaster.t_suratdana_br WHERE idinput='$idinputspd'";
        $showkan= mysqli_query($cnmy, $query);
        $ketemu= mysqli_num_rows($showkan);
        if ($ketemu>0){
            $sh= mysqli_fetch_array($showkan);
            $pnodivisiid=$sh['nodivisi'];
            $prpjmldarispd=$sh['jumlah'];
        }

    }
    
    
?>

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


<div class="">

    <div class="page-title"><div class="title_left"><h3><?PHP echo $pjudul; ?></h3></div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">

        <?php
        $aksi="module/mod_br_spdbpjs/aksi_spdbpjs.php";
        
                ?>
        
        
                <div class='col-md-12 col-sm-12 col-xs-12'>
                    <div class='x_panel'>
                        
                        <form method='POST' action='<?PHP echo "$aksi?module=$_GET[module]&act=import&idmenu=$_GET[idmenu]"; ?>' id='demo-form3' name='form2' data-parsley-validate class='form-horizontal form-label-left'  enctype='multipart/form-data'>
                            
                        <div class='col-md-12 col-sm-12 col-xs-12'>
                            <div class='x_panel'>
                                
                                    <input type="hidden" class="form-control" id='e_txturl' name='e_txturl' autocomplete="off" value='<?PHP echo $ptxturl; ?>' readonly>
                                    
                                    <div class='col-sm-2'>
                                        Periode
                                        <div class="form-group">
                                            <div class='input-group date' id='cbln01x'>
                                                <input type='text' id='tgl1' name='e_periode01' required='required' class='form-control input-sm' placeholder='tgl awal' value='<?PHP echo $tgl_pertama; ?>' placeholder='dd mmm yyyy' Readonly>
                                            </div>
                                        </div>
                                    </div>

                                    <div class='col-sm-2'>
                                        Tgl. Pengajuan
                                       <div class="form-group">
                                            <div class='input-group date' id='mytgl02x'>
                                                <input type="text" class="form-control" id='e_tglberlaku' name='e_tglberlaku' autocomplete="off" required='required' placeholder='dd/MM/yyyy' data-inputmask="'mask': '99/99/9999'" value='<?PHP echo $pperiodepilih; ?>' readonly>
                                            </div>
                                       </div>
                                   </div>

                                    <div class='col-sm-2'>
                                        No. Divisi
                                       <div class="form-group">
                                            <div class='input-group date' id='mytgl02x'>
                                                <input type="text" class="form-control" id='e_nodivisi' name='e_nodivisi' autocomplete="off" required='required' value='<?PHP echo $pnodivisiid; ?>'>
                                                <input type="hidden" class="form-control" id='e_inputid' name='e_inputid' autocomplete="off" required='required' value='<?PHP echo $idinputspd; ?>'>
                                            </div>
                                       </div>
                                   </div>
                                    
                                    <?PHP
                                    if ($psudahapvdir==true){
                                    ?>
                                    <div class='col-sm-2'>
                                        <label>Data sudah diapprove, tidak bisa diupload ulang atau dihapus...!!!</label>
                                       <div class="form-group">
                                            &nbsp;
                                       </div>
                                   </div>
                                    <?PHP
                                    }
                                    ?>

                                    <div class='col-sm-4'>
                                        <small>&nbsp;</small>
                                       <div class="form-group">
                                           <!--<button type='button' class='btn btn-success btn-xs' onclick='self.history.back()'>Back</button>-->
                                           <?PHP
                                           echo "<a class='btn btn-success btn-xs' id='butbacnk' href='?module=spdbpjs&idmenu=$_GET[idmenu]&act=$_GET[idmenu]'>Back</a>";
                                           if ($btnhapus=="isi") {
                                               $pnmbtnhapus="Batal Upload Data (hapus)";
                                               if (!empty($idinputspd)) $pnmbtnhapus="Hapus Data";
                                               if ($psudahapvdir==false){
                                                    echo "<button type='button' class='btn btn-danger btn-xs' onclick=\"ProsesDataHapus('$pperiode_', '$idinputspd')\">$pnmbtnhapus</button>";
                                               }
                                           }
                                           ?>
                                       </div>
                                   </div>

                            </div>
                            
                            
                                <div class='x_panel'>
                                    <?PHP if (empty($idinputspd)) { ?>
                                        <div class='col-sm-7'>
                                            <label>data belum masuk ke permintaan dana, klik tombol <span style="color:blue;"><u><i>Simpan Dana BPJS</i></u></span> di bawah !!!</label>
                                            <br/>*) tombol <span style="color:blue;"><u><i>Simpan Dana BPJS</i></u></span> sebagai validasi bahwa data yang diupload sudah sesuai.<br/>&nbsp;
                                        </div>
                                    <?PHP } ?>
                                    <div class='col-sm-6'>
                                        
                                        <label>Total Pengajuan (Rp.)</label>
                                        <div class="form-group">
                                            <input type="text" class="form-control inputmaskrp2" id='e_txtrpminta' name='e_txtrpminta' autocomplete="off" required='required' value='<?PHP echo $prpjmldarispd; ?>' Readonly>
                                            <input type="hidden" class="form-control" id='e_txtperiode1' name='e_txtperiode1' autocomplete="off" required='required' value='<?PHP echo $pperiode_; ?>' Readonly>
                                        </div>
                                        <?PHP 
                                        if (empty($idinputspd)) { 
                                            include "ttd_bpjsspd.php";
                                            
                                        ?>
                                        <!--
                                            <div class="form-group">
                                                <button type='button' class='btn btn-dark btn-lg' onclick="ProsesDataSimpan('<?PHP //echo $pperiode_; ?>', 'e_nodivisi', 'e_txturl')">Simpan Dana BPJS</button>
                                            </div>
                                        -->
                                        <?PHP 
                                        }else{ 
                                            if ($psudahapvdir==true) echo "*) Sudah Approve Direktur Marketing<br/>";
                                            else echo "*) <span style='color:red;'><b>Belum Approve COO</b></span><br/>";
                                            
                                            if ($psudahapvdir2==true) echo "*) Sudah Approve Direktur<br/>";
                                            else echo "*) <span style='color:red;'><b>Belum Approve Direktur</b></span><br/>";
                                        ?>
                                        
                                        <?PHP } ?>
                                    </div>
                                    



                                </div>
                            
                        </div>
                            
                            
                        </form>
                        
                        
                        <div id='c-data'>
                            <?PHP
                            if ($pbolehupload==true) {
                                
                                // upload file xls
                                $target = basename($_FILES['fileToUpload']['name']) ;
                                move_uploaded_file($_FILES['fileToUpload']['tmp_name'], "fileupload/temp_file/".$target);


                                // beri permisi agar file xls dapat di baca
                                chmod("fileupload/temp_file/".$_FILES['fileToUpload']['name'],0777);


                                $objPHPExcel = PHPExcel_IOFactory::load("fileupload/temp_file/".$_FILES['fileToUpload']['name']);

                                $jmlrec=0;
                                foreach ($objPHPExcel->getWorksheetIterator() as $worksheet){
                                    $totalrow = $worksheet->getHighestRow();
                                    $jmlrec=0;

                                    $psimpandata=false;
                                    unset($pinsert_data_detail);//kosongkan array

                                    for($row=6; $row<=$totalrow; $row++){
                                        $pfile2 = mysqli_real_escape_string($cnmy, $worksheet->getCellByColumnAndRow(2, $row)->getValue());
                                        $pfile3 = mysqli_real_escape_string($cnmy, $worksheet->getCellByColumnAndRow(3, $row)->getValue());
                                        $pfile4 = mysqli_real_escape_string($cnmy, $worksheet->getCellByColumnAndRow(4, $row)->getValue());
                                        $pfile5 = mysqli_real_escape_string($cnmy, $worksheet->getCellByColumnAndRow(5, $row)->getValue());
                                        $pfile6 = mysqli_real_escape_string($cnmy, $worksheet->getCellByColumnAndRow(6, $row)->getValue());
                                        $pfile7 = mysqli_real_escape_string($cnmy, $worksheet->getCellByColumnAndRow(7, $row)->getCalculatedValue());
                                        $pfile8 = mysqli_real_escape_string($cnmy, $worksheet->getCellByColumnAndRow(8, $row)->getCalculatedValue());
                                        $pfile9 = mysqli_real_escape_string($cnmy, $worksheet->getCellByColumnAndRow(9, $row)->getCalculatedValue());
                                        $pfile10 = mysqli_real_escape_string($cnmy, $worksheet->getCellByColumnAndRow(10, $row)->getCalculatedValue());

                                        if (empty($pfile2) AND empty($pfile3) AND empty($pfile5)) {
                                            continue;
                                        }

                                        if (empty($pfile5) AND empty($pfile6)) {
                                            continue;
                                        }

                                        if (!empty($pfile2)) $pfile2 = str_replace("'", "", $pfile2);
                                        if (!empty($pfile2)) $pfile2 = str_replace(" ", "", $pfile2);
                                        if (!empty($pfile2)) $pfile2 = str_replace("*", "", $pfile2);
                                        if (!empty($pfile2)) $pfile2 = str_replace(",","", $pfile2);

                                        if (!empty($pfile4)) $pfile4 = str_replace("'", "", $pfile4);
                                        if (!empty($pfile4)) $pfile4 = str_replace(" ", "", $pfile4);
                                        if (!empty($pfile4)) $pfile4 = str_replace("*", "", $pfile4);
                                        if (!empty($pfile4)) $pfile4 = str_replace(",","", $pfile4);

                                        if (!empty($pfile3)) $pfile3 = str_replace("'", " ", $pfile3);
                                        if (!empty($pfile3)) $pfile3 = str_replace("*", "", $pfile3);


                                        if (!empty($pfile5)) $pfile5 = str_replace("'", "", $pfile5);
                                        if (!empty($pfile5)) $pfile5 = str_replace(" ", "", $pfile5);
                                        if (!empty($pfile5)) $pfile5 = str_replace("*", "", $pfile5);
                                        if (!empty($pfile5)) $pfile5 = str_replace(",","", $pfile5);

                                        if (!empty($pfile6)) $pfile6 = str_replace("'", "", $pfile6);
                                        if (!empty($pfile6)) $pfile6 = str_replace(" ", "", $pfile6);
                                        if (!empty($pfile6)) $pfile6 = str_replace("*", "", $pfile6);
                                        if (!empty($pfile6)) $pfile6 = str_replace(",","", $pfile6);

                                        if (!empty($pfile7)) $pfile7 = str_replace("'", "", $pfile7);
                                        if (!empty($pfile7)) $pfile7 = str_replace(" ", "", $pfile7);
                                        if (!empty($pfile7)) $pfile7 = str_replace("*", "", $pfile7);
                                        if (!empty($pfile7)) $pfile7 = str_replace(",","", $pfile7);
                                        if ((DOUBLE)$pfile7==0) $pfile7="";

                                        if (!empty($pfile8)) $pfile8 = str_replace("'", "", $pfile8);
                                        if (!empty($pfile8)) $pfile8 = str_replace(" ", "", $pfile8);
                                        if (!empty($pfile8)) $pfile8 = str_replace("*", "", $pfile8);
                                        if (!empty($pfile8)) $pfile8 = str_replace(",","", $pfile8);
                                        if ((DOUBLE)$pfile8==0) $pfile8="";

                                        if (!empty($pfile9)) $pfile9 = str_replace("'", "", $pfile9);
                                        if (!empty($pfile9)) $pfile9 = str_replace(" ", "", $pfile9);
                                        if (!empty($pfile9)) $pfile9 = str_replace("*", "", $pfile9);
                                        if (!empty($pfile9)) $pfile9 = str_replace(",","", $pfile9);
                                        if ((DOUBLE)$pfile9==0) $pfile9="";

                                        if (!empty($pfile10)) $pfile10 = str_replace("'", "", $pfile10);

                                        $jml_pj_kode= strlen($pfile2);
                                        if ($jml_pj_kode<10) {
                                            $awalkodeprod=10-(double)$jml_pj_kode;
                                            $pfile2=str_repeat("0", $awalkodeprod).$pfile2;

                                        }

                                        if ((DOUBLE)$pfile6==0) {
                                            //continue;
                                        }else{

                                            if (empty($pfile7)) $pfile7=(DOUBLE)$pfile6*4/100;
                                            if (empty($pfile8)) $pfile8=(DOUBLE)$pfile6*1/100;
                                            if (empty($pfile9)) $pfile9=(DOUBLE)$pfile6+(DOUBLE)$pfile8;
                                        
                                        }

                                        $pinsert_data_detail[] = "('$pperiode_', '$pbulanupload', '$ptglpilihupload', '$pfile2', '$pfile3', '$pfile6', '$pfile9', '$pfile7', '$pfile8', '$pfile10', '$pfile4')";
                                        $psimpandata=true;

                                        //echo "$pfile6, $pfile7, $pfile8, $pfile9<br/>";

                                    }


                                }


                                if ($psimpandata==true) {
                                    $query_detail="INSERT INTO $tmp01 (periode, bulan, tanggal, karyawanid, nama, kelas, bayar, ppt, pkry, keterangan, tempat) VALUES ".implode(', ', $pinsert_data_detail);
                                    $pinsertdetail = mysqli_query($cnmy, $query_detail);
                                    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error INSERT TABLE : $erropesan"; goto hapusdata; }
                                    
                                    $pqupdatejbt = "UPDATE $tmp01 a JOIN hrd.karyawan b on a.karyawanid=b.karyawanid SET a.jabatanid=b.jabatanid";
                                    $pupdatejabatan = mysqli_query($cnmy, $pqupdatejbt);
                                    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error UPDATE JABATAN : $erropesan"; goto hapusdata; }
                                    
                                    if ($psudaada==false) {
                                        $query = "INSERT INTO dbmaster.t_spd_bpjs0 (periode, bulan, tanggal, userid)VALUES('$pperiode_', '$pbulanupload', '$ptglpilihupload', '$puserid')";
                                    }else{
                                        $psntot="";
                                        if ($pnonaktif==true) $psntot=", stsnonaktif='N', dir1_id=NULL, dir1_tgl=NULL, dir1_gbr=NULL ";
                                        
                                        $query = "UPDATE dbmaster.t_spd_bpjs0 SET idinput=NULL, userid='$puserid', tanggal='$ptglpilihupload' $psntot WHERE periode='$pperiode_' AND "
                                                . " ( IFNULL(dir1_tgl,'')='' OR IFNULL(dir1_tgl,'0000-00-00')='0000-00-00' OR IFNULL(dir1_tgl,'0000-00-00 00:00:00')='0000-00-00 00:00:00' )";
                                    }
                                    mysqli_query($cnmy, $query);
                                    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); goto hapusdata; }
                                    
                                    $query = "DELETE FROM dbmaster.t_spd_bpjs WHERE periode='$pperiode_'";
                                    mysqli_query($cnmy, $query);
                                    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); goto hapusdata; }

                                    mysqli_query($cnmy, "ALTER TABLE dbmaster.t_spd_bpjs AUTO_INCREMENT = 1");
                                    
                                    $query = "INSERT INTO dbmaster.t_spd_bpjs (periode, bulan, tanggal, karyawanid, kelas, potongan_pt, potongan_kry, bayar, keterangan, userid, tempat, nmkry, jabatanid)"
                                            . " select periode, bulan, tanggal, karyawanid, kelas, ppt, pkry, bayar, keterangan, '$puserid' as userid, tempat, nama, jabatanid from $tmp01";
                                    mysqli_query($cnmy, $query);
                                    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); goto hapusdata; }
                                    
                                    //update total bayar
                                    $query = "UPDATE dbmaster.t_spd_bpjs0 a JOIN (select periode, sum(bayar) bayar from $tmp01 WHERE periode='$pperiode_' GROUP BY 1) b on a.periode=b.periode SET a.jumlah=b.bayar WHERE a.periode='$pperiode_'";
                                    mysqli_query($cnmy, $query);
                                    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); goto hapusdata; }
                                    
                                }
                                
                                
                            
                            }
                            
                            
                            ?>
                            
                            
                            <div class='x_content' style="margin-left:-20px; margin-right:-20px;"><!-- -->

                                <!--<table id='dtablepiluptgt' class='table table-striped table-bordered' width='100%'>-->
                                <table id='dtablepiluptgt' class='table table-striped table-bordered' width="100%" border="1px solid black">
                                    <thead>
                                        <tr>
                                            <th width='10px'>No</th>
                                            <th align="center" nowrap>ID</th>
                                            <th align="center" nowrap>NAMA</th>
                                            <th align="center" nowrap>TEMPAT/ CABANG</th>
                                            <th align="center">KELAS</th>
                                            <th align="center" nowrap>BAYAR</th>
                                            <th align="center">KETERANGAN</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?PHP
                                        $no=1;
                                        $ptotal=0;
                                        $query = "select * from $tmp01 order by nama";
                                        $tampil= mysqli_query($cnmy, $query);
                                        $ketemu= mysqli_num_rows($tampil);
                                        if ($ketemu>0) {
                                            while ($row= mysqli_fetch_array($tampil)) {
                                                $nkaryawanid=$row['karyawanid'];
                                                $nkaryawannm=$row['nama'];
                                                $ntempat=$row['tempat'];
                                                $nkelas=$row['kelas'];
                                                $nbayar=$row['bayar'];
                                                $nketerangan=$row['keterangan'];
                                                
                                                if ($nkelas=="0") $nkelas="";
                                                
                                                $ptotal=(double)$ptotal+(double)$nbayar;
                                                $nbayar=number_format($nbayar,0,",",",");
                                                
                                                
                                                echo "<tr>";
                                                echo "<td nowrap>$no</td>";
                                                echo "<td nowrap>$nkaryawanid</td>";
                                                echo "<td nowrap>$nkaryawannm</td>";
                                                echo "<td nowrap>$ntempat</td>";
                                                echo "<td nowrap>$nkelas</td>";
                                                echo "<td nowrap align='right'>$nbayar</td>";
                                                echo "<td >$nketerangan</td>";
                                                echo "</tr>";
                                                
                                                $no++;
                                            }
                                            
                                            $ptotal=number_format($ptotal,0,",",",");
                                            
                                            echo "<tr>";
                                            echo "<td nowrap colspan='5' align='right'><b>TOTAL : </b></td>";
                                            echo "<td class='divnone'></td>";
                                            echo "<td class='divnone'></td>";
                                            echo "<td class='divnone'></td>";
                                            echo "<td class='divnone'></td>";
                                            echo "<td nowrap align='right'><b>$ptotal</b>"
                                                    . "<input type='hidden' id='txt_jmlminta' name='txt_jmlminta' value='$ptotal' Readonly>"
                                                    . "</td>";
                                            echo "<td class='divnone'></td>";
                                            echo "</tr>";
                                            
                                        }
                                        
                                        ?>
                                    </tbody>

                                </table>


                                <script>

                                    $(document).ready(function() {
                                        var dataTable = $('#dtablepiluptgt').DataTable( {
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
                                                { className: "text-right", "targets": [4] },//right
                                                { className: "text-nowrap", "targets": [0, 1, 2,4] }//nowrap

                                            ],
                                            "language": {
                                                "zeroRecords": "Lihat Page di bawah!!! Jika ada Page, Pilih Page 1...!!! Jika tidak ada Page, maka data KOSONG..."
                                            },
                                            "scrollY": 460,
                                            "scrollX": true
                                        } );
                                        $('div.dataTables_filter input', dataTable.table().container()).focus();
                                    } );

                                </script>


                                <style>
                                    .divnone {
                                        display: none;
                                    }
                                    #dtablepiluptgt th {
                                        font-size: 13px;
                                    }
                                    #dtablepiluptgt td { 
                                        font-size: 11px;
                                    }
                                    .imgzoom:hover {
                                        -ms-transform: scale(3.5); /* IE 9 */
                                        -webkit-transform: scale(3.5); /* Safari 3-8 */
                                        transform: scale(3.5);

                                    }
                                </style>

                            </div>
                            
                            
                        </div>
                        
                    </div>
                </div>
                <?PHP

        ?>

    </div>
    <!--end row-->
</div>
<?PHP
hapusdata:
    if ($pbolehupload==true AND $psudaapprove==false)  unlink("fileupload/temp_file/".$_FILES['fileToUpload']['name']);
    mysqli_query($cnmy, "DROP  TABLE $tmp01");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp02");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp03");
?>
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

<script>
    
    $(document).ready(function() {
        var iidspd = document.getElementById('e_inputid').value;
        if (iidspd=="") {
            document.getElementById('e_txtrpminta').value=document.getElementById('txt_jmlminta').value;
        }
    } );
                    
    function ProsesDataHapus(nhapus, iidinput){

        ok_ = 1;
        if (ok_) {
            var r = confirm('Apakah akan melakukan proses hapus ...?');
            if (r==true) {

                var txt="";


                var myurl = window.location;
                var urlku = new URL(myurl);
                var module = urlku.searchParams.get("module");
                var idmenu = urlku.searchParams.get("idmenu");
                var nmodul = urlku.searchParams.get("nmodul");

                //document.write("You pressed OK!")
                document.getElementById("demo-form3").action = "module/mod_br_spdbpjs/aksi_spdbpjs.php?module="+module+"&idmenu="+idmenu+"&act=hapus&kethapus="+txt+"&hapusnix="+nhapus+"&nmodul="+nmodul+"&nidinput="+iidinput;
                document.getElementById("demo-form3").submit();
                return 1;
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }

    }
    
    
    function ProsesDataSimpan(nperiode, nnodivisi, nurl){
        var inodiv = document.getElementById(nnodivisi).value;
        var ijmlrpminta = document.getElementById('e_txtrpminta').value;
        
        if (ijmlrpminta=="") {
            alert("Jumlah Minta Kosong....");
            return false;
        }
        
        if (ijmlrpminta=="0") {
            alert("Jumlah Minta Masih Nol....");
            return false;
        }
        
        var myurl = document.getElementById(nurl).value;
        if (nperiode=="") {
            alert("Periode kosong...");
            return false;
        }
        if (inodiv=="") {
            alert("No Divisi / No BR kosong...");
            return false;
        }
        ok_ = 1;
        if (ok_) {
            var r = confirm('Apakah akan melakukan proses simpan...?\n\
Dengan Permintaan Dana Rp. '+ijmlrpminta+'\n\
dan No Divisi '+inodiv+'. ');
            if (r==true) {

                var txt="";

                $.ajax({
                    type:"post",
                    url:"module/mod_br_spdbpjs/simpanpdnpjs.php?module=simpandanabpjs&act=input",
                    data:"uperiode="+nperiode+"&unodiv="+inodiv+"&ujmlrpminta="+ijmlrpminta,
                    success:function(data){
                        alert(data);
                        window.location.href = myurl;
                        //location.reload();
                        //$('.butbacnk').trigger('click');
                        //$("#butbacnk").click();
                    }
                });
                
                
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }

    }
</script>




<?PHP
if ($skey=="1") {
    ?>
        <script type="text/javascript">
            //var mURL = window.location.pathname;
            //var URL = window.location.href;
            //var myurrl = URL.replace("skey=1", "skey=2");
            //window.location.href = myurrl;
        </script>
    <?PHP
}
?>