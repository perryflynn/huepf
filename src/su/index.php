<?php
    
    header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self' 'unsafe-inline'; object-src 'none'");
    header("Strict-Transport-Security: max-age=63072000");
    header("X-Frame-Options: DENY");
    header("Referrer-Policy: same-origin");
    header("X-Content-Type-Options: nosniff");
    header("X-XSS-Protection: 1; mode=block");

    // Delete alias
    if(isset($_GET['action']) && $_GET['action']==="delete" && 
        isset($_GET['alias']) && is_string($_GET['alias']))
    {
        $alias = trim(preg_replace('/[\/]/', '', $_GET['alias']));
        $filename = __DIR__."/links/link-".$alias.".json";
        if(is_file($filename))
        {
            unlink($filename);
        }
    }

    // Create a new alias
    if(isset($_POST['alias']) && isset($_POST['target']))
    {
        $alias = preg_replace('/[\/]/', '', $_POST['alias']);
        $target = $_POST['target'];
        
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
    
?>
<!doctype html>
<html lang="en">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="../view/bootstrap-4.0.0-dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

        <title>hüpf.net Superuser</title>
    </head>
    <body>
        
        <div class="container">
            <div class="row">
                <div class="col">
                
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
                                    <td><a href="/<?php echo $huepflink['alias']; ?>" target="_blank"><?php echo $huepflink['alias']; ?></a></td>
                                    <td><?php echo $huepflink['target']; ?></td>
                                    <td><?php echo $huepflink['hits']; ?></td>
                                    <td><?php echo $huepflink['last_access_date']; ?></td>
                                    <td>[<a href="?action=delete&amp;alias=<?php echo $huepflink['alias']; ?>">delete</a>]</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    
                </div>
            </div>
        </div>
        
        <!-- Optional JavaScript -->
        <!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script src="../view/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
        <script src="../view/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
        <script src="../view/bootstrap-4.0.0-dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    
    </body>
</html>
