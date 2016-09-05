<?php if (empty($tags)): ?>
    <p class="alert alert-info">
        Sorry, but we couldn't find any art tags associated with any current members.
    </p>
<?php else: ?>
    <p class="well">
        Use the following tags to explore members of the Muncie Arts and Culture Council by their areas of art,
        music, performance, and community activism.
    </p>
    <?php
        function tagTree($tags, $memberTagIds, $htmlHelper) {
            $retval = '';
            $retval .= '<ul>';
            foreach ($tags as $tag) {
                $retval .= '<li>';
                if (in_array($tag->id, $memberTagIds)) {
                    $retval .= $htmlHelper->link(
                        ucfirst($tag->name),
                        [
                            'controller' => 'Tags',
                            'action' => 'view',
                            $tag->slug
                        ]
                    );
                } else {
                    $retval .= ucfirst($tag->name);
                }
                $retval .= '</li>';
                if (!empty($tag->children)) {
                    $retval .= tagTree($tag->children, $memberTagIds, $htmlHelper);
                }
            }
            $retval .= '</ul>';
            return $retval;
        }
    ?>
    <?= tagTree($tags, $memberTagIds, $this->Html) ?>
<?php endif; ?>
