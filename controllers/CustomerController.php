<?php
declare(strict_types=1);

class CustomerController
{
    public function index(): void
    {
        $q = trim((string) ($_GET['q'] ?? ''));
        render('customers/index', ['customers' => Customer::all($q), 'q' => $q]);
    }

    public function createForm(): void
    {
        render('customers/form', [
            'title' => 'Add customer', 'action' => url('customer_create'), 'errors' => [],
            'customer' => ['full_name' => '', 'license_no' => '', 'phone' => '', 'email' => ''],
        ]);
    }

    public function store(): void
    {
        Csrf::verifyOrFail();
        [$errors, $data] = $this->validate();
        if (!$errors) {
            try {
                Customer::create($data);
                flash('success', 'Customer added.');
                redirect('customers');
            } catch (PDOException $ex) {
                if ($ex->getCode() !== '23000') { throw $ex; }
                $errors['license_no'] = 'That license number already exists.';
            }
        }
        render('customers/form', ['title' => 'Add customer', 'action' => url('customer_create'),
            'errors' => $errors, 'customer' => $data]);
    }

    public function editForm(): void
    {
        $c = $this->findOrAbort();
        render('customers/form', ['title' => 'Edit customer',
            'action' => url('customer_edit', ['id' => $c['customer_id']]), 'errors' => [], 'customer' => $c]);
    }

    public function update(): void
    {
        Csrf::verifyOrFail();
        $existing = $this->findOrAbort();
        [$errors, $data] = $this->validate();
        if (!$errors) {
            try {
                Customer::update((int) $existing['customer_id'], $data);
                flash('success', 'Customer updated.');
                redirect('customers');
            } catch (PDOException $ex) {
                if ($ex->getCode() !== '23000') { throw $ex; }
                $errors['license_no'] = 'That license number already exists.';
            }
        }
        render('customers/form', ['title' => 'Edit customer',
            'action' => url('customer_edit', ['id' => $existing['customer_id']]),
            'errors' => $errors, 'customer' => $data]);
    }

    public function delete(): void
    {
        Csrf::verifyOrFail();
        $id = (int) ($_POST['id'] ?? 0);
        if (!Customer::find($id)) { abort(404, 'Customer not found'); }
        try {
            Customer::delete($id);
            flash('success', 'Customer deleted.');
        } catch (PDOException $ex) {
            if ($ex->getCode() !== '23000') { throw $ex; }
            flash('error', 'Cannot delete: this customer has rental records.');
        }
        redirect('customers');
    }

    private function findOrAbort(): array
    {
        $c = Customer::find(intParam('id'));
        if (!$c) { abort(404, 'Customer not found'); }
        return $c;
    }

    private function validate(): array
    {
        $d = [
            'full_name'  => input('full_name'),
            'license_no' => strtoupper(input('license_no')),
            'phone'      => input('phone'),
            'email'      => input('email'),
        ];
        $e = [];
        if (mb_strlen($d['full_name']) < 2 || mb_strlen($d['full_name']) > 100) {
            $e['full_name'] = 'Full name must be 2-100 characters.';
        }
        if (!preg_match('/^[A-Z0-9-]{3,30}$/', $d['license_no'])) {
            $e['license_no'] = 'License: 3-30 characters (letters, numbers, dash).';
        }
        if (!preg_match('/^\+?[0-9 ]{7,20}$/', $d['phone'])) {
            $e['phone'] = 'Enter a valid phone number (7-20 digits).';
        }
        if ($d['email'] !== '' && (!filter_var($d['email'], FILTER_VALIDATE_EMAIL) || strlen($d['email']) > 120)) {
            $e['email'] = 'Enter a valid email or leave it empty.';
        }
        if ($d['email'] === '') { $d['email'] = null; } // optional column
        return [$e, $d];
    }
}
