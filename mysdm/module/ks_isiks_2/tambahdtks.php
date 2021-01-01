<?php
include "config/koneksimysqli_it.php";

$pidmodule=$_GET['module'];
$pidmenu=$_GET['idmenu'];
$pidact=$_GET['act'];
$pstsmobile=$_SESSION['MOBILE'];
$piduser=$_SESSION['USERID']; 
$pidcard=$_SESSION['IDCARD']; 

$hari_ini = date("Y-m-d");
$pbulanpilih = date('F Y', strtotime($hari_ini));


$pidinput="";
$pkaryawanid="";
$ppilihdoktid="";
$ppilihdoktnm="";
$ppilaptid="";
$ppilihcn="";

$ptotalsemua=0;

$pfeldbln="cbln01";

$act="input";
if ($pidact=="editdata"){
    $act="update";
    $pfeldbln="cbln01x";
    
    $pidinput=$_GET['id'];
    $ppilihdoktid=$_GET['idk'];
    $pkaryawanid=$_GET['ikr'];
    $ppilihbln=$_GET['ibl'];
    $ppilaptid=$_GET['ap'];
    
    if (!empty($ppilihbln)) $pbulanpilih = date('F Y', strtotime($ppilihbln."-01"));
    
    $sql = "select a.srid as srid, a.bulan as bulan, "
            . " a.dokterid as dokterid, b.nama as nama_dokter, "
            . " a.aptid as aptid, d.nama as nama_apt, a.apttype as apttype, "
            . " a.iprodid as iprodid, c.nama as nama_produk, "
            . " a.qty as qty, a.hna as hna, ifnull(a.qty,0)*ifnull(a.hna,0) as tvalue, a.cn_ks1 as cn_ks1, a.approved as approved ";
    $sql.=" FROM hrd.ks1 as a JOIN hrd.dokter as b on a.dokterid=b.dokterId "
            . " JOIN MKT.iproduk as c on a.iprodid=c.iprodid "
            . " LEFT JOIN hrd.mr_apt as d on a.aptid=d.aptId AND a.srid=d.srid ";
    $sql.=" WHERE a.dokterid='$ppilihdoktid' AND a.srid='$pkaryawanid' AND a.bulan='$ppilihbln' ";
    //echo $sql."<br/>";
    $edit = mysqli_query($cnit, $sql);
    $r    = mysqli_fetch_array($edit);
    
    $pkaryawanid=$r['srid'];
    $ppilihdoktnm=$r['nama_dokter'];
    $ppilihcn=$r['cn_ks1'];
    
    
    $query = "select sum(ifnull(a.qty,0)*ifnull(a.hna,0)) as tvalue "
            . " FROM hrd.ks1 as a WHERE a.dokterid='$ppilihdoktid' AND a.srid='$pkaryawanid' AND a.bulan='$ppilihbln'";
    $edit2 = mysqli_query($cnit, $query);
    $r2    = mysqli_fetch_array($edit2);
    
    $ptotalsemua=$r2['tvalue'];
    $ptotalsemua2=$r2['tvalue'];
    
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
                      id='form_data01' name='form1' data-parsley-validate class='form-horizontal form-label-left'  enctype='multipart/form-data'>
                    
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
                                        <input type='text' id='e_id' name='e_id' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidinput; ?>' Readonly>
                                        <input type='hidden' id='e_sudahid' name='e_sudahid' class='form-control col-md-7 col-xs-12' value='' Readonly>
                                        <input type='hidden' id='e_apt2' name='e_apt2' class='form-control col-md-7 col-xs-12' value='<?PHP echo $ppilaptid; ?>' Readonly>
                                        <input type='hidden' id='e_idinputuser' name='e_idinputuser' class='form-control col-md-7 col-xs-12' value='<?PHP echo $piduser; ?>' Readonly>
                                        <input type='hidden' id='e_idcarduser' name='e_idcarduser' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidcard; ?>' Readonly>
                                    </div>
                                </div>
                                
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Bulan </label>
                                    <input type="hidden" class="form-control" id='e_bulan2' name='e_bulan2' required='required' placeholder='MMMM yyyy' value='<?PHP echo $pbulanpilih; ?>' Readonly>
                                    <div class='col-md-4'>
                                        <div class='input-group date' id='<?PHP echo $pfeldbln; ?>'>
                                            <input type="text" class="form-control" id='e_bulan' name='e_bulan' required='required' placeholder='MMMM yyyy' value='<?PHP echo $pbulanpilih; ?>' Readonly>
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
                                                    $query = "select karyawanId, nama From hrd.karyawan
                                                        WHERE 1=1 ";
                                                    if ($pidact=="editdata"){
                                                        $query .= " AND karyawanid ='$pkaryawanid'";
                                                    }else{
                                                        if (!empty($pfilterkaryawan)) {
                                                            $query .= " AND karyawanid IN $pfilterkaryawan ";
                                                        }else{
															/*
                                                            $query .= " AND (IFNULL(tglkeluar,'0000-00-00')='0000-00-00' OR IFNULL(tglkeluar,'')='') ";
                                                            $query .=" AND LEFT(nama,4) NOT IN ('NN -', 'DR -', 'DM -', 'BDG ', 'OTH.', 'TO. ', 'BGD-', 'JKT ', 'MR -', 'MR S')  "
                                                                    . " and LEFT(nama,7) NOT IN ('NN DM - ', 'MR SBY1')  "
                                                                    . " and LEFT(nama,3) NOT IN ('TO.', 'TO-', 'DR ', 'DR-', 'JKT', 'NN-', 'TO ') "
                                                                    . " AND LEFT(nama,5) NOT IN ('OTH -', 'NN AM', 'NN DR', 'TO - ', 'SBY -', 'RS. P') "
                                                                    . " AND LEFT(nama,6) NOT IN ('SBYTO-', 'MR SBY') ";
															*/
                                                            $query .= " AND nama NOT IN ('ACCOUNTING')";
                                                            $query .= " AND karyawanid NOT IN ('0000002200', '0000002083')";
                                                        }
                                                    }
                                                    $query .= " ORDER BY nama";
                                                    $tampil = mysqli_query($cnmy, $query);
                                                    while ($z= mysqli_fetch_array($tampil)) {
                                                        $pkaryid=$z['karyawanId'];
                                                        $pkarynm=$z['nama'];
                                                        $pkryid=(INT)$pkaryid;
                                                        if ($z['karyawanId']==$pkaryawanid)
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
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Apotik <span class='required'></span></label>
                                    <div class='col-xs-4'>
                                          <select class='form-control input-sm' id='cb_apotik' name='cb_apotik' onchange="ShowDataPilihApotik()" data-live-search="true">
                                              <?PHP 
                                                  echo "<option value='' selected>--Pilih--</option>";
                                                  if (!empty($pkaryawanid)) {
                                                        $query = "select aptid as aptid, nama as nama, apttype as apttype from hrd.mr_apt where srid='$pkaryawanid' ";
                                                        if ($pidact=="editdata") {
                                                            $query .= " and aptid='$ppilaptid' ";
                                                        }else{
                                                            $query .= " and IFNULL(aktif,'')<>'N' ";
                                                        }
                                                        $query .= " order by nama ";
                                                        $result = mysqli_query($cnit, $query);
                                                        $record = mysqli_num_rows($result);

                                                        if ((DOUBLE)$record<=0) echo "<option value='' selected>--Pilih--</option>";

                                                        for ($i=0;$i < $record;$i++) {
                                                            $row = mysqli_fetch_array($result);

                                                            $aptid  = $row['aptid'];
                                                            $nama = $row['nama'];
                                                            if ($nama<>"") {
                                                                if ($aptid==$ppilaptid)
                                                                    echo "<option value=\"$aptid\" selected>$nama - $aptid</option>";
                                                                else
                                                                    echo "<option value=\"$aptid\">$nama - $aptid</option>";
                                                            }
                                                        }
                                                  }
                                              ?>
                                          </select>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>CN <span class='required'></span></label>
                                    <div class='col-xs-4'>
                                        <input type='text' class='form-control inputmaskrp2' id='e_cn' name='e_cn' value='<?PHP echo $ppilihcn; ?>' Readonly>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
                                        Total 
                                    </label>
                                    <div class='col-md-4'>
                                        <input type='text' id='e_total' name='e_total' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $ptotalsemua; ?>' Readonly>
                                    </div>
                                </div>
                                
                                
                            </div>
                            
                            
                            
                            
                            
                        </div>
                    </div>
                
                    
                    <div id="div_detail">

                        <style>
                            .form-group, .input-group, .control-label {
                                margin-bottom:2px;
                            }
                            .control-label {
                                font-size:11px;
                            }
                            #datatable input[type=text], #tabelnobr input[type=text] {
                                box-sizing: border-box;
                                color:#000;
                                font-size:11px;
                                height: 25px;
                            }
                            select.soflow {
                                font-size:12px;
                                height: 30px;
                            }
                            .disabledDiv {
                                pointer-events: none;
                                opacity: 0.4;
                            }

                            table.datatable, table.tabelnobr {
                                color: #000;
                                font-family: Helvetica, Arial, sans-serif;
                                width: 100%;
                                border-collapse:
                                collapse; border-spacing: 0;
                                font-size: 11px;
                                border: 0px solid #000;
                            }

                            table.datatable td, table.tabelnobr td {
                                border: 1px solid #000; /* No more visible border */
                                height: 10px;
                                transition: all 0.1s;  /* Simple transition for hover effect */
                            }

                            table.datatable th, table.tabelnobr th {
                                background: #DFDFDF;  /* Darken header a bit */
                                font-weight: bold;
                            }

                            table.datatable td, table.tabelnobr td {
                                background: #FAFAFA;
                            }

                            /* Cells in even rows (2,4,6...) are one color */
                            tr:nth-child(even) td { background: #F1F1F1; }

                            /* Cells in odd rows (1,3,5...) are another (excludes header cells)  */
                            tr:nth-child(odd) td { background: #FEFEFE; }

                            tr td:hover.biasa { background: #666; color: #FFF; }
                            tr td:hover.left { background: #ccccff; color: #000; }

                            tr td.center1, td.center2 { text-align: center; }

                            tr td:hover.center1 { background: #666; color: #FFF; text-align: center; }
                            tr td:hover.center2 { background: #ccccff; color: #000; text-align: center; }
                            /* Hover cell effect! */
                            tr td {
                                padding: -10px;
                            }
                            .divnone {
                                display: none;
                            }
                        </style>

                        <script>
                            
                            $(document).ready(function() {
                                var dataTable = $('#datatable').DataTable( {
                                    "ordering": false,
                                    bFilter: false, bInfo: false, "bLengthChange": false, "bLengthChange": false,
                                    "bPaginate": false
                                } );
                            });
                            
                            function KosongkanDataProduk() {
                                var newchar = '';
                                var ettl =document.getElementById('e_total').value;
                                var inolkan="0";
                                var ibolehsave=true;
                                
                                if (ettl=="") ettl="0";
                                ettl = ettl.split(',').join(newchar);
                                
                                
                                var esdhid =document.getElementById('e_sudahid').value;
                                var myurl = window.location;
                                var urlku = new URL(myurl);
                                var module = urlku.searchParams.get("module");
                                var idmenu = urlku.searchParams.get("idmenu");
                                var act = urlku.searchParams.get("act");

                                if (act=="editdata" && esdhid=="") {

                                }else{
                                
                                    if (parseFloat(ettl)>parseFloat(inolkan)) {

                                        var cmt = confirm('Sudah ada inputan produk.\n\
Apakah akan dikosongkan datanya...?\n\
Jika sudah dikosongkan klik lagi tampilkan data produk');
                                        if (cmt == false) {
                                            document.getElementById('e_bulan').value=document.getElementById('e_bulan2').value;
                                            //ShowDataApotik();
                                            ibolehsave=false;
                                            return false;
                                        }   

                                    }
                                }
                                
                                if (ibolehsave==true) {
                                    if (act=="editdata" && esdhid=="") {
                                        document.getElementById('e_sudahid').value="1";
                                    }else{
                                        document.getElementById('e_total').value="0";
                                        document.getElementById('e_total2').value="0";
                                    }
                                    $("#c-dataproduk").html("");
                                }
                            }
                            
                            
                            
                            function TampilkanDataProduk() {
                                var iaptid = document.getElementById('cb_apotik').value;
                                if (iaptid=="") {
                                    alert("apotik masih kosong...");
                                    return false;
                                }
                                
                                ShowDataCN();
                                
                                var eid=document.getElementById('e_id').value;
                                var eidkry =document.getElementById('cb_karyawan').value;
                                var eiddr =document.getElementById('e_iddokt').value;
                                var ebln =document.getElementById('e_bulan').value;
                                var eaptid =document.getElementById('cb_apotik').value;
                                
                                
                                
                                
                                var myurl = window.location;
                                var urlku = new URL(myurl);
                                var module = urlku.searchParams.get("module");
                                var act = urlku.searchParams.get("act");
                                var idmenu = urlku.searchParams.get("idmenu");
                                
                                $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                                $.ajax({
                                    type:"post",
                                    url:"module/ks_isiks/inputdetailksisi.php?module=tmapilkanproduk"+"&act="+act,
                                    data:"uid="+eid+"&uidkry="+eidkry+"&uiddr="+eiddr+"&ubln="+ebln+"&uaptid="+eaptid,
                                    success:function(data){
                                        $("#c-dataproduk").html(data);
                                        $("#loading").html("");
                                        HitungTotalJumlahRp();
                                    }
                                });
                            }
                        </script>
                        
                        <button type='button' class='btn btn-info btn-xs' onclick='TampilkanDataProduk()'>Tampilkan Data Produk</button> <span class='required'></span>
                        
                        <div id='loading'></div>
                        <div id='c-dataproduk'>
                        
                        </div>
                        <?PHP //include "module/mod_br_aks_isiks/inputdetailksisi.php"; ?>   
                        
                        

                    </div>
                    
                    
                    <div class='x_panel'>
                        <div class='x_content'>
                            
                            
                            
                            <div class='col-md-12 col-sm-12 col-xs-12'>
                            
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-12 col-sm-12 col-xs-12' for=''>
                                        &nbsp;
                                    </label>
                                    <div class='col-md-12'>
                                        *) Mohon sebelum Inputan KS disave, dicek kembali dan dipastikan <span style="background-color:yellow;">TIDAK ADA KESALAHAN</span>.
                                        <br/>karena setelah disave, data <span style="background-color:yellow;">TIDAK DAPAT DIREVISI (TAMBAH / EDIT / HAPUS)</span>.<br/>
                                        **) Untuk kolom karyawan, dokter, dan apotik harus diisi lengkap agar dapat disave.
                                    </div>
                                </div>
                            
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
                                        Total 
                                    </label>
                                    <div class='col-md-4'>
                                        <input type='text' id='e_total2' name='e_total2' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $ptotalsemua; ?>' Readonly>
                                    </div>
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
            TampilkanDataProduk();
        }
        
    } );
    
    
    function ShowDataKry() {
        document.getElementById('e_apt2').value="";
        HapudDataDokter();
        ShowDataApotik();
        ShowDataCN();
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
        ShowDataApotik();
        ShowDataCN();
    }
    
    function HapudDataDokter(){
        document.getElementById('e_iddokt').value="";
        document.getElementById('e_nmdokt').value="";
    }
    
    function ShowDataApotik(){
        var eidkry =document.getElementById('cb_karyawan').value;
        var eiddr =document.getElementById('e_iddokt').value;
        var eidapt2 =document.getElementById('e_apt2').value;
        
        $.ajax({
            type:"post",
            url:"module/ks_isiks/viewdataksisi.php?module=viewdataaptdr",
            data:"uiddr="+eiddr+"&uidkry="+eidkry+"&uidapt2="+eidapt2,
            success:function(data){
                $("#cb_apotik").html(data);
                var eidapt =document.getElementById('cb_apotik').value;
                document.getElementById('e_apt2').value=eidapt;
            }
        });
    }
    
    function ShowDataPilihApotik(){
        var eidapt =document.getElementById('cb_apotik').value;
        document.getElementById('e_apt2').value=eidapt;
        //KosongkanDataProduk();
        $("#c-dataproduk").html("");
    }
    
    
    function ShowDataCN(){
        var eidkry =document.getElementById('cb_karyawan').value;
        var eiddr =document.getElementById('e_iddokt').value;
        var ebln =document.getElementById('e_bulan').value;
        
        $.ajax({
            type:"post",
            url:"module/ks_isiks/viewdataksisi.php?module=viewdatacndr",
            data:"uiddr="+eiddr+"&uidkry="+eidkry+"&ubln="+ebln,
            success:function(data){
                document.getElementById('e_cn').value=data;
            }
        });
    }
    
    
    $('#cbln01').on('change dp.change', function(e){
        ShowDataCN();
        KosongkanDataProduk();
        document.getElementById('e_bulan2').value=document.getElementById('e_bulan').value;
    });
    
    
    function disp_confirm(pText_,ket)  {
        //ShowDataAtasan();
        //ShowDataJumlah();

        var iid = document.getElementById('e_id').value;
        var ikry = document.getElementById('cb_karyawan').value;
        var idoktid = document.getElementById('e_iddokt').value;
        var iaptid = document.getElementById('cb_apotik').value;
        var ettl = document.getElementById('e_total').value;
        var ebln =document.getElementById('e_bulan').value;
        
        var newchar = '';
        if (ettl=="") ettl="0";
        ettl = ettl.split(',').join(newchar);
                                
        if (ikry=="") {
            alert("karyawan masih kosong...");
            return false;
        }

        if (idoktid=="") {
            alert("Dokter masih kosong...");
            return false;
        }
        
        if (iaptid=="") {
            alert("apotik masih kosong...");
            return false;
        }

        if (ettl=="0") {
            alert("total masih kosong...");
            return false;
        }


            $.ajax({
                type:"post",
                url:"module/ks_isiks/viewdataksisi.php?module=cekdatasudahada",
                data:"ukry="+ikry+"&udoktid="+idoktid+"&uaptid="+iaptid+"&ubln="+ebln,
                success:function(data){
                    //var tjml = data.length;
                    //alert(data);
                    //return false;
                    
                    if (data=="boleh") {


                        ok_ = 1;
                        if (ok_) {
                            var r=confirm(pText_)
                            if (r==true) {
                                var myurl = window.location;
                                var urlku = new URL(myurl);
                                var module = urlku.searchParams.get("module");
                                var idmenu = urlku.searchParams.get("idmenu");
                                //document.write("You pressed OK!")
                                document.getElementById("form_data01").action = "module/ks_isiks/aksi_isiks.php?module="+module+"&act="+ket+"&idmenu="+idmenu;
                                document.getElementById("form_data01").submit();
                                return 1;
                            }
                        } else {
                            //document.write("You pressed Cancel!")
                            return 0;
                        }
        
                    }else{
                        alert(data);
                    }
                    
                }
            });


    }


</script>