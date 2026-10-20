<?php echo $header; ?><?php echo $content_top; ?>
<div class="bgclr000" id="content">
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <!--<h1><span id="head-title"><?php echo $heading_title; ?></span></h1>-->
    <div class="contact-info">
      <div class="content"> 
        <!--<h4 style="text-align: center; background-color: whitesmoke; padding: 10px;"><?php echo $text_location; ?></h4>-->
        
        <div class="col-sm-12" style="padding-bottom: 20px;">
          <div class="col-sm-12" style="margin-bottom: 15px; text-align: center;">
            <h4 style="text-align: center;"><?php echo $text_address; ?></h4>
            <?php echo $store; ?><br />
            <?php echo $address; ?> <br />
            <?php echo $postalcode ; ?> <br />
            <br />
            <?php if($contact_email) { ?>
            <strong>Email:</strong><?php echo $contact_email; ?><br />
            <?php } ?>
            <br />
            <?php if ($telephone) { ?>
            <strong><?php echo $text_telephone; ?></strong> <?php echo $telephone; ?>
            <?php } ?>
            <?php if ($fax) { ?>
            / <b>
            <?php //echo $text_fax; ?>
            </b><?php echo $fax; ?>
            <?php } ?>
          </div>

                <div class="col-sm-12 opening-time">
                <h4 style="text-align: center;">Opening Hours</h4>
        <ul>
          <?php foreach($store_times as $day=>$store_time){ ?>
          <li> <span class="home_day"><?php echo $day; ?></span>
            <?php if($store_time) { ?>
            <?php $d=1; ?>
            <?php foreach($store_time as $time){ ?>
            <span class="home_time"><?php echo $time['start'].' - '.$time['end']; ?></span>
            <?php if($d<count($store_time)){ ?>
            <span id="time_line">|</span>
            <?php } ?>
            <?php $d++; ?>
            <?php } ?>
            <?php } else { ?>
            <span class="home_time">Closed</span>
            <?php } ?>
          </li>
          <?php } ?>
        </ul>
      </div>

          <?php echo $content_bottom; ?> </div>
      </div>
    </div>
</div>
<?php echo $footer; ?>