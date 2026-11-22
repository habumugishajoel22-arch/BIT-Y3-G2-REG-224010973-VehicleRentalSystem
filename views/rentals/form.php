<section class="card narrow">
    <h1><?= e($title) ?></h1>
    <form method="post" action="<?= e($action) ?>" id="rental-form" novalidate
          <?= $existing ? 'data-rate="' . e((string) $existing['daily_rate']) . '"' : '' ?>>
        <?= Csrf::field() ?>

        <label for="vehicle_id">Vehicle</label>
        <?php if ($existing): ?>
            <input type="text" value="<?= e($existing['model']) ?> (<?= e($existing['plate_no']) ?>)" disabled>
        <?php else: ?>
            <select id="vehicle_id" name="vehicle_id">
                <option value="">-- choose an available vehicle --</option>
                <?php foreach ($vehicles as $v): ?>
                    <option value="<?= (int) $v['vehicle_id'] ?>" data-rate="<?= e((string) $v['daily_rate']) ?>"
                        <?= (int) $rental['vehicle_id'] === (int) $v['vehicle_id'] ? 'selected' : '' ?>>
                        <?= e($v['model']) ?> (<?= e($v['plate_no']) ?>) - <?= e(money($v['daily_rate'])) ?>/day
                    </option>
                <?php endforeach; ?>
            </select>
        <?php endif; ?>
        <small class="error"><?= e($errors['vehicle_id'] ?? '') ?></small>

        <label for="customer_id">Customer</label>
        <select id="customer_id" name="customer_id">
            <option value="">-- choose a customer --</option>
            <?php foreach ($customers as $c): ?>
                <option value="<?= (int) $c['customer_id'] ?>"
                    <?= (int) $rental['customer_id'] === (int) $c['customer_id'] ? 'selected' : '' ?>>
                    <?= e($c['full_name']) ?> (<?= e($c['license_no']) ?>)
                </option>
            <?php endforeach; ?>
        </select>
        <small class="error"><?= e($errors['customer_id'] ?? '') ?></small>

        <label for="start_date">Start date</label>
        <input type="date" id="start_date" name="start_date" value="<?= e($rental['start_date']) ?>" required>
        <small class="error"><?= e($errors['start_date'] ?? '') ?></small>

        <label for="end_date">End date</label>
        <input type="date" id="end_date" name="end_date" value="<?= e($rental['end_date']) ?>" required>
        <small class="error"><?= e($errors['end_date'] ?? '') ?></small>

        <p class="estimate">Estimated total: <strong id="estimate">-</strong>
            <span class="muted">(the server calculates the final cost)</span></p>

        <button type="submit" class="btn">Save</button>
        <a class="btn btn-light" href="<?= e(url('rentals')) ?>">Cancel</a>
    </form>
</section>
