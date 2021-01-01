function getDataKaryawan(data1, data2){
    $.ajax({
        type:"post",
        url:"config/viewdata.php?module=viewkaryawan&data1="+data1+"&data2="+data2,
        data:"udata1="+data1+"&udata2="+data2,
        success:function(data){
            $("#myModal").html(data);
        }
    });
}

function getDataModalKaryawan(fildnya1, fildnya2, d1, d2){
    document.getElementById(fildnya1).value=d1;
    document.getElementById(fildnya2).value=d2;
}

function convertToRP(angka) {
    var rupiah = '';
    var angkarev = angka.toString().split('').reverse().join('');
    for (var i = 0; i < angkarev.length; i++) if(i%3 == 0) rupiah += angkarev.substr(i, 3)+'.';
    return rupiah.split('',rupiah.length-1).reverse().join('');
}

