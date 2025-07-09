<button class="btn btn-link print-view-link" aria-label="Switch to print view">
    <span class="label-text">Print view</span>
</button>
<script>
    $(document).ready(function () {
        const url = new URL(window.location);
        const $btn = $('.print-view-link');
        const $label = $btn.find('.label-text');

        const update = (toPrint) => {
            $('body').toggleClass('print', toPrint);
            $label.text(toPrint ? 'Web view' : 'Print view');
        };

        let isPrint = url.searchParams.get('print') === 'true';
        update(isPrint);

        $btn.on('click', () => {
            isPrint = !isPrint;
            if (isPrint) {
                url.searchParams.set('print', 'true');
            } else {
                url.searchParams.delete('print');
            }
            history.replaceState(null, '', url);
            update(isPrint);
        });
    });
</script>
