<?php

namespace LunoxHoshizaki\Database;

class Pagination
{
    protected array $items;
    protected int $total;
    protected int $perPage;
    protected int $currentPage;
    protected int $lastPage;

    public function __construct(array $items, int $total, int $perPage, int $currentPage = null)
    {
        $this->items = $items;
        $this->total = $total;
        $this->perPage = $perPage;
        
        $this->currentPage = $currentPage ?: (int) ($_GET['page'] ?? 1);
        if ($this->currentPage < 1) {
            $this->currentPage = 1;
        }

        $this->lastPage = max((int) ceil($total / $perPage), 1);
    }

    public function items(): array
    {
        return $this->items;
    }

    public function total(): int
    {
        return $this->total;
    }

    public function currentPage(): int
    {
        return $this->currentPage;
    }

    public function perPage(): int
    {
        return $this->perPage;
    }

    public function lastPage(): int
    {
        return $this->lastPage;
    }

    /**
     * Render Bootstrap 5 pagination links.
     */
    public function links(): string
    {
        if ($this->lastPage <= 1) {
            return '';
        }

        // Keep current query params except 'page'
        $query = $_GET;
        unset($query['page']);
        $queryString = count($query) > 0 ? '&' . http_build_query($query) : '';
        $path = strtok($_SERVER["REQUEST_URI"], '?');

        $html = '<nav aria-label="Pagination"><ul class="pagination">';
        
        // Previous Button
        if ($this->currentPage > 1) {
            $prevPage = $this->currentPage - 1;
            $html .= "<li class=\"page-item\"><a class=\"page-link\" href=\"{$path}?page={$prevPage}{$queryString}\">&laquo;</a></li>";
        } else {
            $html .= '<li class="page-item disabled"><span class="page-link">&laquo;</span></li>';
        }

        // Page Numbers (Simple formatting showing all pages for now)
        for ($i = 1; $i <= $this->lastPage; $i++) {
            if ($i === $this->currentPage) {
                $html .= "<li class=\"page-item active\"><span class=\"page-link\">{$i}</span></li>";
            } else {
                $html .= "<li class=\"page-item\"><a class=\"page-link\" href=\"{$path}?page={$i}{$queryString}\">{$i}</a></li>";
            }
        }

        // Next Button
        if ($this->currentPage < $this->lastPage) {
            $nextPage = $this->currentPage + 1;
            $html .= "<li class=\"page-item\"><a class=\"page-link\" href=\"{$path}?page={$nextPage}{$queryString}\">&raquo;</a></li>";
        } else {
            $html .= '<li class="page-item disabled"><span class="page-link">&raquo;</span></li>';
        }

        $html .= '</ul></nav>';
        return $html;
    }
}
