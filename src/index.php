<?php
    
    header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self' 'unsafe-inline'; object-src 'none'");
    header("Strict-Transport-Security: max-age=63072000");
    header("X-Frame-Options: DENY");
    header("Referrer-Policy: same-origin");
    header("X-Content-Type-Options: nosniff");
    header("X-XSS-Protection: 1; mode=block");
    
    $alias = null;
    $mode = null;
    
    // Get alias from GET Parameter (site entry)
    if($_SERVER['REQUEST_METHOD']==="GET" && isset($_GET['alias']) && 
        !empty($_GET['alias']) && is_string($_GET['alias']))
    {
        $mode = "formredir";
        $alias = trim($_GET['alias']);
    }
    // Get alias from POST Parameter (remove referer)
    elseif($_SERVER['REQUEST_METHOD']==="POST" && isset($_POST['alias']) && 
        !empty($_POST['alias']) && is_string($_POST['alias']))
    {
        $mode = "jsredir";
        $alias = trim($_POST['alias']);
    }
    
    // get data from json file
    $huepflink = null;
    $filename = null;
    if(is_string($alias) && is_string($mode))
    {
        if(mb_strlen($alias)>50)
        {
            $alias = mb_substr($alias, 0, 50);
        }
        
        $filename = __DIR__."/su/links/link-".preg_replace('/[\/]/', "", $alias).".json";
        if(is_file($filename))
        {
            $huepflink = json_decode(file_get_contents($filename), true);
        }
    }
    
    $targethref=null;
    $target=null;
    
    // No alias given, show bad request message
    if(is_null($alias))
    {
        header("HTTP/1.1 400 Bad Request", true);
        $mode="400";
    }
    // Alias found, update stats, prepare hyperlink
    else if(is_array($huepflink) && isset($huepflink['target']))
    {
        if($mode==="jsredir" && is_string($filename))
        {
            $huepflink['last_access_date'] = date('c');
            $huepflink['hits']++;
            file_put_contents($filename, json_encode($huepflink));
        }
        
        $search = array(
            "&", '"',
        );

        $replace = array(
            "&amp;", "\\\"",
        );
    
        $targethref = str_replace($search, $replace, $huepflink['target']);
        $target = str_replace("&", "&amp", $huepflink['target']);
    }
    // Alias not found
    else
    {
        header("HTTP/1.1 404 Not Found", true);
        $mode="404";
    }
    
?>
<!doctype html>
<html lang="en">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="view/bootstrap-4.0.0-dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

        <title>hüpf.net Shortlink &amp; Safe redirect</title>
    </head>
    <body>
        
        <div class="container" style="margin-top:20px;">
            <div class="row">
                <div class="col">
                    
                    <?php if($mode==="400"): ?>
                        <p style="text-align:center;">
                            Ungültiger Aufruf!
                        </p>
                    <?php elseif($mode==="404"): ?>
                        <p style="text-align:center;">
                            Der angeforderte Link existiert nicht!
                        </p>
                    <?php elseif($mode==="formredir"): ?>
                        <p style="text-align:center;">
                            Du wirst in wenigen Augenblicken weitergeleitet...
                        </p>
                        
                        <form action="/" class="formredir" method="POST">
                            <input type="hidden" name="alias" value="<?php echo $alias; ?>">
                            <noscript>
                                <div style="text-align:center;">
                                    <input type="submit" class="formredirclick" style="display:inline;" value="Hier klicken um den Referer zu entfernen">
                                </div>
                            </noscript>
                        </form>
                    <?php elseif($mode==="jsredir"): ?>
                        <p style="text-align:center;">
                            Du wirst in wenigen Augenblicken zu folgender Website weitergeleitet:<br>
                            <a href="<?php echo $targethref; ?>" class="targetlink" rel="external nofollow noreferrer noopener"><?php echo $target; ?></a>
                        </p>
                        
                        <div class="alert alert-info" role="alert">
                            Dieser Service dient ausschließlich zur Verkürzung von Hyperlinks und
                            ist nicht für die Inhalte auf der eigentlichen Website verantwortlich! &middot;
                            <a href="put link here" target="_blank">Impressum</a> &middot;
                            <a href="put link here" target="_blank">Datenschutz</a>
                        </div>
                    <?php endif; ?>
                    
                </div>
            </div>
        </div>
        
        <!-- Optional JavaScript -->
        <!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script src="view/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
        <script src="view/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
        <script src="view/bootstrap-4.0.0-dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
        
        <script src="view/script.js" crossorigin="anonymous"></script>
    
    </body>
</html>
