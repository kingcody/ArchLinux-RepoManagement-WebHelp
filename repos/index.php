<?php
  // Functions
  function get_repo_list($path = './') {
    $repos = array();
    $dir_results = scandir($path);
    $repo_path = preg_replace('/index|\.html|\.php/i', '', $_SERVER['REQUEST_URI']);

    foreach ($dir_results as $result) {
      if ($result === '.' or $result === '..') continue;
      // result is a dir
      $repo_db_path = $path . '/' . $result;
      if (is_dir($repo_db_path)) {
        // init repo entry
        $repo_entry = array(
          'name' => $result,
          'display_name' => $result,
          'description' => '',
          'sig_level' => 'Never',
          'server' => 'http://' . $_SERVER['SERVER_NAME'] . $repo_path . '$repo/$arch'
        );

        // if config file for repo exist use dat from there.
        if (file_exists($repo_db_path . '/repo.conf')) {
          $repo_conf = json_decode(file_get_contents($repo_db_path . '/repo.conf'), true);

          foreach ($repo_entry as $key => $value) {
            if ($key != 'name' && array_key_exists($key, $repo_conf) && !empty($repo_conf[$key])) {
              $repo_entry[$key] = $repo_conf[$key];
            }
          }
        }

        // add repo to list
        array_push($repos, $repo_entry);
      }
    }

    return $repos;
  }

  function print_repos() {
    $repo_list = get_repo_list();

    foreach ($repo_list as $key => $repo) {
      echo "<h3 class='repoTitle'>" . $repo['display_name'] . "</h3>";
      if (!empty($repo['description'])) {
        echo "<h5 class='repoDesc'>" . $repo['description'] . "</h5>";
      }
      echo "
        <strong>
          <pre class='pacmanEntry'>[" . $repo['name'] . "]
SigLevel = " . $repo['sig_level'] . "
Server = " . $repo['server'] . "</pre>
        </strong>
      ";
    }
  }
?>
<!DOCTYPE html>
<html class="no-js">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>My Linux Repositories</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- Place favicon.ico and apple-touch-icon(s) in the root directory -->
  <link href="//netdna.bootstrapcdn.com/bootstrap/3.0.2/css/bootstrap.min.css" rel="stylesheet">
  <script src="//netdna.bootstrapcdn.com/bootstrap/3.0.2/js/bootstrap.min.js"></script>
  <style>
    #wrap {
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 10px;
    }

    #wrap .repoListings {
      margin: 40px auto;
      width: 80%;
    }

    .repoDesc {
      margin-left: 10px;
    }

    .pacmanEntry {
      margin-top: 16px;
    }
  </style>
    </head>
    <body>
        <!--[if lt IE 8]>
<p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
<![endif]-->

        <!-- Add your site or application content here -->
        <div id="wrap">
          <h2>/etc/pacman.conf repo entries</h2>
          <div class="repoListings">
            <?php
              print_repos();
            ?>
          </div>
        </div>

        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.10.2.min.js"><\/script>')</script>

        <!-- Google Analytics: change UA-XXXXX-X to be your site's ID. -->
        <!--
        <script>
            (function(b,o,i,l,e,r){b.GoogleAnalyticsObject=l;b[l]||(b[l]=
            function(){(b[l].q=b[l].q||[]).push(arguments)});b[l].l=+new Date;
            e=o.createElement(i);r=o.getElementsByTagName(i)[0];
            e.src='//www.google-analytics.com/analytics.js';
            r.parentNode.insertBefore(e,r)}(window,document,'script','ga'));
            ga('create','UA-XXXXX-X');ga('send','pageview');
        </script>
        -->
    </body>
</html>
