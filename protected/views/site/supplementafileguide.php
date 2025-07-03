<?php
$this->pageTitle = 'GigaDB - Supplemental File Guidelines'; ?>

<div class="content mb-30">
  <div class="container">
    <?php
        $this->widget('TitleBreadcrumb', ['pageTitle' => 'GigaDB -
    Supplemental File Guidelines', 'breadcrumbItems' => [['label' => 'Home',
        'href'                                                            => '/'], ['isActive' => true, 'label' => 'Supplemental File
    Guidelines', ]]]); ?>
    <section>
      <?php
      $this->widget('GuideNavigation'); ?>

      <section>
        <div class="tab-content">
          <div class="tab-pane active">
            <h2 class="page-subtitle h4">Supplemental File Guidelines</h2>

            <div class="subsection">
              <p>Below is a list of things commonly found in supplemental files and how we would recommend they be represented.</p>

              <dl class="definition-list">
                <dt>Raw data files</dt>
                <dd>
                  <p>
                    These should be submitted to recognized community
                    repositories if available, otherwise they should be
                    submitted to GigaDB as part of the associated dataset.
                  </p>
                </dd>

                <dt>Additional methods</dt>
                <dd>
                  <p>
                    These should be translated into an online methods tool such
                    as protocols.io and cited in the main manuscript.
                  </p>
                </dd>

                <dt>Additional details of analysis</dt>
                <dd>
                  <p>
                    These should be translated into an online methods tool such
                    as protocols.io and cited in the main manuscript.
                  </p>
                </dd>

                <dt>Scripts and/or bespoke analysis code</dt>
                <dd>
                  <p>
                    If appropriate these could be uploaded to a code repository
                    such as GitHub and cited in the manuscript, otherwise they
                    should be submitted to GigaDB as part of the associated
                    dataset.
                  </p>
                </dd>

                <dt>Tables of analysis results</dt>
                <dd>
                  <p>
                    These should be converted to a machine readable format and
                    uploaded to GigaDB as part of the associated dataset.
                  </p>
                </dd>

                <dt>Summary Tables of analysis results</dt>
                <dd>
                  <p>
                    You should consider the utility of these carefully, if they
                    are required as part of the narrative of your manuscript
                    they should be included in the manuscript. We would not
                    expect to see these as data files in GigaDB, since they can
                    be re-created from the actual data files.
                  </p>
                </dd>

                <dt>Lists of things</dt>
                <dd>
                  <p>
                    You should consider the utility of these carefully, if they
                    are required as part of the narrative of your manuscript
                    they should be included in the manuscript. If they are large
                    lists of results or input data/accessions they should be
                    converted to a machine readable format and uploaded to
                    GigaDB as part of the associated dataset.
                  </p>
                </dd>

                <dt>Lists of PCR primers</dt>
                <dd>
                  <p>
                    These should be included in the relevant experimental
                    methods and submitted to online methods tool such as
                    protocols.io and cited in the main manuscript.
                  </p>
                </dd>

                <dt>Lists of BLAST hits</dt>
                <dd>
                  <p>
                    The full results files should be uploaded to GigaDB as
                    machine readable data files as part of the associated
                    dataset. If a summary of results is required you should
                    consider the utility of these carefully, if they are
                    essential as part of the narrative of your manuscript they
                    should be included in the manuscript, no summary file should
                    be included in the associated dataset.
                  </p>
                </dd>

                <dt>Diagrams of the experiment</dt>
                <dd>
                  <p>
                    You should consider the utility of these carefully, if they
                    are required as part of the narrative of your manuscript
                    they should be included in the manuscript. We would not
                    expect to see these as data files in GigaDB, since they are
                    not data that can be reused in any way. If a workflow is
                    required, it should be translated into a machine readable
                    workflow (e.g. in Common Workflow Language, CWL) and
                    uploaded to GigaDB as part of the associated dataset.
                  </p>
                </dd>

                <dt>Images of gels</dt>
                <dd>
                  <p>
                    The original images (un-manipulated and un-annotated) should
                    be uploaded to GigaDB as part of the associated dataset. We
                    would not normally expect to see "manuscript-ready" versions
                    of a figure of the manipulated images in GigaDB, if you
                    believe they are required as part of the narrative then they
                    should be included in the main manuscript.
                  </p>
                </dd>

                <dt>
                  Images (e.g of subjects/samples, microscopy slides, etc.)
                </dt>
                <dd>
                  <p>
                    The original images (un-manipulated and un-annotated) should
                    be uploaded to GigaDB as part of the associated dataset. We
                    would not normally expect to see a "manuscript-ready"
                    versions of a figure of the manipulated images in GigaDB,
                    however we understand that there maybe some scenarios where
                    the authors may wish to provide composite images such as
                    multiple staining events overlaid, these should be included
                    in GigaDB together with the original separate channel
                    images.
                  </p>
                </dd>

                <dt>Further discussion of results</dt>
                <dd>
                  <p>
                    You should consider the utility of these carefully, if they
                    are required as part of the narrative of your manuscript
                    they should be included in the manuscript. We would not
                    expect to see these as data files in GigaDB, since they are
                    not "data" that can be reused in any way.
                  </p>
                </dd>

                <dt>
                  Visualizations of data<sup id="fn-ref-1"
                    ><a href="#fn-1" aria-label="See footnote 1">1</a></sup
                  >
                </dt>
                <dd>
                  <p>
                    You should consider the utility of these carefully, if they
                    are required as part of the narrative of your manuscript
                    they should be included in the manuscript. We would not
                    expect to see these as data files in GigaDB, since they are
                    not "data" that can be reused in any way. However, we would
                    expect to see the data files that are being visualized
                    (unless they are already hosted in external stable
                    repositories such as INSDC).
                  </p>
                </dd>
              </dl>

              <p class="footnote" id="fn-1">
                <sup>1</sup> Visualization of data includes (but is not limited
                to) things like Gene orientation diagrams, Circos plots, graphs,
                networks, heatmaps, geographical maps showing locations,
                phylogenetic trees, HiC contact maps, Venn diagrams, alignment
                maps, karyotype diagrams, etc...
                <a href="#fn-ref-1" aria-label="Back to reference">↩︎</a>
              </p>
            </div>
          </div>
        </div>
      </section>
    </section>
  </div>
</div>
