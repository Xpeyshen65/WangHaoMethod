<!DOCTYPE html>
<html>
    <head>
        <title>Lab3_Mosklenko</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    </head>
    <body>
        <div class="input-data">
            <h2>Ограничения для вводимого выражения:</h2>
            <p> 1) Для логических операций необходимо использовать следующие обозначения:<br>
                    <p> & - Конъюнкция; <br>
                        V - Дизъюнкция; <br>
                        ! - Отрицание; <br>
                        > - Импликация; <br></p>
                2) Выражение предшествующее символу импликации должно быть записано в конъюктивной нормальной форме.<br>
                3) Количество переменных в дизъюнктах ограничено до двух.<br>
                4) Выражение стоящее после символа импликации должно состоять только из переменных, перечисленных без знаков разделения.<br>
                5) В выражении запрещено использование любых символов разделителей.<br>
                6) Для записи выражения разрешается использовать только обозначения логических операций, представленных выше,<br>
                а также буквы латинского алфавита, за исключением буквы "V" в любом регистре.<br>
                Нарушение любого ограничения приведет к неопределенному результату работы программы.</p>
            <form name="" method="post" action="lab3_MSK.php">
                <p>Введите выражение:
                    <input type="text" size="70" name="strSource"></p>
                <p><input type="submit" value="Поиск решения" /></p>
            </form>
        </div>
        <div class="process-data">
            <?php
                if ($_POST) {
                    include 'Functional.php';
                    include 'BinaryTree.php';
                    $obj1 = new Functional();
                    $str = htmlspecialchars($_POST['strSource']);
                    $str = mb_strtolower($str);
                    $str = $obj1->changeHTMLtagToSymb($str);
                    $str = $obj1->upV($str);
                    $obj1->setStrSource($str);
                    $str = $obj1->getStrSource();


                    //$str = '(!pV!k)&(gVd)&(!gV!h)>sg';
                    //$str = 'g&(pV!g)&(!pVs)>s';
                    //$str = '(!aV!a)&g&g&(aVb)&(!bV!d)>a';
                    //echo "<br>STR: $str<br>";
                    $formatStr = $obj1->formatExpression($str);
                    echo "Введенное выражение: $formatStr<br>";
                    $result = -1;
                    $result = $obj1->searchSolve($str);

                    if ($result == 0) {
                        echo 'Выражение не выводимо<br>';
                    } elseif ($result == 1) {
                        echo 'Выражение выводимо<br>';
                    } else {
                        echo 'Возникла ошибка при работе программы<br>';
                    }

                }
            ?>
        </div>

    </body>
</html>