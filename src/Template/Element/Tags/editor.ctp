<?php
	$this->Html->script('tag_manager.js', ['block' => 'script']);
?>

<div class="input" id="tag_editing">
	<div id="available_tags_container">
		<div id="available_tags"></div>
		<div id="popular_tags"></div>
	</div>
	<div class="footnote">
		Click <span class="glyphicon glyphicon-triangle-right"></span> to expand groups.
		Click
		<a href="#" title="Selectable tags will appear in blue" id="example_selectable_tag">selectable tags</a>
		to select them.
		Click on a selected tag to unselect it.
	</div>

	<div id="selected_tags_container" style="display: none;">
		<strong>
			Selected tags:
		</strong>
		<span id="selected_tags"></span>
	</div>
</div>

<?php if (! empty($selectedTags)): ?>
    <?php $this->append('buffered'); ?>
        TagManager.selected_tags = <?= json_encode($this->Tag->selectedTagsForJs($selectedTags)) ?>;
        TagManager.preselectTags(TagManager.selected_tags);
    <?php $this->end(); ?>
<?php endif; ?>

<?php $this->append('buffered'); ?>
    TagManager.tags = <?= json_encode($this->Tag->availableTagsForJs($availableTags)) ?>;
    TagManager.createTagList(TagManager.tags, $('#available_tags'));
    $('#new_tag_rules_toggler').click(function(event) {
        event.preventDefault();
        $('#new_tag_rules').slideToggle(200);
    });
<?php $this->end(); ?>
