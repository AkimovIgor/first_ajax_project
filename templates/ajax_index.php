                    <div class="col-md-12" style="margin-bottom: 20px;" id="comments-data">
                        <!-- Вывод флеш-сообщения в случае успеха -->
                        <div class="alert alert-success" style="display: none;" role="alert">
                            <span class="text"></span>
                            <button type="button" class="close" aria-label="Close">
                                <span aria-hidden="true" <?php if ($_SESSION['theme'] == 'sketchy') echo "style='opacityL 0;'" ?>>&times;</span>
                            </button>
                        </div>
                        <div class="card border-secondary">
                            <div class="card-header"><h3>Комментарии</h3></div>

                            <div class="card-body" id="comments" style="overflow-x: auto;">
                                
                                <!-- Если таблица с комментарими не пуста -->
                                <?php if (!empty($paginator['comments'])): ?>
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
                                    <span class="page-link" href="#" data-href="<?= $paginator['link'] ?>1">&laquo;</span>
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
                