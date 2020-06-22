$(document).ready(function($) {
    // pin
    if(sessionStorage.getItem("session")==null) $('#askPinModal').modal('show');
    else {
        $('#created_by').val(sessionStorage.getItem("session_id"));
        $('.current-staff').text(sessionStorage.getItem("session_name"));
    }
    $('#security_pin_btn').on('click', function(){
        var secPin = $('#security_pin_input').val();
        if (secPin != '') {
            $.ajax({
                type: "get",
                data: {
                    secPin: secPin
                },
                url: base_url + "pos/validate_sec_pin",
                dataType: "json",
                success: function (t) {
                    if (t.success) {
                        $('#hidden_sec_pin').val(t.secPin)
                        current_staff = t.staff;
                        // console.log(current_staff);
                        $('.current-staff').text(current_staff.first_name + ' ' + current_staff.last_name);
                        sessionStorage.setItem("session", current_staff.password);
                        sessionStorage.setItem("session_name", current_staff.first_name + ' ' + current_staff.last_name);
                        sessionStorage.setItem("session_id", current_staff.id);
                        $('#session_user').val(current_staff.id);
                        $('#created_by').val(current_staff.id);
                        $('#askPinModal').modal('hide');
                    } else {
                        bootbox.alert(lang.no_match_found);
                    }
    
                },
                error: function () {
                    bootbox.alert(lang.no_match_found);
                    return false;
                }
            });
    
        } else {
            alert('Please enter security Pin');
        }
    
    });
    
    $('#ticket_number_').select2({
        ajax: {
        url: function (params) {
            if(params.term==undefined) return base_url + 'tag/searchPickupCustomer?search=';
            else return base_url + 'tag/searchPickupCustomer?search=' + params.term;
        },
        dataType: 'json',
        delay: 0,
        data: function (params) {
            return {
                q: params.term
            };
        }
        },
        templateResult: formatRepo,
        templateSelection: formatState
    });
    
    // $('#select2-ticket_number-container span').text('Please input ticket number');
    $('#ticket_number').val('');

    $('#spos_customer').focus();

    $('#rack_number').on('keypress', function(e) {
        if(e.which == 13) {
            if ($('#rack_number').val() !== '') {
                $('#ticket_number').focus();
            }
        }
    })

    $('#ticket_number').on('keypress', function(e) {
        if(e.which == 13) {
            if ($('#ticket_number').val() !== '') {
                rankingSubmit()
            }
        }
    })

    // racking

    $('#rack_button').click(function(e) {
        e.preventDefault();
        rankingSubmit()
    });
});



function formatRepo (repo) {
    if (repo.loading) {
        return repo.text;
    }
    var $container = $(
        "<div style='display:flex; justify-content:space-between;'><span> " + repo.id + "</span><span>"+repo.text + " (" + repo.phone+ ")" +"</span></div>"
    );
    return $container;
}

function formatState (repo) {
    if (!repo.id) {
      return repo.text;
    }
    var $state = $(
      "<div style='display:flex; justify-content:space-between;'><span>" + repo.id + "</span></div>"
    );
  
    return $state;
}

function rankingSubmit() {
    if ($('#rack_number').val() == '') $('#rack_number').focus();
    // else if ($('#ticket_number').val() == '') $('#select2-ticket_number-container span').addClass('required');
    else if ($('#ticket_number').val() == '') $('#ticket_number').focus();
    else {
        // $('#select2-ticket_number-container span').removeClass('required');
        var session_id = sessionStorage.getItem("session_id")
        $('#rack_button').focus();
        var form = $('#rack-save-form').serialize();
        $.post(base_url + 'rack/p/order', form + session_id).done(
            function(res) {
                console.log(res)
                // res = JSON.parse(result)
                // customer_ticket = res[0].ticket
                // type_phone = res[0].kind_phone
                // phone_number = res[0].phone
                var meg = "";
                if (res) {
                    // if (type_phone === 'cellphone') {
                        // console.log(type_phone)
                        // customer_ticket, phone_number
                    // } else if (type_phone === 'voicephone') {
                        // console.log(type_phone)
                        // customer_ticket, phone_number
                    // }
                    meg = "Successfully added rack."
                    $('#rack_number').val('');
                    $('#ticket_number').val('');
                    // $('#select2-ticket_number-container span').text('Please input ticket number');
                } else meg = "Failed added rack.";
                $('.alert span').text(meg);
                $('.alert').slideDown();
                window.setTimeout(function() {
                    $('.alert').slideUp();
                }, 1000);
                $('.alert').on('click', function(e) {
                    $(this).slideUp();
                });
            }
        );
    }
    return false;
}