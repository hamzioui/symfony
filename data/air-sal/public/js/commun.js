$(document).ready(function(){

    $('#reservation_Salle').on('change',function(){
        console.log("toto");
        $.ajax({
            url : "/salle/"+ $(this).val() +"/prestations",
            type : 'GET',
            success : function(datas, statut){
                console.log(datas);
            }
        });
    });


    function printData()
    {
        const divToPrint=document.getElementById("printable");
        newWin= window.open("");
        newWin.document.write(divToPrint.outerHTML);
        newWin.print();
        newWin.close();
    }

    $('#print').on('click',function(){
        printData();
    });


});