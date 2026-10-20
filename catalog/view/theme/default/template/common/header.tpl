<!DOCTYPE html>
<html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>">
<head>

<script type="text/javascript">
    (function(c,l,a,r,i,t,y){
        c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
        t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
        y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
    })(window, document, "clarity", "script", "yxjzocuqd8");
</script>

<!-- Google Tag Manager -->

<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-PLG94DSH');</script>
<!-- End Google Tag Manager -->

<script defer id="Cookiebot" src="https://consent.cookiebot.com/uc.js" data-cbid="40f22282-80a4-4fab-a48b-1d12d743d77b"  type="text/javascript"></script>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1"> 
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<!--<meta name="facebook-domain-verification" content="2mr0weSh0jxwvaumyhi8j5470k3y21" />-->

<base href="<?php echo $base; ?>" />

<?php
$request_uri = strtolower(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
?>

<!-- HOMEPAGE -->
<?php if ($request_uri == '/' || $request_uri == '/home') { ?>

<title>SunSushi | Sushi & Japanese Food in Northampton</title>
<meta name="description" content="Enjoy fresh sushi, Japanese food, ramen, wok dishes, rice, curry, bubble tea and more at SunSushi in Northampton. Order online for delivery or collection.">
<meta name="keywords" content="SunSushi, Sun Sushi Northampton, sushi Northampton, Japanese restaurant Northampton, sushi takeaway Northampton, Japanese food Northampton, sushi delivery Northampton, takeaway Northampton">
<link rel="canonical" href="https://www.sunsushi.co.uk/home" />

<!-- MENU -->
<?php } elseif ($request_uri == '/menu' || strpos($request_uri, '/menu/') === 0) { ?>

<title>SunSushi Menu | Sushi & Japanese Food in Northampton</title>
<meta name="description" content="Explore the SunSushi menu in Northampton featuring sushi sets, rolls, maki, nigiri, sashimi, hot sushi, ramen, wok dishes, rice, curry, bento boxes, bubble tea and more.">
<meta name="keywords" content="SunSushi menu, sushi menu Northampton, Japanese menu Northampton, sushi rolls Northampton, nigiri Northampton, sashimi Northampton, ramen Northampton, Japanese takeaway menu">
<link rel="canonical" href="https://www.sunsushi.co.uk/menu" />

<!-- CONTACT -->
<?php } elseif ($request_uri == '/contact' || strpos($request_uri, '/contact/') === 0) { ?>

<title>Contact SunSushi | Sushi Restaurant in Northampton</title>
<meta name="description" content="Contact SunSushi at 75 Saint Leonards Road, Northampton. Find our address, telephone number, opening hours and information for delivery and collection.">
<meta name="keywords" content="SunSushi contact, Sun Sushi Northampton contact, sushi Northampton contact, Japanese restaurant Northampton, SunSushi address, SunSushi phone number">
<link rel="canonical" href="https://www.sunsushi.co.uk/contact" />

<!-- TERMS & CONDITIONS -->
<?php } elseif ($request_uri == '/terms-and-conditions' || strpos($request_uri, '/terms-and-conditions/') === 0) { ?>

<title>Terms & Conditions | SunSushi Northampton</title>
<meta name="description" content="Read the terms and conditions for using the SunSushi website, online ordering service and related services.">
<meta name="keywords" content="SunSushi terms and conditions, Sun Sushi terms, SunSushi Northampton terms">
<link rel="canonical" href="https://www.sunsushi.co.uk/terms-and-conditions" />

<!-- PRIVACY POLICY -->
<?php } elseif ($request_uri == '/privacy-policy' || strpos($request_uri, '/privacy-policy/') === 0) { ?>

<title>Privacy Policy | SunSushi Northampton</title>
<meta name="description" content="Read the SunSushi privacy policy to learn how personal information is collected, used and protected when using our website and ordering services.">
<meta name="keywords" content="SunSushi privacy policy, Sun Sushi privacy, SunSushi Northampton privacy">
<link rel="canonical" href="https://www.sunsushi.co.uk/privacy-policy" />

<!-- COOKIE POLICY -->
<?php } elseif ($request_uri == '/cookie-policy' || strpos($request_uri, '/cookie-policy/') === 0) { ?>

<title>Cookie Policy | SunSushi Northampton</title>
<meta name="description" content="Learn how SunSushi uses cookies on its website and how you can manage or control cookies in your browser.">
<meta name="keywords" content="SunSushi cookie policy, Sun Sushi cookies, SunSushi Northampton cookies">
<link rel="canonical" href="https://www.sunsushi.co.uk/cookie-policy" />

<!-- SERVICE DISCLAIMER -->
<?php } elseif ($request_uri == '/service-disclaimer' || strpos($request_uri, '/service-disclaimer/') === 0) { ?>

<title>Service Disclaimer | SunSushi Northampton</title>
<meta name="description" content="Read the SunSushi service disclaimer covering the use of the website, interactive services, information and online services.">
<meta name="keywords" content="SunSushi service disclaimer, Sun Sushi disclaimer, SunSushi Northampton disclaimer">
<link rel="canonical" href="https://www.sunsushi.co.uk/service-disclaimer" />

<!-- REFUND POLICY -->
<?php } elseif ($request_uri == '/refund-policy' || strpos($request_uri, '/refund-policy/') === 0) { ?>

<title>Refund Policy | SunSushi Northampton</title>
<meta name="description" content="Read the SunSushi refund policy covering refunds and order cancellations for food orders placed through the website.">
<meta name="keywords" content="SunSushi refund policy, Sun Sushi refund, SunSushi Northampton refund, sushi takeaway refund">
<link rel="canonical" href="https://www.sunsushi.co.uk/refund-policy" />

<!-- LOGIN -->
<?php } elseif ($request_uri == '/login') { ?>

<title>Login | SunSushi</title>
<meta name="description" content="Login to your SunSushi account to manage your orders and account details.">
<meta name="robots" content="noindex, nofollow">
<link rel="canonical" href="https://www.sunsushi.co.uk/login" />

<!-- REGISTER -->
<?php } elseif ($request_uri == '/register') { ?>

<title>Create Account | SunSushi</title>
<meta name="description" content="Create your SunSushi account to order online faster and manage your orders and account details.">
<meta name="robots" content="noindex, nofollow">
<link rel="canonical" href="https://www.sunsushi.co.uk/register" />

<!-- FALLBACK -->
<?php } else { ?>

<title>SunSushi | Sushi & Japanese Food in Northampton</title>
<meta name="description" content="SunSushi in Northampton serves fresh sushi, Japanese food, ramen, wok dishes, rice, curry, bubble tea and more. Order online for delivery or collection.">
<meta name="keywords" content="SunSushi, Sun Sushi Northampton, sushi Northampton, Japanese food Northampton, sushi takeaway Northampton, Japanese takeaway Northampton">
<link rel="canonical" href="https://www.sunsushi.co.uk<?php echo $request_uri; ?>" />

<?php } ?>


<?php if ($keywords) { ?>
<meta name="keywords" content="<?php echo $keywords; ?>" />
<?php } ?>
<?php if ($icon) { ?>
<link href="<?php echo $icon; ?>" rel="icon" />
<?php } ?>
<?php foreach ($links as $link) { ?>
<link href="<?php echo $link['href']; ?>" rel="<?php echo $link['rel']; ?>" />
<?php } ?>

<link rel="preload"
      href="/imgd/sunsushilogo.webp"
      as="image"
      type="image/webp"
      fetchpriority="high">


<link rel="preload"
      href="/scss/stylesheets.css"
      as="style"
      onload="this.onload=null;this.rel='stylesheet'">

<noscript>
<link rel="stylesheet"
      href="/scss/stylesheets.css">
</noscript>
<?php foreach ($styles as $style) { ?>
<link rel="<?php echo $style['rel']; ?>" type="text/css" href="<?php echo $style['href']; ?>" media="<?php echo $style['media']; ?>" />
<?php } ?>


<link rel="preload"
      href="catalog/view/javascript/jquery/jquery-3.3.1.min.js"
      as="script">

<script type="text/javascript"
        src="catalog/view/javascript/jquery/jquery-3.3.1.min.js"></script>


<!--<script type="text/javascript" src="catalog/view/javascript/jquery/ui/external/jquery.cookie.js"></script>-->
<script type="text/javascript" src="catalog/view/javascript/common.js" defer></script>

<?php if ($route == 'common/home') { ?>
<script type="text/javascript" src="catalog/view/javascript/jquery/jquery.jcarousel.min.js" defer></script>
<link rel="stylesheet" type="text/css" href="catalog/view/theme/default/stylesheet/carousel.css" media="screen" />
<?php } ?>



<?php foreach ($scripts as $script) { ?>
<script type="text/javascript" src="<?php echo $script; ?>"></script>
<?php } ?>


<link rel="preload"
      href="catalog/view/theme/default/stylesheet/css/bootstrap.min.css"
      as="style"
      onload="this.onload=null;this.rel='stylesheet'">

<noscript>
    <link rel="stylesheet"
          href="catalog/view/theme/default/stylesheet/css/bootstrap.min.css">
</noscript>

<script type="text/javascript" src="catalog/view/javascript/bootstrap.min.js" defer></script>


<link rel="preload"
      href="/catalog/view/theme/default/stylesheet/font-awesome.min.css"
      as="style"
      onload="this.onload=null;this.rel='stylesheet'">

<noscript>
    <link rel="stylesheet"
          href="/catalog/view/theme/default/stylesheet/font-awesome.min.css">
</noscript>

<!--<script type="text/javascript" src="catalog/view/javascript/css3-mediaqueries.js"></script>-->


<link rel="preload"
      href="catalog/view/theme/default/stylesheet/bootstrap-select.min.css"
      as="style"
      onload="this.onload=null;this.rel='stylesheet'">

<noscript>
    <link rel="stylesheet"
          href="catalog/view/theme/default/stylesheet/bootstrap-select.min.css">
</noscript>

<script type="text/javascript" src="catalog/view/javascript/bootstrap-select.min.js" defer></script> 
</head>
<body class="<?php echo $title ?>">

<!-- Google Tag Manager (noscript) -->

<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-PLG94DSH"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>

<!-- End Google Tag Manager (noscript) -->


<?php if($this->config->get('config_notlive_banner')) { ?>
<div class="siteNotLive">
  <p><?php echo  ($this->config->get('config_notlive_banner_message') && $this->config->get('config_notlive_banner_message')!='')?$this->config->get('config_notlive_banner_message'):'THIS SITE IS NOT LIVE YET. THE INFORMATION PROVIDED IS SUBJECT TO REVIEW'; ?></p>
</div>
<?php } ?>
<div ><?php if(isset($shadow_menu)){ echo $shadow_menu; } ?></div>


<header>
  <div id="header">
  <div class="container">
    <div class="row">
    


          <div id="logo">
<a href="<?php echo $home; ?>">
          <picture>
    <source srcset="<?php echo str_replace(array('.png','.jpg','.jpeg'), '.webp', $logo); ?>" type="image/webp">
    <img src="<?php echo $logo; ?>" title="<?php echo $name; ?>" alt="<?php echo $name; ?>" class="img-responsive" width="180" height="63" fetchpriority="high">
</picture></a>


          </div>
       <!-- <div id="welcome">
          <?php if (!$logged) { ?>
          <?php echo $text_welcome; ?>
          <?php } else { ?>
          <?php echo $text_logged; ?>
          <?php } ?>
        </div>-->
    
    <div id="myNav" class="overlay">
  <button type="button" class="closebtn" onclick="closeNav()" aria-label="Close navigation">
    &times;
</button>
  <div class="overlay-content">
    <a href="/home">Home</a>
    <a href="/menu">Menu</a>
    <a href="/delivery-policy">Delivery Policy</a>
    <a href="/login">My Account</a>
    <a href="/contact">Contact</a>
  </div>
</div>

<span style="cursor:pointer;    padding: 0px 15px;" onclick="openNav()"><img src="/image/data/line2.png" class="img-responsive hamburgr" alt="hamburger" title="hamburger" /></span>


    </div>

      </div>
      </div>
  </header>

<a href="/cartetm" class="chketm"><span class="count"><?php echo $itemcount; ?></span><span class="vwbasket">View Basket</span><span class="totalamt">£<?php echo $totalamount; ?></span></a>
<div class="container-fluid">
<div class="row" id="nomarg">
<div id="content" class="col-sm-12 for_border">
<div id="notification"></div>
<?php if(isset($account_coupon_messege)) { ?>
<div id="coupon_notification"> <?php echo $account_coupon_messege; ?> </div>
<script type="text/javascript">
    $(document).ready(function(){
    $('#coupon_notification').dialog();
    }
);
</script>
<?php } ?>
<script type="text/javascript">
  $(document).ready(function() {
<?php if($loginstatus==0){ ?> 
    //alert("sss");
    if(typeof(Storage)!=="undefined" && typeof(localStorage['a1_s1_gkl_'])!=="undefined")  {
        //alert("ss");
        $.ajax({
		url: 'index.php?route=account/account/custredatsav',
		type: 'post',
		data:{ a1_s1_gkl_:localStorage['a1_s1_gkl_' ] } ,
		dataType: 'json',
                success:function(json){
                
                }
        });	        
    }  
<?php } ?>   
});
  </script> 
<script>
    $(document).ready(function() {
        jQuery.expr[':'].contains = function(a, i, m) {
            return jQuery(a).text().toUpperCase().indexOf(m[3].toUpperCase()) >= 0;
        };
        $('#searchbtn').on('click',function() {
         searchproduct();
        });
        $("#searchbtn").on("input", function(){
        // Print entered value in a div box
        if($(this).val()==''){
            searchproduct('e');
        }
        
    });
    });

    function searchproduct(e) {
        if(e!=undefined){
            $('#search_product').val('');
        }
        var value = $('#search_product').val();
          if(value) {
            if($('#menutab').length > 0) {
              $("#menutab .prod_main_categories").hide(); 
              $("#menutab .product-info_simple").hide();            
              $('#menutab .product_name:contains("'+value+'")').parents('.product-info_simple').show();
              $('#menutab .product_name:contains("'+value+'")').parents('.prod_main_categories').show();
            }
          } else {      
            $("#menutab .prod_main_categories").show();
            $("#menutab .product-info_simple").show();
          }
    }
    function checkitemisincart(){
        //alert("check Item");
        //$('.addtocartnb').show();
        //console.log("dggdgd");
        <?php foreach($incart as $id=>$data){ ?>
                  // console.log("mnnnnsnss");
           var qty = "<?php echo $data['qty'] ?>";
           var key = "<?php echo $data['key'] ?>";
           var id = "<?php echo $id ?>";
                $('.productdatas-'+id+' .addtocartnb').hide();
                $('.productdatas-'+id+'  .equiliserpropage').show();
                $('.productdatas-'+id+'  .quantity').html(qty);
                $('.productdatas-'+id+' .remove').html('<div class="fa fa-minus-circle" title="-1" onclick="removeditem('+"'"+key+"'"+','+qty+','+id+');"></div>');
             
        <?php } ?>
    }   
  </script>
  <script>
function openNav() {
  document.getElementById("myNav").style.height = "100%";
}

function closeNav() {
  document.getElementById("myNav").style.height = "0%";
}
</script>