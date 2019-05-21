<?php

class AdminerController extends Controller
{
	public function actionIndex()
	{
	    $this->layout = false;

		$this->render('_adminer');
	}
}
