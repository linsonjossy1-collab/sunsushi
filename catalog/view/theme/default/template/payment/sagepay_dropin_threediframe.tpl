<?php echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n"; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="<?php echo $direction; ?>" lang="<?php echo $language; ?>"
	  xml:lang="<?php echo $language; ?>">
<head>
	<title><?php echo $title; ?></title>
	<base href="<?php echo $base; ?>"/>
</head>
<body>
<div style="text-align: center;">
	<h1><?php echo $heading_title; ?></h1>

        <iframe src="<?php echo $action; ?>" name="3Diframe">
        <form action="<?php echo $acsUrl; ?>" method="post">
            <input type="hidden" name="creq" value="<?php echo $creq; ?>" />
            <input type="hidden" name="threeDSSessionData" value="<?php echo $threeDSSessionData; ?>" />
            <p>Please click button below to proceed to 3D secure.</p>
            <input type="submit" value="Go"/>
        </form>
    </iframe>
</div>
</body>
</html>