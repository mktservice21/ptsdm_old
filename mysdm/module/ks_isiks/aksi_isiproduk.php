<?PHP
session_start();

include("config/koneksimysqli.php");
include("config/fungsi_sql.php");
$cnit=$cnmy;

include "config/cek_akses_modul.php";
    

$pidmodule=$_GET['module'];
$pidmenu=$_GET['idmenu'];
$pidact="tambahbaru";
$act="input";

$aksi="module/ks_isiks/laporanbrbulan.php";




$pidinput=$_POST['e_iddokt'];
$piduser=$_POST['e_idinputuser'];
$pidcard=$_POST['e_idcarduser'];
$pidgrpuser=$_POST['e_idgrpuser'];


$ppilihdoktid=$_POST['e_iddokt'];
$pkaryawanid=$_POST['cb_karyawan'];
$ppilihbln=$_POST['e_bulan'];
$pbulanpilih=$_POST['e_bulan'];
$pthn = date('Y', strtotime($ppilihbln));

$papotikid  = $_POST['cb_apotik'];
$query = "select aptid as aptid, nama as nama, apttype as apttype from hrd.mr_apt WHERE idapotik='$papotikid'";
$tampila=mysqli_query($cnmy, $query);
$arow= mysqli_fetch_array($tampila);
$ppilaptid=$arow['aptid'];
$ppilaptnm=$arow['nama'];
$ppilaptqtyp=$arow['apttype'];
$pnamatypeapt="R";
if ($ppilaptqtyp=="1") $pnamatypeapt="D";

$ppilihcn=$_POST['e_cn'];
$ptotalsemua=0;
    
    
$pfeldbln="cbln01x";


$pkaryawannm=getfield("select nama as lcfields from hrd.karyawan where karyawanid='$pkaryawanid'");
$ppilihdoktnm=getfield("select nama as lcfields from hrd.dokter where dokterid='$ppilihdoktid'");
//$ppilaptnm=getfield("select nama as lcfields from hrd.mr_apt where srid='$pkaryawanid' AND aptid='$ppilaptid'");

//echo "$ppilihdoktid, $pkaryawanid, $ppilihbln, $ppilaptid, $ppilihcn";



    //jika ks samasekali belum ada, maka tidak bisa input.
    $query  = "select distinct dokterid FROM hrd.ks1 WHERE srid='$pkaryawanid' AND dokterid='$ppilihdoktid'";
    $tampil = mysqli_query($cnit, $query);
    $ketemu = mysqli_num_rows($tampil);
    if ((INT)$ketemu<=0) {
        
        //boleh input jika ada data exspsion
        $query = "select distinct dokterid FROM hrd.ks1_buka WHERE srid='$pkaryawanid' AND dokterid='$ppilihdoktid' AND ifnull(aktif,'')<>'N'";
        $tampilb = mysqli_query($cnit, $query);
        $ketemub = mysqli_num_rows($tampilb);
        if ((INT)$ketemub>0) {
        }else{
            
            if ($pthn=="2020") {
                
            }else{
                mysqli_close($cnit);
                $bolehinput="KS samasekali belum ada, silakan info ke MS untuk input...";
                echo $bolehinput;
                exit;
            }
            
        }
    }
    
    
    
?>
<HTML>
<head>
  <title>Kartu Status Isi Produk</title>
    <meta http-equiv="Expires" content="Mon, 01 Jan 2030 1:00:00 GMT">
    <meta http-equiv="Pragma" content="no-cache">
    <?php header("Cache-Control: no-cache, must-revalidate"); ?>
    <link rel="shortcut icon" href="images/icon.ico" />
    <style> .str{ mso-number-format:\@; } </style>
    <link href="vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />
    <link href="css/stylenew.css" rel="stylesheet" type="text/css" />
    
    
    <script src="vendors/jquery/dist/jquery.min.js"></script>
    
    
    <!-- bootstrap-datetimepicker -->
    <link href="vendors/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css" rel="stylesheet">

    <script src="js/hanyaangka.js"></script>
    <!-- jQuery -->
    <script src="vendors/jquery/dist/jquery.min.js"></script>
    <!--input mask -->
    <script src="js/inputmask.js"></script>
    
        
</head>


<BODY class="nav-md">
    

<!-- Modal -->
<div class='modal fade' id='myModal' role='dialog'></div>
<button onclick="topFunction()" id="myBtn" title="Go to top">Top</button>


<script> window.onload = function() { document.getElementById("e_bulan").focus(); } </script>

<!-- mulai div -->
<div class="container body">
<div class="main_container">


<div class="">
    
<!-- Judul -->
<div class="page-title">
    <div class="title_left">
        <h3>
            <?PHP
            $judul="Kartu Status";
            if ($pidact=="tambahbaru")
                echo "Input $judul";
            elseif ($pidact=="editdata")
                echo "Edit $judul";
            else
                echo "Data $judul";
            ?>
        </h3>
    </div>
</div>
<div class="clearfix"></div>
<!-- END judul -->

<!--row-->
<div class="row">

<!-- isinya -->
<div class="">

    <!--row-->
    <div class="row">
        
        <div class='col-md-12 col-sm-12 col-xs-12'>
            
            <div class='x_panel'>
                
                

                <form method='POST' action='<?PHP echo "$aksi?module=$pidmodule&act=input&idmenu=$pidmenu"; ?>' 
                      id='form_data01' name='form1' data-parsley-validate class='form-horizontal form-label-left'  enctype='multipart/form-data'>
                
                    
                    <div class='x_panel'>
                        <div class='x_content'>
                            
                            
                            <div class='col-md-12 col-sm-12 col-xs-12'>
                                
                            
                                <div hidden class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>ID <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input type='text' id='e_id' name='e_id' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidinput; ?>' Readonly>
                                        <input type='text' id='e_idinputuser' name='e_idinputuser' class='form-control col-md-7 col-xs-12' value='<?PHP echo $piduser; ?>' Readonly>
                                        <input type='text' id='e_idcarduser' name='e_idcarduser' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidcard; ?>' Readonly>
                                        <input type='text' id='e_idgrpuser' name='e_idgrpuser' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidgrpuser; ?>' Readonly>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Bulan </label>
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
                                        <input type='hidden' class='form-control' id='cb_karyawan' name='cb_karyawan' value='<?PHP echo $pkaryawanid; ?>' Readonly>
                                        <input type='text' class='form-control' id='nm_karyawan' name='nm_karyawan' value='<?PHP echo "$pkaryawannm ($pkaryawanid)"; ?>' Readonly>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Dokter <span class='required'></span></label>
                                    <div class='col-xs-4'>
                                        <input type='hidden' class='form-control' id='e_iddokt' name='e_iddokt' value='<?PHP echo $ppilihdoktid; ?>' Readonly>
                                        <input type='text' class='form-control' id='e_nmdokt' name='e_nmdokt' value='<?PHP echo "$ppilihdoktnm ($ppilihdoktid)"; ?>' Readonly>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Apotik <span class='required'></span></label>
                                    <div class='col-xs-4'>
                                        <input type='hidden' class='form-control' id='cb_idapotik' name='cb_idapotik' value='<?PHP echo $papotikid; ?>' Readonly>
                                        <input type='hidden' class='form-control' id='cb_apotik' name='cb_apotik' value='<?PHP echo $ppilaptid; ?>' Readonly>
                                        <input type='hidden' class='form-control' id='txt_apttyp' name='txt_apttyp' value='<?PHP echo $ppilaptqtyp; ?>' Readonly>
                                        <input type='hidden' class='form-control' id='txt_apttypnm' name='txt_apttypnm' value='<?PHP echo $pnamatypeapt; ?>' Readonly>
                                        <input type='text' class='form-control' id='txt_nmapotik' name='txt_nmapotik' value='<?PHP echo "$ppilaptnm ($papotikid)"; ?>' Readonly>
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
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
                                        Filter Divisi
                                    </label>
                                    <div class='col-md-4'>
                                        <select class='form-control input-sm' id='myInput' name='myInput' onchange="myFilterDataProduk()" data-live-search="true">
                                            <?PHP
                                                echo "<option value='' selected>--ALL--</option>";
                                                echo "<option value='EAGLE'>EAGLE</option>";
                                                echo "<option value='PEACO'>PEACOCK</option>";
                                                echo "<option value='PIGEO'>PIGEON</option>";
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                
                            </div>
                            <!-- -->
                            
                            
                        </div>
                    </div>
                

                    <!-- DETAIL PRODUK -->
                    
                    <div id="div_detail">
                        <?PHP
                            $pidklaim=$_POST['e_id'];
                            $pidkar=$_POST['cb_karyawan'];
                            $piddokt=$_POST['e_iddokt'];
                            $pblnpl=$_POST['e_bulan'];
                            $papotikid  = $_POST['cb_apotik'];
                            $paptid=$ppilaptid;
                            $pbln = date('Y', strtotime($pblnpl));
                            $pplbulan = date('Y-m', strtotime($pblnpl));
                            
                            
                            $puserid=$_SESSION['USERID'];
                            $now=date("mdYhis");
                            $tmp00 =" dbtemp.tmpinptksdrusr00_".$puserid."_$now ";
                            $tmp01 =" dbtemp.tmpinptksdrusr01_".$puserid."_$now ";


                            //$query ="select iprodid as iprodid, nama as nama, hna as hna, aktif from MKT.iproduk where IFNULL(aktif,'') <> 'N' order by nama";
                            $query ="select DivProdId as divisiis, iprodid as iprodid, nama as nama, hna as hna, aktif from MKT.iprodukh where insentif='Y' and IFNULL(aktif,'') <> 'N' and tahun='$pbln' order by nama";
                            $query = "create TEMPORARY table $tmp00 ($query)";
                            mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
                            
                            $query = "select a.srid as srid, a.bulan as bulan, "
                                    . " a.dokterid as dokterid, "
                                    . " a.aptid as aptid, a.idapotik, a.apttype as apttype, "
                                    . " a.iprodid as iprodid, "
                                    . " a.qty as qty, a.hna as hna, ifnull(a.qty,0)*ifnull(a.hna,0) as tvalue, a.cn_ks1 as cn_ks1, a.approved as approved "
                                    . " FROM hrd.ks1 as a WHERE a.dokterid='$piddokt' AND a.srid='$pidkar' AND a.bulan='$pplbulan' AND a.idapotik='$papotikid'";
                            $query = "create TEMPORARY table $tmp01 ($query)";
                            mysqli_query($cnit, $query);
                            $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }


                            $query = "ALTER TABLE $tmp00 ADD COLUMN qty DECIMAL(20,2), ADD COLUMN tvalue DECIMAL(20,2)";
                            mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }    

                            $query = "UPDATE $tmp00 as a JOIN $tmp01 as b on a.iprodid=b.iprodid SET a.qty=b.qty, a.hna=b.hna, a.tvalue=b.tvalue WHERE IFNULL(b.qty,0)<>0";
                            mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }    

                            $query = "DELETE FROM $tmp01 WHERE IFNULL(iprodid,'') IN (select distinct IFNULL(iprodid,'') FROM $tmp00)";
                            mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }


                            $query = "INSERT INTO $tmp00 (iprodid, nama, hna, aktif, qty, tvalue) "
                                    . " SELECT DISTINCT a.iprodid, b.nama, a.hna, b.aktif, a.qty, a.tvalue FROM $tmp01 as a JOIN MKT.iproduk as b on a.iprodid=b.iprodid";
                            mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
                        
                        ?>
                        
                        
                        <div class='tbldata'>
                            
                            <table id='datatable' class='datatable table nowrap table-striped table-bordered' width="100%">
                                <thead>
                                    <tr>
                                        <th width='2%px' class='divnone'></th>
                                        <th width='5%px'>No</th>
                                        <th width='30%' >Produk</th>
                                        <th width='5%' align="right">Qty</th>
                                        <th width='5%' align="right">Hna</th>
                                        <th width='5%' align="right">Jumlah</th>
                                        <th width='5%'>Divisi</th>
                                    </tr>
                                </thead>
                                <tbody class='inputdatauc'>
                                <?PHP
                                $no=1;
                                $query = "select * from $tmp00 order by nama";
                                $tampil=mysqli_query($cnit, $query);
                                while ($nrow= mysqli_fetch_array($tampil)){
                                    $pdivid=$nrow['divisiis'];
                                    $pkodeidbr=$nrow['iprodid'];
                                    $pnmproduk=$nrow['nama'];
                                    $phna=$nrow['hna'];
                                    $pqty=$nrow['qty'];
                                    $pjumlah=$nrow['tvalue'];

                                    $pfldqty="<input type='text' size='10px' id='e_txtqty[$pkodeidbr]' name='e_txtqty[$pkodeidbr]' onblur=\"HitungJumlahRp('e_txtqty[$pkodeidbr]', 'e_txthna[$pkodeidbr]', 'e_txtjml[$pkodeidbr]')\" class='input-sm inputmaskrp2' autocomplete='off' value='$pqty'>";
                                    $pfldhna="<input type='text' size='10px' id='e_txthna[$pkodeidbr]' name='e_txthna[$pkodeidbr]' onblur=\"HitungJumlahRp('e_txtqty[$pkodeidbr]', 'e_txthna[$pkodeidbr]', 'e_txtjml[$pkodeidbr]')\" class='input-sm inputmaskrp2' autocomplete='off' value='$phna' Readonly>";
                                    $pfldjml="<input type='text' size='10px' id='e_txtjml[$pkodeidbr]' name='e_txtjml[$pkodeidbr]' onblur='HitungTotalJumlahRp()' class='input-sm inputmaskrp2' autocomplete='off' value='$pjumlah' Readonly>";
                                    
                                    $pfldnmprod="<input type='hidden' size='10px' id='e_txtnmprod[$pkodeidbr]' name='e_txtnmprod[$pkodeidbr]' class='input-sm' autocomplete='off' value='$pnmproduk' Readonly>";



                                    $chkbox = "<input type='checkbox' id='chk_kodeid[$pkodeidbr]' name='chk_kodeid[]' value='$pkodeidbr' checked>";

                                    echo "<tr>";
                                    echo "<td nowrap class='divnone'>$chkbox</td>";
                                    echo "<td nowrap>$no</td>";
                                    echo "<td nowrap>$pnmproduk $pfldnmprod</td>";
                                    echo "<td nowrap>$pfldqty</td>";
                                    echo "<td nowrap>$pfldhna</td>";
                                    echo "<td nowrap>$pfldjml</td>";
                                    echo "<td nowrap>$pdivid</td>";
                                    echo "</tr>";

                                    $no++;
                                }
                                ?>
                                </tbody>
                            </table>
                            
                        </div>
                        
                        
                        
                        
                        
                    </div>
                    <!-- END DETAIL PRODUK -->
                
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
<!-- END class="" -->    
    


<!-- END row -->
</div>
    
    
    
    
<!-- END div class="" -->    
</div>

<!-- END main_container -->
</div>
</div>

<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;

</BODY>

<!--<script src="vendors/jquery/dist/jquery.min.js"></script>-->
<!-- Bootstrap -->
<script src="vendors/bootstrap/dist/js/bootstrap.min.js"></script>
<script type='text/javascript' src='datetime/js/jquery-ui.min.js'></script>
<!-- jquery.inputmask -->
<script src="vendors/jquery.inputmask/dist/min/jquery.inputmask.bundle.min.js"></script>
                                                
                                                
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

<script>
    // SCROLL
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
    // END SCROLL
</script>
    

<script>
    function myFilterDataProduk() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("myInput");
        filter = input.value.toUpperCase();
        table = document.getElementById("datatable");
        tr = table.getElementsByTagName("tr");
        for (i = 0; i < tr.length; i++) {
            td = tr[i].getElementsByTagName("td")[6];
            if (td) {
                txtValue = td.textContent || td.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }       
        }
    }
</script>


<script>
    
    $(document).ready(function() {
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var idmenu = urlku.searchParams.get("idmenu");
        var act = urlku.searchParams.get("act");
        
        if (act=="editdata" || act=="input") {
            HitungTotalJumlahRp();
        }
        
        var dataTable = $('#datatable').DataTable( {
            "ordering": false,
            bFilter: false, bInfo: false, "bLengthChange": false, "bLengthChange": false,
            "bPaginate": false
        } );
    });
        
        
        
    function HitungJumlahRp(iqty, ihna, ijml) {
        var aqty=document.getElementById(iqty).value;
        var ahna=document.getElementById(ihna).value;
        var newchar = '';

        if (aqty=="") aqty="0";
        aqty = aqty.split(',').join(newchar);

        if (ahna=="") ahna="0";
        ahna = ahna.split(',').join(newchar);

        var nTotal_="0";
        nTotal_ =parseFloat(aqty)*parseFloat(ahna);

        document.getElementById(ijml).value=nTotal_;

        HitungTotalJumlahRp();
    }

    function HitungTotalJumlahRp() {
        var chk_arr1 =  document.getElementsByName('chk_kodeid[]');
        var chklength1 = chk_arr1.length;
        var newchar = '';

        var nTotal_="0";
        for(k=0;k< chklength1;k++)
        {
            if (chk_arr1[k].checked == true) {

                var kata = chk_arr1[k].value;
                var fields = kata.split('-');    
                var anm_jml="e_txtjml["+fields[0]+"]";
                var ajml=document.getElementById(anm_jml).value;
                if (ajml=="") ajml="0";
                ajml = ajml.split(',').join(newchar);

                nTotal_ =parseFloat(nTotal_)+parseFloat(ajml);


            }
        }

        document.getElementById('e_total').value=nTotal_;
        document.getElementById('e_total2').value=nTotal_;
    }
        
        
        
        
    function disp_confirm(pText_,ket)  {
        //ShowDataAtasan();
        //ShowDataJumlah();

        var iid = document.getElementById('e_id').value;
        var ikry = document.getElementById('cb_karyawan').value;
        var idoktid = document.getElementById('e_iddokt').value;
        var nmoktid = document.getElementById('e_nmdokt').value;
        var iapotikid = document.getElementById('cb_idapotik').value;
        var iaptid = document.getElementById('cb_apotik').value;
        var inmapotik = document.getElementById('txt_nmapotik').value;
        var itpaotik = document.getElementById('txt_apttypnm').value;
        var ettl = document.getElementById('e_total').value;
        var etotalinput = document.getElementById('e_total').value;
        var ebln =document.getElementById('e_bulan').value;
        var ipl_cn = document.getElementById('e_cn').value;
        
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
        
        if (iapotikid=="") {
            alert("apotik masih kosong...");
            return false;
        }

        if (ipl_cn=="" || ipl_cn=="0") {
            alert("cn kosong...");
            return false;
        }
        
        if (ettl=="0") {
            alert("total masih kosong...");
            return false;
        }
        
        
        var iprodnm="";
        var chk_arr1 =  document.getElementsByName('chk_kodeid[]');
        var chklength1 = chk_arr1.length;
        var newchar = '';
        
        for(k=0;k< chklength1;k++)
        {
            if (chk_arr1[k].checked == true) {

                var kata = chk_arr1[k].value;
                var fields = kata.split('-');    
                var anm_prod="e_txtnmprod["+fields[0]+"]";
                var anmprod=document.getElementById(anm_prod).value;
                
                
                var anm_jml="e_txtqty["+fields[0]+"]";
                var ajml=document.getElementById(anm_jml).value;
                if (ajml=="") ajml="0";
                ajml = ajml.split(',').join(newchar);
                
                if (parseFloat(ajml)!=0) {
                    iprodnm=iprodnm+""+anmprod+" = "+ajml+" "+"\n\
";
                }
                
            }
        }
        var iconfirm_ = "";
        var iprodnm2 = "";
        if (iprodnm!="") {
            //iprodnm2 = iprodnm.substring (0, iprodnm.length-3);
            iprodnm2=iprodnm;
        }
        
        iconfirm_="Bulan : "+ebln+", Dokter : "+nmoktid+" \n\
Apotik : "+inmapotik+" ("+itpaotik+") \n\
Total : "+etotalinput+" \n\
"+iprodnm2+"\n\
\n\
Apakah data sudah sesuai, dan akan melakukan simpan...?\n\
\n\
Setelah disimpan, data tidak bisa diubah / hapus..";
        //alert(iconfirm_); return false;
        

            $.ajax({
                type:"post",
                url:"module/ks_isiks/viewdataksisi.php?module=cekdatasudahada",
                data:"ukry="+ikry+"&udoktid="+idoktid+"&uaptid="+iaptid+"&ubln="+ebln+"&uapotikid="+iapotikid,
                success:function(data){
                    //var tjml = data.length;
                    //alert(data);
                    //return false;
                    
                    if (data=="boleh") {


                        ok_ = 1;
                        if (ok_) {
                            var r=confirm(iconfirm_)
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



</HTML>


<?PHP
mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp00");
mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp01");
?>