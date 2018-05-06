<!DOCTYPE html>
<?php
    require_once 'functions.php';

    $recordset = [];
    $id = 0;
    if (isset($_REQUEST)) {
        $recordset = do_command($_REQUEST);
    }
?>
<html lang="ru">
    <head>
        <meta charset="utf-8">
        <title>SQL lesson 2</title>
        <link rel="stylesheet" href="css/styles.css"/>
    </head>
    <body>
        <section class="main-container">
            <h1>Задание к лекции 4.2 «Запросы SELECT, INSERT, UPDATE и DELETE»</h1>
            <div class="form-container">
                <form method="POST">
                    <input type="text" name="description" placeholder="Описание задачи" value="">
                    <input type="submit" name="save" value="Добавить">
                </form>
            </div>
            <div class="form-container">
                <form method="POST">
                    <label for="sort">Сортировать по:</label>
                    <select name="sort_by">
                        <option value="date_added">Дате добавления</option>
                        <option value="is_done">Статусу</option>
                        <option value="description">Описанию</option>
                    </select>
                    <input type="submit" name="sort" value="Отсортировать">
                </form>
            </div>
            <table class="table">
                <thead class="table-head">
                    <tr class="header-row">
                        <td>Описание задачи</td>
                        <td>Дата добавления</td>
                        <td>Статус</td>
                        <td>Действия</td>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recordset as $record): ?>
                    <tr class="table-row">
                        <?php $id = (int)$record['id']; ?>
                        <td class="column-description"><?=$record['description']; ?></td>
                        <td class="column-date"><?=$record['date_added']; ?></td>
                        <td class="column-status">
                            <?php 
                                if ($record['is_done']) {
                                    echo '<span class="task-isdone">выполнено</span>';
                                } else {
                                    echo '<span class="task-active">в работе</span>';
                                }
                            ?>
                        </td>
                        <td class="column-action">
                            <a href="?id=<?=$id;?>;action=edit">Изменить </a>
                            <a href="?id=<?=$id;?>;action=done">Выполнить </a>
                            <a href="?id=<?=$id;?>;action=delete">Удалить </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </body>
</html>
