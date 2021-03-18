<?PHP
    $hari_ini2 = date("Y-m-d");
    $hari_ini = date("Y-m-01");
    $tgl_pertama = date('F Y', strtotime($hari_ini));
    $tgl_akhir = date('F Y', strtotime($hari_ini2));
    
    $fdivisi="";
    $ffrominc="";
    if (!empty($_SESSION['PIPERENTY1'])) $tgl_pertama = $_SESSION['PIPERENTY1'];
    if (!empty($_SESSION['PIPERENTY2'])) $tgl_akhir = $_SESSION['PIPERENTY2'];
    if (!empty($_SESSION['PIDIVISI'])) $fdivisi = $_SESSION['PIDIVISI'];
    if (!empty($_SESSION['PIINCFROM'])) $ffrominc = $_SESSION['PIINCFROM'];
    
    $fkaryawan=$_SESSION['IDCARD'];
    $fstsadmin=$_SESSION['STSADMIN'];
    $flvlposisi=$_SESSION['LVLPOSISI'];
    $figroupuser=$_SESSION['GROUP'];
?>

<div class="">

    <div class="page-title"><div class="title_left">
            <h3>
                <?PHP
                $judul="Proses Insentif Per Divisi";
                if ($_GET['act']=="tambahbaru")
                    echo "Input $judul";
                elseif ($_GET['act']=="editdata")
                    echo "Edit $judul";
                else
                    echo "$judul";
                ?>
            </h3>
    </div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">

        <?php
        //$aksi="module/md_m_prosesdatainsentif/laporanbrbulan.php";
        $aksi="eksekusi3.php";
        switch($_GET['act']){
            default:
                ?>
        
                <script type="text/javascript" language="javascript" >
                    
                    function RefreshDataTabel() {
                        KlikDataTabel();
                    }

                    $(document).ready(function() {
                        KlikDataTabel();
                    } );

                    function KlikDataTabel() {
                        var ket="";
                        var etgltipe="";
                        var etgl1=document.getElementById('tgl1').value;
                        var etgl2=document.getElementById('tgl2').value;
                        var edivisi=document.getElementById('e_divisi').value;
                        var eincfrom=document.getElementById('e_incfrom').value;
                        var eidc=<?PHP echo $_SESSION['USERID']; ?> ;

                        $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                        $.ajax({
                            type:"post",
                            url:"module/md_m_prosesdatainsentif/viewdatatabel.php?module="+ket,
                            data:"eket="+ket+"&utgltipe="+etgltipe+"&uperiode1="+etgl1+"&uperiode2="+etgl2+"&udivisi="+edivisi+"&uidc="+eidc+"&ucabang="+"&uincfrom="+eincfrom,
                            success:function(data){
                                $("#c-data").html(data);
                                $("#loading").html("");
                            }
                        });
                    }

                </script>
                
                <script>
                    function disp_confirm_prev(pText)  {
                        if (pText == "excel") {
                            document.getElementById("demo-form10").action = "<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]&ket=excel"; ?>";
                            document.getElementById("demo-form10").submit();
                            return 1;
                        }else{
                            document.getElementById("demo-form10").action = "<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]&ket=bukan"; ?>";
                            document.getElementById("demo-form10").submit();
                            return 1;
                        }
                    }
                </script>

                    
                <div class='col-md-12 col-sm-12 col-xs-12'>
                    <div class='x_panel'>

                        <div hidden class='x_title'>
                            <h2><input class='btn btn-default' type=button value='Tambah Baru'
                                onclick="window.location.href='<?PHP echo "?module=$_GET[module]&idmenu=$_GET[idmenu]&act=tambahbaru"; ?>';">
                                <small></small>
                            </h2>
                            <div class='clearfix'></div>
                        </div>

                        <form method='POST' action='<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]"; ?>' 
                              id='demo-form10' name='form10' data-parsley-validate class='form-horizontal form-label-left' target="_blank">

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

                            <div hidden class='col-sm-2'>
                               <small>s/d.</small>
                               <div class="form-group">
                                   <div class='input-group date' id='cbln02'>
                                       <input type='text' id='tgl2' name='e_periode02' required='required' class='form-control input-sm' placeholder='tgl akhir' value='<?PHP echo $tgl_akhir; ?>' placeholder='dd mmm yyyy' Readonly>
                                       <span class="input-group-addon">
                                          <span class="glyphicon glyphicon-calendar"></span>
                                       </span>
                                   </div>
                               </div>
                           </div>

                            <div class='col-sm-2'>
                                Divisi
                                <div class="form-group">
                                    <select class='form-control input-sm' id='e_divisi' name='e_divisi' onchange="">
                                        <option value="" selected>All</option>
                                        <option value="blank">_blank</option>
                                        <?PHP
                                            $query = "select DivProdId, nama from MKT.divprod WHERE br='Y' AND DivProdId NOT IN ('OTHER', 'HO', 'OTC')";
                                            $query .=" order by DivProdId";
                                            $tampil = mysqli_query($cnmy, $query);
                                            while ($z= mysqli_fetch_array($tampil)) {
                                                $namadiv=$z['nama'];
                                                $idnamadiv=$z['DivProdId'];
                                                //if ($namadiv=="CAN") $namadiv="CANARY";
                                                if ($fdivisi==$idnamadiv)
                                                    echo "<option value='$idnamadiv' selected>$namadiv</option>";
                                                else
                                                    echo "<option value='$idnamadiv'>$namadiv</option>";
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>


                            <div class='col-sm-2'>
                                Incentive From 
                                <div class="form-group">
                                    <select class='form-control input-sm' id='e_incfrom' name='e_incfrom' onchange="">
                                        <?PHP
                                            if ($ffrominc=="PM") {
                                                echo "<option value='GSM'>GSM</option>";
                                                echo "<option value='PM' selected>PM</option>";
                                            }else{
                                                echo "<option value='GSM' selected>GSM</option>";
                                                echo "<option value='PM'>PM</option>";
                                            }
                                            
                                        ?>
                                    </select>
                                </div>
                            </div>


                            <div class='col-sm-3'>
                                <small>&nbsp;</small>
                               <div class="form-group">
                                   <input type='button' class='btn btn-success btn-xs' id="s-submit" value="View Data" onclick="RefreshDataTabel()">&nbsp;
                                   <input type='button' class='btn btn-default btn-xs' id="s-print" value="Preview" onclick="disp_confirm_prev('bukan')">
                                   <input type='button' class='btn btn-info btn-xs' id="s-excel" value="Excel" onclick="disp_confirm_prev('excel')">
                               </div>
                           </div>
                       </form>

                        <div id='loading'></div>
                        <div id='c-data'>

                        </div>

                    </div>
                </div>
                
                
                <script>

                    $('#cbln01').on('change dp.change', function(e){
                        document.getElementById('tgl2').value=document.getElementById('tgl1').value;
                    });

                    $('#cbln02').on('change dp.change', function(e){
                        document.getElementById('tgl1').value=document.getElementById('tgl2').value;
                    });
                </script>
                

                <?PHP

            break;

            case "tambahbaru":
                include "tambah.php";
            break;

            case "editdata":
                include "tambah.php";
            break;

            case "lihatdata":
                include "lihatdata.php";
            break;
        
        }
        ?>

    </div>
    <!--end row-->
</div>

