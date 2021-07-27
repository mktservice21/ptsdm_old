<?PHP
    include "config/cek_akses_modul.php";
    $aksi="module/md_m_spg_bpjskerja/aksi_bpjskerja.php";
    $hari_ini = date("Y-m-d");
    $tgl_pertama = date('F Y', strtotime('-1 month', strtotime($hari_ini)));
    $tgl_akhir = date('F Y', strtotime('+1 month', strtotime($hari_ini)));
    
    $tgl_pertama = date('F Y', strtotime($hari_ini));
    
    $pkaryawanid = trim($_SESSION['IDCARD']);
    $pnamauser = trim($_SESSION['NAMALENGKAP']);
    $pgroupid = trim($_SESSION['GROUP']);
    
    $apvpilih="approve";
    
    if (!empty($_SESSION['APVCUTISTS'])) $apvpilih=$_SESSION['APVCUTISTS'];
    if (!empty($_SESSION['APVCUTIBLN1'])) $tgl_pertama=$_SESSION['APVCUTIBLN1'];
    if (!empty($_SESSION['APVCUTIBLN2'])) $tgl_akhir=$_SESSION['APVCUTIBLN2'];
    //if (!empty($_SESSION['APVCUTIAPVBY'])) $pkaryawanid=$_SESSION['APVCUTIAPVBY'];
    
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
                        pilihData(eapvpilih);
                    } );

                    function pilihData(ket){
                        var etgl1=document.getElementById('tgl1').value;
                        var ekaryawan=document.getElementById('cb_karyawan').value;

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
                            url:"module/md_m_spg_bpjskerja/viewdatatablebpjskerja.php?module="+module+"&idmenu="+idmenu+"&act="+act,
                            data:"eket="+ket+"&uperiode1="+etgl1+"&ukaryawan="+ekaryawan+"&uketapv="+ket,
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


                <div class="">

                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="title_left">
                            <h3>
                                Data BPJS Ketenagakerjaan SPG
                            </h3>
                        </div></div><div class="clearfix">
                    </div>

                    <!--row-->
                    <div class="row">

                        <form name='form1' id='form1' method='POST' action='' enctype='multipart/form-data'>

                            <div class='col-md-12 col-sm-12 col-xs-12'>
                                <div class='x_panel'>

                                    <div class="well" style="overflow: auto; margin-top: -5px; margin-bottom: 5px; padding-top: 10px; padding-bottom: 6px;">
                                        <input onclick="pilihData('approve')" class='btn btn-warning btn-sm' type='button' name='buttonview1' value='Lihat Data BPJS'>
                                    </div>

                                     <div hidden class='col-sm-3'>
                                        <small>notes</small>
                                        <div class="form-group">
                                            <div class='input-group date'>
                                                <input type='text' class='form-control input-sm' id='e_apvpilih' name='e_apvpilih' value='<?PHP echo $apvpilih; ?>' Readonly>
                                            </div>
                                        </div>
                                    </div>

                                    <div class='col-sm-3'>
                                        Periode
                                        <div class="form-group">
                                            <div class='input-group date' id='cbln01'>
                                                <input type='text' id='tgl1' name='e_periode01' required='required' class='form-control input-sm' placeholder='tgl awal' value='<?PHP echo $tgl_pertama; ?>' placeholder='dd mmm yyyy' Readonly>
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


                                </div>
                            </div>


                            <div id='loading'></div>
                            <div id='c-data'>
                                <div class='x_content'>

                                    <table id='datatable' class='table table-striped table-bordered' width='100%'>
                                        <thead>
                                            <tr>
                                                <th width='7px'>No</th>
                                                <th width='10px'>
                                                    <input type="checkbox" id="chkbtnbr" value="select" 
                                                    onClick="SelAllCheckBox('chkbtnbr', 'chkbox_br[]')" />
                                                </th>
                                                <th width='50px'>&nbsp;</th>
                                                <th width='50px'>Karyawan</th>
                                                <th width='50px'>Jenis</th>
                                                <th width='50px'>Keperluan</th>
                                                <th width='200px'>Periode</th>
                                                <th width='50px'>Satus Approve</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>

                                </div>
                            </div>

                        </form>

                    </div>
                </div>
                
            <?PHP
        break;

        case "tambahbaru":
            include "tambah_cutihrd.php";
        break;

        case "editdata":
            include "tambah_cutihrd.php";
        break;

    }
?>