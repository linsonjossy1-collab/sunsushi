<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/1999/REC-html401-19991224/strict.dtd">
<html> 
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title><?php echo $title; ?></title>
    </head>
    <body style="font-family:lucida sans unicode,lucida grande,sans-serif; color:#000; padding:10px;">
    <div>
        <p>
            <h3><?php echo $greetings; ?></h3>
            <?php echo $content; ?>                
        </p>
        <hr>
        <p>
            <?php if($owner) { ?>
                <h3>Reservation Details</h3>
                Name : <?php echo $name; ?><br>
                Reservation Time : <?php echo $reservation_time_text; ?><br>
                People : <?php echo $persons; ?><br>
                <?php if($childrens) { ?>
                 Childrens : <?php echo $childrens; ?><br>
                 <?php } ?>
                Phone No : <?php echo $phone; ?><br>
                Email : <?php echo $email; ?><br>
                <?php if($comments) { ?>
                Special instruction :<br><?php echo $comments; ?>
                <?php } ?>
            <?php } else { echo $store_info; } ?>
        </p> 
        Thank you!
        <hr>
        <p><?php echo $footer_content;?></p>
    </div>
    </body>
</html>