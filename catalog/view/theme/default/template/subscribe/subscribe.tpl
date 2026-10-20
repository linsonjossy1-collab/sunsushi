<?php echo $header; ?>
  <div class="container">
    <h2></h2>
    <div>
      <div class="col-md-3"></div>
      <div class="col-md-6 panel panel-default" style="margin-top: 10%;">
        <div class="panel-body">
          <?php
            if($update_status )
            { 
          ?>
              <h2><?= $subscription_msg ?></h2>
          <?php
            }
            else
            {
          ?>  
            <h4><?= $subscription_msg ?></h4>                
            <form id="unsubscribe-form" method="post" action="<?=$action?>" class="form-inline">
              <div class="form-group pull-right">
                <input type="email" class="form-control" id="unsubscribe-email" placeholder="Enter email"  name="email" required>
                <input type="button" id="unsubscribe-btn" class="btn btn-danger" name="unsubscribe" value="Unsubscribe">
              </div>             
            </form> 
          <?php
            }
          ?>
        </div>
      </div>
      <div class="col-md-3"></div>     
    </div>
  </div>
<?php echo $footer; ?>
  <script>
    $('#unsubscribe-btn').click(function(){
        var email = $('#unsubscribe-email').val();
        if (validateEmail(email)) {
            $('#unsubscribe-form').submit();
        }
        else {
          alert('Invalid Email Address');
          e.preventDefault();
        }
    });
    function validateEmail(email) {
      var filter = /^(([^<>()\[\]\.,;:\s@\".]+(\.[^<>()\[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i;
      
      if (filter.test(email)) {
        return true;
      }
      else {
        return false;
      }
    }
  </script>
