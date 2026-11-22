<div class="toolbar">
    <h1>Users</h1>
    <a class="btn btn-sm" href="<?= e(url('user_create')) ?>">+ Add user</a>
</div>

<section class="card">
    <div class="table-wrap">
    <table class="table">
        <thead><tr><th>ID</th><th>Username</th><th>Email</th><th>Role</th><th>Created</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($users as $u): ?>
            <tr>
                <td data-label="ID"><?= (int) $u['user_id'] ?></td>
                <td data-label="Username"><?= e($u['username']) ?></td>
                <td data-label="Email"><?= e($u['email']) ?></td>
                <td data-label="Role"><span class="badge badge-<?= e($u['role']) ?>"><?= e($u['role']) ?></span></td>
                <td data-label="Created"><?= e($u['created_at']) ?></td>
                <td class="actions">
                    <a href="<?= e(url('user_edit', ['id' => $u['user_id']])) ?>">Edit</a>
                    <?php if ((int) $u['user_id'] !== (int) Auth::user()['user_id']): ?>
                        <form method="post" action="<?= e(url('user_delete')) ?>" class="inline" data-confirm="Delete this user?">
                            <?= Csrf::field() ?><input type="hidden" name="id" value="<?= (int) $u['user_id'] ?>">
                            <button type="submit" class="link-btn danger">Delete</button>
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
</section>
