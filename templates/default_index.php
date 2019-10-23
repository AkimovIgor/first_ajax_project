<?php require_once('includes/header.php'); ?>

        <main class="py-4">
            <div class="container">
                <div class="row justify-content-center" id="ajax-content">
                    <div class="col-md-12" style="margin-bottom: 20px;" id="comments-data">

                        <!-- Вывод флеш-сообщения в случае успеха -->
                        <?php if (isset($_SESSION['messages']['success'])): ?>
                            <div class="alert alert-success" role="alert">
                                <span class="text"><?= $_SESSION['messages']['success'] ?></span>
                                <button type="button" class="close" aria-label="Close">
                                    <span aria-hidden="true" <?php if ($_SESSION['theme'] == 'sketchy') echo "style='opacity: 0;'" ?>>&times;</span>
                                </button>
                            </div>
                        <?php endif; ?>
                        <?php unset($_SESSION['messages']); ?>

                        <!-- Вывод флеш-сообщения в случае успеха -->
                        <div class="alert alert-success" style="display: none;" role="alert">
                            <span class="text"></span>
                            <button type="button" class="close" aria-label="Close">
                                <span aria-hidden="true" <?php if ($_SESSION['theme'] == 'sketchy') echo "style='opacity: 0;'" ?>>&times;</span>
                            </button>
                        </div>
                        <div class="card border-secondary">
                            <div class="card-header"><h3>Комментарии</h3></div>

                            <div class="card-body" id="comments" style="overflow-x: auto;">
                                
                                <!-- Если таблица с комментарими не пуста -->
                                <?php if (!empty($paginator['comments'])) : ?>
                                    <!-- Вывод данных каждого комментария -->
                                    <?php foreach ($paginator['comments'] as $comment): ?>
                                        <div class="media">
                                            <img src="<?= ($comment['image'] == 'no-user.jpg') ? 'markup/img/' . $comment['image'] : 'uploads/' . $comment['image']?>" class="mr-3" alt="..." width="64" height="64">
                                            <div class="media-body">
                                                <h5 class="mt-0"><?= $comment['name'] ?></h5> 
                                                <span><small><?= prettyDate($comment['date']) ?></small></span>
                                                <p>
                                                    <?= $comment['text'] ?>
                                                </p>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                
                                <?php elseif($paginator['commentsCount'] && $paginator['currentPage'] > $paginator['pageCount'] ): ?>
                                    <span id="">Такой страницы не существует.</span>
                                <!-- Если комментарии в таблице отсутствуют -->
                                <?php elseif(!$paginator['commentsCount']): ?>
                                    <span id="no-comments">Комментариев пока нет.</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <?php if ($paginator['comments'] && $paginator['pageCount'] > 1): ?>
                    <div class="col-md-12" id="comments-pagination">
                        <ul class="pagination justify-content-center">
                            <?php if ($paginator['currentPage'] > 1): ?>
                                <li class="page-item">
                                    <span class="page-link" href="<?= $paginator['link'] ?>1">&laquo;</span>
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
                <div class="row justify-content-center">
                    <div class="col-md-12" style="margin-top: 5px;">
                        <?php if (isset($isLogin)): ?>
                        <div class="card border-secondary">
                            <div class="card-header"><h3>Оставить комментарий</h3></div>
                            
                            <div class="card-body">
                                <form action="store.php" method="POST">
                                    <div class="form-group">
                                        <input type="hidden" name="id" id="exampleFormControlid" class="form-control" value="<?= $userId ?>"/>
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleFormControltext">Сообщение</label>
                                        <textarea name="text" class="form-control" id="exampleFormControltext" rows="3"></textarea>
                                        <!-- Вывод флеш-сообщения -->
                                        <span class="invalid-feedback" role="alert" style="display: none;">
                                            <strong></strong>
                                        </span>
                                    </div>
                                  <button type="submit" id="store" name="store" class="btn btn-success">Отправить</button>
                                </form>
                            </div>
                        </div>
                        <?php else: ?>
                            <div class="alert alert-info" role="alert">
                                Чтобы оставить комментарий <a href="login.php" class="alert-link">авторизуйтесь</a>.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                </div>
            </div>
        </main>
    </div>
    
    <?php require_once('includes/footer.php'); ?>
</body>

</html>