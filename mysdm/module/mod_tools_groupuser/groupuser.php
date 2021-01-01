<script>
$(document).ready(function() {
    var table = $('#datatable').DataTable( {
        fixedHeader: true,
        "stateSave": true
    } );

    $('#enable').on( 'click', function () {
        table.fixedHeader.enable();
    } );

    $('#disable').on( 'click', function () {
        table.fixedHeader.disable();
    } );
} );

</script>

<div class="">

    <div class="col-md-12 col-sm-12 col-xs-12"><div class="title_left"><h3>Group User</h3></div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">
                    
<?php
$aksi="module/mod_tools_groupuser/aksi_groupuser.php";
switch($_GET['act']){
    default:

                echo "<div class='col-md-12 col-sm-12 col-xs-12'>";

                    //panel
                    echo "<div class='x_panel'>";
                        //title
                        echo "<div class='x_title'>
                            <h2><input class='btn btn-default' type=button value='Tambah Baru'
                            onclick=\"window.location.href='?module=$_GET[module]&idmenu=$_GET[idmenu]&act=tambahuser';\">
                            <small>Data Group User</small></h2>
                            <div class='clearfix'></div>
                            </div>";
        

                        //isi content
                        echo "<div class='x_content'>";
                            //isi kata-kata
                            /*
                            echo "<p class='text-muted font-13 m-b-30'>";
                            echo "";
                            echo "</p>";
                             *
                             */

                                $tampil = mysqli_query($cnmy, "SELECT * FROM dbmaster.sdm_groupuser order by NAMA_GROUP");

                                echo "<table id='datatable' class='table table-striped table-bordered'>";
                                echo "<thead>
                                        <tr><th>No</th>
                                            <th>Nama</th>
                                            <th>Group Menu</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead><tbody>";

                                $no=1;
                                while ($r=mysqli_fetch_array($tampil)){
                                    echo "<tr><td>$no</td>
                                          <td><a class='btn btn-primary btn-xs' href=?module=groupuser&act=edituser&id=$r[ID_GROUP]&idmenu=$_GET[idmenu]>$r[NAMA_GROUP]</a></td>";
                                    echo "<td><a class='btn btn-primary btn-xs' href='?module=groupuser&act=editgroupmenu&id=$r[ID_GROUP]&nama=$r[NAMA_GROUP]&idmenu=$_GET[idmenu]' class='btn btn-mini edit'>Lihat dan Edit</a></td>";
                                    echo "<td>";
                                        if ($r['ID_GROUP']=="1"){
                                        }else{
                                            echo " <a class='btn btn-danger btn-xs' href=\"$aksi?module=groupuser&act=hapususer&id=$r[ID_GROUP]\" onClick=\"return confirm('Apakah Anda benar-benar mau menghapusnya?')\">hapus</a>";
                                        }
                                        echo "</td>";
                                    echo "</tr>";
                                    $no++;
                                }
                                echo "</tbody></table>";


                        echo "</div>";//end x_content

                    echo "</div>";//end panel

                echo "</div>";
        
    break;
  
    case "tambahuser":

        echo "<div class='col-md-12 col-sm-12 col-xs-12'>";
            echo "<div class='x_panel'>";
                echo "<form name='loglog' method=POST action='$aksi?module=groupuser&act=input&idmenu=$_GET[idmenu]' onSubmit='return validasi(this)'>
                      <table width='100%'>";
                echo "
                      <tr><td>Nama</td>     <td> : </td><td><input type=text class='form-control' name='nama'></td></tr>";

                echo "<tr><td colspan=3><input type=submit value=Simpan class='btn btn-primary'>
                      <input type=button class='btn btn-primary' value=Batal onclick=self.history.back()></td></tr>
                      </table></form>";
            echo "</div>";//panel
        echo "</div>";
    break;
    
    case "edituser":

            $edit=mysqli_query($cnmy, "SELECT * FROM dbmaster.sdm_groupuser WHERE ID_GROUP='$_GET[id]'");
            $r=mysqli_fetch_array($edit);
        echo "<div class='col-md-12 col-sm-12 col-xs-12'>";
            echo "<div class='x_panel'>";
            
            echo "
                  <form method=POST action=$aksi?module=groupuser&act=update&idmenu=$_GET[idmenu]>
                  <input type=hidden name=id value='$r[ID_GROUP]'>
                  <table width='100%'>
                  <tr><td>Nama</td>     <td> : </td><td><input type=text class='form-control' name='nama' value='$r[NAMA_GROUP]'></td></tr>";
            echo "<tr><td colspan=3><input type=submit class='btn btn-primary' value=Update class='btn'> <input type=button class='btn btn-primary' value=Batal onclick=self.history.back()></td></tr>
                </table></form>";
            
            echo "</div>";//panel
        echo "</div>";
    break;

    case "editgroupmenu":
        echo "<div class='col-md-12 col-sm-12 col-xs-12'>";

            //panel
            echo "<div class='x_panel'>";

        ?>
            <style>
                table.example_2 {
                    color: #333;
                    font-family: Helvetica, Arial, sans-serif;
                    width: 640px;
                    border-collapse:
                    collapse; border-spacing: 0;
                }

                td, th {
                    border: 1px solid transparent; /* No more visible border */
                    height: 30px;
                    transition: all 0.3s;  /* Simple transition for hover effect */
                }

                th {
                    background: #DFDFDF;  /* Darken header a bit */
                    font-weight: bold;
                }

                td {
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
            </style>
        <?PHP
        echo "<form method='POST' enctype='multipart/form-data' action='$aksi?module=groupuser&act=updatemenugrop&idgroup=$_GET[id]&nama=$_GET[nama]&idmenu=$_GET[idmenu]'>";


        echo "<p/><input type=submit class='btn btn-primary' value=Simpan> <a href='?module=groupuser&idmenu=$_GET[idmenu]&act=$_GET[idmenu]' class='btn btn-primary'>Kembali</a><p/>";

        $tampil=  mysqli_query($cnmy, "select * from dbmaster.sdm_menu where parent_id='0' AND publish='Y' order by urutan, judul");
        //echo "<table border='1' id='example_2' cellpadding='0' cellspacing='0' width='100%' class='display'>";
        echo "<table id='datatable2' class='table table-striped table-bordered'>";
        echo "<thead>
                <tr><th>No</th>
                    <th>Nama Menu</th>
                    <th colspan='2'>Cek</th>
                    <th>Tambah</th>
                    <th>Edit</th>
                    <th>Hapus</th>
                </tr>
            </thead><tbody>";

        $no=1;
        while ($r=mysqli_fetch_array($tampil)){
            echo "<tr><td>$no</td>";
            echo "<td class='biasa'><b>Menu $r[JUDUL]</b></td>";

            $carigrp=mysqli_query($cnmy, "select * from dbmaster.sdm_groupmenu where id_group='$_GET[id]' and id='$r[ID]'");
            $adagrp=mysqli_num_rows($carigrp);
            $adachk="";
            if ($adagrp>0) $adachk="checked";
            echo "<td class='center1' width='100'>
                <input type=checkbox value='$r[ID]' name=tag_km[] class='checkall$r[ID]' onClick='toggleCexBoxHILANG(this)' $adachk></td>
                <td></td><td><input type=checkbox value='$r[ID]' name=tag_tambah[] class='checkallT$r[ID]' onClick='toggleCexBox(this)'></td>
            <td><input type=checkbox value='$r[ID]' name=tag_tambah[] class='checkallE$r[ID]' onClick='toggleCexBox(this)'></td>
            <td><input type=checkbox value='$r[ID]' name=tag_tambah[] class='checkallH$r[ID]' onClick='toggleCexBox(this)'></td>";
            echo "</tr>";
            $tampil2=  mysqli_query($cnmy, "select * from dbmaster.sdm_menu where parent_id='$r[ID]' AND publish='Y' order by urutan, judul");
            $nsub=0;
            while ($s=mysqli_fetch_array($tampil2)){
                $no++;

                $carigrp=mysqli_query($cnmy, "select * from dbmaster.sdm_groupmenu where id_group='$_GET[id]' and id='$s[ID]'");
                $adagrp=mysqli_num_rows($carigrp);
                $adachk="";
                $cekT="";$cekE="";$cekH="";
                $cT="N";$cE="N";$cH="N";
                $grp=mysqli_fetch_array($carigrp);
                if ($adagrp>0){
                    
                    $adachk="checked";
                    if ($grp['TAMBAH']=="Y"){ $cekT="checked"; $cT="Y"; }
                    if ($grp['EDIT']=="Y"){ $cekE="checked"; $cE="Y"; }
                    if ($grp['HAPUS']=="Y"){ $cekH="checked"; $cH="Y"; }
                }
                
                if ($nsub==0){
                    $cT="Y";$cE="Y";$cH="Y";

                    echo "<tr><td>$no</td>";
                    echo "<td>$s[JUDUL]</td>";
                    echo "<td class='center' width='100'>&nbsp;</td><td class='center2'><input type=checkbox class='checkall$r[ID]' value='$s[ID]' name=tag_km[] $adachk></td>
                        <td class='center2'><input type=checkbox class='checkallT$r[ID]' value='$cT' name='arr_tambah$grp[ID]' $cekT></td>
                        <td class='center2'><input type=checkbox class='checkallE$r[ID]' value='$cE' name='arr_edit$grp[ID]' $cekE></td>
                        <td class='center2'><input type=checkbox class='checkallH$r[ID]' value='$cH' name='arr_hapus$grp[ID]' $cekH></td>";
                    echo "</tr>";
                }else{
                    echo "<tr><td>$no</td>";
                    echo "<td>$s[JUDUL]</td>";
                    echo "<td class='center' width='100'>&nbsp;</td><td class='center2'><input type=checkbox class='checkall$r[ID]' value='$s[ID]' name=tag_km[] $adachk></td>
                        <td class='center2'><input type=checkbox class='checkallT$r[ID]' name='arr_tambah$s[ID]' $cekT></td>
                        <td class='center2'><input type=checkbox class='checkallE$r[ID]' name='arr_edit$s[ID]' $cekE></td>
                        <td class='center2'><input type=checkbox class='checkallH$r[ID]' name='arr_hapus$s[ID]' $cekH></td>";
                    echo "</tr>";
                }
                
                $nsub++;
            }
            
            $no++;
        }
        echo "</tbody></table>";
        echo "<p/><p/><input type=submit class='btn btn-primary' value=Simpan> <a href='?module=groupuser&idmenu=$_GET[idmenu]&act=$_GET[idmenu]' class='btn btn-primary'>Kembali</a><p/>";
        echo "</form>";

            echo "</div>";//panel
        echo "</div>";
        
    break;
}
?>
    </div>
    <!--end row-->
</div>
<script type="text/javascript">
    function toggleCexBox(source) {
        var aInputs = document.getElementsByTagName('input');
        for (var i=0;i<aInputs.length;i++) {
            if (aInputs[i] != source && aInputs[i].className == source.className) {
                aInputs[i].checked = source.checked;
            }
        }
    }
</script> 