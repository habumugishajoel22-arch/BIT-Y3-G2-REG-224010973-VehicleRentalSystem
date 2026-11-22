<div class="toolbar">
    <h1>Vehicles</h1>
    <a class="btn btn-sm" href="<?= e(url('vehicle_create')) ?>">+ Add vehicle</a>
</div>

<section class="card">
    <form method="get" action="<?= e(BASE_URL) ?>/index.php" class="filters">
        <input type="hidden" name="page" value="vehicles">
        <input type="search" name="q" id="live-search" placeholder="Search plate, model or type..."
               value="<?= e($q) ?>" autocomplete="off"
               data-api="<?= e(url('api_vehicles')) ?>"
               data-edit="<?= e(url('vehicle_edit')) ?>"
               data-delete="<?= e(url('vehicle_delete')) ?>"
               data-id-field="vehicle_id" data-noun="vehicle"
               data-cols="plate_no:Plate,model:Model,type:Type,daily_rate:Daily rate,status:Status"
               data-filter="#status-filter">
        <select name="status" id="status-filter">
            <option value="">All statuses</option>
            <?php foreach (Vehicle::STATUSES as $s): ?>
                <option value="<?= e($s) ?>" <?= $status === $s ? 'selected' : '' ?>><?= e(ucfirst($s)) ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-sm btn-light">Search</button>
    </form>

    <div class="table-wrap">
    <table class="table">
        <thead><tr><th>Plate</th><th>Model</th><th>Type</th><th>Daily rate</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody id="results">
        <?php foreach ($vehicles as $v): ?>
            <tr>
                <td data-label="Plate"><?= e($v['plate_no']) ?></td>
                <td data-label="Model"><?= e($v['model']) ?></td>
                <td data-label="Type"><?= e($v['type']) ?></td>
                <td data-label="Daily rate"><?= e(money($v['daily_rate'])) ?></td>
                <td data-label="Status"><span class="badge badge-<?= e($v['status']) ?>"><?= e($v['status']) ?></span></td>
                <td class="actions">
                    <a href="<?= e(url('vehicle_edit', ['id' => $v['vehicle_id']])) ?>">Edit</a>
                    <form method="post" action="<?= e(url('vehicle_delete')) ?>" class="inline" data-confirm="Delete this vehicle?">
                        <?= Csrf::field() ?>
                        <input type="hidden" name="id" value="<?= (int) $v['vehicle_id'] ?>">
                        <button type="submit" class="link-btn danger">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$vehicles): ?><tr><td colspan="6" class="muted">No vehicles found.</td></tr><?php endif; ?>
        </tbody>
    </table>
    </div>
</section>
