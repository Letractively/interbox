<!DOCTYPE html> 
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1"> 
        <title>{$Title}</title> 
        <link rel="stylesheet" href="jquery.mobile-1.2.0.min.css" />
        <script src="scripts/jquery.min.js"></script>
        <script src="scripts/jquery.mobile-1.2.0.min.js"></script>
    </head> 
    <body> 

        <div data-role="dialog">

            <div data-role="header" data-theme="d">
                <h1>{$Title}</h1>
            </div>

            <div data-role="content" data-theme="c">
                <h1>{$Info}</h1>
                <p>{$Content}</p>
            </div>
        </div>


    </body>
</html>
