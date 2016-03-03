<p>
    Your profile can be styled using <a href="https://daringfireball.net/projects/markdown/">Markdown</a>
    (specifically a subset of the <a href="http://spec.commonmark.org/">CommonMark spec</a>, if you're curious). Here's how!
</p>

<?php
    $markdownHelper = $this->loadHelper('Gourmet/CommonMark.CommonMark');
    $examples = [
        'Links' => "[Muncie Arts and Culture Council](http://MuncieArts.org)",
        'Italics and Bold' => "This is *italics*. \nSo is _this_.\nAnd both **this** and __this__ is bold.\n\nIf you want to mix bold and italics, *you can do it __like this__*.",
        'Line Breaks' => "Single line breaks\nare normally ignored.\n\nBut double line breaks aren't.\n\nIf you need a single line break, (two spaces go here -->)  \nend a line with two spaces before hitting return.",
        'Headers' => "Large Headers\n=============\nFor large headers, underline the header text with equals-signs.\n\nSmaller Headers\n---------------\nFor smaller headers, use dashes.",
        'Blockquotes' => "Need a blockquote? Well, as Mahatma Gandhi famously said,\n> Do it like this!",
        'Unordered Lists' => "- Start lines\n- With dashes\n- For simple lists\n\nNeed line breaks inside of list items?\n- Make sure  \n  Each item  \n  Is indented.\n- Each of the above lines was ended with\ntwo spaces to generate single line breaks.",
        'Ordered Lists' => "Can't have lists running amok, all unordered.\n1. Here's how\n2. to make\n3. an ordered list."
    ];
?>

<ul>
    <?php foreach ($examples as $header => $example): ?>
        <li>
            <a href="#section-<?= strtolower(str_replace(' ', '-', $header)) ?>">
                <?= $header ?>
            </a>
        </li>
    <?php endforeach; ?>
</ul>

<?php foreach ($examples as $header => $example): ?>
    <section class="markdown-example row" id="section-<?= strtolower(str_replace(' ', '-', $header)) ?>">
        <div class="col-sm-offset-1 col-sm-10">
            <h2>
                <?= $header ?>
            </h2>
            <pre><?= $example ?></pre>
            becomes...
            <div>
                <?= $markdownHelper->convertToHtml($example) ?>
            </div>
        </div>
    </section>
<?php endforeach; ?>