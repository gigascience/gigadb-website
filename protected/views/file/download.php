<div class="row">
    <h2>Downloading your selection of files</h2>


    <div class="center">
        <p>
            <?php if ( $bundle->check_download_status() ) { ?>
            <h4>
                <?= CHtml::link($bundle->download_url, $bundle->download_url);  ?>
            </h4>
            <?php } else { ?>
                <em>Please reload this page in a moment, your download is being prepared.</em>
                <? var_dump($bundle->status)?>
            <?php } ?>
        </p>
    </div>

</div>
<div class="clear"></div>

<div class="center">
    <a href="/" class="btn-green">GigaDB home</a>
</div>
