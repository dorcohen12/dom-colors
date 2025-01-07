<?php
	defined('INSITE') or die('No direct script access allowed');
?>
        </div>
    </main>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://cdn.rtlcss.com/bootstrap/v4.5.3/js/bootstrap.bundle.min.js" integrity="sha384-40ix5a3dj6/qaC7tfz0Yr+p9fqWLzzAXiwxVLt9dw7UjQzGYw6rWRhFAnRapuQyK" crossorigin="anonymous"></script>
    <script>
        const App = {
            base_url: "<?php echo $Website->settings->web_url;?>/",
            file_limit: "<?php echo (int)FILE_LIMIT;?>"
        };
    </script>
    <script src="<?php echo $Website->settings->web_url;?>/assets/<?php echo TEMPLATE_NAME;?>/assets/js/app.js?v=<?php echo time();?>"></script>
</body>