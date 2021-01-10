<?php

    include(__DIR__."/func.php");

    if (!is_file(__DIR__."/su/config.json"))
    {
        die("su/config.json not found.");
    }

    $config = json_decode(file_get_contents(__DIR__."/su/config.json"), true);

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
        $alias = cleanAlias($_GET['alias']);
    }
    // Get alias from POST Parameter (remove referer)
    elseif($_SERVER['REQUEST_METHOD']==="POST" && isset($_POST['alias']) &&
        !empty($_POST['alias']) && is_string($_POST['alias']))
    {
        $mode = "jsredir";
        $alias = cleanAlias($_POST['alias']);
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

        $filename = __DIR__."/su/links/link-".$alias.".json";
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

        $targethref = cleanUrl($huepflink['target']);
        $target = $huepflink['target'];
    }
    // Alias not found
    else
    {
        header("HTTP/1.1 404 Not Found", true);
        $mode="404";
    }

    $v = 4;
?>
<!doctype html>
<html lang="en">
    <head>
        <base href="<?php echo $config['basePath']; ?>">
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="stylesheet" href="view/style.css?v=<?php echo $v; ?>" crossorigin="anonymous">
        <title><?php echo $config['siteName']; ?></title>
    </head>
    <body>

        <div class="container" style="margin-top:20px;">

            <?php if ($mode === "400"): ?>
                <p style="text-align:center;">
                    Invalid Request!
                </p>
            <?php elseif ($mode === "404"): ?>
                <p style="text-align:center;">
                    The requested link does not exist!
                </p>
            <?php elseif ($mode === "formredir"): ?>
                <p style="text-align:center;">
                    This automatic redirect removes the referer from your request.
                    If nothing happens, please click on the button:
                </p>

                <form action="<?php echo $config['basePath']; ?>" class="formredir" method="POST">
                    <input type="hidden" name="alias" value="<?php echo $alias; ?>">
                    <div style="text-align:center;" class="formredircontainer">
                        <input type="submit" class="formredirclick" style="display:inline;" value="Click here to remove the referer from your request">
                    </div>
                </form>
            <?php elseif ($mode === "jsredir"): ?>
                <p style="text-align:center; margin: 0px;">
                    You should be redirected to the actual website. If nothing happens, please click the following link:
                </p>
                <p style="text-align:center;">
                    <a href="<?php echo $targethref; ?>" class="targetlink" rel="external nofollow noreferrer noopener"><?php echo htmlentities($target, ENT_COMPAT); ?></a>
                </p>

                <div class="alert alert-info" role="alert" style="margin-top: 40px; text-align:center;">
                    This service is just a link shortener and is <strong>not responsible</strong>
                    for the actual content of the respective websites.<br>
                    <a href="https://github.com/perryflynn/huepf" target="_blank">Source on GitHub</a> &middot;
                    <a href="<?php echo $config['imprintUrl']; ?>" target="_blank">Imprint</a> &middot;
                    <a href="<?php echo $config['privacyUrl']; ?>" target="_blank">Privacy</a>
                </div>
            <?php endif; ?>

        </div>

        <script src="view/script.js?v=<?php echo $v; ?>" crossorigin="anonymous"></script>

    </body>
</html>
