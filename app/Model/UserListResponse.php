<?php

namespace IRFANM\SIMAHU\Model;

class UserListResponse
{
    public array $users;
    public int $total;
    public int $page;
    public int $perPage;
    public int $totalPages;

    public function __construct(array $users, int $total, int $page, int $perPage)
    {
        $this->users = $users;
        $this->total = $total;
        $this->page = $page;
        $this->perPage = $perPage;
        $this->totalPages = ceil($total / $perPage);
    }
}
