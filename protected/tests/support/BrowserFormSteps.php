<?php

/**
 * browser automation steps for filling in forms
 *
 * This trait is to be used in functional tests
 * This trait's function is to ensure browser mediation is performed only in one place as much as possible.
 * Making it easier to change browser mediation framework as needed
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
trait BrowserFormSteps
{

	public function fillReportIndexForm()
	{
		// fill the form needed for the report
        $this->session->getPage()->fillField("Report_start_date", "2018-09-01");
        $this->session->getPage()->fillField("Report_end_date", "2018-09-30");
        $this->session->getPage()->selectFieldOption("Report_ids", "all");
        $this->session->getPage()->pressButton("View");
	}

	public function fillDatasetUpdateFormJustKeywords($input)
	{
		 // Add keywords and submit the form
        $this->session->getPage()->fillField("keywords", $input);
        $this->session->getPage()->pressButton("Save");
	}

	public function fillDatasetManagementFormJustKeywords($input)
	{
		 // Add keywords and submit the form
        $this->session->getPage()->fillField("keywords", $input);
		$this->session->getPage()->checkField("Images[is_no_image]");
        $this->session->getPage()->pressButton("Next");
	}

	public function fillDatasetCreate1FormDummyFieldsJustKeywords($input)
	{
        // Add keywords and submit the form
        $this->session->getPage()->fillField("Dataset_title", "dummy");
        $this->session->getPage()->fillField("Dataset_dataset_size", "4500");
        $this->session->getPage()->fillField("Images_source", "ftp://blah");
        $this->session->getPage()->fillField("Images_license", "CC0");
        $this->session->getPage()->fillField("Images_photographer", "me");
        $this->session->getPage()->fillField("keywords", $input);
        $this->session->getPage()->checkField("Images[is_no_image]");
        $this->session->getPage()->pressButton("Next");
	}


}
?>