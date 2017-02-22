<p>
    The W3C HTML validator checks for html validation errors.  It will only report errors and ignores notices.  HTML errors are due to invalid HTML markup in your pages.  They may cause inconsistent rendering and behavior between browsers.
</p>
<p>
    <?php
    $url = $context->options['service_url'];
    if (isset($parent) && $parent->context->getRawObject() instanceof \SiteMaster\Core\Auditor\Site\Page\MetricGrade) {
        $page = $parent->context->getPage();
        $url .= '?doc=' . urlencode($page->uri);
        $url .= '&checkerrorpages=yes';
    }
    ?>
    To find and fix these errors, you can run your page though the <a href="<?php echo $url ?>">W3C HTML validator</a>.
</p>