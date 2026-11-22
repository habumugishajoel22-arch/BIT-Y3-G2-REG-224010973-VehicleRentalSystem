<div class="toolbar">
    <h1>Rentals</h1>
    <a class="btn btn-sm" href="<?= e(url('rental_create')) ?>">+ New rental</a>
</div>

<section class="card">
    <form method="get" action="<?= e(BASE_URL) ?>/index.php" class="filters">
        <input type="hidden" name="page" value="rentals">
        <input type="search" name="q" placeholder="Search plate, model or customer..." value="<?= e($q) ?>">
        <select name="status">
            <option value="">All statuses</option>
            <?php foreach (Rental::STATUSES as $s): ?>
                <option value="<?= e($s) ?>" <?= $status === $s ? 'selected' : '' ?>><?= e(ucfirst($s)) ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-sm btn-light">Filter</button>
    </form>

    <div class="table-wrap">
    <table class="table">
        <thead><tr><th>#</th><th>Vehicle</th><th>Customer</th><th>Start</th><th>End</th><th>Total</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($rentals as $r): ?>
            <tr>
                <td data-label="#"><?= (int) $r['rental_id'] ?></td>
                <td data-label="Vehicle"><?= e($r['model']) ?> (<?= e($r['plate_no']) ?>)</td>
                <td data-label="Customer"><?= e($r['full_name']) ?></td>
                <td data-label="Start"><?= e($r['start_date']) ?></td>
                <td data-label="End"><?= e($r['end_date']) ?></td>
                <td data-label="Total"><?= e(money($r['total_cost'])) ?></td>
                <td data-label="Status"><span class="badge badge-<?= e($r['status']) ?>"><?= e($r['status']) ?></span></td>
                <td class="actions">
                    <?php if ($r['status'] === 'active'): ?>
                        <a href="<?= e(url('rental_edit', ['id' => $r['rental_id']])) ?>">Edit</a>
                        <form method="post" action="<?= e(url('rental_return')) ?>" class="inline" data-confirm="Mark this vehicle as returned?">
                            <?= Csrf::field() ?><input type="hidden" name="id" value="<?= (int) $r['rental_id'] ?>">
                            <button type="submit" class="link-btn">Return</button>
                        </form>
                        <form method="post" action="<?= e(url('rental_cancel')) ?>" class="inline" data-confirm="Cancel this rental?">
                            <?= Csrf::field() ?><input type="hidden" name="id" value="<?= (int) $r['rental_id'] ?>">
                            <button type="submit" class="link-btn">Cancel</button>
                        </form>
                    <?php endif; ?>
                    <?php if (Auth::user()['role'] === 'admin'): ?>
                        <form method="post" action="<?= e(url('rental_delete')) ?>" class="inline" data-confirm="Delete this rental record permanently?">
                            <?= Csrf::field() ?><input type="hidden" name="id" value="<?= (int) $r['rental_id'] ?>">
                            <button type="submit" class="link-btn danger">Delete</button>
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$rentals): ?><tr><td colspan="8" class="muted">No rentals found.</td></tr><?php endif; ?>
        </tbody>
    </table>
    </div>
</section>
