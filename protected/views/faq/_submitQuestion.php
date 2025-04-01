<div class="panel panel-default js-panel-always-visible" id="contact-panel">
    <div class="panel-heading">
        <h2 class="h4 panel-title" id="headingContact">
            <button data-toggle="collapse" data-parent="#accordion" data-target="#panelContact" aria-expanded="false" aria-controls="panelContact">
                Can't Find What You're Looking For?
            </button>
        </h2>
    </div>
    <div id="panelContact" class="panel-collapse collapse" role="region" aria-labelledby="headingContact">
        <div class="panel-body">
            <p>Have you tried our <a href="/site/help">help pages</a>? If you still can't find the answers you are looking for, submit a question to our team here.</p>
            <form id="faqContactForm" method="post">
                <div class="form-group">
                    <label for="contactName">Your Name<span aria-hidden="true"> *</span></label>
                    <input type="text" class="form-control" id="contactName" name="contactName" required aria-required="true">
                </div>
                <div class="form-group">
                    <label for="contactEmail">Your Email Address<span aria-hidden="true"> *</span></label>
                    <input type="email" class="form-control" id="contactEmail" name="contactEmail" required aria-required="true">
                </div>
                <div class="form-group">
                    <label for="contactQuestion">Your Question<span aria-hidden="true"> *</span></label>
                    <textarea class="form-control" id="contactQuestion" name="contactQuestion" rows="4" required aria-required="true"></textarea>
                </div>
                <div class="btns-row btns-row-end">
                    <button type="submit" class="btn background-btn">Submit your question</button>
                </div>
            </form>
        </div>
    </div>
</div>