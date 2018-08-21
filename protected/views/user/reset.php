 <div class="content">
        <div class="container">
              <section class="page-title-section">
                <div class="page-title">
                    <ol class="breadcrumb pull-right">
                        <li><a href="/">Home</a></li>
                        <li class="active">Reset</li>
                    </ol>
                    <h4>Reset Password</h4>
                </div>
            </section>
        <div class="subsection" style="margin-bottom: 130px;">
	
		<div class="clear"></div>
		
		<p>Fields with <span class="symbol">*</span> are required.</p>
		<div class="create-div">
			<? $form=$this->beginWidget('CActiveForm', array(
				'id'=>'user-form',
				'enableAjaxValidation'=>false,
				'htmlOptions'=>array('class'=>'form-horizontal')
			)) ?>
				 <div class="form-group">
					<?= $form->labelEx($model,'email', array('class'=>'col-xs-3 control-label')) ?>
					<div class="col-xs-9">
						<?= $form->textField($model,'email',array('class'=>'form-control')) ?>
						<font color="red"><?= $form->error($model,'email') ?></font>
					</div>
				</div>

                                <div class="form-group">
                                    <label class="col-xs-3 control-label"><?=Yii::t('app' , 'Mailing list')?></label>
                                   
				    <div class="col-xs-9">				    	
                                         <?php echo $form->checkbox($model,'newsletter'); ?>
				    </div>
                                    <div class="col-xs-9">
                                        <p>Please tick here to join the GigaDB mailing list to receive news, updates and quarterly newsletters about GigaDB</p>   
                                    </div>
                                 </div>
                                <div class="form-group">
                                    <?= $form->labelEx($model,'terms', array('class'=>'col-xs-3 control-label')) ?>              
				    <div class="col-xs-9">				    	
                                         <?php echo $form->checkbox($model,'terms'); ?>
                                         <font color="red"><?= $form->error($model,'terms') ?></font>
				    </div>
                                    <div class="col-xs-9">
                                     <p>Please tick here to confirm you have read and understood our <a href="/site/term#policies">Terms of use</a> and <a href="/site/term#privacy">Privacy Policy</a></p>
                                    </div>
                                 </div>
                                


			
                        <hr>
                            <div class="button-div">
                                <?= MyHtml::submitButton(Yii::t('app' , 'Reset') , array('class'=>'btn background-btn')) ?>
                            </div>
                        <? $this->endWidget() ?>
		</div><!--well-->
		


	
	</div><!--span8-->
    </div><!-- user-form -->
</div>

<script type="text/javascript">

  function checkForm(form)
  {
    ...
    if(!form.User_terms.checked) {
      alert("Please indicate that you accept the Terms and Conditions");
      form.terms.focus();
      return false;
    }
    return true;
  }

</script>