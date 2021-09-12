<?php declare(strict_types=1);

namespace Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Repositories\Instance;

final class Pagination
{
    private int $total;
    private int $limit;
    private int $page;

    public function __construct(array $params, int $total)
    {
        $this->total = $total;
        $this->limit = (int) $params['limit'];
        $this->page = (int) $params['page'];
    }

    public function realLimit(): int
    {
        return $this->limit ? $this->limit : $this->total;
    }

    public function offset(): int
    {
        return ($this->page - 1) * $this->realLimit();
    }

    private function firstPageElement(): int
    {
        $firstPageElement = $this->offset() + 1;
        if ($firstPageElement > $this->total || $this->pages() === 0) {
            return 0;
        }
        return $firstPageElement;
    }

    private function latestPageElement(): int
    {
        $firstPageElement = $this->firstPageElement();
        if ($firstPageElement === 0) {
            return 0;
        }
        $latestPageElement = $firstPageElement + $this->realLimit();
        return $latestPageElement > $this->total ? $this->total : $latestPageElement;
    }

    private function pages(): int
    {
        return $this->limit ? (int) ceil($this->total / $this->realLimit()) : 1;
    }

    public function toArray(): array
    {
        return [
            'total' => $this->total,
            'limit' => $this->limit,
            'current' => $this->page,
            'pages' => $this->pages(),
            'from' => $this->firstPageElement(),
            'to' => $this->latestPageElement(),
        ];
    }
}
