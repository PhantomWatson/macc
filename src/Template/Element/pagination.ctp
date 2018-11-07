<?php
/**
 * @var \App\View\AppView $this
 */
    if ($this->Paginator->hasPrev() || $this->Paginator->hasNext()) {
        echo '<ul class="pagination">';
    }
    if ($this->Paginator->hasPrev()) {
        echo $this->Paginator->prev('< prev');
    }
    echo $this->Paginator->numbers([
        'currentClass' => 'active',
        'currentTag' => 'a',
        'separator' => '',
        'tag' => 'li'
    ]);
    if ($this->Paginator->hasNext()) {
        echo $this->Paginator->next('next >');
    }
    if ($this->Paginator->hasPrev() || $this->Paginator->hasNext()) {
        echo '</ul>';
    }