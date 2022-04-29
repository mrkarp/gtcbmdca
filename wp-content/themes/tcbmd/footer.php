<?php
    /**
    * Footer
    */
?>
</div>

<footer class="bg-eastbay">
    <div class="container-fluid col-sm-10 col-lg-4">
        <div class="row py-4">
            <div class="col">
                <a href="" class="">
                    <img src="<?php echo get_template_directory_uri() ?>/images/fb.png" class="img w-35 float-right" />
                </a>
            </div>
            <div class="col text-center">
                <a href="" class="">
                <img src="<?php echo get_template_directory_uri() ?>/images/inst.png" class="img w-35" />
                </a>
            </div>
            <div class="col">
                <a href="" class="">
                <img src="<?php echo get_template_directory_uri() ?>/images/tw.png" class="img w-35 float-left" />
                </a>
            </div>
        </div>	
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 mt-2 mt-sm-2 text-center text-white">                
                <p class="h6">© 2002- <?php echo date("Y") ?> The Bernese Mountain Dog Club of the Greater Twin Cities, Inc. All right Reversed</p>
            </div>
            <hr>
        </div>	
    </div>
</footer>

<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-176786537-1"></script>
    <script>
    window.dataLayer = window.dataLayer || [];  
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'UA-176786537-1');
</script>
<script defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAssY0-Qtb8Ouh_9EQP5mklPk7cIQxgQ2g">
</script>

</div>

<?php wp_footer(); ?>

</body>
</html>