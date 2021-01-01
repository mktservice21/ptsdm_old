<?PHP session_start(); ?>
    
    <!-- Datatables -->
    <script src="../../vendors/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="../../vendors/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
    <script src="../../vendors/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
    <script src="../../vendors/datatables.net-buttons-bs/js/buttons.bootstrap.min.js"></script>
    <script src="../../vendors/datatables.net-buttons/js/buttons.flash.min.js"></script>
    <script src="../../vendors/datatables.net-buttons/js/buttons.html5.min.js"></script>
    <script src="../../vendors/datatables.net-buttons/js/buttons.print.min.js"></script>
    <script src="../../vendors/datatables.net-fixedheader/js/dataTables.fixedHeader.min.js"></script>
    <script src="../../vendors/datatables.net-keytable/js/dataTables.keyTable.min.js"></script>
    <script src="../../vendors/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
    <script src="../../vendors/datatables.net-responsive-bs/js/responsive.bootstrap.js"></script>
    <script src="../../vendors/datatables.net-scroller/js/dataTables.scroller.min.js"></script>
    <script src="../../vendors/jszip/dist/jszip.min.js"></script>
    <script src="../../vendors/pdfmake/build/pdfmake.min.js"></script>
    <script src="../../vendors/pdfmake/build/vfs_fonts.js"></script>

    
<script>

    
    
    $(document).ready(function() {
        var table = $('#datatable').DataTable({
            <?PHP if ($_SESSION['MOBILE']=="Y") {?>
                fixedHeader: false,
            <?PHP } else {?>
                fixedHeader: true,
            <?PHP } ?>
            "ordering": false,
            "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
            "displayLength": -1,
        } );


    } );
</script>

<style>
    .divnone {
        display: none;
    }
    #per-kiri{float:left;width:30%; margin-right: 15px;}
    #per-kanan{float:left;width:30%; margin-right: 5px;}
</style>
    
<?php
    
    include '../../config/koneksimysqli.php';
    //echo $_POST['buttonapv']." ".$_POST['uperiode1']." ada ".$_POST['uperiode2']."<br/>";
    echo "<div class='x_content'>";
    
        echo "<table id='datatable' class='table table-striped table-bordered'>";
        echo "<thead><tr>";
        echo "<th width='10px'>No</th>";
        echo "<th width='10px'><input type=\"checkbox\" id=\"chkbtnbr\" value=\"select\" "
                . "onClick=\"SelAllCheckBox('chkbtnbr', 'chkbox_br[]')\" /></th>"
        . "<th width='150px'>NOBR</th><th>Yang Mengajukan</th><th>Tgl. Perlu</th>"
        . "<th>Rp.</th><th width='150px'>Keterangan</th>";
        echo "</tr></thead>";
        echo "<tbody>";
        $no=1;
        $query = "SELECT * FROM dbbudget.v_br ";
        if (strtoupper($_POST['buttonapv'])=="APPROVE") {
            $query .=" where NOBR not in (select distinct ifnull(NOBR,'') from dbbudget.t_br_ttd)";
        }elseif (strtoupper($_POST['buttonapv'])=="UNAPPROVE") {
            $query .=" where NOBR in (select distinct ifnull(NOBR,'') from dbbudget.t_br_ttd)";
        }elseif (strtoupper($_POST['buttonapv'])=="REJECT") {
            $query .=" ";
        }else{
            $query .=" ";
        }
        $query .=" order by NOBR";
        $tampil = mysqli_query($cnmy, $query);
        
        while ($r=mysqli_fetch_array($tampil)){

            $rp=number_format($r['RP'],0,",",",");
            $tglperlu = date('d F Y', strtotime($r['TGL_PERLU']));
            echo "<tr scope='row'>";
            echo "<td>$no</td>";
            echo "<td><input type=checkbox value='$r[NOBR]' name=chkbox_br[]></td>";
            if (strtoupper($_POST['buttonapv'])=="APPROVE") {
                echo "<td>";
                ?><a href="#" class='btn btn-success btn-sm' data-toggle='modal' 
                   onClick=window.open("<?PHP echo "eksekusi_ttd.php?module=$_POST[umodule]&idmenu=$_POST[uidmenu]&act=approve&nobr=".$r['NOBR'];?>","Ratting","width=600,height=500,left=500,top=100,scrollbars=yes,toolbar=yes,status=1,pagescrool=yes");>
                    <?PHP echo $r['NOBR']; ?></a><?PHP
                echo "</td>";
            }elseif (strtoupper($_POST['buttonapv'])=="UNAPPROVE") {
                echo "<td>";
                ?><a href="#" class='btn btn-success btn-sm' data-toggle='modal' 
                   onClick=window.open("<?PHP echo "eksekusi_ttd.php?module=$_POST[umodule]&idmenu=$_POST[uidmenu]&act=unapprove&nobr=".$r['NOBR'];?>","Ratting","width=600,height=200,left=500,top=100,scrollbars=yes,toolbar=yes,status=1,pagescrool=yes");>
                    <?PHP echo $r['NOBR']; ?></a><?PHP
                echo "</td>";
            }else echo "<td>$r[NOBR]</td>";
            
            echo "<td>$r[nama]</td>";
            echo "<td>$tglperlu</td>";
            echo "<td align='right'>$rp</td>";
            echo "<td>$r[KETERANGAN]</td>";
            echo "</tr>";

            $tampilakun = mysqli_query($cnmy, "SELECT * FROM dbbudget.v_br_d where NOBR='$r[NOBR]' order by NOBR");
            while ($a=mysqli_fetch_array($tampilakun)){
                $jml=number_format($a['JUMLAH'],0,",",",");
                echo "<tr scope='row'>";
                echo "<td colspan=2></td>";
                echo "<td class='divnone'></td>";
                echo "<td colspan=3>$a[NAMA_AKUN]</td>";
                echo "<td class='divnone'></td>";
                echo "<td class='divnone'></td>";
                echo "<td align='right'>$jml</td>";
                echo "<td>$a[KET]</td>";
                echo "</tr>";
            }

            $no++;
        }
        echo "</tbody>";
        echo "</table>";
    echo "</div>";

    echo "<div class='x_title'><h2>";
        if (strtoupper($_POST['buttonapv'])=="APPROVE") {
            echo "<input type='hidden' name='e_stsapv' id='e_stsapv' value='approve'>";
            //echo "<input class='btn btn-default' type='Submit' name='buttonapv' value='Approve'>";
            echo "<input class='btn btn-default' type='Submit' name='buttonapv' value='Reject'>";
        }elseif (strtoupper($_POST['buttonapv'])=="UNAPPROVE") {
            echo "<input type='hidden' name='e_stsapv' id='e_stsapv' value='unapprove'>";
            echo "<input class='btn btn-default' type='button' name='buttonapv' value='UnApprove' onClick=\"ProsesData('g_module', 'g_idmenu', 'e_stsapv', 'chkbox_br[]')\">";
        }elseif (strtoupper($_POST['buttonapv'])=="REJECT") {
            echo "<input type='hidden' name='e_stsapv' id='e_stsapv' value='reject'>";
        }else{
            echo "<input type='hidden' name='e_stsapv' id='e_stsapv' value='pending'>";
        }
    echo "</h2><div class='clearfix'></div></div>";
    
                            if (strtoupper($_POST['buttonapv'])=="APPROVE") {
                                echo "<div class='col-sm-5'>";
                                include "../../tanda_tangan_base64/tanda_tangan_semua_view.php";
                                echo "</div>";
                            }else{
                                
                            }
                            
            /*
            echo "<td>";//AKSI
                echo " <a class='btn btn-success btn-sm' href=?module=$_GET[module]&idmenu=$_GET[idmenu]&act=editdata&id=$r[NOBR]>Edit</a>
                    <a class='btn btn-danger btn-sm' href=\"$aksi?module=$_GET[module]&act=hapus&id=$r[NOBR]&idmenu=$_GET[idmenu]\"
                    onClick=\"return confirm('Apakah Anda benar-benar akan menghapusnya?')\">Hapus</a>";
            echo "</td>";
             */
echo "</div>";//end panel
                    
?>

