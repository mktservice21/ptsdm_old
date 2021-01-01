<?PHP
    include "config/cek_akses_modul.php";
    $hari_ini2 = date("Y-m-d");
    $hari_ini = date("Y-01-01");
    $tgl_pertama = date('d F Y', strtotime($hari_ini));
    $tgl_akhir = date('t F Y', strtotime($hari_ini2));
    
    if (!empty($_SESSION['SSKASKECILCABT1'])) $tgl_pertama = $_SESSION['SSKASKECILCABT1'];
    if (!empty($_SESSION['SSKASKECILCABT2'])) $tgl_akhir = $_SESSION['SSKASKECILCABT2'];
    
    $fkaryawan=$_SESSION['IDCARD'];
    $fstsadmin=$_SESSION['STSADMIN'];
    $flvlposisi=$_SESSION['LVLPOSISI'];
    $fdivisi=$_SESSION['DIVISI'];
?>

<link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />
<link href="css/stylenew.css" rel="stylesheet" type="text/css" />
<div class="">

    <div class="page-title"><div class="title_left">
            <h3>
                <?PHP
                $judul="Limit Dana PC-M Kas Kecil Cabang CHC";
                if ($_GET['act']=="tambahbaru")
                    echo "Input $judul";
                elseif ($_GET['act']=="editdata")
                    echo "Edit $judul";
                else
                    echo "Data $judul";
                ?>
            </h3>
    </div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">

        <?php
        //$aksi="module/mod_br_entrybrbulan/laporanbrbulan.php";
        $aksi="eksekusi3.php";
        switch($_GET['act']){
            default:
                ?>
                <style>
                    .divnone {
                        display: none;
                    }
                    #datatablezonajbt th {
                        font-size: 12px;
                    }
                    #datatablezonajbt td { 
                        font-size: 11px;
                    }
                    .imgzoom:hover {
                        -ms-transform: scale(3.5); /* IE 9 */
                        -webkit-transform: scale(3.5); /* Safari 3-8 */
                        transform: scale(3.5);

                    }
                </style>
                <script type="text/javascript" language="javascript" >
                    

                    $(document).ready(function() {
                        var table = $('#datatablezonajbt').DataTable({
                            fixedHeader: true,
                            "ordering": false,
                            "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
                            "displayLength": -1,
                            "order": [[ 0, "asc" ]],
                            bFilter: false, bInfo: false, "bLengthChange": false, "bLengthChange": false,
                            "bPaginate": false
                        } );
                    } );


                </script>
                
                <script>
                    function disp_confirm_um(ket, cekbr){
                        var allnobr="";
                        var m_act="simpanum";
                        if (ket=="simpan") {
                            var cmt = confirm('Apakah akan melakukan '+ket+' ...?');
                        }else if (ket=="hapus") {
                            var cmt = confirm('Apakah akan melakukan hapus ...?');
                            m_act="hapusum";
                        }else{
                            var cmt = confirm('Apakah akan melakukan proses ...?');
                        }
                        if (cmt == false) {
                            return false;
                        }else{
                            var myurl = window.location;
                            var urlku = new URL(myurl);
                            var module = urlku.searchParams.get("module");
                            var idmenu = urlku.searchParams.get("idmenu");
                            //document.write("You pressed OK!")
                            document.getElementById("demo-form2").action = "module/mod_budget_ukkaskecilcabchc/aksi_ukkaskecilcabchc.php?module="+module+"&act="+m_act+"&idmenu="+idmenu;
                            document.getElementById("demo-form2").submit();
                            return 1;
                        }



                    }
                </script>

                    
                <div class='col-md-12 col-sm-12 col-xs-12'>
                    <div class='x_panel'>


                        <form method='POST' action='<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]"; ?>' 
                              id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left' >

                            <table id='datatablezonajbt' class='datatable table nowrap table-striped table-bordered' width="100%">
                                <thead>
                                    <tr>
                                        <th width='10px'>NO</th>
                                        <th width='10px'><?PHP //echo $fin_chkall; ?></th>
                                        <th width='30px' align="center">ID</th>
                                        <th width='300px' align="center" nowrap>Cabang</th>
                                        <th width='200px' align="center" nowrap>Limit Rp.</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?PHP
                                    $no=1;
                                    $query = "select a.icabangid_o, a.nama, b.jumlah from dbmaster.v_icabang_o a "
                                            . " LEFT JOIN dbmaster.t_uangmuka_kascabang b on a.icabangid_o=b.icabangid_o "
                                            . " WHERE a.aktif='Y' and left(a.nama,5) NOT IN ('ETH -') ";
                                    $query .=" order by a.nama";
                                    $tampil = mysqli_query($cnmy, $query);
                                    while ($row= mysqli_fetch_array($tampil)) {
                                        $idcab=$row['icabangid_o'];
                                        $nmcab=$row['nama'];
                                        $pjumlah=$row['jumlah'];

                                        $fin_cekbox = "<input type=checkbox value='$idcab' id='chkbox_brum[]' name='chkbox_brum[]'>";
                                        $finrp_id="<input type='hidden' size='8px' id='txt_idbr[]' name='txt_idbr[]' class='input-sm' autocomplete='off' value='$idcab'>";
                                        $finrp_um="<input type='text' size='8px' id='txtrp_um[]' name='txtrp_um[]' class='input-sm inputmaskrp2' autocomplete='off' value='$pjumlah'>";

                                        echo "<tr>";
                                        echo "<td>$no</td>";
                                        echo "<td>$finrp_id</td>";//$fin_cekbox
                                        echo "<td>$idcab</td>";
                                        echo "<td>$nmcab</td>";
                                        echo "<td align='right'>$finrp_um</td>";
                                        echo "</tr>";

                                        $no++;
                                    }

                                ?>
                                </tbody>
                            </table> 

                            <div class='col-md-12 col-sm-12 col-xs-12'>

                                <div id="div_jumlah">
                                    <div class='x_panel'>
                                        <div class='x_content'>
                                            <div class='col-md-12 col-sm-12 col-xs-12'>

                                                <div class='form-group'>
                                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''> <span class='required'></span></label>
                                                    <div class='col-xs-9'>
                                                        <div class="checkbox">
                                                            <button type='button' class='btn btn-info btn-sm' onclick='disp_confirm_um("simpan", "chkbox_brum[]")'>Simpan</button>
                                                            <!--<button type='button' class='btn btn-danger btn-sm' id="btnhapus" name="btnhapus" onclick='disp_confirm_um("hapus", "chkbox_brum[]")'>Hapus</button>-->
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
                </div>
                
                
                

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

