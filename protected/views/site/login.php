<?php
$this->pageTitle=Yii::app()->name . ' - Login';
$this->breadcrumbs=array(
	'Login',
);
?>
<section>   

<div class="container" id="login">
    <h3><?=Yii::t('app' , 'Login')?></h3>
	<div class="row">
	<p><?=Yii::t('app' , 'Please fill out the following form with your login credentials:')?></p>
	<p><?=Yii::t('app' , 'Fields with <span class="required">*</span> are required.')?></p>
        <div class="form well" style="height: 180pt">
			<? $form = $this->beginWidget('CActiveForm', array('htmlOptions'=>array('class'=>'form-horizontal'))) ?>
			<div class="form-group">
				<?= $form->labelEx($model,'username', array('class'=>'col-xs-5 control-label')) ?>
				<div class="col-xs-5">
					<?= $form->textField($model,'username',array('size'=>50,'class'=>'form-control')) ?>
					<?php echo $form->error($model,'username'); ?>
				</div>

			</div>

			<div class="form-group">
				<?= $form->labelEx($model,'password', array('class'=>'col-xs-5 control-label')) ?>
				<div class="col-xs-5">
					<?= $form->passwordField($model,'password',array('size'=>50,'class'=>'form-control')) ?>
					<?php echo $form->error($model,'password'); ?>
				</div>
			</div>
			<div class="form-group">
                                <?= $form->label($model,'rememberMe', array('class'=>'col-xs-5 control-label','disabled'=>"disabled")) ?>
                                <div class="col-xs-5"> 
                                 <?= $form->checkBox($model,'rememberMe',array('class'=>'form-control')) ?>
                                </div>
                     
                        </div>    
					
                        <div class="form-group">                
                            <div class="text-right" style="margin-right: 100px">                              
					<?= MyHtml::submitButton(Yii::t('app' ,'Login'), array('class'=>'btn background-btn')) ?>				
                               </div>    
			</div>
		</div><!--form-->

                <p class="pull-left"><?= MyHtml::link(Yii::t('app' , "Lost Password"), array('user/reset', 'username'=>$model->username)) ?> &nbsp;</p>

                <p><?=Yii::t('app' , 'Or login with your preferred identity provider:')?></p>

		<? $this->endWidget() ?>
	</div>

	<div class="span6">

		
		<p>&nbsp;</p>
		<div class="form well well-large">
			<div class="row">
				<div class="span4 offset1">
					<div class="content-btnlog">
					     <a class="btn btnlog facebook-log" href="/opauth/facebook">
					         <img src="/images/icons/fb.png"/>&nbsp;&nbsp;<?=Yii::t('app' , 'Facebook')?>
					     </a>
					    <a class="btn btnlog google-log" href="/opauth/google">
					         <img src="/images/icons/google.png"/>&nbsp;&nbsp;<?=Yii::t('app' , 'Google')?>
					    </a>
					 </div>

					 <div class="content-btnlog">
					    <a class="btn btnlog twitter-log" href="/opauth/twitter">
					         <img src="/images/icons/twi.png"/>&nbsp;&nbsp;<?=Yii::t('app' , 'Twitter')?>
					    </a>
					    <a class="btn btnlog linkedin-log" href="/opauth/linkedin">
					        <img src="/images/icons/in.png"/>&nbsp;&nbsp;<?=Yii::t('app' , 'LinkedIn')?>
					    </a>
					 </div>

					  <div class="content-btnlog">
					    <a class="btn btnlog linkedin-log" href="/opauth/orcid">
					        <img src="/images/icons/id.png"/>&nbsp;&nbsp;<?=Yii::t('app' , 'ORCID')?>
					    </a>
					 </div>
					 <input type="hidden"/>
				 </div>
			</div>
		</div>
	</div>
</div><!--login-->   
</section>  