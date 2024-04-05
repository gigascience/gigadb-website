<?php include_once('functions.php'); ?>
<!DOCTYPE html>
<html lang="en" class="no-js">
<head>
    <meta charset="utf-8">
    <title>GigaDB - Style Guide</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#000000">

    <!-- Style Guide Boilerplate Styles -->
    <link rel="stylesheet" href="css/sg-style.css">
    <!--[if lt IE 9]><link rel="stylesheet" href="css/sg-style-old-ie.css"><![endif]-->

    <!-- https://github.com/sindresorhus/github-markdown-css -->
    <link rel="stylesheet" href="css/github-markdown.css">

    <!-- Replace below stylesheet with your own stylesheet -->
    <!-- <link rel="stylesheet" href="css/style.css"> -->
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="../css/pager.css">
    <link rel="stylesheet" type="text/css" href="../fonts/open_sans/v13/open_sans.css">
    <link rel="stylesheet" type="text/css" href="../fonts/pt_sans/v8/pt_sans.css">
    <link rel="stylesheet" type="text/css" href="../fonts/lato/v11/lato.css">
    <link rel="stylesheet" href="../css/current.css">
    <link rel="stylesheet" type="text/css" href="/assets/d86acd89/gridview/styles.css">
    <link rel="stylesheet" type="text/css" href="/css/jquery.tag-editor.css">

    <!-- prism Syntax Highlighting Styles -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.22.0/themes/prism.min.css">
</head>
<body>
<a href="#main" class="sg-visually-hidden sg-visually-hidden-focusable">Skip to
    main content</a>

<div id="top" class="sg-header" role="banner">
    <div class="sg-container">
        <h1 class="sg-logo">
            <span class="sg-logo-initials">SG</span>
            <span class="sg-logo-full">STYLE GUIDE</span>
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
            <li>
                <a href="#sg-contrast">Color Contrast</a>
            </li>
        </ul>

        <?php listFilesInFolder('doc'); ?>
    </div><!--/.sg-sidebar-->

    <div id="main" class="sg-main" role="main">
        <div class="sg-container">
            <div class="sg-info">
                <div class="sg-about sg-section">
                    <h2 id="sg-about" class="sg-h2">Getting Started</h2>
                    <p>This pattern library contains the components of
                        gigadb.org - the web interface to the GigaDB database.
                        Having a living style guide helps us keep up to date
                        with the brand guidelines for gigadb.org and aids its
                        development.</p>
                </div><!--/.sg-about-->
                <!-- Manually add your UI colors here. -->
                <div class="sg-colors sg-section">
                    <h2 id="sg-colors" class="sg-h2">Canonical Colors</h2>
                    <div class="sg-color-grid">
                        <div class="sg-color">
                            <div class="sg-color-swatch"
                                 style="background-color: #08893e;"></div>
                            <div class="sg-color-name">@color-gigadb-green</div>
                            <div class="sg-color-value">#08893e</div>
                            <div class="sg-color-name">Used 55 times</div>
                        </div>
                        <div class="sg-color">
                            <div class="sg-color-swatch"
                                 style="background-color: #ff2020;"></div>
                            <div class="sg-color-name">@color-brand-danger</div>
                            <div class="sg-color-value">#ff2020</div>
                            <div class="sg-color-name">Used 1 time</div>
                        </div>
                    </div><!--/.sg-color-grid-->
                </div>
                <div class="sg-colors sg-section">
                  <h2  class="sg-h2">Tints</h2>
                <?php
                $colors = [
                  'gigadb-green' => [
                    'label' => 'GigaDB Green',
                    'tints' => [
                      '50' => [
                        'hex' => '#F3FAF6',
                        'usage' => 1
                        ],
                      '400' => [
                        'hex' => '#39ef83',
                        'usage' => 32
                        ],
                      '600' => [
                        'hex' => '#06b34d',
                        'usage' => 32
                        ],
                      '750' => [
                        'hex' => '#08893e',
                        'usage' => 32
                        ],
                      '800' => [
                        'hex' => '#0d6e36',
                        'usage' => 32
                        ],
                      '900' => [
                        'hex' => '#0d5a2e',
                        'usage' => 32
                        ]
                      ]
                    ],
                  'brand-danger' => [
                    'label' => 'Brand Danger',
                    'tints' => [
                      '100' => [
                        'hex' => '#ffdfdf',
                        'usage' => 1
                        ],
                      '400' => [
                        'hex' => '#ff6464',
                        'usage' => 32
                        ],
                      '500' => [
                        'hex' => '#ff2020',
                        'usage' => 32
                        ],
                      '600' => [
                        'hex' => '#ed1515',
                        'usage' => 32
                        ],
                      '650' => [
                        'hex' => '#eb0000',
                        'usage' => 32
                        ],
                      '700' => [
                        'hex' => '#c80d0d',
                        'usage' => 32
                        ],
                      '800' => [
                        'hex' => '#a50f0f',
                        'usage' => 32
                        ],
                      '900' => [
                        'hex' => '#881414',
                        'usage' => 32
                        ],
                      ]
                    ],
                  ];

                  foreach ($colors as $colorKey => $colorDetails) {
                    $tints = $colorDetails['tints'];
                    ?>
                    <h3 class="sg-h3"><?php echo $colorDetails['label'] ?></h3>
                    <div class="sg-color-grid">
                      <?php
                    foreach ($tints as $tintKey => $tintDetails) {
                      ?>
                      <div class="sg-color">
                        <div class="sg-color-swatch" style="background-color: <?php echo $tintDetails['hex']; ?>"></div>
                        <div class="sg-color-name"><?php echo '@color-' . $colorKey . '-' . $tintKey; ?></div>
                        <div class="sg-color-value"><?php echo $tintDetails['hex']; ?></div>
                        <!-- <div class="sg-color-name">Used <?php
                        echo $tintDetails['usage'];
                        ?> time<?php echo $tintDetails['usage'] == 1 ? '' : 's'; ?></div> -->
                      </div>
                      <?php
                    }
                    ?>
                    </div>
                <?php
                  }
                ?>
                </div>
                <!-- Manually add your UI colors here. -->
                <div class="sg-colors sg-section">
                    <h2  class="sg-h2">Neutral Colors</h2>
                    <div class="sg-color-grid">
                        <div class="sg-color">
                            <div class="sg-color-swatch"
                                 style="background-color: #ffffff;"></div>
                            <div class="sg-color-name">@color-true-white</div>
                            <div class="sg-color-value">#ffffff</div>
                            <div class="sg-color-name">Used 45 times</div>
                        </div>
                        <div class="sg-color">
                            <div class="sg-color-swatch"
                                 style="background-color: #f8f8f8;"></div>
                            <div class="sg-color-name">@color-lighter-gray</div>
                            <div class="sg-color-value">#f8f8f8</div>
                            <div class="sg-color-name">Used 20 times</div>
                        </div>
                        <div class="sg-color">
                            <div class="sg-color-swatch"
                                 style="background-color: #e5e5e5;"></div>
                            <div class="sg-color-name">@color-light-gray</div>
                            <div class="sg-color-value">#e5e5e5</div>
                            <div class="sg-color-name">Used 42 times</div>
                        </div>
                        <div class="sg-color">
                            <div class="sg-color-swatch"
                                 style="background-color: #cccccc;"></div>
                            <div class="sg-color-name">@color-medium-gray</div>
                            <div class="sg-color-value">#cccccc</div>
                            <div class="sg-color-name">Used 18 times</div>
                        </div>
                    </div>
                    <div class="sg-color-grid">
                        <div class="sg-color">
                            <div class="sg-color-swatch"
                                 style="background-color: #999;"></div>
                            <div class="sg-color-name">@color-dark-gray</div>
                            <div class="sg-color-value">#999</div>
                            <div class="sg-color-name">Used 13 times</div>
                        </div>
                        <div class="sg-color">
                            <div class="sg-color-swatch"
                                 style="background-color: #656565;"></div>
                            <div class="sg-color-name">@color-darker-gray</div>
                            <div class="sg-color-value">#656565</div>
                            <div class="sg-color-name">Used 16 times</div>
                        </div>
                        <div class="sg-color">
                            <div class="sg-color-swatch"
                                 style="background-color: #333;"></div>
                            <div class="sg-color-name">@color-warm-black</div>
                            <div class="sg-color-value">#333</div>
                            <div class="sg-color-name">Used 18 times</div>
                        </div>
                        <div class="sg-color">
                            <div class="sg-color-swatch"
                                 style="background-color: #000000;"></div>
                            <div class="sg-color-name">@color-true-black</div>
                            <div class="sg-color-value">#000000</div>
                            <div class="sg-color-name">Used 3 times in .result-cell li a and .nav > .dropdown.active > a:hover, .page-title > h4</div>
                        </div>
                    </div>
                </div>
                <div class="sg-colors sg-section">
                    <h2  class="sg-h2">Utility Colors</h2>
                    <div class="sg-color-grid">
                        <div class="sg-color">
                            <div class="sg-color-swatch"
                                 style="background-color: #32bca3;"></div>
                            <div class="sg-color-name">@color-datatype-green</div>
                            <div class="sg-color-value">#32bca3</div>
                        </div>
                        <div class="sg-color">
                            <div class="sg-color-swatch"
                                 style="background-color: #25b6d3;"></div>
                            <div class="sg-color-name">@color-datatype-blue</div>
                            <div class="sg-color-value">#25b6d3</div>
                        </div>
                        <div class="sg-color">
                            <div class="sg-color-swatch"
                                 style="background-color: #f7bb41;"></div>
                            <div class="sg-color-name">@color-datatype-yellow</div>
                            <div class="sg-color-value">#f7bb41</div>
                        </div>
                        <div class="sg-color">
                            <div class="sg-color-swatch"
                                 style="background-color: #e04f5e;"></div>
                            <div class="sg-color-name">@color-datatype-red</div>
                            <div class="sg-color-value">#e04f5e</div>
                        </div>
                    </div>
                    <div class="sg-color-grid">
                        <div class="sg-color">
                            <div class="sg-color-swatch"
                                 style="background-color: #F3FAF6;"></div>
                            <div class="sg-color-name">@color-pale-green</div>
                            <div class="sg-color-value">#F3FAF6</div>
                            <div class="sg-color-name">Used 1 time in .search-box</div>
                        </div>
                        <div class="sg-color">
                            <div class="sg-color-swatch"
                                 style="background-color: #a5cc7e;"></div>
                            <div class="sg-color-name">@color-light-green</div>
                            <div class="sg-color-value">#a5cc7e</div>
                            <div class="sg-color-name">Used 32 times</div>
                        </div>
                        <div class="sg-color">
                            <div class="sg-color-swatch"
                                 style="background-color: #6ea23a;"></div>
                            <div class="sg-color-name">@color-medium-green</div>
                            <div class="sg-color-value">#6ea23a</div>
                            <div class="sg-color-name">Used 33 times</div>
                        </div>
                        <div class="sg-color">
                            <div class="sg-color-swatch"
                                 style="background-color: #5a9c7b;"></div>
                            <div class="sg-color-name">@color-patina-green</div>
                            <div class="sg-color-value">#5a9c7b</div>
                            <div class="sg-color-name">Used 24 times</div>
                        </div>
                    </div>
                    <div class="sg-color-grid">
                        <div class="sg-color">
                            <div class="sg-color-swatch"
                                 style="background-color: #087A38;"></div>
                            <div class="sg-color-name">@color-watercourse-green</div>
                            <div class="sg-color-value">#087A38</div>
                            <div class="sg-color-name">Used 1 time in .search-box</div>
                        </div>
                        <div class="sg-color">
                            <div class="sg-color-swatch"
                                 style="background-color: #005b2d;"></div>
                            <div class="sg-color-name">@color-dark-green</div>
                            <div class="sg-color-value">#005b2d</div>
                            <div class="sg-color-name">Used 22 times</div>
                        </div>
                        <div class="sg-color">
                            <div class="sg-color-swatch"
                                 style="background-color: #08c;"></div>
                            <div class="sg-color-name">@color-medium-blue</div>
                            <div class="sg-color-value">#08c</div>
                            <div class="sg-color-name">Used 4 times in .nav*</div>
                        </div>
                        <div class="sg-color">
                            <div class="sg-color-swatch"
                                 style="background-color: #005580;"></div>
                            <div class="sg-color-name">@color-dark-blue</div>
                            <div class="sg-color-value">#005580</div>
                            <div class="sg-color-name">Used 2 times in .nav-tabs .dropdown-toggle:hover .caret</div>
                        </div>
                    </div>
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

            <div class="sg-section">
              <h2 class="sg-h2" id="sg-contrast">Color contrast</h2>
              <p>The following color combinations have sufficient color contrast and are used in the site. <a href="https://www.w3.org/WAI/WCAG21/Understanding/contrast-minimum.html#dfn-contrast-ratio">Contrast ratio</a> is included for reference.<p>
              <div class="sg-a11y-section">
                <!-- on white -->
                <div class="sg-a11y-block" style="color: #08893e; background-color: #ffffff;">
                  <h3>@gigadb-green-on-white</h3>
                  <p>@color-gigadb-green-750 on @color-true-white (4.51)</p>
                  <p>Commodo non quis mollit nulla quis minim. Ut dolore veniam est duis do aliquip. Id incididunt labore duis minim irure ad amet eu velit.</p>
                </div>
                <div class="sg-a11y-block" style="color: #eb0000; background-color: #ffffff;">
                  <h3>@brand-danger-on-white</h3>
                  <p>@color-brand-danger-650 on @color-true-white (4.63)</p>
                  <p>Commodo non quis mollit nulla quis minim. Ut dolore veniam est duis do aliquip. Id incididunt labore duis minim irure ad amet eu velit.</p>
                </div>
                <div class="sg-a11y-block" style="color: #656565; background-color: #ffffff;">
                  <h3>@dark-gray-on-white</h3>
                  <p>@color-darker-gray on @color-true-white (5.83)</p>
                  <p>Commodo non quis mollit nulla quis minim. Ut dolore veniam est duis do aliquip. Id incididunt labore duis minim irure ad amet eu velit.</p>
                </div>
                <div class="sg-a11y-block" style="color: #333333; background-color: #ffffff;">
                  <h3>@darker-gray-on-white / @warm-black-on-white</h3>
                  <p>@color-warm-black on @color-true-white (12.63)</p>
                  <p>Commodo non quis mollit nulla quis minim. Ut dolore veniam est duis do aliquip. Id incididunt labore duis minim irure ad amet eu velit.</p>
                </div>
                <!-- on lighter-gray -->
                <div class="sg-a11y-block" style="color: #0d6e36; background-color: #f8f8f8;">
                  <h3>@gigadb-green-on-lighter-gray</h3>
                  <p>@color-gigadb-green-800 on @color-lighter-gray (5.99)</p>
                  <p>Commodo non quis mollit nulla quis minim. Ut dolore veniam est duis do aliquip. Id incididunt labore duis minim irure ad amet eu velit.</p>
                </div>
                <div class="sg-a11y-block" style="color: #656565; background-color: #f8f8f8;">
                  <h3>@gray-on-lighter-gray</h3>
                  <p>@color-darker-gray on @color-lighter-gray (5.49)</p>
                  <p>Commodo non quis mollit nulla quis minim. Ut dolore veniam est duis do aliquip. Id incididunt labore duis minim irure ad amet eu velit.</p>
                </div>
                <div class="sg-a11y-block" style="color: #333333; background-color: #f8f8f8;">
                  <h3>@warm-black-on-lighter-gray</h3>
                  <p>@color-warm-black on @color-lighter-gray (11.90)</p>
                  <p>Commodo non quis mollit nulla quis minim. Ut dolore veniam est duis do aliquip. Id incididunt labore duis minim irure ad amet eu velit.</p>
                </div>
                <div class="sg-a11y-block" style="color: #c80d0d; background-color: #f8f8f8;">
                  <h3>@brand-danger-on-lighter-gray</h3>
                  <p>@color-brand-danger-700 on @color-lighter-gray (5.62)</p>
                  <p>Commodo non quis mollit nulla quis minim. Ut dolore veniam est duis do aliquip. Id incididunt labore duis minim irure ad amet eu velit.</p>
                </div>
                <!-- on light gray -->
                <div class="sg-a11y-block" style="color: #656565; background-color: #e5e5e5;">
                  <h3>@dark-gray-on-light-gray</h3>
                  <p>@color-darker-gray on @color-light-gray (4.63)</p>
                  <p>Commodo non quis mollit nulla quis minim. Ut dolore veniam est duis do aliquip. Id incididunt labore duis minim irure ad amet eu velit.</p>
                </div>
                <!-- on gigadb-green -->
                <div class="sg-a11y-block" style="color: #ffffff; background-color: #08893e;">
                  <h3>@white-on-gigadb-green</h3>
                  <p>@color-true-white on @color-gigadb-green-750 (4.51)</p>
                  <p>Commodo non quis mollit nulla quis minim. Ut dolore veniam est duis do aliquip. Id incididunt labore duis minim irure ad amet eu velit.</p>
                </div>
                <!-- on warm-black -->
                <div class="sg-a11y-block" style="color: #333333; background-color: #cccccc;">
                  <h3>@gray-on-warm-black</h3>
                  <p>@color-warm-black on @color-medium-gray (7.87)</p>
                  <p>Commodo non quis mollit nulla quis minim. Ut dolore veniam est duis do aliquip. Id incididunt labore duis minim irure ad amet eu velit.</p>
                </div>
              </div>
              <div class="sg-markup-controls"><a class="sg-btn--top" href="#top">Back to Top</a></div>
            </div>

            <?php renderFilesInFolder('doc'); ?>
        </div><!--/.sg-container-->
    </div><!--/.sg-main-->
</div><!--/.sg-wrapper-->

<!--[if gt IE 8]><!-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.22.0/prism.min.js"></script><!--<![endif]-->
<script src="js/sg-scripts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.4/jquery.min.js" defer></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js" defer></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js" defer></script>
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js" defer></script>
<script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap.min.js" defer></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tag-editor/1.0.20/jquery.tag-editor.min.js" defer></script>
</body>
</html>

