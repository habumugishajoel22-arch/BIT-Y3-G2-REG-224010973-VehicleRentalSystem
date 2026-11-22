<div class="toolbar">
    <h1>Customers</h1>
    <a class="btn btn-sm" href="<?= e(url('customer_create')) ?>">+ Add customer</a>
</div>

<section class="card">
    <form method="get" action="<?= e(BASE_URL) ?>/index.php" class="filters">
        <input type="hidden" name="page" value="customers">
        <input type="search" name="q" id="live-search" placeholder="Search name, license, phone or email..."
               value="<?= e($q) ?>" autocomplete="off"
               data-api="<?= e(url('api_customers')) ?>"
               data-edit="<?= e(url('customer_edit')) ?>"
               data-delete="<?= e(url('customer_delete')) ?>"
               data-id-field="customer_id" data-noun="customer"
               data-cols="full_name:Name,license_no:License,phone:Phone,email:Email">
        <button type="submit" class="btn btn-sm btn-light">Search</button>
    </form>

    <div class="table-wrap">
    <table class="table">
        <thead><tr><th>Name</th><th>License</th><th>Phone</th><th>Email</th><th>Actions</th></tr></thead>
        <tbody id="results">
        <?php foreach ($customers as $c): ?>
            <tr>
                <td data-label="Name"><?= e($c['full_name']) ?></td>
                <td data-label="License"><?= e($c['license_no']) ?></td>
                <td data-label="Phone"><?= e($c['phone']) ?></td>
                <td data-label="Email"><?= e($c['email']) ?></td>
                <td class="actions">
                    <a href="<?= e(url('customer_edit', ['id' => $c['customer_id']])) ?>">Edit</a>
                    <form method="post" action="<?= e(url('customer_delete')) ?>" class="inline" data-confirm="Delete this customer?">
                        <?= Csrf::field() ?>
                        <input type="hidden" name="id" value="<?= (int) $c['customer_id'] ?>">
                        <button type="submit" class="link-btn danger">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$customers): ?><tr><td colspan="5" class="muted">No customers found.</td></tr><?php endif; ?>
        </tbody>
    </table>
    </div>
</section>
