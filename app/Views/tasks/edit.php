<div class="page-container form-page-container">
    <div class="page-header-actions">
        <div>
            <h2 class="page-heading">Vazifani tahrirlash</h2>
            <p class="page-subheading"><?= htmlspecialchars($task['title']) ?> maʼlumotlarini yangilash</p>
        </div>
        <a href="/tasks" class="btn btn-outline">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            Vazifalar roʻyxatiga
        </a>
    </div>

    <div class="card form-card">
        <div class="card-body">
            <form action="/tasks/<?= $task['id'] ?>/update" method="POST" class="standard-form">
                <?= csrf_field() ?>

                <div class="form-group">
                    <label for="title" class="form-label required">Vazifa sarlavhasi</label>
                    <input 
                        type="text" 
                        id="title" 
                        name="title" 
                        class="form-control" 
                        value="<?= htmlspecialchars(old('title', $task['title'])) ?>" 
                        required
                    >
                </div>

                <div class="form-row">
                    <div class="form-group col-6">
                        <label for="due_date" class="form-label required">Bajarish muddati</label>
                        <input 
                            type="date" 
                            id="due_date" 
                            name="due_date" 
                            class="form-control" 
                            value="<?= htmlspecialchars(old('due_date', $task['due_date'])) ?>" 
                            required
                        >
                    </div>

                    <div class="form-group col-6">
                        <label for="priority" class="form-label">Muhimlik darajasi</label>
                        <select id="priority" name="priority" class="form-control">
                            <?php foreach ($priorities as $pk => $pLabel): ?>
                                <option value="<?= $pk ?>" <?= ($task['priority'] === $pk) ? 'selected' : '' ?>><?= $pLabel ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-6">
                        <label for="status" class="form-label">Holat</label>
                        <select id="status" name="status" class="form-control">
                            <?php foreach ($statuses as $sk => $sLabel): ?>
                                <option value="<?= $sk ?>" <?= ($task['status'] === $sk) ? 'selected' : '' ?>><?= $sLabel ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group col-6">
                        <label for="client_id" class="form-label">Mijoz</label>
                        <select id="client_id" name="client_id" class="form-control">
                            <option value="">-- Mijoz tanlanmagan --</option>
                            <?php foreach ($clients as $c): ?>
                                <option value="<?= $c['id'] ?>" <?= ($task['client_id'] == $c['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($c['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-6">
                        <label for="lead_id" class="form-label">Bitim</label>
                        <select id="lead_id" name="lead_id" class="form-control">
                            <option value="">-- Bitim tanlanmagan --</option>
                            <?php foreach ($leads as $l): ?>
                                <option value="<?= $l['id'] ?>" <?= ($task['lead_id'] == $l['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($l['title']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <?php if (\App\Core\Auth::isAdmin() && !empty($managers)): ?>
                        <div class="form-group col-6">
                            <label for="user_id" class="form-label">Masʼul ijrochi</label>
                            <select id="user_id" name="user_id" class="form-control">
                                <?php foreach ($managers as $mgr): ?>
                                    <option value="<?= $mgr['id'] ?>" <?= ($task['user_id'] == $mgr['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($mgr['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="description" class="form-label">Tavsif va qoʻshimcha maʼlumotlar</label>
                    <textarea 
                        id="description" 
                        name="description" 
                        class="form-control" 
                        rows="4"
                    ><?= htmlspecialchars(old('description', $task['description'] ?? '')) ?></textarea>
                </div>

                <div class="form-actions">
                    <a href="/tasks" class="btn btn-outline">Bekor qilish</a>
                    <button type="submit" class="btn btn-primary">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                            <polyline points="17 21 17 13 7 13 7 21"></polyline>
                            <polyline points="7 3 7 8 15 8"></polyline>
                        </svg>
                        <span>Oʻzgarishlarni saqlash</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
