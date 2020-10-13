<?php include_once('functions.php'); ?>
<!DOCTYPE html>
<html lang="en" class="no-js">
<head>
    <meta charset="utf-8">
    <title>Style Guide Boilerplate</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#000000">

    <!-- Style Guide Boilerplate Styles -->
    <link rel="stylesheet" href="css/sg-style.css">
    <!--[if lt IE 9]>
    <link rel="stylesheet" href="css/sg-style-old-ie.css"><![endif]-->

    <!-- https://github.com/sindresorhus/github-markdown-css -->
    <link rel="stylesheet" href="css/github-markdown.css">

    <!-- Replace below stylesheet with your own stylesheet -->
    <!-- <link rel="stylesheet" href="css/style.css"> -->
    <link rel="stylesheet" href="../css/current.css">

    <!-- prism Syntax Highlighting Styles -->
    <link rel="stylesheet" href="vendor/prism/prism.css">
</head>
<body>
<a href="#main" class="sg-visually-hidden sg-visually-hidden-focusable">Skip to
    main content</a>

<div id="top" class="sg-header" role="banner">
    <div class="sg-container">
        <h1 class="sg-logo">
            <span class="sg-logo-initials">SG</span>
            <span class="sg-logo-full">STYLE GUIDE</span> <em>BOILERPLATE</em>
            <span class="sg-logo-full">FOR GIGADB</span>
        </h1>
        <button type="button" class="sg-nav-toggle">Menu</button>
    </div>
</div><!--/.sg-header-->

<div class="sg-wrapper sg-clearfix">
    <div id="nav" class="sg-sidebar" role="navigation">
        <h2 class="sg-h2 sg-subnav-title">About</h2>
        <ul class="sg-nav-group">
            <li>
                <a href="#sg-about">Getting Started</a>
            </li>
            <li>
                <a href="#sg-colors">Colors</a>
            </li>
            <li>
                <a href="#sg-fontStacks">Fonts</a>
            </li>
        </ul>

        <?php listFilesInFolder('markup'); ?>
    </div><!--/.sg-sidebar-->

    <div id="main" class="sg-main" role="main">
        <div class="sg-container">
            <div class="sg-info">
                <div class="sg-about sg-section">
                    <h2 id="sg-about" class="sg-h2">Getting Started</h2>
                    <p>A living style guide is a great tool to promote visual
                        consistency, unify UX designers and front-end
                        developers, as well as speed up development times. Add
                        some documentation here on how to get started with your
                        new style guide and start customizing this boilerplate
                        to your liking.</p>
                    <p>If you are looking for resources on style guides, check
                        out <a href="http://styleguides.io">styleguides.io</a>.
                        There are a ton of great articles, books, podcasts,
                        talks, and other style guide tools!</p>
                </div><!--/.sg-about-->
                <!-- Manually add your UI colors here. -->
                <div class="sg-colors sg-section">
                    <h2 id="sg-colors" class="sg-h2">Brand Colors</h2>
                    <div class="sg-color-grid">
                        <div class="sg-color">
                            <div class="sg-color-swatch"
                                 style="background-color: #099242;"></div>
                            <div class="sg-color-name">@brand-primary</div>
                            <div class="sg-color-value">#099242</div>
                        </div>
                        <div class="sg-color">
                            <div class="sg-color-swatch"
                                 style="background-color: #ff2020;"></div>
                            <div class="sg-color-name">@brand-danger</div>
                            <div class="sg-color-value">#ff2020</div>
                        </div>
                    </div><!--/.sg-color-grid-->
                </div>
                <div class="sg-colors sg-section">
                    <h2 id="sg-colors" class="sg-h2">Neutral Colors</h2>
                    <div class="sg-color-grid">
                        <div class="sg-color">
                            <div class="sg-color-swatch"
                                 style="background-color: #fff;"></div>
                            <div class="sg-color-name">Not named</div>
                            <div class="sg-color-value">#fff</div>
                        </div>
                        <div class="sg-color">
                            <div class="sg-color-swatch"
                                 style="background-color: #f8f8f8;"></div>
                            <div class="sg-color-name">Not named</div>
                            <div class="sg-color-value">#f8f8f8</div>
                        </div>
                        <div class="sg-color">
                            <div class="sg-color-swatch"
                                 style="background-color: #f5f5f5;"></div>
                            <div class="sg-color-name">Not named</div>
                            <div class="sg-color-value">#f5f5f5</div>
                        </div>
                        <div class="sg-color">
                            <div class="sg-color-swatch"
                                 style="background-color: #f2f2f2;"></div>
                            <div class="sg-color-name">Not named</div>
                            <div class="sg-color-value">#f2f2f2</div>
                        </div>
                    </div>
                    <div class="sg-color-grid">
                        <div class="sg-color">
                            <div class="sg-color-swatch"
                                 style="background-color: #eeeeee;"></div>
                            <div class="sg-color-name">Not named</div>
                            <div class="sg-color-value">#eeeeee</div>
                        </div>
                        <div class="sg-color">
                            <div class="sg-color-swatch"
                                 style="background-color: #ededed;"></div>
                            <div class="sg-color-name">Not named</div>
                            <div class="sg-color-value">#ededed</div>
                        </div>
                        <div class="sg-color">
                            <div class="sg-color-swatch"
                                 style="background-color: #e6e6e6;"></div>
                            <div class="sg-color-name">Not named</div>
                            <div class="sg-color-value">#e6e6e6</div>
                        </div>
                        <div class="sg-color">
                            <div class="sg-color-swatch"
                                 style="background-color: #e5e5e5;"></div>
                            <div class="sg-color-name">Not named</div>
                            <div class="sg-color-value">#e5e5e5</div>
                        </div>
                    </div>
                    <div class="sg-color-grid">
                        <div class="sg-color">
                            <div class="sg-color-swatch"
                                 style="background-color: #eee;"></div>
                            <div class="sg-color-name">Not named</div>
                            <div class="sg-color-value">#eee</div>
                        </div>
                        <div class="sg-color">
                            <div class="sg-color-swatch"
                                 style="background-color: #d0d0d0;"></div>
                            <div class="sg-color-name">Not named</div>
                            <div class="sg-color-value">#d0d0d0</div>
                        </div>
                        <div class="sg-color">
                            <div class="sg-color-swatch"
                                 style="background-color: #ddd;"></div>
                            <div class="sg-color-name">Not named</div>
                            <div class="sg-color-value">#ddd</div>
                        </div>
                        <div class="sg-color">
                            <div class="sg-color-swatch"
                                 style="background-color: #d9d9d9;"></div>
                            <div class="sg-color-name">Not named</div>
                            <div class="sg-color-value">#d9d9d9</div>
                        </div>
                    </div>
                    <div class="sg-color-grid">
                        <div class="sg-color">
                            <div class="sg-color-swatch"
                                 style="background-color: #cccccc;"></div>
                            <div class="sg-color-name">Not named</div>
                            <div class="sg-color-value">#cccccc</div>
                        </div>
                        <div class="sg-color">
                            <div class="sg-color-swatch"
                                 style="background-color: #bfbfbf;"></div>
                            <div class="sg-color-name">Not named</div>
                            <div class="sg-color-value">#bfbfbf</div>
                        </div>
                        <div class="sg-color">
                            <div class="sg-color-swatch"
                                 style="background-color: #b3b3b3;"></div>
                            <div class="sg-color-name">Not named</div>
                            <div class="sg-color-value">#b3b3b3</div>
                        </div>
                        <div class="sg-color">
                            <div class="sg-color-swatch"
                                 style="background-color: #a9a9a9;"></div>
                            <div class="sg-color-name">Not named</div>
                            <div class="sg-color-value">#a9a9a9</div>
                        </div>
                    </div>
                    <div class="sg-color-grid">
                        <div class="sg-color">
                            <div class="sg-color-swatch"
                                 style="background-color: #999;"></div>
                            <div class="sg-color-name">Not named</div>
                            <div class="sg-color-value">#999</div>
                        </div>
                        <div class="sg-color">
                            <div class="sg-color-swatch"
                                 style="background-color: #656565;"></div>
                            <div class="sg-color-name">@gray-dark</div>
                            <div class="sg-color-value">#656565</div>
                        </div>
                        <div class="sg-color">
                            <div class="sg-color-swatch"
                                 style="background-color: #666666;"></div>
                            <div class="sg-color-name">Not named</div>
                            <div class="sg-color-value">#666666</div>
                        </div>
                        <div class="sg-color">
                            <div class="sg-color-swatch"
                                 style="background-color: #666;"></div>
                            <div class="sg-color-name">Not named</div>
                            <div class="sg-color-value">#666</div>
                        </div>
                    </div>
                    <div class="sg-color-grid">
                        <div class="sg-color">
                            <div class="sg-color-swatch"
                                 style="background-color: #404040;"></div>
                            <div class="sg-color-name">Not named</div>
                            <div class="sg-color-value">#404040</div>
                        </div>
                        <div class="sg-color">
                            <div class="sg-color-swatch"
                                 style="background-color: #454545;"></div>
                            <div class="sg-color-name">Not named</div>
                            <div class="sg-color-value">#454545</div>
                        </div>
                        <div class="sg-color">
                            <div class="sg-color-swatch"
                                 style="background-color: #333;"></div>
                            <div class="sg-color-name">Not named</div>
                            <div class="sg-color-value">#333</div>
                        </div>
                        <div class="sg-color">
                            <div class="sg-color-swatch"
                                 style="background-color: #222;"></div>
                            <div class="sg-color-name">Not named</div>
                            <div class="sg-color-value">#222</div>
                        </div>
                    </div>
                    <div class="sg-color-grid">
                        <div class="sg-color">
                            <div class="sg-color-swatch"
                                 style="background-color: #151515;"></div>
                            <div class="sg-color-name">Not named</div>
                            <div class="sg-color-value">#151515</div>
                        </div>
                        <div class="sg-color">
                            <div class="sg-color-swatch"
                                 style="background-color: #000;"></div>
                            <div class="sg-color-name">Not named</div>
                            <div class="sg-color-value">#000</div>
                        </div>
                    </div><!--/.sg-color-grid-->
                </div>
                <div class="sg-colors sg-section">
                    <h2 id="sg-colors" class="sg-h2">Utility Colors</h2>
                    <div class="sg-color-grid">
                        <div class="sg-color">
                            <div class="sg-color-swatch"
                                 style="background-color: #00B050;"></div>
                            <div class="sg-color-name">Not named</div>
                            <div class="sg-color-value">#00B050</div>
                        </div>
                        <div class="sg-color">
                            <div class="sg-color-swatch"
                                 style="background-color: #7AB441;"></div>
                            <div class="sg-color-name">Not named</div>
                            <div class="sg-color-value">#7AB441</div>
                        </div>
                        <div class="sg-color">
                            <div class="sg-color-swatch"
                                 style="background-color: #0fad59;"></div>
                            <div class="sg-color-name">Not named</div>
                            <div class="sg-color-value">#0fad59</div>
                        </div>
                        <div class="sg-color">
                            <div class="sg-color-swatch"
                                 style="background-color: #0eb23c;"></div>
                            <div class="sg-color-name">Not named</div>
                            <div class="sg-color-value">#0eb23c</div>
                        </div>
                    </div>
                    <div class="sg-color-grid">
                        <div class="sg-color">
                            <div class="sg-color-swatch"
                                 style="background-color: #a5cc7e;"></div>
                            <div class="sg-color-name">Not named</div>
                            <div class="sg-color-value">#a5cc7e</div>
                        </div>
                        <div class="sg-color">
                            <div class="sg-color-swatch"
                                 style="background-color: #6ea23a;"></div>
                            <div class="sg-color-name">Not named</div>
                            <div class="sg-color-value">#6ea23a</div>
                        </div>
                        <div class="sg-color">
                            <div class="sg-color-swatch"
                                 style="background-color: #5a9c7b;"></div>
                            <div class="sg-color-name">Not named</div>
                            <div class="sg-color-value">#5a9c7b</div>
                        </div>
                        <div class="sg-color">
                            <div class="sg-color-swatch"
                                 style="background-color: #005b2d;"></div>
                            <div class="sg-color-name">Not named</div>
                            <div class="sg-color-value">#005b2d</div>
                        </div>
                    </div>
                    <div class="sg-color-grid">
                        <div class="sg-color">
                            <div class="sg-color-swatch"
                                 style="background-color: #B9DFC9;"></div>
                            <div class="sg-color-name">Not named</div>
                            <div class="sg-color-value">#B9DFC9</div>
                        </div>
                        <div class="sg-color">
                            <div class="sg-color-swatch"
                                 style="background-color: #4F6228;"></div>
                            <div class="sg-color-name">Not named</div>
                            <div class="sg-color-value">#4F6228</div>
                        </div>
                        <div class="sg-color">
                            <div class="sg-color-swatch"
                                 style="background-color: #087A38;"></div>
                            <div class="sg-color-name">Not named</div>
                            <div class="sg-color-value">#087A38</div>
                        </div>
                        <div class="sg-color">
                            <div class="sg-color-swatch"
                                 style="background-color: #F3FAF6;"></div>
                            <div class="sg-color-name">Not named</div>
                            <div class="sg-color-value">#F3FAF6</div>
                        </div>
                    </div>
                    <div class="sg-color-grid">
                        <div class="sg-color">
                            <div class="sg-color-swatch"
                                 style="background-color: #32bca3;"></div>
                            <div class="sg-color-name">Not named</div>
                            <div class="sg-color-value">#32bca3</div>
                        </div>
                        <div class="sg-color">
                            <div class="sg-color-swatch"
                                 style="background-color: #25b6d3;"></div>
                            <div class="sg-color-name">Not named</div>
                            <div class="sg-color-value">#25b6d3</div>
                        </div>
                        <div class="sg-color">
                            <div class="sg-color-swatch"
                                 style="background-color: #f7bb41;"></div>
                            <div class="sg-color-name">Not named</div>
                            <div class="sg-color-value">#f7bb41</div>
                        </div>
                        <div class="sg-color">
                            <div class="sg-color-swatch"
                                 style="background-color: #e04f5e;"></div>
                            <div class="sg-color-name">Not named</div>
                            <div class="sg-color-value">#e04f5e</div>
                        </div>
                    </div><!--/.sg-color-grid-->
                    <div class="sg-color-grid">
                        <div class="sg-color">
                            <div class="sg-color-swatch"
                                 style="background-color: #08c;"></div>
                            <div class="sg-color-name">Not named</div>
                            <div class="sg-color-value">#08c</div>
                        </div>
                        <div class="sg-color">
                            <div class="sg-color-swatch"
                                 style="background-color: #005580;"></div>
                            <div class="sg-color-name">Not named</div>
                            <div class="sg-color-value">#005580</div>
                        </div>
                        <div class="sg-color">
                            <div class="sg-color-swatch"
                                 style="background-color: #099292;"></div>
                            <div class="sg-color-name">Not named</div>
                            <div class="sg-color-value">#099292</div>
                        </div>
                    </div><!--/.sg-color-grid-->
                </div>

                <!-- Manually add your fonts here. -->
                <!--
                // In common.css font-family: 'Open Sans', Lato, 'PT Sans', Arial, 'Microsoft Yahei', 'Hiragino Sans GB', 'WenQuanYi Zen Hei Mono', sans-serif;
                -->
                <div class="sg-font-stacks sg-section">
                    <h2 id="sg-fontStacks" class="sg-h2">Font Stacks</h2>
                    <dl class="sg-font-list">
                        <dt>Primary Font:</dt>
                        <dd style='font-family: "Open Sans", Lato, "PT Sans", Arial, "Microsoft Yahei", "Hiragino Sans GB", "WenQuanYi Zen Hei Mono", sans-serif;'>
                            "Open Sans", Lato, "PT Sans", Arial, "Microsoft Yahei", "Hiragino Sans GB", "WenQuanYi Zen Hei Mono", sans-serif;
                        </dd>

                        <dt>Primary Font Italic:</dt>
                        <dd style='font-family: "Open Sans", Lato, "PT Sans", Arial, "Microsoft Yahei", "Hiragino Sans GB", "WenQuanYi Zen Hei Mono", sans-serif; font-style: italic;'>
                            "Open Sans", Lato, "PT Sans", Arial, "Microsoft Yahei", "Hiragino Sans GB", "WenQuanYi Zen Hei Mono", sans-serif;
                        </dd>

                        <dt>Primary Font Bold:</dt>
                        <dd style='font-family: "Open Sans", Lato, "PT Sans", Arial, "Microsoft Yahei", "Hiragino Sans GB", "WenQuanYi Zen Hei Mono", sans-serif; font-weight: 800;'>
                            "Open Sans", Lato, "PT Sans", Arial, "Microsoft Yahei", "Hiragino Sans GB", "WenQuanYi Zen Hei Mono", sans-serif;
                        </dd>

                        <dt>Secondary Font:</dt>
                        <dd>
                            No secondary font configured!
                        </dd>

                        <dt>Secondary Font Italic:</dt>
                        <dd>
                            No secondary font italic configured!
                        </dd>

                        <dt>Secondary Font Bold:</dt>
                        <dd>
                            No secondary font bold configured!
                        </dd>
                    </dl>
                    <div class="sg-markup-controls"><a class="sg-btn--top"
                                                       href="#top">Back to
                            Top</a>
                    </div>
                </div><!--/.sg-font-stacks-->
            </div><!--/.sg-info-->

            <?php renderFilesInFolder('markup'); ?>
        </div><!--/.sg-container-->
    </div><!--/.sg-main-->
</div><!--/.sg-wrapper-->

<!--[if gt IE 8]><!-->
<script src="vendor/prism/prism.js"></script><!--<![endif]-->
<script src="js/sg-scripts.js"></script>
</body>
</html>

