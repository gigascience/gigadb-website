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
        <div class="col-xs-10 col-xs-offset-1">
            <nav class="admin-nav row well" aria-label="admin">
                <!-- Column 1 -->
                <div class="col-md-4 admin-nav-col-container">
                    <div class="well admin-nav-col">
                        <a class="btn background-btn" title="Access all DOIs to update/release" href="/adminDataset/admin">Datasets</a>

                        <a class="btn background-btn" title="Manage authors of datasets" href="/adminDatasetAuthor/admin">Dataset:Authors</a>

                        <a class="btn background-btn" title="Manage sample:DOI associations" href="/adminDatasetSample/admin">Dataset:Samples</a>

                        <a class="btn background-btn" title="Add/update all Files and manage their attributes" tilte="Add/update all Files and manage their attributes" href="/adminFile/admin">Dataset:Files</a>

                        <a class="btn background-btn" title="Manage links from datasets to external project pages" href="/adminDatasetProject/admin">Dataset:Project links</a>

                        <a class="btn background-btn" title="List of external accessions for all DOIs" href="/adminLink/admin">Dataset:Links</a>

                        <a class="btn background-btn" title="Manage DOI:DOI relationships" href="/adminRelation/admin">Dataset:Relations</a>

                        <a class="btn background-btn" title="Manage DOI:Funder" href="/datasetFunder/admin">Dataset:Funder</a>

                        <a class="btn background-btn" href="/adminManuscript/admin" title="Add/update links to manuscriptions citing our DOIs" tilte="Add/update links to manuscriptions citing our DOIs">Dataset:Manuscript</a>
                    </div>
                </div>

                <!-- Column 2 -->
                <div class="col-md-4 admin-nav-col-container">
                    <div class="well admin-nav-col">
                        <a class="btn background-btn" title=" List all authors cited in GigaDB, update ORCID for authors" href="/adminAuthor/admin">Authors</a>

                        <a class="btn background-btn" title="Access all Samples list and update sample details" href="/adminSample/admin">Samples</a>

                        <a class="btn background-btn" title="Add/update all Species and manage their attributes" href="/adminSpecies/admin">Species</a>

                        <a class="btn background-btn" title="Add/update list of external projects linked to from datasets" href="/adminProject/admin">Projects</a>

                        <a class="btn background-btn" title="Add/update links to genome browsers and related links" href="/adminExternalLink/admin">External Links</a>

                        <a class="btn background-btn" title="Add/update prefixes of links supported by GigaDB" href="/adminLinkPrefix/admin">Link Prefixes</a>

                        <a class="btn background-btn" title="Add/update funder" href="/funder/admin">Funder</a>

                        <a class="btn background-btn" title="Add/update attribute" href="/attribute/admin">Attribute</a>

                        <a class="btn background-btn" title="Add/update list of logs" href="/report/index">Google Analytics</a>
                    </div>
                </div>

                <!-- Column 3 -->
                <div class="col-md-4 admin-nav-col-container">
                    <div class="well admin-nav-col">
                        <a class="btn background-btn" title="Add/update types of datasets supported by GigaDB" href="/adminDatasetType/admin">Dataset Types</a>

                        <a class="btn background-btn" title="Add/update types of files supported by GigaDB" href="/adminFileType/admin">Data Types</a>

                        <a class="btn background-btn" title="Add/update formats of files supported by GigaDB" href="/adminFileFormat/admin">File Formats</a>

                        <a class="btn background-btn" title="Manage GigaDB user accounts" href="/user/admin">Users</a>

                        <a class="btn background-btn" href="/user/newsletter">Newsletter Subscribers</a>

                        <a class="btn background-btn" title="Manage GigaDB news items to show on home page" href="/news/admin">News Items</a>

                        <a class="btn background-btn" title="manage RSS feed items" href="/rssMessage/admin">RSS Messages</a>

                        <a class="btn background-btn" title="Add/update list of publishers" href="/adminPublisher/admin">Publishers</a>

                        <a class="btn background-btn" title="Add/update list of logs" href="/datasetLog/admin">Update Logs</a>
                    </div>
                </div>
            </nav>


        </div>
        </td>
        </tr>
        </table>

    </div>
</div>
</td>
</tr>
</table>

</div>
</div>
</td>
</tr>
</table>

</div>
</div>
</div>

</div>