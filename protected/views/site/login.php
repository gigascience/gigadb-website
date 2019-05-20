<?php
$this->pageTitle=Yii::app()->name . ' - Login';
$this->breadcrumbs=array(
	'Login',
);
?>
<section>   

<div class="container" id="login">
   <section class="page-title-section">
                <div class="page-title">
                    <ol class="breadcrumb pull-right">
                        <li><a href="/">Home</a></li>                    
                        <li class="active">login</li>
                    </ol>
                    <h4>Login</h4>
                </div>
   </section>
	<div class="subsection row" style="margin-bottom: 130px;">
            <div class="col-xs-6">
                <div class="subsection-login">
                    <p><?=Yii::t('app' , 'Please fill out the following form with your login credentials:')?></p>
                    <p><?=Yii::t('app' , 'Fields with <span class="symbol">*</span> are required.')?></p>
                </div>
               
                 <div class="login-div">
			<? $form = $this->beginWidget('CActiveForm', array('htmlOptions'=>array('class'=>'form-horizontal'))) ?>
			<div class="form-group">
				<?= $form->labelEx($model,'username', array('class'=>'col-xs-3 control-label')) ?>
				<div class="col-xs-9">
					<?= $form->textField($model,'username',array('size'=>50,'class'=>'form-control')) ?>
					<?php echo $form->error($model,'username'); ?>
				</div>

			</div>

			<div class="form-group">
				<?= $form->labelEx($model,'password', array('class'=>'col-xs-3 control-label')) ?>
				<div class="col-xs-9">
					<?= $form->passwordField($model,'password',array('size'=>50,'class'=>'form-control')) ?>
					<?php echo $form->error($model,'password'); ?>
				</div>
			</div>
			<div class="form-group">
                              <div class="col-xs-9" style="float:right;"> 
                                    <div class="checkbox" style="padding-top: 0;">
                                        <?= $form->checkBox($model,'rememberMe') ?>
                                        <?= $form->label($model,'rememberMe', array('disabled'=>"disabled")) ?>  
                                        &nbsp;
                                        <?= CHtml::link(Yii::t('app' , "Lost Password"), array('user/reset', 'username'=>$model->username,'style'=>'float:right')) ?> 
                                         <a href="/user/create" style="float:right;">Create account</a>
                                    </div>
                                </div>
                              
                           
                     
                        </div>    
			<hr>		
                        <div class="button-div">                
                            
                            <?= CHtml::submitButton(Yii::t('app' ,'Login'), array('class'=>'btn background-btn','style'=>'width:236px;hight:40px;float:center;')) ?>	                         
                         
			</div>
		</div><!--form-->
                    <? $this->endWidget() ?>
	</div>

	<div class="col-xs-6">
            <div class="subsection-login">
                        <p>
                            Or login with your preferred identity provider:
                        </p>
                    </div>
            <br>
            <br>
		<div class="ouath-div">
                        <div>
                            <span>
                                <a href="/opauth/facebook">
                                    <img src="/images/new_interface_image/facebook.png">
                                    <span class="logotext">Facebook</span>
                                </a>
                            </span>
                            <span>
                                <a href="/opauth/google">
                                    <img src="/images/new_interface_image/google.png">
                                    <span class="logotext">Google</span>
                                </a>
                            </span>
                            <span>
                                <a href="/opauth/twitter">
                                    <img src="/images/new_interface_image/twitter.png">
                                    <span class="logotext">Twitter</span>
                                </a>
                            </span>
                        </div>
                        <div><!--
                            <span>
                                <a href="/opauth/orcid">
                                    <img src="/images/new_interface_image/orcid.png">
                                    <span class="logotext">ORCID</span>
                                </a>
                            </span>-->
                            <span>
                                <a href="/opauth/linkedin">
                                    <img src="/images/new_interface_image/linkedin.png">
                                    <span class="logotext">LinkedIn</span>
                                </a>
                            </span>
                        </div>
                    </div>
		</div>
	</div>
</div><!--login-->   
</section>  
