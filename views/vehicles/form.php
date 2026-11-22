<section class="card narrow">
    <h1><?= e($title) ?></h1>
    <form method="post" action="<?= e($action) ?>" novalidate>
        <?= Csrf::field() ?>

        <label for="plate_no">Plate number</label>
        <input type="text" id="plate_no" name="plate_no" value="<?= e($vehicle['plate_no']) ?>" required maxlength="15">
        <small class="error"><?= e($errors['plate_no'] ?? '') ?></small>

        <label for="model">Model</label>
        <input type="text" id="model" name="model" value="<?= e($vehicle['model']) ?>" required maxlength="80">
        <small class="error"><?= e($errors['model'] ?? '') ?></small>

        <label for="type">Type</label>
        <select id="type" name="type">
            <?php foreach (Vehicle::TYPES as $t): ?>
                <option value="<?= e($t) ?>" <?= $vehicle['type'] === $t ? 'selected' : '' ?>><?= e(ucfirst($t)) ?></option>
            <?php endforeach; ?>
        </select>
        <small class="error"><?= e($errors['type'] ?? '') ?></small>

        <label for="daily_rate">Daily rate</label>
        <input type="number" id="daily_rate" name="daily_rate" step="0.01" min="0.01" value="<?= e((string) $vehicle['daily_rate']) ?>" required>
        <small class="error"><?= e($errors['daily_rate'] ?? '') ?></small>

        <label for="status">Status</label>
        <select id="status" name="status" <?= $statusLocked ? 'disabled' : '' ?>>
            <?php foreach (Vehicle::STATUSES as $s): ?>
                <option value="<?= e($s) ?>" <?= $vehicle['status'] === $s ? 'selected' : '' ?>><?= e(ucfirst($s)) ?></option>
            <?php endforeach; ?>
        </select>
        <?php if ($statusLocked): ?><small class="muted">Rented vehicles change status when the rental is returned.</small><?php endif; ?>
        <small class="error"><?= e($errors['status'] ?? '') ?></small>

        <button type="submit" class="btn">Save</button>
        <a class="btn btn-light" href="<?= e(url('vehicles')) ?>">Cancel</a>
    </form>
</section>
