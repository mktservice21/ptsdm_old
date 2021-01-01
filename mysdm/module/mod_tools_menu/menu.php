<div class="">

    <div class="col-md-12 col-sm-12 col-xs-12"><div class="title_left"><h3>Data Menu</h3></div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">
<?php
$aksi="module/mod_tools_menu/aksi_menu.php";
switch($_GET['act']){
  // Tampil Menu
  default:
        echo "<div class='col-md-12 col-sm-12 col-xs-12'>";
            //panel
            echo "<div class='x_panel'>";
                echo "<input type=button class='btn btn-default' value='Tambah Menu' onclick=\"window.location.href='?module=menuutama&act=tambahmenu';\" ><br /><br />
                      <div id=paging>
                      *) Apabila PUBLISH = Y, maka Menu ditampilkan di halaman pengunjung. <br />
                      **) Apabila AKTIF = Y, maka Menu ditampilkan di halaman administrator pada daftar menu yang berada di bagian kanan.</div>
                      <table id='datatable' class='table table-striped table-bordered'>
                      <thead><tr>
                      <th>Urutan</td>
                      <th>nama menu</td>
                      <th>link</td>
                      <th>publish</td>
                      <th>kriteria</td>
                      <th>urutan</td>
                      <th>aksi</td>
                      </tr></thead><tbody>";
                $tampil=mysqli_query($cnmy, "SELECT * FROM dbmaster.sdm_menu where parent_id = 0 ORDER BY urutan");
                while ($r=mysqli_fetch_array($tampil)){
                  echo "<tr><td>$r[URUTAN]</td>
                        <td>$r[JUDUL]</td>
                        <td><a href=$r[URL]>$r[URL]</a></td>
                        <td>$r[PUBLISH]</td>
                        <td>$r[KRITERIA]</td>
                        <td>$r[URUTAN]</td>
                        <td width='150'><a class='btn btn-primary' href=?module=menuutama&act=editmenu&id=$r[ID]>Edit</a> &nbsp;
                        <a class='btn btn-danger btn-sm' href=\"$aksi?module=$_GET[module]&act=hapus&id=$r[ID]&idmenu=$_GET[idmenu]\"
                                        onClick=\"return confirm('Apakah Anda benar-benar akan menghapusnya?')\">Hapus</a>";

                   echo "</td></tr>";
                }
                echo "</tbody></table>";

            echo "</div>";//end panel

        echo "</div>";
    break;

  case "tambahmenu":
        echo "<div class='col-md-12 col-sm-12 col-xs-12'>";
            //panel
            echo "<div class='x_panel'>";

                echo "<form method=POST action='$aksi?module=menuutama&act=input'>
                      <table width='100%'>
                      <tr><td>Nama Menu</td> <td> : </td><td><input type=text class='form-control' name='nama_menu'></td></tr>
                      <tr><td>Link</td>       <td> : </td><td><input type=text class='form-control' name='link'></td></tr>
                      
                      <tr><td>Publish</td>    <td> : </td><td><input type='radio' name='publish' id='radio1' value='Y' checked><label for='radio1'>Y</label>
                                                     <input type='radio' name='publish' id='radio2' value='N'><label for='radio2'>N</label></td></tr>

                      <tr><td>Kriteria</td>    <td> : </td><td><input type=radio name='ckriteria' id='kriteria1' value='Y' checked><label for='radio1'>Y</label>
                                                     <input type=radio name='ckriteria' id='kriteria2' value='N'><label for='radio2'>N</label></td></tr>
                                                     
                      <tr><td colspan=3><input type=submit class='btn btn-primary' value=Simpan>
                                        <input type=button class='btn btn-primary' value=Batal onclick=self.history.back()></td></tr>
                      </table></form>";
    
            echo "</div>";//end panel

        echo "</div>";
     break;
 
  case "editmenu":
        echo "<div class='col-md-12 col-sm-12 col-xs-12'>";
            //panel
            echo "<div class='x_panel'>";

                $edit = mysqli_query($cnmy, "SELECT * FROM dbmaster.sdm_menu WHERE id='$_GET[id]'");
                $r    = mysqli_fetch_array($edit);

                

                echo "<form method=POST action=$aksi?module=menuutama&act=update>
                      <input type=hidden name=id value='$r[ID]'>
                      <table width='100%'>

                      <tr><td>Nama Menu</td>     <td> : </td><td><input type=text class='form-control' name='nama_menu' value='$r[JUDUL]'></td></tr>
                      <tr><td>Link</td>     <td> : </td><td><input type=text class='form-control' name='link' value='$r[URL]'></td></tr>";
                if ($r['PUBLISH']=='Y'){
                  echo "<tr><td>Publish</td> <td> : </td><td><input type=radio name='publish' id='radio1' value='Y' checked><label for='radio1'>Y</label>
                                                    <input type=radio name='publish' id='radio2' value='N'><label for='radio2'>N</label></td></tr>";
                }
                else{
                  echo "<tr><td>Publish</td> <td> : </td><td><input type=radio name='publish' id='radio1' value='Y'><label for='radio1'>Y</label>
                                                    <input type=radio name='publish' id='radio2' value='N' checked><label for='radio2'>N</label></td></tr>";
                }
                
                if ($r['KRITERIA']=='Y'){
                  echo "<tr><td>Kriteria</td> <td> : </td><td><input type=radio name='ckriteria' id='kriteria1' value='Y' checked><label for='radio1'>Y</label>
                                                    <input type=radio name='ckriteria' id='kriteria2' value='N'><label for='radio2'>N</label></td></tr>";
                }
                else{
                  echo "<tr><td>Kriteria</td> <td> : </td><td><input type=radio name='ckriteria' id='kriteria1' value='Y'><label for='radio1'>Y</label>
                                                    <input type=radio name='ckriteria' id='kriteria2' value='N' checked><label for='radio2'>N</label></td></tr>";
                }
                
                
                echo "<tr><td>Urutan</td>       <td> : </td><td><input type=text class='form-control' name='urutan' value='$r[URUTAN]'></td></tr>
                      <tr><td colspan=3><input type=submit class='btn btn-primary' value=Update>
                                        <input type=button class='btn btn-primary' value=Batal onclick=self.history.back()></td></tr>
                      </table></form>";

            echo "</div>";//end panel

        echo "</div>";

    break;  
}
?>
    </div>
    <!--end row-->
</div>