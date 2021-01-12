<?php
$pidmodule=$_GET['module'];
$pidmenu=$_GET['idmenu'];
$pidact=$_GET['act'];

$hari_ini = date("Y-m-d");
$pblnberlau = date('F Y', strtotime($hari_ini));


$pstsmobile=$_SESSION['MOBILE'];
$piduser=$_SESSION['USERID']; 
$pidcard=$_SESSION['IDCARD']; 
$pidjbt=$_SESSION['JABATANID']; 
$pidgrpuser=$_SESSION['GROUP']; 

$pidinput="";
$pkaryawanid="";
$ppilihdoktid="";
$ppilihdoktnm="";

$pjumlahki="";
$pmintacn="";
$pmintaroi="";

$pperiode1="";
$pperiode2="";
$pallperiode="";

$ptotalks_sis="";
$proi_sis="";
        
$act="input";
if ($pact=="editdata"){
    $act="update";
    
    $pidbr=$_GET['id'];
    
    $query = "SELECT * FROM hrd.t_estimasi_ki WHERE noid='$pidbr'";
    $tampil= mysqli_query($cnmy, $query);
    $row= mysqli_fetch_array($tampil);
    
    $pkaryawanid=$row['srid'];
    $ppilihdoktid=$row['dokterid'];
    $pjumlahki=$row['jumlah'];
    $pmintacn=$row['est_perbln'];
    $pmintaroi=$row['est_roi'];
    
    $pperiode1=$row['periode1'];
    $pperiode2=$row['periode2'];
    $pallperiode=$row['periode_ket'];
    $ptotalks_sis=$row['cn'];
    $proi_sis=$row['roi'];

    $query = "select nama as nama from hrd.dokter WHERE dokterid='$ppilihdoktid'";
    $tampil2= mysqli_query($cnmy, $query);
    $row2= mysqli_fetch_array($tampil2);
    $ppilihdoktnm=$row2['nama'];
}


?>


<!-- Modal -->
<div class='modal fade' id='myModal' role='dialog'></div>

<script> window.onload = function() { document.getElementById("e_id").focus(); } </script>

<div class="">
    
    
    <!--row-->
    <div class="row">
        
        <div class='col-md-12 col-sm-12 col-xs-12'>
            
            <div class='x_panel'>
                
                <form method='POST' action='<?PHP echo "$aksi?module=$pidmodule&act=input&idmenu=$pidmenu"; ?>' 
                      id='form_data01' name='form1' data-parsley-validate class='form-horizontal form-label-left'  enctype='multipart/form-data' >

                    <div class='col-md-12 col-sm-12 col-xs-12'>
                        <h2>
                            <a class='btn btn-default' href="<?PHP echo "?module=$pidmodule&idmenu=$pidmenu&act=$pidmenu"; ?>">Back</a>
                        </h2>
                        <div class='clearfix'></div>
                    </div>
                    
                    
                    <div class='x_panel'>
                        <div class='x_content'>
                            
                            <div class='col-md-12 col-sm-12 col-xs-12'>
                                
                                
                                <div hidden class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>ID <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input type='text' id='e_id' name='e_id' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidbr; ?>' Readonly>
                                        <input type='hidden' id='e_idinputuser' name='e_idinputuser' class='form-control col-md-7 col-xs-12' value='<?PHP echo $piduser; ?>' Readonly>
                                        <input type='hidden' id='e_idcarduser' name='e_idcarduser' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidcard; ?>' Readonly>
                                        <input type='hidden' id='e_idgrpuser' name='e_idgrpuser' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidgrpuser; ?>' Readonly>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Bulan </label>
                                    <div class='col-md-3'>
                                        <div class='input-group date' id='cbln01'>
                                            <input type="text" class="form-control" id='e_bulan' name='e_bulan' autocomplete='off' required='required' placeholder='d F Y' value='<?PHP echo $pblnberlau; ?>' Readonly>
                                            <span class='input-group-addon'>
                                                <span class='glyphicon glyphicon-calendar'></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Karyawan <span class='required'></span></label>
                                    <div class='col-xs-4'>
                                          <select class='form-control input-sm' id='cb_karyawan' name='cb_karyawan' onchange="ShowDataKry()" data-live-search="true">
                                              
                                              <?PHP 
                                                    echo "<option value='' selected>--Pilihan--</option>";
                                                    if ($pidjbt=="38" OR $pidjbt=="33") {
                                                        
                                                        $query = "select DISTINCT a.karyawanid as karyawanid, a.nama as nama  
                                                            from hrd.karyawan as a 
                                                            left join MKT.iarea as b ON a.areaid=b.areaid and a.icabangid=b.icabangid 
                                                            where (a.jabatanid='15') and (a.tglkeluar='0000-00-00' OR a.aktif='Y') 
                                                            and (a.divisiid<>'OTC')
                                                            and a.icabangid in (select IFNULL(icabangid,'') from hrd.rsm_auth where karyawanid='$pidcard') ";
                                                        
                                                    }else{
                                                        
                                                        $query = "select karyawanId as karyawanid, nama as nama From hrd.karyawan as a
                                                            WHERE 1=1 ";
                                                        if ($pidact=="editdata"){
                                                            $query .= " AND a.karyawanid ='$pkaryawanid'";
                                                        }else{
                                                            /*
                                                            $query .= " AND (IFNULL(tglkeluar,'0000-00-00')='0000-00-00' OR IFNULL(tglkeluar,'')='') ";
                                                            $query .=" AND LEFT(nama,4) NOT IN ('NN -', 'DR -', 'DM -', 'BDG ', 'OTH.', 'TO. ', 'BGD-', 'JKT ', 'MR -', 'MR S')  "
                                                                    . " and LEFT(nama,7) NOT IN ('NN DM - ', 'MR SBY1')  "
                                                                    . " and LEFT(nama,3) NOT IN ('TO.', 'TO-', 'DR ', 'DR-', 'JKT', 'NN-', 'TO ') "
                                                                    . " AND LEFT(nama,5) NOT IN ('OTH -', 'NN AM', 'NN DR', 'TO - ', 'SBY -', 'RS. P') "
                                                                    . " AND LEFT(nama,6) NOT IN ('SBYTO-', 'MR SBY') ";
                                                             * 
                                                             */
                                                            $query .= " AND a.nama NOT IN ('ACCOUNTING')";
                                                            $query .= " AND a.karyawanid NOT IN ('0000002200', '0000002083')";
                                                            
                                                        }
                                                    }
                                                    $query .= " ORDER BY a.nama";
                                                    $tampil = mysqli_query($cnmy, $query);
                                                    while ($z= mysqli_fetch_array($tampil)) {
                                                        $pkaryid=$z['karyawanid'];
                                                        $pkarynm=$z['nama'];
                                                        $pkryid=(INT)$pkaryid;
                                                        if ($pkaryid==$pkaryawanid)
                                                            echo "<option value='$pkaryid' selected>$pkarynm ($pkryid)</option>";
                                                        else
                                                            echo "<option value='$pkaryid'>$pkarynm ($pkryid)</option>";
                                                    }
                                                
                                              ?>
                                          </select>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Dokter <span class='required'></span></label>
                                    <div class='col-xs-4'>
                                            <div class='input-group '>
                                                <?PHP if ($pidact=="editdata") {}else{ ?>
                                                <span class='input-group-btn'>
                                                    <button type='button' class='btn btn-primary' data-toggle='modal' data-target='#myModal' onClick="getDataDokter('e_iddokt', 'e_nmdokt')">Pilih!</button>
                                                </span>
                                                <?PHP } ?>
                                                <input type='text' class='form-control' id='e_iddokt' name='e_iddokt' value='<?PHP echo $ppilihdoktid; ?>' Readonly>
                                            </div>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''> <span class='required'></span></label>
                                    <div class='col-xs-4'>
                                        <input type='text' class='form-control' id='e_nmdokt' name='e_nmdokt' value='<?PHP echo $ppilihdoktnm; ?>' Readonly>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
                                        Permintaan KI Rp.
                                    </label>
                                    <div class='col-md-4'>
                                        <input type='text' id='e_jumlahki' name='e_jumlahki' onblur="HitungROIPermitaan()" autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjumlahki; ?>'>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
                                        &nbsp; 
                                    </label>
                                    <div class='col-md-4'>
                                        <button type='button' class='btn btn-dark' onclick='disp_dataestimasi()'>Isi Estimasi</button>
                                        
                                    </div>
                                </div>
                                <hr/>
                                <div id="loading"></div>
                                <div id="div_estimasi">
                                    <?PHP
                                    if ($pidact=="editdata"){
                                        $plihatks="<a class='btn btn-info btn-xs' href='eksekusi3.php?module=lihatdataksusr&ket=bukan&iid=$pkaryawanid&ind=$ppilihdoktid' target='_blank'>Lihat Rincian KS</a>";
                                    ?>
                                    
                                        <div hidden class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
                                                &nbsp; 
                                            </label>
                                            <div class='col-md-4'>
                                                <input type='text' id='e_periode1' name='e_periode1' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pperiode1; ?>' Readonly>
                                                <input type='text' id='e_periode2' name='e_periode2' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pperiode2; ?>' Readonly>
                                                <input type='text' id='e_periodeall' name='e_periodeall' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pallperiode; ?>' Readonly>
                                            </div>
                                        </div>


                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
                                                Estimasi Tiap Bulan Rp. 
                                            </label>
                                            <div class='col-md-4'>
                                                <input type='text' id='e_etimasiperbln' name='e_etimasiperbln' onblur="HitungROIPermitaan()" autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pmintacn; ?>'>
                                            </div>
                                        </div>

                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
                                                ROI 
                                            </label>
                                            <div class='col-md-4'>
                                                <input type='text' id='e_mintaroi' name='e_mintaroi' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pmintaroi; ?>' >
                                            </div>
                                        </div>



                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
                                                Estimasi Perkiraan Sales 6 Bulan Rp. 
                                            </label>
                                            <div class='col-md-4'>
                                                <input type='text' id='e_cn' name='e_cn' onblur="HitungROIPermitaanSistem()" autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $ptotalks_sis; ?>' Readonly>
                                            </div>
                                        </div>

                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
                                                Perkiraan ROI 
                                            </label>
                                            <div class='col-md-4'>
                                                <input type='text' id='e_roi' name='e_roi' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $proi_sis; ?>' Readonly>
                                            </div>
                                        </div>


                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
                                                &nbsp; 
                                            </label>
                                            <div class='col-md-8'>
                                                <?PHP echo $plihatks; ?> 
                                            </div>
                                        </div>


                                        <hr/>

                                        <script>
                                            function HitungROIPermitaan() {
                                                var ajmlki=document.getElementById('e_jumlahki').value;
                                                var ajmlcn=document.getElementById('e_etimasiperbln').value;
                                                var newchar = '';

                                                if (ajmlki=="") ajmlki="0";
                                                ajmlki = ajmlki.split(',').join(newchar);

                                                if (ajmlcn=="") ajmlcn="0";
                                                ajmlcn = ajmlcn.split(',').join(newchar);

                                                var nTotal_="0";
                                                if (ajmlcn!="0") {
                                                    nTotal_ =(parseFloat(ajmlki)/parseFloat(ajmlcn)).toFixed(2);
                                                }

                                                //document.getElementById('e_mintaroi').value=nTotal_;
                                                HitungROIPermitaanSistem();

                                            }

                                            function HitungROIPermitaanSistem() {
                                                var ajmlki=document.getElementById('e_jumlahki').value;
                                                var ajmlcn=document.getElementById('e_cn').value;
                                                var newchar = '';

                                                if (ajmlki=="") ajmlki="0";
                                                ajmlki = ajmlki.split(',').join(newchar);

                                                if (ajmlcn=="") ajmlcn="0";
                                                ajmlcn = ajmlcn.split(',').join(newchar);

                                                var nTotal_="0";
                                                if (ajmlcn!="0") {
                                                    nTotal_ =(parseFloat(ajmlki)/parseFloat(ajmlcn)).toFixed(2);
                                                }

                                                document.getElementById('e_roi').value=nTotal_;

                                            }
                                        </script>
    
                                    <?PHP
                                    }
                                    ?>
                                    
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <button type='button' class='btn btn-success' onclick='disp_confirm("Simpan ?", "<?PHP echo $act; ?>")'>Save</button>
                                    </div>
                                </div>
                                
                                
                            </div>
                            
                            
                        </div>
                    </div>
                    
                </form>
                
            </div>
    
        </div>
        
    </div>
</div>


<link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />
<link href="css/stylenew.css" rel="stylesheet" type="text/css" />

<script>
    
    $(document).ready(function() {
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var idmenu = urlku.searchParams.get("idmenu");
        var act = urlku.searchParams.get("act");
        
        if (act=="editdata") {
            //TampilkanDataProduk();
        }
        
    } );
    
    
    function ShowDataKry() {
        HapusDataDokter();
    }
    
    function getDataDokter(data1, data2){
        var eidkry =document.getElementById('cb_karyawan').value;
        
        $.ajax({
            type:"post",
            url:"module/ks_isiks/viewdata_ksdr.php?module=viewdatadokter",
            data:"udata1="+data1+"&udata2="+data2+"&uidkry="+eidkry,
            success:function(data){
                $("#myModal").html(data);
                document.getElementById(data1).value="";
                document.getElementById(data2).value="";
            }
        });
    }
    
    function getDataModalDokter(fildnya1, fildnya2, d1, d2){
        document.getElementById(fildnya1).value=d1;
        document.getElementById(fildnya2).value=d2;
    }
    
    function HapusDataDokter(){
        document.getElementById('e_iddokt').value="";
        document.getElementById('e_nmdokt').value="";
    }
    
    
    function disp_dataestimasi() {
        var eid = document.getElementById('e_id').value;
        var eidkry = document.getElementById('cb_karyawan').value;
        var eiddr = document.getElementById('e_iddokt').value;
        var ejmlki = document.getElementById('e_jumlahki').value;
        
        if (eidkry=="") {
            alert("karyawan masih kosong...");
            return false;
        }
        
        if (eiddr=="") {
            alert("dokter masih kosong...");
            return false;
        }
        
        if (ejmlki=="" || ejmlki=="0") {
            alert("Jumlah permintaan KI masih kosong...");
            return false;
        }


        $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
        $.ajax({
            type:"post",
            url:"module/ks_isiestimasiki/isidataestimasi.php?module=tmapilkanestimasi",
            data:"uidkry="+eidkry+"&uiddr="+eiddr+"&ujmlki="+ejmlki+"&uid="+eid,
            success:function(data){
                $("#div_estimasi").html(data);
                $("#loading").html("");
            }
        });
    }
    
    
    function disp_confirm(pText_,ket)  {
        var eidkry = document.getElementById('cb_karyawan').value;
        var eiddr = document.getElementById('e_iddokt').value;
        var ejmlki = document.getElementById('e_jumlahki').value;
        var ejmlestbln = document.getElementById('e_etimasiperbln').value;
        var eroiminta = document.getElementById('e_mintaroi').value;
        
        if (eidkry=="") {
            alert("karyawan masih kosong...");
            return false;
        }
        
        if (eiddr=="") {
            alert("dokter masih kosong...");
            return false;
        }
        
        if (ejmlki=="" || ejmlki=="0") {
            alert("Jumlah permintaan KI masih kosong...");
            return false;
        }
        
        if (ejmlestbln=="" || ejmlestbln=="0") {
            alert("Jumlah Estimasi Tiap Bulan masih kosong...");
            return false;
        }
        
        if (eroiminta=="" || eroiminta=="0") {
            alert("Estimasi ROI masih kosong...");
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
                document.getElementById("form_data01").action = "module/ks_isiestimasiki/aksi_isiestimasiki.php?module="+module+"&act="+ket+"&idmenu="+idmenu;
                document.getElementById("form_data01").submit();
                return 1;
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }
        
        
        
    }
</script>