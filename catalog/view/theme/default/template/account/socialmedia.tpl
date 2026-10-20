<div id="facebook-button">
    <script src='http://connect.facebook.net/en_US/all.js#xfbml=1' type='text/javascript'></script>
    <fb:like href="<?php echo $this->config->get('config_facebook_url'); ?>" layout= "button_count"></fb:like>
</div>
    <script type='text/javascript'>
  //<![CDATA[
    FB.Event.subscribe('edge.create', function(href, widget) {
      onShare('facebook'); // our function that sends ajax request to app to extend users free trial
    });
  //]]>
</script><br />

<div id="tweet-button" >
<a href="<?php echo $this->config->get('config_twitter_url'); ?>" class="twitter-follow-button" data-show-count="true" data-show-screen-name="false" data-lang="en">Follow @Twitter</a>
</div>
<script type='text/javascript'>
// Log any kind of Web Intent event to Google Analytics

      // Category: "twitter_web_intents"

      // Action: Intent Event Type

      // Label: Identifier for action taken: tweet_id, screen_name/user_id, click region

      // First, load the widgets.js file asynchronously 

      window.twttr = (function (d,s,id) {

        var t, js, fjs = d.getElementsByTagName(s)[0];

        if (d.getElementById(id)) return; js=d.createElement(s); js.id=id;

        js.src="//platform.twitter.com/widgets.js"; fjs.parentNode.insertBefore(js, fjs);

        return window.twttr || (t = { _e: [], ready: function(f){ t._e.push(f) } });

      }(document, "script", "twitter-wjs"));



      function followIntentToAnalytics(intent_event) {
          onShare('tweet');

        if (intent_event) {

          var label = intent_event.data.user_id + " (" + intent_event.data.screen_name + ")";

          pageTracker._trackEvent('twitter_web_intents', intent_event.type, label);

        };

      }


     // Wait for the asynchronous resources to load

     twttr.ready(function (twttr) {

      // Now bind our custom intent events
    

      twttr.events.bind('follow',   followIntentToAnalytics);

      });
 

function onShare($social) {
    //window.location.href=''
   //var url=document.URL;
   // var path=url.split('?')  // path[0]+
   $.post('/index.php?route=account/reward/smreward&smedia='+$social, function(data) {
       $('#footer').append(data); 
       $('#smpopupcontent').dialog();
});

  // window.location.href=path[0]+'?route=account/reward&smedia='+$social;
}
</script>
