<?php
declare(strict_types=1);

class VehicleController
{
    public function index(): void
    {
        $q      = trim((string) ($_GET['q'] ?? ''));
        $status = (string) ($_GET['status'] ?? '');
        render('vehicles/index', ['vehicles' => Vehicle::all($q, $status), 'q' => $q, 'status' => $status]);
    }

    public function createForm(): void
    {
        render('vehicles/form', [
            'title' => 'Add vehicle', 'action' => url('vehicle_create'), 'errors' => [],
            'vehicle' => ['plate_no' => '', 'model' => '', 'type' => 'car', 'daily_rate' => '', 'status' => 'available'],
            'statusLocked' => false,
        ]);
    }

    public function store(): void
    {
        Csrf::verifyOrFail();
        [$errors, $data] = $this->validate(null);
        if (!$errors) {
            try {
                Vehicle::create($data);
                flash('success', 'Vehicle added.');
                redirect('vehicles');
            } catch (PDOException $ex) {
                if ($ex->getCode() !== '23000') { throw $ex; }
                $errors['plate_no'] = 'That plate number already exists.';
            }
        }
        render('vehicles/form', ['title' => 'Add vehicle', 'action' => url('vehicle_create'),
            'errors' => $errors, 'vehicle' => $data, 'statusLocked' => false]);
    }

    public function editForm(): void
    {
        $vehicle = $this->findOrAbort();
        render('vehicles/form', [
            'title' => 'Edit vehicle', 'action' => url('vehicle_edit', ['id' => $vehicle['vehicle_id']]),
            'errors' => [], 'vehicle' => $vehicle, 'statusLocked' => $vehicle['status'] === 'rented',
        ]);
    }

    public function update(): void
    {
        Csrf::verifyOrFail();
        $existing = $this->findOrAbort();
        [$errors, $data] = $this->validate($existing);
        if (!$errors) {
            try {
                Vehicle::update((int) $existing['vehicle_id'], $data);
                flash('success', 'Vehicle updated.');
                redirect('vehicles');
            } catch (PDOException $ex) {
                if ($ex->getCode() !== '23000') { throw $ex; }
                $errors['plate_no'] = 'That plate number already exists.';
            }
        }
        render('vehicles/form', ['title' => 'Edit vehicle',
            'action' => url('vehicle_edit', ['id' => $existing['vehicle_id']]),
            'errors' => $errors, 'vehicle' => $data, 'statusLocked' => $existing['status'] === 'rented']);
    }

    public function delete(): void
    {
        Csrf::verifyOrFail();
        $id = (int) ($_POST['id'] ?? 0);
        if (!Vehicle::find($id)) { abort(404, 'Vehicle not found'); }
        try {
            Vehicle::delete($id);
            flash('success', 'Vehicle deleted.');
        } catch (PDOException $ex) {
            if ($ex->getCode() !== '23000') { throw $ex; }
            flash('error', 'Cannot delete: this vehicle has rental records.');
        }
        redirect('vehicles');
    }

    private function findOrAbort(): array
    {
        $vehicle = Vehicle::find(intParam('id'));
        if (!$vehicle) { abort(404, 'Vehicle not found'); }
        return $vehicle;
    }

    private function validate(?array $existing): array
    {
        $d = [
            'plate_no'   => strtoupper(input('plate_no')),
            'model'      => input('model'),
            'type'       => input('type'),
            'daily_rate' => input('daily_rate'),
            'status'     => input('status'),
        ];
        // A rented vehicle keeps its status (changed only by returning the rental)
        if ($existing && $existing['status'] === 'rented') { $d['status'] = 'rented'; }

        $e = [];
        if (!preg_match('/^[A-Z0-9 ]{3,15}$/', $d['plate_no'])) {
            $e['plate_no'] = 'Plate: 3-15 characters, letters/numbers/spaces.';
        }
        if (mb_strlen($d['model']) < 2 || mb_strlen($d['model']) > 80) {
            $e['model'] = 'Model must be 2-80 characters.';
        }
        if (!in_array($d['type'], Vehicle::TYPES, true)) {
            $e['type'] = 'Choose a valid type.';
        }
        if (!is_numeric($d['daily_rate']) || (float) $d['daily_rate'] <= 0 || (float) $d['daily_rate'] > 99999999) {
            $e['daily_rate'] = 'Daily rate must be a positive number.';
        }
        if (!in_array($d['status'], Vehicle::STATUSES, true)) {
            $e['status'] = 'Choose a valid status.';
        }
        return [$e, $d];
    }
}
