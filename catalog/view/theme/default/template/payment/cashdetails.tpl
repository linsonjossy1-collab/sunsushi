<div id="cash-calculator">
    
        <div class="cash_cal_show"></div>
	<div class="cash-calc-top">
		<span class="cal-clear" value="C">C</span>
		<div class="cal-screen">0.00</div>
                <input type="hidden" id="actual_cal" name="actual_cal" value="">
	</div>
	
	<div class="cal-keys">
		
                <div class="left">
                    <span class="cal-pounds" value="1">&pound;1</span>
                    <span class="cal-pounds" value="2">&pound;2</span>
                    <span class="cal-pounds" value="5">&pound;5</span>
                    <span class="cal-pounds" value="10">&pound;10</span>
                    <span class="cal-pounds" value="20">&pound;20</span>
                    <span class="cal-pounds" value="50">&pound;50</span>
                                    </div>
                <div class="centre">
                    <span class="cal-nums" value="1">1</span>
                    <span class="cal-nums" value="2">2</span>
                    <span class="cal-nums" value="3">3</span>
                    <span class="cal-nums" value="4">4</span>
                    <span class="cal-nums" value="5">5</span>
                    <span class="cal-nums" value="6">6</span>
                    <span class="cal-nums" value="7">7</span>
                    <span class="cal-nums" value="8">8</span>
                    <span class="cal-nums" value="9">9</span>
                    <span class="cal-nums" value=".">.</span>
                    <span class="cal-nums" value="0">0</span>
                    <span class="cal-num-clear" value="c">C</span>
                </div>
                <div class="right">
                    <span class="cal-pence" value=".01">1p</span>
                    <span class="cal-pence" value=".02">2p</span>
                    <span class="cal-pence" value=".05">5p</span>
                    <span class="cal-pence" value=".1">10p</span>
                    <span class="cal-pence" value=".2">20p</span>
                    <span class="cal-pence" value=".5">50p</span>
                   
                </div>
		
		
               
	</div>
    
    </div>

<div id="cashdrawer_details">
    <input type="hidden" name="cash_drawer_status" id="cash_drawer_status" value="<?php echo $drawer_status; ?>">
</div>
<script type="text/javascript">
    function skipdrawer(){
           
    }
</script>