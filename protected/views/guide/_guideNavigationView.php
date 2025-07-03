<?php

$isActiveDatasetChecklists = !$isActiveGeneral && !$isActiveSupplementalFileGuide;

?>

<nav aria-label="Submission Guidelines" class="guide-nav" id="guideNav">
    <ul class="nav nav-tabs nav-border-tabs">
        <li class="<?= $isActiveGeneral ? 'active' : '' ?>">
            <a href="/site/guide">General Submission Guidelines</a>
        </li>
        <li class="<?= $isActiveSupplementalFileGuide ? 'active' : '' ?>">
            <a href="/site/supplementalFileGuide">Supplemental File Guidelines</a>
        </li>
        <li class="dropdown<?= $isActiveDatasetChecklists ? ' active' : '' ?>" id="dataset-dropdown">
            <button class="dropdown-toggle" aria-haspopup="true" aria-expanded="false" type="button">
                Datasets Checklists&nbsp;
                <i class="fa fa-angle-down" aria-hidden="true"></i>
            </button>
        </li>
    </ul>
    <div class="dropdown-portal">
        <ul class="dropdown-menu" id="dataset-menu" aria-labelledby="dataset-dropdown">
            <?= $menuHtml ?>
        </ul>
    </div>
</nav>

<script>
    $(document).ready(function () {
        const $guideNav = $('#guideNav');
        const $dropdown = $guideNav.find('.dropdown');
        const $toggleButton = $dropdown.find('.dropdown-toggle');
        const $dropdownMenu = $('.dropdown-menu');
        const $dropdownIcon = $toggleButton.find('.fa');

        $toggleButton.on('click', function (e) {
            e.preventDefault();
            e.stopPropagation();

            const isOpen = $dropdown.hasClass('open');

            $dropdown.removeClass('open')
                .find('.dropdown-toggle')
                .attr('aria-expanded', 'false')
                .find('.fa')
                .removeClass('fa-rotate-180');
            $dropdownMenu.removeClass('dropdown-menu-position');

            if (!isOpen) {
                $dropdown.addClass('open');
                $toggleButton.attr('aria-expanded', 'true');
                $dropdownIcon.addClass('fa-rotate-180');
                $dropdownMenu.addClass('dropdown-menu-position');
            }
        });

        $(document).on('click', function (e) {
            if (!$(e.target).closest('.dropdown, .dropdown-menu').length) {
                $dropdown.removeClass('open')
                    .find('.dropdown-toggle')
                    .attr('aria-expanded', 'false')
                    .find('.fa')
                    .removeClass('fa-rotate-180');
                $dropdownMenu.removeClass('dropdown-menu-position');
            }
        });
    });
</script>