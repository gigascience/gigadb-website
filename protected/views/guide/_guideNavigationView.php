<nav aria-label="Submission Guidelines" class="guide-nav">
    <ul class="nav nav-tabs nav-border-tabs">
        <li class="<?= $isActiveGeneral ? 'active' : '' ?>">
            <a href="/site/guide">General Submission Guidelines</a>
        </li>
        <li class="dropdown<?= !$isActiveGeneral ? ' active' : '' ?>">
            <button class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Datasets Checklists&nbsp;
                <i class="fa fa-angle-down" aria-hidden="true"></i>
            </button>
            <ul class="dropdown-menu">
                <?= $menuHtml ?>
            </ul>
        </li>
    </ul>
</nav>