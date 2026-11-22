<?php
declare(strict_types=1);

class RentalController
{
    public function index(): void
    {
        $q      = trim((string) ($_GET['q'] ?? ''));
        $status = (string) ($_GET['status'] ?? '');
        render('rentals/index', ['rentals' => Rental::all($q, $status), 'q' => $q, 'status' => $status]);
    }

    public function createForm(): void
    {
        $this->showForm('New rental', url('rental_create'), [], [
            'vehicle_id' => intParam('vehicle_id'), 'customer_id' => 0,
            'start_date' => date('Y-m-d'), 'end_date' => date('Y-m-d', strtotime('+1 day')),
        ], null);
    }

    public function store(): void
    {
        Csrf::verifyOrFail();
        [$errors, $d] = $this->validate(true);
        if (!$errors) {
            $err = Rental::create($d['vehicle_id'], $d['customer_id'], $d['start_date'], $d['end_date']);
            if ($err === null) {
                flash('success', 'Rental created.');
                redirect('rentals');
            }
            $errors['vehicle_id'] = $err;
        }
        $this->showForm('New rental', url('rental_create'), $errors, $d, null);
    }

    public function editForm(): void
    {
        $r = $this->findActiveOrAbort();
        $this->showForm('Edit rental #' . $r['rental_id'], url('rental_edit', ['id' => $r['rental_id']]), [], $r, $r);
    }

    public function update(): void
    {
        Csrf::verifyOrFail();
        $r = $this->findActiveOrAbort();
        [$errors, $d] = $this->validate(false);
        if (!$errors) {
            Rental::update($r, $d['customer_id'], $d['start_date'], $d['end_date']);
            flash('success', 'Rental updated.');
            redirect('rentals');
        }
        $this->showForm('Edit rental #' . $r['rental_id'], url('rental_edit', ['id' => $r['rental_id']]), $errors, $d, $r);
    }

    public function returnVehicle(): void
    {
        $this->closeRental('returned', 'Vehicle returned. Rental closed.');
    }

    public function cancel(): void
    {
        $this->closeRental('cancelled', 'Rental cancelled.');
    }

    public function delete(): void // admin only (see router)
    {
        Csrf::verifyOrFail();
        $r = Rental::find((int) ($_POST['id'] ?? 0));
        if (!$r) { abort(404, 'Rental not found'); }
        Rental::delete($r);
        flash('success', 'Rental deleted.');
        redirect('rentals');
    }

    // ---------- helpers ----------
    private function closeRental(string $status, string $message): void
    {
        Csrf::verifyOrFail();
        $r = Rental::find((int) ($_POST['id'] ?? 0));
        if (!$r) { abort(404, 'Rental not found'); }
        if ($r['status'] !== 'active') {
            flash('error', 'Only active rentals can be changed.');
            redirect('rentals');
        }
        Rental::close($r, $status);
        flash('success', $message);
        redirect('rentals');
    }

    private function findActiveOrAbort(): array
    {
        $r = Rental::find(intParam('id'));
        if (!$r) { abort(404, 'Rental not found'); }
        if ($r['status'] !== 'active') {
            flash('error', 'Only active rentals can be edited.');
            redirect('rentals');
        }
        return $r;
    }

    private function showForm(string $title, string $action, array $errors, array $rental, ?array $existing): void
    {
        render('rentals/form', [
            'title' => $title, 'action' => $action, 'errors' => $errors, 'rental' => $rental,
            'existing' => $existing,
            'vehicles' => $existing ? [] : Vehicle::available(),
            'customers' => Customer::all(),
        ]);
    }

    private function validate(bool $isNew): array
    {
        $d = [
            'vehicle_id'  => (int) input('vehicle_id'),
            'customer_id' => (int) input('customer_id'),
            'start_date'  => input('start_date'),
            'end_date'    => input('end_date'),
        ];
        $e = [];
        if ($isNew && $d['vehicle_id'] < 1) {
            $e['vehicle_id'] = 'Choose a vehicle.';
        }
        if ($d['customer_id'] < 1 || !Customer::find($d['customer_id'])) {
            $e['customer_id'] = 'Choose a valid customer.';
        }
        if (!validDate($d['start_date'])) {
            $e['start_date'] = 'Enter a valid start date.';
        } elseif ($isNew && $d['start_date'] < date('Y-m-d')) {
            $e['start_date'] = 'Start date cannot be in the past.';
        }
        if (!validDate($d['end_date'])) {
            $e['end_date'] = 'Enter a valid end date.';
        } elseif (!isset($e['start_date']) && $d['end_date'] < $d['start_date']) {
            $e['end_date'] = 'End date cannot be before the start date.';
        }
        return [$e, $d];
    }
}
