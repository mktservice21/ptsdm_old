<?PHP
    include "config/cek_akses_modul.php";
    include("config/koneksimysqli_it.php");
    
    $fkaryawan=$_SESSION['IDCARD'];
    $fstsadmin=$_SESSION['STSADMIN'];
    $flvlposisi=$_SESSION['LVLPOSISI'];
    $fdivisi=$_SESSION['DIVISI'];
    
    $aksi="eksekusi3.php";
    $hari_ini = date("Y-m-d");
    $tgl_pertama = date('F Y', strtotime($hari_ini));
    
    $pmodule="";
    if (isset($_GET['module'])) $pmodule=$_GET['module'];
    
?>

<!-- Modal -->
<div class='modal fade' id='myModal' role='dialog'></div>

<div class="">

    <div class="page-title"><div class="title_left"><h3>Laporan KS Monitoring User</h3></div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">

        <?php

        ?>
        <form method='POST' action='<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]"; ?>' id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left' target="_blank">
            <div class='col-md-12 col-sm-12 col-xs-12'>
                <div class='x_panel'>

                    <div class='col-md-12 col-sm-12 col-xs-12'>
                        <h2>
                            <button type='button' class='btn btn-success' onclick="disp_confirm('')">Preview</button>
                            <?PHP
                            if ($_SESSION['MOBILE']!="Y") {
                                echo "<button type='button' class='btn btn-danger' onclick=\"disp_confirm('excel')\">Excel</button>";
                            }
                            ?>
                            <a class='btn btn-default' href="<?PHP echo "?module=home"; ?>">Home</a>
                        </h2>
                        <div class='clearfix'></div>
                    </div>
                    
                    <div class='col-md-12 col-xs-12'>
                        <div class='x_panel'>
                          
                            <button type='button' class='btn btn-warning btn-xs' onclick="RefreshDataDokterALL('')">Tampilkan Data Dokter</button><br/><br/>
                            <div id='loading2'></div>
                            <div id='c-data'>

                            </div>
                            
                        </div>
                    </div>
                    
                    
                    <!--kiri-->
                    <div hidden class='col-md-6 col-xs-12'>
                        <div class='x_panel'>
                            <div class='x_content form-horizontal form-label-left'><br />
                                
                                
                                <div hidden class='col-sm-6'>
                                    <b>Periode</b>
                                    <div class="form-group">
                                        <div class='input-group date' id='cbln01'>
                                            <input type='text' id='e_tgl1' name='e_tgl1' required='required' class='form-control' placeholder='tgl awal' value='<?PHP echo $tgl_pertama; ?>' placeholder='dd mmm yyyy' Readonly>
                                            <span class="input-group-addon">
                                               <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div hidden class='col-sm-6'>
                                    <b>s/d.</b>
                                    <div class="form-group">
                                        <div class='input-group date' id='cbln02'>
                                            <input type='text' id='e_tgl2' name='e_tgl2' required='required' class='form-control' placeholder='tgl awal' value='<?PHP echo $tgl_pertama; ?>' placeholder='dd mmm yyyy' Readonly>
                                            <span class="input-group-addon">
                                               <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div id='loading'></div>
                                <div class='form-group'>
                                    <div class='col-sm-12'>
                                        <input type="checkbox" id="chkiddokt" value="deselect" onClick="SelAllCheckBox('chkiddokt', 'chkbox_iddok[]')" checked/> &nbsp; 
                                        <button type='button' class='btn btn-warning btn-xs' onclick="RefreshDataDokter('')">Tampilkan Data Dokter</button><br/><br/>
                                        <div class="form-group">
                                            <div id="kotak-multi3" class="jarak">
                                                <?PHP
                                                /*
                                                    $query = "select distinct k.dokterid as dokterid, d.nama as nama from hrd.ks1 k "
                                                            . " join hrd.dokter d on k.dokterid=d.dokterId "
                                                            . " where left(k.bulan,4) in ('2020') order by 2";
                                                    $tampil = mysqli_query($cnit, $query);
                                                    while ($z= mysqli_fetch_array($tampil)) {
                                                        $pidoktid=$z['dokterid'];
                                                        $pnmdoktb=$z['nama'];
                                                        
                                                        echo "&nbsp; <input type=checkbox value='$pidoktid' name='chkbox_iddok[]' checked> $pnmdoktb ($pidoktid)<br/>";
                                                    }
                                                 * 
                                                 */
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                
                                
                                <div hidden class='form-group'>
                                    <div class='col-sm-12'>
                                        <b>Karyawan</b>
                                        <div class="form-group">
                                            <select class='form-control' id="cb_karyawan" name="cb_karyawan">
                                                <?PHP
                                                echo "<option value='' selected>--All--</option>";
                                                
                                                $query = "select b.karyawanId as karyawanid, b.nama as nama from hrd.karyawan as b WHERE IFNULL(b.nama,'')<>'' ";
                                                $query .=" AND b.karyawanId NOT IN (select distinct karyawanId from dbmaster.t_karyawanadmin)";
                                                $query .=" AND b.jabatanid IN ('15', '10', '18', '08')";
                                                /*
                                                $query .=" AND LEFT(b.nama,4) NOT IN ('NN -', 'DR -', 'DM -', 'BDG ', 'OTH.', 'TO. ', 'BGD-', 'JKT ')  "
                                                        . " and LEFT(b.nama,7) NOT IN ('NN DM - ')  "
                                                        . " and LEFT(b.nama,3) NOT IN ('TO.', 'TO-', 'DR ', 'DR-', 'JKT', 'NN-') "
                                                        . " AND LEFT(b.nama,5) NOT IN ('OTH -', 'NN AM', 'NN DR', 'TO - ') ";
                                                 * 
                                                 */
                                                /*
                                                $query .=" order by b.nama";
                                                $tampil = mysqli_query($cnmy, $query);
                                                
                                                while ($z= mysqli_fetch_array($tampil)) {
                                                    $pnidkry=$z['karyawanid'];
                                                    $pnamakry=$z['nama'];
                                                    echo "<option value='$pnidkry'>$pnamakry ($pnidkry)</option>";
                                                }
                                                 * 
                                                 */
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                
                                <div hidden class='form-group'>
                                    <div class='col-sm-12'>
                                        <b>Dokter</b><br/>
                                        <button type='button' class='btn btn-danger btn-xs' onclick="HapudDataDokter('')">Hapus Dokter</button><br/><br/>
                                        <div class="form-group">
                                            
                                            <div class='input-group '>
                                                <span class='input-group-btn'>
                                                    <button type='button' class='btn btn-primary' data-toggle='modal' data-target='#myModal' onClick="getDataDokter('e_iddokt', 'e_nmdokt')">Pilih!</button>
                                                </span>
                                                <input type='text' class='form-control' id='e_nmdokt' name='e_nmdokt' value='' Readonly>
                                                <input type='hidden' class='form-control' id='e_iddokt' name='e_iddokt' value='' Readonly>
                                            </div>
                                            
                                        </div>
                                    </div>
                                </div>
                            
                            
                            
                                <div hidden class='form-group'>
                                    <div class='col-sm-12'>
                                        <b>Cabang</b> <input type="checkbox" id="chkidcabang" value="deselect" onClick="SelAllCheckBox('chkidcabang', 'chkbox_idcab[]')" checked/>
                                        <div class="form-group">
                                            <div id="kotak-multi2" class="jarak">
                                                <?PHP
                                                echo "&nbsp; <input type=checkbox value='pilih_kosong' name='chkbox_idcab[]' checked> _Blank<br/>";
                                                /*
                                                    echo "&nbsp; <input type=checkbox value='pilih_kosong' name='chkbox_idcab[]' checked> _Blank<br/>";
                                                    $query = "select a.icabangid as icabangid, a.nama as nama from MKT.icabang a "
                                                            . " WHERE 1=1 ";
                                                    $query .= " AND IFNULL(aktif,'')<>'N' ";
                                                    $query .= " AND left(a.nama,5) NOT IN ('OTC -', 'ETH -', 'PEA -')";
                                                    $query .= " ORDER BY a.nama";
                                                    $tampil = mysqli_query($cnmy, $query);
                                                    while ($z= mysqli_fetch_array($tampil)) {
                                                        $picbid=$z['icabangid'];
                                                        $pnmcb=$z['nama'];
                                                        
                                                        echo "&nbsp; <input type=checkbox value='$picbid' name='chkbox_idcab[]' checked> $pnmcb<br/>";
                                                    }
                                                    echo "<br/>";
                                                    $query = "select a.icabangid as icabangid, a.nama as nama from MKT.icabang a "
                                                            . " WHERE 1=1 ";
                                                    $query .= " AND IFNULL(aktif,'')='N' ";
                                                    $query .= " AND left(a.nama,5) NOT IN ('OTC -', 'ETH -', 'PEA -')";
                                                    $query .= " ORDER BY a.nama";
                                                    $tampil = mysqli_query($cnmy, $query);
                                                    while ($z= mysqli_fetch_array($tampil)) {
                                                        $picbid=$z['icabangid'];
                                                        $pnmcb=$z['nama'];
                                                        
                                                        echo "&nbsp; <input type=checkbox value='$picbid' name='chkbox_idcab[]' checked> $pnmcb (Non Aktif)<br/>";
                                                    }
                                                    */
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
        </form>

    </div>
    <!--end row-->
</div>

<script>
    function disp_confirm(pText)  {
        if (pText == "excel") {
            document.getElementById("demo-form2").action = "<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]&ket=excel"; ?>";
            document.getElementById("demo-form2").submit();
            return 1;
        }else{
            document.getElementById("demo-form2").action = "<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]&ket=bukan"; ?>";
            document.getElementById("demo-form2").submit();
            return 1;
        }
    }


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
    
</script>


<script>
    function getDataDokter(data1, data2){
        var eidkry =document.getElementById('cb_karyawan').value;
        
        $.ajax({
            type:"post",
            url:"module/laporan/lap_ks_user/viewdata_dokter.php?module=viewdatadokter",
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
    
    function HapudDataDokter(){
        document.getElementById('e_iddokt').value="";
        document.getElementById('e_nmdokt').value="";
    }
    
    function RefreshDataDokter() {
        $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
        $.ajax({
            type:"post",
            url:"module/laporan/lap_ks_user/viewdatausr.php?module=caridatadokter",
            data:"ualldata=ualldata",
            success:function(data){
                $("#kotak-multi3").html(data);
                $("#loading").html("");
            }
        });

    }
    
    function RefreshDataDokterALL() {
        $("#loading2").html("<center><img src='images/loading.gif' width='50px'/></center>");
        $.ajax({
            type:"post",
            url:"module/laporan/lap_ks_user/viewdatausrall.php?module=caridatadokter",
            data:"ualldata=ualldata",
            success:function(data){
                $("#c-data").html(data);
                $("#loading2").html("");
            }
        });

    }
</script>