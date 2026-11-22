<h1>Dashboard</h1>
<p class="muted">Hello, <strong><?= e($user['username']) ?></strong> (<?= e($user['role']) ?>).</p>

<div class="stats">
    <a class="stat" href="<?= e(url('vehicles', ['status' => 'available'])) ?>"><span class="num"><?= (int) $vehicles['available'] ?></span>Available vehicles</a>
    <a class="stat" href="<?= e(url('vehicles', ['status' => 'rented'])) ?>"><span class="num"><?= (int) $vehicles['rented'] ?></span>Rented vehicles</a>
    <a class="stat" href="<?= e(url('vehicles', ['status' => 'maintenance'])) ?>"><span class="num"><?= (int) $vehicles['maintenance'] ?></span>In maintenance</a>
    <a class="stat" href="<?= e(url('customers')) ?>"><span class="num"><?= (int) $customers ?></span>Customers</a>
    <a class="stat" href="<?= e(url('rentals', ['status' => 'active'])) ?>"><span class="num"><?= (int) $rentals['active'] ?></span>Active rentals</a>
</div>

<section class="card">
    <div class="toolbar">
        <h2>Recent rentals</h2>
        <a class="btn btn-sm" href="<?= e(url('rental_create')) ?>">+ New rental</a>
    </div>
    <div class="table-wrap">
    <table class="table">
        <thead><tr><th>#</th><th>Vehicle</th><th>Customer</th><th>Dates</th><th>Total</th><th>Status</th></tr></thead>
        <tbody>
        <?php foreach ($recent as $r): ?>
            <tr>
                <td data-label="#"><?= (int) $r['rental_id'] ?></td>
                <td data-label="Vehicle"><?= e($r['model']) ?> (<?= e($r['plate_no']) ?>)</td>
                <td data-label="Customer"><?= e($r['full_name']) ?></td>
                <td data-label="Dates"><?= e($r['start_date']) ?> &rarr; <?= e($r['end_date']) ?></td>
                <td data-label="Total"><?= e(money($r['total_cost'])) ?></td>
                <td data-label="Status"><span class="badge badge-<?= e($r['status']) ?>"><?= e($r['status']) ?></span></td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$recent): ?><tr><td colspan="6" class="muted">No rentals yet.</td></tr><?php endif; ?>
        </tbody>
    </table>
    </div>
</section>
