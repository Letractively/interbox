<!DOCTYPE html> 
<html> 
    <head> 
        <meta charset="utf-8">
        <title>{$Title}</title> 
        <meta name="viewport" content="width=device-width, initial-scale=1"> 
        <link rel="stylesheet" href="jquery.mobile-1.2.0.min.css" />
        <script src="scripts/jquery.min.js"></script>
        <script src="scripts/jquery.mobile-1.2.0.min.js"></script>
    </head> 
    <body>

        <div data-role="page">

            <div data-role="header" data-theme="e">
                {$LeftButton}
                <h1>{$Title}</h1>
                {$RightButton}
            </div><!-- /header -->
            {$TopNav}
            <div data-role="content">
                {$Content}
            </div><!-- /content -->

        </div><!-- /page -->

    </body>
</html>
