<?php

use yii\helpers\Html;
use yii\helpers\Url;
use app\models\BotUser;

/** @var yii\web\View $this */
/** @var app\models\User[] $users */
/** @var app\models\User|null $selectedUser */
/** @var yii\data\ActiveDataProvider|null $chatProvider */

$this->title = 'Центр коммуникации';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row g-3">
    <div class="col-12 col-lg-4">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center gap-2">
                <span>Очередь</span>
                <select class="form-select form-select-sm w-auto ms-auto">
                    <option>Статус</option>
                </select>
            </div>
            <div class="list-group list-group-flush" style="max-height:70vh;overflow:auto">
                <?php foreach ($users as $u): ?>
                    <?php $isActive = $selectedUser && $selectedUser->id === $u->id; ?>
                    <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center <?= $isActive ? 'active' : '' ?>"
                       href="<?= Url::to(['index', 'user_id' => $u->id]) ?>">
                        <span><?= Html::encode($u->name ?: ('User #' . $u->id)) ?></span>
                        <span class="badge rounded-pill bg-success-subtle text-success">Новый</span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-8">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span>Клиент</span>
                <?php if ($selectedUser): ?>
                    <button type="button" class="btn btn-sm btn-outline-secondary" title="Информация о клиенте"
                            data-bs-toggle="modal" data-bs-target="#clientInfoModal">…</button>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <?php if ($selectedUser): ?>
                <div class="row g-3">
                    <div class="col-12">
                        <h6>Чат</h6>
                        <div class="border rounded p-2" style="height:300px;overflow:auto;background:#f8f9fa">
                            <?php if ($chatProvider && $chatProvider->getTotalCount()): ?>
                                <?php foreach ($chatProvider->getModels() as $m): ?>
                                    <?php $isBot = (int)$m->is_bot === 1; $text = $isBot ? ($m->out_data ?: $m->tg_out_data) : $m->in_data; ?>
                                    <div class="d-flex mb-2 <?= $isBot ? 'justify-content-end' : '' ?>">
                                        <div class="p-2 rounded <?= $isBot ? 'bg-primary text-white' : 'bg-white border' ?>" style="max-width:75%">
                                            <div class="small text-muted mb-1"><?= date('d.m.Y H:i', strtotime($m->created_at)) ?></div>
                                            <?= nl2br(Html::encode($text)) ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="text-muted">Сообщений пока нет</div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-12">
                        <h6>Информация о клиенте</h6>
                        <table class="table table-sm">
                            <tbody>
                            <tr><th style="width:30%">Имя клиента</th><td><?= Html::encode($selectedUser->name ?: ('User #'.$selectedUser->id)) ?></td></tr>
                            <tr><th>Telegram ID + user id</th><td><?php $bu = $selectedUser->bot_user_id ? BotUser::findOne($selectedUser->bot_user_id) : null; echo $bu ? Html::encode($bu->tg_id.' / '.$selectedUser->id) : Html::encode($selectedUser->id); ?></td></tr>
                            <tr><th>Телефон</th><td><?= Html::encode($selectedUser->phone) ?></td></tr>
                            <tr><th>Email</th><td>—</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php else: ?>
                    <div class="text-muted">Выберите клиента слева, чтобы открыть чат</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php if ($selectedUser): ?>
<!-- Modal: Client Info -->
<div class="modal fade" id="clientInfoModal" tabindex="-1" aria-labelledby="clientInfoModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="clientInfoModalLabel">Информация о клиенте</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <table class="table table-sm">
            <tbody>
            <tr><th style="width:30%">Имя клиента</th><td><?= Html::encode($selectedUser->name ?: ('User #'.$selectedUser->id)) ?></td></tr>
            <tr><th>Telegram ID</th><td><?php $bu = $selectedUser->bot_user_id ? BotUser::findOne($selectedUser->bot_user_id) : null; echo $bu ? Html::encode($bu->tg_id) : '—'; ?></td></tr>
            <tr><th>User ID</th><td><?= Html::encode($selectedUser->id) ?></td></tr>
            <tr><th>Телефон</th><td><?= Html::encode($selectedUser->phone ?: '—') ?></td></tr>
            <tr><th>Статус регистрации</th><td><?= (int)$selectedUser->is_registered ? 'Зарегистрирован' : 'Незарегистрирован' ?></td></tr>
            </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>

