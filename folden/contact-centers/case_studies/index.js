

$(document).ready(function() {
    $('#submitBtn').click(function() {
        
        let e_mail=$('#email').val();
        let e_firstname=$('#firstname').val();
        let e_lastname=$('#lastname').val();
        let e_phone=$('#phone').val();
        let e_addres=$('#company').val();
        let e_country=$('.default_style option:selected').html();
        // var currLoc = $(location).attr('href'); 
        // console.log(currLoc );
        $.ajax({
            type:'get',
            data:{email:e_mail, company: e_addres, firstname: e_firstname, lastname:e_lastname, phone:e_phone, country:e_country},
            url:'mail.php',
            datatype:'json',
            success: function(res) {
               console.log(res);
            }
        });
        // $this->load->view($this->theme.'auth/reset_password', $this->data);
    });
});