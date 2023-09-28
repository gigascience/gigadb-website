<div class="clear"></div>
<div class="container">
    <?php
        $this->widget('application.components.TitleBreadcrumb', [
            'pageTitle' => 'Administration Page',
            'breadcrumbItems' => [
                ['isActive' => true, 'label' => 'Admin'],
            ]
        ]);
    ?>
    <div class="row">
        <div class="span8 offset2">
            <div class="form well">
                <table class="admin">
                    <tr>
                        <td style="vertical-align: top;">

                            <div class="form well height1">

                                <a class="btn background-btn left2" title="Access all DOIs to update/release" href="/adminDataset/admin">Datasets</a>

                                <div class="clear"></div>

                                <a class="btn background-btn left2" title="Manage authors of datasets" href="/adminDatasetAuthor/admin">Dataset:Authors</a>


                                <div class="clear"></div>

                                <a class="btn background-btn left2" title="Manage sample:DOI associations" href="/adminDatasetSample/admin">Dataset:Samples</a>

                                <div class="clear"></div>

                                <a class="btn background-btn left2" title="Add/update all Files and manage their attributes" tilte="Add/update all Files and manage their attributes" href="/adminFile/admin">Dataset:Files</a>


                                <div class="clear"></div>

                                <a class="btn background-btn left2" title="Manage links from datasets to external project pages" href="/adminDatasetProject/admin">Dataset:Project links</a>

                                <div class="clear"></div>


                                <a class="btn background-btn left2" title="List of external accessions for all DOIs" href="/adminLink/admin">Dataset:Links</a>

                                <div class="clear"></div>

                                <a class="btn background-btn left2" title="Manage DOI:DOI relationships" href="/adminRelation/admin">Dataset:Relations</a>

                                <div class="clear"></div>

                                <a class="btn background-btn left2" title="Manage DOI:Funder" href="/datasetFunder/admin">Dataset:Funder</a>

                                <div class="clear"></div>

                                <a class="btn background-btn left2" href="/adminManuscript/admin"  title="Add/update links to manuscriptions citing our DOIs" tilte="Add/update links to manuscriptions citing our DOIs" >Dataset:Manuscript</a>

                                <!--
                                <div class="clear"></div>

                                <a class="btn background-btn left2" title="Fairly use policy" href="/policy/create">Fairly Use Policy</a> -->


                                <div class="clear"></div>



    <!--                            <a class="btn background-btn left2" title="Manualy create a new dataset via web-form" href="/adminDataset/create">Create Dataset</a>

    -->



                            </div>

                        </td>
                        <td style="vertical-align: top;">
                            <div class="form well height1">

                                <a class="btn background-btn left2" title=" List all authors cited in GigaDB, update ORCID for authors" href="/adminAuthor/admin">Authors</a>

                                <div class="clear"></div>

                                <a class="btn background-btn left2" title="Access all Samples list and update sample details" href="/adminSample/admin">Samples</a>

                                <div class="clear"></div>

                                <a class="btn background-btn left2" title="Add/update all Species and manage their attributes" href="/adminSpecies/admin">Species</a>

                                <div class="clear"></div>

                                <a class="btn background-btn left2" title="Add/update list of external projects linked to from datasets" href="/adminProject/admin">Projects</a>

                                <div class="clear"></div>

                                <a class="btn background-btn left2" title="Add/update links to genome browsers and related links" href="/adminExternalLink/admin">External Links</a>

                                <div class="clear"></div>

                                <a class="btn background-btn left2" title="Add/update prefixes of links supported by GigaDB" href="/adminLinkPrefix/admin">Link Prefixes</a>

                                <div class="clear"></div>

                                <a class="btn background-btn left2" title="Add/update funder" href="/funder/admin">Funder</a>
                                <div class="clear"></div>

                                <a class="btn background-btn left2" title="Add/update attribute" href="/attribute/admin">Attribute</a>

                                <div class="clear"></div>

                                <a class="btn background-btn left2" title="Add/update list of logs" href="/report/index">Google Analytics</a>
                            </div>

                        </td>
                        <td style="vertical-align: top;">


                            <div class="form well height1">

                                <a class="btn background-btn left2" title="Add/update types of datasets supported by GigaDB" href="/adminDatasetType/admin">Dataset Types</a>


                                <div class="clear"></div>


                                <a class="btn background-btn left2" title="Add/update types of files supported by GigaDB" href="/adminFileType/admin">Data Types</a>


                                <div class="clear"></div>




                                <a class="btn background-btn left2" title="Add/update formats of files supported by GigaDB" href="/adminFileFormat/admin">File Formats</a>

                                <div class="clear"></div>



                                <a class="btn background-btn left2" title="Manage GigaDB user accounts" href="/user/admin">Users</a>

                                <div class="clear"></div>

                                <a class="btn background-btn left2" title="Newsletter Subscribers" href="/user/newsletter">Newsletter Subscribers</a>

                                <div class="clear"></div>


                                <a class="btn background-btn left2" title="Manage GigaDB news items to show on home page" href="/news/admin" >News Items</a>

                                <div class="clear"></div>




                                <a class="btn background-btn left2" title="manage RSS feed items" href="/rssMessage/admin">RSS Messages</a>

                                <div class="clear"></div>
                                <a class="btn background-btn left2" title="Add/update list of publishers" href="/adminPublisher/admin">Publishers</a>

                                <div class="clear"></div>
                                <a class="btn background-btn left2" title="Add/update list of logs" href="/datasetLog/admin">Update Logs</a>

                            </div>
                        </td>
                    </tr>
                </table>

            </div>
        </div>
    </div>

</div>