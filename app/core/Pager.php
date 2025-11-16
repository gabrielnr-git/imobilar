<?php 

namespace Core;

if (!defined("ROOTPATH")) die("Access Denied");

/**
 * Pager Class
 * for pagination
 */
class Pager
{
    private $limit; // Limit of items to display per page
    private $currentPage; // Current page number
    private $totalPages; // Total number of pages

    // Set up the required data
    public function __construct($totalItems, $limit)
    {
        $this->limit = $limit;
        $this->totalPages = ceil($totalItems / $limit);
        $this->currentPage = $this->getCurrentPage();
    }

    // Get the current page displaying
    public function getCurrentPage():int
    {
        $this->currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        return (int)max(1,min($this->currentPage,$this->totalPages));
    }

    // get the current offset
    public function getOffset():int
    {
        return (int)($this->currentPage - 1) * $this->limit;
    }

    // Display the pagination
    public function display()
    {
        $previous = max(($this->currentPage - 1),1);
        $currentPage = $this->currentPage;
        $next = min(($this->currentPage + 1),$this->totalPages);
        ?>
        <nav class="pager-nav">
            <ul class="pager-ul">
                <?php if ($currentPage > 1):?>
                    <a href="?page=1"><li class="pager-li" id="pager-li-first">Inicio</li></a>
                    <a href="?page=<?=$previous?>"><li class="pager-li"><?=$previous?></li></a>
                <?php endif; ?>
                    <a href="?page=<?=$currentPage?>"><li class="pager-li" id="page-li-current"><?=$currentPage?></li></a>
                <?php if ($currentPage < $this->totalPages): ?>
                    <a href="?page=<?=$next?>"><li class="pager-li"><?=$next?></li></a>
                    <a href="?page=<?=$this->totalPages?>"><li class="pager-li" id="pager-li-next">Fim</li></a>
                <?php endif; ?>
            </ul>
        </nav>
        <?php
    }
}
?>