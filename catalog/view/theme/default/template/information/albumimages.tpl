<?php //print_r($images);?>
<?php if(!empty($images)){ ?>
<?php foreach($images as $k=>$image) { ?>
 <?php $img = $image['org_image']; ?>
<div class="album_img image colorbox" onclick="openModal();currentSlide(<?php echo ($k+1); ?>)"   rel="colorbox" id="<?php echo $image['gallery_image_id']?>" style="background-image: url('<?php echo $img; ?>');">
   
   
</div>
<?php } ?>
<div id="myModal" class="modal">
    <span class="close cursor" onclick="closeModal()">&times;</span>
    <div class="modal-content">
        <?php foreach($images as $k=>$image) { ?>
            <div class="mySlides">
                <img class="demo" src="<?php echo $image['org_image']; ?>" alt="txt" />
            </div>
        <?php } ?>
        <a class="prev" onclick="plusSlides(-1)">&#10094;</a>
        <a class="next" onclick="plusSlides(1)">&#10095;</a>
    </div>
</div>
<?php } ?>
<script>
function openModal() {
  document.getElementById('myModal').style.display = "block";
}

function closeModal() {
  document.getElementById('myModal').style.display = "none";
}

var slideIndex = 1;
showSlides(slideIndex);

function plusSlides(n) {
  showSlides(slideIndex += n);
}

function currentSlide(n) {
  showSlides(slideIndex = n);
}

function showSlides(n) {
  var i;
  var slides = document.getElementsByClassName("mySlides");
  var dots = document.getElementsByClassName("demo");
  var captionText = document.getElementById("caption");
  if (n > slides.length) {slideIndex = 1}
  if (n < 1) {slideIndex = slides.length}
  for (i = 0; i < slides.length; i++) {
      slides[i].style.display = "none";
  }
  for (i = 0; i < dots.length; i++) {
      dots[i].className = dots[i].className.replace(" active", "");
  }
  slides[slideIndex-1].style.display = "block";
  dots[slideIndex-1].className += " active";
  //captionText.innerHTML = dots[slideIndex-1].alt;
}
</script>
 