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
		<p>Fields with <span class="symbol">*</span> are required.</p>
            <div class="reset-message-div">
                <p>
                    If you have lost your password, enter your email and we will send a new password to the email address associated with your account.
                </p>
            </div>
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
                        </div>
                    </div>
                    <div class="button-div">
                        <?= CHtml::submitButton(Yii::t('app' , 'Reset') , array('class'=>'btn background-btn')) ?>
                    </div>
                <? $this->endWidget() ?>
            </div>
	    </div>
    </div>
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