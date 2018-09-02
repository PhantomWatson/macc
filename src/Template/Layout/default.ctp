<?php
/**
 * @var \App\View\AppView $this
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        Muncie Arts and Culture Council
        <?= isset($pageTitle) ? " - $pageTitle" : '' ?>
    </title>
    <?= $this->element('favicons') ?>
    <?= $this->fetch('meta') ?>
    <?= $this->Html->css('style') ?>
    <?= $this->fetch('css') ?>
    <link media="all" type="text/css" href="//fonts.googleapis.com/css?family=Lato%3A400%2C400italic%2C700%2C700italic%7CLato%3A300%7CLato%3A300%7CLato%3A100&amp;subset=latin%2Clatin-ext&amp;ver=3.2.1" rel="stylesheet">
</head>
<body>
    <header>
        <h1 class="sr-only">
            Muncie Arts &amp; Culture
        </h1>

        <div id="logo">
            <a href="/">
                <img src="/img/macc-logo-200px.jpg" alt="Muncie Arts & Culture" />
            </a>
        </div>

        <nav class="navbar navbar-default">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse" aria-expanded="false">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                </div>

                <div class="collapse navbar-collapse" id="navbar-collapse">
                    <?= $this->element('header_nav') ?>
                </div>
            </div>
        </nav>
    </header>

    <div class="container">
        <div class="row">
            <div class="col-sm-8 col-sm-offset-2" id="content">
                <?php if (isset($pageTitle) && ! empty($pageTitle)): ?>
                    <div class="page-header">
                        <h1>
                            <?= $pageTitle ?>
                        </h1>
                    </div>
                <?php endif; ?>

                <?= $this->Flash->render() ?>
                <?= $this->fetch('content') ?>
            </div>
        </div>
    </div>

    <?= $this->element('logos_footer') ?>

    <footer id="footer-links" class="text-center">
        <p>
            &copy; <?= date('Y') ?> Muncie Arts &amp; Culture Council.
            All Rights Reserved.
            Muncie Arts and Culture Council is a not-for-profit 501(c)3 organization.
        </p>
        <ul>
            <li><a href="http://munciearts.org/">Home</a></li>
            <li><a href="http://munciearts.org/about-2/">About</a></li>
            <li><a href="http://munciearts.org/contact-2/">Contact</a></li>
            <li><a href="http://munciearts.org/publicart/">Public Art</a></li>
            <li><a href="http://munciearts.org/news-2/">News</a></li>
            <li><a href="http://69.163.201.253/events/list/">Calendar</a></li>
            <li><a href="http://munciearts.org/support-2/">Support</a></li>
            <li><a href="http://munciearts.org/membership-2/">Membership</a></li>
            <li><a href="http://munciearts.org/arts-directory-2/">Arts Directory</a></li>
            <li>
                <?= $this->Html->link(
                    'Terms of Service',
                    [
                        'prefix' => false,
                        'controller' => 'Pages',
                        'action' => 'terms'
                    ]
                ) ?>
            </li>
            <li>
                <?= $this->Html->link(
                    'Privacy Policy',
                    [
                        'prefix' => false,
                        'controller' => 'Pages',
                        'action' => 'privacy'
                    ]
                ) ?>
            </li>
        </ul>
    </footer>

    <!-- Grab Google CDN's jQuery, with a protocol relative URL; fall back to local if offline -->
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="/js/jquery-1.12.0.min.js"><\/script>')</script>

    <?= $this->Html->script('/bootstrap/js/bootstrap.min') ?>
    <?= $this->Html->script('/js/jquery-scrolltofixed-min') ?>
    <?= $this->Html->script('/js/script.js') ?>

    <?= $this->fetch('script') ?>

    <script>
        $(document).ready(function () {
            <?= $this->fetch('buffered') ?>

            $('nav.navbar').scrollToFixed({
                minWidth: 752
            });
        });

        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
        ga('create', 'UA-74038350-1', 'auto');
        ga('send', 'pageview');
    </script>
</body>
</html>
