<?php
/**
 * @var \CodeIgniter\Pager\PagerRenderer $pager
 */
$pager->setSurroundCount(2);
?>
<div class="btn-group">
    <nav aria-label="<?= lang('Pager.pageNavigation') ?>">
        <ul class="pagination">
            <li class="page-item" data-toggle="tooltip" title="Go to first page">
                <a class="page-link" href="<?= $pager->getFirst();?>" aria-label="<?= lang('Pager.first') ?>">
                    <span aria-hidden="true"><?= lang('Pager.first') ?></span>
                </a>
            </li>
            <?php if ($pager->hasPrevious()) : ?>
                <li class="page-item"  data-toggle="tooltip" title="Go to previous page">
                        <a class="page-link"  href="<?= $pager->getPrevious(); ?>" aria-label="<?= lang('Pager.previous') ?>">
                                <span aria-hidden="true">&laquo;</span>
                        </a>
                </li>                            
            <?php endif ?>
            <?php foreach ($pager->links() as $link) : ?>
                    <li class="page-item <?=$link['active']?'active':'';?>" 
                    <?= $link['active'] ? ' data-toggle="tooltip" data-placement="top" title="Refresh Page" data-original-title="Refresh Page" ' : '' ?>
                    >
                            <a class="page-link" href="<?= $link['uri']; ?>"><?= $link['title'] ?></a>
                    </li>
            <?php endforeach ?>
            <?php if ($pager->hasNext()) : ?>
                <li class="page-item" data-toggle="tooltip" title="Go to next page">
                        <a class="page-link"  href="<?= $pager->getNext(); ?>" aria-label="<?= lang('Pager.next') ?>">
                                <span aria-hidden="true">&raquo;</span>
                        </a>
                </li>
            <?php endif; ?>
            <li class="page-item" data-toggle="tooltip" title="Go to last page">
                <a class="page-link" href="<?= $pager->getLast();?>" aria-label="<?= lang('Pager.last') ?>">
                    <span aria-hidden="true"><?= lang('Pager.last') ?></span>
                </a>
            </li>
        </ul>
    </nav>
</div>