<?php
declare(strict_types=1);

class DashboardController
{
    public function index(): void
    {
        render('dashboard', [
            'user'     => Auth::user(),
            'vehicles' => Vehicle::countByStatus(),
            'rentals'  => Rental::countByStatus(),
            'customers' => Customer::count(),
            'recent'   => Rental::recent(5),
        ]);
    }
}
