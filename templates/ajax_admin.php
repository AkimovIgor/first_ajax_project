<div class="col-md-12">
    <!-- Вывод флеш-сообщения в случае успеха -->
    <div class="alert alert-danger" style="display: none;" role="alert">
        <span class="text"></span>
        <button type="button" class="close" aria-label="Close">
            <span aria-hidden="true" <?php if ($_SESSION['theme'] == 'sketchy') echo "style='opacity: 0;'" ?>>&times;</span>
        </button>
    </div>

    <!-- Вывод флеш-сообщения в случае успеха -->
    <div class="alert alert-success" style="display: none;" role="alert">
        <span class="text"></span>
        <button type="button" class="close" aria-label="Close">
            <span aria-hidden="true" <?php if ($_SESSION['theme'] == 'sketchy') echo "style='opacity: 0;'" ?>>&times;</span>
        </button>
    </div>

    <div class="card" style="margin-bottom: 20px;">
        <div class="card-header"><h3>Админ панель</h3></div>

        <div class="card-body" style="overflow-x: auto;">
            <?php if (!empty($paginator['comments'])): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Аватар</th>
                        <th>Имя</th>
                        <th>Дата</th>
                        <th>Комментарий</th>
                        <th>Действия</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($paginator['comments'] as $comment): ?>
                        <tr>
                            <td>
                                <img src="<?= ($comment['image'] == 'no-user.jpg') ? 'markup/img/' . $comment['image'] : 'uploads/' . $comment['image']?>" alt="" class="img-fluid" width="64" height="64">
                            </td>
                            <td><?= $comment['name'] ?></td>
                            <td><?= prettyDate($comment['date']) ?></td>
                            <td><?= $comment['text'] ?></td>
                            <td>
                                <?php if ($comment['status']): ?>
                                    <span data-id="<?= $comment['id'] ?>" class="update btn btn-warning">Запретить</span>
                                <?php else: ?>
                                    <span data-id="<?= $comment['id'] ?>" class="update btn btn-success">Разрешить</span>
                                <?php endif; ?>
                        
                                <span data-id="<?= $comment['id'] ?>" class="delete btn btn-danger">Удалить</span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <!-- Если комментарии в таблице отсутствуют -->
            <?php else: ?>
                <span>Комментариев пока нет.</span>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($paginator['comments'] && $paginator['pageCount'] > 1): ?>
        <div class="col-md-12" id="comments-pagination">
            <ul class="pagination justify-content-center">
                <?php if ($paginator['currentPage'] > 1): ?>
                    <li class="page-item">
                        <span class="page-link" href="<?= $paginator['link'] ?>1" data-href="<?= $paginator['link'] ?>1">&laquo;</span>
                    </li><li class="page-item">
                        <span class="page-link" href="#" data-href="<?= $paginator['link'] ?><?= $paginator['currentPage'] - 1 ?>">&lsaquo;</span>
                    </li>
                <?php endif; ?>

                <?php for ($i = $paginator['start']; $i <= $paginator['end']; $i++): ?>
                    <li class="page-item <?php if ($i == $paginator['currentPage']): ?>active <?php endif; ?>">
                        <span class="page-link" href="#" data-href="<?= $paginator['link'] . $i ?>"><?= $i ?></span>
                    </li>
                <?php endfor; ?>

                <?php if ($paginator['currentPage'] < $paginator['pageCount']): ?>
                    <li class="page-item">
                        <span class="page-link" href="#" data-href="<?= $paginator['link'] ?><?= $paginator['currentPage'] + 1 ?>">&rsaquo;</span>
                    </li>
                    <li class="page-item">
                        <span class="page-link" href="#" data-href="<?= $paginator['link'] ?><?= $paginator['pageCount'] ?>">&raquo;</span>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    <?php endif; ?>
</div>