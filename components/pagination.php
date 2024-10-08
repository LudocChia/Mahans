<div class="pagination">
    <div class="page-info">
        <?php
        if (!isset($_GET['page-nr'])) {
            $page = 1;
        } else {
            $page = (int)$_GET['page-nr'];
        }
        ?>
    </div>

    <a href="?page-nr=1" class="<?php echo $page == 1 ? 'disabled' : ''; ?>"><i class="bi bi-chevron-bar-left"></i></a>
    <a href="?page-nr=<?= $page > 1 ? $page - 1 : 1 ?>" class="<?php echo $page == 1 ? 'disabled' : ''; ?>"><i class="bi bi-chevron-left"></i></a>

    <div class="page-numbers">
        <?php
        for ($counter = 1; $counter <= $pageCount; $counter++) { ?>
            <a href="?page-nr=<?= $counter; ?>" class="<?= $counter == $page ? 'active' : ''; ?>"><?= $counter; ?></a>
        <?php
        }
        ?>
    </div>

    <a href="?page-nr=<?= $page < $pageCount ? $page + 1 : $pageCount ?>" class="<?php echo $page == $pageCount ? 'disabled' : ''; ?>"><i class="bi bi-chevron-right"></i></a>

    <a href="?page-nr=<?= $pageCount ?>" class="<?php echo $page == $pageCount ? 'disabled' : ''; ?>"><i class="bi bi-chevron-bar-right"></i></a>
</div>