<?PHP
    include "config/cek_akses_modul.php";
    $aksi="module/marketing/mkt_closingcutihrd/aksi_closingcutihrd.php";
    $hari_ini = date("Y-m-d");
    $thn_pilih = date("Y");
    $tgl_pertama = date('F Y', strtotime('-1 month', strtotime($hari_ini)));

    
    $pkaryawanid = trim($_SESSION['IDCARD']);
    $pnamauser = trim($_SESSION['NAMALENGKAP']);
    $pgroupid = trim($_SESSION['GROUP']);
    
    $apvpilih="approve";
    
    if (!empty($_SESSION['CLSCUTITHN'])) $thn_pilih=$_SESSION['CLSCUTITHN'];
    
    $pact="";
    $pmodule=$_GET['module'];
    $pidmenu=$_GET['idmenu'];
    if (isset($_GET['act'])) $pact=$_GET['act'];
    
    switch($pact){
        default:
            ?>


                <script>
                    $(document).ready(function() {
                        var eapvpilih=document.getElementById('e_apvpilih').value;
                        //pilihData(eapvpilih);
                    } );

                    function ProsesClosingCuti(ket){
                        var etahun=document.getElementById('e_tahun').value;

                        document.getElementById('e_apvpilih').value=ket;

                        var myurl = window.location;
                        var urlku = new URL(myurl);
                        var module = urlku.searchParams.get("module");
                        var idmenu = urlku.searchParams.get("idmenu");
                        var act = urlku.searchParams.get("act");
                        
                        //alert(ket);
                        
                        $.ajax({
                            type:"post",
                            url:"module/marketing/viewdatamkt.php?module=cekdataprosclssudahada",
                            data:"eket="+ket+"&utahun="+etahun+"&uketapv="+ket,
                            success:function(data){
                                
                                if (data=="sudahada") {
                                    if (ket=="hapusprosescuti") {
                                        var cmt = confirm('Apakah akan melakukan hapus data tahun '+etahun+' ...???');
                                        if (cmt == false) {
                                            return false;
                                        }
                                    }else{
                                        var cmt = confirm('Tahun '+etahun+' Sudah Pernah Closing Data Cuti....\n\
Apakah akan melakukan proses ulang...???');
                                        if (cmt == false) {
                                            return false;
                                        }
                                    }
                                }else{
                                    if (ket=="hapusprosescuti") {
                                        alert('Tahun '+etahun+" Belum ada data yang diproses..."); return false;
                                    }else{
                                        var cmt = confirm('Apakah akan melakukan proses tahun '+etahun+' ...???');
                                        if (cmt == false) {
                                            return false;
                                        }
                                    }
                                }
                                
                                //simpan data ke DB

                                $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                                $.ajax({
                                    type:"post",
                                    url:"module/marketing/mkt_closingcutihrd/aksi_closingcutihrd.php?module="+module+"&idmenu="+idmenu+"&act="+act,
                                    data:"eket="+ket+"&utahun="+etahun+"&uketapv="+ket,
                                    success:function(data){
                                        $("#c-data").html(data);
                                        $("#loading").html("");
                                    }
                                });
                        
                            }
                        });
                        
                    }

                    function pilihData(ket){
                        var etahun=document.getElementById('e_tahun').value;

                        document.getElementById('e_apvpilih').value=ket;

                        var myurl = window.location;
                        var urlku = new URL(myurl);
                        var module = urlku.searchParams.get("module");
                        var idmenu = urlku.searchParams.get("idmenu");
                        var act = urlku.searchParams.get("act");


                        //alert(ket);

                        $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                        $.ajax({
                            type:"post",
                            url:"module/marketing/mkt_closingcutihrd/viewdatatableclscuti.php?module="+module+"&idmenu="+idmenu+"&act="+act,
                            data:"eket="+ket+"&utahun="+etahun+"&uketapv="+ket,
                            success:function(data){
                                $("#c-data").html(data);
                                $("#loading").html("");
                            }
                        });
                    }
                    
                    function KosongkanData() {
                        $("#c-data").html("");
                    }
                    
                    
                </script>

                <script type="text/javascript">
                    $(document).ready(function() {
                        $('#thn01').on('change dp.change', function(e){
                            KosongkanData();
                        });
                    });
                </script>

                <div class="">

                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="title_left">
                            <h3>
                                Closing Tahunan Cuti Ethical
                            </h3>
                        </div></div><div class="clearfix">
                    </div>

                    <!--row-->
                    <div class="row">

                        <form name='form1' id='form1' method='POST' action='' enctype='multipart/form-data'>

                            <div class='col-md-12 col-sm-12 col-xs-12'>
                                <div class='x_panel'>

                                     <div hidden class='col-sm-3'>
                                        <small>notes</small>
                                        <div class="form-group">
                                            <div class='input-group date'>
                                                <input type='text' class='form-control input-sm' id='e_apvpilih' name='e_apvpilih' value='<?PHP echo $apvpilih; ?>' Readonly>
                                            </div>
                                        </div>
                                    </div>

                                    <div class='col-sm-3'>
                                        Tahun
                                        <div class="form-group">
                                            <div class='input-group date' id='thn01'>
                                                <input type='text' id='e_tahun' name='e_tahun' required='required' class='form-control input-sm' placeholder='tahun' value='<?PHP echo $thn_pilih; ?>' placeholder='yyyy' Readonly>
                                                <span class="input-group-addon">
                                                   <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                     <div class='col-sm-3'>
                                        Proses By
                                        <div class="form-group">
                                            <select class='form-control input-sm' id='cb_karyawan' name='cb_karyawan' onchange="KosongkanData()" data-live-search="true">
                                                <?PHP 
                                                $query = "select karyawanId as karyawanid, nama as nama From hrd.karyawan
                                                    WHERE (IFNULL(tglkeluar,'0000-00-00')='0000-00-00' OR IFNULL(tglkeluar,'')='') ";
                                                $query .= " AND karyawanId ='$pkaryawanid' ";
                                                $query .= " ORDER BY nama";
                                                $tampil= mysqli_query($cnmy, $query);
                                                while ($row= mysqli_fetch_array($tampil)) {
                                                    $npidkry=$row['karyawanid'];
                                                    $npnmkry=$row['nama'];

                                                    if ($npidkry==$pkaryawanid)
                                                          echo "<option value='$npidkry' selected>$npnmkry</option>";
                                                    else
                                                        echo "<option value='$npidkry'>$npnmkry</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                     <div class='col-sm-6'>
                                        &nbsp;
                                        <div class="form-group">
                                            <input onclick="pilihData('lihatdata')" class='btn btn-warning btn-sm' type='button' name='buttonview1' value='Lihat Data'> &nbsp; &nbsp;
                                            <input onclick="ProsesClosingCuti('prosesdatacuti')" class='btn btn-dark btn-sm' type='button' name='buttonview2' value='Proses Data'> &nbsp; &nbsp;
                                            <input onclick="ProsesClosingCuti('hapusprosescuti')" class='btn btn-danger btn-sm' type='button' name='buttonview2' value='Hapus Proses'> &nbsp; &nbsp;
                                        </div>
                                    </div>


                                </div>
                            </div>

                            

                            <div id='loading'></div>
                            <div class='col-md-12 col-sm-12 col-xs-12'>
                                <div class='x_panel'>
                                    
                                    <div id='c-data'>
                                        <div class='x_content'>

                                            <table id='datatable' class='table table-striped table-bordered' width='100%'>
                                                <thead>
                                                    <tr>
                                                        <th width='5px'>No</th>
                                                        <th width='50px'></th>
                                                        <th width='20px'>Karyawan Id</th>
                                                        <th width='30px'>Nama Karyawan</th>
                                                        <th width='30px'>Jabatan</th>
                                                        <th width='50px'>Tgl. Masuk</th>
                                                        <th width='50px'>Jumlah</th>
                                                        <th width='10px'>Cuti</th>
                                                        <th width='30px'>Sisa Cuti</th>
                                                    </tr>
                                                </thead>
                                                <tbody>

                                                </tbody>
                                            </table>

                                        </div>
                                    </div>
                                    
                                </div>
                            </div>

                        </form>

                    </div>
                </div>
                
            <?PHP
        break;
        

    }
?>