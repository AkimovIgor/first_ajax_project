<?php

$content = "<div class='alert alert-success' style='display: none;' role='alert'>
                            <span class='text'></span>
                            <button type='button' class='close' aria-label='Close'>
                                <span aria-hidden='true'>&times;</span>
                            </button>
                        </div>
                        <div class='card'>
                            <div class='card-header'><h3>Комментарии</h3></div>

                            <div class='card-body' id='comments'>";
                            
                                if (!empty($paginator['comments'])) {
                                    foreach ($paginator['comments'] as $comment){
                                         $content .= "<div class='media'>
                                            <img src='"; 
                                            ($comment['image'] == 'no-user.jpg') ? $content .= 'markup/img/' . $comment['image'] : $content .= 'uploads/' . $comment['image'] . "' class='mr-3' alt='...' width='64' height='64'>
                                            <div class='media-body'>
                                                <h5 class='mt-0'>" . $comment['name']. "</h5> 
                                                <span><small>" . prettyDate($comment['date']) . "</small></span>
                                                <p>
                                                    " . $comment['text'] . "
                                                </p>
                                            </div>
                                        </div>";                  
                                    }
                                } elseif($paginator['commentsCount'] && $paginator['currentPage'] > $paginator['pageCount'] ){
                                    $content .= "<span id=''>Такой страницы не существует.</span>";
                                } elseif(!$paginator['commentsCount']){
                                    $content .= "<span id='no-comments'>Комментариев пока нет.</span>";
                                }
                                
                    $content .= "
                            </div>
                        </div>
                    </div>";

                    if ($paginator['comments'] && $paginator['pageCount'] > 1){
                        $content .= "
                        <div class='col-md-12'>
                            <ul class='pagination justify-content-center'>";
                                if ($paginator['currentPage'] > 1){
                                    $content .= "
                                    <li class='page-item'>
                                        <a class='page-link' href='" . $paginator['link'] . "1' onclick='pagination(1);return false;'>&laquo;</a>
                                    </li><li class='page-item'>
                                        <a class='page-link' href='" . $paginator['link'] . "" . $paginator['currentPage'] - 1 . "' onclick='pagination(" . $paginator['currentPage'] - 1 . "); return false;'>&lsaquo;</a>
                                    </li>";
                                } 

                                for ($i = $paginator['start']; $i <= $paginator['end']; $i++){
                                    $content .= "
                                    <li class='page-item"; if ($i == $paginator['currentPage']){ $content .= " active'>"; } 
                                        $content .= "<a class='page-link' href='" . $paginator['link'] . $i . "' onclick='pagination(" . $i . "); return false;'>" . $i . "</a>
                                    </li>";
                                } 

                                if ($paginator['currentPage'] < $paginator['pageCount']){
                                    $content .= "
                                    <li class='page-item'>
                                        <a class='page-link' href='" . $paginator['link'] . "" . $paginator['currentPage'] + 1 . "' onclick='pagination(" . $paginator['currentPage'] + 1 . "); return false;'>&rsaquo;</a>
                                    </li>
                                    <li class='page-item'>
                                        <a class='page-link' href='" . $paginator['link'] . "" . $paginator['pageCount'] . "' onclick='pagination(" . $paginator['pageCount'] . "); return false;'>&raquo;</a>
                                    </li>";
                                }
                                $content .= "
                            </ul>
                        </div>";
                    } 

?>