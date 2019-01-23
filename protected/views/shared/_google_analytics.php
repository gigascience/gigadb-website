    <script type="text/javascript">
        document.addEventListener("DOMContentLoaded", function(event) { //This event is fired after deferred scripts are loaded

            var _gaq = _gaq || [];
            _gaq.push(['_setAccount', "<?= Yii::app()->params['google_analytics_profile'] ?>"]);
              _gaq.push(['_trackPageview']);
            (function() {
                var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
                ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
                var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
              })();

        });
    </script>

