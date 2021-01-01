<?PHP
    $hari_ini = date("Y-m-d");
    $tgl_pertama = date('F Y', strtotime($hari_ini));
    
    if (!empty($_SESSION['UCPTGL1'])) $tgl_pertama = $_SESSION['UCPTGL1'];
    
    $fkaryawan=$_SESSION['IDCARD'];
    if (!empty($_SESSION['EKARUC'])) $fkaryawan=$_SESSION['EKARUC'];
    
    $fstsadmin=$_SESSION['STSADMIN'];
    $flvlposisi=$_SESSION['LVLPOSISI'];
    $fdivisi=$_SESSION['DIVISI'];

?>

<div class="">

    <div class="col-md-12 col-sm-12 col-xs-12"><div class="title_left">
            <h2>
                <?PHP
                $judul="Hari Kerja Marketing";
                if ($_GET['act']=="tambahbaru")
                    echo "Input $judul";
                elseif ($_GET['act']=="editdata")
                    echo "Edit $judul";
                else
                    echo "Data $judul";
                ?>
            </h2>
    </div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">

        <?php
        //$aksi="module/mod_mkt_planuc/laporanbrbulan.php";
        $aksi="eksekusi3.php";
        switch($_GET['act']){
            default:
                ?>
        
                <script type="text/javascript" language="javascript" >

                    function RefreshDataTabel() {
                        KlikDataTabel();
                    }

                    $(document).ready(function() {
                        var ekar=document.getElementById('e_idkaryawan').value;
                        if (ekar != "") {
                            KlikDataTabel();
                        }
                    } );

                    function KlikDataTabel() {
                        var myurl = window.location;
                        var urlku = new URL(myurl);
                        var module = urlku.searchParams.get("module");
                        var idmenu = urlku.searchParams.get("idmenu");
                        var ket="";
                        var ekar=document.getElementById('e_idkaryawan').value;
                        var etgl1=document.getElementById('tgl1').value;
                        var eidc=<?PHP echo $_SESSION['USERID']; ?> ;

                        $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                        $.ajax({
                            type:"post",
                            url:"module/mod_mkt_planuc/viewdatatabel.php?module="+ket,
                            data:"eket="+ket+"&ukar="+ekar+"&uidc="+eidc+"&idmenu="+idmenu+"&module="+module+"&utgl="+etgl1,
                            success:function(data){
                                $("#c-data").html(data);
                                $("#loading").html("");
                            }
                        });
                    }

                </script>
                

                    
                <div class='col-md-12 col-sm-12 col-xs-12'>
                    <div class='x_panel'>

                            <div class='col-sm-2'>
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

                            <div class='col-sm-2'>
                                Karyawan
                                <div class="form-group">
                                    <select class='form-control input-sm' id='e_idkaryawan' name='e_idkaryawan' onchange="showAreaEmp('', 'e_idkaryawan', 'e_idarea')">
                                        <?PHP
                                        comboKaryawanAktif("", "pilihan", $fkaryawan, $_SESSION['STSADMIN'], $_SESSION['LVLPOSISI'], $_SESSION['DIVISI'], $_SESSION['IDCARD']);
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class='col-sm-3'>
                                <small>&nbsp;</small>
                               <div class="form-group">
                                   <input type='button' class='btn btn-success btn-xs' id="s-submit" value="View Data" onclick="RefreshDataTabel()">&nbsp;
                               </div>
                           </div>

                        <div class='col-sm-12'>
                        *) <b>Note :</b><br/>&nbsp;
                        Silahkan diisi bila ada perjalanan dinas ke luar kota / sakit / cuti / alasan tidak masuk lainnya.
                            jika tidak diisi berarti hari kerja seperti biasa.
                        </div>
						
                        <div id='loading'></div>
                        <div id='c-data'>
                            <table id='datatablercbi' class='table table-striped table-bordered' width='100%'>
                                <thead>
                                    <tr>
                                        <th width='10px'><input type="checkbox" id="chkbtnall" value="select" onClick="SelAllCheckBox('chkbtnall', 'chkbox_id[]')"/></th>
                                        <th width='50px'>Hari</th>
                                        <th width='220px'>Tujuan / Note</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>

                    </div>
                </div>
                
                
                

                <?PHP

            break;
        
        }
        ?>

    </div>
    <!--end row-->
</div>

