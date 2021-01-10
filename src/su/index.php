<?php

    include(__DIR__."/../func.php");

    if (!is_file(__DIR__."/config.json"))
    {
        die("su/config.json not found.");
    }

    $config = json_decode(file_get_contents(__DIR__."/config.json"), true);

    header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self' 'unsafe-inline'; object-src 'none'");
    header("Strict-Transport-Security: max-age=63072000");
    header("X-Frame-Options: DENY");
    header("Referrer-Policy: same-origin");
    header("X-Content-Type-Options: nosniff");
    header("X-XSS-Protection: 1; mode=block");

    clearstatcache();

    // Delete alias
    if(isset($_GET['action']) && $_GET['action']==="delete" &&
        isset($_GET['alias']) && is_string($_GET['alias']))
    {
        $alias = cleanAlias($_GET['alias']);
        $filename = __DIR__."/links/link-".$alias.".json";
        if(is_file($filename))
        {
            unlink($filename);
        }

        reload(false);
    }

    // Create a new alias
    if(isset($_POST['alias']) && isset($_POST['target']))
    {
        $alias = cleanAlias($_POST['alias']);
        $target = cleanUrl($_POST['target']);

        if(!empty($alias) && !empty($target))
        {
            $filename = __DIR__."/links/link-".$alias.".json";
            $data = array(
                "alias" => $alias,
                "target" => $target,
                "hits" => 0,
                "create_date" => date('c'),
                "last_access_date" => null,
            );

            file_put_contents($filename, json_encode($data));
        }

        reload(false);
    }

    // Get aliases from json files
    $huepflinks = array();
    foreach (new DirectoryIterator(__DIR__."/links/") as $file)
    {
        if($file->isDot())
        {
            continue;
        }

        if(preg_match('/^link-.+\.json$/', $file->getFilename())===1)
        {
            $huepflinks[] = json_decode(file_get_contents($file->getPathname()), true);
        }
    }

    // Sort aliases by alias name
    uasort($huepflinks, function($a, $b)
    {
        return strnatcmp($a['alias'], $b['alias']);
    });

    $v = 3;
?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="stylesheet" href="../view/style.css?v=<?php echo $v; ?>" crossorigin="anonymous">

        <title><?php echo $config['shortName']; ?> Superuser</title>
    </head>
    <body>

        <div class="container">

            <h1>hüpf.net Superuser</h1>

            <h2>New link</h2>

            <form method="POST" action="/su/">
                <div class="form-group">
                    <label for="field-huepflink">Alias</label>
                    <input type="text" name="alias" class="form-control" maxlength="50" aria-describedby="help-alias" id="field-huepflink" placeholder="Alias">
                    <small id="help-alias" class="form-text text-muted">Important: There is no validation or sanitization for your inputs! Max Length: 50</small>
                </div>

                <div class="form-group">
                    <label for="field-hyperlink">Hyperlink</label>
                    <input type="text" name="target" class="form-control" aria-describedby="help-hyperlink" id="field-hyperlink" placeholder="Hyperlink">
                    <small id="help-hyperlink" class="form-text text-muted">Important: There is no validation or sanitization for your inputs!</small>
                </div>

                <button type="submit" class="btn btn-primary">Create Hüpflink</button>
            </form>

            <h2 style="margin-top:20px;">Existing links</h2>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th scope="col">Alias</th>
                        <th scope="col">Target</th>
                        <th scope="col">Hits</th>
                        <th scope="col">Last access</th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($huepflinks as $huepflink): ?>
                        <tr>
                            <td><a href="/<?php echo urlencode($huepflink['alias']); ?>" target="_blank"><?php echo htmlentities($huepflink['alias'], ENT_COMPAT); ?></a></td>
                            <td class="break"><?php echo htmlentities($huepflink['target'], ENT_COMPAT); ?></td>
                            <td><?php echo htmlentities($huepflink['hits'], ENT_COMPAT); ?></td>
                            <td><?php echo htmlentities($huepflink['last_access_date'], ENT_COMPAT); ?></td>
                            <td>[<a href="?action=delete&amp;alias=<?php echo urlencode($huepflink['alias']); ?>">delete</a>]</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        </div>

    </body>
</html>
