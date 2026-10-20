<?php echo $header; ?>
<script type="text/javascript" src="catalog/view/javascript/datetimepicker/datepicker.min.js"></script>
<script type="text/javascript" src="catalog/view/javascript/datetimepicker/datepicker.en.js"></script>
<link rel="stylesheet" type="text/css" href="catalog/view/theme/default/stylesheet/datetimepicker/datepicker.min.css" media="screen" />
<?php echo $column_left; ?><?php echo $column_right; ?>
<div id="content"><?php echo $content_top; ?>
<script>
        function tablevalidator() {
            obj = document.forms["tablefrm"];
            $('#formdesign .error').remove();
            re = /^\d{1,2}\/\d{1,2}\/\d{4}$/;
            if (obj.arrival_date.value == '' || !obj.arrival_date.value.match(re)) {
                alert("please select arrival date");
                obj.arrival_date.focus();
                return false;

            }
            if (obj.reservation_time.value =='' || obj.reservation_time.value==0) {
                alert("please select time");
                obj.reservation_time.focus();
                return false;
            }
            if (obj.name.value == "") {
                alert("please enter your name");
                obj.name.focus();
                return false;
            }
            if (obj.persons.value == "") {
                alert("please choose number of adults");
                obj.persons.focus();
                return false;
            }
            if (obj.phone.value == "") {
                alert("please enter your phone number");
                obj.phone.focus();
                return false;
            }
            if ((isNaN(obj.phone.value) || obj.phone.value.length < 11)) {
                alert("UK telephone numbers contain 11 digits start with 0");
                obj.phone.focus();
                return false;

            }
            if (obj.email.value == "") {
                alert("please enter your email address");
                obj.email.focus();
                return false;
            }
            var emailExp = /^[\w\-\.\+]+\@[a-zA-Z0-9\.\-]+\.[a-zA-z0-9]{2,4}$/;
            if (!obj.email.value.match(emailExp)) {
                alert("Invalid Email Address.");
                obj.email.focus();
                return false;
            }

            $.ajax({
                url: 'index.php?route=information/tablereservation/validate',
                data:$('#frm').serialize(),
                type:'post',
                dataType: 'json',
            }).done(function(json) {
                if(json['redirect']!=undefined) {
                    window.location.href = json['redirect'];
                } else { 
                     for(var key in json) {
                        $('#'+key).after('<span class="error">'+json[key]+'</span>');
                    }
                    if(json['captcha']!=undefined) {
                        $('#img_captcha').html('<img src="index.php?route=information/tablereservation/captcha"/> <div class="error">Captcha not matching!</div>'); 
                    }      
                    $('#formdesign').prepend('<span class="error">Please check form carefully.</span>');
                }   
            }).fail(function() {
                console.log('Error occured');
            });  
        }  
        $(document).ready(function() {
            var dateToday = new Date();
            $("#arrival_date").datepicker({
                minDate: new Date(),
                language: 'en',
                dateFormat: 'dd/mm/yyyy',
                onSelect: function(date) {
                    $('#arrival_date').datepicker().data('datepicker').hide();
                    loadTime(date);
                }
            }).on('change',function() {
                loadTime($(this).val());
            });
        });
        
        function loadTime(seldate) {
            if(!seldate) {
                return false;
            }
            if(!seldate.match(/^\d{1,2}\/\d{1,2}\/\d{4}$/)) {
                $('#arrival_date').val('');
                return false;
            }
            myDate = seldate.split("/");
            seldate = myDate[0] + "-" + myDate[1] + "-" + myDate[2];
            $('#arrival_date').datepicker().data('datepicker').hide();
            $.ajax({
                url: 'index.php?route=information/tablereservation/getstoretimes',
                data:{date:seldate},
                dataType: 'json'
            }).done(function(json) {
                if(json['message']!=undefined) {
                    alert(json['message']);
                    $('#reservation_time').html('');
                     $('#arrival_date').val('');
                } else {
                    $('#arrival_date').datepicker().data('datepicker').hide();
                    var html = '';
                    for (var key in json['data']) {
                        if (json['data'].hasOwnProperty(key)) {
                            html+="<option value='"+key+"'>"+json['data'][key]+"</option>";
                        }
                    }
                    $('#reservation_time').html(html);
                }
            }).fail(function (jqXHR, textStatus) {
                console.log(textStatus);
            });
        } 
    </script>
    
  <div id="formdesign">
    <div class="container">
    <div class="row">
    
    <div class="col-sm-12" id="table_form">
        <form action="<?php echo $action; ?>" enctype="multipart/form-data" id="frm" method="post" name="tablefrm">
 <h2>Make a Reservation</h2>
 <div class="col-sm-12"><input id="name" name="name" placeholder="Name" type="text" value="" />
        <?php if ($error_name) { ?>
        <span class="error"><?php echo $error_name; ?></span>
        <?php } ?></div>
        <div class="col-sm-6"><input id="email" name="email" placeholder="Email" type="text" value="" />
        <?php if ($error_email) { ?>
                            <span class="error"><?php echo $error_email; ?></span><br />
                            <?php } ?>
        </div>
         <div class="col-sm-6"><input id="phone" name="phone" placeholder="Phone" type="text" value="" />
         <?php if ($error_phone) { ?>
                            <span class="error"><?php echo $error_phone; ?></span><br />
                            <?php } ?>
        </div>
         <div class="col-sm-12"><select id="AdultsRoom" name="persons" size="1"><option value="1">1 person</option><option selected="selected" value="2">2 people</option><option value="3">3 people</option><option value="4">4 people</option><option value="5">5 people</option><option value="6">6 people</option><option value="7">7 people</option><option value="8">8 people</option><option value="9">9 people</option><option value="10">10 people</option><option value="11">11 people</option><option value="12">12 people</option><option value="13">13 people</option><option value="14">14 people</option><option value="15">15 people</option><option value="16">16 people</option><option value="17">17 people</option><option value="18">18 people</option><option value="19">19 people</option><option value="20">20 people</option></select></div>
        <div class="col-sm-6"><input id="arrival_date" name="arrival_date" type="text" placeholder="Date" autocomplete="off"/></div>
        <div class="col-sm-6"><select id="reservation_time" name="reservation_time" size="1"><option value="0">Time</option></select></div>
       
                            
        
                                
       
                                
        
        <div class="col-sm-12"><textarea name="comments" class="form-control" placeholder="Comments"></textarea></div>
        <div class="col-sm-12">
            <?php if($grecaptcha) {
             echo $grecaptcha; } else { ?>
            <input type="text" name="captcha" placeholder="Enter the code: *" />
            <img src="index.php?route=information/tablereservation/captcha" alt="" />
            <?php } ?>
            <div id='error_captcha'></div>
        </div>
        <div id="reservation_btn"><input class="button blacklink5" name="Submit" src="/image/data/reserve_button.png" type="button" value="RESERVE NOW"  onclick="tablevalidator()"/> </div>
        </form>
        </div>
        
        
        </div>
        </div>
    </div>  
    <?php echo $content_bottom; ?></div>
<?php echo $footer; ?>