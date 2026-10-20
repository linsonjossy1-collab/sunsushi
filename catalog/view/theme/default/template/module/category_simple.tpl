<div class="box">

  <div class="box-content">
      <button class="lefty paddle" id="left-button" onclick="scrollcatLeft()"><i class="fa fa-angle-left" aria-hidden="true"></i></button>
    <div class="box-category" id="scrollit">

        <?php foreach ($categories as $category) { ?>

          <?php if ($category['category_id'] == $category_id) { ?>

          <?php } else { ?>

          <?php } ?>
          <?php if ($category['children']) { ?>
          <div class="cater" id="caterr">
          <ul class="scroll-content" id="left-side-bar-category">
              <li class="side-cat-selected allcattn" ><a href=""><img src="/image/data/All.png" alt="all dishes" title="all dishes" /> All</a></li>
            <?php foreach ($category['children'] as $child) { ?>
                <li cid="<?php echo $child['category_id']; ?>" class="<?php if(!empty($child['shipping_methodes'])){ foreach($child['shipping_methodes'] as $catshipping1){ echo "p".$catshipping1." "; } } ?>">
              <?php if ($child['category_id'] == $child_id) { ?>
              <a href="#cat_" class="active"><img src="<?php echo $child['image']; ?>" alt="<?php echo $child['image']; ?>" title="<?php echo $child['image']; ?>" class="img-responsive" /> <?php echo $child['name']; ?></a>
              <?php } else { ?>
              <a href="#cat_"><img src="<?php echo $child['image']; ?>" alt="<?php echo $child['image']; ?>" title="<?php echo $child['image']; ?>" class="img-responsive" /> <?php echo $child['name']; ?></a>
              <?php } ?>
            </li>
            <?php } ?>
          </ul>
          </div>
          <?php } ?>
        </li>
        <?php } ?>
      </ul>
    </div>
      <button class="righty paddle" id="right-button" onclick="scrollcatRight()"><i class="fa fa-angle-right" aria-hidden="true"></i></button>
  </div>
</div>
<script type="text/javascript">
$('document').ready(function(){
$('#left-side-bar-category li').click(function(){
    $('#left-side-bar-category li').removeClass('side-cat-selected');
    $(this).addClass('side-cat-selected');
    console.log($(this).attr('cid'));
    if($(this).attr('cid')){
        $('.prod_main_categories').hide();
        $('#cat_'+$(this).attr('cid')).show();
    }
    
});  
});
function scrollcatLeft() {
  const myDiv = document.getElementById('left-side-bar-category');
  myDiv.scrollBy(-100, 0); // Adjust the scroll amount as needed
  
}
 
function scrollcatRight() {
  const myDiv = document.getElementById('left-side-bar-category');
  myDiv.scrollBy(100, 0); // Adjust the scroll amount as needed
}

$(document).ready(function () {
	// Smooth scrolling when clicking on navigation links
	$("a").on("click", function (event) {
	  if (this.hash !== "") {
		event.preventDefault();
		const hash = this.hash;
		$("html, body").animate(
		  {
			scrollTop: $(hash).offset().top-200,
		  },
		  800 // Adjust the speed of scrolling (in milliseconds) to your preference
		);
	  }
	});
        $(".allcattn a").on("click", function (event) {
            $('.prod_main_categories').show();
            event.preventDefault();
            console.log("yeyyd");
            
	});

	  const scrollDistance = $(window).scrollTop();

  });
  
</script>

<script type="text/javascript">
// Get the container and content elements
var container = document.querySelector('.cater');
var content = document.querySelector('.scroll-content');

// Variables to track mouse movement
var isDown = false;
var startX;
var scrollLeft;

// Event listeners for mouse down, mouse leave, and mouse up
container.addEventListener('mousedown', function(e) {
    isDown = true;
    startX = e.pageX - container.offsetLeft;
    scrollLeft = container.scrollLeft;
    container.classList.add('active');
});

container.addEventListener('mouseleave', function() {
    isDown = false;
    container.classList.remove('active');
});

container.addEventListener('mouseup', function() {
    isDown = false;
    container.classList.remove('active');
});

// Event listener for mouse move
container.addEventListener('mousemove', function(e) {
    if (!isDown) return;
    e.preventDefault();
    var x = e.pageX - container.offsetLeft;
    var walk = (x - startX) * 2; // Adjust the scrolling speed if needed
    container.scrollLeft = scrollLeft - walk;
});

</script>