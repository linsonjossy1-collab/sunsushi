
<?php echo $header; ?>
            <!--
            <script type="text/javascript" src="catalog/view/javascript/jquery/colorbox/jquery.colorbox.js"></script>
            <script type="text/javascript" src="catalog/view/javascript/jquery/colorbox/jquery.colorbox-min.js"></script>
            <link href="catalog/view/javascript/jquery/colorbox/colorbox.css" rel="stylesheet" type="text/css">
            -->
<body class="Gallery" >
            <?php echo $column_left; ?><?php echo $column_right; ?>

<div id="content"><?php echo $content_top; ?>
  <!--<div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <h1><?php echo $heading_title; ?></h1>-->
  <div id="main_gallery_div" class="etm_gallery">
      <div id="etm_gallery_albums">
          <?php //print_r($gallery_albums);?>
          <?php if(!empty($gallery_albums)){ ?>
          <?php if(count($gallery_albums)>1){ ?>
          <?php foreach($gallery_albums as $album) { ?>
          <div id="album_img_<?php echo $album['album_id']?>" class="album_notfn_img">
           <div class=""><a class= "album_show" id="<?php echo $album['album_id']?>"><img src="<?php echo $album['thumb_img']; ?>" alt="<?php echo $album['album_name']; ?>" /></a></div>
          </div>
          <?php } ?>
          <?php } ?>
          <?php } ?>
      </div>
      <div class="etm_album_images ">
          
      </div>
  </div>
   </div>
  <?php echo $content_bottom; ?></div>
<?php echo $footer; ?>
<style>




#main_gallery_div .row > .column {
  padding: 0 8px;
}

#main_gallery_div .row:after {
  content: "";
  display: table;
  clear: both;
}

#main_gallery_div .column {
  float: left;
  width: 25%;
}

/* The Modal (background) */
.modal {
  display: none;
  position: fixed;
  z-index: 1;
  padding-top: 100px;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  overflow: auto;
  background-color: rgba(52, 117, 173, 0.9);
}

/* Modal Content */
.modal-content {
  position: relative;
  background-color: #fefefe;
  margin: auto;
  padding: 0;
  width: 90%;
  max-width: 1200px;
      box-shadow: unset;
    border: unset;
}

/* The Close Button */
.close {
  color: white;
  position: absolute;
  top: 10px;
  right: 25px;
  font-size: 35px;
  font-weight: bold;
}

.close:hover,
.close:focus {
  color: #999;
  text-decoration: none;
  cursor: pointer;
}

.mySlides {
  display: none;
}

.cursor {
  cursor: pointer;
}

/* Next & previous buttons */
.prev,
.next {
  cursor: pointer;
  position: absolute;
  top: 50%;
  width: auto;
  padding: 16px;
  margin-top: -50px;
  color: white;
  font-weight: bold;
  font-size: 20px;
  transition: 0.6s ease;
  border-radius: 0 3px 3px 0;
  user-select: none;
  -webkit-user-select: none;
}

/* Position the "next button" to the right */
.next {
  right: 0;
  border-radius: 3px 0 0 3px;
}

/* On hover, add a black background color with a little bit see-through */
.prev:hover,
.next:hover {
  background-color: rgba(0, 0, 0, 0.8);
}

/* Number text (1/3 etc) */
.numbertext {
  color: #f2f2f2;
  font-size: 12px;
  padding: 8px 12px;
  position: absolute;
  top: 0;
}

img {
  margin-bottom: -4px;
}

.caption-container {
  text-align: center;
  background-color: black;
  padding: 2px 16px;
  color: white;
}

.demo {
  opacity: 0.6;
}

.active,
.demo:hover {
  opacity: 1;
}

img.hover-shadow {
  transition: 0.3s;
}

.hover-shadow:hover {
  box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
}
</style>
</body>
<script type="text/javascript">
$(document).ready(function(){
 $('.album_show').click(function(){
    //alert($(this).attr('id'));
    var album_id=$(this).attr('id');
    $('.etm_album_images').load("index.php?route=information/gallery/albumimages",{album_id: album_id},function(response,statusTxt,xhr){
    
    });
    
 });
<?php if(!empty($gallery_albums) && count($gallery_albums)==1){ ?>
     var albumid=<?php echo $gallery_albums[0]['album_id'];?>;
    $('.etm_album_images').load("index.php?route=information/gallery/albumimages",{album_id: albumid},function(response,statusTxt,xhr){
    
    });
<?php } ?>
});
</script>

    