<div id="login">
  <h2><?php echo $text_returning_customer; ?></h2>
 <!-- <p><?php echo $text_i_am_returning_customer; ?></p> -->
 
  <div class="form-group">
    <label class="col-lg-4 control-label">
        <?php echo $entry_email; ?>
    </label>
    <div class="col-lg-7">
      <input type="text" name="email" value="" id="login_email" />
    </div>
  </div>

  <div class="form-group">
    <label class="col-lg-4 control-label">
        <?php echo $entry_password; ?>
    </label>
    <div class="col-lg-7">
      <input type="password" name="password" value="" />
    </div>
  </div>

  <br>
  <div class="form-group">
    <input type="button" value="<?php echo $button_login; ?>" id="button-login" class="button" />  <br>
    <span class="addresstext"></span><a href="<?php echo $forgotten; ?>"><?php echo $text_forgotten; ?></a><br />
    <span class="addresstext"></span><input type="checkbox" name="remember_me" id="login-remember" value="1">Remember Me
  </div>
</div>